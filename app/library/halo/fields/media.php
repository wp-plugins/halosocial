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

class HALOFieldMedia extends HALOField
{

    /**
     * return editable html for this  field
     * 
     */
    public function getEditableUI()
    {
        $val = $this->value;
        $val = Input::old('field.' . $this->id, $val);

        $mediaType = $this->model->getParams('mediaType', 'photo');

        $dataValueStr = '';
        if (!empty($val)) {
            $mediaIds = json_decode($val);
            $values = array();
            if (is_array($mediaIds) && !empty($mediaIds)) {
                switch ($mediaType) {
                    case 'file':
                        try {
                            $files = HALOFileModel::find($mediaIds);
                        } catch (\Exception $e) {
                            $files = array();
                        }
                        foreach ($files as $file) {
                            $values[] = HALOObject::getInstance(array("id" => $file->id,
                            										"image" => $file->getThumbnail(HALO_PHOTO_THUMB_SIZE, HALO_PHOTO_THUMB_SIZE),
                            										"name" => $file->filename));
                        }
                        break;

                    case 'photo':

                    default://photo as default media type
                        try {
                            $photos = HALOPhotoModel::find($mediaIds);
                        } catch (\Exception $e) {
                            $photos = array();
                        }
                        foreach ($photos as $photo) {
                            $values[] = HALOObject::getInstance(array("id" => $photo->id,
                            										"image" => $photo->getResizePhotoURL(HALO_PHOTO_THUMB_SIZE, HALO_PHOTO_THUMB_SIZE),
                            										"name" => $photo->caption));
                        }
                        break;
                }
            }
            if (!empty($values)) {
                $dataValueStr = json_encode($values);
            }
        }

        return HALOUIBuilder::getInstance('', 'field.media', array('id' => 'field_' . $this->id,
            													'name' => 'field[' . $this->id . ']',
													            'value' => $dataValueStr,
													            'title' => $this->name,
													            'helptext' => $this->tips,
													            'field' => $this->model,
													            'halofield' => $this,
            													'validation' => $this->getValidateValueString()
            													))->fetch();

    }

    /**
     * Display field value as readable html
     *
     * @return Field html
     */
    public function getValueUI($template = "form.readonly_field")
    {
        $value = '';
        $mediaIds = json_decode($this->value);
        $media = array();
        //get the media type
        $mediaType = $this->model->getParams('mediaType', 'photo');
        if (!empty($mediaIds)) {
            switch ($mediaType) {
                case 'file':
                    try {
                        $files = HALOFileModel::find($mediaIds);
                    } catch (\Exception $e) {
                        $files = array();
                    }
                    if ($files) {
                        $value = HALOUIBuilder::getInstance('', 'file_list', array('files' => $files))
                            ->fetch();
                    }
                    break;

                case 'photo':
                    try {
                        $photos = HALOPhotoModel::find($mediaIds);
                    } catch (\Exception $e) {
                        $photos = array();
                    }
                    if ($photos) {
                        $value = HALOUIBuilder::getInstance('', 'photo.gallery_thumb', array('photos' => $photos))
                                ->fetch();
                    }
                    break;
                default:
            }
        }
        return ($value === '') ? __halotext("N/A") : $value;
    }

    /**
     * function to preprocess field value/access/params before saving to database
     * 
     * @return array of (value,access,params)
     */
    public function toPivotArray($data)
    {
                //mark uploaded file as ready state to prevent from crontask cleanup
        $mediaType = $this->model->getParams('mediaType', 'photo');
        try {
            switch ($mediaType) {
                case 'file':
                    $files = HALOFileModel::find($data['value']);
                            //update status of photos so that it will not be removed on crontask
					if($files) {
						HALOFileModel::whereIn('id', $files->modelKeys())->update(array('status' => HALO_MEDIA_STAT_READY));
					}
                    break;
                case 'photo':
                    $photos = HALOPhotoModel::find($data['value']);
                            //update status of photos so that it will not be removed on crontask
					if($photos) {
						HALOPhotoModel::whereIn('id', $photos->modelKeys())->update(array('status' => HALO_MEDIA_STAT_READY));
					}
                    break;
                default:
                    break;
            }
        } catch (\Exception $e) {}
        return parent::toPivotArray($data);
    }
}
