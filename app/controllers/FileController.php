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

class FileController extends BaseController
{

    /**
     * Inject the models.
     *
     * @param HALOFieldModel $field
     */
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * download file
     * @param  int $alphaId
     * @return View
     */
    public function download($alphaId)
    {
        $alphaString = (string) HALOUtilHelper::alphaID($alphaId, true);
        $fileId = 0;
        //trip out the alphaString prefix to get file id
        $prefixLen = 5;
        if (strlen($alphaString) > $prefixLen) {
            $fileId = (int) substr($alphaString, $prefixLen);
        }
        var_dump($fileId);
        if (!$fileId) {
            $title = __halotext('Error');
            $messages = HALOError::failed(__halotext('Invalid File ID'));
            return View::make('site/error', compact('title', 'messages'));
        }
        $file = HALOFileModel::find($fileId);
        if (!$file) {
            $title = __halotext('Error');
            $messages = HALOError::failed(__halotext('File not Found'));
            return View::make('site/error', compact('title', 'messages'));
        }

        $filePath = $file->getFullPath();
        if (!file_exists($filePath)) {
            $title = __halotext('Error');
            $messages = HALOError::failed(__halotext('File not Found'));
            return View::make('site/error', compact('title', 'messages'));
        }
        return HALOUtilHelper::download($filePath, $file->filename);

    }
}
