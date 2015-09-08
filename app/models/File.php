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

class HALOFileModel extends HALOModel
{
    protected $table = 'halo_files';

    protected $fillable = array('filename', 'published');

    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();

        // try to set owner_id as the id of current login user
        $this->owner_id = UserModel::getCurrentUserId();
    }

    //////////////////////////////////// Define Relationships /////////////////////////

    /**
     * HALOUserModel, HALOActivityModel: one to one (owner)
     * 
     * @return Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function owner()
    {
        return $this->belongsTo('HALOUserModel', 'owner_id');
    }

    /**
     * HALOActivityModel, HALOCommentModel: polymorphic (comments)
     * 
     * @return Illuminate\Database\Eloquent\Relations\morphMany
     */
    public function comments()
    {
        $builder = $this->morphMany('HALOCommentModel', 'commentable')->orderBy('created_at');
        return $builder;
    }

    /**
     * HALOActivityModel, HALOLikeModel: polymorphic
     * 
     * @return Illuminate\Database\Eloquent\Relations\morphMany
     */
    public function likes()
    {
        return $this->morphMany('HALOLikeModel', 'likeable');
    }

    //define polymorphic relationship
    /**
     * 
     * @return Illuminate\Database\Eloquent\Relations\morphTo
     */
    public function linkable()
    {
        return $this->morphTo();
    }

    /**
     * HALOFileModel, HALOReportModel: polymorphic
     * @return Illuminate\Database\Eloquent\Relations\morphMany
     */
    public function reports()
    {
        return $this->morphMany('HALOReportModel', 'reportable');
    }
    //////////////////////////////////// Define Relationships /////////////////////////

   	/**
     * Function to move a file to the storage destination that generated from album_id, owner_id
     * 
     * @param  string $oPath 
     * @return HALOFileModel        
     */
    public function copyFileFrom($oPath)
    {
        //get the file name from oPath and generate hash filename base on it
        if (!File::isFile($oPath)) {
            return false;
        }

        $oBasename = basename($oPath);
        $oExt = File::extension($oPath);
        $oFilename = $oBasename;

        $nSubDir = $this->getStorageDir();
        $nDir = File::cleanPath(HALO_MEDIA_BASE_DIR . '/' . $nSubDir);
        $nBasename = md5($oBasename . time());
        $nFilename = $nBasename . '.' . $oExt;

        if (!File::createDir($nDir)) {
            return false;
        }
        //can not create directory
        $nRelPath = $nSubDir . '/' . $nFilename;
        $nPath = $nDir . '/' . $nFilename;

        if (File::copy($oPath, File::cleanPath($nPath))) {

            $this->path = $nRelPath;
            $this->filename = $oFilename;
            $this->status = HALO_MEDIA_STAT_TEMP;
        }
        return $this;
    }

    /**
     * Return generated path from album_id and owner_id
     * 
     * @return string
     */
    public function getStorageDir()
    {
        if (is_null($user = HALOUserModel::getUser($this->owner_id))) {
            $folder = 'null';//owner does not exists, return as null string
        } else {
            $folder = $user->user_id;
        }

        return $folder . '/files/' . date('Y-m-d');
    }

    /**
     * Return full file path
     * 
     * @return string
     */
    public function getFullPath()
    {
        $path = File::cleanPath(HALO_MEDIA_BASE_DIR . '/' . $this->path);
        return $path;
    }

    /**
     * Return file content type
     * 
     * @return HALOUtilHelper
     */
    public function getContentType()
    {
        return HALOUtilHelper::mime_content_type($this->getFullPath());
    }

    /**
     *
     * @return URL of the thumbnail photo
     */
    public function getThumbnail($width = null, $height = null)
    {
        $ext = File::extension($this->path);
        $thumbImage = "assets/images/file_thumb_" . $ext . ".png";
        $thumbUrl = HALOAssetHelper::to($thumbImage);
        if (is_null($thumbUrl)) {
            $thumbUrl = HALOAssetHelper::to("assets/images/file_thumb_default.png", true);
        }
        return HALOPhotoHelper::getResizePhotoURL($thumbUrl, $width, $height, true, false);
    }

    /**
     * Verify file ext is allowed
     * 
     * @param  string $file 
     * @return bool       
     */
    public function verifyFileType($file)
    {
        $allowedFileTypes = HALOConfig::get('file.allowedExtensions', 'pdf,txt,zip,rar');
        $arr = explode(',', $allowedFileTypes);
        $ext = strtolower(File::extension($file));
        if (is_array($arr) && in_array($ext, $arr)) {
            return true;
        }
        return false;
    }

    /**
     * Return downloadable url for this file
     * 
     * @return string
     */
    public function getFileURL()
    {
        $alphaString = $this->created_at->format('His') . $this->id;
        return URL::to('?view=file&task=download&uid=' . HALOUtilHelper::alphaID($alphaString));
    }

    /**
     * Funtion to delete the physical file
     * 
     * @return bool
     */
    public function deletePhysicalFile()
    {
        $path = File::cleanPath(HALO_MEDIA_BASE_DIR . '/' . $this->path);
        if (file_exists($path) && is_file($path)) {
            try {
                File::delete($path);
            } catch (\Exception $e) {
                return false;
            }
        }
        return true;

    }

    /**
     * Return total files counter
     * 
     * @param  string $status 
     * @return int         
     */
    public static function getTotalFilesCounter($status = null)
    {
        if (is_null($status)) {
            return self::count();
        }

        return self::where('status', $status)->count();
    }

}
