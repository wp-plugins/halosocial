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
	home: {
		listStream: function () {
			//halo.util.setUrlParam({'usec':'stream','pg':1});
		}, 
		displaySection: function (section) {
			var sectionWrapperSelector = '[data-halozone="halo-' + section + 's-wrapper"]';
			var $sectionContent = jQuery(sectionWrapperSelector);
			var $sectionHeader = jQuery('.halo-section-heading',$sectionContent.parent());
			if ($sectionContent.children().length == 0) {
				//get the current filters
				if ($sectionHeader.length && jQuery('.filter_form', $sectionHeader).length) {
					var form_id = jQuery('.filter_form', $sectionHeader).attr('id');
					var values = halo.util.getFormValues(form_id);
				} else {
					//no filter provided, get emtpy form
					var values = halo.util.initFormValues();
				}
				halo.util.setFormValue(values, 'usec', section);
				halo.jax.call('home', 'DisplaySection', values);
			}
		},
		refreshSection: function (section) {
			//clear the current section
			var sectionWrapperSelector = '[data-halozone="halo-' + section + 's-wrapper"]';
			var $sectionContent = jQuery(sectionWrapperSelector);
			$sectionContent.children().remove();
			//get the fresh section content
			halo.home.displaySection(section);
		}, 
		deleteMe: function () {
			var values = halo.util.getFormValues('popupForm');
			halo.jax.call("group", "DeleteMe", values);
		}
	}
});

/* ============================================================ Comment features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	comment: {
		initCommentInput: function (el) {
			if(jQuery(el).data('initedCommentInput')) return;
			jQuery(el)
				.autosize()
				.textcomplete([
					{ // user tag
						match: /\B@(\w*)$/,
						search: function (term, callback) {
							//callback(cache[term], true); // Show local cache immediately.
							jQuery.post(halo_jax_targetUrl,
								{
									"com": "autocomplete",
									"func": "searchUsers",
									"term": term,
									csrf_token: jQuery('meta[name="csrf_token"]').attr('content')
								},
								function () {
								},
								'json'
							)
								.done(function (resp) {
									callback(resp); // Resp must be an Array
								})
								.fail(function () {
									callback([]); // Callback must be invoked even if something went wrong.
								});
						},
						template: function (ele) {
							// `ele` is an element of callbacked array.
							return '<img src="' + ele.image + '"></img> ' + ele.name;
						},
						index: 1,
						replace: function (ele) {
							return '@' + ele.name + ' ';
						}
					},
					{ // emoticons
						match: /\B:([\-*\w]*)$/,
						search: function (term, callback) {
							callback(jQuery.map(emojify.emojiNames, function (emoji) {
								return ((emoji.indexOf(term) === 0) || (emoji.indexOf(':' + term) === 0)) ? emoji : null;
							}));
						},
						template: function (value) {
							return emojify.replace(':' + value + ':');
						},
						replace: function (value) {
							return ':' + value + ': ';
						},
						index: 1,
						maxCount: 8
					}
				])
				.overlay([
					{	//user tagging
						match: /\B[@|#]\w+/g,
						css: {
							'background-color': '#d8dfea'
						}
					}
				])
				.on('keydown', function (e) {
					var code = e.keyCode || e.which;
					if (code == 13 && !e.shiftKey) { //Enter keycode without shift
						//validate input
						if(jQuery(this).val() == ''){
							return false;
						}
						//Do something
						halo.comment.submit(jQuery(this).closest('form'));						

						//remove shareblock if exists
						halo.share.resetShareBlock(jQuery(this));
						
						//stop futher processing
						return false;
					}
				})
				//.css('height','25px')
				.focus()
			;
			jQuery(document).trigger('lazyloading.halo');
			//mark this input as inited
			jQuery(el).data('initedCommentInput', true);
			halo.share.bindEvent(jQuery(el),'.textcomplete-wrapper');
		},

		init: function (scope) {
			//turn comment box to halotextarea
			jQuery('.halo-comment-box', scope).each(function () {
				jQuery(this).one('click',function(){
					halo.comment.initCommentInput(this);
				});
			});
			jQuery('.halo-comment-actor-switcher .selectpicker', scope)
				.selectpicker()
				.on('change', function() {
					var actorParts = jQuery(this).val().split('.');
					if(actorParts.length == 2) {
						var displayContext = halo.util.closest(jQuery(this), '[name="comment_display_context"]');
						if(displayContext && displayContext.length) {
							displayContext.val(actorParts[0]);
						}
						var displayId = halo.util.closest(jQuery(this), '[name="comment_display_id"]');
						if(displayId && displayId.length) {
							displayId.val(actorParts[1]);
						}
					}
				});
		},

		submit: function (el) {
			var values = halo.util.getFormValues(jQuery(el).attr('id'));
			
			//check comment switcher 
			var $switcher = jQuery(el).closest('.haloj-content-block').find('.halo-comment-actor-switcher');
			if($switcher.length) {
				var display_context = $switcher.find('[name="comment_display_context"]').val();
				var display_id = $switcher.find('[name="comment_display_id"]').val();
				if(display_context && display_id) {
					halo.util.setFormValue(values, 'display_context', display_context);
					halo.util.setFormValue(values, 'display_id', display_id);
				}
			}
			//check submit action is ready
			if(!halo.util.canSubmit(el)){
				return false;
			}

			//reset the form
			halo.util.resetFormValues(jQuery(el).attr('id'));
			
			//remove photo attached if exist
			jQuery('.halo-uploader-holder', el).empty();

			halo.jax.call("comment", "Submit", values, halo.jax.successCb(function(){
				halo.comment.updateCounter(el, 1);
			}));
		},
		viewall: function (id, context) {
			halo.jax.call("comment", "ViewAll", id, context);
		},
		'delete': function (id) {
			var wrapper = jQuery('[data-comment-id="' + id + '"]').parent();
			halo.jax.call("comment", "Delete", id, halo.jax.successCb(function(){
				halo.comment.updateCounter(wrapper, -1);
			}));
		},
		updateCounter: function(wrapper, delta) {
			//update counter
			if(wrapper.length) {
				$commentCount = halo.util.closest(wrapper, '.comment-count');
				if($commentCount) {
					var count = parseInt($commentCount.html());
					$commentCount.html(count + delta);
				}
			}		
		},
		edit: function (id) {
			halo.jax.call('comment', 'ShowEdit', id);
		},
		editFocus: function (id) {
			var input = jQuery('textarea[name="comment_' + id + '"]');
			//make sure the comment textarea is shown
			input.closest('.halo-stream-comment-item').removeClass('hidden').parent().removeClass('hidden');
			//give the textarea focus status
			input.focus().click();
		},
		showEdit: function (id) {
			//get the textarea element of the edit form
			var el = jQuery('[data-raw-input="message_edit_' + id + '"]');
			if (el.length) {
				//hide the current comment
				jQuery('#comment_' + id).hide();
				//display the edit form
				jQuery('#comment_edit_' + id).show();
				el.height(60);
				//init textarea
				halo.comment.initCommentInput(el);

				//bind cancel or escape keypress on the comment edit form
				el.on('keydown', function (e) {
					var code = e.keyCode || e.which;
					if (code == 27) { //Enter keycode without shift
						//Do something
						halo.comment.doneEdit(id);
						//stop futher processing
						return false;
					}
				});
				//set focus
				jQuery('#comment_edit_' + id + ' textarea').focus().click();
			}
		},
		doneEdit: function (id) {
			//get the textarea element of the edit form
			var el = jQuery('[data-raw-input="message_edit_' + id + '"]');
			if (el.length) {
				//hide edit form
				jQuery('#comment_edit_' + id).remove();
				//display the comment content
				jQuery('#comment_' + id).show();
			}
		},
		removePhoto: function(ele){
			var $wrapper = jQuery(ele).closest('.halo-comment-body').find('.halo-comment-attachment');
			if($wrapper.length){
				$wrapper.find('a').remove();
				jQuery(ele).remove();
			}
			var commentId = $wrapper.closest('[data-comment-id]').attr('data-comment-id');
			if(commentId){
				halo.jax.call('comment','RemovePhoto',commentId);
			}
			return false;
		}
	}
});
!function ($) {
	jQuery(document)
		.on('uploader.uploadSuccess', '.comment_form .halo-uploader-holder', function (e, response) {
			jQuery(this).append('<input type="hidden" name="photo_id" value="'+response.id+'"/>')
		});
}(window.jQuery);

/* ============================================================ Share features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	share: {
		windows: {},
		blankWindow: function(){
			var winId = halo.util.uniqID();
			halo.share.windows[winId] = window.open('', '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');
			return winId;
		},
		startShare: function(url,winId){
			if(typeof halo.share.windows[winId] != 'undefined'){
				var win = halo.share.windows[winId];
				var id = setInterval(function () {
					try {
						if (win.location.href.indexOf("#halo_redirect_close") >= 0) {
								clearInterval(id);
								win.close();
						}
					} catch(e){
					
					}
				}, 3000);		
			}
		},
		getShareUrl: function(url,type){
			if(type == 'google'){
				return 'https://plus.google.com/share?url=' + encodeURIComponent(url);
			} else if (type == 'facebook'){
				var app_id = 'app_id=' + halo.share.getFacebookAppId();
				var link = 'link=' + encodeURIComponent(url);
				var redirect_uri = 'redirect_uri=' + encodeURIComponent(url);
				return 'https://www.facebook.com/dialog/feed?' + app_id + '&' + link + '&' + redirect_uri + '&display=popup'
			}
			
		},
		email: function(postId) {
			var values = halo.util.getFormValues('popupForm');
			if (jQuery.isEmptyObject(values.form)) {
				values = halo.util.initFormValues();
				halo.util.setFormValue(values, 'post_id', postId);
			}
			halo.jax.call("post", "ShareByEmail", values);
		},
		google: function (url) {
			if (typeof url == 'undefined') {
				url = location.href + '#halo_redirect_close';
			}
			//trigger share event
			jQuery(document).trigger('halo_share', ['google']);
			var shareUrl = halo.share.getShareUrl(url,'google');
			win = window.open(shareUrl, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');
			var id = setInterval(function () {
				try {
					if (win.location.href.indexOf("#halo_redirect_close") >= 0) {
							clearInterval(id);
							win.close();
					}
				} catch(e){
				
				}
			}, 3000);
		}, 
		getGoogleCount: function (url, target) {
			var ggCount = 0;
			//for google share count, there is cross site javascript issue so need to use google API to get the counter
			HALOModernizr.load([
				{
					load: ['https://apis.google.com/js/client:plusone.js'],
					complete: function () {
						var params = {
							nolog: true,
							id: url,
							source: "widget",
							userId: "@viewer",
							groupId: "@self"
						};
						//workaround solution, wait for 0.1 seconds before the google api is loaded properly
						setTimeout(function () {
							var apiKey = jQuery('#halo_ggApiKey').attr('data-gg');
							if(!apiKey) return;
							gapi.client.setApiKey(apiKey)
							gapi.client.rpcRequest('pos.plusones.get', 'v1', params).execute(function (resp) {
								if (typeof resp.message !== 'undefined' && resp.message.indexOf('Access Not Configured') >= 0) {
									//API for google plus is not enabled
									halo.log('error', resp);
									jQuery(target).text('');
								} else {
									if (typeof resp.result !== 'undefined' && typeof resp.result.metadata != 'undefined') {
										jQuery(target).text(resp.result.metadata.globalCounts.count);
									}
								}

							});
						}, 100);
					}
				}
			])
		}, 
		getFacebookAppId: function () {
			var e = jQuery('#halo_fbApiKey');
			return e.length ? e.attr('data-fb') : '';
		}, 
		facebook: function (url) {
			if (typeof url == 'undefined') {
				url = location.href + '#halo_redirect_close';
			}
			//trigger share event
			jQuery(document).trigger('halo_share', ['facebook']);
			var shareUrl = halo.share.getShareUrl(url,'facebook');
			win = window.open(shareUrl, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');
			var id = setInterval(function () {
				try {
					if (win.location.href.indexOf("#halo_redirect_close") >= 0) {
							clearInterval(id);
							win.close();
					}
				} catch(e){
				
				}
			}, 3000);
		},
		getFacebookCount: function (url, target) {
			var fbCount = 0;
			jQuery.getJSON('http://graph.facebook.com/?ids=' + url, function (data) {
				if (typeof data[url] !== 'undefined' && typeof data[url].shares !== 'undefined') {
					fbCount = data[url].shares;
				}

				jQuery(target).text(fbCount);
			});
		},
		init: function (scope) {
			jQuery('.halo-share-toggle', scope).each(function () {
				//enable popover on the element
				$e = jQuery(this);
				$shareContainer = $e.siblings('.halo-share-items');
				$e.on('click', function () {
					toggleShare($shareContainer)
				});
				$e.mouseenter(function () {
					openShare($shareContainer);
				});
				function openShare($container) {
					$container.removeClass('hidden');
					$container.trigger('shown.halo.share');

					//update counter
					jQuery('.halo-share-item-wrapper').each(function () {
						//update counter
						var counterFn = jQuery(this).attr('data-counter');
						var url = jQuery(this).attr('data-url');
						var target = jQuery('#count_' + jQuery(this).attr('data-name'));
						if (typeof url === 'undefined') url = location.href;
						if (typeof counterFn !== 'undefined' && typeof halo['share'][counterFn] == 'function') {
							halo['share'][counterFn].apply(this, [url, target]);
						}
					});
				}

				function closeShare($container) {
					$container.addClass('hidden');
					$container.trigger('hide.halo.share');
				}

				function toggleShare($container) {
					if ($container.hasClass('hidden')) {
						openShare($container);
					} else {
						closeShare($container);
					}
				};
				var clickEveryWhere = function (e) {
					if (!jQuery(e.target).hasClass('halo-share-toggle') && jQuery(e.target).closest('.halo-share-items').length === 0) {
						closeShare($shareContainer);
					}
				}

				$shareContainer.on('shown.halo.share', function () {
					jQuery(document).on('click', clickEveryWhere);

				});

				$shareContainer.on('hide.halo.share', function () {
					jQuery(document).off('click', clickEveryWhere);
				});
			});

		}
		, addShareLinkBlock: function(ele,url) {
			var uid = halo.util.uniqID();
			var halozone = 'share.link.block.' + uid;
			var template = '<div class="halo-share-link-block" data-halozone="' + halozone + '"><div class="halo-loading"></div></div>';
			var $target = jQuery(ele);
			//check for existing share link block
			var $shareBlock = jQuery('.halo-share-link-block',$target.parent());
			if($shareBlock.length){
				$shareBlock.replaceWith(jQuery(template));
			} else {
				jQuery(template).insertAfter($target);
			}
			halo.jax.call("system", "AddShareLinkBlock", url, halozone);
			//add the url to the ingore list
			var $input = jQuery('.halo-share-link-input',$target);
			if($input.length){
				//trigger event for share link removal
				$input.trigger('addLink',url);
			}
			
		}
		, removeShareLinkBlock: function(ele){
			var $ele = jQuery(ele);
			var $block = $ele.closest('.halo-share-link-block');
			if($block.length){
				$block.empty();
				$block.append('<input name="nopreview" type="hidden" value="1"/>');
			}
		}
		, removeLinkPreview: function(ele,context,id){
			var $ele = jQuery(ele);
			var $preview = $ele.closest('.halo-url-preview');
			$preview.remove();
			//call ajax to update
			halo.jax.call("system", "RemoveLinkPreview", context, id);
		}
		, resetShareBlock: function(input){
			var $this = jQuery(input);
			var selector = $this.data('gbShareParent');
			if(selector){
				var $parent = $this.closest(selector);
				if($parent.length){
					var $block = jQuery('.halo-share-link-block',$parent.parent());
					if($block.length){
						$block.remove();
					}
				}
			}
		
		}
		, parseShareLink: function(ele){
			var $ele = jQuery(ele);
			//only parse if there is no share link block attached to the element
			var $shareBlock = jQuery('.halo-share-link-block.halo-link-preview',$ele.parent());
			if($shareBlock.length) {
				return ;
			}
			
			var $input = jQuery('textarea',$ele);		//get input val
			var text = $input.val();
			var urls = halo.util.parseUrls(text);
			var url = null;
			//function to get available url in a url list
			function getUrl(urls,$input){
				var ignoreUrlString = $input.data('ignoreUrls');
				var ignoreUrls = [];
				if(ignoreUrlString && ignoreUrlString.length){
					ignoreUrls = ignoreUrlString.split(',');
				}
				if(urls.length){
					var url = urls.shift();
					while(url){
						if(ignoreUrls.indexOf(url) < 0){
							return url;
						}
						url = urls.shift();
					}
				}
				return null;
			}
			if(urls && urls.length){
				url = getUrl(urls,$input);
				//add share block
				if(url){
					halo.share.addShareLinkBlock($ele,url);
				}
			}
		}
		, bindEvent: function($ele,selector){
			$ele.addClass('halo-share-link-input');
			$ele.data('gbShareParent',selector);
			$ele.on('keyup', halo.util.throttle(function () {
				var $this = jQuery(this);
				var selector = $this.data('gbShareParent');
				if(selector){
					var $parent = $this.closest(selector);
					if($parent.length){
						halo.share.parseShareLink($parent);
					}
				}
			}, 500))
			.on('addLink',function(event,url){
				var $this = jQuery(this);
				var ignoreUrls = $this.data('ignoreUrls');
				ignoreUrls = halo.util.addValToArrText(ignoreUrls,url);
				$this.data('ignoreUrls',ignoreUrls);
			})
		}
	}
});

/* ============================================================ status features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	status: {
		taggedUsers: false,
		incomplete: false,
		submit: function () {
			if (halo.util.canSubmit()) {
				//get the active attachment form
				var activeTab = jQuery('.halo-share-box .tab-pane.active');
				var attachmentForm = jQuery('form', activeTab);
				var statusForm = jQuery('#status_form');
				var infoForm = jQuery('#status_form_info');

				//mark as completed
				halo.status.incomplete = false;

				if (statusForm.length && statusForm.valid() && attachmentForm.length && attachmentForm.valid() && infoForm.length && infoForm.valid()) {
					//combine values from 3 forms
					var values = halo.util.getFormValues('status_form');
					values.form = jQuery.extend({}, values.form, halo.util.getFormValues(infoForm.attr('id')).form, halo.util.getFormValues(attachmentForm.attr('id')).form);
					//rtn = {"form":frmValue};
					halo.jax.call("status", "SubmitStatus", values);
				}
			}
		},

		reset: function () {
			var $statusForm = jQuery('.status_form_wrapper');
			if ($statusForm.length) {
				halo.jax.call("status", "ResetStatusForm", $statusForm.attr('data-context'), $statusForm.attr('data-target'));
			}
			/*
			 //reset the form
			 halo.util.resetFormValues('status_form');
			 halo.util.resetFormValues('status_form_info');
			 var activeTab = jQuery('.halo-share-box .tab-pane.active');
			 halo.util.resetFormValues(activeTab.attr('id'));

			 //remove all queued media/photo
			 jQuery('[data-halo-photo-preview]').remove();

			 //reset video share
			 halo.video.displayShareVideo('','','','');
			 jQuery('[name="video_path"]',activeTab).val('');
			 */
		},
		'delete': function (id) {
			halo.popup.confirmDialog(__halotext('Delete Activity Confirm'), __halotext('Are you sure you want to delete this activity?'), 'halo.jax.call(\'status\', \'DeleteStatus\', \'' + id + '\')'); 
		},
		editPost: function (id) {
			halo.jax.call('status', 'EditPostForm', id);
		},
		cancelEditPost: function(id) {
			halo.jax.call('status', 'CancelEditPost', id);
		},
		doneEditPost: function(id) {
			var values = halo.util.getFormValues('edit_post_form_'+id);
			halo.jax.call('status', 'DoneEditPost', id, values);
		},
		deleteCallBack: function (id) {
			halo.popup.close();
			jQuery('[data-actid="' + id + '"]').remove();
		},
		changePrivacy: function (id) {
			var input = jQuery('[name="act_privacy_' + id + '"]');
			halo.jax.call('status', 'ChangePrivacy', id, input.val());
		},
		initLocation: function (scope) {
			//enable autocomplete on the share_info_input
			var input = jQuery('.halo-share-info-location .share_info_input', scope);
			input.on('location.change.halo', function (event, datum) {
				doneEditLocation(datum);
			});
			input.on('change', function(){
				var $this = jQuery(this);
				if(!$this.val().length){
					//consider as removing location
					input.trigger('location.change.halo', [{'name':'','lat':'','lng':''}]);
				}
			});
			//bind on show event
			jQuery('.halo-share-info-location', scope).on('shown.display.halo', function () {
				//autodetect current location
				if (!halo.location.hasShareLocation()) {
					halo.location.autodetect({}, function () {
						var currentPos = halo.location.getCurrentPosition();
						if (currentPos) {
							//auto set the current location as share location
							var datum = {lat: currentPos.lat(), lng: currentPos.lng(), name: halo.location.getCurrentAddress()}
							setShareLocation(datum);
							input.focus();
						}
					});
				} else {
					//make sure the current location is displayed properly
					jQuery('[data-halo-location-share]').val(jQuery('[name="share_location_name"]').val());
					input.focus();
				}
			});

			function doneEditLocation(datum) {
				setShareLocation(datum);
				jQuery('.halo-share-info-location').addClass('hidden');
			}

			function setShareLocation(datum) {
				if (datum.name.length) {
					jQuery('.halo-share-info-loc-icon').attr('title', datum.name);
				} else {
					jQuery('.halo-share-info-loc-icon').attr('title', __halotext('Location'));
				}
				jQuery('.halo-share-info-loc-icon .halo-loc-text').text(' ' + datum.name);
				//assign to the hidden input
				jQuery('[name="share_location_name"]').val(datum.name);
				jQuery('[name="share_location_lat"]').val(datum.lat);
				jQuery('[name="share_location_lng"]').val(datum.lng);
				jQuery('[data-halo-location-share]').val(datum.name);
			}

		},
		initTaggedUser: function (scope) {
			var input = jQuery('.halo-share-info-taggeduser .share_info_input', scope);

			//bind on show event
			jQuery('.halo-share-info-taggeduser', scope).on('shown.display.halo', function () {
				input.focus();
			});

			var tagApi = input.tagsManager({
				onlyTagList: true,
				tagClass: 'tm-tag-info',
				preventSubmitOnEnter: true,
				externalTagId: true,
				tagsContainer: jQuery('.halo-share-info-label')
			})
				.on('tm:popped', function (tag, tagId) {
					jQuery('[name="share_tagged_list"]').val(tagApi.data("tlid").join(','));
				})
				.on('tm:spliced', function (tag, tagId) {
					jQuery('[name="share_tagged_list"]').val(tagApi.data("tlid").join(','));
				})

			var users = new Bloodhound({
				datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
				queryTokenizer: Bloodhound.tokenizers.whitespace,
				remote: {
					url: halo.util.mergeUrlParams(halo_jax_targetUrl, {'term':'%QUERY', 'com':'autocomplete', 'func':'searchUsers', 
																'csrf_token':jQuery('meta[name="csrf_token"]').attr('content')}),
					beforeSend: function (jqXhr, settings) {
						settings.data = JSON.stringify({
							"com": "autocomplete",
							"func": "searchUsers",
							csrf_token: jQuery('meta[name="csrf_token"]').attr('content')
						});
						return true;
					}
				},
			});
			users.initialize();
			function suggestionTemplate(context) {
				return Hogan.compile('<p class="name"><image class="halo-suggestion-img" src={{image}}>{{name}}</p>').render(context);
			};

			function doneTagging() {
				var tagged = jQuery('[name="share_tagged_list"]').val();
				/*
				 if(jQuery.trim(tagged).length){
				 jQuery('.halo-share-info-tag-icon i, .halo-share-info-tag-icon span').addClass('text-primary');
				 } else {
				 jQuery('.halo-share-info-tag-icon i, .halo-share-info-tag-icon span').removeClass('text-primary');
				 }
				 */
			}

			function onTaggingUser(e, datum) {
				tagApi.tagsManager("pushTag", datum.name, false, datum.id);
				jQuery('[name="share_tagged_list"]').val(tagApi.data("tlid").join(','));

			}

			input.typeahead({highlight: true}, {
				name: 'taggedusers',
				displayKey: 'name',
				source: users.ttAdapter(),
				templates: {
					empty: '<div class="halo-location-not-found text-center">' + __halotext('No User Matched') + '</div>',
					suggestion: suggestionTemplate
				},
				engine: Hogan
			})
				.on('typeahead:selected', onTaggingUser)
				.on('typeahead:autocompleted', onTaggingUser)
				.on("focusout", function () {
					doneTagging();
				})
				.on('keydown', function (e) {
					var code = e.keyCode || e.which;
					if (code == halo.keycode.ENTER && !e.shiftKey) { //Enter keycode without shift
						e.preventDefault();
						if (jQuery.trim(jQuery(this).val()) == '') {
							//clear location setting
							doneTagging();
							return false;
						}
					}
				})

			;
		},

		init: function (scope) {
			//turn status box to gbtextarea
			jQuery('.halo-status-box', scope).each(function () {
				jQuery(this).autosize({})
					.textcomplete([
						{ // user tag
							match: /\B@(\w*)$/,
							search: function (term, callback) {
								//callback(cache[term], true); // Show local cache immediately.

								jQuery.post(halo_jax_targetUrl,
									{
										"com": "autocomplete",
										"func": "searchUsers",
										"term": term,
										csrf_token: jQuery('meta[name="csrf_token"]').attr('content')
									},
									function () {
									},
									'json'
								)
									.done(function (resp) {
										callback(resp); // Resp must be an Array
									})
									.fail(function () {
										callback([]); // Callback must be invoked even if something went wrong.
									});
							},
							template: function (ele) {
								// `ele` is an element of callbacked array.
								return '<img src="' + ele.image + '"></img> ' + ele.name;
							},
							index: 1,
							replace: function (ele) {
								return '@' + ele.name + ' ';
							}
						},
						{ // emoticons
							match: /\B:([\-*\w]*)$/,
							search: function (term, callback) {
								callback(jQuery.map(emojify.emojiNames, function (emoji) {
									return ((emoji.indexOf(term) === 0) || (emoji.indexOf(':' + term) === 0)) ? emoji : null;
								}));
							},
							template: function (value) {
								return emojify.replace(':' + value + ':');
							},
							replace: function (value) {
								return ':' + value + ': ';
							},
							index: 1,
							maxCount: 8
						},
					])
					.overlay([
						{	//user tagging
							match: /\B[@|#]\w+/g,
							css: {
								'background-color': '#d8dfea'
							}
						}
					]);
				halo.share.bindEvent(jQuery(this),'.textcomplete-wrapper');
			})
			;
			//init all the share info data
			halo.status.initLocation(scope);
			halo.status.initTaggedUser(scope);

			//click to show halo-status-function and halo-status-heading
			jQuery('.halo-status-box', scope).one('click', function () {
				jQuery('.halo-status-function', scope).removeClass('hidden');
				jQuery('.halo-status-heading', scope).removeClass('hidden');
				jQuery('.halo-status-attachment', scope).removeClass('hidden');
				jQuery('.md-header', scope).removeClass('hidden');
			})

			//bind video url fetching event
			jQuery('[name="video_path"]', scope).on('change', function (e) {
				var url = jQuery(this).val();
				var width = jQuery('.halo-sharebox-embeded-player').width();
				//player size should fixed in 4:3 ratio
				var height = Math.floor(parseInt(width) * 3 / 4);
				jQuery('.halo-sharebox-embeded-player').css('height', height + 'px');
				//show loading icon
				jQuery('.halo-sharebox-embeded-player').html(halo.template.getJaxLoading(2));
				halo.jax.call("video", "FetchVideo", url, width, height);
			});

		}
	}
});

/* ============================================================ review features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	review: {
		submit: function (context, target_id) {
			var values = halo.util.getFormValues('popupForm');
			halo.jax.call("review", "Submit", context, target_id, values);
		}, 
		edit: function (reviewId) {
			var values = halo.util.getFormValues('popupForm');
			halo.jax.call("review", "Edit", reviewId, values);
		}, 
		approve: function (reviewId) {
			var values = halo.util.getFormValues('popupForm');
			halo.jax.call("review", "Approve", reviewId, values);
		}, 
		deleteMe: function (reviewId) {
			var values = halo.util.getFormValues('popupForm');
			halo.jax.call("review", "Delete", reviewId, values);
		}, 
		viewall: function (targetId, context) {
			halo.jax.call("review", "ViewAll", targetId, context);
		}, 
		removeReviewBtn: function () {
			jQuery('.halo-review-btn').remove();
		}, 
		haveReviews: function (count) {
			if (count) {
				jQuery('.halo-no-reviews').addClass('hidden');
			} else {
				jQuery('.halo-no-reviews').removeClass('hidden');
			}
		}
	}
});

/* ============================================================ browse features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	browse: {
		loadScriptCb: null,
		$preferSection: null,
		currentLocation: null,
		inited: false,
		isBrowsing: false,
		ready: false,
		loadCurrentLocation: function () {
			halo.location.autodetect({}, function () {
				var currentPos = halo.location.getCurrentPosition();
				if (currentPos) {
					halo.browse.setCurrentLocation({lat: currentPos.lat(), lng: currentPos.lng(), name: halo.location.getCurrentAddress()});
					halo.browse.reloadResults();
				}
			});
		},
		setCurrentLocation: function (locData) {
			jQuery('input.halo-field-location-lat').val(locData.lat);
			jQuery('input.halo-field-location-lng').val(locData.lng);
			jQuery('input.halo-field-location-name').val(locData.name);
		},
		search: function(){
			jQuery('.navbar-form').submit();
		},
		mobileSearch: function(){
			halo.popup.setFormTitle(__halotext('Search'));
			var content = jQuery('.halo-popup-navbar-search');
			if(content.length){
				halo.popup.setFormContent(content.html());
			}
			halo.popup.showForm('fullscreen');
			halo.popup.addFormAction({"name": __halotext('Search'),"onclick": "halo.browse.search()","href":"javascript:void(0);"})
			halo.popup.addFormActionCancel();
		},
		showMobileFilters: function(selector,title){
			var form = jQuery(selector);
			//halo.init(jQuery('.mfp-content'));
			if(form.length){
				var clonedForm = form.clone(true,true);
				clonedForm.addClass('cloned-form');
				halo.popup.setFormTitle(title);
				jQuery('.hidden-xs',clonedForm).each(function(){
					jQuery(this).removeClass('hidden-xs');
					jQuery(this).addClass('cloned-hidden-xs');
				});
				halo.popup.showForm('fullscreen');
				halo.popup.addFormAction({"name": __halotext('OK'),"onclick": "halo.browse.updateMobileForm('"+selector+"')","href":"javascript:void(0);"});
				halo.popup.addFormActionCancel();
				clonedForm.appendTo(jQuery('.halo-popup-content'));
				jQuery('.halo-slider-input').slider();
			}
			
		},
		updateMobileForm: function(selector){
			var form = jQuery(selector+':not(.cloned-form)');
			var clonedForm = jQuery('.cloned-form');
			if(form.length && clonedForm.length){
				jQuery('.cloned-hidden-xs',clonedForm).each(function(){
					jQuery(this).addClass('hidden-xs');
					jQuery(this).removeClass('cloned-hidden-xs');
				});
				clonedForm.removeClass('cloned-form');
				form.replaceWith(clonedForm);
			}
			halo.popup.close();
			halo.browse.reloadResults();
		},		
		reloadResults: function () {
			if(halo.popup.isOpen()){
				return;		// do not do the ajax load on displaying filter form on popup
			}
			if(halo.browse.isBrowsing){
				var filters = halo.util.getFormValues('filter_form');
				filters = filters.form;
				var param = {};
				for (index = 0; index < filters.length; ++index) {
					param[filters[index][0]] = filters[index][1];
				}

				var filters = halo.util.getFormParams('filter_form');
				halo.browse.displaySection();
				halo.util.setUrlParam(filters);
				
				//clear lock bound if exists
			} else {
				if(jQuery('.halo_browse_form').length){
					jQuery('.halo_browse_form').submit();
				} else if(jQuery('.halo-menu-search').length){
					jQuery('.halo-menu-search').submit();
				}
			}
		},
		displaySection: function (section) {
			if (jQuery('.filter_form').length) {
				var form_id = jQuery('.filter_form').attr('id');
				var values = halo.util.getFormValues(form_id);
			} else {
				//no filter provided, get emtpy form
				var values = halo.util.initFormValues();
			}
			halo.util.setFormValue(values, 'usec', section);
			halo.jax.call('browse', 'DisplaySection', values);
		},
		init: function (scope) {
			//init map
			if (!halo.browse.inited) {
				halo.browse.$preferSection = jQuery(".halo-browser-selected-post");
				if(jQuery('.halo-browse-listing').length) halo.browse.isBrowsing = true;
				halo.browse.inited = true;
				halo.location.loadScript(function () {
					halo.browse.ready = true;
					//init place search box UI
					var $placeInput = jQuery('.halo-field-location-name');
					if ($placeInput.length && $placeInput.attr('id')) {
						halo.location.loadScript(function () {
							var input = document.getElementById($placeInput.attr('id'));
							var searchBox = new google.maps.places.SearchBox(/** @type {HTMLInputElement} */(input));
							google.maps.event.addListener(searchBox, 'places_changed', function () {
								var places = searchBox.getPlaces();
								//always get the first location only
								if (places.length) {
									var loc = places[0].geometry.location;
									halo.browse.setCurrentLocation({lat: loc.lat(), lng: loc.lng(), name: places[0].name});
									halo.browse.reloadResults();
								}
							});
						});
					}

					//init slider UI
					jQuery('.halo-slider-input').on('slideStop', function () {
						halo.browse.reloadResults();
					});
					//init sort button
					jQuery('.halo-sort-btn').on('click', function () {
						var $this = jQuery(this);
						var iconPrefix = 'fa-sort-amount-';
						var dir = jQuery('.' + iconPrefix + 'asc', $this).length ? 'asc' : 'desc';
						var sort = $this.attr('data-value');

						function setDir($ele, dir) {
							jQuery('i', $ele).removeClass(iconPrefix + 'desc');
							jQuery('i', $ele).removeClass(iconPrefix + 'asc');
							jQuery('i', $ele).addClass(iconPrefix + dir);
						}

						if ($this.hasClass('active')) {
							if (dir == 'desc') {
								dir = 'asc';
							} else {
								sort = '';
								$this.removeClass('active');
							}
							//set current direction icon
							setDir($this, dir);
						} else {
							dir = 'desc';
							jQuery('.halo-sort-btn').each(function () {
								jQuery(this).removeClass('active');
								setDir(jQuery(this), 'desc');
							});
							$this.addClass('active');
						}
						$this.siblings('.halo-sort-input').val(sort);
						$this.siblings('.halo-dir-input').val(dir);
						halo.browse.reloadResults();
					});

					//init owner button
					jQuery('.halo-owner-btn').on('click', function () {
						var $this = jQuery(this);
						var ownerId = '';
						$this.toggleClass('active');
						if ($this.hasClass('active')) {
							ownerId = $this.attr('data-value');
						}
						$this.siblings('.halo-owner-input').val(ownerId);
						halo.browse.reloadResults();
					});
				});
			}
		},
		//Optional for prefer post item rearrange
		_selectPostCard: function (_postCard) {
			halo.browse.$preferSection.removeClass("hidden");
			halo.browse.$preferSection.append(jQuery(_postCard).parent());

		},
		canRefresh: function(){
			return !halo.browse.lockRefresh.length || (halo.util.isMobile() && !jQuery('.halo-mapcontent-toggle-btn').hasClass('halo-show'));
		},
		updateResultCounter: function (msg) {
			jQuery('.halo-search-result-stats p').html(msg);
		}
	}
});
