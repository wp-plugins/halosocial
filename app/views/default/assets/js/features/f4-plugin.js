/*
 * Plugin Name: HaloSocial
 * Plugin URL: https://halo.social
 * Description: Social Networking Plugin for WordPress
 * Author: HaloSocial
 * Author URL: https://halo.social
 * Version: 1.0
 * Copyright: (c) 2015 HaloSocial, Inc. All Rights Reserved.
 * License: GPLv3 or later
 * License URL: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: halosocial
 * Domain Path: /language
 *
 * HaloSocial is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * HaloSocial is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY. See the
 * GNU General Public License for more details.
 */

jQuery.extend(true, halo, {
	user: {
		banForm: function(userId){
			if (typeof userId == 'undefined' || !userId ) return false;
			halo.jax.call("ban", "ShowForm", userId);			
		},
		submitBan: function(userId){
			if (typeof userId == 'undefined' || !userId ) return false;
			var values = halo.util.getFormValues('popupForm');
			halo.jax.call("ban", "SubmitForm", userId, values);			
		},
		toggleBan: function(ele){
			$row = jQuery(ele).closest('.halo-ban-setting');
			$duration = $row.find('.halo-ban-duration');
			if($duration.length){
				$duration.toggleClass('hidden');
			}
		}
	}
});

/* ============================================================ conv features
 *
 * ============================================================ */
!function ($) {

	"use strict"; // jshint ;_;


	/* CONV CLASS DEFINITION
	 * ========================= */

	var toggle = '[data-htoggle=conv]'
		, Conv = function (element) {
			var $el = jQuery(element).on('click.conv.data-api', this.toggle)
		}

	Conv.prototype = {

		constructor: Conv, toggle: function (e) {
			var $this = jQuery(this)
				, $parent
				, isActive

			if ($this.is('.disabled, :disabled')) return

			$parent = getParent($this)

			isActive = $parent.hasClass('open')

			//clearMenus()
			$parent.toggleClass('open')

			updateCss($this)

			//$this.focus()

			//store the conv display status to localstorage
			if (isActive) {
				halo.storage.setAttr('conv', 'convid_' + $parent.attr('data-convid'), 'status', 'close')
			} else {
				halo.storage.setAttr('conv', 'convid_' + $parent.attr('data-convid'), 'status', 'open')
			}

			return false
		}, init: function (e) {
			var $this = jQuery(this)

			updateCss($this)
			return false
		}, open: function (e) {
			var $this = jQuery(this)
				, $parent
				, isActive

			if ($this.is('.disabled, :disabled')) return

			$parent = getParent($this)

			isActive = $parent.hasClass('open')

			//clearMenus()
			if (!isActive) {
				$parent.addClass('open')
			}

			if (!$parent.hasClass('halo-conv-active')) {
				//update the active conversation to match the maximum displayable conversations
				if (!canStackNewConv()) {
					//take out the last active conversation and collapse it
					jQuery('.halo-conv-active').last().find('[data-htoggle="conv"]').first().conv('collapse');
				}
			}
			$parent.addClass('halo-conv-active');

			updateCss($this)

			//$this.focus()
			//store the conv display status to localstorage
			halo.storage.setAttr('conv', 'convid_' + $parent.attr('data-convid'), 'status', 'open')
			return false
		}, close: function (e) {
			var $this = jQuery(this)
				, $parent
				, isActive

			if ($this.is('.disabled, :disabled')) return

			$parent = getParent($this)

			$parent.removeClass('open')

			$parent.addClass('halo-conv-active');

			updateCss($this)

			//$this.blur()
			//store the conv display status to localstorage
			halo.storage.setAttr('conv', 'convid_' + $parent.attr('data-convid'), 'status', 'close')

			return false
		}, remove: function (e) {
			var $this = jQuery(this), $parent
			$parent = getParent($this)
			//remove from local storage
			halo.storage.deleteObj('conv', 'convid_' + $parent.attr('data-convid'));
			var isActive = $parent.hasClass('halo-conv-active')
			$parent.remove();
			if (isActive) {
				//get the first collapse conv then open it as replacement
				var replacement = jQuery('[data-convid]:not(.halo-conv-active)').first();
				if (replacement.length) {
					jQuery('[data-htoggle="conv"]', replacement).conv('open');
				}
			}
			//then update css for all prev conv
			jQuery('.halo-conv-active [data-htoggle="conv"]').each(function () {
				updateCss(jQuery(this));
			})

			return false

		}, collapse: function (e) {
			var $this = jQuery(this), $parent
			$parent = getParent($this)
			$parent.removeClass('halo-conv-active');

			updateCss($this)

			//store the conv display status to localstorage
			halo.storage.setAttr('conv', 'convid_' + $parent.attr('data-convid'), 'status', 'collapse')

			return false

		}
	}

	function clearMenus() {
		jQuery(toggle).each(function () {
			getParent(jQuery(this)).removeClass('open')
		})
	}

	function updateCss($this) {
		var $parent = getParent($this)
		var height = 0;
		var right = 0;
		//only display conversation if it is in active state
		if ($parent.hasClass('halo-conv-active')) {
			var titleHeight = $this.outerHeight()
			var contentHeight = $parent.children('.halo-dropdown-menu').first().outerHeight()
			if ($parent.hasClass('open')) {
				height = contentHeight;
			}
			//calculate the right by circumulate all previous sibling element
			$parent.prevAll('.halo-conv-active').each(function () {
				right = right + jQuery(this).outerWidth() + 4;	//plus 4 px for padding
			});
		} else {
			height = -999;
			right = 0;
		}

		$parent.css('bottom', height)
			.css('position','absolute')
			.css('right', right)
			.css('zIndex', 1999)

		//then update the collapse list if it is existing
		updateCollapseConv();
	}

	function updateCollapseConv() {
		//remove the old collapsed
		jQuery('#halo-collapsed-conv-wrapper').remove();
		var convs = jQuery('[data-convid]');
		var collapseList = '';
		convs.each(function () {
			var $this = jQuery(this);
			if (!$this.hasClass('halo-conv-active')) {
				var title = $this.find('[data-htoggle="conv"]').html();
				var convId = $this.attr('data-convid');
				collapseList += '<li><a href="javascript:void(0)" onclick="halo.message.openConvByConvId(\'' + convId + '\')">' + title + '</a></li>';
			}
		})

		//update with the new once if existing
		if (collapseList.length) {
			var collapse = jQuery('<div class="dropup" id="halo-collapsed-conv-wrapper">' +
				'<button id="halo-collapsed-conv-btn" type="button" class="halo-btn halo-btn-default halo-dropdown-toggle" data-htoggle="dropdown"><i class="fa fa-th-list"></i></button>' +
				'<ul class="halo-dropdown-menu" role="menu" aria-labelledby="dLabel">' +
				collapseList +
				'</ul>' +
				'</div>');
			jQuery('[data-halozone="message-zone"]').append(collapse);

			//collapse.css('bottom',collapse.outerHeight())
			collapse.css('bottom', 10)
			collapse.css('right', getActiveConvWidth() + 10)
				.css('position', 'fixed')
				.css('zIndex', 999);
			jQuery('#halo-collapsed-conv-btn').hdropdown()

			//flash collapse button if there is any unread message
			if (jQuery('.halo-conv-unread-counter', collapse).length) {
				halo.util.startFlash(jQuery('#halo-collapsed-conv-btn'), -1);
			} else {
				halo.util.stopFlash(jQuery('#halo-collapsed-conv-btn'));
			}
		}
	}

	function getParent($this) {
		var selector = $this.attr('data-target')
			, $parent

		if (!selector) {
			selector = $this.attr('href')
			selector = selector && /#/.test(selector) && selector.replace(/.*(?=#[^\s]*$)/, '') //strip for ie7
		}

		$parent = selector && jQuery(selector)

		if (!$parent || !$parent.length) $parent = $this.parent()

		return $parent
	}

	function getActiveConvWidth() {
		var activeWidth = 0;
		jQuery('.halo-conv-active').each(function () {
			activeWidth += jQuery(this).outerWidth() + 4; //plus 4px for padding among conversations
		});
		return activeWidth;
	}

	function canStackNewConv() {
		var activeWidth = getActiveConvWidth();

		var collapseWidth = 200;		//collapseWidth
		var convWidth = 250;
		return ((jQuery(window).width() - activeWidth - collapseWidth - convWidth) > 0);
	}

	/* CONV PLUGIN DEFINITION
	 * ========================== */

	var old = jQuery.fn.conv

	jQuery.fn.conv = function (option) {
		return this.each(function () {
			var $this = jQuery(this)
				, data = $this.data('conv')
			if (!data) $this.data('conv', (data = new Conv(this)))
			if (typeof option == 'string') data[option].call($this)
		})
	}

	jQuery.fn.conv.Constructor = Conv


	/* CONV NO CONFLICT
	 * ==================== */

	jQuery.fn.conv.noConflict = function () {
		jQuery.fn.conv = old
		return this
	}


	/* APPLY TO STANDARD CONV ELEMENTS
	 * =================================== */

	jQuery(document)
		.on('click.conv.data-api', '.conv form', function (e) {
			e.stopPropagation()
		})
		.on('click.conv-menu', function (e) {
			e.stopPropagation()
		})
		.on('click.conv.data-api', toggle, Conv.prototype.toggle)

}(window.jQuery);

/* ============================================================ message features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	message: {
		minWindowSize: 768,
		loaded: false,
		fullviewId: 0,
		loadConvs: function (convIds) {
			halo.jax.call("message", "LoadConvs", convIds);
		},
		openConvByPost: function(userId, targetUrl) {
			if(typeof targetUrl !== 'undefined'){
				if (!halo.storage.setDn('curpost', {url: targetUrl})) {
					//TODO
					// Store data using cookie
				}
			}
			// Open conservation
			halo.message.openConvByUserId(userId);
		},
		autoFillConv: function(params) {
			var post = halo.storage.getDn('curpost');
			if (!post) return false;
			params.$input.val(post.url + '\n');
			params.$input.caretToEnd();
			halo.storage.deleteDn('curpost');
		},
		openConvByUserId: function (userId) {
			//open fullview mode if in small size window
			if (!halo.message.hasConvWindow()) {
				halo.jax.call("message", "OpenConvByUserId", userId, 1);
			}
			//check if the conversation window is loaded on
			var $conv = jQuery('[data-convuserid="' + userId + '"]');
			if ($conv.length) {
				//just try to active the loaded conversation window
				var convId = $conv.attr('data-convid');
				//then keep the conv as focus
				halo.message.focusConv(convId);
			} else {
				halo.jax.call("message", "OpenConvByUserId", userId, 0);
			}
		},
		openConvByConvId: function (convId) {
			//do not open window in small size window
			if (!halo.message.hasConvWindow()) {
				return;
			}
			if (convId == halo.message.fullviewId) {
				return;
			}
			var $conv = jQuery('[data-convid="' + convId + '"]');
			if ($conv.length) {
				//just try to active the loaded conversation window
				halo.message.focusConv(convId);
			} else {
				halo.jax.call("message", "OpenConvByConvId", convId);
			}
		},
		changeConv: function (convId) {
			//only work on fullview mode
			halo.jax.call("message", "ChangeConv", convId);
		},
		focusConvFullView: function (convId) {
			setTimeout(function () {
				jQuery('#message-input-' + convId + ' textarea').focus();
			}, 100);
		},
		focusConv: function (convId) {
			//force the conv to be active state by deleting the current conv storage state
			halo.message.removeConvStorage(convId);
			var $conv = jQuery('[data-convid="' + convId + '"]');
			halo.message.displayConv(convId);
			//flash the conv window for take user focus
			halo.util.startFlash($conv.find('.halo-conv-window-title'), 3);
			//focus on the textarea
			$conv.find('textarea').focus();
		},

		updateLastSeenMessage: function (convId, lastseen_id) {
			halo.jax.call("message", "UpdateLastSeenMessage", convId, lastseen_id);
		},

		showOlderConvs: function () {
			halo.jax.call("message", "ShowOlderConvs");
		},

		setConvAsOldest: function (convId) {
			var $oldestMessage = jQuery('[data-convid="' + convId + '"] [data-msgid]').first();
			if ($oldestMessage.length) {
				$oldestMessage.addClass('halo-conv-message-oldest');
			}
		},

		addConv: function (content) {
			//get the conv zone
			var $content = jQuery(content);
			var zoneId = $content.data('halozone');
			var convId = $content.data('convid');
			if (convId == halo.message.fullviewId) {
				return;
			}
			//check if the conv is opened
			var $conv = jQuery('[data-halozone="' + zoneId + '"]');
			if ($conv.length) {
				//merge the conv content
				var $currContentWrapper = jQuery('.halo-conv-entry-wrapper', $conv);
				var $newContentWrapper = jQuery('.halo-conv-entry-wrapper', $content);
				var $mergeContent = halo.util.mergeContent(jQuery('[data-msgid]', $currContentWrapper), jQuery('[data-msgid]', $newContentWrapper), 'data-msgid');
                $mergeContent = halo.util.uniqueContent($mergeContent, 'data-msgid');
                $currContentWrapper.children().remove();
				$currContentWrapper.append(jQuery($mergeContent));
			} else {
				//insert the conv
				var $messageWrapper = jQuery('[data-halozone="message-zone"]');
				var $messageList = jQuery('[data-halozone="message-zone-list"]', $messageWrapper);
				if (!$messageList.length) {

				}
				$messageList.append($content);
			}
			//reinit the conv content
			halo.init($content);
			//display the Conv window
			halo.message.displayConv(convId);
		},
		showFullViewMessages: function (messages) {
			var $fullview = jQuery('.halo-conv-fullview-wrapper');
			var $contentWrapper = jQuery('.halo-conv-entry-wrapper', $fullview);
			if ($contentWrapper.length) {
				var $oldestMessage = jQuery('[data-msgid]', $contentWrapper).first();
				$oldestMessage.before(jQuery(messages));
			}

		},
		stopFullViewLoadMore: function () {
			var $fullview = jQuery('.halo-conv-fullview-wrapper');
			var $contentWrapper = jQuery('.halo-conv-entry-wrapper', $fullview);
			jQuery('.halo-conv-load-more', $fullview).addClass('hide');
			jQuery('.halo-conv-entry-wrapper', $fullview)
				.off('scroll')
		},
		stickFullViewScroll: function () {
			var $fullview = jQuery('.halo-conv-fullview-wrapper');
			var $contentWrapper = jQuery('.halo-conv-entry-wrapper', $fullview);
			$contentWrapper[0].scrollTop = $contentWrapper[0].scrollHeight;
		}, 
		submit: function (el) {
			var values = halo.util.getFormValues(jQuery(el).attr('id'));
			//reset the form
			halo.util.resetFormValues(jQuery(el).attr('id'));
			halo.jax.call("message", "Submit", values);
		},
		init: function (scope) {
			//only for registered user
			if (typeof halo_my_id === 'undefined' || halo_my_id == 0 || typeof halo_feature_message === 'undefined' || halo_feature_message == 0) {
				return;
			}
			//detect and init fullview mode
			halo.message.initFullViewMode(scope);

			//only for big screen size and fullview mode
			if (!halo.message.hasConvWindow()) {
				return;
			}

			//check if the halo message container exists
			if (!halo.message.loaded) {
				//then we need to do ajax call to get message container html
				halo.message.loaded = true;

				//display the active conv
				var convs = halo.storage.getDn('conv');
				var convIds = [];
				if (convs != null) {
					jQuery.each(convs, function (key, value) {
						var parts = key.split('_');
						if (parts.length == 2 && parts[0] == 'convid' && parseInt(parts[1]) > 0) {
							convIds.push(parts[1]);
						}
					});
				}
				//get the container
				setTimeout(function(){
					halo.jax.call('message', 'GetContainerHtml', convIds);
				},10000);
			} else {
				jQuery(scope)
					.on('keydown', '.halo-message-input', function (e) {
						var code = e.keyCode || e.which;
						if (code == 13 && !e.shiftKey) { //Enter keycode without shift
							e.preventDefault();
							//Do something
							halo.message.submit(jQuery(this).closest('form'));
							//stop futher processing
							return false;
						}
					})
				;

				//init conversation window
				jQuery('[data-htoggle="conv"]').conv('init');
			}
		}, 
		initFullViewMode: function (scope) {
			//full view layout
			var $fullview = jQuery('.halo-conv-fullview-wrapper', scope);
			if ($fullview.length) {
				$fullview.on('keydown', '.halo-message-input', function (e) {
					var code = e.keyCode || e.which;
					if (code == 13 && !e.shiftKey) { //Enter keycode without shift
						e.preventDefault();
						//Do something
						halo.message.submit(jQuery(this).closest('form'));
						//stop futher processing
						return false;
					}
				})
				;

				//@rule: in fullview mode, prevent to open chat window for the same conversation
				halo.message.fullviewId = $fullview.attr('data-fullview-convid');
				halo.message.stickFullViewScroll();
				//bind on scroll event on conv content
				jQuery('.halo-conv-entry-wrapper', $fullview)
					.off('scroll')
					.on('scroll', halo.util.throttle(function () {
						var $contentWrapper = jQuery(this);
						var scrollTop = $contentWrapper.scrollTop();
						if (scrollTop == 0) {
							//load older messages
							var $oldestMessage = jQuery('[data-msgid]', $contentWrapper).first();
							if ($oldestMessage.length) {
								if ($oldestMessage.hasClass('halo-conv-message-oldest')) {
									//this is the oldest message, do not need to load older message
								} else {
									var lastId = $oldestMessage.attr('data-msgid');
									//store the lastId for this conv, it will be used for scroll update
									//$conv.attr('data-conv-lastscrollid',lastId);
									halo.jax.call("message", "LoadOlderFullViewMessages", halo.message.fullviewId, lastId);
								}
							}
						}
					}, 500));

				function resizeConvHeight() {
					//keep the fullview wrapper height occupy the window height
					var $convEntries = jQuery('.halo-conv-entry-wrapper', $fullview);
					var pos = halo.util.getPosition($convEntries);
					var delta = jQuery(window).height() - ( pos.top + pos.height + $convEntries.next().height() + 40);
					$convEntries.css('height', pos.height + delta);
					jQuery('.halo-inbox-conv-list').height(jQuery('.halo-inbox-conv-view').height());
				}
				
				//some delay to make sure everything is rendered properly
				setTimeout(function() {
					resizeConvHeight();
				}, 100);
				//autoresize the conv content
				jQuery(window).on('resize', halo.util.throttle(function () {
					resizeConvHeight();
				}, 500));

				// Add message.autofill custom event
				$fullview.on('message.autofill', function(evt) {
					halo.message.autoFillConv({$input: jQuery(this).find('textarea.halo-message-input')});
				});
				// Trigger message.autofill event
				$fullview.trigger('message.autofill');
			}
		}, 
		hasConvWindow: function () {
			return (window.halo_popup_message_win == 1) && ((jQuery(window).width() > halo.message.minWindowSize));
		},
		hideConvWindow: function() {
			if(typeof window.halo_popup_message_win == 'undefined') window.halo_popup_message_win = 0;
			window.halo_popup_message_win = 0;
		},
		updater: function (scope) {
			//rule: do not do any update on small size window
			// if (!halo.message.hasConvWindow()) return;
			if (!jQuery('#halo-message-wrapper, .halo-conv-fullview-wrapper').length) return; //do not do any update if not enable
			values = {};
			values.form = {};

			//get the latest updated message
			var latestId = 0;
			jQuery('#halo-message-wrapper [data-convid], .halo-conv-fullview-wrapper').each(function () {
				var msg = jQuery('[data-msgid]', jQuery(this)).last();
				if (msg.length > 0) {
					var msgId = parseInt(msg.data('msgid'));
					latestId = (msgId > latestId) ? msgId : latestId;
				}
			});

			values.form["context"] = "message";
			values.form["latestId"] = latestId;
			//for fullview conv
			values.form["fullviewId"] = halo.message.fullviewId;
			return values;
		},

		updateUnreadCounter: function (convId) {
			var $conv = jQuery('[data-convid="' + convId + '"]');
			var unreadMessages = jQuery('.halo-message-unread', $conv);
			if (unreadMessages.length) {
				//check if the counter exist
				var counter = jQuery('.halo-conv-unread-counter', $conv);
				if (counter.length) {
					//update existing counter
					counter.html(unreadMessages.length);
				} else {
					//add new counter on conv window header
					jQuery('[data-htoggle="conv"]', $conv).append('<span class="halo-conv-unread-counter badge halo-badge">' + unreadMessages.length + '</span>');
                    halo.message.setUnreadStyle($conv);
					if (convId != 'panel') {
						//also update the counter on panel: recent counter + panel header counter
						//1.recent counter
						jQuery('[data-recent-convid="' + convId + '"] a').append('<span class="halo-message-unread badge halo-badge small">&nbsp;</span>');
						//2.panel header counter
						halo.message.updateUnreadCounter('panel');
					}
				}
			} else {
				//no unread message, remove counter on conv window header
				jQuery('.halo-conv-unread-counter', $conv).remove();
				halo.message.resetUnreadStyle($conv);
				if (convId != 'panel') {
					//also update the counter on panel: recent counter + panel header counter
					//1. recent counter
					jQuery('[data-recent-convid="' + convId + '"] .halo-message-unread').remove();
					//2. panel header counter
					halo.message.updateUnreadCounter('panel');
				}

			}
		},
        setUnreadStyle: function($ele) {
            $ele.addClass('unread-msg-style');
        },
        resetUnreadStyle: function($ele) {
            $ele.removeClass('unread-msg-style');
        },
        updateUnreadMsgInViewport: function($contentWrapper, convId) {
            var seen_id = 0;
            jQuery('[data-msgid]', $contentWrapper).each(function () {
                var $this = jQuery(this);
                var messageBagde = jQuery('.halo-message-unread', $this);
                if (messageBagde.length > 0 && ($this.position().top + $this.height()) < $contentWrapper.innerHeight()) {
                    //mark the message as read
                    messageBagde.hide('slow', halo.util.throttle(function () {
                        messageBagde.remove();
                        //update unread counter
                        halo.message.updateUnreadCounter(convId);

                    }, 200));

                    //update the seen_id
                    seen_id = $this.attr('data-msgid');
                }
            });
            //update the lastseen_id for this conv
            if (seen_id > 0) {
                halo.message.updateLastSeenMessage(convId, seen_id);
            }
        },
		displayConv: function (convId) {
			//make sure the conv win in open state
			var $conv = jQuery('[data-convid="' + convId + '"]');
			//get the current display status of conv
			var status = halo.storage.getAttr('conv', 'convid_' + convId, 'status');
			if (status === null) status = 'open';			//default status is open
			jQuery('[data-htoggle="conv"]', $conv).conv(status);
			//make sure the scroll is at first unread message
			var unreadMessages = jQuery('.halo-message-unread', $conv).closest('[data-msgid]');
			var firstUnreadMessage = unreadMessages.first();
			var viewportHeight = jQuery('.halo-conv-entry-wrapper', $conv).height();
			var $contentWrapper = jQuery('.halo-conv-entry-wrapper', $conv);
			//if the data-conv-lastid attr is set, use it as the scroll position
			var lastScrollMessageId = $conv.attr('data-conv-lastscrollid');
			//refresh the last scroll message id
			$conv.removeAttr('data-conv-lastscrollid');
			if (typeof lastScrollMessageId == 'undefined') {
				if (firstUnreadMessage.length) {
					if (firstUnreadMessage.position().top + firstUnreadMessage.innerHeight() > viewportHeight) {
						//only scroll if first unread message is not in the viewport
						var currScrollTop = $contentWrapper.scrollTop();
						var newScrollTop = (currScrollTop + firstUnreadMessage.position().top) - (viewportHeight - firstUnreadMessage.innerHeight());
						$contentWrapper.scrollTop(newScrollTop);
					} else if (firstUnreadMessage.position().top + firstUnreadMessage.innerHeight() * unreadMessages.length <= viewportHeight) {
                        // TODO
                    }
					//jQuery('.halo-conv-entry-wrapper',$conv).scrollTop(firstUnreadMessage.position().top - (viewportHeight - firstUnreadMessage.innerHeight()));
				} else {
					//there are no unread message then scroll to the end of the conv
					$contentWrapper.each(function () {
						this.scrollTop = this.scrollHeight;
					});
				}
			} else {
				//scroll to the last scroll message
				var lastScrollMessage = jQuery('[data-msgid="' + lastScrollMessageId + '"]', $conv);
				if (lastScrollMessage.length) {
					var currScrollTop = $contentWrapper.scrollTop();
					var newScrollTop = (currScrollTop + lastScrollMessage.position().top) - (viewportHeight - lastScrollMessage.innerHeight());
					$contentWrapper.scrollTop(newScrollTop);
				}
			}
			//update the unread message counter to the conv window title
			halo.message.updateUnreadCounter(convId);
            var $contentWrapper = jQuery('.halo-conv-entry-wrapper', $conv);
            //mark unread message as read when not displayed scrolling
            if ($contentWrapper.closest('[data-convid]').hasClass('open')) {
                halo.message.updateUnreadMsgInViewport($contentWrapper, convId);
            }
			//bind on scroll event on conv content
			$contentWrapper
				.off('scroll')
				.on('scroll', halo.util.throttle(function () {
                    var $that = jQuery(this);
					var scrollTop = $that.scrollTop();
					if (scrollTop == 0) {
						//load older messages
						var $oldestMessage = jQuery('[data-msgid]', $that).first();
						if ($oldestMessage.length) {
							if ($oldestMessage.hasClass('halo-conv-message-oldest')) {
								//this is the oldest message, do not need to load older message
							} else {
								var lastId = $oldestMessage.attr('data-msgid');
								//store the lastId for this conv, it will be used for scroll update
								$conv.attr('data-conv-lastscrollid', lastId);
								halo.jax.call("message", "LoadOlderMessages", convId, lastId);
							}
						}
					} else {
						//mark unread message as read
                        if (!$contentWrapper.closest('[data-convid]').hasClass('open')) {
                            return;
                        }
						halo.message.updateUnreadMsgInViewport($that, convId);
					}
				}, 500));

            var $textArea = $conv.find('textarea.halo-message-input');
            $textArea.on('click', function() {
                halo.message.updateUnreadMsgInViewport($contentWrapper, convId);
            });

			// Add message.autofill custom event
			$conv.on('message.autofill', function(evt) {
				halo.message.autoFillConv({$input: $textArea});
			});

			// Trigger messange.autofill event
			$conv.trigger('message.autofill');
		},

		toggleDisplayConv: function (convId) {
			//make sure the conv win in open state
			var $conv = jQuery('[data-convid="' + convId + '"]');
			jQuery('[data-htoggle="conv"]', $conv).conv('toggle');
		},

		removeConv: function (convId) {
			var $conv = jQuery('[data-convid="' + convId + '"]');
			//update all the conv after this conv
			jQuery('[data-htoggle="conv"]', $conv).conv('remove');
		},

		displayContainer: function (html) {
			//check if the container is not appended
			if (!jQuery('#halo-message-wrapper').length) {
				var container = jQuery(html);
				jQuery('body').append(container);
				//get the current display status of panel
				var status = halo.storage.getAttr('conv', 'convid_panel', 'status');
				if (status === null) status = 'open';			//default status is open
				jQuery('[data-htoggle="conv"]', container).conv(status);
				//init the container
				halo.message.init(container);
			}
		},

		removeConvStorage: function (convId) {
			halo.storage.deleteObj('conv', 'convid_' + convId);
		}
	}
});

/* ============================================================ Poll
 *
 * ============================================================ */
jQuery.extend(true, halo, {
    poll: {
        submitAnswer: function() {
            var data = halo.util.getFormValues('PollAnswersForm');
            halo.jax.call("poll", "SubmitAnswer", data);
        },
        getOther: function(pollId, order) {
            halo.jax.call("poll", "ShowOtherPoll", pollId, order);
        }
    }
});

