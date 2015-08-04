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
	field: {
		showEditFieldForm: function (fieldId) {
			var ajaxCall = 'halo.jax.call("admin,fields", "ShowEditFieldForm","' + fieldId + '");';
			halo.popup.showLoadForm(ajaxCall);
		},
		saveField: function (fieldId) {
			var values = halo.util.getFormValues('popupForm');
			halo.jax.call("admin,fields", "SaveField", fieldId, values);
		},
		deleteField: function (fieldId) {
			halo.jax.call("admin,fields", "DeleteField", fieldId);
		},
		deleteSelectedField: function () {
			//get selected field
			halo.util.getCheckedItem(halo.field.deleteField);
		},
		getFieldConfig: function (fieldType) {
			halo.jax.call("admin,fields", "GetFieldConfig", fieldType);
		}
	}
});

/* ============================================================ Label features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	label: {
        init: function(scope) {
            halo.label.initChainLabels(scope);
        },
		showEditLabelGroupForm: function (labelGroupId) {
			var ajaxCall = 'halo.jax.call("admin,labels", "ShowEditLabelGroupForm","' + labelGroupId + '");';
			halo.popup.showLoadForm(ajaxCall);
		},
		saveLabelGroup: function (labelGroupId) {
			var values = halo.util.getFormValues('popupForm');
			halo.jax.call("admin,labels", "SaveLabelGroup", labelGroupId, values);
		},
		deleteLabelGroup: function (labelGroupId) {
			halo.jax.call("admin,labels", "DeleteLabelGroup", labelGroupId);
		},
		deleteSelectedLabelGroup: function () {
			//get selected label
			halo.util.getCheckedItem(halo.label.deleteLabelGroup);
		},
		showEditLabelForm: function (labelId, labelGroupId) {
			var ajaxCall = 'halo.jax.call("admin,labels", "ShowEditLabelForm","' + labelId + '","' + labelGroupId + '");';
			halo.popup.showLoadForm(ajaxCall);
		},
		saveLabel: function (labelId) {
			var values = halo.util.getFormValues('popupForm');
			halo.jax.call("admin,labels", "SaveLabel", labelId, values);
		},
		showLabelTypeOpt: function (opt) {
			//hide all current label type options
			jQuery('.label_type_opt').addClass('hidden');
			//show only active label options
			jQuery('.label_type_opt.opt_' + opt).removeClass('hidden');
		},
		deleteLabel: function (labelId) {
			halo.jax.call("admin,labels", "DeleteLabel", labelId);
		},
		listLabelsInGroup: function(groupcode, configName, zone){
			halo.jax.call("admin,labels", "ListLabelsInGroup", groupcode, configName, zone);
		},
        onChainLabels: function(ele, context) {
            var $ele = jQuery(ele);      
            var $chainedEle = jQuery('[name*="' + $ele.data('target') + '"]');
            var val = $ele.val();
            if ($ele.attr('name').match(/\.label\.badge/g)) {
                if (!val) {
                    $ele.selectpicker('val', ['']);
                } else if (val[0] == '') {
                    val.shift();
                    $ele.selectpicker('val', val);
                }
            }
            if (val == '') return;
            if (typeof val == 'string') {
                val = [val];
            }
            jQuery('option', $chainedEle).each(function() {
                var optVal = jQuery(this).attr('value');
                if (!optVal) return true;
                if (jQuery.inArray(optVal, val) > -1) {
                    jQuery(this).addClass('hidden');          
                } else {
                    jQuery(this).removeClass('hidden');
                }
                $chainedEle.selectpicker('refresh');   
            });
        },
        initChainLabels: function(scope) {
            jQuery('[name*=".label.status"]', scope).each(function() {
                var parts = jQuery(this).attr('name').split('.');
                halo.label.onChainLabels(this, parts[0]);
            });
        }
	}
});

/* ============================================================ Plugin features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	plugin: {
		showEditPluginForm: function (pluginId) {
			var ajaxCall = 'halo.jax.call("admin,plugins", "ShowEditPluginForm","' + pluginId + '");';
			halo.popup.showLoadForm(ajaxCall);
		},
		showInstallPluginForm: function () {
			var ajaxCall = 'halo.jax.call("admin,plugins", "ShowInstallPluginForm");';
			halo.popup.showLoadForm(ajaxCall);
		},
		savePlugin: function (pluginId) {
			var values = halo.util.getFormValues('popupForm');
			halo.jax.call("admin,plugins", "SavePlugin", pluginId, values);
		},
		deletePlugin: function (pluginId) {
			halo.jax.call("admin,plugins", "UninstallPlugin", pluginId);
		},
		deleteSelectedPlugin: function () {
			//get selected plugin
			halo.util.getCheckedItem(halo.plugin.deletePlugin);
		},
		getPluginConfig: function (pluginType) {
			halo.jax.call("admin,plugins", "GetPluginConfig", pluginType);
		},
		setPluginConfig: function (configParams) {
			jQuery('#pluginConfig').html(configParams);
		},
		moveUp: function (pluginId) {
			halo.jax.call("admin,plugins", "ChangeOrdering", pluginId, -1);
		}, 
		moveDown: function (pluginId) {
			halo.jax.call("admin,plugins", "ChangeOrdering", pluginId, 1);
		} 
	}
});

/* ============================================================ Filter features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	filter: {
		showEditFilterForm: function (filterId) {
			var ajaxCall = 'halo.jax.call("admin,filters", "ShowEditFilterForm","' + filterId + '");';
			halo.popup.showLoadForm(ajaxCall);
		}, 
		saveFilter: function (filterId) {
			var values = halo.util.getFormValues('popupForm');
			halo.jax.call("admin,filters", "SaveFilter", filterId, values);
		}, 
		resetFilters: function () {
			var values = halo.util.getFormValues('popupForm');
			halo.jax.call("admin,filters", "ResetFilters", values);
		}, 
		deleteFilter: function (filterId) {
			halo.jax.call("admin,filters", "DeleteFilter", filterId);
		},
		deleteSelectedFilter: function () {
			//get selected field
			halo.util.getCheckedItem(halo.filter.deleteFilter);
		}, 
		apply: function (ele) {
			var $form = jQuery(ele).closest('.filter_form');
			if ($form.length) {
				$form.submit();
			}
		}, 
		moveUp: function (filterId) {
			halo.jax.call("admin,filters", "ChangeOrdering", filterId, -1);
		}, 
		moveDown: function (filterId) {
			halo.jax.call("admin,filters", "ChangeOrdering", filterId, 1);
		}, 
		saveCustomFilter: function (ele) {
			if (typeof ele == 'string') {
				var $form = jQuery('#' + ele);
			} else {
				var $form = jQuery(ele).closest('.filter_form');
			}
			var formId = $form.attr('id');
			var filterValues = halo.util.getFormValues(formId);

			var values = halo.util.getFormValues('popupForm');
			halo.util.extendFormValues(values, filterValues);

			var $filter = jQuery('.halo-customfilter', $form).first();
			if ($filter.length) {
				var filterId = $filter.attr('data-filterid');
				halo.util.setFormValue(values, 'filter_id', filterId);
				halo.util.setFormValue(values, 'formId', formId);
				halo.jax.call("system", "SaveCustomFilter", values);
			}
		}, 
		redraw: function () {
			halo.util.reload();
		}, 
		init: function (scope){
			//filter init
            jQuery('.selectpicker', scope).each(function () {
                var $input = jQuery(this);
                $input.selectpicker();
                if ($input.data('empty')) {
                    $input.on('change', function(evt) {
                        var val = jQuery(this).val();
                        if (!val) {
                            val = jQuery(this).is('[multiple]') ? [''] : '';
                        } else if (typeof val == 'object' && val[0] == '') {
                            val.shift();
                        }
                        $input.selectpicker('val', val);
                    });
                }       
            });
		}
	}
});

/* ============================================================ Profile features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	profile: {
		showEditProfileForm: function (profileId) {
			var ajaxCall = 'halo.jax.call("admin,profiles", "ShowEditProfileForm","' + profileId + '");';
			halo.popup.showLoadWizard(ajaxCall);
		},
		saveProfile: function (profileId) {
			var values = halo.util.getFormValues('popupForm');
			halo.jax.call("admin,profiles", "SaveProfile", profileId, values);
		},
		deleteProfile: function (profileId) {
			halo.jax.call("admin,profiles", "DeleteProfile", profileId);
		},
		deleteSelectedProfile: function () {
			//get selected field
			halo.util.getCheckedItem(halo.profile.deleteProfile);
		},
		attachField: function (profileId) {
			var values = halo.util.getFormValues('popupForm');
			halo.jax.call("admin,profiles", "AttachFieldToProfile", profileId, values);
		},
		showAttachFieldForm: function (profileId, fieldId) {
			var ajaxCall = 'halo.jax.call("admin,profiles", "ShowAttachFieldForm","' + profileId + '","' + fieldId + '");';
			halo.popup.showLoadForm(ajaxCall);
		},
		showAddNewFieldForm: function (profileId) {
			var ajaxCall = 'halo.jax.call("admin,profiles", "ShowAddNewFieldForm","' + profileId + '");';
			halo.popup.showLoadForm(ajaxCall);
		},
		addNewProfileField: function (profileId) {
			var values = halo.util.getFormValues('popupForm');
			halo.jax.call("admin,profiles", "SaveNewProfileField", profileId, values);
		},

		detachField: function (fieldIds, profileId) {
			halo.jax.call("admin,profiles", "DetachFieldFromProfile", profileId, fieldIds);
		},
		detachSelectedField: function (profileId) {
			//get selected field
			halo.util.getCheckedItem(halo.profile.detachField, profileId);
		}, 
		moveFieldUp: function (profileId, fieldId) {
			halo.jax.call("admin,profiles", "ChangeFieldOrdering", profileId, fieldId, -1);
		}, 
		moveFieldDown: function (profileId, fieldId) {
			halo.jax.call("admin,profiles", "ChangeFieldOrdering", profileId, fieldId, 1);
		}
	}
});

/* ============================================================ Category features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	category: {
		deleteCategory: function (catId) {
			halo.jax.call("admin,commonCategories", "DeleteCategory", catId);
		},
		deleteSelectedCategory: function () {
			//get selected field
			halo.util.getCheckedItem(halo.category.deleteCategory);
		},
		moveUp: function (categoryId, categoryType) {
			halo.jax.call('admin,commonCategories', 'MoveUp', categoryId, categoryType);
		}, 
		moveDown: function (categoryId, categoryType) {
			halo.jax.call('admin,commonCategories', 'MoveDown', categoryId, categoryType);
		},
		editCategory: function(categoryId) {
			halo.jax.call('admin,commonCategories', 'EditCategory', categoryId);
		}
	}
});

/* ============================================================ Tools features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	tools: {
		installDatabase: function (step) {
			if (typeof step === 'undefined' || step == '') {
				//reset the form content
				halo.popup.setFormContent('');
			}
			halo.jax.call("admin,tools", "InstallDatabase", step);
		}, 
		clearCache: function () {
			halo.jax.call("admin,tools", "ClearCache");
		},
		activate: function () {
			var values = halo.util.getFormValues('licenseForm');
			halo.jax.call("admin,tools", "Activate", values);
		},
		changeLicense: function() {
			jQuery('.halo-activate-license-btn').removeClass('disabled');
		},
		checkUpdate: function() {
			halo.jax.call("admin,tools", "CheckUpdate");
		},
		liveUpdate: function() {
			halo.jax.call("admin,tools", "LiveUpdate");
		},
		renew: function() {
		
		},
		init: function(scope) {
			jQuery('.halo-starter-notice').removeClass('halo-notice-hide');
		}
	}
});

/* ============================================================ Online features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	online: {
		forceLogout: function () {
			halo.util.getCheckedItem(halo.online.logout);
		}, 
		logout: function (userId) {
			halo.jax.call("admin,users", "ForceLogout", userId);
		}
	}
});

/* ============================================================ Report features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	report: {
		deleteReport: function (reportId) {
			halo.jax.call("admin,reports", "DeleteReport", reportId);
		}, 
		deleteSelectedReport: function () {
			//get selected field
			halo.util.getCheckedItem(halo.report.deleteReport);
		}
	}
});

/* ============================================================ Role & Permission features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	role: {
		showEditPermissionToRole: function (roleId) {
			var ajaxCall = 'halo.jax.call("admin,roles", "ShowEditPermissionToRole","' + roleId + '");';
			halo.popup.showLoadForm(ajaxCall);
		},
		savePermissionToRole: function (roleId) {
			var values = halo.util.getFormValues('popupForm');
			halo.jax.call("admin,roles", "SavePermissionToRole", roleId, values);
		},
		showEditRoleToPermission: function (permissionId) {
			var ajaxCall = 'halo.jax.call("admin,roles", "ShowEditRoleToPermission","' + permissionId + '");';
			halo.popup.showLoadForm(ajaxCall);
		},
		saveRoleToPermission: function (permissionId) {
			var values = halo.util.getFormValues('popupForm');
			halo.jax.call("admin,roles", "SaveRoleToPermission", permissionId, values);
		},
		showEditRoleForm: function (roleId) {
			var ajaxCall = 'halo.jax.call("admin,roles", "ShowEditRoleForm","' + roleId + '");';
			halo.popup.showLoadForm(ajaxCall);
		},
		saveRole: function (roleId) {
			var values = halo.util.getFormValues('popupForm');
			halo.jax.call("admin,roles", "SaveRole", roleId, values);
		},
		deleteRole: function (roleId) {
			halo.jax.call("admin,roles", "DeleteRole", roleId);
		},
		deleteSelectedRole: function () {
			//get selected field
			halo.util.getCheckedItem(halo.role.deleteRole);
		},
		showEditPermissionForm: function (permissionId) {
			var ajaxCall = 'halo.jax.call("admin,roles", "ShowEditPermissionForm","' + permissionId + '");';
			halo.popup.showLoadForm(ajaxCall);
		},
		savePermission: function (permissionId) {
			var values = halo.util.getFormValues('popupForm');
			halo.jax.call("admin,roles", "SavePermission", permissionId, values);
		},
		deletePermission: function (permissionId) {
			halo.jax.call("admin,roles", "DeletePermission", permissionId);
		},
		deleteSelectedPermission: function () {
			//get selected field
			halo.util.getCheckedItem(halo.role.deletePermission);
		}, 
		removePermissionFromRole: function (permissionId, roleId) {
			halo.jax.call("admin,roles", "RemovePermissionFromRole", permissionId, roleId);
		}
	}
});

/* ============================================================ location features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	location: {
		//admin functions
		showEditDistrictForm: function (districtId) {
			halo.jax.call("admin,districts", "ShowEditDistrictForm", districtId);
		}, 
		saveDistrict: function (districtId) {
			var values = halo.util.getFormValues('popupForm');
			halo.jax.call("admin,districts", "SaveDistrict", districtId, values);
		}, 
		deleteDistrict: function (districtId) {
			halo.jax.call("admin,districts", "DeleteDistrict", districtId);
		}, 
		deleteSelectedDistrict: function () {
			halo.util.getCheckedItem(halo.location.deleteDistrict);
		}, 
		showEditCityForm: function (cityId) {
			halo.jax.call("admin,cities", "ShowEditCityForm", cityId);
		}, 
		saveCity: function (cityId) {
			var values = halo.util.getFormValues('popupForm');
			halo.jax.call("admin,cities", "SaveCity", cityId, values);
		}, 
		deleteCity: function (cityId) {
			halo.jax.call("admin,cities", "DeleteCity", cityId);
		}, 
		deleteSelectedCity: function () {
			halo.util.getCheckedItem(halo.location.deleteCity);
		}
	}
});

/* ============================================================ google chart features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	chart: {
		draw: function (query, queryFn, drawFn) {
			var context = this;
			if (jQuery.isFunction(queryFn)) {
				queryFn.apply(context, [query, drawFn]);
			}
		}, 
		draw_charts: function (el) {
			var container = jQuery(el).closest('.halo_statistic_dimension_charts');
			var chartDimension = jQuery('[name="chart_dimension"]').val();
			if(halo['chart']['draw_' + chartDimension]){
				halo['chart']['draw_' + chartDimension].apply(this, [container]);
			}
		}, 
		draw_VisitsChart: function (container) {

			var $config = jQuery('#halo_gatracking');
			var options = {
				width: 500,
				height: 300,
				legend: { position: 'top', maxLines: 3 },
				bar: { groupWidth: '75%' },
				orientation: 'horizontal',
			};

			jQuery('.halo_chart_wrapper_content', container).html('');
			jQuery('<div class="halo_chart_content_visitors halo-pull-left">').appendTo(jQuery('.halo_chart_wrapper_content', container));
			if (parseInt($config.attr('data-gauser')) > 0) { //page type dimention is configured
				options.width = 350;		//make the chart width smaller
				halo.chart.draw_VisitsChart_Basic(container, options);
				//draw extra page view chart
				jQuery('<div class="halo_chart_content_usertype halo-pull-left">').appendTo(jQuery('.halo_chart_wrapper_content', container));
				var options2 = {
					width: 150,
					height: 300,
					title: 'Visitor By User Type',
					legend: { position: 'top', maxLines: 3 }
				};
				halo.chart.draw_VisitsChart_Extra(container, options2);
			} else {
				halo.chart.draw_VisitsChart_Basic(container, options);
			}

		}, 
		draw_VisitsChart_Basic: function (container, options) {
			//1. build the query
			var endDate = new Date();
			var period = parseInt(jQuery('[name="chart_period"]', container).val());
			var startDate = new Date();

			startDate.setDate(startDate.getDate() - period);
			if (period != 0) endDate.setDate(endDate.getDate() - 1);		//for old data (period == 0)

			if (period <= 1) {
				var dimensions = 'ga:dateHour,ga:visitorType';
				var sort = 'ga:dateHour';
			} else {
				var dimensions = 'ga:date,ga:visitorType';
				var sort = 'ga:date';
			}
			var query = {    'ids': 'ga:' + jQuery('#halo_gatracking').attr('data-gaprofile'),
				'start-date': halo.util.dateToYMD(startDate),
				'end-date': halo.util.dateToYMD(endDate),
				'metrics': 'ga:visits',
				'dimensions': dimensions,
				'sort': sort,
				'maxResults': 100
			}
			//2. define drawFn
			function drawChartWrapper(results) {
				var endDate = new Date();
				var startDate = new Date(endDate);

				if (period != 0) endDate.setDate(endDate.getDate() - 1);		//for old data (period == 0)

				startDate.setDate(startDate.getDate() - period);
				if (period <= 1) {
					var nSample = 24;
				} else {
					var nSample = period;
				}

				google.load("visualization", "1", {packages: ["corechart"], callback: drawChart});
				function drawChart() {
					//setup datatable headers
					var data = new google.visualization.DataTable();
					if (period <= 1) {
						data.addColumn('datetime', 'Datetime');
					} else {
						data.addColumn('date', 'Date');
					}
					data.addColumn('number', 'New Visitors');
					data.addColumn('number', 'Return Visitors');

					data.addRows(nSample);

					var startDate = new Date();
					var searchIndex = [];

					var startDate = new Date();
					startDate.setDate(startDate.getDate() - period);
					startDate.setHours(0);
					startDate.setMinutes(0);
					startDate.setSeconds(0);

					//feed empty data
					for (var i = 0; i < nSample; i++) {
						var d = new Date(startDate);
						if (period <= 1) {
							var h = d.getHours() + i;
							d.setHours(h);

							searchIndex.push((halo.util.dateToYMD(d) + (h <= 9 ? '0' + h : h)).replace(/-/g, ""));
						} else {
							d.setDate(d.getDate() + i)
							searchIndex.push(halo.util.dateToYMD(d).replace(/-/g, ""));
						}
						data.setCell(i, 0, d);
						data.setCell(i, 1, 0);
						data.setCell(i, 2, 0);
					}
					//feed search result
					if (typeof results.rows !== 'undefined' && results.rows.length) {
						//build search by date index

						for (var i = 0; i < results.rows.length; i++) {
							var row = results.rows[i];
							var rowIdx = searchIndex.indexOf(row[0]);		//indexing by date
							var colIdx = (row[1] == 'New Visitor') ? 1 : 2;
							var val = row[2];
							data.setCell(rowIdx, colIdx, val);
						}
					}

					var chart = new google.visualization.BarChart(jQuery('.halo_chart_content_visitors', container)[0]);
					chart.draw(data, options);
				}
			}

			//3. call the halo.chart.draw wrapper function
			halo.chart.draw(query, halo.ga.getData, drawChartWrapper);
		}, 
		draw_VisitsChart_Extra: function (container, options) {
			//1. build the query
			var endDate = new Date();
			var period = parseInt(jQuery('[name="chart_period"]', container).val());
			var startDate = new Date();

			startDate.setDate(startDate.getDate() - period);
			if (period != 0) endDate.setDate(endDate.getDate() - 1);		//for old data (period == 0)

			var $config = jQuery('#halo_gatracking');
			var $userTypeDimension = parseInt($config.attr('data-gauser'));
			if (period <= 1) {
				var dimensions = 'ga:dimension' + $userTypeDimension;
			} else {
				var dimensions = 'ga:dimension' + $userTypeDimension;
			}

			var query = {    'ids': 'ga:' + $config.attr('data-gaprofile'),
				'start-date': halo.util.dateToYMD(startDate),
				'end-date': halo.util.dateToYMD(endDate),
				'metrics': 'ga:visits',
				'dimensions': dimensions,
				'maxResults': 100
			}
			//2. define drawFn
			function drawChartWrapper(results) {

				google.load("visualization", "1", {packages: ["corechart"], callback: drawChart});
				function drawChart() {
					//setup datatable headers
					var data = new google.visualization.DataTable();
					data.addColumn('string', 'User Type');
					data.addColumn('number', 'Visits');

					//feed search result
					if (typeof results.rows !== 'undefined' && results.rows.length) {
						//build search by date index
						//use searchIndex to prevent the duplicate  rows
						var searchIndex = [];
						for (var i = 0; i < results.rows.length; i++) {
							var row = results.rows[i];
							var idx, idx2;
							idx = searchIndex.indexOf(row[0])
							if (idx >= 0) {
								data.setCell(idx, 1, data.getValue(idx, 1) + parseInt(row[1]));
							} else {
								data.addRow([row[0], parseInt(row[1])]);
								searchIndex.push(row[0]);
							}
						}
					}

					var chart = new google.visualization.PieChart(jQuery(".halo_chart_content_usertype", container)[0]);
					chart.draw(data, options);
				}
			}

			//3. call the halo.chart.draw wrapper function
			halo.chart.draw(query, halo.ga.getData, drawChartWrapper);
		}, 
		draw_PageViewsChart: function (container) {

			var $config = jQuery('#halo_gatracking');
			var options = {
				width: 500,
				height: 300,
				legend: { position: 'top', maxLines: 3 },
				bar: { groupWidth: '75%' },
				orientation: 'horizontal',
			};

			jQuery('.halo_chart_wrapper_content', container).html('');
			jQuery('<div class="halo_chart_content_pageviews halo-pull-left">').appendTo(jQuery('.halo_chart_wrapper_content', container));
			if (parseInt($config.attr('data-gapagetype')) > 0) { //page type dimention is configured
				options.width = 350;		//make the chart width smaller
				halo.chart.draw_PageViewsChart_Basic(container, options);
				//draw extra page view chart
				jQuery('<div class="halo_chart_content_pagetype halo-pull-left">').appendTo(jQuery('.halo_chart_wrapper_content', container));
				var options2 = {
					width: 150,
					height: 300,
					title: 'Pageview By PageType',
					legend: { position: 'top', maxLines: 3 }
				};
				halo.chart.draw_PageViewsChart_Extra(container, options2);
			} else {
				halo.chart.draw_PageViewsChart_Basic(container, options);
			}

		}, 
		draw_PageViewsChart_Basic: function (container, options) {
			//1. build the query
			var endDate = new Date();
			var period = parseInt(jQuery('[name="chart_period"]', container).val());
			var startDate = new Date();

			startDate.setDate(startDate.getDate() - period);
			if (period != 0) endDate.setDate(endDate.getDate() - 1);		//for old data (period == 0)

			var $config = jQuery('#halo_gatracking');
			if (period <= 1) {
				var dimensions = 'ga:dateHour';
				var sort = 'ga:dateHour';
			} else {
				var dimensions = 'ga:date';
				var sort = 'ga:date';
			}
			var query = {    'ids': 'ga:' + $config.attr('data-gaprofile'),
				'start-date': halo.util.dateToYMD(startDate),
				'end-date': halo.util.dateToYMD(endDate),
				'metrics': 'ga:pageviews',
				'dimensions': dimensions,
				'sort': sort,
				'maxResults': 100
			}
			//2. define drawFn
			function drawChartWrapper(results) {
				var endDate = new Date();
				var startDate = new Date(endDate);

				if (period != 0) endDate.setDate(endDate.getDate() - 1);		//for old data (period == 0)

				startDate.setDate(startDate.getDate() - period);
				if (period <= 1) {
					var nSample = 24;
				} else {
					var nSample = period;
				}

				google.load("visualization", "1", {packages: ["corechart"], callback: drawChart});
				function drawChart() {
					//setup datatable headers
					var data = new google.visualization.DataTable();
					if (period <= 1) {
						data.addColumn('datetime', 'Datetime');
					} else {
						data.addColumn('date', 'Date');
					}
					data.addColumn('number', 'Pageviews');

					data.addRows(nSample);

					var startDate = new Date();
					var searchIndex = [];

					var startDate = new Date();
					startDate.setDate(startDate.getDate() - period);
					startDate.setHours(0);
					startDate.setMinutes(0);
					startDate.setSeconds(0);

					//feed empty data
					for (var i = 0; i < nSample; i++) {
						var d = new Date(startDate);
						if (period <= 1) {
							var h = d.getHours() + i;
							d.setHours(h);
							searchIndex.push((halo.util.dateToYMD(d) + (h <= 9 ? '0' + h : h)).replace(/-/g, ""));
						} else {
							d.setDate(d.getDate() + i)
							searchIndex.push(halo.util.dateToYMD(d).replace(/-/g, ""));
						}
						data.setCell(i, 0, d);
						data.setCell(i, 1, 0);
					}
					//feed search result
					if (typeof results.rows !== 'undefined' && results.rows.length) {
						//build search by date index

						for (var i = 0; i < results.rows.length; i++) {
							var row = results.rows[i];
							var rowIdx = searchIndex.indexOf(row[0]);		//indexing by date
							var colIdx = 1;
							var val = row[1];
							data.setCell(rowIdx, colIdx, val);
						}
					}

					var chart = new google.visualization.BarChart(jQuery(".halo_chart_content_pageviews", container)[0]);
					chart.draw(data, options);
				}
			}

			//3. call the halo.chart.draw wrapper function
			halo.chart.draw(query, halo.ga.getData, drawChartWrapper);
		}, 
		draw_PageViewsChart_Extra: function (container, options) {
			//1. build the query
			var endDate = new Date();
			var period = parseInt(jQuery('[name="chart_period"]', container).val());
			var startDate = new Date();

			startDate.setDate(startDate.getDate() - period);
			if (period != 0) endDate.setDate(endDate.getDate() - 1);		//for old data (period == 0)

			var $config = jQuery('#halo_gatracking');
			var $pageTypeDimension = parseInt($config.attr('data-gapagetype'));
			if (period <= 1) {
				var dimensions = 'ga:dimension' + $pageTypeDimension;
			} else {
				var dimensions = 'ga:dimension' + $pageTypeDimension;
			}

			var query = {    'ids': 'ga:' + $config.attr('data-gaprofile'),
				'start-date': halo.util.dateToYMD(startDate),
				'end-date': halo.util.dateToYMD(endDate),
				'metrics': 'ga:pageviews',
				'dimensions': dimensions,
				'maxResults': 100
			}
			//2. define drawFn
			function drawChartWrapper(results) {

				google.load("visualization", "1", {packages: ["corechart"], callback: drawChart});
				function drawChart() {
					//setup datatable headers
					var data = new google.visualization.DataTable();
					data.addColumn('string', 'Page Type');
					data.addColumn('number', 'Pageviews');

					//feed search result
					if (typeof results.rows !== 'undefined' && results.rows.length) {
						//build search by date index
						//use searchIndex to prevent the duplicate  rows
						var searchIndex = [];
						for (var i = 0; i < results.rows.length; i++) {
							var row = results.rows[i];
							var idx, idx2;
							idx = searchIndex.indexOf(row[0])
							if (idx >= 0) {
								data.setCell(idx, 1, data.getValue(idx, 1) + parseInt(row[1]));
							} else {
								data.addRow([row[0].replace('halo_', '') + ' page', parseInt(row[1])]);
								searchIndex.push(row[0]);
							}
						}
					}

					var chart = new google.visualization.PieChart(jQuery(".halo_chart_content_pagetype", container)[0]);
					chart.draw(data, options);
				}
			}

			//3. call the halo.chart.draw wrapper function
			halo.chart.draw(query, halo.ga.getData, drawChartWrapper);
		}, 
		draw_PlatformDeviceChart: function (container) {
			//1. build the query
			var endDate = new Date();
			var period = parseInt(jQuery('[name="chart_period"]', container).val());
			var startDate = new Date();

			startDate.setDate(startDate.getDate() - period);
			if (period != 0) endDate.setDate(endDate.getDate() - 1);		//for old data (period == 0)

			var dimensions = 'ga:browser,ga:deviceCategory';
			var sort = 'ga:visits';
			var query = {    'ids': 'ga:' + jQuery('#halo_gatracking').attr('data-gaprofile'),
				'start-date': halo.util.dateToYMD(startDate),
				'end-date': halo.util.dateToYMD(endDate),
				'metrics': 'ga:visits',
				'dimensions': dimensions,
				'sort': sort,
				'maxResults': 100
			}
			//2. define drawFn
			function drawChartWrapper(results) {
				google.load("visualization", "1", {packages: ["corechart"], callback: drawChart});
				function drawChart() {

					//setup datatable headers
					var data = new google.visualization.DataTable();
					data.addColumn('string', 'Browser');
					data.addColumn('number', 'Visits');

					var data2 = new google.visualization.DataTable();
					data2.addColumn('string', 'Device');
					data2.addColumn('number', 'Visits');

					//feed search result
					if (typeof results.rows !== 'undefined' && results.rows.length) {
						//build search by date index
						//use searchIndex to prevent the duplicate  rows
						var searchIndex = [];
						var searchIndex2 = [];
						for (var i = 0; i < results.rows.length; i++) {
							var row = results.rows[i];
							var idx, idx2;
							idx = searchIndex.indexOf(row[0])
							if (idx >= 0) {
								data.setCell(idx, 1, data.getValue(idx, 1) + parseInt(row[2]));
							} else {
								data.addRow([row[0], parseInt(row[2])]);
								searchIndex.push(row[0]);
							}

							idx2 = searchIndex2.indexOf(row[1]);
							if (idx2 >= 0) {
								data2.setCell(idx2, 1, data2.getValue(idx2, 1) + parseInt(row[2]));
							} else {
								data2.addRow([row[1], parseInt(row[2])]);
								searchIndex2.push(row[1]);
							}
						}
					}
					var options = {
						width: 250,
						height: 300,
						title: 'Visitors By Browser',
						legend: { position: 'top', maxLines: 3 }
					};
					var options2 = {
						width: 250,
						height: 300,
						title: 'Visitors By Device',
						legend: { position: 'top', maxLines: 3 }
					};
					jQuery('.halo_chart_wrapper_content', container).html('');

					jQuery('<div class="halo_chart_content_browser halo-pull-left">').appendTo(jQuery('.halo_chart_wrapper_content', container));
					jQuery('<div class="halo_chart_content_device halo-pull-left">').appendTo(jQuery('.halo_chart_wrapper_content', container));


					var chart = new google.visualization.PieChart(jQuery(".halo_chart_content_browser", container)[0]);
					chart.draw(data, options);

					var chart2 = new google.visualization.PieChart(jQuery(".halo_chart_content_device", container)[0]);
					chart2.draw(data2, options2);
				}
			}

			//3. call the halo.chart.draw wrapper function
			halo.chart.draw(query, halo.ga.getData, drawChartWrapper);
		}, 
		draw_GeoNetworkChart: function (container) {
			//1. build the query
			var endDate = new Date();
			var period = parseInt(jQuery('[name="chart_period"]', container).val());
			var startDate = new Date();

			startDate.setDate(startDate.getDate() - period);
			if (period != 0) endDate.setDate(endDate.getDate() - 1);		//for old data (period == 0)

			var dimensions = 'ga:city';
			var sort = 'ga:visits';
			var query = {    'ids': 'ga:' + jQuery('#halo_gatracking').attr('data-gaprofile'),
				'start-date': halo.util.dateToYMD(startDate),
				'end-date': halo.util.dateToYMD(endDate),
				'metrics': 'ga:visits',
				'dimensions': dimensions,
				'sort': sort,
				'maxResults': 100
			}
			//2. define drawFn
			function drawChartWrapper(results) {
				google.load("visualization", "1", {packages: ["corechart"], callback: drawChart});
				function drawChart() {

					//setup datatable headers
					var data = new google.visualization.DataTable();
					data.addColumn('string', 'City');
					data.addColumn('number', 'Visits');

					//feed search result
					if (typeof results.rows !== 'undefined' && results.rows.length) {
						//build search by date index
						//use searchIndex to prevent the duplicate  rows
						var searchIndex = [];
						for (var i = 0; i < results.rows.length; i++) {
							var row = results.rows[i];
							var idx, idx2;
							idx = searchIndex.indexOf(row[0])
							if (idx >= 0) {
								data.setCell(idx, 1, data.getValue(idx, 1) + parseInt(row[1]));
							} else {
								data.addRow([row[0], parseInt(row[1])]);
								searchIndex.push(row[0]);
							}
						}
					}
					var options = {
						width: 500,
						height: 300,
						title: 'Visitors By Location',
						legend: { position: 'top', maxLines: 3 }
					};

					jQuery('.halo_chart_wrapper_content', container).html('');

					var chart = new google.visualization.PieChart(jQuery(".halo_chart_wrapper_content", container)[0]);
					chart.draw(data, options);

				}
			}

			//3. call the halo.chart.draw wrapper function
			halo.chart.draw(query, halo.ga.getData, drawChartWrapper);
		}, 
		draw_SocialActivitiesChart: function (container) {
			//1. build the query
			var endDate = new Date();
			var period = parseInt(jQuery('[name="chart_period"]', container).val());
			var startDate = new Date();

			startDate.setDate(startDate.getDate() - period);
			if (period != 0) endDate.setDate(endDate.getDate() - 1);		//for old data (period == 0)

			var dimensions = 'ga:socialInteractionNetworkAction';
			var sort = 'ga:socialInteractions';
			var query = {    'ids': 'ga:' + jQuery('#halo_gatracking').attr('data-gaprofile'),
				'start-date': halo.util.dateToYMD(startDate),
				'end-date': halo.util.dateToYMD(endDate),
				'metrics': 'ga:socialInteractions',
				'dimensions': dimensions,
				'sort': sort,
				'maxResults': 100
			}
			//2. define drawFn
			function drawChartWrapper(results) {
				google.load("visualization", "1", {packages: ["corechart"], callback: drawChart});
				function drawChart() {

					//setup datatable headers
					var data = new google.visualization.DataTable();
					data.addColumn('string', 'Social Activities');
					data.addColumn('number', 'Hits');

					//feed search result
					if (typeof results.rows !== 'undefined' && results.rows.length) {
						//build search by date index
						//use searchIndex to prevent the duplicate  rows
						var searchIndex = [];
						for (var i = 0; i < results.rows.length; i++) {
							var row = results.rows[i];
							var idx, idx2;
							idx = searchIndex.indexOf(row[0])
							if (idx >= 0) {
								data.setCell(idx, 1, data.getValue(idx, 1) + parseInt(row[1]));
							} else {
								data.addRow([row[0], parseInt(row[1])]);
								searchIndex.push(row[0]);
							}
						}
					}
					var options = {
						width: 500,
						height: 300,
						title: 'Social Activities',
						legend: { position: 'top', maxLines: 3 }
					};

					jQuery('.halo_chart_wrapper_content', container).html('');

					var chart = new google.visualization.PieChart(jQuery(".halo_chart_wrapper_content", container)[0]);
					chart.draw(data, options);

				}
			}

			//3. call the halo.chart.draw wrapper function
			halo.chart.draw(query, halo.ga.getData, drawChartWrapper);
		}
	}
});

