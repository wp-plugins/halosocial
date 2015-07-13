<?php
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

class HALOLabelAPI
{

    /**
     * get available label groups for a specific target
     * 
     * @param  object  $target 
     * @param  bool $manualOnly
     * @return array
     */
    public static function getLabelGroups($target, $manualOnly = true, $checkPermission = true)
    {
        $labelGroups = Cache::rememberForever('label.group.' . $target->getContext(), function () use ($target) {
            $rtn = new stdClass();
            //fire event to load all avaialble label groups configured for this target
            Event::fire('label.loadGroup', array($target->getContext(), &$rtn));
            if (isset($rtn->labelGroups)) {
                $rtn->labelGroups->load('labels');
                return $rtn->labelGroups;
            } else {
                return array();
            }
        });
		if(!$checkPermission) {
			return $labelGroups;
		}
        //show only available label groups
        foreach ($labelGroups as $gkey => $labelGroup) {
            foreach ($labelGroup->labels as $skey => $label) {
                if ($label->label_type == HALOLabelModel::LABEL_TYPE_MANUAL) {
                    $allowedRoles = $label->getParams('allowedRoles', array());
                    //init auth lib
                    $roles = HALOAuth::getRoles();
                    $accept = false;
                    //var_dump($allowedRoles);
                    foreach ($allowedRoles as $roleName) {
                        $role = HALOUtilHelper::findInCollection($roles, $roleName, 'name');
                        if ($role && HALOAuth::hasRole($role->name, $target)) {
                            $accept = true;
                        }
                    }
                    if (!$accept) {
                        $labelGroup->labels->forget($skey);
                    }
                } else {
                    if ($manualOnly) {
                        //this label is not available, remove it
                        $labelGroup->labels->forget($skey);

                    } else {

                    }
                }
            }
            //remove label group if there is no labels available
            if (count($labelGroup->labels) == 0) {
                $labelGroups->forget($gkey);
            }
        }
        return $labelGroups;
    }

    /**
     * assign labels to a target
     * 
     * @param  object  $target
     * @param  array  $labelIds
     * @param  bool $manualOnly
     * @return bool
     */
    public static function assignLabels($target, $labelIds, $manualOnly = true)
    {
        $labelGroups = HALOLabelAPI::getLabelGroups($target, $manualOnly);

        //get allowed label ids
        $allowedLabelIds = array();
        foreach ($labelGroups as $labelGroup) {
            $allowedLabelIds = array_merge($allowedLabelIds, $labelGroup->labels->lists('id'));
        }
        //filter labelId with the allowed label ids
        $syncArray = array();
        if (!empty($allowedLabelIds)) {
            foreach ($labelIds as $labelId) {
                if (in_array($labelId, $allowedLabelIds)) {
                    $syncArray[$labelId] = array('params' => '', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now());
                }
            }
        }
        $target->labels()->sync($syncArray, true);
        return true;
    }

    /**
     * return assigned labels of an object
     * 
     * @param  object $obj
     * @return mixed
     */
    public static function getLabels($obj)
    {
        //check for labels exists
        if (method_exists($obj, 'labels')) {
            return $obj->labels;
        } else {
            return array();
        }
    }

    /**
     * return assigned labels of an object, limit by group
     * 
     * @param  object $obj 
     * @param  int $groupCode
     * @return array
     */
    public static function getLabelByGroup($obj, $groupCode)
    {
        static $cachedGroupLabels = array();
        $labelGroup = null;
        if (!isset($cachedGroupLabels[$groupCode])) {
            $cachedGroupLabels[$groupCode] = HALOLabelGroupModel::with('labels')->where('group_code', $groupCode)->first();
        }

        $selectedLabels = array();
        $labelGroup = $cachedGroupLabels[$groupCode];
        if ($labelGroup) {
            $mode = $labelGroup->group_type;
            if (count($labelGroup->labels)) {
                $labels = HALOLabelAPI::getLabels($obj);
                $groupLabelIds = $labelGroup->labels->lists('id');
                foreach ($labels as $label) {
                    if (in_array($label->id, $groupLabelIds)) {
                        if ($mode == HALOLabelGroupModel::GROUP_TYPE_SINGLE) {
                            $selectedLabels = (empty($selectedLabels) || $selectedLabels->created_at < $label->created_at) ? $label : $selectedLabels;
                        } else if ($mode == HALOLabelGroupModel::GROUP_TYPE_MULTIPLE) {
                            $selectedLabels[] = $label;
                        }
                    }
                }
            }
        }
        //trigger event
        Event::fire('label.onGetLabelByGroup', array($obj, $groupCode, &$selectedLabels));
        return $selectedLabels;

    }

    /**
     * function to update timer labels
     */
    public static function updateTimerLabels()
    {
        $now = Carbon::now();
        //load timer labels
        $timerLabels = HALOLabelModel::where('label_type', HALOLabelModel::LABEL_TYPE_TIMER)->get();
        $removeLabelableIds = array();
        foreach ($timerLabels as $label) {
            $lifeTime = $label->getParams('lifetime', 0);
            if ($lifeTime) {
                $validTime = $now->copy()->subHours(intval($lifeTime));

                $labelableIds = $label->labelables()->where('created_at', '<', $validTime)->lists('id');
                if (!empty($labelableIds)) {
                    $removeLabelableIds = array_merge($removeLabelableIds, $labelableIds);
                }
            }
        }
        if (!empty($removeLabelableIds)) {
            //remove labelables
            HALOLabelableModel::destroy($removeLabelableIds);
        }
    }
	
	/*
		function to get label filter options
	*/
    public static function byLabelFilterOptions($target)
    {
		$options = array();

		$labelGroups = HALOLabelAPI::getLabelGroups($target, false, false);
		foreach($labelGroups as $labelGroup) {
			foreach($labelGroup->labels as $label) {
				$options[] = HALOObject::getInstance(array('name' => __halotext($label->name), 'value' => Str::slug($label->name)));
			}
		}
		return $options;
    }
	

}
