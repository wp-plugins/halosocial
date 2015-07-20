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

class HALOFieldGroup extends HALOField
{
    /**
     * return editable html for this  field
     */
    public function getEditableUI()
    {
        $subFieldsStr = $this->model->getParams('subFields');
        $subFields = json_decode($subFieldsStr);

        if (!is_array($subFields)) {
            return '';
        }
        //header is the first element of array
        $subFieldHeader = array_shift($subFields);

        //convert the subField code to subField input name
        foreach ($subFields as $key => $fieldCode) {
            $subFields[$key] = 'field[' . HALOField::getFieldId($fieldCode[0]) . ']';
        }
        return HALOUIBuilder::getInstance('', 'field.group', array('id' => $this->id,
            'title' => $this->name,
            'helptext' => $this->tips,
            'field' => $this->model,
            'subFields' => $subFields,
        ))->fetch();

    }

    /**
     * Display field  as readable html
     *
     * @return Field html
     */
    public function getReadableUI($template = "form.readonly_field", array $attr = array())
    {
        return $this->getEditableUI();
    }

}
