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

class HALOPhotoHelper 
{
	static $memoryLimit = -1;

	/**
	 * get the path of the photo with size postfix
	 *
	 * @param  string $path path of photo --An array of user ids to be loaded.
	 * @param  string $size sixe of photo
	 * @return string
	 */
	public static function getPathWithSizePostfix($path, $size) 
	{
		if ($size == '') {
			return URL::asset($path);
		}
		$file = preg_replace('#\.[^.]*$#', '', $path);
		$dot = strrpos($path, '.') + 1;
		$ext = substr($path, $dot);

		return URL::asset($file . '_' . $size . '.' . $ext);
	}

	/**
	 * change a photo dir to converted photo dir
	 *
	 * @param string $oFile
	 * @param string $prefix
	 * @return   string	The file name
	 */
	public static function getConvertedDir($oFile, $prefix = "halo_converted") 
	{
		static $searchDirs = null;
		if(is_null($searchDirs)) {
			$searchDirs = array();
			$searchDirs['upload'] = rtrim(File::cleanPath(HALO_MEDIA_BASE_DIR), DIRECTORY_SEPARATOR);
			$searchDirs['halo'] = rtrim(File::cleanPath(HALO_APP_PATH), DIRECTORY_SEPARATOR);
			$searchDirs['base'] = rtrim(File::cleanPath(base_path()), DIRECTORY_SEPARATOR);		
		}
		$nFile = $oFile;
		if (!empty($prefix)) {
			$mediaDir = rtrim(File::cleanPath(HALO_MEDIA_BASE_DIR), DIRECTORY_SEPARATOR);
			$convertedDir = $mediaDir . DIRECTORY_SEPARATOR . $prefix;
			//check if oFile in converted photo dir format
			if(strpos($oFile, $convertedDir) === 0) {
				return $oFile;	//skip converting
			}
			foreach($searchDirs as $key => $searchDir) {
				$nDir =  $convertedDir . DIRECTORY_SEPARATOR . $key;
				$oDir = $searchDir;
				$pos = strpos($oFile, $oDir);
				if($pos === 0) {
					$nFile = $nDir . substr($oFile, strlen($oDir));
					return $nFile;
				}
			}			
		}
		return $nFile;
	}

	/**
	 * Add signature postfix and prefix to a filename
	 *
	 * @param string $oFile
	 * @param string	$sig	signature string
	 * @return	string	The file name
	 */
	public static function addFileSignature($oFile, $sig) 
	{
		if ($sig == '') {
			return $oFile;
		}
		$nFile = preg_replace('#\.[^.]*$#', '', $oFile);
		$dot = strrpos($oFile, '.') + 1;
		$ext = substr($oFile, $dot);
		$nFile = $nFile . '_' . $sig . '.' . $ext;
		//add photo prefix
		$nFile = self::getConvertedDir($nFile);

		return $nFile;
	}

	/**
	 * Resize current image based on given width/height
	 *
	 * Width and height are optional, the not given parameter is calculated
	 * based on the given. The ratio boolean decides whether the resizing
	 * should keep the image ratio. You can also pass along a boolean to
	 * prevent the image from being upsized.
	 *
	 * @param int $width  The target width for the image
	 * @param int $height The target height for the image
	 * @param bool $ratio  Determines if the image ratio should be preserved
	 * @param bool $upsize Determines whether the image can be upsized
	 *
	 * @return string URL of the resized photo
	 */
	public static function getResizePhotoURL($oUrl, $width = null, $height = null, $ratio = false, $upsize = true) 
	{
		//thumbnail resize
		//generate photo signature postfix from input params
		$sig = $width . 'x' . $height;
		//convert url to local dir for resizing if needed
		$oDir = File::fromUrl($oUrl);
		if (is_null($width) || is_null($height)) {
			//keep the ratio
			$ratio = true;
		}
		if (empty($oDir)) {
			//the url is not local url
			return $oUrl;
		}
		$nDir = self::addFileSignature($oDir, $sig);
		//check if original file exists
		if (!file_exists($oDir)) {
			//file not found
			return '';
		}
		//only resize if the file doesn't exists
		try {
			if (!file_exists($nDir)) {
				//create folder if not exists
				$folder = dirname($nDir);
				if (!is_dir($folder)) {
					File::createDir($folder);
				}
				self::setMemoryLimit();
				$image = Image::make($oDir);
				if ($width == 'center') {
					//resize and crop the phot in square size
					$size = $height;
					$currWidth = $image->width();
					$currHeight = $image->height();
					if ($currWidth < $currHeight) {
						//use currWidth as bases size
						$newWidth = $height;
						$newHeight = floor($currHeight * $height / $currWidth);
						$top = floor(($newHeight - $height) / 2);
						$left = 0;
					} else {
						//use currHeight as based size
						$newHeight = $height;
						$newWidth = floor($currWidth * $height / $currHeight);
						$top = 0;
						$left = floor(($newWidth - $height) / 2);
					}
					//resize the image
					$image->resize($newWidth, $newHeight, function ($constraint) {
						$constraint->aspectRatio();
					});
					//crop the image
					$image->crop((int) $height, (int) $height, (int) $left, (int) $top);//crop the image
				} else {
					$image->resize($width, $height, function ($constraint) use ($ratio, $upsize) {
						if ($ratio) {
							$constraint->aspectRatio();
						}

						if ($upsize) {
							$constraint->upsize();
						}
					}
					);
				}
				$image->save($nDir);
				self::restorMemoryLimit();
			}
		} catch (\Exception $e) {
			return '';
		}
		return File::toUrl($nDir);
	}
	/**
	 * fit Crop Size
	 * 
	 * @param  object $image
	 * @param  int $width
	 * @param  int $height
	 * @param  int $left
	 * @param  int $top
	 */
	protected static function fitCropSize(&$image, $width, $height, $left, $top) 
	{
		$currWidth = $image->width();
		$currHeight = $image->height();

		//first zoom the image to match the minimum size
		$rpW = ($width / ($currWidth - $left));
		$rpH = ($height / ($currHeight - $top));

		$rW = ($width / $currWidth);//without pos
		$rH = ($height / $currHeight);//without pos
		$zoom = 1;
		if ($rpW > 1 && $rpH > 1) {
			if ($rW > 1 || $rH > 1) {
				//need to resize image
				$zoom = ($rW < $rH) ? $rH : $rW;
				//centering the crop point
				$top = floor((($zoom * $currHeight) - $height) / 2);
				$left = floor((($zoom * $currWidth) - $width) / 2);
			} else {
				//re-position the crop point
				$zoom = 1;
				$top = $currHeight - $height;

				$left = $currWidth - $width;
			}
		} else if ($rpW > 1) {
			if ($rW > 1) {
				//need to resize image
				$zoom = $rW;
				$top = floor((($zoom * $currHeight) - $height) / 2);
				$left = 0;
			} else {
				$zoom = 1;
				$left = $currWidth - $width;
			}
			$zoom = $rW;
		} else if ($rpH > 1) {
			if ($rH > 1) {
				//need to resize image
				$zoom = $rH;
				$top = 0;
				$left = floor((($zoom * $currWidth) - $width) / 2);
			} else {
				$zoom = 1;
				$top = $currHeight - $height;
			}
		}
		if ($zoom != 1) {
			$image = $image->resize(floor($currWidth * $zoom), floor($currHeight * $zoom));
		}
		$image->crop($width, $height, $left, $top);//crop the image
	}

	/**
	 * crop current image based on given zoom, top,left, ratio to an image size regard to size
	 * 
	 * @param  string $oUrl
	 * @param  int $width
	 * @param  int $height
	 * @param  int $zoom
	 * @param  int $top
	 * @param  int $left
	 * @param  int $vpWidth
	 * @return string URL of the cropped photo
	 */
	public static function getCropPhotoURL($oUrl, $width, $height, $zoom, $top, $left, $vpWidth) 
	{
		//generate photo signature postfix from input params
		$sig = $width . 'x' . $height . '_' . $zoom . '_' . abs($top) . '_' . abs($left) . '_' . $vpWidth;
		//convert url to local dir for resizing if needed
		$oDir = File::fromUrl($oUrl);
		if (empty($oDir)) {
			//the url is not local url
			return $oUrl;
		}
		$nDir = self::addFileSignature($oDir, $sig);
		//check if original file exists
		if (!file_exists($oDir)) {
			//file not found
			return null;
		}
		//only resize if the file doesn't exists
		if (!file_exists($nDir)) {
			//create folder if not exists
			$folder = dirname($nDir);
			if (!is_dir($folder)) {
				File::createDir($folder);
			}
			try {
				self::setMemoryLimit();
				$image = Image::make($oDir);
				/*calculate the nature zoom ratio: 	vpWidth => 100% viewport zoom
				$zoom is the zoom value from the view port so the nature zoom is $zoom * viewport zoom
				 */
				//viewport zoom ratio = nature width / vpWidth
				$vpWidth = $vpWidth ? $vpWidth : $image->width();//viewport width must not zero

				$vpZoom = $vpWidth / $image->width();
				$natureZoom = $zoom / 100 * $vpZoom;
				$scaleWidth = floor($vpWidth / $natureZoom);

				$scaleTop = abs(floor($top / $natureZoom));
				$scaleLeft = abs(floor($left / $natureZoom));
				$scaleHeight = floor($scaleWidth * $height / $width);

				//verify crop image dimension
				//$image->crop((int)$scaleWidth,(int)$scaleHeight,(int)$scaleLeft,(int)$scaleTop);		//crop the image
				self::fitCropSize($image, (int) $scaleWidth, (int) $scaleHeight, (int) $scaleLeft, (int) $scaleTop);

				$image->resize($width, $height, function ($constraint) {
					$constraint->aspectRatio();
					$constraint->upsize();
				});

				$image->save($nDir);
				self::restorMemoryLimit();
			} catch (\Exception $e) {
				var_dump($e);
				return null;
			}
		}
		return File::toUrl($nDir);
	}

	/*
	function to set php memory limit
	 */
	public static function setMemoryLimit($limit = -1) 
	{
		self::$memoryLimit = ini_get('memory_limit');
		ini_set('memory_limit', $limit);

	}

	/**
	 * function to restore the previous memory limit set
	 */
	public static function restorMemoryLimit() 
	{
		ini_set('memory_limit', self::$memoryLimit);
	}

	/**
	 * return url for default image of a specific type
	 * 
	 * @param  string $type
	 * @param  object $target 
	 * @return string
	 */
	public static function getDefaultImagePath($type = 'avatar', $target) 
	{
		//@todo: apply  template override for default avatar
		$context = $target->getContext();
		switch ($type) {
			case 'avatar':
				$image = "assets/images/" . $context . "_avatar_default.png";
				break;
			case 'thumb':
				$image = "assets/images/" . $context . "_avatar_default.png";
				break;
			case 'cover':
				$image = "assets/images/" . $context . "_cover_default.png";
				break;
			case 'album':
				$image = "assets/images/album_cover_default.png";
				break;
			default:
				$image = "assets/images/" . $context . "_all_default.png";
		}
		//trigger event
		Event::fire('system.onGetDefaultImage', array($target, &$image));
		//use view finder to find the first match image
		$imageUrl = HALOAssetHelper::to($image);

		return $imageUrl;
	}

	/**
	 * return site banner
	 * 
	 * @return string
	 */
	public static function getSiteBanner() 
	{
		$image = "assets/images/banner_default_new.png";
		return HALOAssetHelper::to($image);
	}

	/*
		combine up to 4 photos to a single photo. 
	*/
	public static function combinePhotos($photos, $size, $ratio = 1) {
		//determind photo layout
		//@rule: photo to be combined must have min width = 1/2 target photo width and min height = 1/2 target photo height
		$combinedPhotos = array();
		$combinedPhotoWidth = $size;
		$combinedPhotoHeight = round($size * $ratio);
		$layout = 0;
		foreach($photos as $photo) {
			$photoPath = $photo->getFullPath();
			if(file_exists($photoPath)) {
				$image = Image::make($photoPath);
				if(($image->width() >= ($combinedPhotoWidth / 2)) && $image->height() >= ($combinedPhotoHeight /2)){
					$combinedPhotos[] = $image;
				} else {
					$image->destroy();	//free memory
				}
			}
			if(count($combinedPhotos) >= 4) {
				break;
			}
		}
		$layout = (count($combinedPhotos) > 4)? 4: count($combinedPhotos);
		//insert images
		if($layout){
			$rtnImage = Image::canvas($combinedPhotoWidth, $combinedPhotoHeight);
			switch($layout){
				case 4:
					$topleft = $combinedPhotos[0];
					HALOPhotoHelper::centerFit($topleft, round($combinedPhotoWidth/2), round($combinedPhotoHeight/2));
					$rtnImage->insert($topleft);
					
					$topright = $combinedPhotos[1];
					HALOPhotoHelper::centerFit($topright, round($combinedPhotoWidth/2), round($combinedPhotoHeight/2));
					$rtnImage->insert($topright, 'top-right');

					$bottomleft = $combinedPhotos[2];
					HALOPhotoHelper::centerFit($bottomleft, round($combinedPhotoWidth/2), round($combinedPhotoHeight/2));
					$rtnImage->insert($bottomleft, 'bottom-left');

					$bottomright = $combinedPhotos[3];
					HALOPhotoHelper::centerFit($bottomright, round($combinedPhotoWidth/2), round($combinedPhotoHeight/2));
					$rtnImage->insert($bottomright, 'bottom-right');

					break;
				case 3:
					$topleft = $combinedPhotos[0];
					HALOPhotoHelper::centerFit($topleft, round($combinedPhotoWidth/2), round($combinedPhotoHeight/2));
					$rtnImage->insert($topleft);
					
					$topright = $combinedPhotos[1];
					HALOPhotoHelper::centerFit($topright, round($combinedPhotoWidth/2), round($combinedPhotoHeight/2));
					$rtnImage->insert($topright, 'top-right');

					$bottomleft = $combinedPhotos[2];
					HALOPhotoHelper::centerFit($bottomleft, $combinedPhotoWidth, round($combinedPhotoHeight/2));
					$rtnImage->insert($bottomleft, 'bottom-left');

					break;
				case 2:
					$topleft = $combinedPhotos[0];
					HALOPhotoHelper::centerFit($topleft, $combinedPhotoWidth, round($combinedPhotoHeight/2));
					$rtnImage->insert($topleft);
					
					$bottomleft = $combinedPhotos[1];
					HALOPhotoHelper::centerFit($bottomleft, $combinedPhotoWidth, round($combinedPhotoHeight/2));
					$rtnImage->insert($bottomleft, 'bottom-left');
				
					break;
					
				case 1:
					$topleft = $combinedPhotos[0];
					HALOPhotoHelper::centerFit($topleft, $combinedPhotoWidth, $combinedPhotoHeight, 0, 0);
					$rtnImage->insert($topleft);
					break;
			}
			
			return $rtnImage;
		} else {
			return null;	//could not create combine photo
		}
	}
	
	public static function centerFit(&$image, $width, $height) {
		$image->fit($width, $height);
		return $image;
	}
}
