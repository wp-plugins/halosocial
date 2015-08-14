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

class HALOFieldTable extends HALOField
{

    /**
     * return editable html for this  field
     * 
     * @return HALOUIBuilder
     */
    public function getEditableUI()
    {
        $columnsStr = $this->model->getParams('columns');
        $columns = json_decode($columnsStr);

        $val = $this->value;

        //only display this field if header data format is correct
        if (!is_array($columns)) {
            return '';
        }

        array_shift($columns);
        $columnsHeader = array();
        foreach ($columns as $key => $col) {
            $columnsHeader[] = $col[0];
        }
        if (empty($columnsHeader)) {
            return '';
        }
        //column header is not configured, do not display this field

        $fieldBuilder = HALOUIBuilder::getInstance('', 'field.jsontable', array('name' => 'field[' . $this->id . ']',
            'id' => $this->id,
            'title' => $this->name,
            'field' => $this->model,
            'helptext' => $this->tips,
            'halofield' => $this,
            'default' => json_encode(array($columnsHeader)),
            'value' => $val,
            'validation' => $this->getValidateValueString()));

        return HALOUIBuilder::getInstance('', 'field.edit_layout', array('fieldHtml' => $fieldBuilder->fetch(),
        ))                                                                                         ->fetch();

    }

    /**
     * Display field value as readable html
     *
     * @return Field html
     */
    public function getValueUI($template = "form.readonly_field")
    {
        $columnsStr = $this->model->getParams('columns');
        $columns = json_decode($columnsStr);

        $val = $this->value;
        if ($val === '') {
            return __halotext("N/A");
        }

        //only display this field if header data format is correct
        if (!is_array($columns)) {
            return '';
        }

        array_shift($columns);
        $columnsHeader = array();
        foreach ($columns as $key => $col) {
            $columnsHeader[] = $col[0];
        }
        if (empty($columnsHeader)) {
            return '';
        }
        //column header is not configured, do not display this field

        //clean the field value
        $data = json_decode($val);
        if (is_array($data)) {
            foreach ($data as $i => $row) {
                if (is_array($row)) {
                    foreach ($row as $j => $cell) {
                        $row[$i] = HALOOutputHelper::text2html($cell);
                    }
                    $data[$i] = $row;
                }
            }
            $val = json_encode($data);
        }
        $value = HALOUIBuilder::getInstance('', 'field.readable_jsontable', array('name' => 'field[' . $this->id . ']',
            'id' => $this->id,
            'readonly' => 'readonly',
            'title' => $this->name,
            'field' => $this->model,
            'default' => json_encode(array($columnsHeader)),
            'value' => $val,
        ))->fetch();
        return $value;

    }
}
