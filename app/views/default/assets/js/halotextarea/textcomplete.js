/*!
 * jQuery.textcomplete.js
 *
 * Repositiory: https://github.com/yuku-t/jquery-textcomplete
 * License:     MIT
 * Author:      Yuku Takahashi
 */

;(function ($) {

  'use strict';

  /**
   * Exclusive execution control utility.
   */
  var lock = function (func) {
    var free, locked;
    free = function () { locked = false; };
    return function () {
      var args;
      if (locked) return;
      locked = true;
      args = toArray(arguments);
      args.unshift(free);
      func.apply(this, args);
    };
  };

  /**
   * Convert arguments into a real array.
   */
  var toArray = function (args) {
    var result;
    result = Array.prototype.slice.call(args);
    return result;
  };

  /**
   * Bind the func to the context.
   */
  var bind = function (func, context) {
    if (func.bind) {
      // Use native Function#bind if it's available.
      return func.bind(context);
    } else {
      return function () {
        func.apply(context, arguments);
      };
    }
  };

  /**
   * Get the styles of any element from property names.
   */
  var getStyles = (function () {
    var color;
    color = $('<div></div>').css(['color']).color;
    if (typeof color !== 'undefined') {
      return function ($el, properties) {
        return $el.css(properties);
      };
    } else {  // for jQuery 1.8 or below
      return function ($el, properties) {
        var styles;
        styles = {};
        $.each(properties, function (i, property) {
          styles[property] = $el.css(property);
        });
        return styles
      };
    }
  })();

  /**
   * Default template function.
   */
  var identity = function (obj) { return obj; };

  /**
   * Memoize a search function.
   */
  var memoize = function (func) {
    var memo = {};
    return function (term, callback) {
      if (memo[term]) {
        callback(memo[term]);
      } else {
        func.call(this, term, function (data) {
          memo[term] = (memo[term] || []).concat(data);
          callback.apply(null, arguments);
        });
      }
    };
  };

  /**
   * Determine if the array contains a given value.
   */
  var include = function (array, value) {
    var i, l;
    if (array.indexOf) return array.indexOf(value) != -1;
    for (i = 0, l = array.length; i < l; i++) {
      if (array[i] === value) return true;
    }
    return false;
  };

  /**
   * Textarea manager class.
   */
  var Completer = (function () {
    var html, css, $baseWrapper, $baseList, tagged_list, rawInput;

    html = {
      wrapper: '<div class="textcomplete-wrapper"></div>',
      list: '<ul class="halo-dropdown-autocomplete"></ul>'
    };
    css = {
      wrapper: {
        position: 'relative'
      },
      list: {
        position: 'absolute',
        top: 0,
        left: 0,
        zIndex: '100',
        display: 'none'
      }
    };
	tagged_list = [];
	
    $baseWrapper = $(html.wrapper).css(css.wrapper);
    $baseList = $(html.list).css(css.list);

    function Completer($el, strategies) {
      var $wrapper, $list, focused;
      $list = $baseList.clone();
      this.el = $el.get(0);  // textarea element
      this.$el = $el;
	  
	  var rawInputId = $el.data('raw-input');
	  this.rawInput = jQuery('#' + rawInputId);
	  
	  //detect if the rawInput data is provided
	  var rawInputVal = this.rawInput.val();
	  if(typeof rawInputVal !== 'undefined'){
		rawInputVal = rawInputVal.trim();
	  } else {
		rawInputVal = '';
	  }
	  this.tagged_list = [];
	  if(rawInputVal.length > 0){
		//rawInputData is provided, update the taggedlist also textarea data
		var reg = /@\[([0-9]+) (.+?)\]/g;
		var inputVal = rawInputVal;
		var matches;
		while ((matches = reg.exec(rawInputVal)) !== null)
		{
			var tagId =  matches[1];
			var tagName = matches[2];
			inputVal = inputVal.replace(matches[0],'@'+tagName);
			this.tagged_list[tagId] = {'id':tagId,'name':tagName};			
		}
		
		$el.val(inputVal);
	  }
	
	  
      $wrapper = prepareWrapper(this.$el);

      // Refocus the textarea if it is being focused
      focused = this.el === document.activeElement;
      this.$el.wrap($wrapper).before($list);
      if (focused) { this.el.focus(); }

      this.listView = new ListView($list, this);
      this.strategies = strategies;
      this.$el.on('keyup', bind(this.onKeyup, this));
      this.$el.on('keydown', bind(this.listView.onKeydown, this.listView));
	  
      this.$el.on('input', bind(this.onInput, this));
      this.$el.on('change', bind(this.onInput, this));
	  
	  //caller queue
	  this.lastCaller = 0;

      // Global click event handler
      $(document).on('click', bind(function (e) {
        if (e.originalEvent && !e.originalEvent.keepTextCompleteDropdown) {
          this.listView.deactivate();
        }
      }, this));
    }

    /**
     * Completer's public methods
     */
    $.extend(Completer.prototype, {

      /**
       * Show autocomplete list next to the caret.
       */
      renderList: function (data) {
        if (this.clearAtNext) {
          this.listView.clear();
          this.clearAtNext = false;
        }
        if (data.length) {
          if (!this.listView.shown) {
            this.listView
                .setPosition(this.getCaretPosition())
                .clear()
                .activate();
            this.listView.strategy = this.strategy;
          }
          data = data.slice(0, this.strategy.maxCount);
          this.listView.render(data);
        }
        
        if (!this.listView.data.length && this.listView.shown) {
          this.listView.deactivate();
        }
      },

      searchCallbackFactory: function (free) {
        var self = this;
        return function (data, keep) {
          self.renderList(data);
          if (!keep) {
            // This is the last callback for this search.
            free();
            self.clearAtNext = true;
          }
        };
      },

      /**
       * Keyup event handler.
       */
      onKeyup: function (e) {
        var searchQuery, term, resetTimer;
		this.addCaller();		  
		var callNum = this.getCaller();

        searchQuery = this.extractSearchQuery(this.getTextFromHeadToCaret());
		
        if (searchQuery.length) {
          term = searchQuery[1];
          if (this.term === term) return; // Ignore shift-key or something.
          this.term = term;
		  
		  //wait for more input before executing the search
		  var completer = this;
		  setTimeout(function(){
			var lastCaller = completer.getCaller();						
			if(callNum >= lastCaller){
				//reset lastCaller number
				completer.resetCaller();
				completer.search(searchQuery);
			}
		  },500);
        } else {
          this.term = null;
          this.listView.deactivate();
        }
      },

      onSelect: function (value) {
        var pre, post, newSubStr;
        pre = this.getTextFromHeadToCaret();
        post = this.el.value.substring(this.el.selectionEnd);

        newSubStr = this.strategy.replace(value);
        if ($.isArray(newSubStr)) {
          post = newSubStr[1] + post;
          newSubStr = newSubStr[0];
        }
        pre = pre.replace(this.strategy.match, newSubStr);
		
		//update the user tag list
		var tagged_list = this.getTaggedList();
		tagged_list[value.id] = value;
		this.setTaggedList(tagged_list);
		//update the raw content
		this.updateRawContent(pre + post);
		
        this.$el.val(pre + post);
        this.el.focus();
        this.el.selectionStart = this.el.selectionEnd = pre.length;
      },
	  
      onInput: function (e) {
        var text = this.$el.val();
        this.updateRawContent(text);
      },

	  getTaggedList: function(){
		if(typeof this.tagged_list === 'undefined'){
			this.tagged_list = [];
		}
		return this.tagged_list;
	  },
	  
	  setTaggedList: function(list){
		this.taggedList = list;
	  },
	  
	  addCaller: function(){
	    var curr = this.lastCaller + 1;
		this.lastCaller = curr;
		return this.lastCaller;
	  },
	  
	  getCaller: function(){
		return this.lastCaller;
	  },
	  
	  resetCaller: function(){
		this.lastCaller = 0;
	  },
	  
	  updateRawContent: function(content){
		tagged_list = this.getTaggedList();
		var rawContent,overlayContent;
		rawContent = content;
		overlayContent = content;
		for(var key in tagged_list){
			var ele = tagged_list[key];
			var searchStr = '@' + ele.name;
			var replaceStr = '@[' + ele.id + ' ' + ele.name + ']';
			var replaceOverlay = '<b>@' + ele.name + '</b>';
			//if search string is not found, update the tagged list
			if(rawContent.search(searchStr) < 0){
				tagged_list.splice(key, 1);
				this.setTaggedList(tagged_list);
			} else {
				var searchReg = new RegExp(searchStr, 'g');
				rawContent = rawContent.replace(searchStr,replaceStr);
				overlayContent = overlayContent.replace(searchStr,replaceOverlay);
			}
		}
		this.rawInput.val(rawContent);
		
		var overlay = this.$el.prev('.textoverlay');
		overlay.html(overlayContent);
		//console.log(overlayContent);
		
		//update the overlay layer also
		
	  },
      // Helper methods
      // ==============

      /**
       * Returns caret's relative coordinates from textarea's left top corner.
       */
      getCaretPosition: function () {
        // Browser native API does not provide the way to know the position of
        // caret in pixels, so that here we use a kind of hack to accomplish
        // the aim. First of all it puts a div element and completely copies
        // the textarea's style to the element, then it inserts the text and a
        // span element into the textarea.
        // Consequently, the span element's position is the thing what we want.

        if (this.el.selectionEnd === 0) return;
        var properties, css, $div, $span, position, dir;

        dir = this.$el.attr('dir') || this.$el.css('direction');
        properties = ['border-width', 'font-family', 'font-size', 'font-style',
          'font-variant', 'font-weight', 'height', 'letter-spacing',
          'word-spacing', 'line-height', 'text-decoration', 'text-align',
          'width', 'padding-top', 'padding-right', 'padding-bottom',
          'padding-left', 'margin-top', 'margin-right', 'margin-bottom',
          'margin-left'
        ];
        css = $.extend({
          position: 'absolute',
          overflow: 'auto',
          'white-space': 'pre-wrap',
          top: 0,
          left: -9999,
          direction: dir
        }, getStyles(this.$el, properties));

        $div = $('<div></div>').css(css).text(this.getTextFromHeadToCaret());
        $span = $('<span></span>').text('.').appendTo($div);
        this.$el.before($div);
        position = $span.position();
        position.top += $span.height() - this.$el.scrollTop();

        if (dir == 'rtl') {
        	position.left -= this.listView.$el.width();
        }

        $div.remove();
        return position;
      },

      getTextFromHeadToCaret: function () {
        var text, selectionEnd, range;
        selectionEnd = this.el.selectionEnd;
        if (typeof selectionEnd === 'number') {
          text = this.el.value.substring(0, selectionEnd);
        } else if (document.selection) {
          range = this.el.createTextRange();
          range.moveStart('character', 0);
          range.moveEnd('textedit');
          text = range.text;
        }
        return text;
      },

      /**
       * Parse the value of textarea and extract search query.
       */
      extractSearchQuery: function (text) {
        // If a search query found, it returns used strategy and the query
        // term. If the caret is currently in a code block or search query does
        // not found, it returns an empty array.

        var i, l, strategy, match;
        for (i = 0, l = this.strategies.length; i < l; i++) {
          strategy = this.strategies[i];
          match = text.match(strategy.match);
          if (match) { return [strategy, match[strategy.index]]; }
        }
        return [];
      },

      search: lock(function (free, searchQuery) {
        var term, strategy;
        this.strategy = searchQuery[0];
        term = searchQuery[1];
        this.strategy.search(term, this.searchCallbackFactory(free));
      })
    });

    /**
     * Completer's private functions
     */
    var prepareWrapper = function ($el) {
      return $baseWrapper.clone().css('display', $el.css('display'));
    };

    return Completer;
  })();

  /**
   * Dropdown menu manager class.
   */
  var ListView = (function () {

    function ListView($el, completer) {
      this.data = [];
      this.$el = $el;
      this.index = 0;
      this.completer = completer;

      this.$el.on('click', 'li.textcomplete-item', bind(this.onClick, this));
    }

    $.extend(ListView.prototype, {
      shown: false,

      render: function (data) {
        var html, i, l, index, val;

        html = '';
        for (i = 0, l = data.length; i < l; i++) {
          val = data[i];
          if (include(this.data, val)) continue;
          index = this.data.length;
          this.data.push(val);
          html += '<li class="textcomplete-item" data-index="' + index + '"><a>';
          html +=   this.strategy.template(val);
          html += '</a></li>';
          if (this.data.length === this.strategy.maxCount) break;
        }
        this.$el.append(html)
        if (!this.data.length) {
          this.deactivate();
        } else {
          this.activateIndexedItem();
        }
      },

      clear: function () {
        this.data = [];
        this.$el.html('');
        this.index = 0;
        return this;
      },

      activateIndexedItem: function () {
        var $item;
        this.$el.find('.active').removeClass('active');
        this.getActiveItem().addClass('active');
      },

      getActiveItem: function () {
        return $(this.$el.children().get(this.index));
      },

      activate: function () {
        if (!this.shown) {
          this.$el.show();
          this.shown = true;
        }
        return this;
      },

      deactivate: function () {
        if (this.shown) {
          this.$el.hide();
          this.shown = false;
          this.data = this.index = null;
        }
        return this;
      },

      setPosition: function (position) {
        this.$el.css(position);
        return this;
      },

      select: function (index) {
        this.completer.onSelect(this.data[index]);
        this.deactivate();
      },

      onKeydown: function (e) {
        var $item;
        if (!this.shown) return;
        if (e.keyCode === 27) {         // ESC
            this.deactivate();
        } else if (e.keyCode === 38) {         // UP
          e.preventDefault();
          if (this.index === 0) {
            this.index = this.data.length-1;
          } else {
            this.index -= 1;
          }
          this.activateIndexedItem();
        } else if (e.keyCode === 40) {  // DOWN
          e.preventDefault();
          if (this.index === this.data.length - 1) {
            this.index = 0;
          } else {
            this.index += 1;
          }
          this.activateIndexedItem();
        } else if (e.keyCode === 13 || e.keyCode === 9) {  // ENTER or TAB
		  e.stopImmediatePropagation();
          e.preventDefault();
          this.select(parseInt(this.getActiveItem().data('index')));
        }
      },

      onClick: function (e) {
        var $e = $(e.target);
        e.originalEvent.keepTextCompleteDropdown = true;
        if (!$e.hasClass('textcomplete-item')) {
          $e = $e.parents('li.textcomplete-item');
        }
        this.select(parseInt($e.data('index')));
      }
    });

    return ListView;
  })();

  $.fn.textcomplete = function (strategies) {
    var i, l, strategy;
    for (i = 0, l = strategies.length; i < l; i++) {
      strategy = strategies[i];
      if (!strategy.template) {
        strategy.template = identity;
      }
      if (strategy.index == null) {
        strategy.index = 2;
      }
      if (strategy.cache) {
        strategy.search = memoize(strategy.search);
      }
      strategy.maxCount || (strategy.maxCount = 10);
    }
    new Completer(this, strategies);

    return this;
  };

})(window.jQuery);