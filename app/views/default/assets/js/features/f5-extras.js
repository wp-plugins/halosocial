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
 
(function ($) {
    // Behind the scenes method deals with browser
    // idiosyncrasies and such
    jQuery.caretTo = function (el, index) {
        if (el.createTextRange) { 
            var range = el.createTextRange(); 
            range.move("character", index); 
            range.select(); 
        } else if (el.selectionStart != null) { 
            el.focus(); 
            el.setSelectionRange(index, index); 
        }
    };
    
    // Another behind the scenes that collects the
    // current caret position for an element
    
    // TODO: Get working with Opera
    jQuery.caretPos = function (el) {
        if ("selection" in document) {
            var range = el.createTextRange();
            try {
                range.setEndPoint("EndToStart", document.selection.createRange());
            } catch (e) {
                // Catch IE failure here, return 0 like
                // other browsers
                return 0;
            }
            return range.text.length;
        } else if (el.selectionStart != null) {
            return el.selectionStart;
        }
    };

    // The following methods are queued under fx for more
    // flexibility when combining with jQuery.fn.delay() and
    // jQuery effects.

    // Set caret to a particular index
    jQuery.fn.caret = function (index, offset) {
        if (typeof(index) === "undefined") {
            return jQuery.caretPos(this.get(0));
        }
        
        return this.queue(function (next) {
            if (isNaN(index)) {
                var i = jQuery(this).val().indexOf(index);
                
                if (offset === true) {
                    i += index.length;
                } else if (typeof(offset) !== "undefined") {
                    i += offset;
                }
                
                jQuery.caretTo(this, i);
            } else {
                jQuery.caretTo(this, index);
            }
            
            next();
        });
    };

    // Set caret to beginning of an element
    jQuery.fn.caretToStart = function () {
        return this.caret(0);
    };

    // Set caret to the end of an element
    jQuery.fn.caretToEnd = function () {
        return this.queue(function (next) {
            jQuery.caretTo(this, jQuery(this).val().length);
            next();
        });
    };
}(jQuery));

/* ============================================================ bootstrap-switch features
 * bootstrap-switch - v3.0.2
 * http://www.bootstrap-switch.org
 * ========================================================================
 * Copyright 2012-2013 Mattia Larentis
 *
 * ========================================================================
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================================
 */
(function () {
	var __slice = [].slice;

	(function ($, window) {
		"use strict";
		var BootstrapSwitch;
		BootstrapSwitch = (function () {
			function BootstrapSwitch(element, options) {
				if (options == null) {
					options = {};
				}
				this.$element = jQuery(element);
				this.options = jQuery.extend({}, jQuery.fn.bootstrapSwitch.defaults, {
					state: this.$element.is(":checked"),
					size: this.$element.data("size"),
					animate: this.$element.data("animate"),
					disabled: this.$element.is(":disabled"),
					readonly: this.$element.is("[readonly]"),
					indeterminate: this.$element.data("indeterminate"),
					onColor: this.$element.data("on-color"),
					offColor: this.$element.data("off-color"),
					onText: this.$element.data("on-text"),
					offText: this.$element.data("off-text"),
					labelText: this.$element.data("label-text"),
					baseClass: this.$element.data("base-class"),
					wrapperClass: this.$element.data("wrapper-class"),
					radioAllOff: this.$element.data("radio-all-off")
				}, options);
				this.$wrapper = jQuery("<div>", {
					"class": (function (_this) {
						return function () {
							var classes;
							classes = ["" + _this.options.baseClass].concat(_this._getClasses(_this.options.wrapperClass));
							classes.push(_this.options.state ? "" + _this.options.baseClass + "-on" : "" + _this.options.baseClass + "-off");
							if (_this.options.size != null) {
								classes.push("" + _this.options.baseClass + "-" + _this.options.size);
							}
							if (_this.options.animate) {
								classes.push("" + _this.options.baseClass + "-animate");
							}
							if (_this.options.disabled) {
								classes.push("" + _this.options.baseClass + "-disabled");
							}
							if (_this.options.readonly) {
								classes.push("" + _this.options.baseClass + "-readonly");
							}
							if (_this.options.indeterminate) {
								classes.push("" + _this.options.baseClass + "-indeterminate");
							}
							if (_this.$element.attr("id")) {
								classes.push("" + _this.options.baseClass + "-id-" + (_this.$element.attr("id")));
							}
							return classes.join(" ");
						};
					})(this)()
				});
				this.$container = jQuery("<div>", {
					"class": "" + this.options.baseClass + "-container"
				});
				this.$on = jQuery("<span>", {
					html: this.options.onText,
					"class": "" + this.options.baseClass + "-handle-on " + this.options.baseClass + "-" + this.options.onColor
				});
				this.$off = jQuery("<span>", {
					html: this.options.offText,
					"class": "" + this.options.baseClass + "-handle-off " + this.options.baseClass + "-" + this.options.offColor
				});
				this.$label = jQuery("<label>", {
					html: this.options.labelText,
					"class": "" + this.options.baseClass + "-label"
				});
				if (this.options.indeterminate) {
					this.$element.prop("indeterminate", true);
				}
				this.$element.on("init.bootstrapSwitch", (function (_this) {
					return function () {
						return _this.options.onInit.apply(element, arguments);
					};
				})(this));
				this.$element.on("switchChange.bootstrapSwitch", (function (_this) {
					return function () {
						return _this.options.onSwitchChange.apply(element, arguments);
					};
				})(this));
				this.$container = this.$element.wrap(this.$container).parent();
				this.$wrapper = this.$container.wrap(this.$wrapper).parent();
				this.$element.before(this.$on).before(this.$label).before(this.$off).trigger("init.bootstrapSwitch");
				this._elementHandlers();
				this._handleHandlers();
				this._labelHandlers();
				this._formHandler();
			}

			BootstrapSwitch.prototype._constructor = BootstrapSwitch;

			BootstrapSwitch.prototype.state = function (value, skip) {
				if (typeof value === "undefined") {
					return this.options.state;
				}
				if (this.options.disabled || this.options.readonly || this.options.indeterminate) {
					return this.$element;
				}
				if (this.options.state && !this.options.radioAllOff && this.$element.is(':radio')) {
					return this.$element;
				}
				value = !!value;
				this.$element.prop("checked", value).trigger("change.bootstrapSwitch", skip);
				return this.$element;
			};

			BootstrapSwitch.prototype.toggleState = function (skip) {
				if (this.options.disabled || this.options.readonly || this.options.indeterminate) {
					return this.$element;
				}
				return this.$element.prop("checked", !this.options.state).trigger("change.bootstrapSwitch", skip);
			};

			BootstrapSwitch.prototype.size = function (value) {
				if (typeof value === "undefined") {
					return this.options.size;
				}
				if (this.options.size != null) {
					this.$wrapper.removeClass("" + this.options.baseClass + "-" + this.options.size);
				}
				if (value) {
					this.$wrapper.addClass("" + this.options.baseClass + "-" + value);
				}
				this.options.size = value;
				return this.$element;
			};

			BootstrapSwitch.prototype.animate = function (value) {
				if (typeof value === "undefined") {
					return this.options.animate;
				}
				value = !!value;
				this.$wrapper[value ? "addClass" : "removeClass"]("" + this.options.baseClass + "-animate");
				this.options.animate = value;
				return this.$element;
			};

			BootstrapSwitch.prototype.disabled = function (value) {
				if (typeof value === "undefined") {
					return this.options.disabled;
				}
				value = !!value;
				this.$wrapper[value ? "addClass" : "removeClass"]("" + this.options.baseClass + "-disabled");
				this.$element.prop("disabled", value);
				this.options.disabled = value;
				return this.$element;
			};

			BootstrapSwitch.prototype.toggleDisabled = function () {
				this.$element.prop("disabled", !this.options.disabled);
				this.$wrapper.toggleClass("" + this.options.baseClass + "-disabled");
				this.options.disabled = !this.options.disabled;
				return this.$element;
			};

			BootstrapSwitch.prototype.readonly = function (value) {
				if (typeof value === "undefined") {
					return this.options.readonly;
				}
				value = !!value;
				this.$wrapper[value ? "addClass" : "removeClass"]("" + this.options.baseClass + "-readonly");
				this.$element.prop("readonly", value);
				this.options.readonly = value;
				return this.$element;
			};

			BootstrapSwitch.prototype.toggleReadonly = function () {
				this.$element.prop("readonly", !this.options.readonly);
				this.$wrapper.toggleClass("" + this.options.baseClass + "-readonly");
				this.options.readonly = !this.options.readonly;
				return this.$element;
			};

			BootstrapSwitch.prototype.indeterminate = function (value) {
				if (typeof value === "undefined") {
					return this.options.indeterminate;
				}
				value = !!value;
				this.$wrapper[value ? "addClass" : "removeClass"]("" + this.options.baseClass + "-indeterminate");
				this.$element.prop("indeterminate", value);
				this.options.indeterminate = value;
				return this.$element;
			};

			BootstrapSwitch.prototype.toggleIndeterminate = function () {
				this.$element.prop("indeterminate", !this.options.indeterminate);
				this.$wrapper.toggleClass("" + this.options.baseClass + "-indeterminate");
				this.options.indeterminate = !this.options.indeterminate;
				return this.$element;
			};

			BootstrapSwitch.prototype.onColor = function (value) {
				var color;
				color = this.options.onColor;
				if (typeof value === "undefined") {
					return color;
				}
				if (color != null) {
					this.$on.removeClass("" + this.options.baseClass + "-" + color);
				}
				this.$on.addClass("" + this.options.baseClass + "-" + value);
				this.options.onColor = value;
				return this.$element;
			};

			BootstrapSwitch.prototype.offColor = function (value) {
				var color;
				color = this.options.offColor;
				if (typeof value === "undefined") {
					return color;
				}
				if (color != null) {
					this.$off.removeClass("" + this.options.baseClass + "-" + color);
				}
				this.$off.addClass("" + this.options.baseClass + "-" + value);
				this.options.offColor = value;
				return this.$element;
			};

			BootstrapSwitch.prototype.onText = function (value) {
				if (typeof value === "undefined") {
					return this.options.onText;
				}
				this.$on.html(value);
				this.options.onText = value;
				return this.$element;
			};

			BootstrapSwitch.prototype.offText = function (value) {
				if (typeof value === "undefined") {
					return this.options.offText;
				}
				this.$off.html(value);
				this.options.offText = value;
				return this.$element;
			};

			BootstrapSwitch.prototype.labelText = function (value) {
				if (typeof value === "undefined") {
					return this.options.labelText;
				}
				this.$label.html(value);
				this.options.labelText = value;
				return this.$element;
			};

			BootstrapSwitch.prototype.baseClass = function (value) {
				return this.options.baseClass;
			};

			BootstrapSwitch.prototype.wrapperClass = function (value) {
				if (typeof value === "undefined") {
					return this.options.wrapperClass;
				}
				if (!value) {
					value = jQuery.fn.bootstrapSwitch.defaults.wrapperClass;
				}
				this.$wrapper.removeClass(this._getClasses(this.options.wrapperClass).join(" "));
				this.$wrapper.addClass(this._getClasses(value).join(" "));
				this.options.wrapperClass = value;
				return this.$element;
			};

			BootstrapSwitch.prototype.radioAllOff = function (value) {
				if (typeof value === "undefined") {
					return this.options.radioAllOff;
				}
				this.options.radioAllOff = value;
				return this.$element;
			};

			BootstrapSwitch.prototype.onInit = function (value) {
				if (typeof value === "undefined") {
					return this.options.onInit;
				}
				if (!value) {
					value = jQuery.fn.bootstrapSwitch.defaults.onInit;
				}
				this.options.onInit = value;
				return this.$element;
			};

			BootstrapSwitch.prototype.onSwitchChange = function (value) {
				if (typeof value === "undefined") {
					return this.options.onSwitchChange;
				}
				if (!value) {
					value = jQuery.fn.bootstrapSwitch.defaults.onSwitchChange;
				}
				this.options.onSwitchChange = value;
				return this.$element;
			};

			BootstrapSwitch.prototype.destroy = function () {
				var $form;
				$form = this.$element.closest("form");
				if ($form.length) {
					$form.off("reset.bootstrapSwitch").removeData("bootstrap-switch");
				}
				this.$container.children().not(this.$element).remove();
				this.$element.unwrap().unwrap().off(".bootstrapSwitch").removeData("bootstrap-switch");
				return this.$element;
			};

			BootstrapSwitch.prototype._elementHandlers = function () {
				return this.$element.on({
					"change.bootstrapSwitch": (function (_this) {
						return function (e, skip) {
							var checked;
							e.preventDefault();
							e.stopImmediatePropagation();
							checked = _this.$element.is(":checked");
							if (checked === _this.options.state) {
								return;
							}
							_this.options.state = checked;
							_this.$wrapper.removeClass(checked ? "" + _this.options.baseClass + "-off" : "" + _this.options.baseClass + "-on").addClass(checked ? "" + _this.options.baseClass + "-on" : "" + _this.options.baseClass + "-off");
							if (!skip) {
								if (_this.$element.is(":radio")) {
									jQuery("[name='" + (_this.$element.attr('name')) + "']").not(_this.$element).prop("checked", false).trigger("change.bootstrapSwitch", true);
								}
								return _this.$element.trigger("switchChange.bootstrapSwitch", [checked]);
							}
						};
					})(this),
					"focus.bootstrapSwitch": (function (_this) {
						return function (e) {
							e.preventDefault();
							return _this.$wrapper.addClass("" + _this.options.baseClass + "-focused");
						};
					})(this),
					"blur.bootstrapSwitch": (function (_this) {
						return function (e) {
							e.preventDefault();
							return _this.$wrapper.removeClass("" + _this.options.baseClass + "-focused");
						};
					})(this),
					"keydown.bootstrapSwitch": (function (_this) {
						return function (e) {
							if (!e.which || _this.options.disabled || _this.options.readonly || _this.options.indeterminate) {
								return;
							}
							switch (e.which) {
								case 37:
									e.preventDefault();
									e.stopImmediatePropagation();
									return _this.state(false);
								case 39:
									e.preventDefault();
									e.stopImmediatePropagation();
									return _this.state(true);
							}
						};
					})(this)
				});
			};

			BootstrapSwitch.prototype._handleHandlers = function () {
				this.$on.on("click.bootstrapSwitch", (function (_this) {
					return function (e) {
						_this.state(false);
						return _this.$element.trigger("focus.bootstrapSwitch");
					};
				})(this));
				return this.$off.on("click.bootstrapSwitch", (function (_this) {
					return function (e) {
						_this.state(true);
						return _this.$element.trigger("focus.bootstrapSwitch");
					};
				})(this));
			};

			BootstrapSwitch.prototype._labelHandlers = function () {
				return this.$label.on({
					"mousemove.bootstrapSwitch touchmove.bootstrapSwitch": (function (_this) {
						return function (e) {
							var left, pageX, percent, right;
							if (!_this.isLabelDragging) {
								return;
							}
							e.preventDefault();
							_this.isLabelDragged = true;
							pageX = e.pageX || e.originalEvent.touches[0].pageX;
							percent = ((pageX - _this.$wrapper.offset().left) / _this.$wrapper.width()) * 100;
							left = 25;
							right = 75;
							if (_this.options.animate) {
								_this.$wrapper.removeClass("" + _this.options.baseClass + "-animate");
							}
							if (percent < left) {
								percent = left;
							} else if (percent > right) {
								percent = right;
							}
							_this.$container.css("margin-left", "" + (percent - right) + "%");
							return _this.$element.trigger("focus.bootstrapSwitch");
						};
					})(this),
					"mousedown.bootstrapSwitch touchstart.bootstrapSwitch": (function (_this) {
						return function (e) {
							if (_this.isLabelDragging || _this.options.disabled || _this.options.readonly || _this.options.indeterminate) {
								return;
							}
							e.preventDefault();
							_this.isLabelDragging = true;
							return _this.$element.trigger("focus.bootstrapSwitch");
						};
					})(this),
					"mouseup.bootstrapSwitch touchend.bootstrapSwitch": (function (_this) {
						return function (e) {
							if (!_this.isLabelDragging) {
								return;
							}
							e.preventDefault();
							if (_this.isLabelDragged) {
								_this.isLabelDragged = false;
								_this.state(parseInt(_this.$container.css("margin-left"), 10) > -(_this.$container.width() / 6));
								if (_this.options.animate) {
									_this.$wrapper.addClass("" + _this.options.baseClass + "-animate");
								}
								_this.$container.css("margin-left", "");
							} else {
								_this.state(!_this.options.state);
							}
							return _this.isLabelDragging = false;
						};
					})(this),
					"mouseleave.bootstrapSwitch": (function (_this) {
						return function (e) {
							return _this.$label.trigger("mouseup.bootstrapSwitch");
						};
					})(this)
				});
			};

			BootstrapSwitch.prototype._formHandler = function () {
				var $form;
				$form = this.$element.closest("form");
				if ($form.data("bootstrap-switch")) {
					return;
				}
				return $form.on("reset.bootstrapSwitch",function () {
					return window.setTimeout(function () {
						return $form.find("input").filter(function () {
							return jQuery(this).data("bootstrap-switch");
						}).each(function () {
							return jQuery(this).bootstrapSwitch("state", this.checked);
						});
					}, 1);
				}).data("bootstrap-switch", true);
			};

			BootstrapSwitch.prototype._getClasses = function (classes) {
				var c, cls, _i, _len;
				if (!jQuery.isArray(classes)) {
					return ["" + this.options.baseClass + "-" + classes];
				}
				cls = [];
				for (_i = 0, _len = classes.length; _i < _len; _i++) {
					c = classes[_i];
					cls.push("" + this.options.baseClass + "-" + c);
				}
				return cls;
			};

			return BootstrapSwitch;

		})();
		jQuery.fn.bootstrapSwitch = function () {
			var args, option, ret;
			option = arguments[0], args = 2 <= arguments.length ? __slice.call(arguments, 1) : [];
			ret = this;
			this.each(function () {
				var $this, data;
				$this = jQuery(this);
				data = $this.data("bootstrap-switch");
				if (!data) {
					$this.data("bootstrap-switch", data = new BootstrapSwitch(this, option));
				}
				if (typeof option === "string") {
					return ret = data[option].apply(data, args);
				}
			});
			return ret;
		};
		jQuery.fn.bootstrapSwitch.Constructor = BootstrapSwitch;
		return jQuery.fn.bootstrapSwitch.defaults = {
			state: true,
			size: null,
			animate: true,
			disabled: false,
			readonly: false,
			indeterminate: false,
			onColor: "primary",
			offColor: "default",
			onText: "ON",
			offText: "OFF",
			labelText: "&nbsp;",
			baseClass: "bootstrap-switch",
			wrapperClass: "wrapper",
			radioAllOff: false,
			onInit: function () {
			},
			onSwitchChange: function () {
			}
		};
	})(window.jQuery, window);

}).call(this);

/* ============================================================ bootstrap-slider
 * bootstrap-slider.js v2.0.0
 * http://www.eyecon.ro/bootstrap-slider
 * =========================================================
 * Copyright 2012 Stefan Petre
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
 * ========================================================= */
!function ($) {

	var Slider = function (element, options) {
		this.element = jQuery(element);
		this.options = options;
		this.picker = jQuery('<div class="slider">' +
			'<div class="slider-track">' +
			'<div class="slider-selection"></div>' +
			'<div class="slider-handle"></div>' +
			'<div class="slider-handle"></div>' +
			'</div>' +
			'<div class="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>' +
			'<div class="slider-display"><span class="slider-display-min"></span><span class="slider-display-max"></span></div>' +
			'</div>')
			.insertBefore(this.element)
			.append(this.element);
		this.id = this.element.data('slider-id') || options.id;
		if (this.id) {
			this.picker[0].id = this.id;
		}

		if (typeof Modernizr !== 'undefined' && Modernizr.touch) {
			this.touchCapable = true;
		}

		var tooltip = this.element.data('slider-tooltip') || options.tooltip;

		this.tooltip = this.picker.find('.tooltip');
		this.display = this.picker.find('.slider-display');
		this.displayMin = this.display.find('.slider-display-min');
		this.displayMax = this.display.find('.slider-display-max');
		this.tooltipInner = this.tooltip.find('div.tooltip-inner');

		this.orientation = this.element.data('slider-orientation') || options.orientation;
		switch (this.orientation) {
			case 'vertical':
				this.picker.addClass('slider-vertical');
				this.stylePos = 'top';
				this.mousePos = 'pageY';
				this.sizePos = 'offsetHeight';
				this.tooltip.addClass('right')[0].style.left = '100%';
				break;
			default:
				/*this.picker
				 .addClass('slider-horizontal')
				 .css('width', this.element.outerWidth());*/
				this.picker
					.addClass('slider-horizontal');
				this.orientation = 'horizontal';
				this.stylePos = 'left';
				this.mousePos = 'pageX';
				this.sizePos = 'offsetWidth';
				break;
		}


		this.min = this.element.data('slider-min') || options.min;
		this.max = this.element.data('slider-max') || options.max;
		this.step = this.element.data('slider-step') || options.step;
		this.value = this.element.data('slider-value') || options.value;
		if (this.value[1]) {
			this.range = true;
		}

		this.selection = this.element.data('slider-selection') || options.selection;
		this.selectionEl = this.picker.find('.slider-selection');
		if (this.selection === 'none') {
			this.selectionEl.addClass('hidden');
		}
		this.selectionElStyle = this.selectionEl[0].style;


		this.handle1 = this.picker.find('.slider-handle:first');
		this.handle1Stype = this.handle1[0].style;
		this.handle2 = this.picker.find('.slider-handle:last');
		this.handle2Stype = this.handle2[0].style;

		var handle = this.element.data('slider-handle') || options.handle;
		switch (handle) {
			case 'round':
				this.handle1.addClass('round');
				this.handle2.addClass('round');
				break;
			case 'triangle':
				this.handle1.addClass('triangle');
				this.handle2.addClass('triangle');
				break;
		}

		if (this.range) {
			this.value[0] = Math.max(this.min, Math.min(this.max, this.value[0]));
			this.value[1] = Math.max(this.min, Math.min(this.max, this.value[1]));
		} else {
			this.value = [ Math.max(this.min, Math.min(this.max, this.value))];
			this.handle2.addClass('hidden');
			if (this.selection == 'after') {
				this.value[1] = this.max;
			} else {
				this.value[1] = this.min;
			}
		}
		this.diff = this.max - this.min;
		this.percentage = [
			(this.value[0] - this.min) * 100 / this.diff,
			(this.value[1] - this.min) * 100 / this.diff,
			this.step * 100 / this.diff
		];

		this.offset = this.picker.offset();
		this.size = this.picker[0][this.sizePos];

		this.formater = options.formater;

		this.layout();

		if (this.touchCapable) {
			// Touch: Bind touch events:
			this.picker.on({
				touchstart: jQuery.proxy(this.mousedown, this)
			});
		} else {
			this.picker.on({
				mousedown: jQuery.proxy(this.mousedown, this)
			});
		}

		if (tooltip === 'show') {
			this.picker.on({
				mouseenter: jQuery.proxy(this.showTooltip, this),
				mouseleave: jQuery.proxy(this.hideTooltip, this)
			});
		} else {
			this.tooltip.addClass('hidden');
		}
	};

	Slider.prototype = {
		constructor: Slider,
		over: false,
		inDrag: false,

		showTooltip: function () {

			this.tooltip.addClass('in');
			this.tooltip.addClass('top')[0].style.top = -this.tooltip.outerHeight() + 'px';
			/*var left = Math.round(this.percent*this.width);
			 this.tooltip.css('left', left - this.tooltip.outerWidth()/2);*/
			this.size = this.picker[0][this.sizePos];
			this.layout();
			this.over = true;

		},

		hideTooltip: function () {
			if (this.inDrag === false) {
				this.tooltip.removeClass('in');
			}
			this.over = false;
		},

		layout: function () {
			this.handle1Stype[this.stylePos] = this.percentage[0] + '%';
			this.handle2Stype[this.stylePos] = this.percentage[1] + '%';
			if (this.orientation == 'vertical') {
				this.selectionElStyle.top = Math.min(this.percentage[0], this.percentage[1]) + '%';
				this.selectionElStyle.height = Math.abs(this.percentage[0] - this.percentage[1]) + '%';
			} else {
				this.selectionElStyle.left = Math.min(this.percentage[0], this.percentage[1]) + '%';
				this.selectionElStyle.width = Math.abs(this.percentage[0] - this.percentage[1]) + '%';
			}
			if (this.range) {
				this.tooltipInner.text(
					this.formater(this.value[0]) +
						' : ' +
						this.formater(this.value[1])
				);
				this.displayMin.text(this.formater(this.value[0]));
				this.displayMax.text(this.formater(this.value[1]));
				this.tooltip[0].style[this.stylePos] = this.size * (this.percentage[0] + (this.percentage[1] - this.percentage[0]) / 2) / 100 - (this.orientation === 'vertical' ? this.tooltip.outerHeight() / 2 : this.tooltip.outerWidth() / 2) + 'px';
			} else {
				this.tooltipInner.text(
					this.formater(this.value[0])
				);
				this.tooltip[0].style[this.stylePos] = this.size * this.percentage[0] / 100 - (this.orientation === 'vertical' ? this.tooltip.outerHeight() / 2 : this.tooltip.outerWidth() / 2) + 'px';
			}
		},

		mousedown: function (ev) {

			// Touch: Get the original event:
			if (this.touchCapable && ev.type === 'touchstart') {
				ev = ev.originalEvent;
			}

			this.offset = this.picker.offset();
			this.size = this.picker[0][this.sizePos];

			var percentage = this.getPercentage(ev);

			if (this.range) {
				var diff1 = Math.abs(this.percentage[0] - percentage);
				var diff2 = Math.abs(this.percentage[1] - percentage);
				this.dragged = (diff1 < diff2) ? 0 : 1;
			} else {
				this.dragged = 0;
			}

			this.percentage[this.dragged] = percentage;

			if (this.touchCapable) {
				// Touch: Bind touch events:
				jQuery(document).on({
					touchmove: jQuery.proxy(this.mousemove, this),
					touchend: jQuery.proxy(this.mouseup, this)
				});
			} else {
				jQuery(document).on({
					mousemove: jQuery.proxy(this.mousemove, this),
					mouseup: jQuery.proxy(this.mouseup, this)
				});
			}

			this.inDrag = true;
			var val = this.calculateValue();
			this.element.trigger({
				type: 'slideStart',
				value: val
			}).trigger({
				type: 'slide',
				value: val
			})
				.data('value', val)
				.prop('value', val);
			this.layout();
			return false;
		},

		mousemove: function (ev) {

			// Touch: Get the original event:
			if (this.touchCapable && ev.type === 'touchmove') {
				ev = ev.originalEvent;
			}

			var percentage = this.getPercentage(ev);
			if (this.range) {
				if (this.dragged === 0 && this.percentage[1] < percentage) {
					this.percentage[0] = this.percentage[1];
					this.dragged = 1;
				} else if (this.dragged === 1 && this.percentage[0] > percentage) {
					this.percentage[1] = this.percentage[0];
					this.dragged = 0;
				}
			}
			this.percentage[this.dragged] = percentage;
			var val = this.calculateValue();
			this.element
				.trigger({
					type: 'slide',
					value: val
				})
				.data('value', val)
				.prop('value', val);
			this.layout();
			return false;
		},

		mouseup: function (ev) {
			if (this.touchCapable) {
				// Touch: Bind touch events:
				jQuery(document).off({
					touchmove: this.mousemove,
					touchend: this.mouseup
				});
			} else {
				jQuery(document).off({
					mousemove: this.mousemove,
					mouseup: this.mouseup
				});
			}

			this.inDrag = false;
			if (this.over == false) {
				this.hideTooltip();
			}
			this.element;
			var val = this.calculateValue();
			this.element
				.trigger({
					type: 'slideStop',
					value: val
				})
				.data('value', val)
				.prop('value', val);
			this.layout();
			return false;
		},

		calculateValue: function () {
			var val;
			if (this.range) {
				val = [
					(this.min + Math.round((this.diff * this.percentage[0] / 100) / this.step) * this.step),
					(this.min + Math.round((this.diff * this.percentage[1] / 100) / this.step) * this.step)
				];
				this.value = val;
			} else {
				val = (this.min + Math.round((this.diff * this.percentage[0] / 100) / this.step) * this.step);
				this.value = [val, this.value[1]];
			}
			return val;
		},

		getPercentage: function (ev) {
			if (this.touchCapable) {
				ev = ev.touches[0];
			}
			var percentage = (ev[this.mousePos] - this.offset[this.stylePos]) * 100 / this.size;
			percentage = Math.round(percentage / this.percentage[2]) * this.percentage[2];
			return Math.max(0, Math.min(100, percentage));
		},

		getValue: function () {
			if (this.range) {
				return this.value;
			}
			return this.value[0];
		},

		setValue: function (val) {
			this.value = val;

			if (this.range) {
				this.value[0] = Math.max(this.min, Math.min(this.max, this.value[0]));
				this.value[1] = Math.max(this.min, Math.min(this.max, this.value[1]));
			} else {
				this.value = [ Math.max(this.min, Math.min(this.max, this.value))];
				this.handle2.addClass('hidden');
				if (this.selection == 'after') {
					this.value[1] = this.max;
				} else {
					this.value[1] = this.min;
				}
			}
			this.diff = this.max - this.min;
			this.percentage = [
				(this.value[0] - this.min) * 100 / this.diff,
				(this.value[1] - this.min) * 100 / this.diff,
				this.step * 100 / this.diff
			];
			this.layout();
		}
	};

	jQuery.fn.slider = function (option, val) {
		return this.each(function () {
			var $this = jQuery(this),
				data = $this.data('slider'),
				options = typeof option === 'object' && option;
			if (!data) {
				$this.data('slider', (data = new Slider(this, jQuery.extend({}, jQuery.fn.slider.defaults, options))));
			} else {
				//Retrieve old option,destroy old slider & create new with extended options
				var oldOpt = $this.data('slider').options;
				var oldslider = $this.parent();
				oldslider.before($this);
				oldslider.remove();
				$this.data('slider', (data = new Slider(this, jQuery.extend({}, oldOpt, options))));
			}
			if (typeof option == 'string') {
				data[option](val);
			}
		})
	};

	jQuery.fn.slider.defaults = {
		min: 0,
		max: 10,
		step: 1,
		orientation: 'horizontal',
		value: 0,
		selection: 'before',
		tooltip: 'show',
		handle: 'round',
		formater: function (value) {
			return value;
		}
	};

	jQuery.fn.slider.Constructor = Slider;

}(window.jQuery);

/* ============================================================ tooltip validation features
 * jQuery Validation Bootstrap Tooltip extention v0.3
 *
 * https://github.com/Thrilleratplay/jQuery-Validation-Bootstrap-tooltip
 *
 * Copyright 2013 Tom Hiller
 * Released under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 */
(function ($) {
	jQuery.extend(true, jQuery.validator, {
		prototype: {
			defaultShowErrors: function () {
				var self = this;
				jQuery.each(this.successList, function (index, value) {
					halo.template.setInputSuccess(jQuery(value));
				});
				jQuery.each(this.errorList, function (index, value) {
					halo.template.setInputError(jQuery(value.element), value.message);
				});
			}
		}
	});
}(jQuery));

/* ============================================================ jQuery scrollable selector feature
 *
 * ============================================================ */
(function ($) {
	jQuery.extend(jQuery.expr[":"], {
		scrollable: function (element) {
			var vertically_scrollable, horizontally_scrollable;
			if (jQuery(element).is('html')) return true;

			if (jQuery(element).css('overflow') == 'scroll' || jQuery(element).css('overflowX') == 'scroll' || jQuery(element).css('overflowY') == 'scroll') return true;

			vertically_scrollable = (element.clientHeight < element.scrollHeight) && (
				jQuery.inArray(jQuery(element).css('overflowY'), ['scroll', 'auto']) != -1 || jQuery.inArray(jQuery(element).css('overflow'), ['scroll', 'auto']) != -1);

			if (vertically_scrollable) return true;

			horizontally_scrollable = (element.clientWidth < element.scrollWidth) && (
				jQuery.inArray(jQuery(element).css('overflowX'), ['scroll', 'auto']) != -1 || jQuery.inArray(jQuery(element).css('overflow'), ['scroll', 'auto']) != -1);
			return horizontally_scrollable;
		}
	});
})(jQuery);

/* ============================================================ abstract base class for collection plugins
 Written by Keith Wood (kbwood{at}iinet.com.au) December 2013.
 Licensed under the MIT (https://github.com/jquery/jquery/blob/master/MIT-LICENSE.txt) license. */
(function () {
	var j = false;
	window.JQClass = function () {
	};
	JQClass.classes = {};
	JQClass.extend = function extender(f) {
		var g = this.prototype;
		j = true;
		var h = new this();
		j = false;
		for (var i in f) {
			h[i] = typeof f[i] == 'function' && typeof g[i] == 'function' ? (function (d, e) {
				return function () {
					var b = this._super;
					this._super = function (a) {
						return g[d].apply(this, a || [])
					};
					var c = e.apply(this, arguments);
					this._super = b;
					return c
				}
			})(i, f[i]) : f[i]
		}
		function JQClass() {
			if (!j && this._init) {
				this._init.apply(this, arguments)
			}
		}

		JQClass.prototype = h;
		JQClass.prototype.constructor = JQClass;
		JQClass.extend = extender;
		return JQClass
	}
})();

(function ($) {
	JQClass.classes.JQPlugin = JQClass.extend({name: 'plugin', defaultOptions: {}, regionalOptions: {}, _getters: [], _getMarker: function () {
		return'is-' + this.name
	}, _init: function () {
		jQuery.extend(this.defaultOptions, (this.regionalOptions && this.regionalOptions['']) || {});
		var c = camelCase(this.name);
		$[c] = this;
		jQuery.fn[c] = function (a) {
			var b = Array.prototype.slice.call(arguments, 1);
			if ($[c]._isNotChained(a, b)) {
				return $[c][a].apply($[c], [this[0]].concat(b))
			}
			return this.each(function () {
				if (typeof a === 'string') {
					if (a[0] === '_' || !$[c][a]) {
						throw'Unknown method: ' + a;
					}
					$[c][a].apply($[c], [this].concat(b))
				} else {
					$[c]._attach(this, a)
				}
			})
		}
	}, setDefaults: function (a) {
		jQuery.extend(this.defaultOptions, a || {})
	}, _isNotChained: function (a, b) {
		if (a === 'option' && (b.length === 0 || (b.length === 1 && typeof b[0] === 'string'))) {
			return true
		}
		return jQuery.inArray(a, this._getters) > -1
	}, _attach: function (a, b) {
		a = jQuery(a);
		if (a.hasClass(this._getMarker())) {
			return
		}
		a.addClass(this._getMarker());
		b = jQuery.extend({}, this.defaultOptions, this._getMetadata(a), b || {});
		var c = jQuery.extend({name: this.name, elem: a, options: b}, this._instSettings(a, b));
		a.data(this.name, c);
		this._postAttach(a, c);
		this.option(a, b)
	}, _instSettings: function (a, b) {
		return{}
	}, _postAttach: function (a, b) {
	}, _getMetadata: function (d) {
		try {
			var f = d.data(this.name.toLowerCase()) || '';
			f = f.replace(/'/g, '"');
			f = f.replace(/([a-zA-Z0-9]+):/g, function (a, b, i) {
				var c = f.substring(0, i).match(/"/g);
				return(!c || c.length % 2 === 0 ? '"' + b + '":' : b + ':')
			});
			f = jQuery.parseJSON('{' + f + '}');
			for (var g in f) {
				var h = f[g];
				if (typeof h === 'string' && h.match(/^new Date\((.*)\)$/)) {
					f[g] = eval(h)
				}
			}
			return f
		} catch (e) {
			return{}
		}
	}, _getInst: function (a) {
		return jQuery(a).data(this.name) || {}
	}, option: function (a, b, c) {
		a = jQuery(a);
		var d = a.data(this.name);
		if (!b || (typeof b === 'string' && c == null)) {
			var e = (d || {}).options;
			return(e && b ? e[b] : e)
		}
		if (!a.hasClass(this._getMarker())) {
			return
		}
		var e = b || {};
		if (typeof b === 'string') {
			e = {};
			e[b] = c
		}
		this._optionsChanged(a, d, e);
		jQuery.extend(d.options, e)
	}, _optionsChanged: function (a, b, c) {
	}, destroy: function (a) {
		a = jQuery(a);
		if (!a.hasClass(this._getMarker())) {
			return
		}
		this._preDestroy(a, this._getInst(a));
		a.removeData(this.name).removeClass(this._getMarker())
	}, _preDestroy: function (a, b) {
	}});
	function camelCase(c) {
		return c.replace(/-([a-z])/g, function (a, b) {
			return b.toUpperCase()
		})
	}

	jQuery.JQPlugin = {createPlugin: function (a, b) {
		if (typeof a === 'object') {
			b = a;
			a = 'JQPlugin'
		}
		a = camelCase(a);
		var c = camelCase(b.name);
		JQClass.classes[c] = JQClass.classes[a].extend(b);
		new JQClass.classes[c]()
	}}
})(jQuery);

/* ============================================================ imagesLoaded plugin
*/
+function ($) {
	"use strict";
	// ======================= imagesLoaded Plugin ===============================
	// https://github.com/desandro/imagesloaded

	// jQuery('#my-container').imagesLoaded(myFunction)
	// execute a callback when all images have loaded.
	// needed because .load() doesn't work on cached images

	// callback function gets image collection as argument
	//  this is the container

	// original: mit license. paul irish. 2010.
	// contributors: Oren Solomianik, David DeSandro, Yiannis Chatzikonstantinou

	jQuery.fn.imagesLoaded = function (callback) {
		var $images = this.find('img'),
			len = $images.length,
			_this = this,
			blank = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';

		function triggerCallback() {
			callback.call(_this, $images);
		}

		function imgLoaded() {
			if (--len <= 0 && this.src !== blank) {
				setTimeout(triggerCallback);
				$images.off('load error', imgLoaded);
			}
		}

		if (!len) {
			triggerCallback();
		}

		$images.on('load error', imgLoaded).each(function () {
			// cached images don't fire load sometimes, so we reset src.
			if (this.complete || this.complete === undefined) {
				var src = this.src;
				// webkit hack from http://groups.google.com/group/jquery-dev/browse_thread/thread/eee6ab7b2da50e1f
				// data uri bypasses webkit log warning (thx doug jones)
				this.src = blank;
				this.src = src;
			}
		});

		return this;
	};
}(window.jQuery);

/* ============================================================ hammer plugin
*/
if(halo.util.isMobile()){
(function(factory) {
    if (typeof define === 'function' && define.amd) {
        define(['jquery', 'hammerjs'], factory);
    } else if (typeof exports === 'object') {
        factory(require('jquery'), require('hammerjs'));
    } else {
        factory(jQuery, Hammer);
    }
}(function($, Hammer) {
    function hammerify(el, options) {
        var $el = jQuery(el);
        if(!$el.data("hammer")) {
            $el.data("hammer", new Hammer($el[0], options));
        }
    }

    jQuery.fn.hammer = function(options) {
        return this.each(function() {
            hammerify(this, options);
        });
    };

    // extend the emit method to also trigger jQuery events
    Hammer.Manager.prototype.emit = (function(originalEmit) {
        return function(type, data) {
            originalEmit.call(this, type, data);
            jQuery(this.element).trigger({
                type: type,
                gesture: data
            });
        };
    })(Hammer.Manager.prototype.emit);
}));
}