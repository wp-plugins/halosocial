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
		},
		syncRoles: function(){
			halo.jax.call("admin,roles", "SyncRoles");
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

/* ============================================================ theme features
 *
 * ============================================================ */
jQuery.extend(true, halo, {
	theme: {
		//admin functions
		customize: function (index, reset) {
			var values = {};
			index = index?index:0;
			reset = parseInt(reset);
			if(!reset) {
				values = halo.util.getFormValues('halo-admin-form');
			}
			halo.theme.startCustomize();
			console.log(values);
			halo.jax.call("admin,config", "CustomizeTheme", values, index);
		},
		startCustomize: function(btnClass){
			if(!btnClass) {
				btnClass = '.halo-theme-apply-btn';
			}
			if(!jQuery(btnClass + ' .halo-loading').length) {
				jQuery(btnClass).prepend('<span class="halo-loading fa fa-spinner fa-spin"></span>');
				window.onbeforeunload = function() {
					return "Theme customization is running. Please wait until it is completed!";
				};
			}		
		},
		doneCustomize: function() {
			if(jQuery('.halo-theme-apply-btn .halo-loading').length) {
				jQuery('.halo-theme-apply-btn').find('.halo-loading').remove();
				window.onbeforeunload = null;
				halo.util.reload();
			}		
			if(jQuery('.halo-theme-reset-btn .halo-loading').length) {
				jQuery('.halo-theme-reset-btn').find('.halo-loading').remove();
				window.onbeforeunload = null;
				halo.util.reload();
			}		
		},
		init: function(scope) {
			jQuery('.halo-color-input input', scope).colorPicker();
		},
		resetCustomize: function(){
			halo.theme.startCustomize('.halo-theme-reset-btn');
			halo.jax.call("admin,config", "ResetTheme");
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


/*! tinyColorPicker - v1.0.0 2015-04-18 */
!function(a,b){"use strict";function c(a,c,d,f,g){if("string"==typeof c){var c=t.txt2color(c);d=c.type,n[d]=c[d],g=g!==b?g:c.alpha}else if(c)for(var h in c)a[d][h]=k(c[h]/l[d][h][1],0,1);return g!==b&&(a.alpha=+g),e(d,f?a:b)}function d(a,b,c){var d=m.options.grey,e={};return e.RGB={r:a.r,g:a.g,b:a.b},e.rgb={r:b.r,g:b.g,b:b.b},e.alpha=c,e.equivalentGrey=Math.round(d.r*a.r+d.g*a.g+d.b*a.b),e.rgbaMixBlack=i(b,{r:0,g:0,b:0},c,1),e.rgbaMixWhite=i(b,{r:1,g:1,b:1},c,1),e.rgbaMixBlack.luminance=h(e.rgbaMixBlack,!0),e.rgbaMixWhite.luminance=h(e.rgbaMixWhite,!0),m.options.customBG&&(e.rgbaMixCustom=i(b,m.options.customBG,c,1),e.rgbaMixCustom.luminance=h(e.rgbaMixCustom,!0),m.options.customBG.luminance=h(m.options.customBG,!0)),e}function e(a,b){var c,e,k,o=b||n,p=t,q=m.options,r=l,s=o.RND,u="",v="",w={hsl:"hsv",rgb:a},x=s.rgb;if("alpha"!==a){for(var y in r)if(!r[y][y]){a!==y&&(v=w[y]||"rgb",o[y]=p[v+"2"+y](o[v])),s[y]||(s[y]={}),c=o[y];for(u in c)s[y][u]=Math.round(c[u]*r[y][u][1])}x=s.rgb,o.HEX=p.RGB2HEX(x),o.equivalentGrey=q.grey.r*o.rgb.r+q.grey.g*o.rgb.g+q.grey.b*o.rgb.b,o.webSave=e=f(x,51),o.webSmart=k=f(x,17),o.saveColor=x.r===e.r&&x.g===e.g&&x.b===e.b?"web save":x.r===k.r&&x.g===k.g&&x.b===k.b?"web smart":"",o.hueRGB=t.hue2RGB(o.hsv.h),b&&(o.background=d(x,o.rgb,o.alpha))}var z,A,B,C=o.rgb,D=o.alpha,E="luminance",F=o.background;return z=i(C,{r:0,g:0,b:0},D,1),z[E]=h(z,!0),o.rgbaMixBlack=z,A=i(C,{r:1,g:1,b:1},D,1),A[E]=h(A,!0),o.rgbaMixWhite=A,q.customBG&&(B=i(C,F.rgbaMixCustom,D,1),B[E]=h(B,!0),B.WCAG2Ratio=j(B[E],F.rgbaMixCustom[E]),o.rgbaMixBGMixCustom=B,B.luminanceDelta=Math.abs(B[E]-F.rgbaMixCustom[E]),B.hueDelta=g(F.rgbaMixCustom,B,!0)),o.RGBLuminance=h(x),o.HUELuminance=h(o.hueRGB),q.convertCallback&&q.convertCallback(o,a),o}function f(a,b){var c={},d=0,e=b/2;for(var f in a)d=a[f]%b,c[f]=a[f]+(d>e?b-d:-d);return c}function g(a,b,c){return(Math.max(a.r-b.r,b.r-a.r)+Math.max(a.g-b.g,b.g-a.g)+Math.max(a.b-b.b,b.b-a.b))*(c?255:1)/765}function h(a,b){for(var c=b?1:255,d=[a.r/c,a.g/c,a.b/c],e=m.options.luminance,f=d.length;f--;)d[f]=d[f]<=.03928?d[f]/12.92:Math.pow((d[f]+.055)/1.055,2.4);return e.r*d[0]+e.g*d[1]+e.b*d[2]}function i(a,c,d,e){var f={},g=d!==b?d:1,h=e!==b?e:1,i=g+h*(1-g);for(var j in a)f[j]=(a[j]*g+c[j]*h*(1-g))/i;return f.a=i,f}function j(a,b){var c=1;return c=a>=b?(a+.05)/(b+.05):(b+.05)/(a+.05),Math.round(100*c)/100}function k(a,b,c){return a>c?c:b>a?b:a}var l={rgb:{r:[0,255],g:[0,255],b:[0,255]},hsv:{h:[0,360],s:[0,100],v:[0,100]},hsl:{h:[0,360],s:[0,100],l:[0,100]},alpha:{alpha:[0,1]},HEX:{HEX:[0,16777215]}},m={},n={},o={r:.298954,g:.586434,b:.114612},p={r:.2126,g:.7152,b:.0722},q=a.Colors=function(a){this.colors={RND:{}},this.options={color:"rgba(204, 82, 37, 0.8)",grey:o,luminance:p,valueRanges:l},r(this,a||{})},r=function(a,d){var e,f=a.options;s(a);for(var g in d)d[g]!==b&&(f[g]=d[g]);e=f.customBG,f.customBG="string"==typeof e?t.txt2color(e).rgb:e,n=c(a.colors,f.color,b,!0)},s=function(a){m!==a&&(m=a,n=a.colors)};q.prototype.setColor=function(a,d,f){return s(this),a?c(this.colors,a,d,b,f):(f!==b&&(this.colors.alpha=f),e(d))},q.prototype.setCustomBackground=function(a){return s(this),this.options.customBG="string"==typeof a?t.txt2color(a).rgb:a,c(this.colors,b,"rgb")},q.prototype.saveAsBackground=function(){return s(this),c(this.colors,b,"rgb",!0)};var t={txt2color:function(a){var b={},c=a.replace(/(?:#|\)|%)/g,"").split("("),d=(c[1]||"").split(/,\s*/),e=c[1]?c[0].substr(0,3):"rgb",f="";if(b.type=e,b[e]={},c[1])for(var g=3;g--;)f=e[g]||e.charAt(g),b[e][f]=+d[g]/l[e][f][1];else b.rgb=t.HEX2rgb(c[0]);return b.alpha=d[3]?+d[3]:1,b},RGB2HEX:function(a){return((a.r<16?"0":"")+a.r.toString(16)+(a.g<16?"0":"")+a.g.toString(16)+(a.b<16?"0":"")+a.b.toString(16)).toUpperCase()},HEX2rgb:function(a){return a=a.split(""),{r:parseInt(a[0]+a[a[3]?1:0],16)/255,g:parseInt(a[a[3]?2:1]+(a[3]||a[1]),16)/255,b:parseInt((a[4]||a[2])+(a[5]||a[2]),16)/255}},hue2RGB:function(a){var b=6*a,c=~~b%6,d=6===b?0:b-c;return{r:Math.round(255*[1,1-d,0,0,d,1][c]),g:Math.round(255*[d,1,1,1-d,0,0][c]),b:Math.round(255*[0,0,d,1,1,1-d][c])}},rgb2hsv:function(a){var b,c,d,e=a.r,f=a.g,g=a.b,h=0;return g>f&&(f=g+(g=f,0),h=-1),c=g,f>e&&(e=f+(f=e,0),h=-2/6-h,c=Math.min(f,g)),b=e-c,d=e?b/e:0,{h:1e-15>d?n&&n.hsl&&n.hsl.h||0:b?Math.abs(h+(f-g)/(6*b)):0,s:e?b/e:n&&n.hsv&&n.hsv.s||0,v:e}},hsv2rgb:function(a){var b=6*a.h,c=a.s,d=a.v,e=~~b,f=b-e,g=d*(1-c),h=d*(1-f*c),i=d*(1-(1-f)*c),j=e%6;return{r:[d,h,g,g,i,d][j],g:[i,d,d,h,g,g][j],b:[g,g,i,d,d,h][j]}},hsv2hsl:function(a){var b=(2-a.s)*a.v,c=a.s*a.v;return c=a.s?1>b?b?c/b:0:c/(2-b):0,{h:a.h,s:a.v||c?c:n&&n.hsl&&n.hsl.s||0,l:b/2}},rgb2hsl:function(a,b){var c=t.rgb2hsv(a);return t.hsv2hsl(b?c:n.hsv=c)},hsl2rgb:function(a){var b=6*a.h,c=a.s,d=a.l,e=.5>d?d*(1+c):d+c-c*d,f=d+d-e,g=e?(e-f)/e:0,h=~~b,i=b-h,j=e*g*i,k=f+j,l=e-j,m=h%6;return{r:[e,l,f,f,k,e][m],g:[k,e,e,l,f,f][m],b:[f,f,k,e,e,l][m]}}}}(window);
!function(a,b,c){"use strict";function d(b){return b.value||b.getAttribute("value")||a(b).css("background-color")||"#fff"}function e(a){return a.originalEvent.touches?a.originalEvent.touches[0]:a}function f(b){return a(b.find(s.doRender)[0]||b[0])}function g(b){var c=a(this),e=c.offset(),g=a(window),i=s.gap;b?(t=f(c),q.$trigger=c,(u||h()).css({left:(u[0]._left=e.left)-((u[0]._left=u[0]._left+u[0]._width-(g.scrollLeft()+g.width()))+i>0?u[0]._left+i:0),top:(u[0]._top=e.top+c.outerHeight())-((u[0]._top=u[0]._top+u[0]._height-(g.scrollTop()+g.height()))+i>0?u[0]._top+i:0)}).show(s.animationSpeed,function(){b!==!0&&(y._width=y.width(),v._width=v.width(),v._height=v.height(),r.setColor(d(t[0])),n(!0))})):a(u).hide(s.animationSpeed,function(){t.blur(),q.$trigger=null,n(!1)})}function h(){return a("head").append('<style type="text/css">'+(s.css||I)+(s.cssAddon||"")+"</style>"),q.$UI=u=a(H).css({margin:s.margin}).appendTo("body").show(0,function(){var b=a(this);F=s.GPU&&b.css("perspective")!==c,v=a(".cp-xy-slider",this),w=a(".cp-xy-cursor",this),x=a(".cp-z-cursor",this),y=a(".cp-alpha",this).toggle(!!s.opacity),z=a(".cp-alpha-cursor",this),s.buildCallback.call(q,b),b.prepend("<div>").children().eq(0).css("width",b.children().eq(0).width()),this._width=this.offsetWidth,this._height=this.offsetHeight}).hide().on(D,".cp-xy-slider,.cp-z-slider,.cp-alpha",i)}function i(b){var c=this.className.replace(/cp-(.*?)(?:\s*|$)/,"$1").replace("-","_");b.preventDefault&&b.preventDefault(),b.returnValue=!1,t._offset=a(this).offset(),(c="xy_slider"===c?k:"z_slider"===c?l:m)(b),A.on(E,j).on(C,c)}function j(){A.off(C).off(E)}function k(a){var b=e(a),c=b.pageX-t._offset.left,d=b.pageY-t._offset.top;r.setColor({s:c/v._width*100,v:100-d/v._height*100},"hsv"),n()}function l(a){{var b=e(a).pageY-t._offset.top;r.colors.hsv}r.setColor({h:360-b/v._height*360},"hsv"),n()}function m(a){var b=e(a).pageX-t._offset.left,c=b/y._width;r.setColor({},"rgb",c>1?1:0>c?0:c),n()}function n(a){var b=r.colors,d=b.hueRGB,e=b.RND.rgb,f=b.RND.hsl,g="#222",h="#ddd",i=t.data("colorMode"),j=1!==b.alpha,k=Math.round(100*b.alpha)/100,l=e.r+", "+e.g+", "+e.b,m="HEX"!==i||j?"rgb"===i||"HEX"===i&&j?j?"rgba("+l+", "+k+")":"rgb("+l+")":"hsl"+(j?"a(":"(")+f.h+", "+f.s+"%, "+f.l+"%"+(j?", "+k:"")+")":"#"+b.HEX,n=b.HUELuminance>.22?g:h,p=b.rgbaMixBlack.luminance>.22?g:h,q=(1-b.hsv.h)*v._height,s=b.hsv.s*v._width,u=(1-b.hsv.v)*v._height,A=k*y._width,B=F?"translate3d":"",C=t.val(),D=t[0].hasAttribute("value")&&""===C&&a!==c;v._css={backgroundColor:"rgb("+d.r+","+d.g+","+d.b+")"},w._css={transform:B+"("+s+"px, "+u+"px, 0)",left:F?"":s,top:F?"":u,borderColor:b.RGBLuminance>.22?g:h},x._css={transform:B+"(0, "+q+"px, 0)",top:F?"":q,borderColor:"transparent "+n},y._css={backgroundColor:"rgb("+l+")"},z._css={transform:B+"("+A+"px, 0, 0)",left:F?"":A,borderColor:p+" transparent"},t._css={backgroundColor:D?"":m,color:D?"":b.rgbaMixBGMixCustom.luminance>.22?g:h},t.text=D?"":C!==m?m:"",a!==c?o(a):G(o)}function o(a){v.css(v._css),w.css(w._css),x.css(x._css),y.css(y._css),z.css(z._css),s.doRender&&t.css(t._css),t.text&&t.val(t.text),s.renderCallback.call(q,t,"boolean"==typeof a?a:c)}var p,q,r,s,t,u,v,w,x,y,z,A=a(document),B="",C="touchmove.a mousemove.a pointermove.a",D="touchstart.a mousedown.a pointerdown.a",E="touchend.a mouseup.a pointerup.a",F=!1,G=window.requestAnimationFrame||window.webkitRequestAnimationFrame||function(a){a()},H='<div class="cp-color-picker"><div class="cp-z-slider"><div class="cp-z-cursor"></div></div><div class="cp-xy-slider"><div class="cp-white"></div><div class="cp-xy-cursor"></div></div><div class="cp-alpha"><div class="cp-alpha-cursor"></div></div></div>',I=".cp-color-picker{position:absolute;overflow:hidden;padding:6px 6px 0;background-color:#444;color:#bbb;font-family:Arial,Helvetica,sans-serif;font-size:12px;font-weight:400;cursor:default;border-radius:5px}.cp-color-picker>div{position:relative;overflow:hidden}.cp-xy-slider{float:left;height:128px;width:128px;margin-bottom:6px;background:linear-gradient(to right,#FFF,rgba(255,255,255,0))}.cp-white{height:100%;width:100%;background:linear-gradient(rgba(0,0,0,0),#000)}.cp-xy-cursor{position:absolute;top:0;width:10px;height:10px;margin:-5px;border:1px solid #fff;border-radius:100%;box-sizing:border-box}.cp-z-slider{float:right;margin-left:6px;height:128px;width:20px;background:linear-gradient(red 0,#f0f 17%,#00f 33%,#0ff 50%,#0f0 67%,#ff0 83%,red 100%)}.cp-z-cursor{position:absolute;margin-top:-4px;width:100%;border:4px solid #fff;border-color:transparent #fff;box-sizing:border-box}.cp-alpha{clear:both;width:100%;height:16px;margin:6px 0;background:linear-gradient(to right,#444,rgba(0,0,0,0))}.cp-alpha-cursor{position:absolute;margin-left:-4px;height:100%;border:4px solid #fff;border-color:#fff transparent;box-sizing:border-box}",J=function(a){r=this.color=new b(a),s=r.options};J.prototype={render:n,toggle:g},a.fn.colorPicker=function(b){var c=function(){};return b=a.extend({animationSpeed:150,GPU:!0,doRender:!0,customBG:"#FFF",opacity:!0,renderCallback:c,buildCallback:c,body:document.body,scrollResize:!0,gap:4},b),!q&&b.scrollResize&&a(window).on("resize scroll",function(){q.$trigger&&q.toggle.call(q.$trigger[0],!0)}),p=p?p.add(this):this,p.colorPicker=q||(q=new J(b)),B+=(B?", ":"")+this.selector,a(b.body).off(".a").on(D,function(b){var c=a(b.target);-1!==a.inArray(c.closest(B)[0],p)||c.closest(u).length||g()}).on("focus.a click.a",B,g).on("change.a",B,function(){r.setColor(this.value||"#FFF"),p.colorPicker.render(!0)}),this.each(function(){var c=d(this),e=c.split("("),g=f(a(this));g.data("colorMode",e[1]?e[0].substr(0,3):"HEX").attr("readonly",s.preventFocus),b.doRender&&g.css({"background-color":c,color:function(){return r.setColor(c).rgbaMixBGMixCustom.luminance>.22?"#222":"#ddd"}})})}}(jQuery,Colors);
//# sourceMappingURL=jqColorPicker.js.map
