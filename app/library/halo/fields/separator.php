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

class HALOFieldSeparator extends HALOField
{

    /**
     * return editable html for this  field
     * 
     * @return HALOUIBuilder
     */
    public function getEditableUI()
    {
        return HALOUIBuilder::getInstance('', 'field.separator', array('id' => $this->id,
        													  'title' => $this->name))->fetch();

    }

    /**
     * Display field  as readable html
     *
     * @return Field html
     */
    public function getReadableUI($template = "form.readonly_field", array $attr = array())
    {
        $defaultAttr = array('title' => $this->name, 'type' => $this->type);
        $attr = array_merge($defaultAttr, $attr);
        return HALOUIBuilder::getInstance('', $template, $attr)->fetch();
    }

    /**
     * Return validation rule for value of this field
     *
     * @return array valiation value rule
     */
    public function getValidateValueRule()
    {
        return array();
    }

}
