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
 
jQuery(document).ready(function () {
	//extend jQuery
	jQuery.fn.outerHTML = function(s) {
		return (s)
		? this.before(s).remove()
		: jQuery("<p>").append(this.eq(0).clone()).html();
	};
	
	jQuery.fn.haloReplaceHtml = function(sVal, nVal) {
		if(sVal && jQuery.isArray(sVal) && nVal && jQuery.isArray(nVal)) {
			var eleHtml = this.outerHTML();
			var isChanged = false;
			for(var i = 0; i < sVal.length; i++){
				if(nVal[i]) {
					eleHtml = eleHtml.replace(new RegExp(sVal[i], 'g'), nVal[i]);
					isChanged = true;
				}
			}
			if(isChanged) {
				var $replace = jQuery(eleHtml);
				this.replaceWith($replace);
				halo.util.init($replace);				
			}
		}
	}

	//init halo
	halo.init(this);

	//check for redirect message
	halo.util.getRedirectMessage();

	//update elapsed timer
	halo.util.updateElapsedTime();
		
	//bind input error handler
	jQuery(document).on('inputerror.halo', halo.util.lockFunction(function (event, input, errors) {
		//check if the input inside tab
		var tabId = jQuery(input).closest('.tab-pane.halo-tab').attr('id');

		if (typeof tabId !== 'undefined' && tabId.length) {
			jQuery('[href="#' + tabId + '"]').htab('show');
		}
		if (!halo.util.isElementInViewport(input)) {
			input.focus();
			jQuery('html, body').animate({
				scrollTop: (input.offset().top - 60)
			}, 500);
		}
	}, 100));

	if (jQuery('.halo-post-panel-wrapper').length) {
		halo.post.enableCoverSwitching();
	}

	if(halo.util.isXs()){
		//bind scroll events
//		var lastScrollTop = 0, st, direction;
//		function detectDirection() {
//			st = window.pageYOffset;
//			if (st > lastScrollTop) {
//					jQuery(window).trigger('halo_scroll_down');
//			} else {
//					jQuery(window).trigger('halo_scroll_up');
//			}
//			lastScrollTop = st;
//		}
//		jQuery(window).bind('scroll', halo.util.throttle(function() {
//			detectDirection();
//		},200));
//		jQuery(window).bind('halo_scroll_down',function(){
//			jQuery('.halo-navbar').hide();
//		});
//		jQuery(window).bind('halo_scroll_up',function(){
//			jQuery('.halo-navbar').show();
//		});
	}
	
	//touch device active state
	var lasttouch = null;
	jQuery(document).on('touchend','a,button',halo.util.throttle(function(){
		if(lasttouch){
			jQuery(lasttouch).toggleClass("touched");
		}
		lasttouch = jQuery(this);
		jQuery(this).toggleClass("touched");
	},100));
	
	//scroll to top
	jQuery(window).scroll(halo.util.throttle(function(){
		if (jQuery(this).scrollTop() > 100) {
			jQuery('.halo-scroll-top-wrapper').fadeIn();
		} else {
			jQuery('.halo-scroll-top-wrapper').fadeOut();
		}
	},500));
	
	//Click event to scroll to top
	jQuery('.halo-scroll-top-wrapper a').click(function(){
		jQuery('html, body').animate({scrollTop : 0},800);
		return false;
	});
	
	jQuery('.halo-popupfilter-wrapper').popupfilter();

	//select picker title
	jQuery(document).on('updateTitle.halo', 'select.selectpicker', function() {
		//update dropdown title
		var $dropdown = jQuery(this).next();
		if($dropdown.length) {
			var title = jQuery(this).find(":selected").attr('title');
			if(title) {
				$dropdown.find('.selectpicker').attr('title', title);
			}
		}
		
	}).on('change', 'select.selectpicker', function() {
		jQuery(this).trigger('updateTitle.halo');
	});
	jQuery('select.selectpicker').trigger('updateTitle.halo');
	
	//prevent filter_form submit
	jQuery('#filter_form').submit(function (e) {
		e.preventDefault(); 
	});
	
	// override Jquery Errors mesage (plugin)
	jQuery.extend(jQuery.validator.messages, {
		required: __halotext("This field is required."),
		remote: __halotext("Please fix this field."),
		email: __halotext("Please enter a valid email address."),
		url: __halotext("Please enter a valid URL."),
		date: __halotext("Please enter a valid date."),
		dateISO: __halotext("Please enter a valid date (ISO)."),
		number: __halotext("Please enter a valid number."),
		digits: __halotext("Please enter only digits."),
		creditcard: __halotext("Please enter a valid credit card number."),
		equalTo: __halotext("Please enter the same value again."),
		maxlength: jQuery.validator.format("Please enter no more than {0} characters."),
		minlength: jQuery.validator.format("Please enter at least {0} characters."),
		rangelength: jQuery.validator.format("Please enter a value between {0} and {1} characters long."),
		range: jQuery.validator.format("Please enter a value between {0} and {1}."),
		max: jQuery.validator.format("Please enter a value less than or equal to {0}."),
		min: jQuery.validator.format("Please enter a value greater than or equal to {0}.")
	});
	
	//dispatch halo on ready event
	document.dispatchEvent(new Event('__haloDoneLoading'));

	// Javascript to enable link to tab
	var url = document.location.toString();
	if (url.match('#')) {
    $('.halo-nav-hashlink.nav-tabs a[href=#'+url.split('#')[1]+']').htab('show') ;
	} 
  // Change hash for page-reload
  $(document).on('shown.bs.htab', '.halo-nav-hashlink.nav-tabs a', function (e) {
      window.location.hash = e.target.hash;
      window.scrollTo(0, 0);
  })  


});

