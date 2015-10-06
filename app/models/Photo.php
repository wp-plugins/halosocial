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

class HALOPhotoModel extends HALOModel
{
    protected $table = 'halo_photos';

    protected $fillable = array('album_id', 'caption', 'published');

    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();

        // try to set owner_id as the id of current login user
        $this->owner_id = HALOUserModel::getCurrentUserId();
    }

    //////////////////////////////////// Define Relationships /////////////////////////

    /**
     * HALOUserModel, HALOPhotoModel: belong to (owner)
     * 
     * @return Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function owner()
    {
        return $this->belongsTo('HALOUserModel', 'owner_id');
    }

    /**
     * HALOAlbumModel, HALOPhotoModel: belong to (owner)
     * 
     * @return Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function album()
    {
        return $this->belongsTo('HALOAlbumModel', 'album_id');
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

    /**
     * HALOActivityModel, HALOTagModel: many to many polymorph
     * 
     * @return Illuminate\Database\Eloquent\Relations\morphMany
     */
    public function tags()
    {
        return $this->morphToMany('HALOTagModel', 'tagging', 'halo_tagging', 'tagging_id', 'tag_id');
    }

    /**
     * HALOActivityModel, HALOTagModel: many to many polymorph
     * 
     * @return Illuminate\Database\Eloquent\Relations\morphMany
     */
    public function tagusers()
    {
        return $this->morphToMany('HALOTagModel', 'tagging', 'halo_tagging', 'tagging_id', 'tag_id')
                    ->where('halo_tags.taggable_type', 'HALOUserModel')
                    ->withPivot('params');
    }

    //define polymorphic relationship
    public function linkable()
    {
        return $this->morphTo();
    }

    /**
     * HALOPhotoModel, HALOReportModel: polymorphic
     * 
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
     * @return HALOPhotoModel        
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
            $my = HALOUserModel::getUser();
            $this->path = $nRelPath;
            $this->album_id = $my->getDefaultAlbumId();
            $this->caption = $oFilename;
            $this->status = HALO_MEDIA_STAT_TEMP;
        }
        return $this;
    }

    /**
     * Return genetratad path from album_id and owner_id
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

        return $folder . '/photos/' . date('Y-m-d');
    }

    /**
     * Return full phôt path based on input signature
     * 
     * @param  string $sig 
     * @return HALOPhotoHelper      
     */
    public function getFullPath($sig = null)
    {
        $path = File::cleanPath(HALO_MEDIA_BASE_DIR . '/' . $this->path);
        return HALOPhotoHelper::addFileSignature($path, $sig);
    }

    /**
     * Return full phôt url based on input signature
     * 
     * @param  string $sig 
     * @return HALOPhotoHelper    
     */
    public function getPhotoURL($sig = null)
    {
        if ($this->storage == 'file') {
            $path = HALO_MEDIA_BASE_URL . '/' . $this->path;
            return HALOPhotoHelper::addFileSignature($path, $sig);
        }
        if ($this->storage == 'remote') {
            if (($path = $this->getParams('cache_path', false)) === false || !file_exists(HALO_MEDIA_BASE_DIR . '/' . $path)) {
				//create external folder if not exists
				if(!file_exists( HALO_MEDIA_BASE_DIR . '/external')) {
					File::makeDirectory(HALO_MEDIA_BASE_DIR . '/external');
				}
                //try to cache file to local host
                $path = 'external/' . md5(pathinfo($this->path, PATHINFO_FILENAME) . time()) . '.' . pathinfo($this->path, PATHINFO_EXTENSION);

                $imageBuffer = HALOUtilHelper::getImageContent($this->path);
                if ($imageBuffer !== false) {
                    if (@file_put_contents(HALO_MEDIA_BASE_DIR . '/' . $path, $imageBuffer)) {
                        $this->setParams('cache_path', $path);
                        $this->save();
                    }
                } else {
					return '';
				}
            }
            $path = HALO_MEDIA_BASE_URL . '/' . $path;
            return HALOPhotoHelper::addFileSignature($path, $sig);
        }
        return '';
    }

    /**
     * Return display name with link for this model
     * 
     * @param  string $class 
     * @return string        
     */
    public function getDisplayLink($class = '')
    {
        if (!empty($class)) {
            $class = 'class="' . $class . '" ';
        }
        return '<a ' . $class . 'href="' . $this->getUrl() . '">' . $this->getDisplayName() . '</a>';
    }

    /**
     * Return display name without link for this model
     * 
     * @return string
     */
    public function getDisplayName()
    {
        return __halotext('photo');
    }

    /**
     * Return view url
     * 
     * @return string
     */
    public function getUrl()
    {
        return URL::to('?view=photo&task=show&uid=' . $this->id);
    }

    /**
     * Resize current image based on given width/height
     *
     * Width and height are optional, the not given parameter is calculated
     * based on the given. The ratio boolean decides whether the resizing
     * should keep the image ratio. You can also pass along a boolean to
     * prevent the image from being upsized.
     *
     * @param integer $width  The target width for the image
     * @param integer $height The target height for the image
     * @param boolean $ratio  Determines if the image ratio should be preserved
     * @param boolean $upsize Determines whether the image can be upsized
     *
     * @return URL of the resized photo
     */
    public function getResizePhotoURL($width = null, $height = null, $ratio = false, $upsize = true)
    {
        return HALOPhotoHelper::getResizePhotoURL($this->getPhotoURL(), $width, $height, $ratio, $upsize);
    }

    /**
     * Center crop current image based on given width/height
     *
     * Width and height are optional, the not given parameter is calculated
     * based on the given. The ratio boolean decides whether the resizing
     * should keep the image ratio. You can also pass along a boolean to
     * prevent the image from being upsized.
     *
     * @param integer $width  The target width for the image
     * @param integer $height The target height for the image
     * @param boolean $ratio  Determines if the image ratio should be preserved
     * @param boolean $upsize Determines whether the image can be upsized
     *
     * @return URL of the resized photo
     */
    public function getCropPhotoURL($width = null, $height = null)
    {
        return HALOPhotoHelper::getCropPhotoURL($this->getPhotoURL(), $width, $height, 1, 0, 0, $width);
    }

    /**
     * Correct the orietation
     * 
     * @return bool
     */
    public function orientate()
    {
        try {
            HALOPhotoHelper::setMemoryLimit();
            $path = File::cleanPath(HALO_MEDIA_BASE_DIR . '/' . $this->path);
            $image = Image::make($path);
            $image->orientate();
            $image->save($path);
            HALOPhotoHelper::restorMemoryLimit();
        } catch (\Exception $e) {
            var_dump($e);
            return null;
        }
    }

    /**
     * Verify file exit is allowed
     * 
     * @param  string $file 
     * @return bool
     */
    public function verifyFileType($file)
    {
        $allowedFileTypes = HALOConfig::get('photo.allowedExtensions', 'png, jpg, jpeg');
        $arr = explode(',', $allowedFileTypes);
        $ext = strtolower(File::extension($file));
        if (is_array($arr) && in_array($ext, $arr)) {
            return true;
        }
        return false;
    }

    /**
     * Verify file size
     * 
     * @param  string $file 
     * @return bool       
     */
    public function verifyFileSize($file)
    {
        try {
            $image = Image::make($file);
            return ($image->width() <= HALOConfig::get('photo.maxWidth', 2500)) && ($image->height() <= HALOConfig::get('photo.maxHeight', 1920));
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * get original photo width
     * 
     * @return float
     */
    public function getWidth()
    {
				$path = '';
				if($this->storage == 'file'){
					$path = File::cleanPath(HALO_MEDIA_BASE_DIR . '/' . $this->path);
				} else if($this->storage == 'remote'){
					$path = File::cleanPath(HALO_MEDIA_BASE_DIR . '/' . $this->getParams('cache_path'));
				}
        try {
            $image = Image::make($path);
            return $image->width();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * get original photo height
     * 
     * @return float
     */
    public function getHeight()
    {
				if($this->storage == 'file'){
					$path = File::cleanPath(HALO_MEDIA_BASE_DIR . '/' . $this->path);
				} else if($this->storage == 'remote'){
					$path = File::cleanPath(HALO_MEDIA_BASE_DIR . '/' . $this->getParams('cache_path'));
				}
        try {
            $image = Image::make($path);
            return $image->height();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Function to delete the physical file
     * 
     * @return bool
     */
    public function deletePhysicalFile()
    {
        $path = File::cleanPath(HALO_MEDIA_BASE_DIR . '/' . $this->path);
        if (file_exists($path) && is_file($path)) {
            try {
                File::delete($path);
                //remove all copy versions
                $copyPath = HALOPhotoHelper::addFileSignature($path, '*');

                array_map('unlink', glob($copyPath));
            } catch (\Exception $e) {
                return false;
            }
        }
        return true;

    }

    /**
     * Return the onclick action for notification on activity
     * 
     * @return bool
     */
    public function getNotificationTargetAction()
    {
        HALOResponse::redirect($this->getUrl());
        return true;
    }

    /**
     * Return total photos counter
     * 
     * @param  string $status 
     * @return int         
     */
    public static function getTotalPhotosCounter($status = null)
    {
        if (is_null($status)) {
            return self::count();
        }

        return self::where('status', $status)->count();
    }

    /**
     * Mark Delete 
     * 
     * @return int
     */
    public function markDelete()
    {
        $this->status = 0;
        $this->save();
    }

    /**
     * Can Delete 
     * 
     * @return HALOUserModel
     */
    public function canDelete()
    {
        $my = HALOUserModel::getUser();
        return $my && (HALOAuth::can('backend.view') || $my->id == $this->owner_id);
    }
	
    /**
     * Check if photo file is existing
     * 
     * @return boolean
     */
	public function isExists(){
		return file_exists($this->getFullPath());
	}
}
