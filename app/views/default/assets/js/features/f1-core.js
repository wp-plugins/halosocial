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
 
if (typeof(halo) == 'undefined') {
	// create our halo namespace
	halo = {
		extend: function (obj) {
			window.jQuery.extend(this, obj);
		}
	}
}

if(!halo_jax_targetUrl) {
	var halo_jax_targetUrl = '';
}

if(!halo_assets_url) {
	var halo_assets_url = '';
}
//define jQuery instance
if(!$) {
	var $ = window.jQuery;
}

jQuery(document).ready(function(){
	if(navigator.userAgent.match(/iPhone/i)) {
		var viewportmeta = jQuery('meta[name="viewport"]');
		if (viewportmeta) {
			jQuery(document.body).on('gesturestart',function(){
				viewportmeta.attr('content','width=device-width, minimum-scale=0, maximum-scale=10.0');
			});
			jQuery(document.body).on('gesturechange',function(e){
				if(e.originalEvent.scale <=1) {
					viewportmeta.attr('content','width=device-width,minimum-scale=1.0,maximum-scale=1.0,initial-scale=1.0');
				}
			});
		}
	}
});

/*	IE console log fixing
 */
jQuery.extend(true, halo, {
	log: function(msg){
		if(!window.console) return;
		console.log(msg);
		/*
		if (!window.console) window.console = {};
		if (!window.halo.log) window.halo.log = function () {
		};
		*/
	},
	ilog: function(msg){
		var log = jQuery('#gblog');
		if(!log.length){
			jQuery('body').append('<div id="gblog"></div>');
			log = jQuery('#gblog');
		}
    log.append(msg + "\n");
    log.scrollTop(log[0].scrollHeight);	
	}
});

/* ============================================================ translation
 translation
 */
if (typeof(__halotext) == 'undefined') {
	__halotext = function (text) {
        if (typeof(haloTranslationJSON) == 'undefined' || !haloTranslationJSON || !haloTranslationJSON[text]) return text;
		return haloTranslationJSON[text];
	}
}

/* ============================================================ gmap icons

*/
if (typeof(__getGlobalMarkerIcon) == 'undefined') {
	__getGlobalMarkerIcon = function(type, cat) {
		var iconBase = halo_assets_url + "/images/gmap_marker/";
		var iconSet = {
			default: {
				icon: iconBase + "marker_default.png",
				iconHover: iconBase + "marker_default_hover.png"
			},
			shop: {
				cat_12: {	//thoi trang
					icon: iconBase + "marker_shop_fashion.png",
					iconHover: iconBase + "marker_shop_fashion.png"
				},
				cat_23: { //may tinh
					icon: iconBase + "marker_shop_computer.png",
					iconHover: iconBase + "marker_shop_computer.png"
				},
				cat_50: { //KTS
					icon: iconBase + "marker_shop_digital.png",
					iconHover: iconBase + "marker_shop_digital.png"
				},
				cat_3: { //BDS
					icon: iconBase + "marker_shop_realestate.png",
					iconHover: iconBase + "marker_shop_realestate.png"
				},
				cat_21: { //DT
					icon: iconBase + "marker_shop_electronic.png",
					iconHover: iconBase + "marker_shop_electronic.png"
				},
				cat_24: { //Xe
					icon: iconBase + "marker_shop_vehicle.png",
					iconHover: iconBase + "marker_shop_vehicle.png"
				},
				cat_26: { //MVB
					icon: iconBase + "marker_shop_mombaby.png",
					iconHover: iconBase + "marker_shop_mombaby.png"
				},
				cat_25: { //Food
					icon: iconBase + "marker_shop_food.png",
					iconHover: iconBase + "marker_shop_food.png"
				},
				cat_16: { //Smartphone
					icon: iconBase + "marker_shop_cellphone.png",
					iconHover: iconBase + "marker_shop_cellphone.png"
				},
				cat_67: { //pet
					icon: iconBase + "marker_shop.png",
					iconHover: iconBase + "marker_shop_hover.png"					
				},
				icon: iconBase + "marker_shop.png",
				iconHover: iconBase + "marker_shop_hover.png"
			},
			user: {
				cat_12: {	//thoi trang
					icon: iconBase + "marker_user_fashion.png",
					iconHover: iconBase + "marker_user_fashion.png"
				},
				cat_23: { //may tinh
					icon: iconBase + "marker_user_computer.png",
					iconHover: iconBase + "marker_user_computer.png"
				},
				cat_50: { //KTS
					icon: iconBase + "marker_user_digital.png",
					iconHover: iconBase + "marker_user_digital.png"
				},
				cat_3: { //BDS
					icon: iconBase + "marker_user_realestate.png",
					iconHover: iconBase + "marker_user_realestate.png"
				},
				cat_21: { //DT
					icon: iconBase + "marker_user_electronic.png",
					iconHover: iconBase + "marker_user_electronic.png"
				},
				cat_24: { //Xe
					icon: iconBase + "marker_user_vehicle.png",
					iconHover: iconBase + "marker_user_vehicle.png"
				},
				cat_26: { //MVB
					icon: iconBase + "marker_user_mombaby.png",
					iconHover: iconBase + "marker_user_mombaby.png"
				},
				cat_25: { //Food
					icon: iconBase + "marker_user_food.png",
					iconHover: iconBase + "marker_user_food.png"
				},
				cat_16: { //Smartphone
					icon: iconBase + "marker_user_cellphone.png",
					iconHover: iconBase + "marker_user_cellphone.png"
				},
				cat_67: { //pet
					icon: iconBase + "marker_shop.png",
					iconHover: iconBase + "marker_shop_hover.png"					
				},
				icon: iconBase + "marker_default.png",
				iconHover: iconBase + "marker_default_hover.png"
			}
		};
		var icon = {normal: iconSet.default.icon, active:iconSet.default.iconHover};
		if(type == 1){
			if(typeof iconSet.shop['cat_'+cat] != 'undefined'){
				icon.normal = iconSet.shop['cat_'+cat].icon;
				icon.active = iconSet.shop['cat_'+cat].iconHover;
			}
		} else if(type == 0){
			if(typeof iconSet.user['cat_'+cat] != 'undefined'){
				icon.normal = iconSet.user['cat_'+cat].icon;
				icon.active = iconSet.user['cat_'+cat].iconHover;
			}
		}
		return icon;
	};
}

/* ============================================================ push service features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	pushService: {
		init: function () {
		},
		sendUser: function (userid) {
			socket.send(JSON.stringify({
				"type": "user_id",
				"data": userid
			}));
		}
	}
});

/* ============================================================ user features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	user: {
		getActive: function () {
			// return the current active user
			return 0;
		},

		login: function () {
			var values = halo.util.getFormValues('loginForm');
			//update new csrf token
			var _token = jQuery('#loginForm [name="_token"]').val();
			jQuery('meta[name="csrf_token"]').attr('content', _token);
			halo.jax.call('user', 'Login', values);
			return false;
		},
		showLogin: function () {
			halo.jax.call('user', 'ShowLogin','',document.URL);
		}, 
		showLoginToggle: function (ele) {
			$e = jQuery(ele);
			$e.attr('data-htoggle', 'popover');
			$e.attr('data-placement', 'bottom');
			//enable popover
			var $popover = $e.hpopover({
				html: true,
				trigger: 'click',
				delay: { show: 100, hide: 150 },
				//container: 'body',
				template: '<div class="popover"><div class="arrow"></div><div class="popover-content halo-login-wrapper"></div></div>',
				content: function () {
					var loadingDiv = '<div data-halozone="halo-login-list" class="halo-login-loading">'
						+ halo.template.getJaxLoading(2)
						+ '</div>';
					return loadingDiv;
				}
			});
			var clickEveryWhere = function (e) {
				if (jQuery(e.target).closest('.halo-login-wrapper').length === 0) {
					$e.hpopover('toggle');
				}
			}
			var pressEscapse = function (e) {
				if (e.keyCode === 27) {
					$e.hpopover('toggle');
				}
			}

			$e.on('shown.bs.hpopover', function () {
				halo.jax.call("user", "ShowLoginToggle",document.URL);
				jQuery(document).on('click', clickEveryWhere);

				jQuery(document).on('keydown', pressEscapse);

			});

			$e.on('hide.bs.hpopover', function () {
				jQuery(document).off('click', clickEveryWhere);
				jQuery(document).off('keydown', pressEscapse);
			});

			$e.hpopover('toggle');
		}, 
		setupSubmitLogin: function () {
			jQuery('#loginForm').submit(function (ev) {
				ev.preventDefault(); // to stop the form from submitting
				halo.user.login();
			});
		}, 
		checkRegistrationRule: function() {
            if (jQuery('#halo-registration_rules').is(':checked')) {
                jQuery('#halo-submit-registration').removeAttr('disabled');
            } else {
                jQuery('#halo-submit-registration').attr('disabled', 'disabled');
            }
        }, 
		onRegistrationSubmit: function(form) {
        	var errorClasses = "has-error has-feedback";
        	if (jQuery('#halo-registration_rules').is(':checked')) {
                jQuery('.halo-registration-rule-group').removeClass(errorClasses).find('.help-block').addClass('hide');
                jQuery(form).submit();
            } else {
                jQuery('.halo-registration-rule-group').addClass(errorClasses).find('.help-block').removeClass('hide');
                return false;
            }
       	}, 
		listStream: function () {
			//halo.util.setUrlParam({'usec':'stream','pg':1});
		}, 
		listAbout: function () {
			//halo.util.setUrlParam({'usec':'aboutme','pg':1});
		}, 
		displaySection: function (section, userid) {
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
				halo.util.setFormValue(values, 'userid', userid);
				halo.util.setFormValue(values, 'usec', section);
				halo.jax.call('user', 'DisplaySection', values);
			}
		}, 
		refreshSection: function (section, userid) {
			//clear the current section
			var sectionWrapperSelector = '[data-halozone="halo-' + section + 's-wrapper"]';
			var $sectionContent = jQuery(sectionWrapperSelector);
			$sectionContent.children().remove();
			//get the fresh section content
			halo.user.displaySection(section, userid);
		}, 
		showChangePassword: function () {
			halo.jax.call('user', 'ShowChangePassword');
		}, 
		changePassword: function () {
			var values = halo.util.getFormValues('popupForm');
			halo.jax.call('user', 'ChangePassword', values);
		}, 
		showChangeProfile: function () {
			halo.jax.call('user', 'ShowChangeProfile');
		}, 
		changeProfileType: function(ele) {
			var profile_id = jQuery(ele).val();
			halo.jax.call('user', 'ChangeProfileType', profile_id);
		},
		changeRegisterProfileType: function(ele) {
			var profile_id = jQuery(ele).val();
			var $hasProfile = jQuery('[name="has_profile"]');
			$hasProfile.val('');
			$hasProfile.trigger('change');
			halo.jax.call('user', 'ChangeProfileType', profile_id, function() {
				//check for profile field content
				$profileFields = jQuery('[data-halozone="user_profile_edit"]');

				//hide next step in registration wizard if profile field content is empty
				var $activeStep = jQuery('#registerForm .halo-wizard-step-title.active');
				if($activeStep.length) {
					if (!$profileFields.html().trim().length) {
						$activeStep.next().addClass('ignore');
					} else {
						$activeStep.next().removeClass('ignore');
					}
				}
				
				$hasProfile.val('1');
				$hasProfile.trigger('change');
			});
		},		
		changeProfile: function () {
			var values = halo.util.getFormValues('popupForm');
			halo.jax.call('user', 'ChangeProfile', values);
		}, 
		deleteUser: function (userId) {
			halo.jax.call("admin,users", "DeleteUser", userId);
		}, 
		deleteSelectedUser: function () {
			//get selected field
			halo.util.getCheckedItem(halo.user.deleteUser);
		}, 
		deleteMe: function () {
			var values = halo.util.getFormValues('popupForm');
			halo.jax.call("user", "DeleteMe", values);
		}, 
		setBadge: function (userId, badgeId, badgeType, content) {
			var $badge = jQuery('.halo-user-list-wrapper [data-halo-userid="' + userId + '"] .halo-badge-wrapper');
			if ($badge.length) {
				var oldBadge = jQuery('[data-badge="' + badgeId + '"]', $badge);
				if (oldBadge.length) {
					oldBadge.replaceWith(jQuery(halo.util.badge(badgeId, badgeType, content)));
				} else {
					$badge.append(jQuery(halo.util.badge(badgeId, badgeType, content)));
				}
			}
		}, 
		removeUserList: function (userId) {
			var $user = jQuery('.halo-user-list-wrapper .halo-list-item-user[data-halo-userid="' + userId + '"]');
			if ($user.length) {
				$user.remove();
			}
		},
		updateHaloMyId: function(userId) {
		    if (typeof userId !== 'undefined') {
                halo_my_id = userId;
                return halo_my_id;
            }
		},
		validateNewUser: function() {
			var $email = jQuery('#registerForm [name="email"]');
			var $username = jQuery('#registerForm [name="username"]');
			if($username.val() && $email.val()){
				halo.jax.session.set('formId', '#registerForm');
				halo.jax.call('user', 'ValidateNewUser', $email.val(), $username.val(), halo.user.validatePassword);
			}
		},
		validatePassword: function(frm) {
			if(!frm) frm = '#registerForm';
			var form = jQuery(frm);
			if(form.length) {
				var $password = form.find('[name="password"]');
				var $password_confirm = form.find('[name="password_confirmation"]');
				if($password.val() && $password.val() !== $password_confirm.val()){
					halo.template.setInputError($password_confirm, __halotext('Password miss matched'));
				} else {
					halo.template.clearInputError($password_confirm);
				}
			}
		
		}
	}
});

/* ============================================================ video features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	video: {
		displayShareVideo: function (title, description, embededPlayer, providerVid) {
			jQuery('[name="video_title"]').val(title);
			jQuery('[name="video_description"]').val(description);
			jQuery('[name="video_vid"]').val(providerVid);
			embededPlayer = (embededPlayer.length) ? embededPlayer : jQuery('<div class="text-center text-muted" style="padding-top:40%"><strong>' + __halotext('Unknown Video') + '</strong></div>');
			jQuery('.halo-sharebox-embeded-player').html(embededPlayer);
		}, 
		validateForm: function () {
			jQuery('#status_form_video').valid();
		},
		remove: function (videoId, confirm) {
			if (typeof confirm == 'undefined') {
				confirm = 0;
			}
			halo.jax.call('video', 'RemoveVideo', videoId, confirm);
		},
		removeVideoUI: function (videoId) {
			jQuery('[data-video-id="' + videoId + '"]').closest('.halo-list-wrapper-video').remove();
			halo.popup.close();
		},
		init: function (scope) {
			//enable gallery on stream
			jQuery('.halo-video-popup', scope).each(function () {
				jQuery(this).haloMagnificPopup({
					delegate: 'a',
					type: 'iframe',
					tLoading: __halotext('Loading video #%curr%...'),
					mainClass: 'mfp-img-mobile',
					iframe: {
						markup: '<div class="mfp-iframe-scaler">' +
							'<div class="mfp-close"></div>' +
							'<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>' +
							'</div>', // HTML markup of popup, `mfp-close` will be replaced by the close button

						patterns: {
							youtube: {
								index: 'youtube.com/', // String that detects type of video (in this case YouTube). Simply via url.indexOf(index).

								id: 'v=', // String that splits URL in a two parts, second part should be %id%
								// Or null - full URL will be returned
								// Or a function that should return %id%, for example:
								// id: function(url) { return 'parsed id'; }

								src: '//www.youtube.com/embed/%id%?autoplay=1' // URL that will be set as a source for iframe.
							},
							vimeo: {
								index: 'vimeo.com/',
								id: '/',
								src: '//player.vimeo.com/video/%id%?autoplay=1'
							},
							dailymotion: {
								index: 'dailymotion.com',
								id: function (url) {
									var m = url.match(/^.+dailymotion.com\/(video|hub)\/([^_]+)[^#]*(#video=([^_&]+))?/);
									if (m !== null) {
										if (m[4] !== undefined) {

											return m[4];
										}
										return m[2];
									}
									return null;
								},
								src: 'http://www.dailymotion.com/embed/video/%id%'
							}
							// you may add here more sources
						},

						srcAction: 'iframe_src', // Templating object key. First part defines CSS selector, second attribute. "iframe_src" means: find "iframe" and set attribute "src".
					}, callbacks: {
						change: function () {


						}, open: function () {
							var videoId = this.currItem.el.data('video-id');

							halo.jax.call('video', 'LoadPopupContent', videoId);
						}
					}
				});
			});

		}

	}
});

/* ============================================================ photo features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	photo: {
		nThumbPerCarousel: 5,

		changeAvatar: function (context, userId) {
			halo.jax.call('photo', 'ShowEditAvatarForm', context, userId);
		},

		saveAvatar: function (context, userId) {
			var values = halo.util.getFormValues('popupForm');
			halo.jax.call('photo', 'SaveAvatar', context, userId, values);
		},

		changeCover: function (context, userId) {
			halo.jax.call('photo', 'ShowEditCoverForm', context, userId);
		},

		saveCover: function (context, userId) {
			var values = halo.util.getFormValues('popupForm');
			halo.jax.call('photo', 'SaveCover', context, userId, values);
		},
		remove: function (photoId, confirm) {
			if (typeof confirm == 'undefined') {
				confirm = 0;
			}
			halo.jax.call('photo', 'RemovePhoto', photoId, confirm);
		},
		removePhotoUI: function (photoId) {
			jQuery('[data-photo-id="' + photoId + '"]').parent().remove();
			halo.popup.close();
		},
		initCarousel: function(scope) {
			jQuery(document).trigger('initCarousel', [scope]);
			
			jQuery('.halo-thumb-carousel', scope).each(function() {
				var $esCarousel = jQuery(this);
				var $items = $esCarousel.find('ul > li');
				var options = jQuery(this).data();
				var defaults = {mode: 'carousel', showModes: 1, current: 0};
				options = jQuery.extend(defaults, options);
				var current = options.current;
				var _showItem = function($item) {
					var $esCarousel = $item.closest('.halo-thumb-carousel');
					var $items = $esCarousel.find('ul > li');
					var $oldItem = $items.filter('.selected');
					$items.removeClass('selected');
					$item.addClass('selected');
					$esCarousel.elastislide('setCurrent', $item.index());
					$esCarousel.trigger('rg:shownItem', [$item, $oldItem]);	
				};
				$esCarousel.elastislide({
					imageW: 128,
					minItems: 3,
					onClick: function ($item) {
						_showItem($item);
					}
				});

				$esCarousel.elastislide('setCurrent', current);
				_showItem($items.eq(current));
			});
		},
		initGallery: function (scope) {
			var selector = '.rg-gallery';
			if (jQuery(scope).find(selector).attr('id') !== undefined) {
				//var selector = '#' + jQuery(scope).find(selector).attr('id');
			}
			jQuery(selector, scope).each(function () {
				// gallery container
				var $rgGallery = jQuery(this),
				// carousel container
					$esCarousel = jQuery('div.es-carousel-wrapper',$rgGallery),
				// the carousel items
					$items = $esCarousel.find('ul > li'),
				// total number of items
					itemsCount = $items.length;
				var options = jQuery(this).data();
				var defaults = {mode: 'carousel', showModes: 1, current: 0};
				options = jQuery.extend(defaults, options);
				var current = options.current,
				// mode : carousel || fullview
					mode = options.mode,
				// control if one image is being loaded
					anim = false,
					init = function () {
						// (not necessary) preloading the images here...
						if (options.showModes || mode == 'fullview') {
							$items.add('<img src="' + halo_assets_url + '/images/ajax-loader.gif"/><img src="' + halo_assets_url + '/images/black.png"/>').imagesLoaded(function () {
								if (options.showModes) {
									// add options
									_addViewModes();
								}
								// add large image wrapper
								_addImageWrapper();
								// show first image
								_showImage($items.eq(current));
							});
						}
						// initialize the carousel
						_initCarousel();
					},
					_initCarousel = function () {
						$esCarousel.elastislide({
							imageW: 128,
							minItems: 3,
							onClick: function ($item) {
								//if (anim) return false;
								anim = true;
								if (mode == 'fullview') {
									// on click show image
									_showImage($item);
								} else {
									// on click show popup
									_popupImage($item);
								}
								// change current
								current = $item.index();
							}
						});
						// set elastislide's current to current
						$esCarousel.elastislide('setCurrent', current);
						//set current mode
						_setViewMode(mode);
					},
					_setViewMode = function (mode) {
						$rgGallery.removeClass(function (index, css) {
							return (css.match(/\brg-mode-\S+/g) || []).join(' ');
						});
						$rgGallery.addClass('rg-mode-' + mode);
					},
					_addViewModes = function () {

						// top right buttons: hide / show carousel

						var $viewfull = jQuery('<a href="#" class="rg-view-full halo-btn halo-btn-default halo-btn-xs halo-btn-nobg" title="' + __halotext('gallery view') + '"><i class="fa fa-square"></i>Fullview</a>'),
							$viewthumbs = jQuery('<a href="#" class="rg-view-thumbs halo-btn halo-btn-default halo-btn-xs halo-btn-nobg" title="' + __halotext('thumbnail view') + '"><i class="fa fa-th-list"></i>Thumbview</a>');
						if (mode == 'fullview') {
							$viewfull.addClass('rg-view-selected');
						} else {
							$viewthumbs.addClass('rg-view-selected');
						}
						$rgGallery.prepend(jQuery('<div class="rg-view"/>').append($viewfull).append($viewthumbs));

						$viewfull.bind('click.rgGallery', function (event) {
							$esCarousel.elastislide('destroy');
							_initCarousel();
							$viewfull.addClass('rg-view-selected');
							$viewthumbs.removeClass('rg-view-selected');
							mode = 'fullview';
							_setViewMode(mode);
							return false;
						});

						$viewthumbs.bind('click.rgGallery', function (event) {
							$esCarousel.elastislide('destroy');
							_initCarousel();
							$viewthumbs.addClass('rg-view-selected');
							$viewfull.removeClass('rg-view-selected');
							mode = 'carousel';
							_setViewMode(mode);
							return false;
						});

					},
					_addImageWrapper = function () {
						// addNavigation
						var $navPrev = $rgGallery.find('a.rg-image-nav-prev'),
							$navNext = $rgGallery.find('a.rg-image-nav-next'),
							$imgWrapper = $rgGallery.find('div.rg-image');

						$navPrev.bind('click.rgGallery', function (event) {
							_navigate('left');
							return false;
						});

						$navNext.bind('click.rgGallery', function (event) {
							_navigate('right');
							return false;
						});

						if ($esCarousel.data('carousel').itemsCount < 2) {
							$navPrev.hide();
							$navNext.hide();
						}
						;

						// add touchwipe events on the large image wrapper
						$imgWrapper.touchwipe({
							wipeLeft: function () {
								_navigate('right');
							},
							wipeRight: function () {
								_navigate('left');
							},
							preventDefaultEvents: false
						});

						jQuery(document).bind('keyup.rgGallery', function (event) {
							if (event.keyCode == 39)
								_navigate('right');
							else if (event.keyCode == 37)
								_navigate('left');
						});
					},
					_navigate = function (dir) {
						if (anim) return false;
						anim = true;

						if (dir === 'right') {
							if (current + 1 >= itemsCount)
								current = 0;
							else
								++current;
						}
						else if (dir === 'left') {
							if (current - 1 < 0)
								current = itemsCount - 1;
							else
								--current;
						}

						_showImage($items.eq(current));
						
						//trigger image lazy load
						halo.lazyload.loadImages();
					},
					_popupImage = function ($item) {
						//delegate click event to the target popup photo
						var photoId = jQuery('[data-photo-target]', $item).attr('data-photo-target');
						var $wrapper = $item.closest('.es-carousel-wrapper');
						var $targetPhoto = jQuery('[data-photo-id="' + photoId + '"]', $wrapper);
						$targetPhoto.click();
						anim = false;
					}
				_showImage = function ($item) {
					var $esCarousel = $item.closest('.es-carousel-wrapper');
					var $rgGallery = $item.closest(selector);
					var $items = $esCarousel.find('ul > li');
					// shows the large image that is associated to the $item
					var $loader = $rgGallery.find('div.rg-loading').show();
					//get old selected image
					var $oldItem = $items.filter('.selected');

					$items.removeClass('selected');
					$item.addClass('selected');

					var $thumb = $item.find('img'),
						largesrc = $thumb.data('large'),
						title = $thumb.data('description');
					if(typeof largesrc !== 'undefined'){
						jQuery('<img/>').load(function () {
							var image = jQuery('<a href="' + largesrc + '"><img src="' + largesrc + '"/></a>');
							if (mode == 'fullview') {
								image.on('click', function (e) {
									_popupImage($item);
									e.preventDefault();
									return false;
								});
							}
							$rgGallery.find('div.rg-image').empty().append(image);

							if (title)
								$rgGallery.find('div.rg-caption').show().children('p').empty().text(title);

							$loader.hide();

							if (mode === 'fullview') {
								$esCarousel.elastislide('reload');
								$esCarousel.elastislide('setCurrent', current);
							}

							anim = false;

						}).attr('src', largesrc);
					} else {
						$loader.hide();
						$esCarousel.elastislide('reload');
						$esCarousel.elastislide('setCurrent', current);
						anim = false;
					}
					//triger event
					$rgGallery.trigger('rg:shownItem',[$item, $oldItem]);
				};

				init();
			});
		},
		init: function (scope) {
			// Init post photos
			halo.photo.initGallery(scope);
			halo.photo.initCarousel(scope);
			//enable gallery on stream
			jQuery('.halo-gallery-popup', scope).each(function () {
				jQuery(this).haloMagnificPopup({
					delegate: 'a',
					type: 'image',
					tLoading: __halotext('Loading image #%curr%...'),
					mainClass: 'mfp-img-mobile',
					gallery: {
						enabled: true,
						navigateByImgClick: true,
						preload: [1, 1] // Will preload 0 - before current, and 1 after the current image
					},
					image: {
						markup: '<div class="mfp-figure">' +
							'<div class="halo-gallery-popup-content" style="margin:0px;">' +
							'	<div class="mfp-close"></div>' +
							'	<div class="halo-gallery-body">' +
							'	<div class="halo-gallery-left">' +
							'		<div class="mfp-img"></div>' +
							'		<div class="halo-photo-tags"></div>' +
							'		<div class="mfp-bottom-bar clearfix">' +
							'			<div class="mfp-title halo-pull-left"></div>' +
							'			<div class="mfp-counter halo-pull-left"></div>' +
							'			<div class="halo-photo-actions halo-pull-left" data-halozone="halo-photo-actions">' +
							'			</div>' +
							'		</div>' +
							'	</div>' +
							'	<div class="halo-gallery-right">' +
							'		<div class="halo-gallery-right-container">' +
							'			<div class="container-fluid halo-gallery-right-wrapper">' +
							'			</div>' +
							'		</div>' +
							'	</div>' +
                            '   <div class="clearfix"></div>' +
							'	</div>' +
							'</div>' +
							'</div>', // Popup HTML markup. `.mfp-img` div will be replaced with img tag, `.mfp-close` by close button
						tError: __halotext('<a href="%url%">The image #%curr%</a> could not be loaded.'),
						titleSrc: function (item) {
							return item.el.attr('title');
						}
					},
					callbacks: {
						change: function () {


						},
						buildControls: function () {
							// re-appends controls inside the main container
							if (typeof this.arrowLeft != 'undefined' && this.arrowLeft && typeof this.arrowRight != 'undefined' && this.arrowRight) {
								jQuery('.halo-gallery-left', this.contentContainer).append(this.arrowLeft.add(this.arrowRight));
							}
						},
						imageLoadComplete: function () {
							// fires when image in current popup finished loading
							//load comments
							var photoId = this.currItem.el.data('photo-id');
							var context = 'photo';

							//clear any tags, tag input from previous picture
							halo.photo.doneTagging();
							jQuery('.halo-photo-tag').remove();
							halo.photo.currentTagging.photoId = photoId;

							//setup zone
							var wrapper = jQuery('.halo-gallery-right').find('div.halo-gallery-right-wrapper');

							wrapper.attr('data-halozone', 'popup_comment.' + context + '.' + photoId);
							halo.jax.call(context, 'LoadPopupContent', photoId);
						},
						open: function() {
							jQuery('.halo-gallery-left').touchwipe({
								wipeLeft: function () {
									jQuery('.mfp-arrow-left').trigger('click');
								},
								wipeRight: function () {
									jQuery('.mfp-arrow-right').trigger('click');
								},
								preventDefaultEvents: false
							});
						}
					}
				});
			});
		
		}, 
		initUploadBtn: function (selector, holderSelector) {
			//create a wrapper the el for uploader holder
			var uploaderId = 'photoUploader';
			var browserBtn = jQuery(selector).attr('id', 'plupload-browse-button-' + uploaderId);
			var holder = jQuery(holderSelector).attr('id', 'halo-plupload-upload-ui-' + uploaderId)
				.addClass('halo-uploader-holder')
			halo.uploader.uploader_init(jQuery, holder, jQuery.extend({photoWidth: 0, photoHeight: 0, uploaderId: uploaderId}, halo.uploader.progressOnlyOption));

		}, 
		updateEditingPhoto: function (selector, photoId, src) {
			var $container = jQuery(selector);
			var $image = jQuery('img.img-dragtoedit', $container);
			var $zoom = jQuery('[name="photoZoom"]', $container);
			var $top = jQuery('[name="photoTop"]', $container);
			var $left = jQuery('[name="photoLeft"]', $container);
			var $width = jQuery('[name="photoWidth"]', $container);
			var $photoId = jQuery('[name="photoId"]', $container);
			var $viewport = jQuery('.halo-photo-view-port-wrap', $container);

			$photoId.val(photoId);
			//make sure the halo-popup-upload2edit-photo is active
			jQuery('[href="#halo-popup-upload2edit-photo"]').htab('show');
			//reset old photo positioning values
			$top.val(0);
			$left.val(0);
			$zoom.val(100);
			$image.attr('src', src);
			halo.photo.startEdit(selector);
			$zoom.slider('setValue', 100);
		}, 
		startEdit: function (selector) {
			var $container = jQuery(selector);
			var $image = jQuery('img.img-dragtoedit', $container);
			var $zoom = jQuery('[name="photoZoom"]', $container);
			var $top = jQuery('[name="photoTop"]', $container);
			var $left = jQuery('[name="photoLeft"]', $container);
			var $width = jQuery('[name="photoWidth"]', $container);
			var $photoId = jQuery('[name="photoId"]', $container);
			var $viewport = jQuery('.halo-photo-view-port-wrap', $container);
			//**Load event has many caveat when used on img element
			//Off to prevent event stacking up

			$image.off('load');
			$image.one('load',function () {
				//halo.log('load');

				var minZoom;

				//init zoom and position
				$image.css('top', $top.val() + 'px')
					.css('left', $left.val() + 'px')
					.css('width', parseInt($zoom.val()) + '%');

				//configure viewport height to match viewport ratio
				var viewportRatio = $viewport.data('viewport-ratio');
				var ratioParts = viewportRatio.split(':');
				if (ratioParts.length == 2) {
					$viewport.height(Math.round($viewport.width() * parseInt(ratioParts[1]) / parseInt(ratioParts[0])));
				}
				$width.val($viewport.width());

				//calculating min zoom
				if ($viewport.height() > $image.height()) {
					minZoom = Math.floor(parseFloat($zoom.val()) * ($viewport.height() / $image.height())) + 1;
					//force min zoom as the current zoom value
					$zoom.val(minZoom);
					$image.css('width', parseInt($zoom.val()) + '%');
				} else {
					minZoom = 100;
				}

				//configure image draggable
				$image.draggable({
					drag: function (event, ui) {
						var minTop = $viewport.height() - $image.height();
						var minLeft = $viewport.width() - $image.width();
						if (ui.position.top > 0) {
							ui.position.top = 0;
						}
						if (ui.position.left > 0) {
							ui.position.left = 0;
						}
						if (ui.position.top < minTop) {
							ui.position.top = minTop;
						}
						if (ui.position.left < minLeft) {
							ui.position.left = minLeft;
						}
					},
					stop: function (event, ui) {
						$top.val(ui.position.top);
						$left.val(ui.position.left);
					}
				});
				//setup event handlers
				//on change zoom setting
				//Modified slider plugin to reset with new options when init again
				//The plugin's onslide event is not reliable- value not sync properly
				$zoom.slider({min: minZoom, max: minZoom + 200, value: parseInt($zoom.val())})
					.on('slide',function () {
						var zoomVal = $zoom.val() + '%';
						$image.css({width: zoomVal});

						//Restrict image's edge - must snap to wrapper
						var remainingWidth = $image.position().left + $image.width();
						var remainingHeight = $image.position().top + $image.height();
						if (remainingWidth < $viewport.width()) {
							$image.css({left: $image.position().left + ($viewport.width() - remainingWidth) + 'px'});
						}
						if (remainingHeight < $viewport.height()) {
							$image.css({top: $image.position().top + ($viewport.height() - remainingHeight) + 'px'});
						}
						//-----------------------

					}).on('slideStop', function () {
						var zoomVal = $zoom.val() + '%';
						$image.css({width: zoomVal});
					});
				//on photo uploaded
				jQuery('[data-halo-photo-upload-btn]', $container).on('uploader.uploadSuccess', function (event, photo) {
					if (typeof photo.error === 'undefined' && typeof photo.image !== 'undefined') {
						halo.photo.updateEditingPhoto($container, photo.id, photo.image);
					}

				});

				$image.css('visibility', '');

			}).each(function () {
				if (this.complete) jQuery(this).load();
			});
		}, 
		changeAlbumListing: function (albumId) {
			halo.jax.call('photo', 'ChangeAlbumListing', albumId);
		}, 
		showAlbumPhotos: function (albumId) {
			//cancle all current ajax request
			halo.jax.cancelRequests();
			var userId = jQuery('[data-halo-pagination] [name="userid"]').first().val();
			halo.jax.call('photo', 'ShowAlbumPhotos', albumId, userId);
		}, 
		showUserAlbums: function (userId) {
			//clean up current zone
			jQuery('[data-halozone="halo-albums-wrapper"]').html('');
            var values = halo.util.initFormValues();
            halo.util.setFormValue(values, 'userid', userId);
            halo.util.setFormValue(values, 'usec', 'album');
			halo.jax.call('user', 'DisplaySection', values);
		}, 
		showAlbumListing: function () {
			halo.jax.call('photo', 'ShowAlbumListing');
		}, 
		selectListingPhoto: function (photoId) {
			halo.jax.call('photo', 'selectListingPhoto', photoId)
		}, 
		initTagForm: function () {
			var tagForm = jQuery('<form id="halo_photo_tags"></form>');
			var tagInput = jQuery('<input id="photo_tag_input" type="text" name="photo_tag_user"></input>');
			tagForm.append(tagInput);

		}, 
		clearTagInput: function () {
			jQuery('.halo-photo-tag-input').remove();
		}, 
		initTagInput: function () {
			var container = jQuery('<div class="halo-photo-tag-input"></div>');
			var input = jQuery('<input class="" type="text" name="photo_tag_user"/>');
			container.append(input);

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
				}
			});
			users.initialize();
			function suggestionTemplate(context) {
				return Hogan.compile('<p class="name"><image class="halo-suggestion-img" src={{image}}>{{name}}</p>').render(context);
			};

			function onTaggingUser(e, datum) {
				halo.photo.addTag(datum.id);
				//remove the img select + input tag
				halo.photo.resetTagging();
			}

			input.typeahead({highlight: true}, {
				name: 'taggedusers',
				displayKey: 'name',
				source: users.ttAdapter(),
				templates: {
					empty: '<div class="halo-users-not-found text-center">' + __halotext('No User Matched') + '</div>',
					suggestion: suggestionTemplate
				},
				engine: Hogan
			})
				.on('typeahead:selected', onTaggingUser)
				.on('typeahead:autocompleted', onTaggingUser)

			;
			return container;
		},
		addTag: function (userId) {
			var area = halo.photo.currentTagging.area;
			var photoId = halo.photo.currentTagging.photoId;
			var img = halo.photo.currentTagging.img;
			//calculate the tagging area
			var startPos = halo.photo.mapImageCoord(area.x1, area.y1, img, false);
			var endPos = halo.photo.mapImageCoord(area.x2, area.y2, img, false);
			var x1 = startPos.x;
			var y1 = startPos.y;
			var x2 = endPos.x;
			var y2 = endPos.y;
			//store tag info
			if (typeof area !== 'undefined') {
				halo.jax.call('photo', 'AddTag', photoId, userId, x1, y1, x2, y2);
			}
		},
		removeTag: function (userId) {
			var photoId = halo.photo.currentTagging.photoId;
			halo.jax.call('photo', 'RemoveTag', photoId, userId);
		},
		currentTagging: {},
		showTag: function (username, userId, x1, y1, x2, y2, removable) {
			var $tagsContainer = jQuery('.halo-photo-tags');
			var tagHtml = jQuery('<div class="halo-photo-tag" data-tag-userid="' + userId + '"></div>');
			var removeTagHtml = (removable == 'true') ? '<a href="javascript:void(0)" title="' + __halotext('Remove Tag') + '" onclick="halo.photo.removeTag(\'' + userId + '\')" class="halo-photo-tag-remove"><i class="fa fa-times"></i></a>' : '';
			var tagName = jQuery('<div class="halo-photo-tag-name">' + username + removeTagHtml + '</div>');
			tagHtml.append(tagName);
			$tagsContainer.append(tagHtml);

			//calculate the tagging area
			var img = jQuery('.halo-gallery-left > img');

			var startPos = halo.photo.mapImageCoord(x1, y1, img, true);
			var endPos = halo.photo.mapImageCoord(x2, y2, img, true);
			tagHtml.css('top', startPos.y + 'px')
				.css('left', startPos.x + 'px')
				.css('height', endPos.y - startPos.y)
				.css('width', endPos.x - startPos.x)
			var tagUserName = jQuery('<span class="halo-tagged-user" data-halo-userid="' + userId + '">' + username + '</span>');
			jQuery('.halo-gallery-right .halo-tagged-users-wrapper').removeClass('hidden');
			jQuery('.halo-gallery-right .halo-tagged-users-wrapper').append(tagUserName);

			jQuery('a', tagUserName).hover(function () {
				tagHtml.addClass('active');
			}, function () {
				tagHtml.removeClass('active');
			})
		},
		showTagObject: function (tagData) {
			halo.photo.showTag(tagData.username, tagData.userId, tagData.x1, tagData.y1, tagData.x2, tagData.y2, tagData.removable);
		},
		clearTag: function (userId) {
			jQuery('[data-tag-userid="' + userId + '"]').remove();
			jQuery('.halo-tagged-user[data-halo-userid="' + userId + '"]').remove();
		},
		startTagging: function () {
			if (jQuery('.halo-photo-end-tagging').hasClass('hidden')) {
				jQuery('.halo-photo-tag-btn').toggleClass('hidden');
			}
			jQuery('.halo-gallery-left .mfp-img').imgAreaSelect({
				handles: true,
				parent: '.halo-gallery-left',
				onSelectStart: function () {
					//remove any tag input
					halo.photo.clearTagInput();
				}, onSelectEnd: function (img, area) {
					//remove any tag input
					halo.photo.clearTagInput();
					halo.photo.currentTagging.img = img;
					halo.photo.currentTagging.area = area;
					var tagInput = halo.photo.initTagInput();
					jQuery(img).after(tagInput);
					jQuery('.imgareaselect-outer').remove();
					tagInput.css('position', 'absolute')
						.css('top', area.y2 + 10)
						.css('left', area.x1)
						.css('z-index', 2000)
						.css('width', '150px')
						.focus();
				}
			}).on('click.halotagging', function (e) {
				e.stopPropagation();
			});
		}, doneTagging: function () {
			if (jQuery('.halo-photo-begin-tagging').hasClass('hidden')) {
				jQuery('.halo-photo-tag-btn').toggleClass('hidden');
			}
			halo.photo.clearTagInput();
			jQuery('.imgareaselect-selection').parent('div').remove();
			jQuery('.halo-gallery-left .mfp-img').imgAreaSelect({remove: true}).off('click.halotagging');
		}, resetTagging: function () {
			halo.photo.clearTagInput();
			jQuery('.imgareaselect-selection').parent('div').css('top', -9999);

		}, mapImageCoord: function (x, y, img, toViewPort) {
			var $img = jQuery(img);
			var paddingTop = parseInt($img.css('padding-top'));
			var paddingLeft = parseInt($img.css('padding-left'));
			if ($img.length == 0) return {'x': null, 'y': null};
			var nSize = halo.util.getImageNatureSize($img);
			var vWidth = $img.width();
			var vHeight = $img.height();
			var xRatio = (toViewPort) ? (vWidth / nSize.width) : nSize.width / vWidth;
			var yRatio = (toViewPort) ? ((vHeight) / nSize.height) : nSize.height / (vHeight);

			return (toViewPort) ? {'x': Math.floor(x * xRatio) + paddingLeft, 'y': Math.floor(y * yRatio) + paddingTop} : {'x': Math.floor((x - paddingLeft) * xRatio), 'y': Math.floor((y - paddingTop) * yRatio)}
		}
	}
});

/* ============================================================ follower features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	follower: {
		follow: function (context, target_id) {
			halo.jax.call('follower', 'Follow', context, target_id);
		},
		unfollow: function (context, target_id) {
			halo.jax.call('follower', 'UnFollow', context, target_id);
		}
	}
});

/* ============================================================ friend features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	friend: {
		sendRequest: function (userId) {
			halo.jax.call('friend', 'SendFriendRequest', userId);
		}, approveRequest: function (userId,refresh) {
			if(typeof refresh == 'undefined'){
				refresh = 0;
			}
			halo.jax.call('friend', 'ApproveRequest', userId, refresh);
			window.event.stopPropagation();
			return false;
		}, rejectRequest: function (userId,refresh) {
			if(typeof refresh == 'undefined'){
				refresh = 0;
			}
			halo.jax.call('friend', 'RejectRequest', userId, refresh);
			window.event.stopPropagation();
			return false;
		}, unFriend: function (userId) {
            var values = halo.util.initFormValues();
            halo.util.setFormValue(values, 'user_id', userId);
            halo.util.setFormValue(values, 'url', location.href);
			halo.jax.call('friend', 'UnFriend', values);
		}
	}
});

/* ============================================================ websocket features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	websocket: {
		inited: false,
		init: function () {
			//skip if socket inited
			if (halo.websocket.inited) return true;
			//skip if userid is not configured
			if (typeof halo_my_id === 'undefined' || halo_my_id == 0 || typeof halo_feature_push === 'undefined' || halo_feature_push == 0) return true;
			try {
				var serverAddr = halo_socket_address;
				var socket = io.connect(serverAddr, {reconnection: false});
				socket.on('open', function () {
					console.info('Socket is now opened.');
				})

				socket.on('updater', function (data) {
					halo.updater.start('', true, data.message, data.params);
				});

				socket.emit('user', {userid: halo_my_id, host: window.location.hostname});

				socket.on('close', function () {
					console.info('Socket is now closed.');
				})

				socket.on('connect_error', function (obj) {
					if (obj.description == 400) {
						halo.log("Socket.io reported a generic error");
						//stop the reconnect
					}
					halo.updater.pushserver;
				});

				socket.on('connect_timeout', function () {
					halo.log("Connection timeout");
				});

				socket.on('disconnect', function () {
					halo.log("Socket.io disconected");
				});

				window.socket = socket; // debug

				halo.websocket.inited = true;

				halo.updater.pushserver = true;

			} catch (e) {

				halo.log("exception: " + e);

			}
		}
	}
});

/* ============================================================ language features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	lang: function () {
		var str = arguments[0];
		for (var i = 1; i < arguments.length; i++) {

			str = str.replace('%s', arguments[i]);
		}
		return str;
	}
});

/* ============================================================ init features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	init: function (scope) {
		//template init must be started firstly
		if (typeof halo['template']['init'] === 'function') {
			halo['template']['init'].apply(this, [scope]);
		}
		//console.time('halo_init');
		for (feature in halo) {
			//console.time(feature)
			if (feature !== 'template' && typeof halo[feature]['init'] === 'function') {
				try {
					halo[feature]['init'].apply(this, [scope]);
				} catch(err){
					halo.log(err);
				}
			}
			//console.timeEnd(feature);
		}
		//console.timeEnd('halo_init');
		//trigger event
		jQuery(document).trigger('afterHALOInit', scope);
	}
});

/* ============================================================ updater features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	updater: {
		inited: false,
		interval: 10000,	//default update interval
		pushserver: false,
		lastUpdate: 0,
		init: function (scope) {
			if (!halo.updater.inited) {
				setTimeout(function () {
					halo.updater.start(scope);
				}, halo.updater.interval);				
				halo.updater.inited = true;
			}
		},
		start: function (scope, noRepeat, features, params) {
			var rules = [];
			if (typeof features == 'string' && features.length > 0) {
				features = features.split(' ');
			}
			for (feature in halo) {

				if (typeof halo[feature]['updater'] === 'function') {
					if (typeof features == 'undefined' || features.length == 0 || features.indexOf(feature) >= 0) {
						var rule = halo[feature]['updater'].apply(this, [scope, params]);
						if (typeof rule !== 'undefined') {
							rules.push(rule);
						}

					}
				}
			}
			if (rules.length) {
				var d = new Date();
				var t = d.getTime();
				var delta = 15000;	//minimum update interval (15s)
				if (halo.updater.lastUpdate + delta < t || noRepeat) {
					var params = ['system', 'UpdateContent'];
					params = params.concat(rules);
					halo.jax.call.apply(this, params);
					halo.updater.lastUpdate = t;
				}
			}

			//set interval update
			if (halo.updater.pushserver == true) {
				//@todo:
				//pushservice is enable, just need to check that the socket is still opening

			}
			if (typeof noRepeat !== 'undefined' && noRepeat == true) {
				// run the updater without interval
			} else {
				setTimeout(function () {
					halo.updater.start('');
				}, halo.updater.interval);
			}
		},

		setInterval: function (interval) {
			halo.updater.interval = parseInt(interval) || halo.updater.interval;
		}
	}
});

/* ============================================================ utilities features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	util: {
		incomplete: false,
		jsonData: {},
		init: function (scope) {
			//2. additional dropdown trigger: privacy dropdown
			jQuery('[data-dropdown-target] a', scope).click(function (e) {
				e.preventDefault();
				var ancestor = jQuery(this).parents('[data-dropdown-target]');
				var id = ancestor.attr('id');
				var label = jQuery(this).html();
				var input_target = jQuery('[data-dropdown-input="' + id + '"]');
				var val = jQuery(this).data('value');
				var button_target = jQuery('[data-dropdown="' + id + '"]');
				input_target.val(val);
				button_target.html(label).trigger('click');
			});

			//3. additional dropdown trigger: map

			// Set event keypress for input elements to check confirmtion popup
			halo.util.onInputChanges({
				eventType: 'change.halo.post keyup.halo.post',
				container: '.halo-data-confirm-wrapper',
				selector: '[data-confirm]',
				handler: function(evt) {
					var validator = halo.util.validateDataConfirmation('.halo-data-confirm-wrapper');
					halo.util.incomplete = !validator.success;
					halo.util.dataConfirm = validator;
				}
			});
			
			// Init confirmation popup for post and shop editor
			halo.util.bindShowLeaving();
			
			// text ellipsis
			jQuery('.haloj-ellipsis',scope).each(function(){
				var $this = jQuery(this);
				if($this.data('initedEllipsis')) return;
				var height = $this.data('height') || 100;
				$this.css('display','block');
				$this.css('max-height', height);
				$this.css('overflow', 'hidden');
				//check if this element has overflow
				if(this.offsetHeight < this.scrollHeight){
					$this.addClass('haloj-truncated');
					
					var url = $this.data('link');
					var blank = '';
					var text = '';
					if(url){
						//continue reading link, need to add read_more param to url
						if(url.indexOf('?') >= 0){
							url = url + '&read_more=1';
						} else {
							url = url + '?read_more=1';
						}
						blank = 'target="_blank"';
						text = __halotext('Continue reading');
					} else {
						url = 'javascript:void(0)';
						blank = '';
						text = __halotext('See more');
					}
					var readMore = jQuery('<a class="haloj-readmore-btn haloj-readmore" href="'+ url +'" '+ blank +'>' + text + '</a>');
					readMore.insertAfter($this);
					jQuery('<div class="haloj-readmore-wrapper">').append(readMore).insertAfter($this);
					readMore.data('target',$this);
					if(!$this.data('link')){
						readMore.on('click',function(){
							var $this = jQuery(this);
							var $target = $this.data('target');
							$this.toggleClass('haloj-readmore');
							if($this.hasClass('haloj-readmore')){
								//toggle to truncated mode
								var scrollTop = jQuery(document).scrollTop();
								var height = $target.data('height') || 100;
								var realHeight = $target[0].offsetHeight;
								$target.css('max-height', height);
								$this.text(__halotext('See more'));
								//adjust the document scroll
								jQuery(document).scrollTop(scrollTop - (realHeight - height));
							} else {
								$target.css('max-height', 'none');
								$this.text(__halotext('See less'));
							}
						})
					}
					//adjustHeight($this, readMore);
				}
				$this.data('initedEllipsis',true)
			});
		},
		lang: function (str) {
			return str;
		},
		addSlashes: function (str) {
			return (str + '').replace(/[\\"']/g, '\\$&').replace(/\u0000/g, '\\0');
		},
		getFormValues: function (frm) {
			var objForm;
			if (typeof frm == 'string' || frm instanceof String) {
				objForm = jQuery('#' + frm);
			}
			if(!objForm || !objForm.length) {
				objForm = jQuery(frm);
			}
			// Find disabled inputs, and remove the "disabled" attribute
			var disabled = objForm.find(':input:disabled').removeAttr('disabled');

			var frmValue = objForm.serializeArray();
			//update checkbox input
			var selector = "input:checkbox:not(:checked)";
			var checkbox = objForm.find(selector);
			if (checkbox.length) {
				jQuery(selector).each(function () {
					frmValue.push({name: this.name, value: '0' });
				});
			}

			// re-disabled the set of inputs that you previously enabled
			disabled.attr('disabled', 'disabled');

			//convert to ajax array format
			frmValue = halo.util.serializeObject(frmValue);

			//store the formID to ajax session for response processing
			var formId = objForm.attr('id');
			if(formId) {
				halo.jax.startSession().set('formId', '#' + formId);
			}
			rtn = {"form": frmValue};
			
			//remove ingore inputs
			var ignoreInput = objForm.find('.ignore-input');
			ignoreInput.each(function() {
				var key = jQuery(this).attr('name');
				if(key) {
					halo.util.deleteFormKey(rtn, key);
				}
			});
			
			//remove not empty inputs
			var ignoreInput = objForm.find('.not-empty-input');
			ignoreInput.each(function() {
				var key = jQuery(this).attr('name');
				if(key) {
					var val = halo.util.getFormValue(rtn, key);
					if(!val){
						halo.util.deleteFormKey(rtn, key);
					}
				}
			});
			
			return rtn;
			
		}, initFormValues: function () {
			return {form: {}};
		}, setFormValue: function (frm, key, val) {
			if (typeof frm.form !== 'undefined') {
				frm.form[key] = val;
			}
		}, extendFormValues: function (frmA, frmB) {
			frmA.form = jQuery.extend(frmA.form, frmB.form);
			return frmA;
		}, deleteFormKey: function (frm, key) {
			if (typeof frm.form !== 'undefined') {
				delete frm.form[key];
			}
		}, getFormValue: function (frm, key) {
			if (typeof frm.form !== 'undefined') {
				return frm.form[key];
			}
		}, getFormParams: function (frm) {
			var objForm;
			var formId = '#' + frm;
			objForm = jQuery('#' + frm);
			// Find disabled inputs, and remove the "disabled" attribute
			var disabled = objForm.find(':input:disabled').removeAttr('disabled');

			var frmValue = jQuery(formId).serializeArray();
			//update checkbox input
			var selector = formId + " input:checkbox:not(:checked)";
			var checkbox = jQuery(selector);
			if (checkbox.length) {
				jQuery(selector).each(function () {
					frmValue.push({name: this.name, value: '0' });
				});
			}

			// re-disabled the set of inputs that you previously enabled
			disabled.attr('disabled', 'disabled');
			var params = {}
			jQuery.each(frmValue, function () {
				params[this.name] = this.value;
			});
			return params;
		},
		resetFormValues: function (frm) {
			var objForm;
			var formId = '#' + frm;
			objForm = jQuery('#' + frm);
			objForm.find('[data-resetable], .data-resetable').each(function () {
				jQuery(this).val('');
				jQuery(this).trigger('change');
				jQuery(this).trigger('halo.onreset');
			});
			//clear any input error message
			objForm.find(':input').each(function () {
				var input = jQuery(this);
				//remove the current error messages
				halo.template.clearInputError(input);

			});

		},

		serializeObject: function (a) {
			var o = {};
			if (a instanceof Array) {
				jQuery.each(a, function () {
					halo.util.parseInputName(o, this.name, this.value);
				});
			}
			return o;
		},

		parseInputName: function (o, param, val) {
			var pos = param.indexOf('[');
			var $currentKey = null;
			var arr = [];
			if (pos > 0) {
				var key = param.substr(0, pos);
				arr.push(key);
				for (var i = pos, c = param.length; i < c; i++) {
					var $char = param.charAt(i);
					if ('[' === $char) {
						if (null !== $currentKey) {
							//invalid input
							return;
						}
						$currentKey = '';
					} else if (']' === $char) {
						if (null === $currentKey) {
							arr.push('');
						} else {
							arr.push($currentKey);
						}

						$currentKey = null;
					} else {
						if (null === $currentKey) {
							//invalid input
							return;
						}

						$currentKey = $currentKey.concat($char);
					}
				}
			} else {
				arr.push(param);
			}
			var ref = o;
			while (arr.length) {
				var key = arr.shift();
				if (key.length == 0) {
					key = -1;
					for (var p in ref) {
						var c = parseInt(p);
						key = (c > key) ? c : key;
					}
					key++;
				}
				if (typeof ref[key] === 'undefined') {
					ref[key] = {};
				}
				if (arr.length == 0) {
					ref[key] = val;
				} else {
					if (!jQuery.isPlainObject(ref[key])) {
						ref[key] = {};
					}
					ref = ref[key];
				}

			}
			return o;
		},

		getCheckedItem: function () {
			callback = arguments[0];
			var checked = []
			jQuery("input[name='cid[]']:checked").each(function () {
				checked.push(parseInt(jQuery(this).val()));
			});
			if (checked.length == 0) {
				alert(__halotext('No items selected'));
			} else if (callback && typeof(callback) === "function") {
				arguments[0] = checked;
				callback.apply({}, arguments);
			}
			return checked;
		},

		reload: function () {
			location.reload();
		},

		back: function () {
			//history.go(-1);
			location.href = document.referrer;
		},

		redirect: function (url) {
			location.href = url;
		},

		reloadWithParam: function (params) {
			var currParams = halo.util.getCurrentQueryObj();
			newParams = jQuery.extend({}, currParams, params);
			window.location.search = '?' + jQuery.param(newParams);
		},
		prettyUrl: function(url) {
			url = url.replace(/%2C|%2c/g, ",").replace(/%3A/g, ":").replace(/%5B/g, "[").replace(/%5D/g, "]").replace(/%25/g, "%");
			return url;
		},
		mergeUrlParams: function(url, params) {
			var parser = document.createElement('a');
			parser.href = url;
			var origParams = halo.util.getQueryObj(url);
			var newParams = jQuery.extend({}, origParams, params);
			var newUrl = parser.protocol + '//' + parser.host + parser.pathname + '?' + jQuery.param(newParams);
			return halo.util.prettyUrl(newUrl);
		},
		setUrl: function (newUrl) {
			//make the newUrl have a better look 
			newUrl = halo.util.prettyUrl(newUrl);
			//only change url if different
			if(newUrl != document.URL) {
				if (history && history.pushState) {
					history.pushState({}, document.title, newUrl);
				}
			}
		},
		setUrlParam: function (params, currParams) {
			if (history && history.pushState) {
				if (typeof currParams == 'undefined') {
					//use the current param if not defined
					currParams = halo.util.getCurrentQueryObj();
				}
				var newParams = jQuery.extend({}, currParams, params);
				//remove all empty params
				for (var p in newParams) {
					if (newParams[p] === null || !newParams[p].length) {
						delete newParams[p];
					}
				}
				var newUrl = '//' + location.host + location.pathname + '?' + jQuery.param(newParams);
				newUrl = halo.util.prettyUrl(newUrl);
				history.pushState({}, document.title, newUrl);
			} else {
				//halo.util.reloadWithParam(params);
			}
		},
		getCurrentQueryObj: function () {
			if (location.search != '') {
				var params = halo.util.getQueryObj(location.href);
				return params;
				//return JSON.parse('{"' + decodeURI(location.search.substring(1).replace(/&/g, "\",\"").replace(/=/g, "\":\"")) + '"}');
			} else {
				return {};
			}
		},
		getQueryObj: function (url) {
			var index = url.indexOf('?');
			if (index != -1) {
				var uri = decodeURIComponent(url.substring(index + 1));
				uri = uri.replace(/\+/g, ' ');
				var chunks = uri.split('&');
				var params = Object();

				for (var i=0; i < chunks.length ; i++) {
					var chunk = chunks[i].split('=');
					if(chunk[0].search("\\[\\]") !== -1) {
						if( typeof params[chunk[0]] === 'undefined' ) {
							params[chunk[0]] = [chunk[1]];

						} else {
							params[chunk[0]].push(chunk[1]);
						}


					} else {
						params[chunk[0]] = chunk[1];
					}
				}
				return params;
			} else {
				return {};
			}
		}, setSystemError: function (msg) {
			halo.util.setSystemMessage(msg, 'error');
		}, setRedirectMessage: function (msg, msgType) {
			halo.storage.setDn('redirectMessage', {msg: msg, msgType: msgType});
		}, scriptCall: function (func, data) {
			var scr = func + '(';
			if (jQuery.isArray(data)) {
				data[0] = ('' + data[0]).replace(/\\\"/g, "\"");
				scr += '(data[0])';
				for (var l = 1; l < data.length; l++) {
					data[l] = ('' + data[l]).replace(/\\\"/g, "\"");
					scr += ',(data[' + l + '])';
				}
			} else {
				scr += 'data';
			}
			scr += ');';
			eval(scr);
		},
		scriptJsonCall: function(func, data) {
			var data = data;
			var scr = func + "(data)";
			eval(scr);
		},
		getRedirectMessage: function () {
			var data = halo.storage.getDn('redirectMessage');
			if (typeof data !== 'undefined' && data) {
				halo.util.setSystemMessage(data.msg, data.msgType);
				halo.storage.deleteDn('redirectMessage');
			}
		}, setSystemMessage: function (msg, msgType) {
			if (!msg.length) return;
			//if the popup is open, display the error on the popup
			//mapping message type with css class
			switch (msgType) {
				case 'message':
					msgType = 'info';
					break;
				case 'error':
					msgType = 'warning';
					break;
			}
			if (halo.popup.isOpen()) {
				halo.popup.setMessage(msg, msgType);
			} else {
				//else display the error on system message div
				var msgDiv = jQuery('<div class="alert alert-' + msgType + '">'
					+ '<button type="button" class="close" data-dismiss="alert">&times;</button>'
					+ msg
					+ '</div>');

				if (!jQuery('#halo-system-message-container').length)
					jQuery('body').append(jQuery('<div id="halo-system-message-container" class="halo-system-error">'));
				jQuery('#halo-system-message-container').append(msgDiv);
				//setup self destroy
				msgDiv.fadeOut(6000, function () {
					jQuery(this).remove();
				});
			}
		}, matchHeight: function () {
			jQuery("[data-match-height]").each(function () {
				var parentRow = jQuery(this),
					slaveCols = jQuery(this).find("[data-height-watch-slave]"),
					masterCols = jQuery(this).find("[data-height-watch-master]"),
					masterHeights = masterCols.map(function () {
						return jQuery(this).height();
					}).get(),
					tallestMaster = Math.max.apply(Math, masterHeights);

				slaveCols.each(function () {
					var $this = jQuery(this);
					$this.css('height', tallestMaster - 80);
				});

			});
		},

		updatePageTitle: function(title){
			document.title = title;
		},
		updateElapsedTime: function () {
			jQuery("[data-utime]").each(function () {
				var timestamp = jQuery(this).data('utime');
				var date = new Date(timestamp * 1000);
				var now = new Date();
				var timediff = Math.abs(now - date);
				//to elapse time
				var elapsedStr = halo.util.toElapsedTime(timediff);
				if (!elapsedStr) {
					//empty elapsedStr return, just get the title as display
					elapsedStr = jQuery(this).attr('title');
				}
				jQuery(this).html(elapsedStr);
			});
			//live elapsed time update
			setTimeout(function () {
				halo.util.updateElapsedTime();
			}, 5000);

		},

		toElapsedTime: function (timediff) {
			var elapse = {};
			var second = 1000, minute = second * 60, hour = minute * 60, day = hour * 24, week = day * 7;
			var balance = timediff;

			elapse.days = Math.floor(balance / day);
			balance = balance - elapse.days * day;
			elapse.hours = Math.floor(balance / hour);
			balance = balance - elapse.hours * hour;
			elapse.minutes = Math.floor(balance / minute);
			balance = balance - elapse.minutes * minute;
			elapse.seconds = Math.floor(balance / second);
			balance = balance - elapse.seconds * second;
			//convert to string
			//rule: only do live elapsed time update for the timediff below 1 day
			var rtn = [];

			if (elapse.days == 0) {
				if (elapse.hours == 1) {
					rtn.push(halo.lang(__halotext('an') + ' ' + __halotext('hour')));
				}
				if (elapse.hours > 1) {
					rtn.push(halo.lang('%s ' + __halotext('hours'), elapse.hours));
				} else {
					if (elapse.minutes == 1) {
						rtn.push(halo.lang('%s ' + __halotext('minute'), elapse.minutes));
					}
					if (elapse.minutes > 1) {
						rtn.push(halo.lang('%s ' + __halotext('minutes'), elapse.minutes));
					}
					if (elapse.seconds > 1 && elapse.minutes == 0) {
						rtn.push(halo.lang('%s ' + __halotext('seconds'), elapse.seconds));
					}
				}
			}
			if (rtn.length) {
				return halo.lang('%s ' + __halotext('ago'), rtn.join(' '));
			} else {
				return '';
			}
		},

		findZone: function (htmlStr) {
			//rule, get the first node that include data-halozone attribute
			var $zone = jQuery(htmlStr);
			var zoneId = $zone.attr('data-halozone');
			if(typeof zoneId !== 'undefined' && zoneId){

			} else {
				$zone = $zone.find('[data-halozone]').first();
			}
			
			return $zone;
			
			var wrapper = jQuery('<div>').append(jQuery(htmlStr));
			var zone = wrapper.find('[data-halozone]').first();
			return zone;
		},

		addValToArrText: function (val, arrText) {
			var arr = arrText.length ? arrText.split(',') : [];
			var index = arr.indexOf('' + val);
			if (index < 0) {
				arr.push(val);
			}
			return arr.join(',');
		},
		remValFromArrText: function (val, arrText) {
			var arr = arrText.length ? arrText.split(',') : [];
			var index = arr.indexOf('' + val);
			if (index > -1) {
				arr.splice(index, 1);
			}
			return arr.join(',');
		},
		toggleState: function (id, model, field) {
			halo.jax.call("system", "ToggleState", id, model, field);
		},

		startFlash: function (e, count) {
			var $e = jQuery(e);
			$e.addClass('halo-blinker');
			if (count > 0) {
				var n = count * 1000;

				setTimeout(function () {
					halo.util.stopFlash(e)
				}, n);
			}
			return;
		},

		stopFlash: function (e) {
			var $e = jQuery(e);
			$e.removeClass('halo-blinker');
		},

		setCookie: function (name, value, expires, path, domain) {
			var cookie = name + "=" + escape(value) + ";";

			if (expires) {
				// If it's a date
				if (expires instanceof Date) {
					// If it isn't a valid date
					if (isNaN(expires.getTime()))
						expires = new Date();
				}
				else
					expires = new Date(new Date().getTime() + parseInt(expires) * 1000 * 60 * 60 * 24);

				cookie += "expires=" + expires.toGMTString() + ";";
			}

			if (path)
				cookie += "path=" + path + ";";
			if (domain)
				cookie += "domain=" + domain + ";";

			document.cookie = cookie;
		},

		getCookie: function (name) {
			var regexp = new RegExp("(?:^" + name + "|;\s*" + name + ")=(.*?)(?:;|$)", "g");
			var result = regexp.exec(document.cookie);
			return (result === null) ? null : result[1];
		},

		throttle: function (fun, delay) {
			var timer = null;

			return function () {
				var context = this, args = arguments;
	
				clearTimeout(timer);
				timer = setTimeout(function () {
					fun.apply(context, args);
				}, delay);
			};
		},
		loopKeys: [],
		loop: function(key, fun, delay) {
			if(halo.util.loopKeys[key]) {
				clearTimeout(halo.util.loopKeys[key]);
			}
			halo.util.loopKeys[key] = setTimeout(function() {
										fun();
										halo.util.loop(key, fun, delay);
									}, delay);
		},
		staticThrottleKeys: [],
		staticThrottle: function(key, func, delay) {
			if(!halo.util.staticThrottleKeys[key]){
				halo.util.staticThrottleKeys[key] = halo.util.throttle(func, delay);
			}
			return halo.util.staticThrottleKeys[key];
		},

		lockFunction: function (fun, delay) {
			var lock = null;
			var timer = null;
			return function () {
				var context = this, args = arguments;
				if (!lock) {
					fun.apply(context, args);
					lock = true;
				}
				clearTimeout(timer);
				timer = setTimeout(function () {
					lock = false;
				}, delay);
			};
		},

		mergeContent: function (contentA, contentB, attr) {
			var ret = [];
			var i = 0, j = 0, k = 0;
			var valA, valB;
			while (i < contentA.length && j < contentB.length) {
				valA = parseInt(jQuery(contentA[i]).attr(attr));
				valB = parseInt(jQuery(contentB[j]).attr(attr));
				if (valA <= valB) {
					ret[k++] = contentA[i++];
				} else {
					ret[k++] = contentB[j++];
				}
			}
			//add the tail to the end
			while (i < contentA.length) {
				ret[k++] = contentA[i++];
			}
			//add the tail to the end
			while (j < contentB.length) {
				ret[k++] = contentB[j++];
			}
			return jQuery(ret);
		},

		convertKeyArr2KeyDot: function (key) {
			var rtn = key;
			if (typeof key !== 'undefined') {
				var i = key.indexOf(']');
				var j = key.indexOf('[');
				if ((i - j) > 1) {
					var arrIndex = key.substr(j, i - j + 1);
					var dotIndex = '.' + key.substr(j + 1, i - j - 1);
					rtn = key.replace(arrIndex, dotIndex);
				}
			}
			return rtn;
		}, dateToYMD: function (date) {
			var d = date.getDate();
			var m = date.getMonth() + 1;
			var y = date.getFullYear();
			return '' + y + '-' + (m <= 9 ? '0' + m : m) + '-' + (d <= 9 ? '0' + d : d);
		}, isIE: function () {
			return navigator.appVersion.indexOf('MSIE') >= 0 ? true : false;
		}, isMobile: function(){
			if(typeof halo.util.mobile == 'undefined'){
				if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
					halo.util.mobile = true; 
				} else {
					halo.util.mobile = false;
				}
			}
			return halo.util.mobile;
		},
		uniqTimePrefix: null,
		uniqTimeIndex: 0,
		uniqID: function () {
			var timePrefix = new Date().getTime();
			if (timePrefix == halo.util.uniqTimePrefix) {
				halo.util.uniqTimeIndex++;
			} else {
				halo.util.uniqTimePrefix = timePrefix;
				halo.util.uniqTimeIndex = 0;
			}
			return halo.util.uniqTimePrefix + '' + halo.util.uniqTimeIndex;
		}, getImageNatureSize: function (img) {
			var $img = jQuery(img);
			if (typeof $img[0]['naturalWidth'] !== 'undefined') {
				return {'width': $img[0]['naturalWidth'], 'height': $img[0]['naturalHeight']};
			} else {
				//ie8 or below
				var oWidth = $img.width();
				var oHeight = $img.height();

				$img.css('width', 'auto');
				$img.css('height', 'auto');

				var rVal = new Object();
				rVal.width = $img.width();
				rVal.height = $img.height();

				$img.css('width', oWidth);
				$img.css('height', oHeight);

				return rVal;
			}
		}, addZonePagination: function (zoneId, paginationHtml) {
			//find the zone
			var $zone = jQuery('[data-halozone="' + zoneId + '"]');
			$paginationHtml = jQuery(paginationHtml);
			if ($zone.length) {
				var currPagination = $zone.siblings('[data-halo-pagination]').first();
				if (currPagination.length) {
					currPagination.replaceWith($paginationHtml);
				} else {
					$zone.after($paginationHtml);
				}
				halo.init($paginationHtml);
			}
		}, timeoutBtn: function (btnId) {
			var $btn = jQuery('#' + btnId);
			if ($btn.length) {
				var timer = $btn.attr('data-timer');
				if (typeof timer !== 'undefined') {
					timer = parseInt(timer);
					if (timer == 0) {
						//simulate the btn click action
						$btn.click();
					} else {
						//decrease the timer and start the timeout
						$btn.attr('data-timer', timer - 1);
						var $counter = jQuery('.btn-counter', $btn);
						if (!$counter.length) {
							$counter = jQuery('<span class="btn-counter"></span>');
							$counter.appendTo($btn);
						}
						$counter.html('(' + timer + ')');
						setTimeout(function () {
							halo.util.timeoutBtn(btnId);
						}, 1000);
					}
				}
			}
		}, badge: function (badgeId, badgeType, content) {
			return html = '<span data-badge="' + badgeId + '" class="badge badge-' + badgeType + ' "><span class="halo-badge-name">' + content + '</span></span>';
		}, enableTagging: function (selector) {
			jQuery(selector).tagging();
		}, focus: function (selector) {
			jQuery(selector).focus();
		}, executeFunctionByName: function (functionName, context /*, args */) {
			var args = Array.prototype.slice.call(arguments, 2);
			var namespaces = functionName.split(".");
			var func = namespaces.pop();
			var $this = window;
			for (var i = 0; i < namespaces.length; i++) {
				$this = $this[namespaces[i]];
			}
			return $this[func].apply(context, args);
		}, isElementInViewport: function (el, delta) {
			if (el instanceof jQuery) {
				el = el[0];
			}

			if(!delta) delta = 0;
			var rect = el.getBoundingClientRect();

			//check for hidden
			if (rect.height == 0 && rect.width == 0) return false;

			return (
				rect.top >= (0 - delta) &&
					rect.left >= (0 - delta) &&
					(rect.bottom - delta) <= (window.innerHeight || document.documentElement.clientHeight) && /*or jQuery(window).height() */
					(rect.right - delta) <= (window.innerWidth || document.documentElement.clientWidth) /*or jQuery(window).width() */
				);
		}, onBlurSlug: function(e) {
            var $ele = jQuery(e);
            var $action = $ele.next('a');
            $action.tooltip({title: __halotext('Click to save slug'), placement: 'left'});
            if ($ele.val() != $ele.data('confirm')) {
                $action.tooltip('show');
            }
        }, editSlug: function (context, id) {
			halo.jax.call("system", "editSlug", context, id);
		}, saveSlug: function (e, context, id) {
			var $ele = jQuery(e);
			var $slug = $ele.prev('[name="slug"]');
			halo.jax.call("system", "saveSlug", context, id, $slug.val());
		}, getPosition: function ($el) {
			var el = $el[0]
			return jQuery.extend({}, (typeof el.getBoundingClientRect == 'function') ? el.getBoundingClientRect() : {
				width: el.offsetWidth, height: el.offsetHeight
			}, $el.offset())
		}, canSubmit: function () {
			//check if user can submit a form
			if (halo.uploader.uploading) {
				halo.util.setSystemError(__halotext('Please wait until file upload completes.'));
				return false;
			}
			return true;
		}, isXs: function(){
			return jQuery(window).width() < 768;
		}, isUrl: function (s) {
			var regexp = /(((ftp|http|https):\/\/)|(www\.{1}))(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/ ;
			return regexp.test(s);
		}, parseWWWUrl: function(s) {
            if (halo.util.isUrl(s)) {
                if (/^(www)\.{1}/.test(s)) {
                    return 'http://' + s;
                    // return s.replace('www.', 'http://');
                }
            }
            return s;
        }, parseUrls: function (s) {
			var regexp = /(((ftp|http|https):\/\/)|(www\.{1}))(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/g ;
			var urls = [];
			var url = '';
			while (match = regexp.exec(s)){
				url = match.shift();
				if(url.trim() !== ''){
					urls.push(url.trim());
				}
			}
			return urls;
		}, setViewMode: function(view, mode){
			var viewName = 'v'+view;
			var opt = {};
			opt[viewName] = mode;
			//reset paging
			opt['pg'] = 1;
			halo.util.reloadWithParam(opt);
		},
		setHtml: function (selector, html) {
			var ele = jQuery(selector);
			if(ele.length) {
				ele.html(html);
			}
			
		}, updateResultCounter: function(selector,str){
			var $ele = jQuery(selector);
			if($ele.length) {
				$ele.text(str);
				//insert refresh btn
				halo.pagination.insertRefreshFilterButton($ele.parent());
			}
		},counting: []
		, countdown: function(scope){
			jQuery('.halo-countdown',scope).each(function(){
				var $ele = jQuery(this);
				var countdown  = $ele.attr('data-countdown');
				if(countdown){
					countdown = parseInt(countdown);
					if(countdown > 0){
						var interval = setInterval(function() {						
							$ele.text(halo.util.secondToDate(countdown));
							if(countdown == 0) {
								clearInterval(interval);
								$ele.remove();
								$ele.trigger('change');
								return;
							}
							countdown --;
						}, 1000);
					}
				}
			});
		}, secondToDate: function(seconds){
			// multiply by 1000 because Date() requires miliseconds
			var date = new Date(seconds * 1000);
			var hh = date.getUTCHours();
			var mm = date.getUTCMinutes();
			var ss = date.getSeconds();
			// This line gives you 12-hour (not 24) time
			if (hh > 12) {hh = hh - 12;}
			// These lines ensure you have two-digits
			if (hh < 10) {hh = "0"+hh;}
			if (mm < 10) {mm = "0"+mm;}
			if (ss < 10) {ss = "0"+ss;}
			// This formats your string to HH:MM:SS
			var t = hh+":"+mm+":"+ss;
			return t;
		}, 
		bindShowLeaving: function() {
			// Show popup confirmation when users leaving some pages
			// var pages = ['/post/edit', '/shop/edit'];
			// for (var i in pages) {
			// 	 if (window.location.pathname.indexOf(pages[i]) != -1) {
			// 		//TODO
			// 		jQuery(window).bind('beforeunload', function(evt) {
			// 			if (halo.util.incomplete) {
			// 				if (typeof halo.util.dataConfirm !== 'undefined' && !halo.util.dataConfirm.success) {
			// 					jQuery(window).scrollTop(halo.util.dataConfirm.input.offset().top);
			// 					halo.util.dataConfirm.input.closest('.form-group').addClass('has-error');
			// 				}
			// 				return (__halotext('You have unsaved changes. Are you sure you want to continue?'));
			// 			}
			// 		});
			// 		break;
			// 	}
			// }
			jQuery(window).bind('beforeunload', function(evt) {
				/*
				if (halo.util.incomplete) {
					if (typeof halo.util.dataConfirm !== 'undefined' && !halo.util.dataConfirm.success) {
						jQuery(window).scrollTop(halo.util.dataConfirm.input.offset().top);
						halo.util.dataConfirm.input.closest('.form-group').addClass('has-error');
					}
					return (__halotext('You have unsaved changes. Are you sure you want to continue?'));
				}
				*/
			});
		}, 
		unbindShowLeaving: function() {
			jQuery(window).unbind('beforeunload');
		},
		setIncomplete: function(status) {
			halo.util.incomplete = status;
		},
		onInputChanges: function(params) {
			jQuery(document).on(params.eventType, params.selector, halo.util.throttle(function(evt) {
				params.handler(evt);
			}, 100));
		}, 
		changePage: function($form, page, keepUrl){
			if(! $form.length) {
				return;
			}
			var values = halo.util.getFormValues($form.attr('id'));
			var com = halo.util.getFormValue(values, 'com');
			halo.util.deleteFormKey(values, 'com');
			var func = halo.util.getFormValue(values, 'func');
			halo.util.deleteFormKey(values, 'func');

			halo.util.setFormValue(values, 'pg', page);
			halo.jax.call(com, func, values);

			if(!keepUrl) {
				halo.util.setUrlParam({'pg': page});
			}
		},
		setDataJson: function(key, value) {
			halo.util.jsonData[key] = value;
		},
		validateDataConfirmation: function(selector) {
			var $selector = jQuery(selector);
			var confirmed = true;
			var $input = null;
			$selector.find('[data-confirm]').each(function() {
				// if (this.id.indexOf('halo_field_unit') !== -1) {
				// 	var value = jQuery(this).attr('value');
				// } else {
				// 	var value = jQuery(this).val();
				// }
				var value = jQuery(this).val();
				if (jQuery(this).data('confirm') != value) {
					confirmed = false;
					$input = jQuery(this);
					return false;
				}
			});
			return {success: confirmed, input: $input};
		},
		addRequiredScripts: function(scripts, callback){
			// Wrapper to include required scripts, scripts can be an array of paths, callback is optional
			var loaded = 0, haveLoadedScript = false;
			for (var i=0; i<scripts.length; i++) {
				var script;
				if (typeof scripts[i] === 'object') {
					script = scripts[i].src;
					if (eval('typeof ' + scripts[i].object) !== 'undefined') {
						loaded++;
						continue;
					}
				} else {
					script = scripts[i];
				}
				halo.util.loadJavaScript(script, function(){ if (++loaded == scripts.length && typeof callback === 'function') callback(); });
				haveLoadedScript = true;
			}
			if (!haveLoadedScript && typeof callback === "function")
				callback();
			return haveLoadedScript;
		},
		isFunction: function (functionToCheck) {
			var getType = {};
			return functionToCheck && getType.toString.call(functionToCheck) === '[object Function]';
		},
		loadJavaScript: function(url, callback){
		// Wrapper to load javascripts into head element with optional callback
			var head = document.getElementsByTagName('head')[0];
			if (head){
				var script = document.createElement('script');
				script.type = 'text/javascript';
				script.src = url;
				// If callback is passed, set up script to call callback when loaded
				if (typeof callback == "function"){
					script.onreadystatechange = function(e) {
						if (!e) e = window.event;
						var el = e.target || e.srcElement;
						if (el && ['loaded', 'complete'].contains(el.readyState)) {
							callback();
						}
					};
					//Bradly SHARPE - Add default OnLoad event
					script.onload = callback;
				}
				head.appendChild(script);
			}
		},
		closest: function(ele, selector) {
			var $parent = jQuery(ele).parent();
			while($parent.length) {
				var found = $parent.find(selector);
				if(found.length) {
					return jQuery(found[0]);
				}
				$parent = $parent.parent();
			}
			return null;
		}
	}
});

/* ============================================================ keycode constants
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	keycode: {
		BACKSPACE: 8, TAB: 9, ENTER: 13, SHIFT: 16, CONTROL: 17, ALT: 18, ESCAPE: 27, LEFT: 37, UP: 38, RIGHT: 39, DOWN: 40, NBSP: 160
	}

});

/* ============================================================ localStorage features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	storage: {
		//keep in mind that storage setting is per user basic
		getUniquedDnId: function (dn) {
			var my_id;
			if (typeof halo_my_id === 'undefined') {
				my_id = 'id0_';
			} else {
				my_id = 'id' + halo_my_id + '_';
			}
			return '' + my_id + dn;

		},
		setAttr: function (dn, obj, attr, val) {
			dn = halo.storage.getUniquedDnId(dn);
			var dnData = JSON.parse(localStorage.getItem(dn));
			if (dnData == null) dnData = {};
			//init obj if not exists
			if (typeof dnData[obj] == 'undefined') {
				dnData[obj] = {};
			}
			dnData[obj][attr] = val;
			localStorage.setItem(dn, JSON.stringify(dnData));
		},

		getAttr: function (dn, obj, attr) {
			dn = halo.storage.getUniquedDnId(dn);
			var dnData = JSON.parse(localStorage.getItem(dn));
			//init obj if not exists
			if (dnData == null || typeof dnData[obj] == 'undefined' || typeof dnData[obj][attr] == 'undefined') {
				return null;
			}
			return dnData[obj][attr];
		},

		deleteAttr: function (dn, obj, attr) {
			dn = halo.storage.getUniquedDnId(dn);
			var dnData = JSON.parse(localStorage.getItem(dn));
			if (dnData != null && typeof dnData[obj] != 'undefined' && typeof dnData[obj][attr] != 'undefined') {
				delete dnData[obj][attr];
				localStorage.setItem(dn, JSON.stringify(dnData));
			}
		},

		setObj: function (dn, obj, val) {
			dn = halo.storage.getUniquedDnId(dn);
			var dnData = JSON.parse(localStorage.getItem(dn));
			if (dnData == null) dnData = {};
			dnData[obj] = val;
			localStorage.setItem(dn, JSON.stringify(dnData));
		},

		getObj: function (dn, obj) {
			dn = halo.storage.getUniquedDnId(dn);
			var dnData = JSON.parse(localStorage.getItem(dn));
			//init obj if not exists
			if (dnData == null || typeof dnData[obj] == 'undefined') {
				return null;
			}
			return dnData[obj];
		},

		deleteObj: function (dn, obj) {
			dn = halo.storage.getUniquedDnId(dn);
			var dnData = JSON.parse(localStorage.getItem(dn));
			if (dnData != null && typeof dnData[obj] != 'undefined') {
				delete dnData[obj];
				localStorage.setItem(dn, JSON.stringify(dnData));
			}
		},

		setDn: function (dn, val) {
			dn = halo.storage.getUniquedDnId(dn);
			try {
				localStorage.setItem(dn, JSON.stringify(val));
				return true;
			} catch (errors) {
				return false;
			}
		},

		toggleDn: function (dn, val) {
			var curr = halo.storage.getDn(dn);
			var newVal = curr?0:1;
			dn = halo.storage.getUniquedDnId(dn);
			try {
				localStorage.setItem(dn, newVal);
				return true;
			} catch (errors) {
				return false;
			}
		},

		getDn: function (dn) {
			dn = halo.storage.getUniquedDnId(dn);
			var dnData = JSON.parse(localStorage.getItem(dn));
			if (dnData == null) {
				return null;
			}
			return dnData;
		},

		deleteDn: function (dn) {
			dn = halo.storage.getUniquedDnId(dn);
			localStorage.removeItem(dn);
		}
	}

})

/* ============================================================ Ajax features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	jax: {
		loadingTimeout: 400,
		session: new haloSession(),
		targetUrl: '',
		requests: [],
		recentPostData: [],
		requestObjects: [],
		
		/**
		 * trigger while doing ajax request
		 */
		onLoading: function () {
			var now = new Date();
			halo.jax.requests.push(now.getTime());
			if (halo.jax.requests.length == 1) {
				//show loading state
				halo.jax.showLoadingState();
			}
		},
		/**
		 * trigger when done ajax request
		 */
		onDoneLoading: function () {
			halo.jax.requests.pop();
			halo.jax.requestObjects.pop();
			if (halo.jax.requests.length == 0) {
				//hide loading state
				halo.jax.hideLoadingState();
			}
		},
		cancelRequests: function() {
			while(halo.jax.requestObjects.length) {
				var req = halo.jax.requestObjects.pop();
				req.abort();
			}
		}
		, showLoadingState: function () {
			var $container = jQuery('#halo-onloading-wrapper');
			if ($container.length) {
				$container.removeClass('hidden');
			} else {
				//jQuery('body').append(jQuery('<div id="halo-onloading-wrapper" class="text-primary">' + halo.template.getJaxLoading(2) + '</div>'));
				jQuery('body').append(jQuery('<div id="halo-onloading-wrapper" class="text-primary">' + halo.template.getJaxLoading(2) + '</div>'));
			}
		}, hideLoadingState: function () {
			jQuery('#halo-onloading-wrapper').addClass('hidden');
		}
		/**
		 * Process the json string
		 */
		 , onProcessResponse: function (responseTxt) {

			// clean up any previous error
			var result = eval(responseTxt);

			// we now have an array, that contains an array.
			for (var i = 0; i < result.length; i++) {

				var cmd = result[i][0];
				var id = result[i][1];
				var property = result[i][2];
				var data = result[i][3];

				var objElement = jQuery(id);

				switch (cmd) {
					case 'as': 	// assign or clear

						if (objElement) {
							eval("objElement." + property + "=  data \; ");
						}
						break;

					case 'uz': 	// update data zone
						halo.zone.updateZone(data);
						break;

					case 'az': 	// after zone
						halo.zone.afterZone(data, id);
						break;

					case 'bz': 	// before zone
						halo.zone.beforeZone(data, id);
						break;

					case 'iz': 	// insert zone
						halo.zone.insertZone(data, id, property);
						break;

					case 'izc': 	// insert zone
						halo.zone.insertZoneContent(data, property);
						break;

					case 'rz': 	// remove zone
						halo.zone.removeZone(id);
						break;

					case 'er':	// error
						if (data) {
							data = jQuery.parseJSON(data);
							//append error message to the submit form
							var formId = this.session.get('formId', '');
							if (formId) {
								halo.form.setInputError(formId, data);
							}
							//for global error message
							if (typeof data['error'] !== 'undefined') {
								var msg = data['error'];
								msg = msg.join('<br>');
								halo.util.setSystemError(msg);
							}
						}
						break;

					case 'al':	// alert
						if (data) {
							alert(data);
						}
						break;

					case 'rm':
						jQuery.remove(id);
						break;

					case 'cs':	// call script
						halo.util.scriptCall(id, data);
						break;
					case 'msg':
						halo.util.setSystemMessage(data, property);
						break;

					case 'red':
						halo.util.setRedirectMessage(property, id);
						location.href = data;
						break;

					case 'ref':
						location.reload(data);
						break;

					case 'stop':
						jQuery('#halo-loading').hide();
						break;
					case 'csj':
						halo.util.scriptJsonCall(id, data);
						break;
					default:
						alert("Unknow command: " + cmd);
				}
			}

			//delete responseTxt;
		},

		/**
		 * Function call to perform an ajax request
		 */
		call: function (comName, sFunction) {

			var arg = "";
			//check for the callback function
			var cbFunc = arguments[arguments.length - 1];
			var args = Array.prototype.slice.call(arguments);
			if(cbFunc && halo.util.isFunction(cbFunc)) {
				args = args.slice(0, -1);
			}
			
			arg = halo.jax.buildArgs(args);
			halo.jax.submitTask(comName, sFunction, arg, cbFunc);
		},
		/**
		 * Buidl argument into string
		 */
		buildArgs: function (arguments) {
			var arg = {};
			if (arguments.length > 2) {
				for (var i = 2; i < arguments.length; i++) {
					var a = arguments[i];
					pro = "arg" + i;
					var d = {};
					if (jQuery.isArray(a)) {
						d._a_ = a;
						arg[pro] = JSON.stringify(d);
					} else if (typeof a == "string") {
						a = a.replace(/"/g, "&quot;");

						d._d_ = encodeURIComponent(a);
						arg[pro] = JSON.stringify(d);

					} else if ((typeof a == "object") && a.hasOwnProperty('form')) {
						//this is for form serialize
						d._f_ = a.form;
						arg[pro] = JSON.stringify(d);
					} else {
						d._d_ = encodeURIComponent(a);
						arg[pro] = JSON.stringify(d);
					}
				}
			}

			return arg;
		},
		/**
		 * Sumbit ajax task
		 */
		submitTask: function (comName, func, postData, responseFunc, cacheKey) {

			var targetUrl = halo_jax_targetUrl;

			// remote controller
			postData.com = comName;
			// remote function call
			postData.func = func;
			//crsf data
			postData.csrf_token = jQuery('meta[name="csrf_token"]').attr('content');
			postData.__refer_url = document.URL;
			for(var i = 0; i < halo.jax.recentPostData.length; i++){
				if(JSON.stringify(halo.jax.recentPostData[i]) === JSON.stringify(postData)){
					return; //prevent duplicate jax call in short time
				}
			}
			halo.jax.recentPostData.push(postData);
			setTimeout(function(){
				halo.jax.recentPostData.pop();
			}, 500);	
			
			//enable loading state
			halo.jax.onLoading();

			var ses;
			var req = jQuery.ajax({
				url: targetUrl,
				type: 'POST',
				data: postData,
				session: jQuery.extend(true, {}, halo.jax.session),
				dataType: 'JSON'}
				)
				.done(function (resp) {
					try {
						halo.jax.onProcessResponse(resp);
					} catch (e) {

					}
					halo.jax.onDoneLoading();
				})
				.fail(function () {
					halo.log('error');
					halo.jax.onDoneLoading();
				})
				.always(function (data, textStatus) {
					//alert( "complete" );
					// halo.jax.onDoneLoading();
					
					if(responseFunc && halo.util.isFunction(responseFunc)) {
						responseFunc.apply(undefined, [data, textStatus]);
					}
				});
			halo.jax.requestObjects.push(req);
			
		},		
		successCb: function(func) {
			return function (data, textStatus) {
				if(textStatus === 'success') {
					func.apply(this, [data]);
				}
			};
		},
		
		errorCb: function(func) {
			return function (data, textStatus) {
				if(textStatus === 'error') {
					func.apply(this, [data]);
				}
			};		
		},
		
		startSession: function () {
			halo.jax.session = new haloSession();
			return halo.jax.session;
		}
	}

});

/* ============================================================ Session features
 *
 * ============================================================ */
function haloSession() {
	this.data = new Object();

	this.set = function (key, val) {
		this.data[key] = val;
		return this;
	};
	this.get = function (key, def) {
		if (typeof this.data[key] === 'undefined') {
			return def;
		} else {
			return this.data[key];
		}
	}
}

/* ============================================================ Popup features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	popup: {
		title: '',
		content: '',
		message: '',
		actions: new Array(),
		instance: null,
		confirmClosing: false,

		reset: function () {
			halo.popup.title = '';
			halo.popup.content = '';
			halo.popup.message = '';
			halo.popup.actions = [];
			halo.popup.instance = null;
		}, 
		
		setFormTitle: function (title) {
			halo.popup.title = title;
			if (halo.popup.isOpen()) {
				//refresh the popup if it is opened
				jQuery('.halo-popup-title').html(halo.popup.title);
			}
		},

		setFormContent: function (content) {
			halo.popup.content = content;
			if (halo.popup.isOpen()) {
				//refresh the popup if it is opened
				jQuery('.halo-popup-content').html(halo.popup.content);
				halo.init(jQuery('.halo-popup-content'));
			}
		},
		addFormContent: function (content) {
			//append to the end of form content
			content = halo.popup.content + content;

			halo.popup.setFormContent(content);
		},
		setMessage: function (msg, msgType, clearContent) {

			switch (msgType) {
				case 'message':
					msgType = 'info';
					break;
				case 'error':
					msgType = 'warning';
					break;
			}

			message = '<div data-alert class="alert fade in alert-box alert-' + msgType + '">'
				+ msg
				+ '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'
				+ '</div>';
			halo.popup.message = message;
			if(clearContent) {
				halo.popup.content = '';
			}

			if (halo.popup.isOpen()) {
				//refresh the popup if it is opened
				jQuery('.halo-popup-message').html(message);
				if(clearContent) {
					jQuery('.halo-popup-content').html('');
					halo.popup.content = '';
				}
			}
		},

		resetFormAction: function () {
			halo.popup.actions = new Array();
		},

		addFormAction: function (opts) {
			if (typeof opts == 'string') {
				opts = jQuery.parseJSON(opts);
			}
			var options = jQuery.extend({    name: 'Action',
				'class': 'halo-btn-primary',
				onclick: '',
				href: 'javascript:void(0);',
				icon: ''
			}, opts);
			var icon = '';
			if (options.icon !== '') {
				icon = '<i class="fa fa-' + options.icon + '"></i>';
			}
			var action = '<button type="button" class="halo-action-btn halo-btn ' + options['class'] + '" onclick="' + options.onclick + '">' + icon + options.name + '</button>';
			halo.popup.actions.push(action);

			if (halo.popup.isOpen()) {
				//refresh the popup if it is opened
				jQuery('.halo-popup-actions').html(halo.popup.getFormAction());
			}
		},

		getFormAction: function () {
			var formAction = '<div class="button-bar row"><div class="button-bar-wrapper halo-pull-right">';
			for (var i = 0; i < halo.popup.actions.length; i++) {
				formAction += halo.popup.actions[i];
			}
			formAction += '</div></div>';
			return formAction;
		},

		addFormActionCancel: function (opts) {
			halo.popup.addFormAction({name: __halotext('Cancel'),
				onclick: 'halo.popup.close()',
				class: 'halo-btn-default',
				icon: 'times'});

		},

		showForm: function (mode) {
			//remove any active popup
			//jQuery('#halo-popup-wrapper').remove();
			var popupClass = '';
			if(typeof mode != 'undefined'){
				popupClass = ' halo-popup-' + mode;
			}
			var formContent = '<div class="halo-popup-wrapper'+popupClass+'">'
				+ '	<div class="halo-popup-title">'
				+ halo.popup.title
				+ '	</div>'
				+ '	<div class="halo-popup-message">'
				+ halo.popup.message
				+ '	</div>'
				+ '	<div class="halo-popup-content">'
				+ halo.popup.content
				+ '	</div>'
				+ '	<div class="clearfix"></div>'
				+ '	<div class="halo-popup-actions">'
				+ halo.popup.getFormAction()
				+ '	</div>'
				+ '</div>';
			if (jQuery.haloMagnificPopup.isOpen) {
				jQuery.haloMagnificPopup.currItem.src = formContent;
				jQuery.haloMagnificPopup.updateItemHTML();
			} else {
				halo.popup.instance = jQuery.haloMagnificPopup.open({
					items: {
						src: formContent
					},
					type: 'inline',
					callbacks: {
						open: function() {
							if (halo.popup.confirmClosing && typeof halo.popup.confirmClosing == 'function') {
								jQuery.haloMagnificPopup.instance.close = function() {
									var confirmation = halo.popup.confirmClosing();
									if (!confirmation.success)	{
										if (!confirm(confirmation.message)) {
											return;
										}
									}
									jQuery.haloMagnificPopup.proto.close.call(this);
								}
							}		
						},
						afterClose: function() {
							halo.popup.confirmClosing = false;
						}
					}
				});
				//magnific scrolling lazyload
				jQuery('.mfp-wrap').on('scroll', halo.util.throttle(function(){
					jQuery(document).trigger('lazyloading.halo');
				}, 100));
			}
			halo.init(jQuery('.halo-popup-wrapper'));
			jQuery(document).trigger('lazyloading.halo');
			//clean up actions after showing
			halo.popup.actions = new Array();

		},

		showLoadForm: function (ajaxCall) {
			halo.popup.setFormTitle(halo.template.getLoadingIcon() + ' ' + __halotext('Loading'));
			halo.popup.setFormContent('');
			halo.popup.showForm();
			eval(ajaxCall);
		},

		confirmDialog: function (title, message, callback) {
			halo.popup.setFormTitle(title);
			halo.popup.setFormContent(message);
			//reset old form actions
			halo.popup.resetFormAction();
			halo.popup.addFormAction({name: __halotext('OK'),
				onclick: callback,
				icon: 'check',
                class: 'halo-btn-primary'
            });
			halo.popup.addFormActionCancel();
			halo.popup.showForm();
		},
		
		confirmDelete: function (title, message, callback) {
			//check for items selected
			var checked = []
			jQuery("input[name='cid[]']:checked").each(function () {
				checked.push(parseInt(jQuery(this).val()));
			});
			if(!checked.length) {
				halo.popup.confirmDialog(title, "Please select at least one item to delete.", 'halo.popup.close()');
			} else {
				halo.popup.confirmDialog(title, message, callback);
			}
		},

        showMsg: function (msg) {
            halo.popup.setFormTitle('HALO');
            halo.popup.setFormContent(msg);
            //reset old form actions
            halo.popup.resetFormAction();
            halo.popup.addFormAction({
                name: __halotext('OK'),
                onclick: 'halo.popup.close()',
                icon: 'check',
                class: 'halo-btn-primary'
            });
            halo.popup.showForm();
        },

		showWizard: function () {
			//remove any active popup
			//jQuery('#halo-popup-wrapper').remove();

			var formContent = '<div class="halo-popup-wrapper">'
				+ '	<div class="halo-popup-message">'
				+ halo.popup.message
				+ '	</div>'
				+ '	<div class="halo-popup-content">'
				+ halo.popup.content
				+ '	</div>'
				+ '	<div class="halo-popup-actions">'
				+ halo.popup.getFormAction()
				+ '	</div>'
				+ '</div>';
			if (jQuery.haloMagnificPopup.isOpen) {
				jQuery.haloMagnificPopup.currItem.src = formContent;
				jQuery.haloMagnificPopup.updateItemHTML();
			} else {
				halo.popup.instance = jQuery.haloMagnificPopup.open({
					items: {
						src: formContent
					},
					type: 'inline'
				});
			}
			//clean up actions after showing
			halo.popup.actions = new Array();

		},
		showLoadWizard: function (ajaxCall) {
			halo.popup.setFormContent(halo.template.getLoadingIcon(2));
			halo.popup.showWizard();
			eval(ajaxCall);
		},

		close: function () {
			jQuery.haloMagnificPopup.close();
		},

		isOpen: function () {
			ele = jQuery('.halo-popup-wrapper');
			if (ele.length > 0) {
				return true;
			} else {
				return false;
			}
		},

		setConfirmClosing: function(handler) {
			halo.popup.confirmClosing = handler;
		}
	}
});

/* ============================================================ Field features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	field: {
		setFieldConfig: function (configParams) {
			jQuery('#fieldConfig').html(configParams);
			halo.init(jQuery('#fieldConfig'));
		},
		changeFieldUnit: function (el) {
			var $el = jQuery(el);
			//convert unit values
			var $unitSelect = $el.closest('[data-unit-target]');
			var targetId = $unitSelect.attr('data-unit-target');
			if (typeof targetId !== 'undefined' && targetId.length) {
				var $target = jQuery('#' + targetId);
				//there are 2 cases: readonly and editing
				var origVal = $target.attr('data-halo-value');
				if (typeof  origVal === 'undefined') {
					//editing case
					var $oldUnit = jQuery('li.active a', $unitSelect);
					var oldRate = parseFloat($oldUnit.attr('data-halo-rate'));
					var newRate = parseFloat($el.attr('data-halo-rate'));
					var oldVal = $target.autoNumeric('get');
					var newVal = oldVal * oldRate / newRate;
				} else {
					//readonly case, the origVal always use based rate
					var newRate = parseFloat($el.attr('data-halo-rate'));
					var oldVal = parseInt(origVal);
					var newVal = oldVal / newRate;
				}
				if (newRate != 0) {
					$target.autoNumeric('set', newVal);
				}

			}
			halo.form.changeTreeSelectOption(el, false);
		},
		initChainSelect: function (subOptions, subFields) {
			//get all option for this input
			var parent = '';
			var value = '';
			for (var i = 0; i < subFields.length; i++) {
				var fieldInput = jQuery('[name="' + subFields[i] + '"]');
				if (fieldInput.length) {
					value = fieldInput.attr('data-halo-value');
					var optionsHtml = halo.field.getChainOptions(subOptions, i, parent, value);
					parent = value;
					halo.field.setChainOption(fieldInput, optionsHtml);
					//bind event
					fieldInput.on('change', { "input": fieldInput, "subOptions": subOptions, "subFields": subFields }, halo.field.onChangeChainSelect);
				}
			}
		}, onChangeChainSelect: function (event) {
			var el = event.data.input;
			var subOptions = event.data.subOptions;
			var subFields = event.data.subFields;
			var $el = jQuery(el);
			//get level
			var level = subFields.indexOf($el.attr('name'));
			if (level >= 0 && level < subFields.length) {
				var parent = $el.val();
				var nextField = jQuery('[name="' + subFields[level + 1] + '"]');
				if (nextField.length) {
					var optionsHtml = halo.field.getChainOptions(subOptions, level + 1, parent, '');
					halo.field.setChainOption(nextField, optionsHtml);
					//trigger on change for next field
					nextField.trigger('change');
				}
			}
		}, setChainOption: function (field, options) {
			var emptyOption = '<option value="" > -- ' + __halotext('Select') + ' -- </option>';
			if (options.length) {
				field.html(emptyOption + options);
				field[0].disabled = false;
			} else {
				field.html(emptyOption);
				field[0].disabled = true;
			}
		}, getChainOptions: function (subOptions, level, parent, value) {
			var levelIndex = subOptions[0].indexOf('level');
			var valueIndex = subOptions[0].indexOf('value');
			var titleIndex = subOptions[0].indexOf('title');
			var parentIndex = subOptions[0].indexOf('parent');
			var options = [];
			var optionHtml = '';
			for (var i = 1; i < subOptions.length; i++) {
				if (subOptions[i][levelIndex] == level && subOptions[i][parentIndex] == parent) {
					optionHtml = '<option value="' + subOptions[i][valueIndex] + '"' +
						((subOptions[i][valueIndex] == value) ? ' selected="true"' : '') + '>' +
						subOptions[i][titleIndex] + '</option>';
					options.push(optionHtml);
				}
			}
			return options.join('');
		},
		assignFieldToGroup: function (subFields, groupId) {
			//get all option for this input
			var groupWrapper = jQuery('#' + groupId);
			if (groupWrapper.length) {
				//define the scope
				var $scope = groupWrapper.closest('.halo-profile-fields');
				for (var i = 0; i < subFields.length; i++) {
					//for editable fields
					var fieldInput = jQuery('[name="' + subFields[i] + '"]', $scope);
					if (fieldInput.length) {
						var field = fieldInput.closest('.halo-field-layout-wrapper').appendTo(groupWrapper);
					}
					//for readable fields
					var fieldHtml = jQuery('[data-halo-field="' + subFields[i] + '"]', $scope);
					if (fieldHtml.length) {
						fieldHtml.appendTo(groupWrapper);
					}

				}
			}
		}, assignFieldDateRange: function (subFields, rangeId) {
			//get all option for this input
			var rangeWrapper = jQuery('#' + rangeId);
			if (rangeWrapper.length) {
				//define the scope
				var $scope = rangeWrapper.closest('.halo-profile-fields');
				//setup startField
				var startField = jQuery('[name="' + subFields.start + '"]', $scope);
				if (startField.length) {
					startField.closest('.halo-field-layout-wrapper').appendTo(jQuery('.halo-startdate', rangeWrapper));
				}
				//setup endField
				var endField = jQuery('[name="' + subFields.end + '"]', $scope);
				if (endField.length) {
					endField.closest('.halo-field-layout-wrapper').appendTo(jQuery('.halo-enddate', rangeWrapper));

				}
				//setup constrain
				if (startField.length && endField.length) {
					startField.closest('.halo_field_date, .halo_field_datetime, .halo_field_time').on('changeDate', function (ev) {
						var startDate = ev.date;
						if(startDate){
							//datetimepicker treat the setDate value as local date, but the return value is UTC date, so we need to covert the startDate to local date
							startDate = new Date(startDate.valueOf() + startDate.getTimezoneOffset() * 60000);
						}
						endField.closest('.halo_field_date, .halo_field_datetime, .halo_field_time').datetimepicker('setStartDate', startDate)
							.datetimepicker('show');
					})
				}
			}
		}, init: function (scope) {
			var $tabs = jQuery('.halo-field-tab',scope);
			if ($tabs.length) {
				//generate the tab hmtl structure
				var $tabContainer = jQuery('<div class="halo-field-tab-container">'
					+ '<ul class="halo-nav nav-tabs halo-tab-nav"></ul>'
					+ '<div class="halo-tab-content"></div>'
					+ '</div>');
				//append the tabContainer to the first found halo-field-tab
				$tabContainer.insertBefore(jQuery($tabs[0]));
				$tabs.each(function () {
					var tabName = jQuery(this).attr('data-tab-name');
					var tabId = halo.util.uniqID();
					jQuery('.halo-tab-nav', $tabContainer).append(jQuery('<li class=""><a href="#' + tabId + '" data-htoggle="tab">' + tabName + '</a></li>'));
					var $tabContent = jQuery('<div class="tab-pane halo-tab" id="' + tabId + '"></div>');
					jQuery('.halo-tab-content', $tabContainer).append($tabContent);
					var $siblings = jQuery(this).nextUntil('.halo-field-tab');
					$siblings.appendTo($tabContent);
				})
				jQuery('.halo-tab-nav a:first', $tabContainer).htab('show');
				$tabs.remove();
			}
		}, 
		initDateRange: function (rangeId, startEle, endEle) {
			//get all option for this input
			var rangeWrapper = jQuery('#' + rangeId);
			if (rangeWrapper.length) {
				//setup startField
				var startField = jQuery(startEle, rangeWrapper);
				//setup endField
				var endField = jQuery(endEle, rangeWrapper);
				var startInput = jQuery('input',startField);
				var endInput = jQuery('input',endField);
				//setup constrain
				if (startField && startField.length && endField.length && startInput.length && endInput.length) {
					startField.datetimepicker({
						weekStart     : 1,
						todayBtn      : 1,
						autoclose     : 1,
						todayHighlight: 1,
						startView     : 2,
						minView       : 2,
						forceParse    : 0,
						pickerPosition: "bottom-left"
					});
					endField.datetimepicker({
						weekStart     : 1,
						todayBtn      : 1,
						autoclose     : 1,
						todayHighlight: 1,
						startView     : 2,
						minView       : 2,
						forceParse    : 0,
						pickerPosition: "bottom-left"
					});
					//setup constrain
					
					startField
						.on('changeDate', function (ev) {
							var startDate = ev.date;
							if(startDate){
							//datetimepicker treat the setDate value as local date, but the return value is UTC date, so we need to covert the startDate to local date
								startDate = new Date(startDate.valueOf() + startDate.getTimezoneOffset() * 60000);
							}
							endField.datetimepicker('setStartDate', startDate);
						})
						;
					endField
						.on('changeDate', function (ev) {
							var endDate = ev.date;
							if(endDate){
								//datetimepicker treat the setDate value as local date, but the return value is UTC date, so we need to covert the endDate to local date
								endDate = new Date(endDate.valueOf() + endDate.getTimezoneOffset() * 60000);
							} else {
								endDate = startField.data('date-enddate');
							}
							startField.datetimepicker('setEndDate', endDate);
						})
						;
				}
			}
		},
		showTooltip: function(show) {
			var $tips = jQuery('#popupForm [name="tips"]').closest('.form-group');
			show = parseInt(show);
			if($tips.length) {
				if(show) {
					$tips.show();
				} else {
					$tips.hide();
				}
			}
		},
		setReadOnlyMode: function(readonly){
			readonly = parseInt(readonly);
			//tooltips
			var $tips = jQuery('#popupForm [name="tips"]').closest('.form-group');
			if($tips.length) {
				if(readonly) {
					$tips.hide();
				} else {
					$tips.show();
				}
			}
			
			//published
			var $published = jQuery('#popupForm [name="published"]').closest('.form-group');
			if($published.length) {
				if(readonly) {
					$published.removeClass('col-md-6');
				} else {
					$published.addClass('col-md-6');
				}
			}

			//highlight
			var $highlight = jQuery('#popupForm [name="params[highlight]"]').closest('.form-group');
			if($highlight.length) {
				if(readonly) {
					$highlight.hide();
				} else {
					$highlight.show();
				}
			}
			
			//required
			var $required = jQuery('#popupForm [name="required"]').closest('.form-group');
			if($required.length) {
				if(readonly) {
					$required.hide();
				} else {
					$required.show();
				}
			}		

			//privacy
			var $privacy = jQuery('#popupForm [name="params[enablePrivacy]"]').closest('.form-group');
			if($privacy.length) {
				if(readonly) {
					$privacy.hide();
				} else {
					$privacy.show();
				}
			}		
		}
	}
});

/* ============================================================ Label features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	label: {
		showLabelTypeOpt: function (opt) {
			//hide all current label type options
			jQuery('.label_type_opt').addClass('hidden');
			//show only active label options
			jQuery('.label_type_opt.opt_' + opt).removeClass('hidden');
		},
		showAssignLabel: function (context, target_id) {
			halo.jax.call('label', 'ShowAssignLabelForm', context, target_id);
		},
		assignLabel: function () {
			var values = halo.util.getFormValues('popupForm');
			halo.jax.call('label', 'AssignLabel', values);
		}

	}
});

/* ============================================================ Notification features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	notification: {
		userId: 0,
		list: function (e, userId) {
			halo.notification.userId = userId;
		},
		init: function (scope) {
			jQuery('.halo-notification-toggle', scope).each(function () {
				//enable popover on the element
				$e = jQuery(this);
				$e.attr('data-htoggle', 'popover');
				$e.attr('data-placement', 'bottom');
				//enable popover
				var $popover = $e.hpopover({
					html: true,
					trigger: 'click',
					delay: { show: 100, hide: 150 },
					//container: 'body',
					template: '<div class="popover"><div class="arrow"></div><div class="popover-content halo-notification-wrapper"></div></div>',
					content: function () {
						var loadingDiv = '<div data-halozone="halo-notification-list" class="halo-notification-loading">'
							+ halo.template.getJaxLoading(2)
							+ '</div>';
						return loadingDiv;
					}
				});
				var clickEveryWhere = function (e) {
					if (jQuery(e.target).closest('.halo-notification-wrapper').length === 0) {
						jQuery('.halo-notification-toggle').hpopover('hide').parent().find('.popover').css('display','none');
					}
				};
				var pressEscapse = function (e) {
					if (e.keyCode === 27) {
						jQuery('.halo-notification-toggle').hpopover('hide');
					}
				};

				$e.on('shown.bs.hpopover', function () {
					//load popover content from server
					if (halo.notification.userId) {
						//execute  ajax call to update the popover content
						var values = halo.util.initFormValues();
						halo.jax.call("notification", "loadNotification", values);
					}

					jQuery(document.body).on('click', clickEveryWhere);

					jQuery(document).on('keydown', pressEscapse);

				});

				$e.on('hide.bs.hpopover', function () {
					jQuery(document.body).off('click', clickEveryWhere);
					jQuery(document).off('keydown', pressEscapse);
				});
			});

			//event handler for notification click
			jQuery('.halo-notification-item', scope).each(function () {
				var $this = jQuery(this);
				var notifId = jQuery(this).attr('data-notifid');
				$this.on('click', function (evt) {
					if (evt.target.nodeName.toLowerCase() != 'a') {
						if (typeof notifId !== 'undefined') {
							halo.jax.call('notification', 'showNotification', notifId);
						}
					}
				});
				jQuery('.halo-notification-action .halo-notifiation-markread', $this).on('click', function (event) {
					if (typeof notifId !== 'undefined') {
						halo.notification.markAsRead(notifId);
					}
					event.stopPropagation();
				});
				jQuery('.halo-notification-action .halo-notification-hide', $this).on('click', function (event) {
					if (typeof notifId !== 'undefined') {
						halo.notification.hide(notifId);
					}
					event.stopPropagation();
				});
			})
		},
		updater: function (scope) {
			var values = {};
			if (typeof halo_my_id === 'undefined' || halo_my_id == 0) return;
			values.form = {};
			values.form["context"] = "notification";
			return values;
		}, markAsRead: function (notifId) {
			halo.jax.call('notification', 'markAsRead', notifId);
		}, setReadState: function (notifId, isRead) {
			if (isRead) {
				jQuery('#halo-notification-item-' + notifId).removeClass('unread');
			} else {
				jQuery('#halo-notification-item-' + notifId).addClass('unread');
			}
		}, remove: function (notifId) {
			jQuery('#halo-notification-item-' + notifId).remove();
		}, hide: function (notifId) {
			halo.jax.call('notification', 'hide', notifId);
		}, showSettings: function () {
			var ajaxCall = 'halo.jax.call("notification", "ShowSettings");';
			halo.popup.showLoadForm(ajaxCall);

		}, saveSettings: function () {
			var values = halo.util.getFormValues('popupForm');
			halo.jax.call("notification", "SaveSettings", values);
		}, showFullview: function (ele) {
			halo.jax.call('notification', 'ShowFullview');
		}
	}
});

/* ============================================================ Pagination features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	pagination: {
		changeLimit: function (value) {
			halo.util.reloadWithParam({limit: value, pg: 1});
		},
		changeFilter: function () {
			var filters = halo.util.getFormValues('filter_form');
			filters = filters.form;
			var param = {};
			for (index = 0; index < filters.length; ++index) {
				param[filters[index][0]] = filters[index][1];
			}

			var filters = halo.util.getFormParams('filter_form');
			halo.util.reloadWithParam(jQuery.extend({pg:1}, filters));
		}, 
		applyAjaxFilter: function () {

		}, 
		updatePagingNav: function(ele,direction){
			var $this = jQuery(ele);
			var $wrapper = $this.closest('.halo-paging-nav');
			var currentPostLbl = jQuery('.halo-paging-nav-current',$wrapper);
			var newPost = parseInt(currentPostLbl.text()) + parseInt(direction) - 1;		//zero base index
			var postCount = parseInt($wrapper.data('count'));
			newPost = newPost % postCount;
			if(newPost < 0){
				newPost = newPost + postCount;
			}
			//set new label
			currentPostLbl.text(newPost + 1);	//1 base index
			return newPost;
		},
		refreshFilter: function(ele) {
			var $this = jQuery(ele);
			//find filter label input
			var $panel = $this.closest('.tab-pane.active');
			if($panel.length) {
				var filterLabel = jQuery('.halo-filter-label-input', $panel);
				if(filterLabel.length) {
					var html = filterLabel.outerHTML();
					html = html.replace('displaySection(', 'refreshSection(');
					var cloneLabel = jQuery(html).hide();
					filterLabel.after(cloneLabel);
					cloneLabel.trigger('change').remove();
				}
			}
		},
		insertRefreshFilterButton: function(beforeEle, strClass) {
			var $beforeEle = jQuery(beforeEle);
			strClass = strClass?strClass:'';
			if($beforeEle.length) {
				//check for exists button
				if($beforeEle.siblings('.halo-refresh-filter-btn').length)  return;
				//make sure the filter label input exists
				var $panel = $beforeEle.closest('.tab-pane.active');
				if($panel.length) {
					var filterLabel = jQuery('.halo-filter-label-input', $panel);
					if(filterLabel.length) {
						var $btn = jQuery('<span class="halo-refresh-filter-btn' + strClass + '" onclick="halo.pagination.refreshFilter(this)" title="' + __halotext("Reload") + '"><i class="fa fa-refresh"></i></span>');
						$beforeEle.after($btn);
					}
				}
			}
		},
        init: function(scope) {
            //prevent pagination ajax default link behaviour
            jQuery('.halo-pagination-ajax ul.pagination a.halo-pagination-link', scope).on('click', function(e) {
                e.preventDefault();
            });
        }		
	}
});

/* ============================================================ Form features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	form: {
		checkAllBox: function (checkbox, stub) {
			c = 0;
			for (i = 0, n = checkbox.form.elements.length; i < n; i++) {
				e = checkbox.form.elements[i];
				if (e.type == checkbox.type) {
					if ((stub && e.name.indexOf(stub) == 0) || !stub) {
						e.checked = checkbox.checked;
						c += (e.checked == true ? 1 : 0);
					}
				}
			}
			if (checkbox.form.boxchecked) {
				checkbox.form.boxchecked.value = c;
			}
			return true;
		},
		submit: function (formId) {
			//additional validation put here
			document.forms[formId].submit();
		},
		setInputError: function (formId, errors) {
			//the display of error message depends on template layout. @todo: move to template javascript
			var form = jQuery(formId);
			if (form.length) {
				//clear old input error messages
				jQuery(formId + ' :input').each(function () {
					var input = jQuery(this);
					//remove the current error messages
					halo.template.clearInputError(input);
				});
				//add new input error messages
				jQuery(formId + ' :input').each(function () {
					var input = jQuery(this);
					var inputName = input.attr('name');	
					//clean input name in array format
					if(inputName) {
						inputName = inputName.replace('[]', '');
					}
					var inputNameDot = halo.util.convertKeyArr2KeyDot(inputName);
					if (typeof errors[inputName] !== 'undefined') {
						//set the new error message
						var messages = errors[inputName];
						halo.template.setInputError(input, messages);
					} else if (typeof errors[inputNameDot] !== 'undefined') {
						//set the new error message
						var messages = errors[inputNameDot];
						halo.template.setInputError(input, messages);
					}
				});
			}

		},
		clearInputError: function(ele) {
			var $input = jQuery(ele);
			var $control = $input.closest('.form-group');
			if($control.length) {
				$input.tooltip('destroy');
				$control.removeClass('has-feedback has-error');
				$control.find('.form-control-feedback').remove();
				$control.find('.help-block').remove();
			}
			
		},
		checkFormReady: function(formId, passCb, failCb) {
			var form = jQuery(formId);
			//bind on change event on all input
			var checkFn = function() {
				if(!form.find('.has-error').length) {
					if(passCb) passCb();
				} else {
					if(failCb) failCb();
				}
			}
			
			//bind checking
			form.find(':input')
				.keyup(halo.util.throttle(function(){
					halo.form.clearInputError(jQuery(this));
					halo.util.loop(formId, checkFn, 4000);
				}, 1000))
				.change(checkFn);
		},
		changeTreeSelectOption: function (el, triggerEvent) {
			var $el = jQuery(el);
			var inputName = $el.data('halo-input');
			var form = $el.closest('form');
			//var input = jQuery('[name="' + inputName + '"]', form);
			var input = jQuery('[name="' + inputName + '"]');
			//check if multiple selection tree
			var isMultiple = input.data('halo-multiple');
			if (typeof isMultiple !== 'undefined' && isMultiple !== false) {
				isMultiple = true;
			} else {
				isMultiple = false;
			}

			//process differently fo multiple and single select tree
			if (isMultiple) {
				//for multiple select, click an option to toggle select
				var selectedVal = $el.data('halo-value');
				var currVal = jQuery.trim(input.val());
				var currValArr = currVal.length ? currVal.split(',') : [];
				var index = currValArr.indexOf('' + selectedVal);
				if ($el.parent().hasClass('active')) {
					//toggle off
					$el.find('i.halo-tree-selected').remove();
					$el.parent().removeClass('active');
					if (index > -1) {
						currValArr.splice(index, 1);
					}
				} else {
					//toggle on
					if (jQuery('i.fa', $el).length) {
						$el.html($el.html());
					} else {
						$el.html('<i class="halo-tree-selected fa fa-check"></i> ' + $el.html());
					}
					$el.parent().addClass('active');
					if (index < 0) {
						currValArr.push(selectedVal);
					}
				}
				input.val(currValArr.join(','));
				//update the selection
				var sel = $el.closest('[data-tree-select]');
				var selected = jQuery('li.active', sel);
				var selectedText = [];
				selected.each(function () {
					selectedText.push(jQuery(this).text());
				})
				jQuery('.halo-filter-selected-val', sel).html(selectedText.join(','));
			}
			else {
				input.val($el.data('halo-value'));
				//remove all current checked item
				jQuery('[data-halo-input="' + inputName + '"]').each(function () {
					jQuery(this).find('i.halo-tree-selected').remove();
					jQuery(this).parent().removeClass('active');
				});
				//add check icon on the selected option
				if (jQuery('i.fa', $el).length) {
					$el.html($el.html());
				} else {
					$el.html('<i class="halo-tree-selected fa fa-check"></i> ' + $el.html());
				}
				$el.parent().addClass('active');
				//update the selection
				var sel = $el.closest('[data-tree-select]');
				sel.children().first().html($el.html());
				//close the toggle display
				sel.children('.halo-toggle-display').addClass('hidden');
			}

			//reset validation on change
			halo.template.clearInputError(input);
			//trigger on change event for hidden input
			if (typeof triggerEvent === 'undefined' || triggerEvent) {
				input.trigger('change');
				//trigger validation action
				if(input.closest('form').length){
					input.valid();
				}
			}
		}, selectTreeNode: function (ele) {
			//on click/touch on non leaf node and the leafOnly select mode is enable, automatically expend the node
			var $changeLevel = jQuery(ele).siblings('.halo-tree-change-level');
			if ($changeLevel.length) {
				$changeLevel.trigger('click');
			}
		},
		changeTreeLevel: function (levelId) {
			var $treeOpts = jQuery('ul[data-level-id="' + levelId + '"]');
			//generate short opt list that only show one tree level
			var $levelWrapper = jQuery('<div class="halo-tree-select-wrapper halo-toggle-display"></div>');
			var $levelHeader = jQuery('<div class="halo-tree-level-header"></div>');
			var $levelOpts = $treeOpts.clone().removeClass('hidden halo-toggle-display')
			var $parentLevel = $treeOpts.parents('ul[data-level-id]');

			//short options only have one level options
			jQuery('.halo-tree-select-menu', $levelOpts).remove();

			//build level wrapper content
			//only generate level header if there is parent level
			if ($parentLevel.length) {
				var parentLevelId = $parentLevel.attr('data-level-id');
				var parentLevelTitle = $treeOpts.siblings('.halo-tree-level-title').find('span');
				if (parentLevelTitle.length) {
					$levelHeader.append('<div class="halo-tree-level-header-title"><a href="javascript:void(0)" data-level-id="' + parentLevelId + '" class="halo-tree-change-level"><i class="fa fa-angle-double-left"></i>' + parentLevelTitle.text() + '</a></div>');
				}
				$levelWrapper.append($levelHeader);

			}
			$levelWrapper.append($levelOpts);
			//bind onclick to expand sub level
			jQuery('a.halo-tree-change-level', $levelWrapper).on('click', function () {
				halo.form.changeTreeLevel(jQuery(this).attr('data-level-id'));
			});
			var $treeWrapper = $treeOpts.closest('[data-tree-select]');
			if ($treeWrapper.length) { //check for valid tree html structure
				var $optWrapper = jQuery('.halo-tree-select-wrapper', $treeWrapper);
				//insert (if not exists) or replace (if exists)
				if ($optWrapper.length) {
					$optWrapper.replaceWith($levelWrapper);
				} else {
					$toggleBtn = jQuery('[data-htoggle="display"]', $treeWrapper);
					if ($toggleBtn.length) {
						$levelWrapper.addClass('hidden').insertAfter($toggleBtn);
					}
				}
			}
		},
		editInlineLocation: function (e) {
			var $this = jQuery(e);
			$this.text(__halotext('Update'));
			var url = $this.attr('href');
			var context = $this.attr('data-context');
			var targetid = $this.attr('data-targetid');

			$this.attr('href', 'javascript:void(0)');
			$this.attr('onclick', "halo.form.refreshInlineLocation('" + context + "','" + targetid + "')");
			window.open(url, '_blank');
		},
		refreshInlineLocation: function (context, targetid) {
			halo.jax.call('system', 'RefreshInlineLocation', context, targetid);
		},
		showMobileForm: function(selector,title){
			var form = jQuery(selector);
			halo.popup.setFormTitle(title);
			if(form.length){
				halo.popup.setFormContent(form.parent().html());
			}
			halo.popup.showForm();
			halo.popup.addFormAction({"name": __halotext('OK'),"onclick": "halo.form.updateMobileForm(this)","href":"javascript:void(0);"})
			halo.popup.addFormActionCancel();
			
		},
		updateMobileForm: function(ele){
			
		},
		initUserTag: function (scope) {
			var input = jQuery('.halo-user-tag',scope);
			if(!input.length) return;
			input.each(function(){
				var $this = jQuery(this);
				var users = new Bloodhound({
					datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
					queryTokenizer: Bloodhound.tokenizers.whitespace,
					remote: {
						url: halo.util.mergeUrlParams(halo_jax_targetUrl, {'term':'%QUERY', 'com':'autocomplete', 'func':'searchUsers', 
																		'csrf_token':jQuery('meta[name="csrf_token"]').attr('content') + (input.data('search')?'&param='+input.data('search'):'')}),
						beforeSend: function (jqXhr, settings) {
							settings.data = JSON.stringify({
								"com": "autocomplete",
								"func": "searchUsers",
								csrf_token: jQuery('meta[name="csrf_token"]').attr('content')
							});
							return true;
						}
					}
				});
				users.initialize();
				$this.typeahead({highlight: true}, {
					name: 'taggedusers',
					displayKey: 'name',
					source: users.ttAdapter(),
					templates: {
						empty: '<div class="halo-users-not-found text-center">' + __halotext('No User Matched') + '</div>',
						suggestion: suggestionTemplate
					},
					engine: Hogan
				})
					.on('typeahead:selected', onTaggingUser)
					.on('typeahead:autocompleted', onTaggingUser)

				;
			});
			function suggestionTemplate(context) {
				return Hogan.compile('<p class="name"><image class="halo-suggestion-img" src={{image}}>{{name}}</p>').render(context);
			};

			function onTaggingUser(e, datum) {
				var parent = jQuery(e.target).closest('.form-group');
				var inputVal = jQuery('.halo-user-tag-value',parent);
				var input = jQuery('.twitter-typeahead',parent);
				if(input.length && inputVal.length){
					var html = '<span class="tm-tag tm-tag-success">'
											+'<span>'+datum.name+'</span>'
											+' <a href="javascript:void(0)" class="tm-tag-remove" data-id="'+datum.id+'" onclick="halo.form.removeUserTag(this)">x</a>'
										+'</span>';
					if(jQuery('[data-mode="single"]',input).length){
						inputVal.val(datum.id);
						jQuery('.tm-tag',parent).remove();
					} else {
						var newVal = halo.util.addValToArrText(datum.id,inputVal.val());
						inputVal.val(newVal);
					}
					inputVal.trigger('change');
					jQuery(html).insertBefore(input);
				}
			}

		},

		initDateRange: function (startId, endId) {
			//get all option for this input
			var startInput = jQuery('#' + startId);
			var endInput = jQuery('#' + endId);
			//setup constrain
			if (startInput.length && endInput.length) {
				startInput.datetimepicker({
					weekStart     : 1,
					todayBtn      : 1,
					autoclose     : 1,
					todayHighlight: 1,
					startView     : 2,
					minView       : 2,
					forceParse    : 0,
					pickerPosition: "bottom-left"
				});
				endInput.datetimepicker({
					weekStart     : 1,
					todayBtn      : 1,
					autoclose     : 1,
					todayHighlight: 1,
					startView     : 2,
					minView       : 2,
					forceParse    : 0,
					pickerPosition: "bottom-left"
				});
				//setup constrain
				
				startInput
					.on('changeDate', function (ev) {
						var startDate = ev.date;
						if(startDate){
						//datetimepicker treat the setDate value as local date, but the return value is UTC date, so we need to covert the startDate to local date
							startDate = new Date(startDate.valueOf() + startDate.getTimezoneOffset() * 60000);
						}
						endInput.datetimepicker('setStartDate', startDate);
					})
					;
				endInput
					.on('changeDate', function (ev) {
						var endDate = ev.date;
						if(endDate){
							//datetimepicker treat the setDate value as local date, but the return value is UTC date, so we need to covert the endDate to local date
							endDate = new Date(endDate.valueOf() + endDate.getTimezoneOffset() * 60000);
						} else {
							endDate = startInput.data('date-enddate');
						}
						startInput.datetimepicker('setEndDate', endDate);
					})
					;
			}
		},
		
		removeUserTag: function(e){
			var $this = jQuery(e);
			var parent = $this.closest('.form-group');
			var inputVal = jQuery('.halo-user-tag-value',parent);
			if(inputVal.length){
				var newVal = halo.util.remValFromArrText($this.data('id'),inputVal.val());
				inputVal.val(newVal);
				inputVal.trigger('change');				
			}
			$this.closest('.tm-tag-success').remove();
		},
		initHashTag: function (scope) {
			var input = jQuery(".halo-hash-tag",scope);
			if(!input.length) return;
			input.each(function(){
				var $this = jQuery(this);
				if($this.data('initedTag')) return;	//only init one time
				//build tagmanager options
				var tmOptions = buildTMOptions($this);
				$this.tagsManager(tmOptions);

				var tags = new Bloodhound({
					datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
					queryTokenizer: Bloodhound.tokenizers.whitespace,
					remote: {
						url: halo.util.mergeUrlParams(halo_jax_targetUrl, {'term':'%QUERY', 'com':'autocomplete', 'func':'searchTags', 
																		'csrf_token':jQuery('meta[name="csrf_token"]').attr('content') + (input.data('search')?'&param='+input.data('search'):'')}),
						beforeSend: function (jqXhr, settings) {
							settings.data = JSON.stringify({
								"com": "autocomplete",
								"func": "searchTags",
								csrf_token: jQuery('meta[name="csrf_token"]').attr('content')
							});
							return true;
						}
					}
				});
				tags.initialize();
				$this.typeahead({highlight: true}, {
					name: 'taggedusers',
					displayKey: 'name',
					source: tags.ttAdapter(),
					templates: {
						empty: '<div class="halo-tags-not-found text-center">' + __halotext('No Tag Found') + '</div>',
						suggestion: suggestionTemplate
					},
					engine: Hogan
				})
				;
				$this.data('initedTag', true);
				$this.focus();
				
			});
			function suggestionTemplate(context) {
				return Hogan.compile('<p class="name">{{name}}</p>').render(context);
			};
			
			function buildTMOptions($input){
				
				var options = {
						deleteTagsOnBackspace: false,
						preventSubmitOnEnter: false,
						tagClass: 'tm-tag-success',
						tagTarget: '_blank',
						tagsContainer: '#' + $input.data('container-id')
					};
				options = jQuery.extend(options,$input.data());
				
				//if ajax push is parameters
				var tagTargetId = $input.attr('data-targetid');
				var tagContext = $input.attr('data-context');
				if( tagTargetId && tagContext){
					options.AjaxPush = halo_jax_targetUrl + '?com=autocomplete&func=pushTags&csrf_token=' + jQuery('meta[name="csrf_token"]').attr('content');
					options.AjaxPushParameters = { 
													com: "autocomplete",
													func: "pushTags",
													targetid: tagTargetId,
													context: tagContext,
													csrf_token: jQuery('meta[name="csrf_token"]').attr('content')
												};
					options.AjaxPushAllTags = true;
				}
				return options;
			}
		},
		initTMTag: function (scope) {
			var input = jQuery(".halo-tm-tag",scope);
			if(!input.length) return;
			
			input.each(function(){
				var $this = jQuery(this);
				//build tagmanager options
				var tmOptions = buildTMOptions($this);
				$this.tagsManager(tmOptions);
				
			});
			
			function buildTMOptions($input){
				
				var options = {
						deleteTagsOnBackspace: false,
						preventSubmitOnEnter: false,
						tagClass: 'tm-tag-success',
						tagTarget: '_blank',
						tagsContainer: '#' + $input.data('container-id')
					};
				options = jQuery.extend(options,$input.data());				
				return options;
			}
		},
		initHammer: function (scope){
			jQuery('.halo-hammer',scope).hammer();
		},
		initMultipleSelect: function(scope) {
			jQuery('select[multiple] option', scope).each(function() {
				var toggle = jQuery(this).prop('selected');
				jQuery(this).click(function(evt) {
					jQuery(this).prop('selected', toggle = !toggle);
				});
			});
		},
		initWizard: function(scope) {
			jQuery('.halo-wizard-wrapper', scope).each(function() {
				var $wizard = jQuery(this);
				var options = jQuery.extend({}, $wizard.data());
				//get all steps
				var $steps = $wizard.find('.halo-wizard-steps > .halo-wizard-step-title');
				//get all buttons
				var $prevBtn = $wizard.find('.halo-wizard-btn-previous');
				var $nextBtn = $wizard.find('.halo-wizard-btn-next').hide();
				var $cancelBtn = $wizard.find('.halo-wizard-btn-cancel');
				var $finishBtn = $wizard.find('.halo-wizard-btn-finish').hide();
				
				function hasPreviousStep($step) {
					return $step.prevAll(':not(.ignore)').length;
				}
				
				function hasNextStep($step) {
					return $step.nextAll(':not(.ignore)').length;				
				}
				
				function displayButtons() {
					//get current active step
					var $activeStep = getActiveStep();
					//prev Btn
					if(hasPreviousStep($activeStep)) { 
						$prevBtn.show();
					} else {
						$prevBtn.hide();
					}

					//next Btn
					if(hasNextStep($activeStep)) { 
						$nextBtn.show();
					} else {
						$nextBtn.hide();
					}
					
					//finish btn
					if(hasNextStep($activeStep)) { 
						$finishBtn.hide();
					} else {
						$finishBtn.show();
					}
					
					//validate wizard on next step
					if(options.validate && options.validate == 'validate'){
						if(!validateStep($activeStep)) {
							$nextBtn.addClass('disabled');
							$finishBtn.addClass('disabled');
						} else {
							$nextBtn.removeClass('disabled');
							$finishBtn.removeClass('disabled');
						}
					}
				}
				
				function validateStep($step) {
					getStepContent($step);
					var $stepContent = getStepContent($step);
					//no has error input
					if($stepContent.find('.form-group.has-error').length > 0) {
						return false;
					}
					//all required input must be filled
					var $requiredInputs = $stepContent.find('[required]');
					for(var i = 0; i < $requiredInputs.length; i++) {
						//for radio, checkbox required
						if(jQuery($requiredInputs[i]).is(':radio,:checkbox')) {
							//back to form-group parent
							var $radioControl = jQuery($requiredInputs[i]).closest('.form-group');
							if($radioControl.find(':radio:checked,:checkbox:checked').length == 0){
								return false;
							}
						}
						else {
							if(jQuery($requiredInputs[i]).val().length == 0) {
								return false;
							}
						}
					}
					// var $requiredLabels = $stepContent.find('label.required');
					// var $requiredRadio = $stepContent.find('label.required [type="radio"]');
					return true;
				}
				
				function getStepContent($step) {
					var id = $step.find('a[data-htoggle="wizard"]').data('target');
					return jQuery(id);
				}
				function getActiveStep() {
					return $wizard.find('.halo-wizard-steps > .halo-wizard-step-title.active');
				}
				function bindButtons() {
					$nextBtn.on('click', function() {
						var $activeStep = getActiveStep();
						var $nextSteps = $activeStep.nextAll(':not(.ignore)');
						if($nextSteps.length) {
							var $nextStep = jQuery($nextSteps[0]);
							// var $nextStep = $wizard.find('.halo-wizard-steps > .halo-wizard-step-title:eq(' + ($activeStep.index() + 1) + ').halo-wizard-step-title');
							$activeStep.addClass('completed');
							$nextStep.removeClass('disabled');
							$nextStep.find('a').wizard('show');
							// displayButtons();
						}
					});
					$prevBtn.on('click', function() {
						var $activeStep = getActiveStep();
						var $prevSteps = $activeStep.prevAll(':not(.ignore)');
						if($prevSteps.length){
							// var $newStep =  $wizard.find('.halo-wizard-steps > .halo-wizard-step-title:eq(' + ($activeStep.index() - 1) + ').halo-wizard-step-title');
							var $newStep =  jQuery($prevSteps[0]);
							$newStep.find('a').wizard('show');
							// displayButtons();
						}
					});
				}
				function bindSteps() {
					$wizard.find('.halo-wizard-steps > .halo-wizard-step-title > a[data-htoggle="wizard"]').on('shown.bs.wizard',function() {
						displayButtons();
					});
					$wizard.on('keyup', 'input,textarea', halo.util.throttle(function(){
						displayButtons();
					}, 1000));
					$wizard.on('change', ':input,textarea,select', halo.util.throttle(function(){
						displayButtons();
					}, 1000));
					
				}
				//setup action buttons status
				displayButtons();
				
				//bind event to buttons
				bindButtons();
				
				//bind event to steps
				bindSteps();
				
			});
		},
		init: function (scope) {
			// Init multiple select tag
			halo.form.initMultipleSelect(scope);

			//init tree select UI
			jQuery('[data-tree-select] > .halo-tree-select-menu', scope).each(function () {
				var $treeOpts = jQuery(this);
				//assign uniquId for each tree level
				jQuery('.halo-tree-select-menu', $treeOpts).each(function () {
					var uid = halo.util.uniqID();
					jQuery(this).attr('data-level-id', uid);
					jQuery(this).prev('.halo-tree-change-level').attr('data-level-id', uid);
				})
				//set tree level id for this UI
				var uid = halo.util.uniqID();
				$treeOpts.attr('data-level-id', uid);
				//change to the first tree level
				halo.form.changeTreeLevel(uid);

				//hide the current select options
				$treeOpts.removeClass('halo-toggle-display').addClass('halo-tree-select-data');

			});
			//init form tag
			jQuery('.tm-tag-remove', scope).on('click', function () {
				jQuery(this).closest('.tm-tag').remove();
			});
			//init user tag
			halo.form.initUserTag(scope);
			
			//init hash tag
			halo.form.initHashTag(scope);

			//init tm tag
			halo.form.initTMTag(scope);
			if(halo.util.isMobile()){
				//init tm tag
				halo.form.initHammer(scope);
			}
			
			//init wizard
			halo.form.initWizard(scope);
			
			//init auto numeric
			jQuery('.haloj-auto-numeric', scope).autoNumeric('init');
		}
	}
});

/* ============================================================ Online features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	online: {
		list: {}, setList: function (list) {
			halo.online.list = list;
		}, 
		changeUserOnline: function (userId, newStatus, clientType) {
			if (newStatus == 0) {	//offline status
				//thumnail avatar
				jQuery('.halo-user-' + userId + '.thumbnail').removeClass('user-online');
				//chat panel
				jQuery('#conv-contacts .halo-user-' + userId + ' .fa-user').removeClass('onl');
			} else {	//online status
				//thumnail avatar
				jQuery('.halo-user-' + userId + '.thumbnail').addClass('user-online');
				//chat panel
				jQuery('#conv-contacts .halo-user-' + userId + ' .fa-user').addClass('onl');
			}
		}, 
		updateStatus: function (list) {
			halo.online.list = JSON.parse(list);
			//also update offline status
			if (typeof halo.online.list.offline !== 'undefined') {
				var offlineUsers = halo.online.list.offline;
				for (var i = 0; i < offlineUsers.length; i++) {
					var userId = offlineUsers[i];
					halo.online.changeUserOnline(userId, 0);
				}
			}
			if (typeof halo.online.list.online !== 'undefined') {
				var onlineUsers = halo.online.list.online;
				for (var i = 0; i < onlineUsers.length; i++) {
					var user = onlineUsers[i];
					halo.online.changeUserOnline(user.id, 1)
				}
			}


		}, 
		init: function (scope) {
			//init code only update online users
			if (typeof halo.online.list.online !== 'undefined') {
				var onlineUsers = halo.online.list.online;
				for (var i = 0; i < onlineUsers.length; i++) {
					var user = onlineUsers[i];
					halo.online.changeUserOnline(user.id, 1)
				}
			}
		}, 
		updater: function (scope, params) {
			var userId = parseInt(params);
			var newStatus = (userId > 0) ? 1 : 0;
			userId = Math.abs(userId);
			halo.online.changeUserOnline(userId, newStatus);
			halo.online.showOnlineNotif(userId);
		}, 
		showOnlineNotif: function (userId) {
			var $user = jQuery('#conv-contacts .halo-user-' + userId).clone();
			if (!$user.length) return; 		//only show notif if user is valid

			var lastNotif = jQuery('.halo-online-notif').last();
			if (!lastNotif.length) lastNotif = jQuery('[data-halozone="conv.panel"]');
			var pos = lastNotif.position();

			var onlineStatus = jQuery('.fa-user', $user).hasClass('onl');
			var onlineText = 'is ' + (onlineStatus ? 'online' : 'offline');

			if (typeof pos !== 'undefined') {
				var $notif = jQuery('<div class="halo-online-notif"><span> ' + onlineText + '</span></div>')
					.prepend($user).appendTo('body');
				$notif.css('position', 'fixed')
					.css('top', pos.top - 35 + 'px')
					.css('right', '10px');
				//self detroy
				$notif.fadeOut(3000, function () {
					jQuery(this).remove();
				});
			}
		}, 
	}
});

/* ============================================================ Like features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	like: {
		like: function (context, targetId, mode) {
			halo.jax.call("like", "Like", context, targetId, mode);
		},
		unlike: function (context, targetId, mode) {
			halo.jax.call("like", "UnLike", context, targetId, mode);
		},

		dislike: function (context, targetId, mode) {
			halo.jax.call("like", "Dislike", context, targetId, mode);
		},
		undislike: function (context, targetId, mode) {
			halo.jax.call("like", "UnDislike", context, targetId, mode);
		}

	}
});

/* ============================================================ Report features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	report: {
		showReport: function (context, targetId) {
			halo.jax.call("report", "ShowReport", context, targetId);
		}, 
		submitReport: function (context, targetId) {
			var data = halo.util.getFormValues('popupForm');
			halo.jax.call("report", "SubmitReport", data);
		}
	}
});

/* ============================================================ activity features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	activity: {
		refresh: function () {
			var values = halo.util.getFormValues('streamFilters');
			halo.jax.call("stream", "Refresh", values);
		},

		updater: function (scope) {
			if (!jQuery('#streamFilters').length) return;
			var values = halo.util.getFormValues('streamFilters');
			//get the latest activity id
			var latestAct = jQuery('[data-actid]:first');
			var latestId = 0;
			if (latestAct.length) {
				latestId = latestAct.data('actid');
			}
			//get activity list. @rule: only get the first 20 activities to request for update.
			var actList = jQuery('[data-actid]').slice(0, 20);
			var actIds = [];
			actList.each(function () {
				var actId = jQuery(this).data('actid');
				if (typeof actId !== 'undefined') {
					actIds.push(actId);
				}
			})
			values.form["context"] = "stream";
			values.form["latestId"] = latestId;
			values.form["actIds"] = actIds;
			return values;
		}, 
		updateActivity: function (data) {
			//@rule: to update activity content, we just update the comment content of activity, in detail, just insert new comments
			var $newAct = jQuery(data);
			var $oldAct = jQuery('#' + $newAct.attr('id'));
			if ($oldAct.length) {
				//new comment will be insert just before the last halo-comment-input
				var $commentInput = jQuery('.halo-comment-input:last', $oldAct);
				var inputZone = $commentInput.attr('data-halozone');
				var $commentWrapper = jQuery('.halo-stream-comment-wrapper', $oldAct);
				var commentZone = $commentWrapper.attr('data-halozone');
				var $newComments = jQuery('.halo-stream-comment-item', $newAct);
				$newComments.each(function () {
					var $this = jQuery(this);
					//only insert if this is new comment
					if (typeof $this.attr('id') !== 'undefined' && jQuery('#' + $this.attr('id'), $oldAct).length == 0) {
						if (typeof inputZone !== 'undefined') {
							halo.zone.beforeZone($this, inputZone);
							halo.zone.markChanged($this);
						} else if (typeof commentZone !== 'undefined') {
							halo.zone.insertZone($this, commentZone);
							halo.zone.markChanged($this);
						}
					}
				});
			}
		}, 
		insertActivity: function (data) {
			var $data = jQuery(data);
			halo.zone.markChanged($data);
			halo.zone.insertZone($data, 'stream_content', 'first');

		}, 
		lastUpdate: new Date()
		, loadMore: function (ele) {
			var streamContent = jQuery('[data-halozone="stream_content"]');
			var d = new Date();
			if (streamContent.length && (d.getTime() - halo.activity.lastUpdate.getTime()) > 2000) {
				var lastAct = streamContent.children(':last');
				var lastId = lastAct.length ? lastAct.data('actid') : 0;
				//@todo: get the current filter setting
				var filters = halo.util.getFormValues('streamFilters');
				halo.jax.call('stream', 'LoadOlder', lastId, filters);
				halo.activity.lastUpdate = d;
			}
		}
		, autoloadMore: function(e){
			jQuery('.halo-stream-loadmore-btn')
				.addClass('halo-autoclick')
				.trigger('click');
			jQuery(e).hide();
		}
		, init: function (scope) {
			
		}
		, toggleComment: function(ele) {
			var $commentBlock = jQuery(ele).closest('.halo-stream-content');
			$commentBlock.find('.halo-comment-content, .halo-comment-input').removeClass('hidden');			
			var $commentInput = $commentBlock.find('.halo-comment-content>.halo-comment-input .halo-comment-box');
			$commentInput.focus().click();
		}
	}
});

/* ============================================================ autoclick features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	autoclick: {
		inited: false,
		autoclick: function (scope) {
			jQuery('.halo-autoclick',scope).each(function () {
				var $btn = jQuery(this);
				var delta = $btn.prop('data-delta');
				if (typeof delta == 'undefined') {
					delta = 0;
				}
				if (halo.util.isElementInViewport($btn, delta)) {
					$btn.trigger('click');
				}
			});
		},
		init: function (scope) {
			if (!halo.autoclick.inited) {
				//$scrolls = jQuery(':scrollable');
				jQuery(document).on('scroll', halo.util.throttle(function () {
						halo.autoclick.autoclick(jQuery('html'));
					}, 100));
				/*	
				$scrolls.each(function () {
					var $scroll = jQuery(this);
					if ($scroll.is('html')) $scroll = jQuery(document);
					$scroll.on('scroll', halo.util.throttle(function () {
						halo.autoclick.autoclick(jQuery('html'));
					}, 100));
				})
				*/
				halo.autoclick.inited = true;
			}
			jQuery('.haloj-scrollable',scope).on('scroll', halo.util.throttle(function () {
					halo.autoclick.autoclick(scope);
				}, 100));
			halo.autoclick.autoclick(scope);
		}
	}
});

/* ============================================================ lazy load images features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	lazyload: {
		inited: false,
		loadAll: function(scope){
			halo.lazyload.loadImages(scope);
			halo.lazyload.loadUploadBtn(scope);
			halo.lazyload.loadSelectInput(scope);
			halo.lazyload.loadTreeSelect(scope);
			halo.lazyload.loadEllipsis(scope);
			halo.lazyload.loadSliders(scope);
		},
		loadImages: function (scope) {
			jQuery('img[data-src]',scope).each(function () {
				var $img = jQuery(this);
				if (!$img.data('lazyloadedImage') && halo.util.isElementInViewport($img, 400)) {
					var src = $img.attr('data-src');
					if(src){
						$img.removeProp('data-src');
						$img.prop('src', src);
						$img.data('lazyloadedImage',true);
					}
				}
			});
		},
		loadSliders: function (scope) {
			jQuery('.halo-slider',scope).each(function () {
				var $slider = jQuery(this);
				if (!$slider.data('lazyloadedSlider') && halo.util.isElementInViewport($slider, 400)) {
					var slickOptions = $slider.attr('data-options');
					if(slickOptions) {
						slickOptions = jQuery.parseJSON( slickOptions );
						$slider.slick(slickOptions);
					}
					$slider.data('lazyloadedSlider',true);
				}
			});
		},
		loadUploadBtn: function(scope){
			jQuery('.haloj-uploader-btn',scope).each(function () {
				var $btn = jQuery(this);
				if (!$btn.data('lazyloadedUploader') && halo.util.isElementInViewport($btn, 0)) {
					var uid = $btn.attr('data-upload-uid');
					if(uid){
						var holder = jQuery('[data-upload-holder-uid="'+uid+'"]');
						if(holder.length){
							var uploaderId = uid;
							jQuery(this).attr('id', 'plupload-browse-button-' + uploaderId);
							holder.attr('id', 'halo-plupload-upload-ui-' + uploaderId).addClass('halo-uploader-holder');
							var options = jQuery.extend({photoWidth: 0, photoHeight: 0, uploaderId: uploaderId}, halo.uploader.singlePreviewOption);
							var uploader = halo.uploader.uploader_init(jQuery, holder, options);
						}
					}
					$btn.data('lazyloadedUploader',true);
				}
			});
			
		},
		loadSelectInput: function (scope) {
			jQuery('.haloj-selectpicker', scope).each(function () {
				var $input = jQuery(this);
				if (!$input.data('lazyloadedSelectInput') && halo.util.isElementInViewport($input, 0)) {
					$input.selectpicker();
					$input.data('lazyloadedSelectInput',true);
				}
			})
		},
		loadTreeSelect: function (scope){
			//init the selected state for tree select
			jQuery('[data-tree-select]', scope).each(function () {
				//get current input values
				var $that = jQuery(this);
				if (!$that.data('lazyloadedTreeSelect') && halo.util.isElementInViewport($that, 0)) {
					jQuery('input', $that).each(function () {
						var input = jQuery(this);
						jQuery('[data-halo-value="' + input.val() + '"][data-halo-input="' + input.attr('name') + '"]').each(function () {
							halo.form.changeTreeSelectOption(this, false);
						})
					});
					$that.data('lazyloadedTreeSelect',true);
				}
			});		
		},
		loadEllipsis: function (scope) {
			function adjustHeight($ele, readMore){

				var elemenNode = 1;
				var textNode = 3;
				var size = jQuery($ele).data('height') | 100;
				var baseTop = $ele.offset().top;

				function wrapNodes($parent) {
					var $contents = $parent.contents();
					if($contents.length > 1) {
						$contents.each(function(){
							if (this.nodeType === elemenNode) {
							
							} else if (this.nodeType === textNode && this.nodeValue.trim() !== '') {
								jQuery(this).replaceWith(jQuery('<span>' + this.nodeValue + '</span>'));
							}
						});
					}
				}
				function findEllipsisElement(elements, size) {
					for(var i = 0; i < elements.length; i++){
						var $this = jQuery(elements[i]);
						var offset = $this.offset();
						var height = $this.height();
						var top = offset.top - baseTop;
						if( top < size && top + height > size) {	
							return $this;
						}
					};
					return null;
				}
								
				var $wrapper = jQuery($ele);
				var $children
				var $found;
				wrapNodes($wrapper);
				$children = $wrapper.children();
				var limit = 10;
				do	{
					//wrap Nodes
					$found = findEllipsisElement($children, size);
					if($found) {
						wrapNodes($found);
						$children = $found.children();
					}
					limit --;
				} while ($found && $children.length > 1 && limit > 0);
				//adjust max-height depends on $found node
				if($found){
					if($found[0].nodeType === textNode) {
						var lineHeight = parseFloat($found.css('line-height'));
						var nLines = Math.floor($found.height()/lineHeight);
						var top = $found.offset().top  - baseTop;
						var maxHeight = top + Math.ceil(nLines * lineHeight);
					} else {
						var maxHeight = $found.offset().top  - baseTop;
					}
					//configure maxHeight
					jQuery($ele).css('max-height', maxHeight)
								.data('height', maxHeight);					
				}

			}
			jQuery('.haloj-ellipsis.haloj-truncated', scope).each(function () {
				//get current input values
				var $ele = jQuery(this);
				var readMore = $ele.next().find('.haloj-readmore-btn');
				if (!$ele.data('lazyloadedEllipsis') && readMore.length && halo.util.isElementInViewport($ele, 200)) {
					$ele.css('position', 'relative');
					adjustHeight($ele,readMore);

					$ele.data('lazyloadedEllipsis',true);
				}
			});
			
		},
		loadLocations: function() {
			jQuery('[data-halo-map-dropdown]').each(function() {
				var $that = jQuery(this);
				if (halo.util.isElementInViewport($that, 400) && !Boolean($that.data('halo-loc-distance'))) {
					halo.location.loadScript(function () {
						var curPosition = halo.location.getCurrentPosition();
						var __loadLocCb = function() {
							if (curPosition) {
								var disPosition = new google.maps.LatLng($that.data('halo-loc-lat'), $that.data('halo-loc-lng'));
								var directionsService = new google.maps.DirectionsService();
								var request = {
									origin: curPosition,
									destination: disPosition,
									travelMode: google.maps.TravelMode.DRIVING
								};
								directionsService.route(request, function (response, status) {
									if (status == google.maps.DirectionsStatus.OK) {
										var legs = response.routes[0].legs;
										var totalDistance = 0;
										for (var i = 0; i < legs.length; ++i) {
											totalDistance += legs[i].distance.value;
										}
										totalDistance = totalDistance/1000;
										totalDistance = totalDistance < 0.5 ? totalDistance : Math.round(totalDistance);
										$that.data('halo-loc-distance', totalDistance);
										$that.data('halo-loc-directions', response);
										if (totalDistance < 0.5) {
											$that.html($that.data('halo-loc-name') + ' (' + __halotext('nearly you') + ')');
										} else {
											$that.html($that.data('halo-loc-name') + ' (' + totalDistance + 'km)');
										}
									} else {
										if (status == google.maps.DirectionsStatus.OVER_QUERY_LIMIT) {
											setTimeout("__loadLocCb", halo.location.delay);
										} else {
											//TODO
										}
									}
								});
							}
						};
						if (curPosition == null) {
							if (halo.location.isDetectable()) {
								halo.location.autodetect({}, function() {
									curPosition = halo.location.getCurrentPosition();
									__loadLocCb();
								});
							}
						} else {
							__loadLocCb();
						}
					})
				}
			});
		},
		init: function (scope) {
			if (!halo.lazyload.inited) {
				jQuery(document).on('scroll', halo.util.throttle(function () {
						halo.lazyload.loadAll(jQuery('html'));
					}, 100));
				/*
				$scrolls = jQuery(':scrollable');
				$scrolls.each(function () {
					var $scroll = jQuery(this);
					if ($scroll.is('html')) $scroll = jQuery(document);
					$scroll.on('scroll', halo.util.throttle(function () {
						halo.lazyload.loadImages(jQuery('html'));
						//halo.lazyload.loadLocations();
					}, 100));
				})
				*/
				
				jQuery(document).on('shown.bs.htab',function(e){
					halo.lazyload.loadAll(jQuery('html'));
					if(jQuery('#post_photo').length) {
						halo.photo.init(jQuery('#post_photo'));
					}
				});
				jQuery(document).on('shown.display.halo',function(e){
					halo.lazyload.loadAll(jQuery('html'));
				});
				//custom event for lazyload trigger
				jQuery(document).on('lazyloading.halo',function(e){
					halo.lazyload.loadAll(jQuery('html'));
				});
				halo.lazyload.inited = true;
			}
			jQuery('.haloj-scrollable',scope).on('scroll', halo.util.throttle(function () {
					halo.lazyload.loadAll(scope);
				}, 100));
			//lazyload with some delay.
			halo.util.throttle(function () {
					halo.lazyload.loadAll(scope);
				}, 100).apply();
		}
	}
});

/* ============================================================ Uploader features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	uploader: {
		uploading: false,
		options: {},
		//uploader options to show the upload progress bar only
		progressOnlyOption: {
			multi_selection:false,
			fnUploadFile: function (up, file) {

			}, 
			fnFileAdded: function (up, files) {
				var hundredmb = 100 * 1024 * 1024, max = parseInt(up.settings.max_file_size, 10);

				jQuery('#media-upload-error').html('');

				halo.uploader.uploadStart();

				plupload.each(files, function (file) {
					if (max > hundredmb && file.size > hundredmb && up.runtime != 'html5')
						halo.uploader.uploadSizeError(up, file, true);
					else {
						halo.uploader.fileQueued(up, file);
						var cancelBtn = jQuery('<div class="halo-upload-cancle-btn halo-upload-action-btn">')
						cancelBtn.append(jQuery('<button type="button" class="halo-btn halo-btn-xs" title="Cancel"><i class="fa fa-minus"></button>')
							.on('click', function () {
								up.removeFile(file);
								jQuery(this).closest('#media-item-' + file.id).remove();
							}));
						var newMediaContent = jQuery('<div class="media-item-wrapper" id="media-item-' + file.id + '">')
							.append('<div class="progress progress-striped active halo-upload-progress">'
								+ '<div class="halo-upload-percent progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"> <span class="sr-only"></span> </div>'
								+ '</div>')
							.append(cancelBtn);
						jQuery(up.el).append(newMediaContent);
					}
				});

				up.refresh();
				up.start();
			}, 
			fnFileUploaded: function (up, file, response) {
				response = jQuery.parseJSON(response.response);

				var item = jQuery('#media-item-' + file.id);
				//check for error message
				if (typeof response.error !== 'undefined') {
					//error response
					var msg = response.error.message;
					alert(msg);
				}
				item.remove();
				//trigger on upload success
				jQuery(up.el).trigger('uploader.uploadSuccess', [response]);
			}
		},
		singlePreviewOption: {
			multiple_queues: false,
			max_file_count: 1,
			multi_selection:false,
			fnUploadFile: function (up, file) {

			}, 
			fnFileAdded: function (up, files) {
				var hundredmb = 100 * 1024 * 1024, max = parseInt(up.settings.max_file_size, 10);
				//single file upload, remove previously added file
				jQuery('.media-item-wrapper',up.el).remove();
				jQuery('#media-upload-error').html('');

				halo.uploader.uploadStart();

				plupload.each(files, function (file) {
					if (max > hundredmb && file.size > hundredmb && up.runtime != 'html5')
						halo.uploader.uploadSizeError(up, file, true);
					else {
						halo.uploader.fileQueued(up, file);
						var cancelBtn = jQuery('<div class="halo-upload-cancle-btn halo-upload-action-btn">')
						cancelBtn.append(jQuery('<button type="button" class="halo-btn halo-btn-xs" title="Cancel"><i class="fa fa-minus"></button>')
							.on('click', function () {
								up.removeFile(file);
								var wrapper = jQuery(this).closest('#media-item-' + file.id);
								if(wrapper.length){
									wrapper.parent().find('[name="photo_id"]').remove();
									wrapper.remove();
								}
							}));
						var preview = jQuery('<div class="halo-photo-upload-wrap">')
									.append('<div class="halo-upload-preview"></div>')
									.append('<div class="progress progress-striped active halo-upload-progress">'
										+ '<div class="halo-upload-percent progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"> <span class="sr-only"></span> </div>'
										+ '</div>')
									.append(cancelBtn);
						var newMediaContent = jQuery('<div class="media-item-wrapper" id="media-item-' + file.id + '">')
							.append(preview);
						jQuery(up.el).append(newMediaContent);
					}
				});

				up.refresh();
				up.start();
			}, 
			fnFileUploaded: function (up, file, response) {
				response = jQuery.parseJSON(response.response);

				var item = jQuery('#media-item-' + file.id);
				//check for error message
				if (typeof response.error !== 'undefined') {
					//error response
					var msg = response.error.message;
					alert(msg);
				}
				item.find('.halo-upload-progress').remove();
				item.find('.halo-upload-preview').append('<img class="" src="'+response.image+'">');
				//trigger on upload success
				jQuery(up.el).trigger('uploader.uploadSuccess', [response]);
			}
		},
		getConfig: function (option) {
			var defaultOption = {
				uploaderId: 'halo-uploader',
				photoWidth: 60,
				photoHeight: 60,
				inputName: 'media_id',
				chunkSize: '1mb',
				maxFileSize: '1000mb',
				mediaType: 'photo',
				allowedExtensions: "jpg,gif,png",
				fnInit: function (up, uploaderEl) {
					jQuery('.moxie-shim', uploaderEl).addClass('col-md-2 col-xs-4').width('');

				}, fnFileAdded: function (up, files) {
					var hundredmb = 100 * 1024 * 1024, max = parseInt(up.settings.max_file_size, 10);

					jQuery('#media-upload-error').html('');

					halo.uploader.uploadStart();

					plupload.each(files, function (file) {
						if (max > hundredmb && file.size > hundredmb && up.runtime != 'html5')
							halo.uploader.uploadSizeError(up, file, true);
						else {
						    halo.uploader.fileQueued(up, file);
                            up.trigger('beforeUploadCb.halo');
						}
					});
					up.refresh();
					up.start();
				}, fnUploadFile: function (up, file) {
					halo.uploader.fileUploading(up, file);
				}, fnUploadProgress: function (up, file) {
					halo.uploader.uploadProgress(up, file);
				}, fnError: function (up, err) {
					halo.uploader.uploadError(err.file, err.code, err.message, up);
					up.refresh();
				}, fnFileUploaded: function (up, file, response) {
					halo.uploader.uploadSuccess(up, file, response.response);
				}, fnUploadComplete: function (up, files) {
					halo.uploader.uploadComplete(up, files);
				}
			}
			option = jQuery.extend({}, defaultOption, option);
			var gbUploaderInit = {
				// General settings
				runtimes: 'html5,flash,silverlight,html4',

				browse_button: 'plupload-browse-button-' + option.uploaderId,

				//container: 'halo-plupload-upload-ui-' + option.uploaderId,

				drop_element: 'drag-drop-area-' + option.uploaderId,

				file_data_name: 'async-upload',

				multiple_queues: 1,

				max_file_size: option.maxFileSize,

				// Fake server response here
				// url : '../upload.php',
				url: halo_jax_targetUrl,
				multipart_params: {
					"com": "media",
					"photowidth": option.photoWidth,
					"photoheight": option.photoHeight,
					"mediaType": option.mediaType,
					"func": "upload",
					"csrf_token": jQuery('meta[name="csrf_token"]').attr('content')
				},
				// Maximum file size

				// User can upload no more then 20 files in one go (sets multiple_queues to false)
				max_file_count: 20,

				chunk_size: option.chunkSize,

				// Resize images on clientside if we can
				resize: {
					width: 1920,
					height: 1080,
					quality: 100,
					crop: false // do not crop image
				},

				// Specify what files to browse for
				filters: {
					mime_types: [
						{ title: "Image files", extensions: "jpg,gif,png" },
						{ title: "Zip files", extensions: "zip,avi" },
						{ title: "Allowed Files", extensions: "*" }
					],
					prevent_duplicates: false
				},

				// Rename files by clicking on their titles
				rename: true,

				// Sort files
				sortable: true,

				// Enable ability to drag'n'drop files onto the widget (currently only HTML5 supports that)
				dragdrop: true,

				// Views to activate
				views: {
					list: true,
					thumbs: true, // Show thumbs
					active: 'thumbs'
				},

				multipart: 1,
				urlstream_upload: 1,

				//gb upload params
				showTitle: true,

				// Flash settings
				flash_swf_url: 'http://rawgithub.com/moxiecode/moxie/master/bin/flash/Moxie.cdn.swf',

				// Silverlight settings
				silverlight_xap_url: 'http://rawgithub.com/moxiecode/moxie/master/bin/silverlight/Moxie.cdn.xap'
			};
			gbUploaderInit = jQuery.extend({}, gbUploaderInit, option);
			this.options = gbUploaderInit;
			return this.options;
		},
		uploadStart: function () {
			//triger on file uploading start
			return true;
		},
		uploadSizeError: function (up, file, over100mb) {
			var message;

			if (over100mb)
				message = pluploadL10n.big_upload_queued.replace('%s', file.name) + ' ' + pluploadL10n.big_upload_failed.replace('%1$s', '<a class="uploader-html" href="#">').replace('%2$s', '</a>');
			else
				message = pluploadL10n.file_exceeds_size_limit.replace('%s', file.name);

			jQuery('#media-items').append('<div id="media-item-' + file.id + '" class="media-item error"><p>' + message + '</p></div>');
			up.removeFile(file);
		},
		uploadProgress: function (up, file) {
			var item = jQuery('#media-item-' + file.id);

			//jQuery('.bar', item).width( (200 * file.loaded) / file.size );
			jQuery('.halo-upload-percent', item).css('width', file.percent + '%')
				.attr('aria-valuenow', '' + file.percent);
		},
		uploadError: function (fileObj, errorCode, message, uploader) {
			var hundredmb = 100 * 1024 * 1024, max;
			switch (errorCode) {
				case plupload.FAILED:
					halo.uploader.FileError(fileObj, pluploadL10n.upload_failed);
					break;
				case plupload.FILE_EXTENSION_ERROR:
					halo.uploader.FileError(fileObj, pluploadL10n.invalid_filetype);
					break;
				case plupload.FILE_SIZE_ERROR:
					uploadSizeError(uploader, fileObj);
					break;
				case plupload.IMAGE_FORMAT_ERROR:
					halo.uploader.FileError(fileObj, pluploadL10n.not_an_image);
					break;
				case plupload.IMAGE_MEMORY_ERROR:
					halo.uploader.FileError(fileObj, pluploadL10n.image_memory_exceeded);
					break;
				case plupload.IMAGE_DIMENSIONS_ERROR:
					halo.uploader.FileError(fileObj, pluploadL10n.image_dimensions_exceeded);
					break;
				case plupload.GENERIC_ERROR:
					halo.uploader.QueueError(pluploadL10n.upload_failed);
					break;
				case plupload.IO_ERROR:
					max = parseInt(uploader.settings.max_file_size, 10);

					if (max > hundredmb && fileObj.size > hundredmb)
						halo.uploader.FileError(fileObj, pluploadL10n.big_upload_failed.replace('%1$s', '<a class="uploader-html" href="#">').replace('%2$s', '</a>'));
					else
						halo.uploader.QueueError(pluploadL10n.io_error);
					break;
				case plupload.HTTP_ERROR:
					halo.uploader.QueueError(pluploadL10n.http_error);
					break;
				case plupload.INIT_ERROR:
					jQuery('.media-upload-form').addClass('html-uploader');
					break;
				case plupload.SECURITY_ERROR:
					halo.uploader.QueueError(pluploadL10n.security_error);
					break;
				/*		case plupload.UPLOAD_ERROR.UPLOAD_STOPPED:
				 case plupload.UPLOAD_ERROR.FILE_CANCELLED:
				 jQuery('#media-item-' + fileObj.id).remove();
				 break;*/
				default:
					halo.uploader.FileError(fileObj, pluploadL10n.default_error);
			}
		},

		QueueError: function (message) {
			alert(message);
			jQuery('#media-upload-error').show().html('<div class="error"><p>' + message + '</p></div>');
		},

		// file-specific error messages
		FileError: function (fileObj, message) {
			halo.uploader.itemAjaxError(fileObj.id, message);
		},

		itemAjaxError: function (id, message) {
			var item = jQuery('#media-item-' + id), filename = item.find('.filename').text(), last_err = item.data('last-err');

			if (last_err == id) // prevent firing an error for the same file twice
				return;

			item.html('<div class="error-div">' +
				'<a class="dismiss" href="#">' + pluploadL10n.dismiss + '</a>' +
				'<strong>' + pluploadL10n.error_uploading.replace('%s', jQuery.trim(filename)) + '</strong> ' +
				message +
				'</div>').data('last-err', id);
		},

		fileUploading: function (up, file) {
			var hundredmb = 100 * 1024 * 1024, max = parseInt(up.settings.max_file_size, 10);

			if (max > hundredmb && file.size > hundredmb) {
				setTimeout(function () {

					if (file.status < 3 && file.loaded === 0) { // not uploading
						wpFileError(file, pluploadL10n.big_upload_failed.replace('%1$s', '<a class="uploader-html" href="#">').replace('%2$s', '</a>'));
						up.stop(); // stops the whole queue
						up.removeFile(file);
						up.start(); // restart the queue
					}
				}, 10000); // wait for 10 sec. for the file to start uploading
			}
		},
		// progress and success handlers for media multi uploads
		fileQueued: function (up, fileObj) {
			var uploaderId = up.getOption('uploaderId');
			// Get rid of unused form
			jQuery('.media-blank').remove();

			var items = jQuery('#media-items-' + uploaderId);

			// Create a progress bar containing the filename
			var newMedia = jQuery('.halo-media-items', items).children().last().clone();
			newMedia.attr('id', 'media-item-' + fileObj.id)
				.addClass('child-of-1')
				.attr('data-halo-photo-preview', '');
			var cancelBtn = jQuery('<div class="halo-upload-cancle-btn halo-upload-action-btn">')
			cancelBtn.append(jQuery('<button type="button" class="halo-btn halo-btn-xs" title="Cancel"><i class="fa fa-minus"></button>')
				.on('click', function () {
					up.removeFile(fileObj);
					jQuery(this).closest('.halo-media-item').remove();
				}));
			var newMediaContent = jQuery('<div class="halo-media-item-wrapper halo-in-square">')
				.append('<div class="halo-upload-preview">')
				.append(cancelBtn)
				.append('<div class="progress progress-striped active halo-upload-progress">'
					+ '<div class="halo-upload-percent progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"> <span class="sr-only"></span> </div>'
					+ '</div>');
			var showTitle = up.getOption('showTitle');
			if (showTitle) {
				newMediaContent.append(jQuery('<div class="halo-upload-filename original">').text(' ' + fileObj.name));
			}
			newMedia.html(newMediaContent);
			newMediaContent.before('<div class="halo-dummy-square">');
			//append to the last photo preview or insert to the wrapper
			var lastMedia = up.el.find('[data-halo-photo-preview]').last();
			if (lastMedia.length == 0) {
				//append to wrapper
				var mediaWrapper = up.el.find('.halo-media-items');
				newMedia.prependTo(mediaWrapper);
			} else {
				if (up.getOption('multipleUpload') == false) {
					lastMedia.replaceWith(newMedia);
				} else {
					lastMedia.after(newMedia);
				}
			}

			// Disable submit
			halo.uploader.uploading = true;
		},

		uploadComplete: function (up, files) {
			halo.uploader.uploading = false;
			up.trigger('uploadCompleteCb.halo');
		},

		uploadSuccess: function (up, file, response) {
			response = jQuery.parseJSON(response);
			var item = jQuery('#media-item-' + file.id);
			var uploaderId = up.getOption('uploaderId');
			//check for error message
			if (typeof response.error !== 'undefined') {
				//error response
				var msg = response.error.message;
				alert(msg);
				item.remove();
			}

			//remove the progress bar
			jQuery('.halo-upload-progress', item).remove();
			//remove cancel button
			jQuery('.halo-upload-cancle-btn', item).remove();
			var removeBtn = jQuery('<div class="halo-upload-remove-btn halo-upload-action-btn">')
			removeBtn.append(jQuery('<button type="button" class="halo-btn halo-btn-xs" title="Remove"><i class="fa fa-times"></button>')
				.on('click', function () {
					item.remove();
				}));

			//update the preview picture
			jQuery('.halo-upload-preview', item)
				.append(jQuery('<img class="halo-upload-preview-img img-thumbnail img-responsive halo-center-block">').attr('src', response.image))
				.after(removeBtn);

			//extra step to clean all empty value input
			jQuery('[name="' + up.getOption('inputName') + '[]"]').each(function () {
				if (jQuery(this).val() == '') {
					jQuery(this).remove();
				}
			});
			//store the file remote id to media_id input file
			item.append('<input class="mediaInput" type="hidden" data-resetable name="' + up.getOption('inputName') + '[]" value="' + response.id + '">');

		},
		beforeUploadCb: function() {
		    if (jQuery('.halo-status-function-btn').length) {
                jQuery('.halo-status-function-btn').attr('disabled', 'disabled');
            }
		},
        uploadCompleteCb: function() {
            if (jQuery('.halo-status-function-btn').length) {
                jQuery('.halo-status-function-btn').removeAttr('disabled');
            }
            //TODO
        },
		uploader_init: function ($, el, option) {
			var gbUploaderInit = halo.uploader.getConfig(option);
			var uploader = new plupload.Uploader(gbUploaderInit);

			uploader.el = jQuery(el);

			uploader.bind('Init', function (up) {
				gbUploaderInit.fnInit(up, uploader.el);
			});

			uploader.init();

			uploader.bind('FilesAdded', gbUploaderInit.fnFileAdded);

			// uploader.bind('BeforeUpload', function(up, file) {});

			uploader.bind('UploadFile', gbUploaderInit.fnUploadFile);

			uploader.bind('UploadProgress', gbUploaderInit.fnUploadProgress);

			uploader.bind('Error', gbUploaderInit.fnError);

			uploader.bind('FileUploaded', gbUploaderInit.fnFileUploaded);

			uploader.bind('UploadComplete', gbUploaderInit.fnUploadComplete);

			// Custom events
			uploader.bind('uploadCompleteCb.halo', halo.uploader.uploadCompleteCb);
            uploader.bind('beforeUploadCb.halo', halo.uploader.beforeUploadCb);

			//add filed defined via data-media-value
			var media = uploader.getOption('mediaValue');
			if (typeof media !== 'undefined' && jQuery.isArray(media)) {
				jQuery.each(media, function (key, value) {
					halo.uploader.addMedia(uploader, value);
				});
			}
			jQuery(el).data('uploader',uploader);
			return uploader;
		},
		addMedia: function (up, media) {
			var items = jQuery('#media-items-' + up.getOption('uploaderId'));

			// Create a progress bar containing the filename
			var newMedia = jQuery('.halo-media-items', items).children().last().clone();
			newMedia.attr('id', 'media-item-' + media.id)
				.addClass('child-of-1')
				.attr('data-halo-photo-preview', '');
			var removeBtn = jQuery('<div class="halo-upload-remove-btn halo-upload-action-btn">')
			removeBtn.append(jQuery('<button type="button" class="halo-btn halo-btn-sx" title="Remove"><i class="fa fa-times"></button>')
				.on('click', function () {
					newMedia.remove();
				}));
			var newMediaContent = jQuery('<div class="halo-media-item-wrapper halo-in-square">')
				.append(jQuery('<div class="halo-upload-preview">')
					.append(jQuery('<img class="halo-upload-preview-img img-thumbnail img-responsive halo-center-block">').attr('src', media.image))
				)
				.append(removeBtn);
			var showTitle = up.getOption('showTitle');
			if (showTitle) {
				newMediaContent.append(jQuery('<div class="halo-upload-filename original">').text(' ' + media.name));
			}
			newMediaContent.append('<input type="hidden" data-resetable name="' + up.getOption('inputName') + '[]" value="' + media.id + '">');
			newMedia.html(newMediaContent);
			newMediaContent.before('<div class="halo-dummy-square">');
			//append to the last photo preview or insert to the wrapper
			var lastMedia = up.el.find('[data-halo-photo-preview]').last();
			if (lastMedia.length == 0) {
				//append to wrapper
				var mediaWrapper = up.el.find('.halo-media-items');
				newMedia.prependTo(mediaWrapper);
			} else {
				if (up.getOption('multipleUpload') == false) {
					lastMedia.replaceWith(newMedia);
				} else {
					lastMedia.after(newMedia);
				}
			}

		},
		initUploaders: function (el, inOptions) {
			var wrapper = jQuery(el).find('.halo-uploader-wrapper');
			if (typeof inOptions === 'undefined') {
				inOptions = {};
			}
			if (typeof inOptions === 'string') {
				inOptions = jQuery.parseJSON(inOptions);
			}
			if (wrapper.length > 0) {
				wrapper.each(function () {
					var data = jQuery(this).data();
					var options = jQuery.extend(inOptions, data);
					halo.uploader.uploader_init(jQuery, this, options);
				})
			}

		},

		init: function (scope) {
			//init uploader if it is loaded
			halo.uploader.initUploaders(scope);
		}
	}
});

/* ============================================================ uploader translation

 */
var pluploadL10n = {
	'queue_limit_exceeded': __halotext('You have attempted to queue too many files.'),
	'file_exceeds_size_limit': __halotext('%s exceeds the maximum upload size for this site.'),
	'zero_byte_file': __halotext('This file is empty. Please try another.'),
	'invalid_filetype': __halotext('This file type is not allowed. Please try another.'),
	'not_an_image': __halotext('This file is not an image. Please try another.'),
	'image_memory_exceeded': __halotext('Memory limit exceeded! Please try a smaller file.'),
	'image_dimensions_exceeded': __halotext('This is larger than the maximum size. Please try another.'),
	'default_error': __halotext('An error occurred in the upload. Please try again later.'),
	'missing_upload_url': __halotext('There was a configuration error. Please contact the server administrator.'),
	'upload_limit_exceeded': __halotext('You may only upload 1 file.'),
	'http_error': __halotext('HTTP error.'),
	'upload_failed': __halotext('Upload failed.'),
	'big_upload_failed': __halotext('Please try uploading this file with the %1$sbrowser uploader%2$s.'),
	'big_upload_queued': __halotext('%s exceeds the maximum upload size for the multi-file uploader when used in your browser.'),
	'io_error': __halotext('IO error.'),
	'security_error': __halotext('Security error.'),
	'file_cancelled': __halotext('File canceled.'),
	'upload_stopped': __halotext('Upload stopped.'),
	'dismiss': __halotext('Dismiss'),
	'crunching': __halotext('Crunching&hellip;'),
	'deleted': __halotext('moved to the trash.'),
	'error_uploading': __halotext('&#8220;%s&#8221; has failed to upload.')
};

/* ============================================================ template features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	template: {
		clearInputError: function (input) {
			//input.siblings('[data-halo-error]').remove();
			//clear input error by make it as success input
			var refer = input.attr('data-rule-feedback');
			if (typeof refer !== 'undefined') {
				jQuery('[data-feedback="' + refer + '"]').tooltip('destroy');
			} else {
				if (input.closest('.form-group').hasClass('has-feedback')) {
					halo.template.setInputSuccess(input);
					return;
				}
			}
			/*
			 input.closest('div.form-group').removeClass('has-feedback')
			 .removeClass('has-error');
			 */
		},

		setInputError: function (input, errors) {
			//convert errors array
			if (!(errors instanceof Array)) {
				errors = [errors];
			}
			var errMsg = '';
			for (var i = 0; i < errors.length; i++) {
				//append the error after the input field
				var errMsg = errMsg + errors[i] + '\n';
				//input.after(jQuery('<span class="help-block" data-halo-error>'+errors[i]+'</span>'));
			}
			//check if the error message for profiel field
			if (errMsg.indexOf('field.') > -1) {
				var inputName = jQuery(input).attr('name');

				var inputNameDot = halo.util.convertKeyArr2KeyDot(inputName);
				//search for input label
				var inputLabel = jQuery('[for="' + inputName + '"]').text();
				if (inputLabel.length) {
					var re = new RegExp(inputNameDot, 'g');
					errMsg = errMsg.replace(re, "'" + inputLabel + "'");
				}

			}
			/*
			 input.closest('div.form-group').addClass('has-feedback')
			 .addClass('has-error');
			 */
			var container = input;
			var refer = input.attr('data-rule-feedback');
			if (typeof refer !== 'undefined') {
				container = jQuery('[data-feedback="' + refer + '"]');
			} else {
				input.closest('.form-group').removeClass('has-success has-warning has-error');
				input.closest('.form-group').addClass('has-error has-feedback');

				//input.nextAll('.form-control-feedback').remove();
				//input.after(jQuery('<span class="fa fa-times form-control-feedback"></span>'));
			}
			// container.tooltip('destroy').tooltip(halo.template.getValidationTooltipOptions(input, errMsg)).tooltip('show');

			container.tooltip(halo.template.getValidationTooltipOptions(input, errMsg)).tooltip('show');
			//triger event on error container
			jQuery(document).trigger('inputerror.halo', [container, errors]);
		}, 
		setInputSuccess: function (input) {
			var container = input;
			var refer = input.attr('data-rule-feedback');
			if (typeof refer !== 'undefined') {
				container = jQuery('[data-feedback="' + refer + '"]');
			} else {
				input.closest('.form-group').removeClass('has-success has-warning has-error');
				input.closest('.form-group').addClass('has-success has-feedback');

				//input.nextAll('.form-control-feedback').remove();
				//input.after(jQuery('<span class="fa fa-check form-control-feedback"></span>'));
			}
			container.tooltip('destroy');
		}, 
		getValidationTooltipOptions: function (element, message) {
			var $e = jQuery(element);
			var options = {
				/* Using Twitter Bootstrap Defaults if no settings are given */
				animation: $e.data('animation') || true,
				html: $e.data('html') || false,
				placement: $e.data('placement') || 'top',
				selector: $e.data('animation') || true,
				title: $e.attr('title') || message,
				trigger: jQuery.trim('manual ' + ($e.data('trigger') || '')),
				delay: $e.data('delay') || 0,
				container: $e.data('container') || false
			};
			return options;
		}, 
		init: function (scope) {
			/*
			 if (scope instanceof HTMLDocument){
			 //skip init on document scope, it should be inited by css framework
			 } else {
			 //1.init core js template
			 }
			 */

			//init special class
			//auto size
			jQuery('.haloTextAreaAutoSzie', scope).each(function () {
				jQuery(this).autosize();
			});

			//init jsontable
			jQuery('.table-halo-json-editable', scope).each(function () {
				jQuery('.table-halo-json-editable').jsontable();
			});

			//init switch
			jQuery('.halo-switch-input', scope).each(function () {
				jQuery('.halo-switch-input').bootstrapSwitch();
			});

			//init slider
			jQuery('.halo-slider-input', scope).each(function () {
				jQuery('.halo-slider-input').slider();
			});

			//init tooltip
			jQuery('.halo-tooltip', scope).tooltip();

			//init group check
			jQuery(scope).groupcheck();

			//init stacked list
			jQuery(scope).haloStackedList();

			//force validation to validate hidden fields
			jQuery.validator.setDefaults({
				ignore: [],
				// any other default options and/or rules
			});

			//help block init
			jQuery('.halo-form-helpblock', scope).tooltip({html: true, trigger: 'click focus'});
			
			//styling for empty child div
			jQuery('.halo-user-response-actions:not(:has(li))',scope).hide();
			
			//init countdown
			halo.util.countdown(scope);
			
			//hide hidden filter
			jQuery('.halo-filter-panel', scope).each(function() {
				if(jQuery(this).find('input:not([type="hidden"])').length == 0) {
					jQuery(this).hide();
				}
			});
			
			//group info list
			halo.template.listEllipsis(scope);
		},

		listEllipsis: function(scope) {
			var $lists = jQuery('.haloj-list-ellipsis-more .haloc-list', scope);
			var maxItem = 4;
			$lists.each(function() {
				var $list = jQuery(this);
				var $children = $list.children();
				if($children.length > maxItem) {
					var $dropdown = jQuery('<li class="halo-dropdown col-xs-6"></li>').css('padding', '0px');
					var $dropdownTitle = jQuery('<a href="#" class="halo-dropdown-toggle" data-htoggle="dropdown" role="button">' + ($children.length - maxItem + 1) + ' More <span class="caret"></span></a>');
					var $dropdownMenu = jQuery('<ul class="halo-dropdown-menu" role="menu"></ul>');
					for(var i = maxItem - 1 ; i < $children.length; i++) {
						jQuery($children[i])
							.removeClass('col-xs-6')
							.appendTo($dropdownMenu);
					}
					$dropdownTitle.appendTo($dropdown);
					$dropdownMenu.appendTo($dropdown);
					$dropdown.appendTo($list);
				}
			})
		},
		getLoadingIcon: function (zoom) {
			var size = '';
			if (typeof zoom !== 'undefined') {
				size = 'fa-' + parseInt(zoom) + 'x';
			}
			//return '<i class="fa fa-spinner fa-spin ' + size + '"></i>';
			return '<i class="halo-loading-icon ' + size + '"></i>';
		}, 
		getJaxLoading: function (zoom) {
			return '<div class="halo-jax-loading">' + halo.template.getLoadingIcon(zoom) + '</div>';
		}, 
		removeStreamLoadMore: function () {
			jQuery('#halo-stream-loadmore').remove();
		}

	}
});

/* ============================================================ zone features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	zone: {
		initZone: function (zone) {
			halo.init(zone);
		},
		updateZone: function (data) {
			//get data zone id
			var replace = halo.util.findZone(data);
			var zoneId = replace.data('halozone');
			if (zoneId.length) {
				var selector = "[data-halozone='" + zoneId + "']";
				var el = jQuery(selector);
				if (el.length) {
					el.replaceWith(replace);
					//reinit binding on the updated zone
					halo.zone.initZone(jQuery(selector));
				}
			}
		}, 
		afterZone: function (data, zoneId) {
			//get data zone id
			var zone = jQuery(data);
			if (zoneId.length) {
				var selector = "[data-halozone='" + zoneId + "']";
				var el = jQuery(selector);
				if (el.length) {
					el.after(zone);
					//reinit binding on the updated zone
					halo.zone.initZone(zone);
				}
			}
		}, 
		beforeZone: function (data, zoneId) {
			//get data zone id
			var zone = jQuery(data);
			if (zoneId.length) {
				var selector = "[data-halozone='" + zoneId + "']";
				var el = jQuery(selector);
				if (el.length) {
					el.before(zone);
					//reinit binding on the updated zone
					halo.zone.initZone(zone);
				}
			}
		}, 
		insertZone: function (data, zoneId, mode) {
			//get data zone id
			var zone = jQuery(data);
			if (zoneId.length) {
				var selector = "[data-halozone='" + zoneId + "']";
				var el = jQuery(selector);
				if (el.length) {
					if (mode == 'overwrite') {
						el.html(zone);
					} else {
						//check for halopaging exits
						var halopage = zone.attr('data-halopage');
						var pContent;
						if(halopage) {
							var pSel = "[data-halopage='" + halopage + "']";
							var pContent = el.find(pSel);
						}
						if(pContent && pContent.length) {
							//zone paging exists, just replace it
							pContent.replaceWith(zone);
						} else {
							if (mode == 'first') {
								//insert as the first child
								el.prepend(zone);
							} else {
								//insert as the last child
								el.append(zone);
							}
						}
					}
					//reinit binding on the updated zone
					halo.zone.initZone(el);
				}
			}
		}, 
		insertZoneContent: function (data, mode) {
			//get data zone id
			var zoneWrapper = halo.util.findZone(data);
			var zoneId = zoneWrapper.data('halozone');
			var zoneContent = zoneWrapper.html();
			if (zoneId.length) {
				var selector = "[data-halozone='" + zoneId + "']";
				var el = jQuery(selector);
				if (el.length) {
					if (mode == 'first') {
						//insert as the first child
						el.prepend(zoneContent);
					} else {
						//insert as the last child
						el.append(zoneContent);
					}
					//reinit binding on the updated zone
					halo.zone.initZone(el);
				}
			}
		}, 
		removeZone: function (zoneId) {
			if (zoneId.length) {
				var selector = "[data-halozone='" + zoneId + "']";
				var el = jQuery(selector);
				if (el.length) {
					el.remove();
				}
			}

		}, 
		markChanged: function (data) {
			jQuery(data).addClass('halo-changed-background');
			//timer to remove class
			setTimeout(function () {
				jQuery(data).removeClass('halo-changed-background');
			}, 1000 * 60)

		}
	}
});

/* ============================================================ brief features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	brief: {
		cache: {},
		showDelay: 500,
		hideDelay: 500,
		init: function (scope) {
			//only enable brief for non-touch device
			if (!halo.util.isMobile()){
				jQuery('[data-brief-context][data-brief-id]', scope).each(function () {
					var $this = jQuery(this);
					var briefContext = $this.attr('data-brief-context');
					var briefId = $this.attr('data-brief-id');
					var targetId = 'brief_' + briefContext + '_' + briefId;
					$this.attr('data-popover-target', targetId);
					var popover = jQuery(this).hpopover({
						html: true,
						delay: { show: halo.brief.showDelay, hide: halo.brief.hideDelay },
						trigger: 'manual',
						placement: 'auto bottom',
						container: 'body',
						template: '<div class="popover brief-popover"><div class="arrow"></div><div data-popover-source="' + targetId + '" class="popover-content"></div></div>',
						content: function () {
							var loadingDiv = '<div data-halozone="brief_' + briefContext + '_' + briefId + '" class="halo-loading container halo-brief-loading">'
								+ halo.template.getJaxLoading(2)
								+ '</div>';
							return loadingDiv;
						}
					});
					//onclick in brief, clear the cache
					var clickEveryWhere = function (e) {
						if (jQuery(e.target).closest('.brief-popover').length !== 0) {
							delete halo.brief.cache[briefContext + '_' + briefId];
							$brief = jQuery('[data-halozone="brief_' + briefContext + '_' + briefId + '"]').addClass('halo-loading');
						}
						$this.hpopover('hide');
					}
					var singleBrief = function (e) {
						$this.hpopover('hide');
					}
					jQuery(this).on('shown.bs.hpopover', function () {
						//
						var $this = jQuery(this);
						var briefContext = $this.attr('data-brief-context');
						var briefId = $this.attr('data-brief-id');
						//check for local cache before calling ajax request
						if (typeof halo.brief.cache[briefContext + '_' + briefId] === 'undefined') {
							halo.jax.call("system", "GetBrief", briefContext, briefId);
						} else {
							halo.brief.updateBrief(briefContext, briefId, halo.brief.cache[briefContext + '_' + briefId]);
						}

						jQuery('[data-popover-source="' + targetId + '"]').on('mouseenter', function () {
							//configure on mouseleave event
							var targetId = jQuery(this).attr('data-popover-source');
							var target = jQuery('[data-popover-target="' + targetId + '"]');
							target.addClass('in');
							jQuery(this).one('mouseleave', function () {
								target.removeClass('in');
								setTimeout(function () {
									if (!target.hasClass('in')) {
										target.hpopover('hide');
									}
								}, halo.brief.hideDelay)
							})

						})

						jQuery(document).on('click', clickEveryWhere);
						jQuery(document).one('openbrief.halo', singleBrief);
					})
						.on('show.bs.hpopover', function () {
							jQuery(document).trigger('openbrief.halo');
						})
					;
					jQuery(this).on('mouseenter', function () {
						var $this = jQuery(this);
						$this.addClass('in');
						setTimeout(function () {
							if ($this.hasClass('in')) {
								$this.hpopover('show');
							}
						}, halo.brief.showDelay)
					});

					jQuery(this).on('mouseleave', function () {
						var $this = jQuery(this);
						$this.removeClass('in');
						setTimeout(function () {
							if (!$this.hasClass('in')) {
								$this.hpopover('hide');
							}
						}, halo.brief.hideDelay)

					});

					jQuery(this).on('hide.bs.hpopover', function () {
						//
						var $this = jQuery(this);
						var briefContext = $this.attr('data-brief-context');
						var briefId = $this.attr('data-brief-id');
						$brief = jQuery('[data-halozone="brief_' + briefContext + '_' + briefId + '"]');
						//update the cache
						if ($brief.length && !$brief.hasClass('halo-loading')) {
							halo.brief.cache[briefContext + '_' + briefId] = $brief;
						}
						jQuery(document).off('click', clickEveryWhere);
						jQuery(document).off('openbrief.halo', singleBrief);
					});
				});
			}
		}, 
		updateBrief: function (briefContext, briefId, content) {
			$brief = jQuery('[data-halozone="brief_' + briefContext + '_' + briefId + '"]');
			if ($brief.length) {
				var $content = jQuery(content);
				$brief.replaceWith($content);
				//update the cache
				halo.brief.cache[briefContext + '_' + briefId] = content;

				halo.init($content);
			}
		}
	}
});

/* ============================================================ thumbgrid features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	thumbgrid: {
		inited: false,
		init: function (scope) {
			//bind screen change event
			if(!halo.thumbgrid.inited) {
				jQuery(window).resize(halo.util.throttle(function() {
					halo.thumbgrid.init(jQuery(document));
				}, 100));
				halo.thumbgrid.inited = true;
			}
			
			var $thumbs = jQuery('.halo-list-thumb', scope);
			if($thumbs.length) {
				//clear old styling
				$thumbs.each(function(){
					if(this.style) {
						this.style.height = "";
					}
				});
				//get column count
				var nColumn = halo.thumbgrid.getColumnCount($thumbs.first());
				for(var i = 0; i < $thumbs.length; i++) {
					if( (i % nColumn) == ( nColumn - 1)) {
						//get max height
						var maxHeight = 0;
						for(var j = ( i - nColumn); j <= i; j ++) {
							maxHeight = Math.max(maxHeight, jQuery($thumbs[j]).height());
						}
						//set equal height
						for(var j = ( i - nColumn); j <= i; j ++) {
							 jQuery($thumbs[j]).height(maxHeight);
						}								
					}
				}
			}
		},
		getColumnCount: function($thumb) {
			var nColumn = Math.floor($thumb.parent().width() / $thumb.width());
			//adjustment
			if(nColumn >= 5) {
				nColumn = 6;
			}
			return nColumn;
		}
	}
});

/* ============================================================ location features
 *
 * ============================================================ */
function __haloLoadScriptCb() {
	halo.location.hasGoogleScript = true;
	halo.location.loadingScript = false;
	halo.location.loadScriptCb.apply(this, halo.location.paramsCb);
	//Load infobox after google api
	var _infoboxScript = document.createElement('script');
	_infoboxScript.type = 'text/javascript';
	_infoboxScript.src = halo_assets_url + '/js/infobox_packed.js';
	document.body.appendChild(_infoboxScript);
}
jQuery.extend(true, halo, {
	location: {
		map: null,
		markers: [],
		hasGoogleScript: false,
		loadingScript: false,
		loadScriptCb: null,
		paramsCb: [],
		detectable: true,
		currentLocation: null,
		delay: 1000,
		wait: true,
		// Define google maps controls
		gmapControls: {
			// Set callback events to element Dom control 
			// e.g: {
			// 			click: function() {
			// 				alert('clicked');
			// 			}
			// 			drag: function() {
			// 				alert('dragged');
			// 			}
			// 		}
			_setEvents: function(elementDom, callbackEvents) {
				for (var evt in callbackEvents) {
					google.maps.event.addDomListener(elementDom, evt, callbackEvents[evt]);
				}
			},
			_createElement: function(elemHTML) {
				var $div = jQuery('<div style="margin-top: 16px; margin-right: 5px; padding-top: 2px;" class="halo-gmap-control-common"><div><div>' + elemHTML + '</div></div></div>');
				return $div;
			},
			create: {
				CurrentPositionControl: function(map, callbackEvents) {
					$div = halo.location.gmapControls._createElement('<i class="fa fa-location-arrow"></i>');
					var divDom = $div.get(0);
					map.controls[google.maps.ControlPosition.TOP_LEFT].push(divDom);
					halo.location.gmapControls._setEvents(divDom, callbackEvents);
					return divDom;
				},
				RefreshAreaControl: function(map, callbackEvents) {
					$div = halo.location.gmapControls._createElement('<i class="fa fa-refresh"></i><span class="arrow_box arrow_box_left">' + __halotext('Search this area') + '</span>');
					
					$div.bind('halo.stop_noticeme',function() {
						jQuery('.arrow_box', this).hide(400);
					});

					$div.bind('halo.noticeme',function() {
						jQuery('.arrow_box', this).show(400).css('display','block');
					});
					
					$div.bind('halo.spin',function() {
						jQuery('.fa-refresh', this).addClass('fa-spin');
					});
					
					$div.bind('halo.spinstop',function() {
						jQuery('.fa-refresh', this).removeClass('fa-spin');
					});
					
					$div.bind('halo.mapreload',function() {
						jQuery(this).trigger('halo.stop_noticeme');
						jQuery(this).trigger('halo.spin');
					});

					var divDom = $div.get(0);
					map.controls[google.maps.ControlPosition.TOP_LEFT].push(divDom);
					halo.location.gmapControls._setEvents(divDom, callbackEvents);
					return divDom;
				}
			}
		},
		gmapCommon: {
			map: null,
			infoBox: null,
			display: null,
			clusterInfo: null,
			markers: [],
			PostMarker: function(params, map, myPosition) {
				var gmap = halo.location.gmapCommon;
				var icon = __getGlobalMarkerIcon(params.type_id, params.main_cat_id);
				var curIcon = halo_assets_url + "/images/gmap_marker/marker_current.png";
				var postMarker = {};

				// Set properties;
				for (var i in params) {
					postMarker[i] = params[i];
				}

				// Set marker property;
				postMarker.marker = new google.maps.Marker({
					position: new google.maps.LatLng(params.lat, params.lng),
					map: map,
					draggable: false,
					animation: google.maps.Animation.DROP,
					icon: params.is_current ? curIcon : icon.normal,
					title: params.title
				});
				postMarker.marker.clusterIndex = params.cluster_index;
				postMarker.marker.postId = params.post_id;
				if (myPosition) {
					postMarker.currentDistance = halo.location.getDistance(myPosition, postMarker.marker.getPosition());
				} else {
					postMarker.currentDistance = false;
				}

				postMarker.render = function() {
					
				};
				
				// Clear google maps marker
				postMarker.clear = function() {
					postMarker.marker.setAnimation(null);
				};

				// Get google directions
				postMarker.doDirections = function(originPos, directions, display) {
					if (originPos) {
						if (!Boolean(postMarker.distance)) {
							var request = {
								origin: originPos,
								destination: postMarker.marker.getPosition(),
								travelMode: google.maps.TravelMode.DRIVING
							};
							directions.route(request, function (response, status) {
								if (status == google.maps.DirectionsStatus.OK) {
									var totalDistance = 0;
									var legs = response.routes[0].legs;
									for (var i = 0; i < legs.length; ++i) {
										totalDistance += legs[i].distance.value;
									}
									totalDistance = totalDistance/1000;
									totalDistance = totalDistance < 0.5 ? totalDistance : Math.round(totalDistance);
									var distanceHTML = '<i class="fa fa-road"></i> ' + totalDistance + ' km';
									var content = '<div class="halo-list-wrapper-post">' + jQuery(postMarker.content).find('.halo-post-distance').html(distanceHTML).closest('.halo-list-wrapper-post').html() + '</div>';
									postMarker.distance = totalDistance;
									postMarker.directions = response;
									postMarker.content = content;
									if (postMarker.currentDistance && postMarker.currentDistance <= 30) {
										display.setDirections(response);
									}
								}
							});
						} else {
							if (postMarker.currentDistance && postMarker.currentDistance <= 30) {
								display.setDirections(postMarker.directions);
							}
						}
					}
				};

				// Bind Events to PostMarker marker
				postMarker.on = function(type, handler) {
					var that = this;
					google.maps.event.addListener(postMarker.marker, type, function() {
						handler(that);
					});
				};

				return postMarker;
			},
			clearMarkers: function(option) {
				for (var i in halo.location.gmapCommon.markers) {
					if (option === undefined || option == 'map') {
						halo.location.gmapCommon.markers[i].setMap(null);
					} else if (option == 'animation') {
						halo.location.gmapCommon.markers[i].setAnimation(null);
					}	
				}
			},
			clearMap: function() {
				halo.location.gmapCommon.clearMarkers('map');
				if (halo.location.gmapCommon.display) {
					halo.location.gmapCommon.display.setMap(null);
					halo.location.gmapCommon.display = null;
				}
				if (halo.location.gmapCommon.infoBox) {
					halo.location.gmapCommon.infoBox.close();
					halo.location.gmapCommon.infoBox = null;
				}
				halo.location.gmapCommon.markers = [];
			},
			renderMakers: function(posts, postMarkers, markers, infoBox, myPosition, directions, display, bounds, map) {
				for (var i in posts) {
					var postMarker = new halo.location.gmapCommon.PostMarker(posts[i], map, myPosition);
					
					postMarker.on('click', function(post) {
						infoBox.setContent(post.content);
						infoBox.open(map, post.marker);
						halo.location.gmapCommon.clearMarkers('animation');
						post.marker.setAnimation(google.maps.Animation.BOUNCE);
						if (halo.location.gmapCommon.clusterInfo) {
							halo.location.gmapCommon.clusterInfo.close();
						}
					});

					postMarker.on('mouseover', function(post) {
						post.doDirections(myPosition, directions, display);
					});

					jQuery(postMarker).bind('halo.marker.direction', function() {
						this.doDirections(myPosition, directions, display);
					});

					postMarkers[i] = postMarker;
					markers[i] = postMarker.marker;
					bounds.extend(postMarker.marker.getPosition());
				}
			},
			initMarkerClusterer: function(map, markers, contents, postMarkers, myPosition, directions, display) {
				halo.util.addRequiredScripts([halo_assets_url + '/js/markerclusterer.min.js'], function() {
					var mcOptions = {
						averageCenter: true,
						title: __halotext('Show more posts'),
						maxZoom: 18
					};
					var markerClusterer = new MarkerClusterer(map, markers, mcOptions);

					google.maps.event.addListener(markerClusterer, 'mouseover', function (c) {
						jQuery(postMarkers[c.getMarkers()[0].postId]).trigger('halo.marker.direction');
					});

					google.maps.event.addListener(markerClusterer, 'click', function (c) {
						if (halo.location.gmapCommon.checkSameClusterIndex(c.getMarkers())) {
							markerClusterer.setZoomOnClick(false);
							// halo.location.gmapCommon.expandCluster(c);
							if (!halo.location.gmapCommon.clusterInfo) {
								halo.location.gmapCommon.clusterInfo = new google.maps.InfoWindow({
									maxWidth: 300
								});
							}
							halo.location.gmapCommon.clusterInfo.setContent(contents[c.getMarkers()[0].clusterIndex].replace('{distance}', jQuery(postMarkers[c.getMarkers()[0].postId].content).find('.halo-post-distance').html()));
							halo.location.gmapCommon.clusterInfo.open(map, c.getMarkers()[0]);
							if (halo.location.gmapCommon.infoBox) {
								halo.location.gmapCommon.infoBox.close();
								halo.location.gmapCommon.clearMarkers('animation');
							}
						} else {
							markerClusterer.setZoomOnClick(true);
						}
					});
					google.maps.event.addListener(markerClusterer, 'clusteringbegin', function (mc) {
						// mc.setZoomOnClick(false);
					});

				});
			},
			expandCluster: function(cluster) {
				var ONE_RADIAN = Math.PI/180,
					RADIUS = 1,
					markers = cluster.getMarkers(),
					clusterX = markers[0].getPosition().lat(),
					clusterY = markers[0].getPosition().lng();

				for (var i in markers) {
					var angle = (360 / markers.length) * i * ONE_RADIAN;
    				var lat = clusterX + Math.cos(angle) * RADIUS;
    				var lng = clusterY + Math.sin(angle) * RADIUS;
    				var position = new google.maps.LatLng(lat, lng);
    				markers[i].setPosition(position);
				}

			},
			checkSamePosition: function(markers) {
				var leng = markers.length;
				if (leng < 2) {
					return false;
				}
				var marker = markers[0];
				for (var i = 1; i < leng; i++) {
					if (!(marker.getPosition().lat() == markers[i].getPosition().lat() && marker.getPosition().lng() == markers[i].getPosition().lng())) {
						return false;
					}
				}
				return true;
			},
			checkSameClusterIndex: function(markers) {
				if (markers.length < 2) return false;
				var index = markers[0].clusterIndex;
				for (var i = 1; i < markers.length; i++) {
					if (index != markers[i].clusterIndex) {
						return false;
					}
				}
				return true;
			}
		},
		getCurrentAddress: function (type) {
			if (halo.location.currentLocation) {
				var loc = halo.location.currentLocation.address[0];
				return loc.formatted_address;
			} else {
				return '';
			}
		}, 
		setDetectable: function (val) {
			halo.location.detectable = val;
		}, 
		isDetectable: function () {
			return halo.location.detectable;
		}, 
		getCurrentPosition: function () {
			if (halo.location.currentLocation) {
				return halo.location.currentLocation.latlng;
			} else {
				return null;
			}
		}, 
		loadScript: function (callback,param) {
			var key = jQuery('#halo_ggApiKey').attr('data-gg');
			if(!key) return;
			//prevent multiple loading google script
			if (halo.location.loadingScript) {
				setTimeout(function () {
					halo.location.loadScript(callback,param);
				}, 200)
				return;
			}
			halo.location.loadScriptCb = callback;
			halo.location.paramsCb = (typeof param !== 'undefined')?param:[];
			if (!halo.location.hasGoogleScript) {
				halo.location.loadingScript = true;
				var script = document.createElement('script');
				script.type = 'text/javascript';
				script.src = halo.location.getGoogleMapUrl('__haloLoadScriptCb');
				document.body.appendChild(script);
			} else {
				callback.apply(this, halo.location.paramsCb);
			}
		},
		autodetect: function (options, callback) {
			function setCurrentLocation(position) {
				halo.location.loadScript(function () {
					var latlng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
					var geocoder = new google.maps.Geocoder();
					options = jQuery.extend(options, { 'latLng': latlng });
					geocoder.geocode(options, function (results, status) {
						if (status == google.maps.GeocoderStatus.OK) {
							if (results[0]) {
								halo.location.currentLocation = {latlng: latlng, address: results};
							} else {
								halo.location.setDetectable(false);
								alert('No results found');
							}
						} else {
							if (status == google.maps.GeocoderStatus.OVER_QUERY_LIMIT) {
								setTimeout(function() {
									setCurrentLocation(position);
								}, halo.location.delay);
								halo.log('Geocoder failed due to: ' + status);
							} else {
								halo.location.setDetectable(false);
							}
						}
						callback.apply();
					});
				});
			}

			if (!halo.location.currentLocation) {
				//determine if the handset has client side geo location capabilities
				if(navigator.userAgent.indexOf("Safari") > -1){
					if(geo_position_js.init()){
						 geo_position_js.getCurrentPosition(setCurrentLocation, halo.location.error,{maximumAge: Infinity,timeout:1000});
					}
					else{
						 halo.log(__halotext('Browser does not support this method to detect location'));
					}
				} else {
					if (navigator.geolocation) {
						navigator.geolocation.getCurrentPosition(setCurrentLocation, halo.location.error,{maximumAge: Infinity,timeout:1000});
					} else {
						//try with other method to detect locaiont
						halo.log(__halotext('Browser does not support this method to detect location'));
					}
				}
			} else {
				callback.apply();
			}
		}, 
		detect: function () {
			halo.location.autodetect({}, function () {
				var pos = halo.location.getCurrentPosition();
				if (pos)
					halo.location.detected(pos);
			});
		},
		getGoogleMapUrl: function (callback) {
			var key = jQuery('#halo_ggApiKey').attr('data-gg');
			var url;
			if(!key) {
				//use default api key
				key = 'AIzaSyDuiUyJp4LPYfxlRLnJvGbxj-JPAtEBOOc&sensor';
			}
			url = 'https://maps.googleapis.com/maps/api/js?key=' + key + '&sensor=true&libraries=places&callback=' + callback;
			return url;
		}, 
		clearMarkers: function () {
			for (var i = 0, marker; marker = halo.location.markers[i]; i++) {
				marker.setMap(null);
			}
			halo.location.markers = [];
		},
		onUpdateLocationMarker: function (marker) {
			jQuery('[name="halo_checkin_lat"]').val(marker.getPosition().lat());
			jQuery('[name="halo_checkin_lng"]').val(marker.getPosition().lng());
		},
		checkin: function (controlId) {
			//validate to make sure location is provided properly
			var locLat = jQuery('[name="halo_checkin_lat"]').val();
			var locLng = jQuery('[name="halo_checkin_lng"]').val();
			var locName = jQuery('[name="halo_checkin_name"]').val();
			if (('' + locLat).length && ('' + locLng).length && locName.length) {
				halo.location.setLocationData(controlId, {lat: locLat, lng: locLng, name: locName});
				halo.popup.close();
			} else {
				alert(__halotext('Please enter your location name'));
				jQuery('[name="halo_checkin_name"]').focus();
			}

		},
		showCheckin: function (controlId) {
			//open the popup form
			halo.popup.setFormTitle(__halotext('Find location'));
			var mapCanvas = '<div id="map_canvas" class="container" style="height: 400px;width: 400px"></div>';
			var input = '<input id="pac-input" name="halo_checkin_name" class="controls" type="text" placeholder="' + __halotext('Search Location') + '">';
			var inputLat = '<input type="hidden" name="halo_checkin_lat">';
			var inputLng = '<input type="hidden" name="halo_checkin_lng">';
			var warning = '<div class="halo-allow-location text-center halo-top16"><p>*Please grant Halo.Social access to your location when asked by browser.</p></div>';
			halo.popup.setFormContent(mapCanvas + input + inputLat + inputLng + warning);
			//enable detect location mode if browser supports
			if (navigator.geolocation) {
				halo.popup.addFormAction({    name: __halotext('Use My Location'),
				    'class': 'halo-btn-primary',
					onclick: 'halo.location.detect()',
					href: 'javascript:void(0);',
					icon: 'crosshairs'
				});
			}
			halo.popup.addFormAction({name: __halotext('Use this location'),
				onclick: 'halo.location.checkin(\'' + controlId + '\')',
				icon: 'check'});

			halo.popup.addFormActionCancel();
			halo.popup.showForm();
			halo.location.loadScript(function () {

				//render google map
				var markers = [];
				var map = new google.maps.Map(document.getElementById('map_canvas'), {
					mapTypeId: google.maps.MapTypeId.ROADMAP
				});

				var defaultBounds = new google.maps.LatLngBounds(
					new google.maps.LatLng(-33.8902, 151.1759),
					new google.maps.LatLng(-33.8474, 151.2631));
				map.fitBounds(defaultBounds);
				var input = /** @type {HTMLInputElement} */(document.getElementById('pac-input'));
				// Create the search box and link it to the UI element.
				map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
				halo.location.map = map;
				var searchBox = new google.maps.places.SearchBox(/** @type {HTMLInputElement} */(input));
				// Listen for the event fired when the user selects an item from the
				// pick list. Retrieve the matching places for that item.
				google.maps.event.addListener(searchBox, 'places_changed', function () {
					var places = searchBox.getPlaces();
					//clear old markers
					halo.location.clearMarkers();

					// For each place, get the icon, place name, and location.
					var bounds = new google.maps.LatLngBounds();
					for (var i = 0, place; place = places[i]; i++) {
						var image = {
							url: place.icon,
							size: new google.maps.Size(71, 71),
							origin: new google.maps.Point(0, 0),
							anchor: new google.maps.Point(17, 34),
							scaledSize: new google.maps.Size(25, 25)
						};

						// Create a marker for each place.
						var marker = new google.maps.Marker({
							map: map,
							draggable: true,
							title: place.name,
							position: place.geometry.location
						});
						halo.location.markers.push(marker);
						bounds.extend(place.geometry.location);

						//marker is draggable, bind event to update lng,lat
						google.maps.event.addListener(marker, "dragend", function () {
							halo.location.onUpdateLocationMarker(this)
						});
						google.maps.event.addListener(marker, "click", function () {
							halo.location.onUpdateLocationMarker(this)
						});
						//udate lng,lat
						halo.location.onUpdateLocationMarker(marker)
					}

					map.fitBounds(bounds);
				});

				input.focus();
			});

		},

		detected: function (position) {
			//location name must be enter
			jQuery('#pac-input').val(halo.location.getCurrentAddress());
			jQuery('#pac-input').focus();
			halo.location.clearMarkers();
			//marker not exists, create new one
			var marker = new google.maps.Marker({
				map: halo.location.map,
				draggable: true,
				title: jQuery('#pac-input').val(),
				position: position
			});
			google.maps.event.addListener(marker, "dragend", function () {
				halo.location.onUpdateLocationMarker(this)
			});
			google.maps.event.addListener(marker, "click", function () {
				halo.location.onUpdateLocationMarker(this)
			});

			halo.location.map.setCenter(position);
			//triger event to update latlng
			google.maps.event.trigger(marker, "click");
			halo.location.markers.push(marker);
		},

		error: function (error) {
			halo.log(error);
			halo.location.setDetectable(false);
			if (error.code == error.PERMISSION_DENIED) {
				// pop up dialog asking for location
                halo.log(__halotext('HaloSocial does not have permission to use your location.'));
			} else {
				halo.log(__halotext('Could not detect your current location.'));
			}
		},
		hasShareLocation: function () {
			return jQuery('[name="share_location_name"]').val().length;
		},
		showNearbyPosts: function(data) {
			halo.location.gmapCommon.clearMap();
			halo.location.loadScript(function () {
				var posts = data.posts;
				var canvasId = data.input.old.canvas_id;
				var currentPostId = data.post_id;
				var postMarkers = [];
				var refreshingUI;
				var map;				
				var mapOptions = {
					mapTypeId: google.maps.MapTypeId.ROADMAP,
					zoom: data.input.old.zoom,
					mapTypeControl: false
				};

				var myPosition = halo.location.getCurrentPosition();
				if (!halo.location.gmapCommon.map) {
					map = new google.maps.Map(document.getElementById(canvasId), mapOptions);
					halo.location.gmapCommon.map = map;
				} else {
					map = halo.location.gmapCommon.map;
				}

				if (typeof data.input.old.center_lat !== 'undefined' && typeof data.input.old.center_lng !== 'undefined') {
					map.setCenter(new google.maps.LatLng(data.input.old.center_lat, data.input.old.center_lng));
				}

				if (map.controls[google.maps.ControlPosition.TOP_LEFT].getAt(1)) {
					jQuery(map.controls[google.maps.ControlPosition.TOP_LEFT].getAt(1)).trigger('halo.spinstop');
				}

				google.maps.event.addListener(map, 'dragstart', function() {
					//jQuery(RefreshAreaControl).trigger('halo.stop_noticeme');
				});

				google.maps.event.addListener(map, 'dragend', function() {
					jQuery(RefreshAreaControl).trigger('halo.noticeme');
				});

				google.maps.event.addListener(map, 'zoom_changed', function() {
					jQuery(RefreshAreaControl).trigger('halo.noticeme');
				});
				
				if (data.type == 'viewport') {
					//map.setCenter(new google.maps.LatLng(data.map_center[0], data.map_center[1]));
					//map.setZoom(data.input.old.zoom);
				}

				var directions = new google.maps.DirectionsService();
				halo.location.gmapCommon.display = new google.maps.DirectionsRenderer({map: map, preserveViewport: true, suppressMarkers : true});
				var bounds = new google.maps.LatLngBounds();
				
				halo.location.gmapCommon.infoBox = new InfoBox({
					content: '',
					boxStyle: {
						width: "250px",
						background: "url('http://google-maps-utility-library-v3.googlecode.com/svn/trunk/infobox/examples/tipbox.gif') no-repeat"
					},
					pixelOffset: new google.maps.Size(-125, -60),
					infoBoxClearance: new google.maps.Size(16, 16),
					closeBoxMargin: "-17px 7px 0 0",
					alignBottom: true
				});

				// Close InfoBox and remove marker Animation when clicking on map
				google.maps.event.addListener(map, 'click', function() {
					if (typeof halo.location.gmapCommon.infoBox !== 'undefined') {
						halo.location.gmapCommon.infoBox.close();
						halo.location.gmapCommon.clearMarkers('animation');
					}
					if (halo.location.gmapCommon.clusterInfo) {
						halo.location.gmapCommon.clusterInfo.close();
					}
				});
				// Trigger click for clearing markers
				google.maps.event.addListener(halo.location.gmapCommon.infoBox, "closeclick", function () {
					halo.location.gmapCommon.clearMarkers('animation');
				});

				// Building Google Maps custom controls
				 if (!map.controls[google.maps.ControlPosition.TOP_LEFT].getLength()) {
					var CurrentPositionControl = halo.location.gmapControls.create.CurrentPositionControl(map, {
						click: function() {
								halo.location.autodetect({}, function() {
								var myPosition = halo.location.getCurrentPosition();
								if (myPosition) {
									jQuery(RefreshAreaControl).trigger('halo.mapreload');
									var values = halo.util.initFormValues();
									halo.util.setFormValue(values, 'canvas_id', canvasId);
									halo.util.setFormValue(values, 'post_id', currentPostId);
									halo.util.setFormValue(values, 'lat', myPosition.lat());
									halo.util.setFormValue(values, 'lng', myPosition.lng());
									halo.util.setFormValue(values, 'limit', 24);
									halo.util.setFormValue(values, 'center_lat', myPosition.lat());
									halo.util.setFormValue(values, 'center_lng', myPosition.lng());
									halo.post.getNearbyPosts(values);
								}
							});
						}
					});

					var RefreshAreaControl = halo.location.gmapControls.create.RefreshAreaControl(map, {
						click: function() {
							jQuery(RefreshAreaControl).trigger('halo.mapreload');
							var center = map.getCenter();
							var bounds = map.getBounds();
							var topLocation = bounds.getNorthEast();
	                  		var bottomLocation = bounds.getSouthWest();
	                  		var values = halo.util.initFormValues();
							halo.util.setFormValue(values, 'canvas_id', canvasId);
							halo.util.setFormValue(values, 'post_id', currentPostId);
							halo.util.setFormValue(values, 'limit', 24);
							halo.util.setFormValue(values, 'type', 'viewport');
							halo.util.setFormValue(values, 'toplat', topLocation.lat());
							halo.util.setFormValue(values, 'toplng', topLocation.lng());
							halo.util.setFormValue(values, 'btmlat', bottomLocation.lat());
							halo.util.setFormValue(values, 'btmlng', bottomLocation.lng());
							halo.util.setFormValue(values, 'zoom', map.getZoom());
							halo.post.getNearbyPosts(values);
						}
					});
				}

				halo.location.gmapCommon.renderMakers(
					posts, 
					postMarkers, 
					halo.location.gmapCommon.markers, 
					halo.location.gmapCommon.infoBox, 
					myPosition, directions, 
					halo.location.gmapCommon.display, 
					bounds, 
					map
				);

				if (myPosition) {
					var marker = new google.maps.Marker({
						position: myPosition,
						map: map,
						title: 'My location',
						animation: google.maps.Animation.DROP
					});
					if (data.input.old.type == 'latlng' && (postMarkers[data.post_id].currentDistance && postMarkers[data.post_id].currentDistance <= 30)) {
						var myInfo = new google.maps.InfoWindow({
							content: '<div style="width: 70px;">My location</div>'
						});
						myInfo.open(map, marker);
					}
					if (postMarkers[data.post_id].currentDistance && postMarkers[data.post_id].currentDistance <= 30) {
						postMarkers[data.post_id].doDirections(myPosition, directions, halo.location.gmapCommon.display);
					}
					// halo.location.gmapCommon.markers.push(marker);
					bounds.extend(myPosition);
				}
				if (data.input.old.type == 'latlng') {
					//map.fitBounds(bounds);
				}

				halo.location.gmapCommon.initMarkerClusterer(
					map, 
					halo.location.gmapCommon.markers, 
					data.cluster_content, 
					postMarkers, 
					myPosition, 
					directions, 
					halo.location.gmapCommon.display
				);
			});
		},

		init: function (scope) {
			// Parse popover size
			var __parseSize = function() {
				var m = halo.util.isMobile();
				var wrapWidth = 500;
				var wrapHeight = 450;
				var paddingWidth = 30;
				var paddingHeight = 20;
				return {
					m: m,
					mapClass: m ? '' : 'halo-map-popover',
					wrapWidth: wrapWidth,
					innerHeight: wrapHeight - paddingHeight,
					innerWidth: wrapWidth - paddingWidth
				}
			};

			//map dropdown
			jQuery('[data-halo-map-dropdown]', scope).each(function () {
				var link = jQuery(this);
				var canvasId = link.data('halo-loc-id') + '-canvas';
				var targetId = canvasId;
				link.attr('data-popover-target', targetId)
				var mapPopover = jQuery(this).hpopover({
					html: true,
					container: 'body',
					placement: 'auto top',
					delay: { show: 100, hide: 500 },
					trigger: 'manual',
					template: '<div class="popover ' + __parseSize().mapClass + '"><div class="arrow"></div>' + '<div data-popover-source="' + targetId + '" class="popover-content"></div></div>',
					content: function () {
						if (!__parseSize().m) {
							var canvas = '<div id="' + canvasId + '" class="halo-map-canvas halo-map-loading" style="height:' + __parseSize().innerHeight + 'px; paddingLeft: 0px; paddingRight: 0px">';
						} else {
							var canvas = '<div id="' + canvasId + '" class="halo-map-canvas halo-map-loading">';
						}
						return canvas;
					}
				});

				jQuery(this).on('mouseenter touchstart', function (e) {
					var $this = jQuery(this);
					if (!$this.hasClass('iiln')) {
						$this.addClass('in');
						setTimeout(function () {
							if ($this.hasClass('in')) {
								$this.hpopover('show');
							}
						}, halo.brief.showDelay);
					}
				});

				jQuery(this).on('mouseleave', function () {
					var $this = jQuery(this);
					$this.removeClass('in');
					setTimeout(function () {
						if (!$this.hasClass('in')) {
							$this.hpopover('hide');
						}
					}, halo.brief.hideDelay)

				});

				jQuery(this)
				.on('show.bs.hpopover', function(evt) {
				})
				.on('shown.bs.hpopover', function () {
					var link = jQuery(this);
					var address = link.data('halo-loc-name');
					var lat = link.data('halo-loc-lat');
					var lng = link.data('halo-loc-lng');
					var canvasId = link.data('halo-loc-id') + '-canvas';
/*
					if (!__parseSize().m) {
						jQuery('#' + canvasId).css({width: __parseSize().innerWidth + 'px', height: __parseSize().innerHeight + 'px', paddingLeft: '0px', paddingRight: '0px'});
					}
*/
					halo.location.loadScript(function () {
						var bounds = new google.maps.LatLngBounds();
						var point = new google.maps.LatLng(lat, lng);
						var mapOptions = {
							center: point,
							mapTypeId: google.maps.MapTypeId.ROADMAP,
							zoom: 12,
							mapTypeControl: false
						};
						var map = new google.maps.Map(document.getElementById(canvasId),
							mapOptions);

						var marker = new google.maps.Marker({
							position: point,
							map: map,
							draggable: true,
							icon: halo_assets_url + "/images/gmap_marker/marker_default.png",
							title: address
						});

						// Add full view button control
						var $fullBtn = jQuery('<div title="'+__halotext('Show full map of similar posts')+'" style="margin-top: 5px; margin-right: 5px; cursor: pointer;" class="halo-gmap-control"><i class="fa fa-plus-circle"></i> Full view</div>');
						var fullBtnDom = $fullBtn.get(0);
						map.controls[google.maps.ControlPosition.TOP_RIGHT].push(fullBtnDom);
						google.maps.event.addDomListener(fullBtnDom, 'click', function() {
							link.trigger('click');
							link.hpopover('hide');
						});


						var __loadMarkerInfo = function(distance) {			
								var contentInfo = '<div><i class="fa fa-road"></i> ' + distance + ' km' + '</div>';
								if (distance < 0.5) {
									contentInfo = '<div><i class="fa fa-road"></i> ' + __halotext('location near you') + ' </div>';
								}
								var infowindow = new google.maps.InfoWindow({
									content: contentInfo
								});
								infowindow.open(map, marker);
						}

						// Add my location to map
						var myPosition = halo.location.getCurrentPosition();
						var __loadLocCb = function() {
							if (myPosition) {
								var positionDistance = halo.location.getDistance(myPosition, point);
								var hasDirect = positionDistance <= 15;
								var directionsDisplay = new google.maps.DirectionsRenderer({
									preserveViewport:true,
									suppressMarkers : true
								});
								directionsDisplay.setMap(map);
								if (!Boolean(link.data('halo-loc-distance'))) {
									var directionsService = new google.maps.DirectionsService();
									var request = {
										origin: myPosition,
										destination: point,
										travelMode: google.maps.TravelMode.DRIVING
									};
									directionsService.route(request, function (response, status) {
										if (status == google.maps.DirectionsStatus.OK) {
											var legs = response.routes[0].legs;
											var totalDistance = 0;
											for (var i = 0; i < legs.length; ++i) {
												totalDistance += legs[i].distance.value;
											}
											totalDistance = totalDistance/1000;
											totalDistance = totalDistance < 0.5 ? totalDistance : Math.round(totalDistance);
											link.data('halo-loc-distance', totalDistance);
											link.data('halo-loc-directions', response);
											if (totalDistance < 0.5) {
												link.html(link.data('halo-loc-name') + ' (' + __halotext('nearly you') + ')');
											} else {
												link.html(link.data('halo-loc-name') + ' (' + totalDistance + 'km)');
											}
											if (hasDirect) {
												directionsDisplay.setDirections(response);
											}
											__loadMarkerInfo(totalDistance);
										} else {
											if (status == google.maps.DirectionsStatus.OVER_QUERY_LIMIT) {
												//setTimeout("__loadLocCb", halo.location.delay);
											} else {
												//TODO
											}
										}
									});
								} else {
									__loadMarkerInfo(link.data('halo-loc-distance'));
									if (hasDirect) {
										directionsDisplay.setDirections(link.data('halo-loc-directions'));
									}
								}

								if (hasDirect) {
									var myMarker = new google.maps.Marker({
										position: myPosition,
										map: map,
										draggable: true,
										title: __halotext('You are here')
									});
									bounds.extend(point);
									bounds.extend(myPosition);
									map.fitBounds(bounds);
								}
							}
						};
						if (myPosition == null) {
							if (halo.location.isDetectable()) {
								halo.location.autodetect({}, function() {
									myPosition = halo.location.getCurrentPosition();
									__loadLocCb();
								});
							}
						} else {
							__loadLocCb();
						}
						//bind event to make sure that the popup keep open
						jQuery('[data-popover-source="' + targetId + '"]').on('mouseenter', function () {
							//configure on mouseleave event
							var targetId = jQuery(this).attr('data-popover-source');
							var target = jQuery('[data-popover-target="' + targetId + '"]');
							target.addClass('in');
							jQuery(this).one('mouseleave', function () {
								target.removeClass('in');
								if (!target.hasClass('in')) {
										target.hpopover('hide');
									}
								setTimeout(function () {
									if (!target.hasClass('in')) {
										target.hpopover('hide');
									}
								}, halo.brief.hideDelay)
							});

						});
					});

				});
			});
			
			//Show map clicking on location link
			jQuery('[data-halo-map-dropdown]', scope).each(function() {
				var conWidth = jQuery(window).width() * .8;
				var conHeight = jQuery(window).height() * .9;

				var $that = jQuery(this);
				$that.haloMagnificPopup({
					mainClass: 'mfp-img-mobile',
					type: 'inline',
					//showCloseBtn: false,
					items: {
						src: '<div style="background-position: center center; background-color: #fff; max-width:'+conWidth+'px; width: auto; margin: 0px auto; position: relative;" class="halo-map-canvas-container">' +
								'<div style="width: '+conWidth+'px; height: '+conHeight+'px;" id="' + $that.data('halo-loc-id') + '-map-canvas' + '"></div>' +
							'</div>'
					},
					callbacks: {
						beforeOpen: function() {
							halo.location.gmapCommon.map = null;
						},
						open: function() {
							$that.hpopover('hide');
							
							var canvasId = $that.data('halo-loc-id') + '-map-canvas';
							var postLat = $that.data('halo-loc-lat');
							var postLng = $that.data('halo-loc-lng');
							var postId = $that.data('post-id');
							var postAddress = $that.data('halo-loc-name');
							var locArr = $that.data('halo-loc-id').split('-');
							
							jQuery('#' + canvasId).closest('.mfp-wrap').css('z-index', '99999999');
							jQuery('#' + canvasId).closest('.mfp-content').find('.mfp-close').css({top: '-32px', color: '#ccc'});

                            halo.location.loadScript(function () {
                                var map;
                                var latlng = new google.maps.LatLng(postLat, postLng);                
                                var mapOptions = {
                                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                                    zoom: 15,
                                    mapTypeControl: false,
                                    center: latlng
                                };

                                if (!halo.location.gmapCommon.map) {
                                    map = new google.maps.Map(document.getElementById(canvasId), mapOptions);
                                    halo.location.gmapCommon.map = map;
                                } else {
                                    map = halo.location.gmapCommon.map;
                                }
                                var marker = new google.maps.Marker({
                                    position: latlng,
                                    map: map,
                                    title: postAddress,
                                    animation: google.maps.Animation.DROP
                                });
                                var info = new google.maps.InfoWindow({
                                    content: postAddress
                                });
                                info.open(map, marker);
                            });



                            // Post plugin: Get nearby posts
                            // Uncomment to implement nearby posts
							// var values = halo.util.initFormValues();
							// halo.util.setFormValue(values, 'canvas_id', canvasId);
							// halo.util.setFormValue(values, 'location_id', locArr[locArr.length - 1]);
							// halo.util.setFormValue(values, 'post_id', postId);
							// halo.util.setFormValue(values, 'center_lat', postLat);
							// halo.util.setFormValue(values, 'center_lng', postLng);
							// halo.post.getNearbyPosts(values);
						},
						afterClose: function() {
							halo.location.gmapCommon.markers = [];
						}
					}
				});
			});

			jQuery(document.body).on('touchstart',function(e){
				if (jQuery(e.target).data('halo-map-dropdown') == undefined) {
					var target=jQuery('[data-halo-map-dropdown]',scope);
						target.hpopover('hide');
						target.removeClass('in');
				}
			});

			//location filed
			halo.location.initLocationField(scope);
		},
		setLocationData: function (controlId, datum) {
			var control = jQuery('#' + controlId);
			if (control.length) {
				var input = jQuery('.halo-field-location-name', control).last();		//use last matched element
				var latInput = jQuery('.halo-field-location-lat', control);
				var lngInput = jQuery('.halo-field-location-lng', control);
				latInput.val(datum.lat);
				lngInput.val(datum.lng);
				input.val(datum.name);
				//trigger event
				input.trigger('location.change.halo', [datum]);
			}
		},
		initLocationField: function (scope) {
			//enable autocomplete on the share_info_input
			var controls = jQuery('.halo-location-control', scope);
			
			controls.each(function () {
				var $this = jQuery(this);
				var placeType = $this.attr('data-place-type');
				switch(placeType){
					case 'halo':
						initHALOPlaceSearch($this);
						break;
					case 'gmap':
					default:
						initGmapPlaceSearch($this);
						break;
				}
			})
			initCurrentLocationDetect(scope);
			
			function initCurrentLocationDetect(scope) {
				var detectBtns = jQuery('.halo-field-location-detect',scope);
				detectBtns.each(function() {
					var $this = jQuery(this);
					var $control = $this.closest('.halo-location-control');
					var input = jQuery('.halo-field-location-name', $control);
					var latInput = jQuery('.halo-field-location-lat', $control);
					var lngInput = jQuery('.halo-field-location-lng', $control);
					var controlId = $this.attr('id');
					
					$this.on('click',function(){
						halo.location.autodetect({}, function () {
							var pos = halo.location.getCurrentPosition();
							if (pos) {
								latInput.val(pos.lat());
								lngInput.val(pos.lng());
								input.val(halo.location.getCurrentAddress());
								$control.trigger('changeFilter.halo');
							} else {
								//show error message
								alert('We can not detect your current position');
							}
						});
					});
				})
			}
			function initGmapPlaceSearch($ele){
				var $this = jQuery($ele)
				var input = jQuery('.halo-field-location-name', $this);
				var latInput = jQuery('.halo-field-location-lat', $this);
				var lngInput = jQuery('.halo-field-location-lng', $this);
				var controlId = $this.attr('id');
				halo.location.loadScript(function($ele,nameInput,latInput,lngInput){
					if(!nameInput || !nameInput.length) return;
					var autocomplete = new google.maps.places.Autocomplete(
						/** @type {HTMLInputElement} */nameInput[0],
						{ types: ['geocode'] });
					// When the user selects an address from the dropdown,
					// populate the address fields in the form.
					google.maps.event.addListener(autocomplete, 'place_changed', function() {
							fillInAddress();
						});
						
					function fillInAddress() {
						var place = autocomplete.getPlace();
						if(place.geometry) {
							var loc = place.geometry.location;
							latInput.val(loc.lat());
							lngInput.val(loc.lng());
							var datum = {'name':input.val(),'lat':loc.lat(),'lng':loc.lng()};
							input.trigger('location.change.halo', [datum]);
							$ele.trigger('changeFilter.halo');
						}
					}
				},[$this,input,latInput,lngInput]);
			
			}
			
			function initHALOPlaceSearch($ele){
				var $this = jQuery($ele)
				var input = jQuery('.halo-field-location-name', $this);
				var latInput = jQuery('.halo-field-location-lat', $this);
				var lngInput = jQuery('.halo-field-location-lng', $this);
				var controlId = $this.attr('id');

				var locations = new Bloodhound({
					datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
					queryTokenizer: Bloodhound.tokenizers.whitespace,
					remote: {
						url: halo.util.mergeUrlParams(halo_jax_targetUrl, {'term':'%QUERY', 'com':'autocomplete', 'func':'searchLocations', 
																		'csrf_token':jQuery('meta[name="csrf_token"]').attr('content') }),
						beforeSend: function (jqXhr, settings) {
							settings.data = JSON.stringify({
								"com": "autocomplete",
								"func": "searchLocations",
								csrf_token: jQuery('meta[name="csrf_token"]').attr('content')
							});
							return true;
						}
					},
				});
				locations.initialize();

				function suggestionTemplate(context) {
					return Hogan.compile('<p class="name">{{name}}</p>').render(context);
				};

				function showMap(lat, lng, title, canvasId) {
					halo.location.loadScript(function () {
						var myLatlng = new google.maps.LatLng(lat, lng);
						var myOptions = {
							zoom: 16,
							center: myLatlng,
							mapTypeId: google.maps.MapTypeId.ROADMAP
						}
						var map = new google.maps.Map(document.getElementById(canvasId), myOptions);
						var marker = new google.maps.Marker({
							position: myLatlng,
							map: map,
							draggable: true,
							title: title
						});
					});
				}

				input.typeahead({highlight: true}, {
					name: 'locations',
					displayKey: 'name',
					source: locations.ttAdapter(),
					templates: {
						header: '<div class="halo-location-suggestion-header"><a href="javascript:void(0)" onclick="halo.location.showCheckin(\'' + controlId + '\');return false;">' + __halotext('Click here to check in your location') + '</a></div>',
						empty: '<div class="halo-location-not-found text-center">' + __halotext('No Location matched') + '</div>',
						suggestion: suggestionTemplate,
						footer: '<div id="halo-location-suggestion" class="halo-location-suggestion hidden" style="height:150px"></div>'
					},
					engine: Hogan
				})
					.on('typeahead:cursorchanged', function (object, datum) {
						jQuery('#halo-location-suggestion').removeClass('hidden');
						showMap(datum.lat, datum.lng, datum.name, "halo-location-suggestion");
					})
					.on('typeahead:selected', function (object, datum) {
						halo.location.setLocationData(controlId, datum);
					})
					.on('typeahead:autocompleted', function (object, datum) {
						halo.location.setLocationData(controlId, datum);
					})
					.on('keydown', function (e) {
						var code = e.keyCode || e.which;
						if (code == halo.keycode.ENTER && !e.shiftKey) { //Enter keycode without shift
							e.preventDefault();
							if (jQuery.trim(jQuery(this).val()) == '') {
								//clear location setting
								halo.location.setLocationData(controlId, {lng: '', lat: '', name: ''});
								return false;
							}
						}
					})
				;
			
			}
			
		},
		getDistance: function (pos1, pos2) {
			// r = radius of the earth in statute miles
			var r = 3963.0;
			// Convert lat or lng from decimal degrees into radians (divide by 57.2958)
			var lat1 = pos1.lat() / 57.2958;
			var lng1 = pos1.lng() / 57.2958;
			var lat2 = pos2.lat() / 57.2958;
			var lng2 = pos2.lng() / 57.2958;
			// distance = circle radius from center to Northeast corner of bounds
			var dis = r * Math.acos(Math.sin(lat1) * Math.sin(lat2) +
				Math.cos(lat1) * Math.cos(lat2) * Math.cos(lng2 - lng1));
			return dis;
		}
	}
});

/* ============================================================ emoji features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	emoji: {
		setup: false, runSetup: function () {
			if (!halo.emoji.setup) {
				emojify.setConfig({

					emojify_tag_type: 'div',           // Only run emojify.js on this element
					only_crawl_id: null,            // Use to restrict where emojify.js applies
					img_dir: halo_assets_url + '/images/emoji',  // Directory for emoji images
					ignored_tags: {                // Ignore the following tags
						'SCRIPT': 1,
						'TEXTAREA': 1,
						'A': 1,
						'PRE': 1,
						'CODE': 1
					}
				});
				halo.emoji.setup = true;
			}
		}, 
		init: function (scope) {
			halo.emoji.runSetup();
			//init emojify
			emojify.run(jQuery(scope)[0]);
		}, 
		popular: [ 'smile', 'scream', 'bamboo', 'heart_eyes', 'grin', 'kissing_heart', 'smiley', 'wink', 'grinning', 'sweet_potato', 'stuck_out_tongue_winking_eye', 'smirk', 'ok_hand'
			, 'stuck_out_tongue_closed_eyes', 'clap', 'raised_hands', 'dancer', 'joy', 'kissing_closed_eyes', 'pensive', 'skull', 'unamused', 'full_moon_with_face', 'princess'
			, 'angry', 'kiss', 'flushed', 'weary', 'see_no_evil', 'hear_no_evil', 'stuck_out_tongue', 'disappointed', 'sleepy', 'kissing', 'relieved', 'cry', 'rage', '-1', '+1', 'muscle'
			, 'facepunch', 'sunglasses', 'cupid', 'broken_heart', 'fire', 'two_hearts', 'v', 'heart'
		]
	}
});

/* ============================================================ markdown editor features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	markdown: {
		init: function (scope) {
			if(!halo.util.isMobile()){
				if (typeof HTMLDocument !== 'undefined' && scope instanceof HTMLDocument) {
					//skip init on document scope, it should be inited by css framework
				} else {
					jQuery('textarea[data-provide="markdown"]', jQuery(scope)).markdown();
				}
			}
		}
	}
});

/* ============================================================ json table features
 *
 * ============================================================ */
!function ($) {

	"use strict"; // jshint ;_;


	/* Jsontable CLASS DEFINITION
	 * ========================= */

	var tablecell = '[data-jsontable] td,[data-jsontable] th'
		, Jsontable = function (element, options) {
			this.$element = jQuery(element)
			this.options = options

			this.init(element, options)
		}

	Jsontable.DEFAULTS = {
		interval: 5000,
		jsonreadonly: false,
		jsoninput: null
	}

	Jsontable.prototype = {

		constructor: Jsontable, init: function (element, options) {
			var $this = jQuery(this)
			renderTable($this, element, options)

			return false
		}, addRow: function ($table, pos) {
			var $newRow = jQuery('<tr></tr>');
			jQuery('th', $table).each(function () {
				$newRow.append('<td></td>');
			});
			var $rows = jQuery('tr', $table);
			if (!$rows.length || typeof $rows[pos] == 'undefined') {
				//insert new row to the end
				jQuery('tbody', $table).append($newRow);
			} else {
				jQuery($rows[pos]).after($newRow);
			}

			updateInput($table);
			return $newRow;
		}, deleteRows: function ($table) {
			var $rows = jQuery('tr.info', $table);
			if ($rows.length) $rows.remove();
			updateInput($table);
		}, selectRow: function (e) {
			var $e = jQuery(this);
			var $table = $e.closest('[data-jsontable]');
			jQuery('tr', $table).removeClass('info');
			$e.closest('tr').addClass('info');
		}, inlineEdit: function (e) {
			var targetElement = jQuery(this);
			var $table = targetElement.closest('[data-jsontable]');
			var readonly = $table.attr('data-jsonreadonly');
			if (typeof readonly !== 'undefined' && readonly !== false) {
				return false;
			}

			//create replaceWith editable input
			var replaceWith = jQuery('<input class="halo-inlineEditInput" type="text" />');

			//setup event handlers
			replaceWith.on('blur', function () {
				jQuery(this).trigger('inlineEdit.done');
			})

			replaceWith.on('keydown', function (e) {
				var code = e.keyCode || e.which;
				if ((code == halo.keycode.ENTER && !e.shiftKey) || code == halo.keycode.TAB) { //Enter keycode without shift and Tab keycode
					jQuery(this).trigger('inlineEdit.done');
					if (e.shiftKey) {
						//give focus on prev cell
						var cell = getPrevCell(targetElement, $table);
					} else {
						//give focus on next cell
						var cell = getNextCell(targetElement, $table);
					}
					if (cell !== null) {
						cell.trigger('click.jsontable.data-api');
					}
					return false;
				}
				if (code == halo.keycode.ESCAPE) { //Escape keycode
					jQuery(this).trigger('inlineEdit.cancel');
					e.stopImmediatePropagation();
					e.stopPropagation();
					return false;
				}
			});

			replaceWith.on('inlineEdit.done', function () {
				var text = jQuery(this).val();
				if (text == '') {
					text = String.fromCharCode(halo.keycode.NBSP);
				}
				targetElement.text(jQuery(this).val());
				targetElement.show();
				jQuery(this).remove();

				//update the jsoninput value
				var $table = targetElement.closest('[data-jsontable]');
				if ($table.length) {
					updateInput($table);
				}
			});

			replaceWith.on('inlineEdit.cancel', function () {
				targetElement.show();
				jQuery(this).remove();
			});
			var headerElement = jQuery(jQuery('th', $table)[targetElement.index()]);
			//display inline input

			var height = Math.max(targetElement.outerHeight(), headerElement.height()),
				width = Math.max(targetElement.outerWidth(), headerElement.width());

			targetElement.hide();
			var text = targetElement.text();
			if (text == String.fromCharCode(halo.keycode.NBSP)) {
				text = '';
			}
			replaceWith.css('height', height)
				.css('width', width)
				.val(text)
			targetElement.after(replaceWith);
			replaceWith.focus();

			//
			return false
		}

	}

	function updateInput($table) {
		var data = [];
		var $container = $table.closest('.halo-jsontable-container');
		var input = jQuery('[name="' + $table.attr('data-jsoninput') + '"]', $container);
		var readonly = $table.attr('data-jsonreadonly');
		if (input.length && (typeof readonly === 'undefined' || readonly === false)) {
			var rows = jQuery('thead,tr', $table);
			rows.each(function () {
				var cells = jQuery('th,td', jQuery(this));
				var rowData = []
				var isEmptyRow = true;
				cells.each(function () {
					var text = jQuery(this).text();
					if (text == String.fromCharCode(halo.keycode.NBSP)) {
						text = '';
					}
					rowData.push(text);
					if (text.length) {
						isEmptyRow = false;
					}
				});
				if (!isEmptyRow) {
					data.push(rowData);
				}
			});
			input.val(JSON.stringify(data));
		}
	}

	function getNextCell(cell, table) {
		var nextCell = cell.nextAll('td').first();
		if (!nextCell.length) {
			var nextRow = cell.parent().nextAll('tr').first();
			if (!nextRow.length) {
				//insert new row to the end of the table
				nextRow = Jsontable.prototype.addRow(table, -1);
			}
			nextCell = jQuery('td', nextRow).first();
		}
		return nextCell;

	}

	function getPrevCell(cell, table) {
		var prevCell = cell.prev('td');
		if (!prevCell.length) {
			var prevRow = cell.parent().prev('tr');
			if (!prevRow.length) {
				return cell;
			}
			prevCell = jQuery('td', prevRow).last();
		}
		return prevCell;

	}

	function renderTable($this, element, options) {
		var $table = jQuery(element);
		var $container = $table.closest('.halo-jsontable-container');
		var input = jQuery('[name="' + options.jsoninput + '"]', $container);
		if (input.length) {
			var data;
			try {
				data = jQuery.parseJSON(input.val());
			}
			catch (err) {
				//use data-default
				data = jQuery.parseJSON(input.attr('data-default'));
			}
			var $thead = jQuery('<thead></thead>');
			var $tbody = jQuery('<tbody></tbody>');
			if (jQuery.isArray(data) && data.length) {
				var hasBody = false;
				jQuery.each(data, function (index, row) {
					if (jQuery.isArray(row)) {
						//first elment is table header
						if (index == 0) {
							$thead = jQuery('<thead></thead>');
							jQuery.each(row, function (i, col) {
								$thead.append('<th>' + col + '</th>');
							})
						} else {
							var $tr = jQuery('<tr></tr>');
							jQuery.each(row, function (i, col) {
								$tr.append('<td>' + col + '</td>');
							})
							$tbody.append($tr);
							hasBody = true;
						}
					}
				});
				//incause no body, add empty row
				if (!hasBody) {
					var $emptyRow = jQuery('<tr></tr>');
					jQuery('th', $thead).each(function () {
						$emptyRow.append('<td></td>');
					});
					$tbody.append($emptyRow);
				}
			} else {
				//create default json table with 2 column
				$thead = jQuery('<thead><th></th><th></th></thead>');
				$tbody = jQuery('<tr><td></td><td></td></tr>');
			}
			$table.html($thead);
			$table.append($tbody);
			if (!jQuery('.halo-table-toolbar', $table.closest('.form-group')).length && !options.jsonreadonly) {
				var $toolbar = jQuery('<div class="halo-table-toolbar"></div>');
				input.after($toolbar);
				//add action buttons
				var addButton = jQuery('<a class="halo-btn halo-btn-default halo-btn-xs halo-jsontable-add-btn" href="javascript:void(0)" title="' + __halotext('Add New Row') + '"><i class="fa fa-plus-square"></i> </a>');
				addButton.on('click', function (e) {
					Jsontable.prototype.addRow($table, -1)
				});
				$toolbar.append(addButton);
				//delete action buttons
				var deleteButton = jQuery('<a class="halo-btn halo-btn-default halo-btn-xs halo-jsontable-delete-btn" href="javascript:void(0)" title="' + __halotext('Delete Selected Row') + '"><i class="fa fa-times-circle"></i> </a>');
				deleteButton.on('click', function (e) {
					Jsontable.prototype.deleteRows($table)
				});
				$toolbar.append(deleteButton);
				//insert action buttons
				var insertButton = jQuery('<a class="halo-btn halo-btn-default halo-btn-xs halo-jsontable-insert-btn" href="javascript:void(0)" title="' + __halotext('Insert Row Above') + '"><i class="fa fa-sign-in"></i> </a>');
				insertButton.on('click', function (e) {
					var rows = jQuery('tr', $table);
					var selectedRowPos = -1;
					rows.each(function (index, value) {
						if (jQuery(value).hasClass('info'))  selectedRowPos = index;
					});
					Jsontable.prototype.addRow($table, selectedRowPos);
				});
				$toolbar.append(insertButton);
				//clear action buttons
				var clearButton = jQuery('<a class="halo-btn halo-btn-default halo-btn-xs halo-jsontable-clear-btn" href="javascript:void(0)" title="' + __halotext('Erase table') + '"><i class="fa fa-eraser"></i> </a>');
				clearButton.on('click', function (e) {
					input.val(input.attr('data-default'));
					renderTable($this, element, options);
				});
				$toolbar.append(clearButton);

				//hint messages
				// $toolbar.append(jQuery('<span class="halo-jsontable-hint"><em> (' + __halotext('Double click on table cell to edit') + ') </em></span>'));

			}
		}


	}

	/* CONV PLUGIN DEFINITION
	 * ========================== */

	var old = jQuery.fn.jsontable

	jQuery.fn.jsontable = function (option) {
		return this.each(function () {
			var $this = jQuery(this)
				, data = $this.data('jsontable')
				, options = jQuery.extend({}, Jsontable.DEFAULTS, $this.data(), typeof option == 'object' && option)
			if (!data) $this.data('jsontable', (data = new Jsontable(this, options)))
			if (typeof option == 'string') data[option].call($this)
		})
	}


	jQuery.fn.jsontable.Constructor = Jsontable


	/* CONV NO CONFLICT
	 * ==================== */

	jQuery.fn.jsontable.noConflict = function () {
		jQuery.fn.jsontable = old
		return this
	}


	/* APPLY TO STANDARD Jsontable ELEMENTS
	 * =================================== */

	jQuery(document)
		.on('click.jsontable.data-api', tablecell, Jsontable.prototype.inlineEdit)
		.on('click.jsontable.data-api', tablecell, Jsontable.prototype.selectRow)

}(window.jQuery);

/* ============================================================ tab overflow feature
 * ========================================================= */
!function ($) {
	jQuery(document).on('ready', function (e) {
		cutOffOverflowTab();
	});
	jQuery(window).on('resize', halo.util.throttle(function () {
		cutOffOverflowTab();
	}, 500));
	function cutOffOverflowTab() {
		jQuery('.halo-tab-overflow').each(function () {
			var $this = jQuery(this);
			//check if the more dropdown exists
			var $currList = jQuery('.halo-overflow-dropdown > .halo-overflow-list', $this).first();
			var $tabsContainer = $this.children('.nav-tabs')
			if ($currList.length) {
				//just get all the drowdown list and append to tab container. (or reset the tab to original)
				$currList.children('li').appendTo($tabsContainer);
				$currList.closest('.halo-overflow-dropdown').remove();
			}

			var maxWidth = $this.width();

			var $moreList = jQuery('<ul class="halo-overflow-list halo-dropdown-menu" role="menu"></ul>');
			var $more = jQuery('<li class="halo-overflow-dropdown halo-dropdown"><a href="#" data-htoggle="dropdown">' + __halotext('More') + ' <b class="caret"></b></a></li>');
			$more.append($moreList);
			var moreWidth = 80;
			var sumWidth = 0;
			$tabsContainer.children('li').each(function () {
				var $li = jQuery(this);
				sumWidth = sumWidth + $li.width();
				if (sumWidth + moreWidth < maxWidth) {
					//space is available, just skip it
				} else {
					//no more space, put this tab to the dropdow
					$li.appendTo($moreList);
				}
			});
			if (sumWidth + moreWidth > maxWidth) {
				$tabsContainer.append($more);
			}

		})
	}
	function showTab(ele,dir){
		var currentTab = jQuery(ele);
		var targetTab = null;
		if(currentTab.length){
			if(dir == 1){
				targetTab = currentTab.next();
			} else {
				targetTab = currentTab.prev();
			}
			if(targetTab && targetTab.length){
				var targetTabId = targetTab.attr('id');				
				if(targetTabId){
					var targetTab = jQuery('[data-target="#'+targetTabId+'"]');
					if(targetTab.length){
						targetTab.htab('show');
					}
				}	
			}
		}
	}
	jQuery(document).on('swipeleft', '.halo-tab-overflow .halo-hammer', halo.util.throttle(function (e) {
		showTab(this,1);
	},300));
	jQuery(document).on('swiperight', '.halo-tab-overflow .halo-hammer', halo.util.throttle(function (e) {
		showTab(this,-1);
	},300));
	
}(window.jQuery);

/* ============================================================ stacked list feature
 * ========================================================= */
!function ($) {
	jQuery(document).on('click.halostackedlist.api', '.halo-stacked-items .halo-dropdown-menu > li', function (e) {
		cutOffOverflowedItems(jQuery(this).closest('.halo-stacked-list').parent(), jQuery(this));
	});
	jQuery(document).on('click.halostackedlist.api', '.halo-stacked-items .halo-dropdown-menu > li > .halo-dropdown-toggle', function (e) {
		//cutOffOverflowedItems(jQuery(this).closest('.halo-stacked-list').parent(),jQuery(this));
		var $li = jQuery(this).parent();
		cutOffOverflowedItems($li.closest('.halo-stacked-list').parent(), $li);
	});
	jQuery(document).on('ready', function (e) {
		cutOffOverflowedItems(document);
	});
	jQuery(window).on('resize', halo.util.throttle(function () {
		cutOffOverflowedItems(document);
	}, 500));
	function cutOffOverflowedItems(scope, firstItem) {
		jQuery('.halo-stacked-list', scope).addBack('.halo-stacked-list').each(function () {
			var $this = jQuery(this);
			$this.css('visibility', 'hidden');
			var stackedClass = $this.attr('data-stacked-class');
			if (typeof stackedClass == 'undefined') stackedClass = 'halo-btn halo-btn-default';
			//check if the more dropdown exists
			var $currList = jQuery('.halo-stacked-items > .halo-overflow-list', $this);
			var $listContainer = jQuery('.halo-list-container', $this);
			if ($currList.length) {
				//just get all the drowdown list and append to tab container. (or reset the tab to original)
				$currList.children().appendTo($listContainer);
				$currList.closest('.halo-stacked-items').remove();
			}

			var maxWidth = $this.width();
			var $moreList = jQuery('<ul class="halo-dropdown-menu halo-overflow-list" role="menu"></ul>');
			var $more = jQuery('<li class="halo-stacked-items halo-dropdown dropup"><a href="#" class="' + stackedClass + '" data-htoggle="dropdown"><i class="fa fa-th-list"></i></a></li>');
			$more.append($moreList);
			var moreWidth = 80;
			var sumWidth = 0;
			//prepare items
			var $itemsList = [];
			if (typeof firstItem !== 'undefined' && firstItem.length) {
				$itemsList.push(firstItem);
				firstItem.nextAll().each(function () {
					$itemsList.push(jQuery(this));
				})
				jQuery(firstItem.prevAll().get().reverse()).each(function () {
					$itemsList.push(jQuery(this));
				});
				//$itemsList = jQuery.merge($itemsList,);
				//$itemsList = jQuery.merge($itemsList,jQuery(firstItem.prevAll().get().reverse()));
			} else {
				$itemsList = jQuery.merge($itemsList, jQuery('.halo-list-container > li', $this));
			}
			jQuery.each($itemsList, function () {
				var $li = jQuery(this);
				sumWidth = sumWidth + $li.width();
				if (sumWidth + moreWidth < maxWidth) {
					//space is available, just skip it
					$li.appendTo($listContainer);
				} else {
					//no more space, put this tab to the dropdow
					$li.appendTo($moreList);
				}
			});
			if ($moreList.children().length && (sumWidth + moreWidth > maxWidth)) {
				$listContainer.append($more);
			}
			$this.css('visibility', 'visible');

		})
	}

	// STACKEDLIST PLUGIN DEFINITION
	// =========================

	var old = jQuery.fn.hpopover

	jQuery.fn.haloStackedList = function (options) {
		return this.each(function () {
			cutOffOverflowedItems(jQuery(this))
		})
	};

}(window.jQuery);

/* ============================================================ gb group checbox
 * ========================================================= */
!function ($) {
	function updateCtrlState(groupcheck) {
		var $ctrl = jQuery('[data-halo-groupcheck="' + groupcheck + '"].halo-groupcheck-ctrl');
		if (!$ctrl.length) return;
		//get checked list
		var checkedList = jQuery('[data-halo-groupcheck="' + groupcheck + '"].halo-groupcheck-ele:checked');
		var list = jQuery('[data-halo-groupcheck="' + groupcheck + '"].halo-groupcheck-ele');
		if (list.length) {
			if (list.length == checkedList.length) {
				//checked all
				$ctrl.prop('checked', true);
				$ctrl.removeClass('halo-groupcheck-partial');
			} else if (checkedList.length) {
				//partial checked
				$ctrl.prop('checked', true);
				$ctrl.addClass('halo-groupcheck-partial');
			} else {
				//unchecked all
				$ctrl.prop('checked', false);
				$ctrl.removeClass('halo-groupcheck-partial');
			}
		}
	}

	function init(scope) {
		jQuery('[data-halo-groupcheck]', scope).each(function () {
			updateCtrlState(jQuery(this).attr('data-halo-groupcheck'));
		});
	}

	/* CONV PLUGIN DEFINITION
	 * ========================== */

	var old = jQuery.fn.groupcheck

	jQuery.fn.groupcheck = function (option) {
		return this.each(function () {
			init(this);
		})
	}

	jQuery(document).on('click', '.halo-groupcheck-ctrl', function (e) {
		var $this = jQuery(this);
		var groupcheck = $this.attr('data-halo-groupcheck');
		var checked = $this.prop('checked');
		$this.removeClass('halo-groupcheck-partial');
		jQuery('[data-halo-groupcheck="' + groupcheck + '"].halo-groupcheck-ele').prop('checked', checked);
	})
	jQuery(document).on('click', '.halo-groupcheck-ele', function (e) {
		var groupcheck = jQuery(this).attr('data-halo-groupcheck');
		if (groupcheck.length) {
			updateCtrlState(groupcheck);
		}
	})
	jQuery(document).on('ready', function () {
		init(this);
	});

}(window.jQuery);

/* ============================================================ bootstrap hover dropdown
 * Project: Bootstrap Hover Dropdown
 * Author: Cameron Spear
 * Contributors: Mattia Larentis
 *
 * Dependencies: Bootstrap's Dropdown plugin, jQuery
 *
 * A simple plugin to enable Bootstrap dropdowns to active on hover and provide a nice user experience.
 *
 * License: MIT
 *
 * http://cameronspear.com/blog/bootstrap-dropdown-on-hover-plugin/
 */
(function ($, window, undefined) {
	// outside the scope of the jQuery plugin to
	// keep track of all dropdowns
	var $allDropdowns = jQuery();

	// if instantlyCloseOthers is true, then it will instantly
	// shut other nav items when a new one is hovered over
	jQuery.fn.dropdownHover = function (options) {
		// don't do anything if touch is supported
		// (plugin causes some issues on mobile)
		if ('ontouchstart' in document) return this; // don't want to affect chaining

		// the element we really care about
		// is the dropdown-toggle's parent
		$allDropdowns = $allDropdowns.add(this.parent());

		return this.each(function () {
			var $this = jQuery(this),
				$parent = $this.parent(),
				defaults = {
					delay: 500,
					instantlyCloseOthers: true
				},
				data = {
					delay: jQuery(this).data('delay'),
					instantlyCloseOthers: jQuery(this).data('close-others')
				},
				showEvent = 'show.bs.hdropdown',
				hideEvent = 'hide.bs.hdropdown',
			// shownEvent  = 'shown.bs.hdropdown',
			// hiddenEvent = 'hidden.bs.hdropdown',
				settings = jQuery.extend(true, {}, defaults, options, data),
				timeout;

			$parent.hover(function (event) {
				// so a neighbor can't open the dropdown
				if (!$parent.hasClass('open') && !$this.is(event.target)) {
					// stop this event, stop executing any code
					// in this callback but continue to propagate
					return true;
				}

				$allDropdowns.find(':focus').blur();

				if (settings.instantlyCloseOthers === true)
					$allDropdowns.removeClass('open');

				window.clearTimeout(timeout);
				$parent.addClass('open');
				$this.trigger(showEvent);
			}, function () {
				timeout = window.setTimeout(function () {
					$parent.removeClass('open');
					$this.trigger(hideEvent);
				}, settings.delay);
			});

			// this helps with button groups!
			$this.hover(function () {
				$allDropdowns.find(':focus').blur();

				if (settings.instantlyCloseOthers === true)
					$allDropdowns.removeClass('open');

				window.clearTimeout(timeout);
				$parent.addClass('open');
				$this.trigger(showEvent);
			});

			// handle submenus
			$parent.find('.dropdown-submenu').each(function () {
				var $this = jQuery(this);
				var subTimeout;
				$this.hover(function () {
					window.clearTimeout(subTimeout);
					$this.children('.halo-dropdown-menu').show();
					// always close submenu siblings instantly
					$this.siblings().children('.halo-dropdown-menu').hide();
				}, function () {
					var $submenu = $this.children('.halo-dropdown-menu');
					subTimeout = window.setTimeout(function () {
						$submenu.hide();
					}, settings.delay);
				});
			});
		});
	};

	jQuery(document).ready(function () {
		// apply dropdownHover to all elements with the data-hover="dropdown" attribute
		jQuery('[data-hover="dropdown"]').dropdownHover();
	});
})(jQuery, this);

/* ============================================================ toggle display features
 *
 * ============================================================ */
!function ($) {

	"use strict";
	var displayEle = '[data-htoggle="display"]'

	function toggleDisplay(e) {
		var $this = jQuery(this);
		var targetSelector = $this.attr('data-target');
		var $target;
		if (typeof targetSelector !== 'undefined') {
			$target = jQuery(targetSelector);
		} else {
			$target = jQuery(this).nextAll('.halo-toggle-display').first();
		}
		var clickEveryWhere = function (e) {
			if (!jQuery(e.target)[0] == $this[0] && jQuery(e.target).closest($this).length === 0) {
				//hide
			}
		}

		var isHidden = $target.hasClass('hidden');
		//hide all sibling content
		var siblingsSelector = jQuery(this).attr('data-siblings');
		jQuery(siblingsSelector).addClass('hidden');
		//toggle display this content
		if (isHidden) {
			$target.removeClass('hidden');
			$target.trigger('shown.display.halo');

		} else {
			$target.addClass('hidden');
			$target.trigger('hidden.display.halo');
		}
	}


	jQuery(document)
		.on('click.halodisplay.data-api', displayEle, toggleDisplay)
//    .on('mouseenter.halodisplay.data-api'  , displayEle, toggleDisplay)

}(window.jQuery);

/* ============================================================ toggle ui features
 *
 * ============================================================ */
!function ($) {

	"use strict";
	var eleSelector = '[data-uitoggle]'
	var __toggleUISettings = {};
	function toggleUIHandler(event) {
		toggleUI(this);
	}
	function toggleUI(ele, retries, stopPropagate) {
		var $ele = jQuery(ele);
		var targetSelector = $ele.attr('data-uitoggle');
		retries = retries?retries:5;		//max retries = 5
		if(!targetSelector) return;
		//check for toggle UI settings exists
		var parts = targetSelector.split('.');
		var uiSettings;
		if(parts.length > 1) {		//valid uitoogle key format
			var pkg = parts[0];
			var uiKey = parts.slice(1).join('.');
			if(typeof __toggleUISettings[pkg] !== 'undefined') {
				//settings already loaded
				uiSettings = __toggleUISettings[pkg][uiKey];
				//search for similar UIs and apply soft toggle on them
				var uiKey = $ele.attr('data-uikey');
				if(!stopPropagate && uiKey) {
					var similarSelector = '[data-uitoggle^="' + parts[0] + '.' + parts[1] + '"][data-uikey="' + uiKey + '"]';
					var $propagation = jQuery(similarSelector);
					if($propagation.length) {
						$propagation.each(function() {
							var $this = jQuery(this);
							if($this[0] != $ele[0]) {
								toggleUI($this, 5, true);
							}
						});
					}
				}
				
				applyUISetting($ele, uiSettings);

			} else {
				//UI setttings is not loaded, request the pkg from server
				retries -- ;
				if(retries) {
					halo.jax.call('system', 'loadUISettings', pkg, halo.util.throttle(function() {
						toggleUI($ele, retries);
					}, 100));
				}
			}
		}
	}
	
	function applyUISetting($ele, uiSettings) {
		//check for function exist
		if(uiSettings && uiSettings['func'] && halo.uitoggle[uiSettings['func']]) {
			halo.uitoggle[uiSettings['func']].apply($ele, [uiSettings]);
		}
	}


	jQuery(document)
		.on('click', eleSelector, toggleUIHandler);
	
	function searchAndReplaceElement(ele, sVal, nVal) {
		for(var i = 0; i < sVal.length; i++){
			if(nVal[i]) {
				eleHtml = eleHtml.replace(sVal[i], nVal[i]);
			}
		}
	}
	jQuery.extend(true, halo, {
		uitoggle: {
			savePkg: function(pkg, data) {
				var obj = JSON && JSON.parse(data) || jQuery.parseJSON(data);
				__toggleUISettings[pkg] = obj;
			},
			toggle: function(settings) {
				var $this = jQuery(this);
				var eleHtml = $this.outerHTML();
				if(settings['states'] && jQuery.isArray(settings['states'])) {
					var states = settings['states'];
					//find the current state
					var i = 0;
					var found = -1;
					while( i < states.length && found === -1) {
						if(jQuery.isArray(states[i])) {
							var j = 0;
							var skip = false;
							while (j < states[i].length && !skip) {
								if(eleHtml.search(states[i][j]) < 0) {
									skip = true;
									break;
								}
								j++;
							}
							if(!skip) {
								found = i;
								break;
							}
							i++;
						}
					}
					if(found >= 0) {
						var newState = (found + 1) % states.length;
						$this.haloReplaceHtml(states[found], states[newState]);
					}
				}
			},
			replace: function(settings) {
				var $this = jQuery(this);
				$this.haloReplaceHtml(settings['sVal'], settings['nVal']);
			},
			hide: function(settings) {
				jQuery(this).hide();
			}
		}
	});
}(window.jQuery);

/* ============================================================ extended bootstrap tab features
 *
 * ============================================================ */
!function ($) {

	"use strict";

	jQuery(document)
		.on('shown.bs.htab', 'a[data-htoggle="tab"][data-ondisplay]', function () {
			var $this = jQuery(this);
			var handler = $this.attr('data-ondisplay');
			//halo.util.executeFunctionByName(handler
			//eval(handler);
			//update url
			halo.util.setUrlParam({}, halo.util.getQueryObj(this.href));
			var tmpFunc = new Function(handler);
			tmpFunc.context = $this;
			tmpFunc();
			
			//set page title if need
			var ptitle = $this.attr('data-title');
			if(ptitle){
				halo.util.updatePageTitle(ptitle);
			}
		})
	jQuery(document).on('ready', function () {
		var $active = jQuery('.active a[data-htoggle="tab"][data-ondisplay]');
		var handler = $active.attr('data-ondisplay');
		//update url
		if ($active.length) {
			//halo.util.setUrlParam({}, halo.util.getQueryObj($active[0].href));
			var tmpFunc = new Function(handler);
			tmpFunc.context = $active;
			tmpFunc();
		}
	})

}(window.jQuery);

/* ============================================================ ajax pagination features
 *
 * ============================================================ */
!function ($) {

	"use strict";
	jQuery(document)
		.on('click.halo.pagination', 'a.halo-pagination-link[data-pagination-page]', halo.util.throttle(function (event) {
			event.preventDefault();
			var $this = jQuery(this);
			var page = $this.attr('data-pagination-index');
			var $form = $this.closest('form');
			var keepUrl = false;
			//consider to change url or not
			if($this.hasClass('halo-pagination-auto')){
				keepUrl = true;
			}
			halo.util.changePage($form, page, keepUrl);
			}, 1000)
		)

		.on('click.halo.pagination', 'a.halo-paging-nav-prev, a.halo-paging-nav-next', function (event) {
			event.preventDefault();
			var $this = jQuery(this);
			var page = $this.attr('data-pagination-index');

			var wrapper = $this.closest('.halo-section-container');
			if(!wrapper.length) return;
			var $form = jQuery('.halo-pagniation-frm',wrapper);
			
			var keepUrl = false;
			//consider to change url or not
			if($this.hasClass('halo-pagination-auto')){
				keepUrl = true;
			}
			halo.util.changePage($form, page, keepUrl);
		})

}(window.jQuery);

/* ============================================================ filter features
 *
 * ============================================================ */
!function ($) {

	"use strict";
	var displayEle = '[data-htoggle="filter"]'
	var filterItem = '.halo-filter-content .halo-filter-toggle[id]'
	var filterText = '.halo-filter-content .halo-filter-text[id]'
	var filterTag = '.halo-filter-content .halo-filter-tag[id]'
	var filterSlider = '.halo-filter-content .halo-filter-slider[id]'
	var filterLocation = '.halo-filter-content .halo-filter-location[id]'
	var checkedIcon = '<i class="fa fa-check-square-o text-success checked-filter"></i>'
	var unCheckedIcon = '<i class="fa fa-square-o"></i>'
	var inited = false;
	var tagManagers = {};

	function toggleContent(e) {
		var filterContentId = jQuery(this).attr('data-filter-content');
		jQuery('#' + filterContentId).toggleClass('hidden');
	}

	function toggleItem(e) {
		var $this = jQuery(this);
		var $singleContainer = $this.closest('.halo-single-select');
		//for multiple select
		if ($singleContainer.length) {
			//for single select
			//uncheck all in the same level
			jQuery('.halo-filter-toggle', $singleContainer).each(function (index, ele) {
				//only clear state for other checkbox
				if (ele != $this[0])
					setState(jQuery(ele), 'unchecked');
			});
		}

		//check the current one
		if (isChecked($this)) {
			setState($this, 'unchecked');
		} else {
			setState($this, 'checked');
		}

		//trigger on change event
		var $filterLabel = getFilterLabel($this);
		$filterLabel.trigger('change');
	}

	function changeText(e) {
		var $this = jQuery(this);
		var $label = getFilterLabel($this);

		var tagManager = getTagManager($label);

		//update the filter value
		$this.attr('data-halo-value', $this.val());
		var tagId = getTagId($this);
		var lbl = $this.val();

		//get label
		var dataLabel = $this.attr('data-halo-label');
		if(dataLabel){
			lbl = '[' + dataLabel + ': ' + lbl + ']';
		} else {
			var $filterTitle = jQuery('.halo-filter-title', $this.closest('.halo-filter-rule'));
			lbl = $filterTitle.length ? '[' + $filterTitle.text() + ': ' + lbl + ']' : lbl;
		}
		//lable could not have comma character, just replace it with dash character
		lbl = lbl.replace(/,/g,' - ');

		var value = $this.val();
		if (value == '') {		//mean change text to clear tag
			//just remove the current tag if exists
			tagManager.tagsManager("spliceTag", tagId);
		} else {
			//always remove the current tag first (silent mode)
			tagManager.tagsManager("spliceTagSilent", tagId);
			//restore old value
			$this.val(value);
			if (isChecked($this)) {
				tagManager.tagsManager("pushTag", lbl, false, tagId);
			}
		}
		//trigger on change event
		$label.trigger('change');
	}
	
	function changeTag(e) {
		var $this = jQuery(this);
		var $label = getFilterLabel($this);

		var tagManager = getTagManager($label);
		//update the filter value
		$this.attr('data-halo-value', $this.val());
		var tagId = getTagId($this);
		var lbl = $this.val();

		//get label
		var dataLabel = $this.attr('data-halo-label');
		if(dataLabel){
			lbl = '' + dataLabel + ': ';
		} else {
			var $filterTitle = jQuery('.halo-filter-title', $this.closest('.halo-filter-rule'));
			lbl = $filterTitle.length ? '' + $filterTitle.text() + ': ' : lbl;
		}
		var value = $this.val();
		if (value == '') {		//mean change text to clear tag
			//just remove the current tag if exists
			//tagManager.tagsManager("spliceTag", tagId);
		} else {
			//get current tag input
			var $tagInputs = jQuery('input[data-tag-orgid="'+tagId+'"]');
			var newTagValues = value.split(',');
			$tagInputs.each(function(){
				var $this = jQuery(this);
				var val = $this.val();
				var index = newTagValues.indexOf(val);
				if(index > -1){
					//no change, just remove this index from new tag values list
					newTagValues.splice(index,1);
				} else {
					var removeTagId = $this.attr('id');
					tagManager.tagsManager("spliceTagSilent", removeTagId);
				}
			});
			if(newTagValues.length){
				jQuery.each(newTagValues, function(index,value){
					var newTagId = halo.util.uniqID()
					jQuery('<input class="halo-filter-tag-child" type="hidden" data-tag-orgid="'+tagId+'" id="'+newTagId+'" value="'+value+'">').insertAfter($this);
					tagManager.tagsManager("pushTag", '[#' + value + ']', false, newTagId);
				});
			}
			/*
			//always remove the current tag first (silent mode)
			tagManager.tagsManager("spliceTagSilent", tagId);
			//restore old value
			$this.val(value);
			if (isChecked($this)) {
				tagManager.tagsManager("pushTag", lbl, false, tagId);
			}
			*/
		}
		//trigger on change event
		$label.trigger('change');
	}

	function changeLocation(e) {
		var $this = jQuery(this);
		var $label = getFilterLabel($this);
		var tagManager = getTagManager($label);
		//update the filter value
		$this.attr('data-halo-value', $this.val());
		var tagId = getTagId($this);
		var lbl = $this.val();

		var $filterTitle = jQuery('.halo-filter-title', $this.closest('.halo-filter-rule'));
		lbl = $filterTitle.length ? '[' + $filterTitle.text() + ': ' + lbl + ']' : lbl;

		var value = $this.val();
		if (value == '') {		//mean change text to clear tag
			//just remove the current tag if exists
			tagManager.tagsManager("spliceTag", tagId);
		} else {
			//always remove the current tag first (silent mode)
			tagManager.tagsManager("spliceTagSilent", tagId);
			//restore old value
			$this.val(value);
			if (isChecked($this)) {
				tagManager.tagsManager("pushTag", lbl, false, tagId);
			}
		}
		//trigger on change event
		$label.trigger('change');
	}

	function getTagManager($label) {
		var id = $label.attr('id');
		//init a new tag manager if not exists
		if (typeof tagManagers[id] == 'undefined') {
			//init a new tag manager
			var tagManager = $label.tagsManager({
					onlyTagList: true,
					tagClass: 'tm-tag-info',
					tagsContainer: jQuery('.' + id),
					preventSubmitOnEnter: true,
					hiddenTagListName: '',
					externalTagId: true
				})
					.on('tm:popped', onRemoveFilterTag)
					.on('tm:spliced', onRemoveFilterTag)
					.on('tm:pushed', onAddFilterTag)
					.on('tm:delete', onDeleteFilterTag)
				;
			tagManagers[id] = tagManager;
		}
		
		function onDeleteFilterTag(event){
			//trigger on change event
			$label.trigger('change');
		}
		
		function onRemoveFilterTag(event, tagName, tagId) {
			var $this = jQuery('#' + tagId);
			var inputName = $this.attr('data-halo-input');
			var inputVal = $this.attr('data-halo-value');
			if(!inputName){	//halo-input is not defined, use $this as input
				var $input = $this;
				inputName = $this.attr('name');
			} else {
				var $input = jQuery('[name="' + inputName + '"]', $this.closest('.halo-filter'));
			}
			if ($input.length) {
				//remove tag might be triggered via tag list
				removeTag($this);
				//remove lbl
				var params = {};
				var val = jQuery.trim($input.val());
				if ($this.hasClass('halo-filter-toggle')) {
					$input.val(halo.util.remValFromArrText(inputVal, val));
				} else if ($this.hasClass('halo-filter-text')) {
					$input.val($this.val());
				} else if ($this.hasClass('halo-filter-tag-child')) {
					var $oldInput = $input;
					//get origial tag input
					$input = jQuery('#'+$input.attr('data-tag-orgid'));
					inputName = $input.attr('name');
					//update input
					$input.val(halo.util.remValFromArrText($oldInput.val(), jQuery.trim($input.val()))).trigger('tm:sync');
					$oldInput.remove();
				} else if ($this.hasClass('halo-filter-location')) {
					$this.val('');
					$input.val($this.val());
					var $inputLat = $input.siblings('.halo-field-location-lat').val('')
					var $inputLng = $input.siblings('.halo-field-location-lng').val('')
					//set lng lat param
					params[$inputLat.attr('name')] = $inputLat.val();
					params[$inputLng.attr('name')] = $inputLng.val();
				}
				params[inputName] = $input.val();
				//update url, stream content
				updateUrl($this, params);
			}
		}

		function onAddFilterTag(event, tagName, tagId) {
			var $this = jQuery('#' + tagId);
			var inputName = $this.attr('data-halo-input');
			var inputVal = $this.attr('data-halo-value');
			if(!inputName){	//halo-input is not defined, use $this as input
				var $input = $this;
				inputName = $this.attr('name');
			} else {
				var $input = jQuery('[name="' + inputName + '"]', $this.closest('.halo-filter'));
			}
			if ($input.length) {
				var params = {};
				//add lbl
				var val = jQuery.trim($input.val());
				if ($this.hasClass('halo-filter-toggle')) {
					$input.val(halo.util.addValToArrText(inputVal, val));
				} else if ($this.hasClass('halo-filter-text')) {
					$input.val($this.val());
				} else if ($this.hasClass('halo-filter-tag-child')) {
					//get origial tag input
					$input = jQuery('#'+$input.attr('data-tag-orgid'));
					inputName = $input.attr('name');
				} else if ($this.hasClass('halo-filter-location')) {
					$input.val($this.val());
					var $inputLat = $input.siblings('.halo-field-location-lat')
					var $inputLng = $input.siblings('.halo-field-location-lng')
					//set lng lat param
					params[$inputLat.attr('name')] = $inputLat.val();
					params[$inputLng.attr('name')] = $inputLng.val();
				}
				params[inputName] = $input.val();
				//update url, stream content
				updateUrl($this, params);
			}
		}

		function updateUrl($ele, params) {
			if (inited) {
				//get the url params of this filter label
				var $label = getFilterLabel($ele);
				var oldParams = $label.data('oldParams');
				halo.util.setUrlParam(params, oldParams);
				//update the url params for this filter label
				$label.data('oldParams', halo.util.getCurrentQueryObj())
			}
		}

		return tagManagers[id];
	}

	function getTagId($ele) {
		return $ele.attr('id');
	}

	function getFilterLabel($ele) {
		var labelId = $ele.closest('.halo-filter-content').attr('data-filter-label');
		var $label = jQuery('#' + labelId);
		return $label;
	}

	function setState($ele, state) {
		var $this = $ele;
		var $isChecked = jQuery('i', $this).first();
		var $label = getFilterLabel($this);

		function updateFilterTag($ele) {
			//get text only
			var $title = $ele.hasClass('halo-tree-node') ? $ele.next() : $ele;
			var lbl = $title.clone()
				.children()
				.remove()
				.end()
				.text();
			var tagManager = getTagManager($label);

			var $filterTitle = jQuery('.halo-filter-title', $ele.closest('.halo-filter-rule'));
			lbl = $filterTitle.length ? '[' + $filterTitle.text() + ': ' + lbl + ']' : lbl;

			var inputName = $ele.attr('data-halo-input');
			var inputVal = $ele.attr('data-halo-value');
			var tagId = getTagId($ele);
			if (isChecked($ele)) {
				tagManager.tagsManager("pushTag", lbl, false, tagId);
			} else {
				tagManager.tagsManager("spliceTag", tagId);
			}

		}

		if (isChecked($this) && state != 'checked') {
			$isChecked.replaceWith(jQuery(unCheckedIcon));
		} else if (!isChecked($this) && state == 'checked') {
			$isChecked.replaceWith(jQuery(checkedIcon));
		} else {
			//new state = old state, do nothing
			return;
		}
		updateFilterTag($this);
	}

	function isChecked($ele) {
		//for toggle filter
		if ($ele.hasClass('halo-filter-toggle')) {
			var $isChecked = jQuery('i', $ele).first();
			return $isChecked.hasClass('checked-filter');
		}
		//for text filter
		else if ($ele.hasClass('halo-filter-text') || $ele.hasClass('halo-filter-tag')) {
			return $ele.val().length > 0;
		}
		//for location filter
		else if ($ele.hasClass('halo-filter-location')) {
			return $ele.val().length > 0;
		}
	}

	function removeTag($ele) {
		//for toggle filter
		if ($ele.hasClass('halo-filter-toggle')) {
			setState($ele, 'unchecked');
		}
		//for text filter
		else if ($ele.hasClass('halo-filter-text')) {
			$ele.val('');
		}

	}

	jQuery(document)
		.on('click.filterDisplay.data-api', displayEle, toggleContent)
		.on('click.filterItem.data-api', filterItem, toggleItem)
		.on('change.filterItem.data-api', filterText, changeText)
		.on('change.filterItem.data-api', filterTag, changeTag)
		.on('slideStop.filterItem.data-api', filterSlider, changeText)
		.on('location.change.halo', filterLocation, changeLocation)
		.ready(function () {
			jQuery('.halo-filter [name*="filter"]').each(function () {
				//simulate click event on selected on values
				var $input = jQuery(this);
				var filterName = $input.attr('name');
				var filterVal = jQuery.trim($input.val());
				var filterItems = filterVal.length ? filterVal.split(',') : [];
				jQuery.each(filterItems, function (index, val) {
					var $ele = jQuery('.halo-filter [data-halo-input="' + filterName + '"][data-halo-value="' + val + '"]')
					if ($ele.length) {
						//for toggle filter
						if ($ele.hasClass('halo-filter-toggle')) {
							setState($ele, 'checked');
						}
						//for text filter
						else if ($ele.hasClass('halo-filter-text')) {
							$ele.each(function (index, ele) {
								jQuery(ele).val(val);
								jQuery(ele).trigger('change');
							});
						} else if ($ele.hasClass('halo-filter-location')) {
							$ele.each(function (index, ele) {
								jQuery(ele).val(val);
								jQuery(ele).trigger('location.change.halo');
							});
						}
					}
				});

				//mark as inited
				inited = true;
			})
		})
}(window.jQuery);

/* ============================================================ google analytics features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	ga: {
		options: {    
			config: {trackingId: 'UA-XXXX-Y', cookieDomain: 'auto'}, hits: [],
		}, 
		inited: false, debug: false, loadedApi: false, init: function (scope) {
			//single init only
			if (!halo.ga.inited) {
				halo.ga.inited = true;
			} else {
				return;
			}
			var $e = jQuery('#halo_gatracking');
			var options = null;
			if ($e.length) {
				try {
					options = jQuery.parseJSON($e.attr('data-ga'));
					halo.ga.debug = parseInt($e.attr('data-gadebug'));
				} catch (ex) {
				}

			}
			if (typeof options !== 'undefined' && options != null) {
				halo.ga.setupConfig(options);
			}
			//bind social trigger event
			jQuery(document).on('halo_share', function (e, network) {
				var params = {'socialNetwork': network, 'socialAction': 'share', 'socialTarget': location.href};
				halo.ga.exe_social(params);
			});
		}, 
		setupConfig: function (options) {
			options = jQuery.extend({}, halo.ga.options, options);
			//init the ga tracking
			HALOModernizr.load({
				load: ('https:' == location.protocol ? '//ssl' : '//www') + '.google-analytics.com/analytics.js',
				complete: function () {
					//create ga tracker object by using options.config
					setTimeout(function () {
						ga('create', options.config);

						//use options.hits to setup tracking behaviour
						var hits = options.hits;
						halo.ga.processHits(hits);
					}, 100);

				}
			});

		}, 
		processHits: function (hits) {
			if (!jQuery.isArray(hits)) {
				hits = jQuery.parseJSON(hits);
			}
			for (var i = 0; i < hits.length; i++) {
				var hit = hits[i];
				if (typeof halo['ga']['exe_' + hit.handler] !== 'undefined') {
					if (halo.ga.debug) {
						hit.params.hitCallback = function () {
							halo.log('sent data', hit.handler, hit.params);
						};
					}
					halo['ga']['exe_' + hit.handler].apply(this, [hit.params]);
				}
			}
		}, 
		exe_pageview: function (params) {
			//addintion params modification put here
			if(typeof ga !=='undefined'){
				ga('send', 'pageview', params);
			}
		}, 
		exe_event: function (params) {
			//addintion params modification put here
			if(typeof ga !=='undefined'){
				ga('send', 'event', params);
			}
		}, 
		exe_social: function (params) {
			//addintion params modification put here
			if(typeof ga !=='undefined'){
				ga('send', 'social', params);
			}
		}, 
		exe_timing: function (params) {
			//addintion params modification put here
			if(typeof ga !=='undefined'){
				ga('send', 'timing', params);
			}
		}, 
		getData: function (query, cb) {
			if (!halo.ga.loadedApi) {
				function initGGClientAPI() {
					var clientId = jQuery('#halo_ggClientId').attr('data-gg');
					var apiKey = jQuery('#halo_ggApiKey').attr('data-gg');
					var scopes = 'https://www.googleapis.com/auth/analytics.readonly';
					if (typeof clientId == 'undefined' || typeof apiKey == ' undefined') return {};
					// This function is called after the Client Library has finished loading
					// 1. Set the API Key
					gapi.client.setApiKey(apiKey);

					// 2. Call the function that checks if the user is Authenticated. This is defined in the next section
					window.setTimeout(checkAuth, 1);

					function checkAuth() {
						// Call the Google Accounts Service to determine the current user's auth status.
						// Pass the response to the handleAuthResult callback function
						gapi.auth.authorize({client_id: clientId, scope: scopes, immediate: true}, handleAuthResult);
					}

					function handleAuthResult(authResult) {
						if (authResult) {
							// The user has authorized access
							// Load the Analytics Client. This function is defined in the next section.
							jQuery('.authorize-wrapper').hide();
							loadAnalyticsClient();
						} else {
							// User has not Authenticated and Authorized
							handleUnAuthorized();

						}
					}

					// Unauthorized user
					function handleUnAuthorized() {
						var authorizeButton = jQuery('.authorize-button');
						var authorizeWrapper = jQuery('.authorize-wrapper');

						// Show the 'Authorize Button' and hide the 'Get Visits' button
						authorizeWrapper.show();

						// When the 'Authorize' button is clicked, call the handleAuthClick function
						authorizeButton.on('click', handleAuthClick);
					}

					function handleAuthClick(event) {
						gapi.auth.authorize({client_id: clientId, scope: scopes, immediate: false}, handleAuthResult);
						return false;
					}

					function loadAnalyticsClient() {
						// Load the Analytics client and set handleAuthorized as the callback function
						gapi.client.load('analytics', 'v3', makeApiCall);
					}
				}

				HALOModernizr.load([
					{
						load: ['https://apis.google.com/js/client.js'],
						complete: function () {
							//workaround solution, wait for 0.1 seconds before the google api is loaded properly
							setTimeout(function () {
								initGGClientAPI();
								halo.ga.loadedApi = true;
							}, 100);
						}
					}
				])
			} else {
				makeApiCall();
			}
			function makeApiCall() {
				var restRequest = gapi.client.request({
					'path': '/analytics/v3/data/ga',
					'params': query
				});
				restRequest.execute(handleCoreReportingResults);
				//var apiQuery = gapi.client.analytics.data.ga.get(query);
				//apiQuery.execute(handleCoreReportingResults);
			}

			function handleCoreReportingResults(results) {
				if (!results.error) {
					// Success. Do something cool!
					if (jQuery.isFunction(cb)) {
						cb.apply(this, [results]);
					}
				} else {
					//alert('There was an error: ' + results.message);
					if (jQuery.isFunction(cb)) {
						cb.apply(this, [results]);
					}
					return false;
				}
			}
		}
	}
});

/* ============================================================ auto hide feature
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	autohide: {
		init: function (scope) {
			$ele = jQuery('.halo-autohide',scope);
			if($ele.length){
				setTimeout(function(){
					jQuery('.halo-autohide').remove();
				},5000);
			}
		}
	}
});

/* ============================================================ tagging feature
 *
 * ============================================================ */
+function ($) {
	"use strict";

	// TAGGING PUBLIC CLASS DEFINITION
	// ===============================

	var Tagging = function (element, options) {
		this.tagApi =
			this.type =
				this.options =
					this.enabled =
						this.$element = null

		this.init('tagging', element, options)
	}

	Tagging.DEFAULTS = {
		selector: false, template: '<div class="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>', taggingFilters: '', trigger: 'hover focus', title: '', delay: 0, html: false, container: false
	}

	Tagging.prototype.init = function (type, element, options) {
		this.enabled = true
		this.type = type
		this.$element = jQuery(element)
		this.options = this.getOptions(options)
		var $element = this.$element;
		var that = this;
		//add tagging input element
		var input = jQuery('<input name="tagginginput" value="" class="form-control">')
			.insertBefore($element);
		$element.attr('type', 'hidden');		//make sure the real input is in hidden state
		var $tagsContainer = $element.parent();
		that.tagApi = input.tagsManager({
			onlyTagList: true,
			tagClass: 'tm-tag-info',
			tagsContainer: $tagsContainer,
			preventSubmitOnEnter: true,
			externalTagId: true
		})
			.on('tm:popped', function (tag, tagId) {
				$element.val(that.tagApi.data("tlid").join(','));
			})
			.on('tm:spliced', function (tag, tagId) {
				$element.val(that.tagApi.data("tlid").join(','));
			})
		//init typeahead
		var tags = new Bloodhound({
			datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
			queryTokenizer: Bloodhound.tokenizers.whitespace,
			remote: {
				url: halo.util.mergeUrlParams(halo_jax_targetUrl, {'term':'%QUERY', 'com':'autocomplete', 'func':'searchUsers', 
																'csrf_token':jQuery('meta[name="csrf_token"]').attr('content') + (that.options.taggingFilters.length ? ('&' + that.options.taggingFilters) : '')}),
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
		tags.initialize();
		input.typeahead({highlight: true}, {
			name: 'tags',
			displayKey: 'name',
			source: tags.ttAdapter(),
			templates: {
				empty: that.emptyTemplate(),
				suggestion: that.suggestionTemplate
			},
			engine: Hogan
		})
			.on('typeahead:selected', addTag)
			.on('typeahead:autocompleted', addTag)
			.on("focusout", function () {
				that.doneTagging();
			})
			.on('keydown', function (e) {
				var code = e.keyCode || e.which;
				if (code == halo.keycode.ENTER && !e.shiftKey) { //Enter keycode without shift
					e.preventDefault();
					if (jQuery.trim(jQuery(this).val()) == '') {
						//clear location setting
						that.doneTagging();
						return false;
					}
				}
			})

		function addTag(e, datum) {
			that.addTag(datum);
			input.trigger('tagadded.halo');
		}

		//add tags

	}

	Tagging.prototype.getDefaults = function () {
		return Tagging.DEFAULTS
	}

	Tagging.prototype.getOptions = function (options) {
		options = jQuery.extend({}, this.getDefaults(), this.$element.data(), options)

		return options
	}

	Tagging.prototype.addTag = function (datum) {
		this.tagApi.tagsManager("pushTag", datum.name, false, datum.id);
		this.$element.val(this.tagApi.data("tlid").join(','));

		// Removeable after added tag
		var newTagRemoveId = this.tagApi.data("tm_rndid") + '_Remover_' + datum.id;
		var that = this;
		jQuery('#' + newTagRemoveId).on('click', function(e) {
			that.spliceTag(datum.id);
			jQuery(this).closest('.tm-tag').remove();
		});
	}

	Tagging.prototype.suggestionTemplate = function (context) {
		return Hogan.compile('<p class="name"><image class="halo-suggestion-img" src={{image}}>{{name}}</p>').render(context);
	}

	Tagging.prototype.emptyTemplate = function () {
		return '<div class="halo-location-not-found text-center">' + __halotext('No User Matched') + '</div>';
	}

	Tagging.prototype.doneTagging = function () {
		return true;
	}

	Tagging.prototype.spliceTag = function(tagId) {
		this.tagApi.tagsManager('spliceTag', tagId);
	}


	// TAGGING PLUGIN DEFINITION
	// =========================

	var old = jQuery.fn.tagging

	jQuery.fn.tagging = function (option) {
		return this.each(function () {
			var $this = jQuery(this)
			var data = $this.data('bs.tagging')
			var options = typeof option == 'object' && option

			if (!data) $this.data('bs.tagging', (data = new Tagging(this, options)))
			if (typeof option == 'string') data[option]()
		})
	}

	jQuery.fn.tagging.Constructor = Tagging


	// TAGGING NO CONFLICT
	// ===================

	jQuery.fn.tagging.noConflict = function () {
		jQuery.fn.tagging = old
		return this
	}

}(window.jQuery);

//HaloSocial customization
+function($) {jQuery(document).ready(function(){

/* ============================================================ override popover bootstrap
 * Bootstrap: popover.js v3.0.0
 * http://twbs.github.com/bootstrap/javascript.html#popovers
 * ========================================================================
 * Copyright 2012 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ======================================================================== */

+function ($) {
	"use strict";

	// POPOVER PUBLIC CLASS DEFINITION
	// ===============================

	var Popover = function (element, options) {
		this.init('hpopover', element, options)
	}

	if (!jQuery.fn.tooltip) throw new Error('Popover requires tooltip.js')

	Popover.DEFAULTS = jQuery.extend({}, jQuery.fn.tooltip.Constructor.DEFAULTS, {
		placement: 'right', trigger: 'click', content: '', template: '<div class="popover"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
	})


	// NOTE: POPOVER EXTENDS tooltip.js
	// ================================

	Popover.prototype = jQuery.extend({}, jQuery.fn.tooltip.Constructor.prototype)

	Popover.prototype.constructor = Popover

	Popover.prototype.getDefaults = function () {
		return Popover.DEFAULTS
	}

	Popover.prototype.setContent = function () {
		var $tip = this.tip()
		var title = this.getTitle()
		var content = this.getContent()

		$tip.find('.popover-title')[this.options.html ? 'html' : 'text'](title)
		$tip.find('.popover-content')[this.options.html ? 'html' : 'text'](content)

		$tip.removeClass('fade top bottom left right in')

		// IE8 doesn't accept hiding via the `:empty` pseudo selector, we have to do
		// this manually by checking the contents.
		if (!$tip.find('.popover-title').html()) $tip.find('.popover-title').hide()
	}

	Popover.prototype.applyPlacement = function (offset, placement) {
		var replace
		var $tip = this.tip()
		var width = $tip[0].offsetWidth
		var height = $tip[0].offsetHeight

		// manually read margins because getBoundingClientRect includes difference
		var marginTop = parseInt($tip.css('margin-top'), 10)
		var marginLeft = parseInt($tip.css('margin-left'), 10)

		// we must check for NaN for ie 8/9
		if (isNaN(marginTop))  marginTop = 0
		if (isNaN(marginLeft)) marginLeft = 0
		offset.top = offset.top + marginTop
		offset.left = offset.left + marginLeft

		$tip
			.offset(offset)
			.addClass('in')

		// check to see if placing tip in new offset caused the tip to resize itself
		var actualWidth = $tip[0].offsetWidth
		var actualHeight = $tip[0].offsetHeight

		//correct left right position
		var elePos = halo.util.getPosition(this.$element);
		offset.left = elePos.left;
		$tip.offset(offset);

		if (placement == 'top' && actualHeight != height) {
			replace = true
			offset.top = offset.top + height - actualHeight
		}

		if (/bottom|top/.test(placement)) {
			var delta = 0
			var windowWidth = jQuery(window).width();
			if(actualWidth < (windowWidth / 2)){
				var  elePost = halo.util.getPosition(this.$element);
				//user left or right border of target element
				delta = actualWidth - elePost.width;
				if(elePost.left < (windowWidth/2)){
					//left border
					offset.left = elePost.left;
					if(delta > 0){
						this.replaceArrow(delta, actualWidth, 'left');
					}
				} else {
					offset.left = elePost.right - actualWidth;
					if(delta > 0){
						this.replaceArrow( -delta, actualWidth, 'left')
					}
				}
				$tip.offset(offset);
			} else {
				if (offset.left < 0) {
					delta = offset.left * -2
					offset.left = 0

					$tip.offset(offset)

					actualWidth = $tip[0].offsetWidth
					actualHeight = $tip[0].offsetHeight
					this.replaceArrow(delta - width + actualWidth, actualWidth, 'left')
				} else if (width + offset.left > windowWidth) {
					offset.left = windowWidth - width - 10; //10px padding
					$tip.offset(offset)
					var pos = this.getPosition();

					this.replaceArrow(-(pos.width * 2), actualWidth, 'left')
				}
				//calculate arrow pos
				var centerTip = offset.left + ( actualWidth / 2 );
				var centerTarget = elePos.left + ( elePos.width / 2 );
				if(centerTarget >= offset.left && centerTarget <= (offset.left + actualWidth)){
					delta = (centerTip - centerTarget) * 2;
					this.replaceArrow(delta, actualWidth, 'left')
				}
			}
		} else {
			this.replaceArrow(actualHeight - height, actualHeight, 'top')
		}
		if (replace) $tip.offset(offset)
	}

	Popover.prototype.replaceArrow = function (delta, dimension, position) {
		this.arrow().css(position, delta ? (50 * (1 - delta / dimension) + "%") : '')
	}

	Popover.prototype.hasContent = function () {
		return this.getTitle() || this.getContent()
	}

	Popover.prototype.getContent = function () {
		var $e = this.$element
		var o = this.options

		return $e.attr('data-content')
			|| (typeof o.content == 'function' ?
			o.content.call($e[0]) :
			o.content)
	}

	Popover.prototype.arrow = function () {
		return this.$arrow = this.$arrow || this.tip().find('.arrow')
	}

	Popover.prototype.tip = function () {
		if (!this.$tip) this.$tip = jQuery(this.options.template)
		return this.$tip
	}


	// POPOVER PLUGIN DEFINITION
	// =========================

	var old = jQuery.fn.hpopover

	jQuery.fn.hpopover = function (option) {
		return this.each(function () {
			var $this = jQuery(this)
			var data = $this.data('bs.hpopover')
			var options = typeof option == 'object' && option

			if (!data) $this.data('bs.hpopover', (data = new Popover(this, options)))
			if (typeof option == 'string') data[option]()
		})
	}

	jQuery.fn.hpopover.Constructor = Popover


	// POPOVER NO CONFLICT
	// ===================

	jQuery.fn.hpopover.noConflict = function () {
		jQuery.fn.hpopover = old
		return this
	}

}(window.jQuery);

/* ============================================================ popup filter feature
	popup filter
*/
+function ($) {
	"use strict";
	var ns = {};

	// DATEFILTER PUBLIC CLASS DEFINITION
	// ===============================

	ns.DateFilter = function (element, options, e) {
		if (e) {
		  e.stopPropagation();
		  e.preventDefault();
		}
		this.$element = jQuery(element);
		this.$newElement = null;
		this.$button = null;
		this.$menu = null;
		this.options = options;
		this.$startdate = null;
		this.$enddate = null;
		this.$title = null;
		
		this.init();
	}

	ns.DateFilter.DEFAULTS = jQuery.extend({}, {
		placement: 'right', 
		trigger: 'click', 
		content: '', 
		template: '<div class="popover"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
	})


	ns.DateFilter.prototype = {

		getDefaults: function () {
			return DateFilter.DEFAULTS
		},
		init: function () {
			this.$newElement = this.$element.find('.halo-popupfilter-content');
			this.$menu = this.$newElement.find('> .halo-dropdown-menu');
			this.$button = this.$newElement.find('> button');
			this.$startdate = this.$element.find('.start-date-input');
			this.$enddate = this.$element.find('.end-date-input');
			this.$within = this.$element.find('.within-date-input');
			this.$morethan = this.$element.find('.morethan-date-input');
			this.$title = this.$element.find('.filter-option');
			this.originalTitle = this.$title.text();
			
			this.activeMode = null;
			//this.initDatePicker();
			this.clickListener();
			
			this.updateTitle();
		},
		render: function(){
			//this.$element.append(this.$new
		},
		setSize: function(){
			var width = Math.min(jQuery(window).width() - 40,400);
			this.$menu.css({width: width +'px'});
		},
		clickListener: function () {
		  var that = this;

		  this.$newElement.on('touchstart.dropdown', '.halo-dropdown-menu', function (e) {
			e.stopPropagation();
		  });

		  this.$newElement.on('click', function (e) {
			that.setSize();
			//e.stopPropagation();
		  });

		  this.$menu.on('click', function (e) {
			  e.preventDefault();
			  e.stopPropagation();
		  });

		  this.$element.change(function () {
			that.render(false);
		  });
		  
		  this.$startdate.change(function(){
			that.activeMode = 'between';
			that.resetValues(['$startdate','$enddate']);
			that.updateTitle();
		  });

		  this.$enddate.change(function(){
			that.activeMode = 'between';
			that.resetValues(['$startdate','$enddate']);
			that.updateTitle();
		  });

		  this.$within.change(function(evt){
		  	if (!isNaN(jQuery(this).val()) && jQuery(this).val() >= 0 ){
				that.activeMode = 'within';
				that.resetValues(['$' + that.activeMode]);
				that.updateTitle();
			}
			if (isNaN(jQuery(this).val()) || jQuery(this).val() < 0){
		  		evt.preventDefault();
		  		return false;
		  	}
		  });

		  this.$morethan.change(function(evt){
		  	if (!isNaN(jQuery(this).val()) && jQuery(this).val() >= 0 ){
			  	that.activeMode = 'morethan';
				that.resetValues(['$' + that.activeMode]);
				that.updateTitle();
		  	}
		  	if (isNaN(jQuery(this).val()) || jQuery(this).val() < 0){
		  		evt.preventDefault();
		  		return false;
		  	}
		  });
		},
		resetValues: function(exclusive){
			var keys = ['$startdate','$enddate','$within','$morethan'];
			for(var key in keys){
				if( exclusive.indexOf(keys[key]) < 0 && typeof this[keys[key]] !== 'undefined'){
					this[keys[key]].val('');
				}
			}
		},
		updateTitle: function(){
			var title = [];
			var titleStr = this.originalTitle;
			var that = this;
			if(that.activeMode === null){
				//try to detect current mode
				if(that.$startdate.val() || that.$enddate.val()){
					that.activeMode = 'between';
				} else if(that.$within.val()){
					that.activeMode = 'within';
				} else if(that.$morethan.val()){
					that.activeMode = 'morethan';
				} else {
					that.activeMode = 'between';
				}
			}
			switch (that.activeMode){
				case 'between':
					if(that.$startdate.val()){
						title.push(that.$startdate.attr('data-halo-label') + ' ' + that.$startdate.val());
					}
					if(that.$enddate.val()){
						title.push(that.$enddate.attr('data-halo-label') + ' ' + that.$enddate.val());
					}
					if(title.length){
						titleStr = title.join(' - ');
					}
					break;
				case 'within':
					if(that.$within.val()){
						titleStr = that.$within.attr('data-halo-label') + ' ' + that.$within.val() + ' ' + __halotext('day(s)');
					}
					break;
				case 'morethan':
					if(that.$morethan.val()){
						titleStr = that.$morethan.attr('data-halo-label') + ' ' + that.$morethan.val() + ' ' + __halotext('day(s)');
					}					
					break;
				default:
					break;
			}
			that.setAtiveLabel(that.activeMode);
			that.$title.html(titleStr);
			that.$button.attr('title',titleStr);
		},
		setAtiveLabel : function(mode){
			var modes = {between:'dateBetween',within:'dateWithin',morethan:'dateMorethan'};
			if(typeof modes[mode] === 'undefined') return;
			var selector = '[data-type="'+ modes[mode] +'"] .halo-option-item';
			//clear current active
			this.$newElement.find('.halo-option-item').empty();
			this.$newElement.find(selector).append('<span class="fa fa-check"></span>');
		}
		
	}


	// RANGEFILTER PUBLIC CLASS DEFINITION
	// ===============================
	ns.RangeFilter = function (element, options, e) {
		if (e) {
		  e.stopPropagation();
		  e.preventDefault();
		}
		this.$element = jQuery(element);
		this.$newElement = null;
		this.$button = null;
		this.$menu = null;
		this.options = options;
		this.$startdate = null;
		this.$enddate = null;
		this.$title = null;
		
		this.init();
	}

	ns.RangeFilter.DEFAULTS = jQuery.extend({}, {
		placement: 'right', 
		trigger: 'click', 
		content: '', 
		template: '<div class="popover"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
	})


	ns.RangeFilter.prototype = {

		getDefaults: function () {
			return DateFilter.DEFAULTS
		},
		init: function () {
			this.$newElement = this.$element.find('.halo-popupfilter-content');
			this.$menu = this.$newElement.find('> .halo-dropdown-menu');
			this.$button = this.$newElement.find('> button');
			this.$min = this.$element.find('.min-range-input');
			this.$max = this.$element.find('.max-range-input');
			this.$na = this.$element.find('.na-range-input');
			this.$checkbox = this.$element.find('.halo-checkbox-filter');
			this.$title = this.$element.find('.filter-option');
			this.originalTitle = this.$title.text();
			
			this.activeMode = null;
			//this.initDatePicker();
			this.clickListener();
			
			this.updateTitle();
		},
		render: function(){
			//this.$element.append(this.$new
		},
		setSize: function(){
			var width = Math.min(jQuery(window).width() - 40,400);
			this.$menu.css({width: width +'px'});
		},
		clickListener: function () {
		  var that = this;

		  this.$newElement.on('touchstart.dropdown', '.halo-dropdown-menu', function (e) {
			e.stopPropagation();
		  });

		  this.$newElement.on('click', function (e) {
			that.setSize();
			//e.stopPropagation();
		  });

		  this.$menu.on('click', function (e) {
			  e.preventDefault();
			  e.stopPropagation();
		  });

		  this.$checkbox.on('click', function (e) {
			var input = jQuery(this).find('.halo-checkbox-input');
			if(input.length){
				var val = input.val()?'':1;
				input.val(val);
				input.trigger('change');
			}
		  });

		  this.$element.change(function () {
			that.render(false);
		  });
		  
		  this.$min.on('keypress', function(e){
			var code = e.keyCode || e.which;
			if(code == 13) { //Enter keycode
				jQuery(this).trigger('change');
			}
		  });
		  
		  this.$min.change(function(){
			that.activeMode = 'between';
			that.resetValues(['$min','$max']);
			that.updateTitle();
			
			//set min max contrains
			var max = parseFloat(that.$max.autoNumeric('get'));
			var min = parseFloat(that.$min.autoNumeric('get'));
			if(max < min) max = min;
			that.$max.autoNumeric('set', max);
			
		  });

		  this.$max.on('keypress', function(e){
			var code = e.keyCode || e.which;
			if(code == 13) { //Enter keycode
				jQuery(this).trigger('change');
			}
		  });
		  
		  this.$max.change(function(){
			that.activeMode = 'between';
			that.resetValues(['$min','$max']);
			that.updateTitle();

			//set min max contrains
			var max = parseFloat(that.$max.autoNumeric('get'));
			var min = parseFloat(that.$min.autoNumeric('get'));
			if(max < min) min = max;
			that.$min.autoNumeric('set', min);
						
		  });

		  this.$na.change(function(){
			if(jQuery(this).val()){
				that.activeMode = 'na';
				that.resetValues(['$na']);
			} else {
				that.activeMode = 'empty';
				that.resetValues([]);
			}
			that.updateTitle();
		  });
		},
		resetValues: function(exclusive){
			var keys = ['$min','$max','$na'];
			for(var key in keys){
				if( exclusive.indexOf(keys[key]) < 0 && typeof this[keys[key]] !== 'undefined'){
					this[keys[key]].val('');
				}
			}
		},
		updateTitle: function(){
			var title = [];
			var titleStr = this.originalTitle;
			var that = this;
			if(that.activeMode === null){
				//try to detect current mode
				if(that.$min.val() || that.$max.val()){
					that.activeMode = 'between';
				} else if(that.$na.val()){
					that.activeMode = 'na';
				} else {
					that.activeMode = 'between';
				}
			}
			switch (that.activeMode){
				case 'between':
					if(that.$min.val()){
						title.push(that.$min.attr('data-halo-label') + ' ' + that.$min.val());
					}
					if(that.$max.val()){
						title.push(that.$max.attr('data-halo-label') + ' ' + that.$max.val());
					}
					if(title.length){
						titleStr = titleStr + ': ' + title.join(' - ');
					}
					break;
				case 'na':
					if(that.$na.val()){
						titleStr = titleStr + ': ' + that.$na.attr('data-halo-label');
					}
					break;
				default:
					break;
			}
			that.setAtiveLabel(that.activeMode);
			that.$title.html(titleStr);
			that.$button.attr('title',titleStr);
		},
		setAtiveLabel : function(mode){
			var modes = {between:'rangeBetween',na:'rangeNa',empty:'empty'};
			if(typeof modes[mode] === 'undefined') return;
			var selector = '[data-type="'+ modes[mode] +'"] .halo-option-item';
			//clear current active
			this.$newElement.find('.halo-option-item').empty();
			this.$newElement.find('.halo-option-item').append('<span class="fa fa-square-o"></span>');
			this.$newElement.find(selector).empty();
			this.$newElement.find(selector).append('<span class="fa fa-check"></span>');
		}
		
	}



	// LOCATIONFILTER PUBLIC CLASS DEFINITION
	// ===============================

	ns.LocationFilter = function (element, options, e) {
		if (e) {
		  e.stopPropagation();
		  e.preventDefault();
		}
		this.$element = jQuery(element);
		this.$newElement = null;
		this.$button = null;
		this.$menu = null;
		this.options = options;
		this.$startdate = null;
		this.$enddate = null;
		this.$title = null;
		
		this.init();
	}

	ns.LocationFilter.DEFAULTS = jQuery.extend({}, {
		placement: 'right', 
		trigger: 'click', 
		content: '', 
		template: '<div class="popover"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
	})


	ns.LocationFilter.prototype = {

		getDefaults: function () {
			return LocationFilter.DEFAULTS
		},
		init: function () {
			this.$newElement = this.$element.find('.halo-popupfilter-content');
			this.$menu = this.$newElement.find('> .halo-dropdown-menu');
			this.$button = this.$newElement.find('> button');
			this.$title = this.$element.find('.filter-option');
			this.$inputName = this.$element.find('.halo-field-location-name');
			this.$inputLat = this.$element.find('.halo-field-location-lat');
			this.$inputLng = this.$element.find('.halo-field-location-lng');
			this.$distance = this.$element.find('.halo-field-location-distance');
			
			this.clickListener();
			this.render();
		},
		render: function(){
			this.updateTitle();
		},
		setSize: function(){
			var width = Math.min(jQuery(window).width() - 40,400);
			this.$menu.css({width: width +'px'});
		},
		clickListener: function () {
		  var that = this;

		  this.$newElement.on('touchstart.dropdown', '.halo-dropdown-menu', function (e) {
			e.stopPropagation();
		  });

		  this.$newElement.on('click', function (e) {
			that.setSize();
			//e.stopPropagation();
		  });

		  this.$menu.on('click', function (e) {
			  e.preventDefault();
			  e.stopPropagation();
		  });

		  this.$menu.on('changeFilter.halo',function () {
			that.updateTitle();
		  });
		  this.$distance.change(function(){
			that.updateTitle();
		  });
		  
		},
		updateTitle: function(){
			var titleStr = this.originalTitle;
			if(this.$inputName.val() && this.$distance.val()){
				titleStr = this.$distance.attr('data-halo-label') + ' ' + this.$inputName.val() + ' ' + this.$distance.val() + ' km';
			}
			this.$title.html(titleStr);
			this.$button.attr('title',titleStr);
		}
	}


	// SELECTFILTER PUBLIC CLASS DEFINITION
	// ===============================

	ns.SelectFilter = function (element, options, e) {
		this.$element = jQuery(element);
		this.$newElement = this.$element.find('.halo-popupfilter-content');
		this.$newElement.selectpicker({});
	}

	ns.SelectFilter.DEFAULTS = jQuery.extend({}, {
		placement: 'right', 
		trigger: 'click', 
		content: '', 
		template: '<div class="popover"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
	})


	// TEXTFILTER PUBLIC CLASS DEFINITION
	// ===============================

	ns.TextFilter = function (element, options, e) {
		this.$element = jQuery(element);
		this.$newElement = this.$element.find('.halo-popupfilter-content');
	}

	ns.TextFilter.DEFAULTS = jQuery.extend({}, {
		placement: 'right', 
		trigger: 'click', 
		content: '', 
		template: '<div class="popover"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
	})


	// POPOVER PLUGIN DEFINITION
	// =========================

	var old = jQuery.fn.popupfilter

	jQuery.fn.popupfilter = function (option) {
		return this.each(function () {
			var $this = jQuery(this)
			var data = $this.data('bs.popupfilter')
			var options = typeof option == 'object' && option

			if (!data) {
				var filterType = $this.attr('data-filter-type');
				if(filterType && typeof ns[filterType] != 'undefined'){
					$this.data('bs.popupfilter', (data = new ns[filterType](this, options)))
				} else {
					$this.data('bs.popupfilter', (data = new ns.DateFilter(this, options)))
				}
			}
			if (typeof option == 'string') data[option]()	//execute function
		})
	}

	//jQuery.fn.popupfilter.Constructor = DateFilter


	// POPUPFILTER NO CONFLICT
	// ===================

	jQuery.fn.popupfilter.noConflict = function () {
		jQuery.fn.popupfilter = old
		return this
	}

	function updateUrl($ele, params) {
		if (inited) {
			//get the url params of this filter label
			var $label = getFilterLabel($ele);
			var oldParams = $label.data('oldParams');
			//halo.util.setUrlParam(params, oldParams);
			//update the url params for this filter label
			$label.data('oldParams', halo.util.getCurrentQueryObj())
		}
	}

	function getFilterLabel($ele) {
		var labelId = $ele.closest('.halo-filter-content').attr('data-filter-label');
		var $label = jQuery('#' + labelId);
		return $label;
	}
	function updateFilterGroup(ele){
		//update current url
		var $this = jQuery(ele);
		var $filterLabel = $this.closest('.halo-filter').find('.halo-filter-label-input');
		var params = {};
		var $inputs = $this.find('.halo-popoverfilter-sub-input');
		$inputs.each(function(){
			params[jQuery(this).attr('name')] = jQuery(this).val();
		});
		
		var oldParams = $filterLabel.data('oldParams');
		//halo.util.setUrlParam(params, oldParams);
		
		$filterLabel.data('oldParams', halo.util.getCurrentQueryObj())

		//call ajax to update result
		$filterLabel.trigger('change');	
	}
	
	jQuery(document).on('change','.halo-popoverfilter-input',halo.util.throttle(function(){
		var $filterLabel = jQuery(this).closest('.halo-filter').find('.halo-filter-label-input');
		//update current url
		var $this = jQuery(this);
		var params = {};
		params[$this.attr('name')] = $this.val();
		
		var oldParams = $filterLabel.data('oldParams');
		//halo.util.setUrlParam(params, oldParams);
		
		$filterLabel.data('oldParams', halo.util.getCurrentQueryObj())
		
		//call ajax to update result
		$filterLabel.trigger('change');
	},300))
	.on('changeFilter.halo','div.halo-popoverfilter-input',halo.util.throttle(function(){
		updateFilterGroup(jQuery(this));
	},300))
	.on('change','.halo-popoverfilter-sub-input',halo.util.throttle(function(){
		var $ele = jQuery(this).closest('.halo-popoverfilter-input');
		if($ele.length)
			updateFilterGroup($ele);
	},300))
	;
}(window.jQuery);

//HaloSocial customization
});}(window.jQuery);

/* ============================================================ wizard feature
 * ======================================================================== */
+function ($) { "use strict";

  // WIZARD CLASS DEFINITION
  // ====================

  var Wizard = function (element) {
    this.element = $(element)
  }

  Wizard.prototype.show = function () {
    var $this    = this.element
    var $steps      = $this.closest('.halo-wizard-steps')
    var selector = $this.attr('data-target')

    if (!selector) {
      selector = $this.attr('href')
      selector = selector && selector.replace(/.*(?=#[^\s]*$)/, '') //strip for ie7
    }

    if ($this.parent('.halo-wizard-step-title').hasClass('active')) return

    var previous = $steps.find('.active:last a')[0]
    var e        = $.Event('show.bs.wizard', {
      relatedTarget: previous
    })

    $this.trigger(e)

    if (e.isDefaultPrevented()) return

    var $target = $(selector)

    this.activate($this.parent('.halo-wizard-step-title'), $steps)
    this.activate($target, $target.parent(), function () {
      $this.trigger({
        type: 'shown.bs.wizard'
      , relatedTarget: previous
      })
    })
  }

  Wizard.prototype.activate = function (element, container, callback) {
    var $active    = container.find('> .active')
    var transition = callback
      && $.support.transition
      && $active.hasClass('fade')

    function next() {
      $active
        .removeClass('active')
        .find('> .halo-dropdown-menu > .active')
        .removeClass('active')

      element.addClass('active')

      if (transition) {
        element[0].offsetWidth // reflow for transition
        element.addClass('in')
      } else {
        element.removeClass('fade')
      }
      callback && callback()
    }

    transition ?
      $active
        .one($.support.transition.end, next)
        .emulateTransitionEnd(150) :
      next()

    $active.removeClass('in')
  }


  // WIZARD PLUGIN DEFINITION
  // =====================

  var old = $.fn.wizard

  $.fn.wizard = function ( option ) {
    return this.each(function () {
      var $this = $(this)
      var data  = $this.data('bs.wizard')

      if (!data) $this.data('bs.wizard', (data = new Wizard(this)))
      if (typeof option == 'string') data[option]()
    })
  }

  $.fn.wizard.Constructor = Wizard


  // WIZARD NO CONFLICT
  // ===============

  $.fn.wizard.noConflict = function () {
    $.fn.wizard = old
    return this
  }


  // WIZARD DATA-API
  // ============

  $(document).on('click.bs.wizard.data-api', '[data-htoggle="wizard"]', function (e) {
    e.preventDefault();
	if(!$(this).parent().hasClass('disabled')) {
		$(this).wizard('show');
	}
  })

}(window.jQuery);

/* ============================================================ scroll features (must be inited last)
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	scroll: {
		init: function (scope) {
			
			if (!halo.util.isMobile()) {
				//resize nice scroll
				
				jQuery(".nicescroll-rails:not(:last-child)",jQuery(".halo-notification-wrapper").parent()).each(function() {
					jQuery(this).remove();
				})
				//Nicescroll doesnt properly destroy with jQuery.getnicescroll.remove() - Also, too many init triggered
				
				jQuery('.halo-nicescroll, .halo-notification-content, .halo-conv-entry-wrapper,.halo-stream-more-activity ul', scope).each(function(){
					var $this = jQuery(this);
					var scrollOpt = {
										autohidemode: false,
										nativeparentscrolling: false,
										cursoropacitymax: 0.7,
										horizrailenabled: false
									};
					scrollOpt = jQuery.extend({},scrollOpt, $this.data());
					var $scroll = $this.niceScroll(scrollOpt);
					$scroll.resize();

					//for dropdown scoller
					if($this.closest('.halo-dropdown').length) {
						$this.closest('.halo-dropdown').on('hidden.bs.hdropdown', function(){
							$scroll.hide();
						})
						.on('shown.bs.hdropdown', function(){
							$scroll.show();
						})
					}
					//for popover scroller
					var $popoverContent = $this.closest('.popover');
					if($popoverContent.length) {
						var $popover = halo.util.closest($this, '[data-htoggle="popover"]');
						if($popover.length){
							$popover.on('hidden.bs.hpopover', function(){
								$scroll.hide();
								jQuery('.nicescroll-rails',$popoverContent).remove();
							})
							.on('shown.bs.hpopover', function(){
								$scroll.show();
							});
						}
					}
				});

			} else {
				var doc=jQuery(document);
				if (doc.scrollTop() > 0) jQuery(".halo-scrolltop").show();
				doc.on('scroll',function() {
					if (doc.scrollTop() >0) {
						jQuery(".halo-scrolltop").show();
					}	else {
						jQuery(".halo-scrolltop").hide();
					}
				});
			}
		}
	}
});

