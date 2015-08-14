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

class HALOStatus
{
    public static $_status = array();

    /**
     * share status UI
     * 
     * @param  string $context
     * @param  int $target_id
     * @return object
     */
    public static function getStatusUI($context, $target_id)
    {
        return HALOUIBuilder::getInstance('', 'sharebox.status', array('title' => __halotext('Status'),
																		'action' => 'status.create',
																		'icon' => 'comment-o',
																		'placeholder' => '',
																	)
				)
					->addUI('attachment', HALOUIBuilder::getInstance('', '', array()));
    }

    /**
     * return share photo UI
     * 
     * @param  string $context
     * @param  int $target_id
     * @return object
     */
    public static function getPhotoUI($context, $target_id)
    {
        $my = HALOUserModel::getUser();
        $albumOptions = array();
        foreach ($my->albums as $album) {
            $albumOptions[] = array('value' => $album->id, 'title' => $album->name);
        }
		
        $attachment = HALOUIBuilder::getInstance('', 'grid.wrapper', array('class' => 'halo-share-grid'));
		$selectAlbum = HALOUIBuilder::getInstance('', 'grid.row', array());
		if(count($albumOptions)){
			$selectAlbum->addUI('exist_album', HALOUIBuilder::getInstance('', 'form.select', array('name' => 'album_id', 'size' => '6',
								'title' => __halotext('Select album'),
								'value' => '',
								'options' => $albumOptions,
							)))
						->addUI('new_album', HALOUIBuilder::getInstance('', 'form.text', array('name' => 'album_name', 'size' => '6',
									'title' => __halotext('Or New'),
									'placeholder' => __halotext('Album name'), 'value' => ''
							)))
						;
		} else {
			$selectAlbum->addUI('exist_album', HALOUIBuilder::getInstance('', 'form.hidden', array('name' => 'album_id', 'value' => ''
							)))
						->addUI('new_album', HALOUIBuilder::getInstance('', 'form.hidden', array('name' => 'album_name', 'value' => ''
							)))
						;		
		}
		$attachment->addUI('album_select', $selectAlbum);
        $attachment->addUI('uploader', HALOUIBuilder::getInstance('', 'photo.uploader', array('id' => 'photo', 'validate' => 'required|feedback:photo')))
        ;
        return HALOUIBuilder::getInstance('', 'sharebox.photo', array('title' => __halotext('Photo'),
            'action' => 'photo.create',
            'icon' => 'camera-retro',
            'placeholder' => '',
        )
        )
            ->addUI('attachment', $attachment)
        ;
    }


    /**
     * return share video UI
     * 
     * @param  string $context
     * @param  int $target_id
     * @return object
     */
    public static function getVideoUI($context, $target_id)
    {
        $attachment = HALOUIBuilder::getInstance('', 'video_upload', array());
        return HALOUIBuilder::getInstance('', 'sharebox.video', array('title' => __halotext('Video'),
            'action' => 'video.create',
            'icon' => 'film',
            'placeholder' => '',
        )
        )
            ->addUI('attachment', $attachment)
        ;

    }

    /**
     * function to render status share box
     * 
     * @param  string $context
     * @param  int $target_id
     * @param  array  $options 
     * @return string
     */
    public static function render($context, $target_id, $options = array())
    {
        //check permission
        if (!HALOAuth::can('activity.create')) {
            return '';
        }

        //default options
        $default = array();
        $options = array_merge(self::getDefaultOptions($context, $target_id), $options);

        //trigger event to load available content sharing
        $contentUIs = new stdClass();
        Event::fire('system.onLoadShareBoxUI', array(&$contentUIs, $context, $target_id));
        $builder = HALOUIBuilder::getInstance('', 'sharebox.sharebox', array('options' => $options, 'context' => $context, 'target_id' => $target_id, 'name' => 'status_box', 'placeholder' => __halotext('Write something ...')));
        $UIs = get_object_vars($contentUIs);

        foreach ($UIs as $uiName => $uiBuilder) {
            $builder->addUI($uiName, $uiBuilder);
        }
        //$builder = self::init($context,$target_id);

        return (empty($UIs)) ? '' : $builder->fetch();
    }

    /**
     * return default sharebox options
     * 
     * @param  string $context
     * @param  int $target_id
     * @return array
     */
    public static function getDefaultOptions($context, $target_id)
    {
        $defaultOptions = array('defaultPrivacy' => HALOConfig::get('global.activityDefaultPrivacy'));
        Event::fire('system.getDefaultShareOption', array($context, $target_id, &$defaultOptions));
        return $defaultOptions;
    }
}
