var halo_emoticons = [
        // really weird bug if you have :{ and then have :{) in the same container anywhere *after* :{ then :{ doesn't get matched, e.g. :] :{ :) :{) :) :-) will match everything except :{
        //  But if you take out the :{) or even just move :{ to the right of :{) then everything works fine. This has something to do with the preMatch string below I think, because
        //  it'll work again if you set preMatch equal to '()'
        //  So for now, we'll just remove :{) from the emoticons, because who actually uses this mustache man anyway?
      // ":{)",
    //threeCharacterEmoticons = 
      ":-)", ":o)", ":c)", ":^)", ":-D", ":-(", ":-9", ";-)", ":-P", ":-p", ":-�", ":-b", ":-O", ":-/", ":-X", ":-#", ":'(", "B-)", "8-)", ";*(", ":-*", ":-\\",
      "?-)", // <== This is my own invention, it's a smiling pirate (with an eye-patch)!
      // and the twoCharacterEmoticons from below, but with a space inserted
      ": )", ": ]", "= ]", "= )", "8 )", ": }", ": D", "8 D", "X D", "x D", "= D", ": (", ": [", ": {", "= (", "; )", "; ]", "; D", ": P", ": p", "= P", "= p", ": b", ": �", ": O", "8 O", ": /", "= /", ": S", ": #", ": X", "B )", ": |", ": \\", "= \\", ": *", ": &gt;", ": &lt;",//, "* )"
    //twoCharacterEmoticons @separate these out so that we can add a letter-spacing between the characters for better proportions
      ":)", ":]", "=]", "=)", "8)", ":}", ":D", ":(", ":[", ":{", "=(", ";)", ";]", ";D", ":P", ":p", "=P", "=p", ":b", ":�", ":O", ":/", "=/", ":S", ":#", ":X", "B)", ":|", ":\\", "=\\", ":*", ":&gt;", ":&lt;"//, "*)"
    ];
 
;/*!
	Autosize v1.18.2 - 2014-01-06
	Automatically adjust textarea height based on user input.
	(c) 2014 Jack Moore - http://www.jacklmoore.com/autosize
	license: http://www.opensource.org/licenses/mit-license.php
*/
;(function ($) {
	var
	defaults = {
		className: 'autosizejs',
		append: '',
		callback: false,
		minHeight: 0,
		resizeDelay: 10
	},

	// border:0 is unnecessary, but avoids a bug in Firefox on OSX
	copy = '<textarea tabindex="-1" style="position:absolute; top:-999px; left:0; right:auto; bottom:auto; border:0; padding: 0; -moz-box-sizing:content-box; -webkit-box-sizing:content-box; box-sizing:content-box; word-wrap:break-word; height:0 !important; min-height:0 !important; overflow:hidden; transition:none; -webkit-transition:none; -moz-transition:none;"/>',

	// line-height is conditionally included because IE7/IE8/old Opera do not return the correct value.
	typographyStyles = [
		'fontFamily',
		'fontSize',
		'fontWeight',
		'fontStyle',
		'letterSpacing',
		'textTransform',
		'wordSpacing',
		'textIndent'
	],

	// to keep track which textarea is being mirrored when adjust() is called.
	mirrored,

	// the mirror element, which is used to calculate what size the mirrored element should be.
	mirror = $(copy).data('autosize', true)[0];

	// test that line-height can be accurately copied.
	mirror.style.lineHeight = '99px';
	if ($(mirror).css('lineHeight') === '99px') {
		typographyStyles.push('lineHeight');
	}
	mirror.style.lineHeight = '';

	$.fn.autosize = function (options) {
		if (!this.length) {
			return this;
		}

		options = $.extend({}, defaults, options || {});

		if (mirror.parentNode !== document.body) {
			$(document.body).append(mirror);
		}

		return this.each(function () {
			var
			ta = this,
			$ta = $(ta),
			maxHeight,
			minHeight,
			boxOffset = 0,
			callback = $.isFunction(options.callback),
			originalStyles = {
				height: ta.style.height,
				overflow: ta.style.overflow,
				overflowY: ta.style.overflowY,
				wordWrap: ta.style.wordWrap,
				resize: ta.style.resize
			},
			timeout,
			width = $ta.width();

			if ($ta.data('autosize')) {
				// exit if autosize has already been applied, or if the textarea is the mirror element.
				return;
			}
			$ta.data('autosize', true);

			if ($ta.css('box-sizing') === 'border-box' || $ta.css('-moz-box-sizing') === 'border-box' || $ta.css('-webkit-box-sizing') === 'border-box'){
				boxOffset = $ta.outerHeight() - $ta.height();
			}

			// IE8 and lower return 'auto', which parses to NaN, if no min-height is set.
			if(options.minHeight == 0){
				minHeight = Math.max(parseInt($ta.css('minHeight'), 10) - boxOffset || 0, $ta.height());
			} else {
				minHeight = options.minHeight;
			}

			$ta.css({
				overflow: 'hidden',
				overflowY: 'hidden',
				wordWrap: 'break-word', // horizontal overflow is hidden, so break-word is necessary for handling words longer than the textarea width
				resize: ($ta.css('resize') === 'none' || $ta.css('resize') === 'vertical') ? 'none' : 'horizontal'
			});

			// The mirror width must exactly match the textarea width, so using getBoundingClientRect because it doesn't round the sub-pixel value.
			function setWidth() {
				var width;
				var style = window.getComputedStyle ? window.getComputedStyle(ta, null) : false;
				
				if (style) {
					width = ta.getBoundingClientRect().width;

					$.each(['paddingLeft', 'paddingRight', 'borderLeftWidth', 'borderRightWidth'], function(i,val){
						width -= parseInt(style[val],10);
					});

					mirror.style.width = width + 'px';
				}
				else {
					// window.getComputedStyle, getBoundingClientRect returning a width are unsupported and unneeded in IE8 and lower.
					mirror.style.width = Math.max($ta.width(), 0) + 'px';
				}
			}

			function initMirror() {
				var styles = {};

				mirrored = ta;
				mirror.className = options.className;
				maxHeight = parseInt($ta.css('maxHeight'), 10);

				// mirror is a duplicate textarea located off-screen that
				// is automatically updated to contain the same text as the
				// original textarea.  mirror always has a height of 0.
				// This gives a cross-browser supported way getting the actual
				// height of the text, through the scrollTop property.
				$.each(typographyStyles, function(i,val){
					styles[val] = $ta.css(val);
				});
				$(mirror).css(styles);

				setWidth();

				// Chrome-specific fix:
				// When the textarea y-overflow is hidden, Chrome doesn't reflow the text to account for the space
				// made available by removing the scrollbar. This workaround triggers the reflow for Chrome.
				if (window.chrome) {
					var width = ta.style.width;
					ta.style.width = '0px';
					var ignore = ta.offsetWidth;
					ta.style.width = width;
				}
			}

			// Using mainly bare JS in this function because it is going
			// to fire very often while typing, and needs to very efficient.
			function adjust() {
				var height, original;

				if (mirrored !== ta) {
					initMirror();
				} else {
					setWidth();
				}

				mirror.value = ta.value + options.append;
				mirror.style.overflowY = ta.style.overflowY;
				original = parseInt(ta.style.height,10);

				// Setting scrollTop to zero is needed in IE8 and lower for the next step to be accurately applied
				mirror.scrollTop = 0;

				mirror.scrollTop = 9e4;

				// Using scrollTop rather than scrollHeight because scrollHeight is non-standard and includes padding.
				height = mirror.scrollTop;

				if (maxHeight && height > maxHeight) {
					ta.style.overflowY = 'scroll';
					height = maxHeight;
				} else {
					ta.style.overflowY = 'hidden';
					if (height < minHeight) {
						height = minHeight;
					}
				}

				height += boxOffset;

				if (original !== height) {
					ta.style.height = height + 'px';
					if (callback) {
						options.callback.call(ta,ta);
					}
				}
			}

			function resize () {
				clearTimeout(timeout);
				timeout = setTimeout(function(){
					var newWidth = $ta.width();

					if (newWidth !== width) {
						width = newWidth;
						adjust();
					}
				}, parseInt(options.resizeDelay,10));
			}

			if ('onpropertychange' in ta) {
				if ('oninput' in ta) {
					// Detects IE9.  IE9 does not fire onpropertychange or oninput for deletions,
					// so binding to onkeyup to catch most of those occasions.  There is no way that I
					// know of to detect something like 'cut' in IE9.
					$ta.on('input.autosize keyup.autosize', adjust);
				} else {
					// IE7 / IE8
					$ta.on('propertychange.autosize', function(){
						if(event.propertyName === 'value'){
							adjust();
						}
					});
				}
			} else {
				// Modern Browsers
				$ta.on('input.autosize', adjust);
			}

			// Set options.resizeDelay to false if using fixed-width textarea elements.
			// Uses a timeout and width check to reduce the amount of times adjust needs to be called after window resize.

			if (options.resizeDelay !== false) {
				$(window).on('resize.autosize', resize);
			}

			// Event for manual triggering if needed.
			// Should only be needed when the value of the textarea is changed through JavaScript rather than user input.
			$ta.on('autosize.resize', adjust);

			// Event for manual triggering that also forces the styles to update as well.
			// Should only be needed if one of typography styles of the textarea change, and the textarea is already the target of the adjust method.
			$ta.on('autosize.resizeIncludeStyle', function() {
				mirrored = null;
				adjust();
			});

			$ta.on('autosize.destroy', function(){
				mirrored = null;
				clearTimeout(timeout);
				$(window).off('resize', resize);
				$ta
					.off('autosize')
					.off('.autosize')
					.css(originalStyles)
					.removeData('autosize');
			});

			// on reset call
			$ta.on('halo.onreset', adjust);

			// Call adjust in case the textarea already contains text.
			adjust();
		});
	};
}(window.jQuery)); // jQuery or jQuery-like library, such as Zepto
;/*!
 * jQuery.textoverlay.js
 *
 * Repository: https://github.com/yuku-t/jquery-textoverlay
 * License:    MIT
 * Author:     Yuku Takahashi
 */

;(function ($) {

  'use strict';

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

  var entityMap = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#x27;',
    '/': '&#x2F;'
  }

  var entityRegexe = /[&<>"'\/]/g

  /**
   * Function for escaping strings to HTML interpolation.
   */
  var escape = function (str) {
    return str.replace(entityRegexe, function (match) {
      return entityMap[match];
    })
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

  var Overlay = (function () {

    var html, css, textareaToWrapper, textareaToOverlay, allowedProps;

    html = {
      wrapper: '<div class="textoverlay-wrapper"></div>',
      overlay: '<div class="textoverlay"></div>'
    };

    css = {
      wrapper: {
        margin: 0,
        padding: 0,
        overflow: 'hidden'
      },
      overlay: {
        position: 'absolute',
        color: 'transparent',
        'white-space': 'pre-wrap',
        'word-wrap': 'break-word',
        overflow: 'hidden'
      },
      textarea: {
        background: 'transparent',
        position: 'relative',
        outline: 0
      }
    };

    // CSS properties transport from textarea to wrapper
    textareaToWrapper = ['display'];
    // CSS properties transport from textarea to overlay
    textareaToOverlay = [
      'margin-top',
      'margin-right',
      'margin-bottom',
      'margin-left',
      'padding-top',
      'padding-right',
      'padding-bottom',
      'padding-left',
      'font-family',
      'font-weight',
      'font-size',
      'background-color',
	  'line-height'
    ];

    function Overlay($textarea, strategies) {
      var $wrapper, position;

      // Setup wrapper element
      position = $textarea.css('position');
      if (position === 'static') position = 'relative';
      $wrapper = $(html.wrapper).css(
        $.extend({}, css.wrapper, getStyles($textarea, textareaToWrapper), {
          position: position
        })
      );

      // Setup overlay
      this.textareaTop = parseInt($textarea.css('border-top-width'));
      this.$el = $(html.overlay).css(
        $.extend({}, css.overlay, getStyles($textarea, textareaToOverlay), {
          top: this.textareaTop,
          right: parseInt($textarea.css('border-right-width')),
          bottom: parseInt($textarea.css('border-bottom-width')),
          left: parseInt($textarea.css('border-left-width'))
        })
      );

      // Setup textarea
      this.$textarea = $textarea.css(css.textarea);

      // Render wrapper and overlay
      this.$textarea.wrap($wrapper).before(this.$el);

      // Intercept val method
      // Note that jQuery.fn.val does not trigger any event.
      this.$textarea.origVal = $textarea.val;
      this.$textarea.val = bind(this.val, this);

      // Bind event handlers
      //this.$textarea.on('input', bind(this.onInput, this));
      //this.$textarea.on('change', bind(this.onInput, this));
      this.$textarea.on('scroll', bind(this.resizeOverlay, this));
      this.$textarea.on('resize', bind(this.resizeOverlay, this));

      // Strategies must be an array
      this.strategies = $.isArray(strategies) ? strategies : [strategies];

      this.renderTextOnOverlay();
    }

    $.extend(Overlay.prototype, {
      val: function (value) {
        return value == null ? this.$textarea.origVal() : this.setVal(value);
      },

      setVal: function (value) {
        this.$textarea.origVal(value);
		return this;
        //return this.renderTextOnOverlay();
      },

      onInput: function (e) {
        //this.renderTextOnOverlay();
      },

      renderTextOnOverlay: function () {
        var text, i, l, strategy, match, style;
        text = escape(this.$textarea.val());

        // Apply all strategies
        for (i = 0, l = this.strategies.length; i < l; i++) {
          strategy = this.strategies[i];
          match = strategy.match;
          if ($.isArray(match)) {
            match = $.map(match, function (str) {
              return str.replace(/(\(|\)|\|)/g, '\$1');
            });
            match = new RegExp('(' + match.join('|') + ')', 'g');
          }

          // Style attribute's string
          style = 'background-color:' + strategy.css['background-color'];

          text = text.replace(match, function (str) {
            return '<span style="' + style + '">' + str + '</span>';
          });
        }
        this.$el.html(text);
        return this;
      },

      resizeOverlay: function () {
        this.$el.css({ top: this.textareaTop - this.$textarea.scrollTop() });
      }
    });

    return Overlay;

  })();

  $.fn.overlay = function (options) {
    new Overlay(this, options);
    return this;
  };

})(window.jQuery);;/*!
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