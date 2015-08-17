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

class HALOAlbumModel extends HALOModel
{
    protected $table = 'halo_photos_albums';
	
	protected $fillable = array('name', 'description', 'published');
	
	/**
	 * Get Validate rule 
	 * 
	 * @return array
	 */
	public function getValidateRule()
	{
		return array('name' => 'required');	
	
	}

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
	//define relationship here
	/**
	 * 
	 * @return Illuminate\Database\Eloquent\Relations\hasMany
	 */
	public function photos() 
	{
		return $this->hasMany('HALOPhotoModel', 'album_id')->where('status', '!=', HALO_MEDIA_STAT_TEMP);
	}

	/**
	 * HALOUserModel, HALOAlbumModel: one to many (owner)
	 * 
	 * @return Illuminate\Database\Eloquent\Relations\belongsTo
	 */
	public function owner(){
		return $this->belongsTo('HALOUserModel', 'owner_id');
	}
	
	
	//////////////////////////////////// Define Relationships /////////////////////////
	
	//define relationship here
		
	/**
	 * Return path to the album thumb image. If album thumb is not set, return default thumb
	 * 
	 * @param  array $size the thumb size (optional)
	 * @return string path to the album thumb image
	 */
	public function getCover($size = HALO_PHOTO_AVATAR_SIZE, $ratio = 0.75)
	{
		if($this->cover_id) {
			$path = $this->cover->getPhotoURL();
		} else {
			$photos = $this->photos()->orderBy('created_at', 'desc')->get();
			if(count($photos)){
				//create cover from its photos
				//get the first 4 photo id as a part of photo name
				$photoCount = count($photos);
				$photoCount = ($photoCount > 4)? 4:$photoCount;
				$photoIds = array();
				for($i = 0; $i < $photoCount; $i++){
					$photoIds[] = $photos[$i]->id;
				}
				$fileName = "/album_covers/{$this->id}_" . implode('_', $photoIds) . ".jpg";
				
				$path = HALO_MEDIA_BASE_DIR . $fileName;
				$dir = dirname($path);
				if(!File::exists($dir)) {
					File::createDir($dir);
				}
				$path = HALOPhotoHelper::addFileSignature($path, null);
				if(!file_exists($path)){
					$image = HALOPhotoHelper::combinePhotos($photos, $size, $ratio);
					if($image) {
						$image->save($path);
					}
				}
				$rtn = HALOPhotoHelper::getCropPhotoURL(HALO_MEDIA_BASE_URL . $fileName, $size, round($size * $ratio), 100, 0, 0, 0);
				return $rtn;
				
			} else {
				$path = HALOPhotoHelper::getDefaultImagePath('album', $this);
				return HALOPhotoHelper::getResizePhotoURL($path, $size, round($size * $ratio));
			}
		}
		return HALOPhotoHelper::getResizePhotoURL($path, $size, round($size * $ratio));
		
	}

    /**
     * Return display name with link for this model
     * @return string
     */
    public function getDisplayLink($class = '') 
    {	
		if(!empty($class)) {
			$class = 'class="'.$class.'" ';
		}
		return	'<a ' . $class .'href="' . $this->getUrl() . '">' . $this->getDisplayName() . '</a>';
	}	

    /**
     * Return display name without link for this model
     * @return  string
     */
    public function getDisplayName() 
    {	
		return	htmlentities($this->name) ;
	}	

	/**
	 * Return view url
	 * 
	 * @return string
	 */
	public function getUrl()
	{
		return URL::to('?view=photo&task=album&uid=' . $this->id);
	}
	
    /**
     * Check permission that user can edit this album
     * 
     * @param int $userId
     * @return bool
     */
    public function canEdit($userId = null)
    {
        if (HALOAuth::can('album.edit', $this, $userId))
            return true;
        if (HALOAuth::hasRole('owner', $this, $userId))
            return true;
        return false;
    }

    /**
     * Function to delete this album model and all its data
     * 
     * @return bool
     */
    
    public function delete()
    {   
        $albumId = $this->id;
        Event::fire('album.onBeforeDelete', array($this));
        $rtn = true;

        // Delete all of photos in album
        $this->photos()->delete();

        // Delete activities
        HALOActivityModel::whereRaw('POSITION(\'"album_id":' . $albumId . '\' IN wp_halo_activities.params) <> 0')
            ->where('actor_id', $this->owner_id)
            ->delete();

        parent::delete();

        if ($rtn) {
            Event::fire('album.onAfterDelete', array($albumId));
        }
        return $rtn;
    }
}