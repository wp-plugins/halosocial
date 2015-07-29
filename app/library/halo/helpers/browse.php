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

class HALOBrowseHelper 
{
	const UNIT_THOUSAND = 1000;
	const UNIT_MILION = 1000000;
	const UNIT_BILION = 1000000000;
	/**
	 * get Default Location
	 * 
	 * @return HALOLocationModel
	 */
	public static function getDefaultLocation() 
	{
		//1. get location from session
		$value = Session::get('defaultLocation', null);
		if (is_null($value)) {
			$value = 1;///default location
		}
		$defaultLoc = HALOLocationModel::find($value);
		if (!$defaultLoc) {
			$defaultLoc = new HALOLocationModel();
		}

		return $defaultLoc;
	}
	/**
	 * format Money
	 * 
	 * @param  string $value
	 * @param  string $unit
	 * @return string
	 */
	public static function formatMoney($value, $unit) 
	{
		$unitStr = strtoupper($unit);
		$str = $value;
		if ($unitStr == 'VND' || $unit == 'VNĐ') {
			$val = ceil((float) $value);
			if ($val <= HALOBrowseHelper::UNIT_THOUSAND) {
				$str = self::number_format($val);
			} elseif ($val < HALOBrowseHelper::UNIT_MILION) {
				//k
				$k = self::number_format(round($val / HALOBrowseHelper::UNIT_THOUSAND, 3));
				$str = $k . 'K';
			} elseif ($val < HALOBrowseHelper::UNIT_BILION) {
				//milion
				$m = self::number_format(round($val / HALOBrowseHelper::UNIT_MILION, 3));
				$str = $m . ' Tr';
			} else {
				//bilion
				$b = self::number_format(round($val / HALOBrowseHelper::UNIT_BILION, 3));
				$str = $b . ' Tỷ';
			}
		}
		return $str;
	}
	/**
	 * format number
	 * 
	 * @param  string $num
	 * @return string
	 */
	public static function number_format($num) 
	{
		$digits = 0;
		$part = explode('.', '' . $num);
		if (count($part) == 2) {
			$digits = strlen($part[1]);
		}
		return number_format($num, $digits, ',', '.');
	}
	/**
	 * get Post OwnerLink
	 * 
	 * @param  object  $post
	 * @param  bool $showOriginal
	 * @return string
	 */
	public static function getPostOwnerLink($post, $showOriginal = false) 
	{
		$owner = $post->owner;
		if ($post->linkable_type == 'HALOShopModel' && $post->linkable) {
			$owner = $post->linkable;
		}
		if ($owner) {
			if ($showOriginal && $post->linkable_type == 'HALOShopModel' && (HALOAuth::can('post.edit', $post) || HALOAuth::can('post.approve', $post))) {
				$original = $post->owner ? ' [' . $post->owner->getDisplayLink() . ']' : '';
				return $owner->getDisplayLink() . $original;
			} else {
				return $owner->getDisplayLink();
			}
		} else {
			return '';
		}
	}
	/**
	 * get Post TypeId
	 * 
	 * @param  object $post
	 * @return int
	 */
	public static function getPostTypeId($post) 
	{
		$owner = $post->owner;
		if ($post->linkable_type == 'HALOShopModel') {
			$type = 1;//shop type
		} else {
			$type = 0;//user type
		}
		return $type;
	}
	/**
	 * get Post Main CategoryId 
	 * 
	 * @param  object $post
	 * @return int
	 */
	public static function getPostMainCatId($post) 
	{
		return HALOPostCategoryModel::getTopParent($post->category_id);
	}
	/**
	 * cloak Email
	 * 
	 * @param  string $email
	 * @return string 
	 */
	public static function cloakEmail($email) 
	{
		$parts = explode('@', $email);
		if (count($parts) == 2) {
			$parts[0] = substr($parts[0], 0, 1) . '***' . substr($parts[0], -1, 1);
			$email = implode('@', $parts);
		}
		return '<a href="mailto:' . $email . '">' . $email . '</a>';
	}
	/**
	 * get User Address
	 * 
	 * @param  mixed $user
	 * @return string
	 */
	public static function getUserAddress($user) 
	{
		return 'abc';
	}
	/**
	 * get Seller Contact
	 * 
	 * @param  object $target
	 * @return string
	 */
	public static function getSellerContact($target) 
	{
		$contactInfo = $target->getParams('contactInfo');
		$contactInfo = $contactInfo ? $contactInfo : __halotext('N/A');
		return $contactInfo;
	}

	/**
	 * get Text Search Condition
	 * 
	 * @param  string $col 
	 * @param  string $val 
	 * @return string --function to return raw query condition statement for text searching
	 */
	public static function getTextSearchCondition($col, $val) 
	{
		if ($val !== '') {
			$val = DB::getPdo()->quote("%" . $val . "%");
			return "(lower(" . $col . ") like " . strtolower($val) . " or lower(" . $col . ") like " . strtolower($val) . ")";
		} else {
			return '1=1';
		}
	}

	/**
	 * display filter handler for display post searching
	 * 
	 * @param  HALOParams $params
	 * @param  mixed  $uiType
	 * @return string
	 */
	public static function getBrowseSearchDisplayFilter(HALOParams $params, $uiType) 
	{
		$params->set('uiType', 'browse.search_filter');
		return '';
	}

	/**
	 * apply filter handler to apply post searching
	 * 
	 * @param  Illuminate\Database\Query\Builder $query
	 * @param  array $value 
	 * @param  mixed $params 
	 * @return bool
	 */
	public static function getBrowseSearchApplyFilter(&$query, $value, $params) 
	{
		try {
			if (!empty($value) && isset($value['text']) && $value['text'] != '') {
				if (HALOConfig::get('se.enable', false)) {
					$se = HALOSearch::getInstance();

					$se->query->should(array('match' => array('title' => $value['text'])));
					$se->query->should(array('match' => array('description' => $value['text'])));

				} else {
					$query = $query->whereRaw('(' . self::getTextSearchCondition('title', $value['text'])

						. ' or ' . self::getTextSearchCondition('description', $value['text']) . ')');
				}
			}
			if (!empty($value) && isset($value['cat'])) {
				if ($value['cat']) {
					$cat = HALOPostCategoryModel::find($value['cat']);
					if ($cat) {
						$catIds = $cat->descendantsAndSelf()->where('published', 1)->lists('id');
						if (HALOConfig::get('se.enable', false)) {
							$se = HALOSearch::getInstance();

							$se->filter->must(array('terms' => array('category_id' => $catIds)));

						} else {
							if (!empty($catIds)) {
								$query = $query->whereIn('category_id', $catIds);
							}
						}
					}
				}
			}
			return true;
		} catch (\Exception $e) {
			return false;
		}

		return true;
	}

	/**
	 * display filter handler for post searching by location
	 * 
	 * @param  HALOParams $params
	 * @param  mixed   $uiType
	 * @return array
	 */
	public static function getBrowseLocationDisplayFilter(HALOParams $params, $uiType) 
	{
		$params->set('uiType', 'browse.location_filter');
		$options = array();
		$options['location'] = array(HALOObject::getInstance(array('name' => __halotext('Nearby'), 'value' => 'nearby')), //location value
		);
		$options['distance'] = array(HALOObject::getInstance(array('name' => __halotext('Distance'), 'value' => '5')), //diamater value in km
		);
		return $options;
	}

	/**
	 * apply filter handler to apply  post searching by location
	 * 
	 * @param  Illuminate\Database\Query\Builder $query
	 * @param  array $value
	 * @param  mixed $params 
	 * @return bool
	 */
	public static function getBrowseLocationApplyFilter(&$query, $value, $params) 
	{
		//store the location to db
		if (isset($value['name']) && isset($value['lat']) && isset($value['lng'])) {
			$loc = HALOLocationModel::findLocation($value['name'], $value['lat'], $value['lng']);
			$locId = $loc ? $loc->id : null;
			if (!is_null($locId)) {
				Session::set('defaultLocation', $locId);
			}
		}

		//prefer topLoc and btmLoc
		if (!empty($value) && (!empty($value['toplat']) || !empty($value['toplng']) || !empty($value['btmlat']) || !empty($value['btmlng']))) {
			try {
				$query = $query->whereHas('location', function ($q) use ($value) {
					$toplat = isset($value['toplat']) ? round((float) $value['toplat'], 6) : 0;
					$toplng = isset($value['toplng']) ? round((float) $value['toplng'], 6) : 0;
					$btmlat = isset($value['btmlat']) ? round((float) $value['btmlat'], 6) : 0;
					$btmlng = isset($value['btmlng']) ? round((float) $value['btmlng'], 6) : 0;
					if (HALOConfig::get('se.enable', false)) {
						$se = HALOSearch::getInstance();
						$topLeft = array($toplat, min($toplng, $btmlng));
						$btmRight = array($btmlat, max($toplng, $btmlng));
						$se->filter->must(array('geo_bounding_box' => array('location' => array('top_left' => implode(', ', $topLeft), 'bottom_right' => implode(', ', $btmRight)))));

					} else {
						$latRange = array($toplat, $btmlat);
						$lngRange = array($toplng, $btmlng);

						asort($latRange, SORT_NUMERIC);
						asort($lngRange, SORT_NUMERIC);

						$q->whereBetween('halo_locations.lat', $latRange)
						->whereBetween('halo_locations.lng', $lngRange);

					}
				});

				return true;

			} catch (\Exception $e) {
				return false;
			}

		} elseif (!empty($value) && !empty($value['lat']) && !empty($value['lng'])) {
			try {
				if (HALOConfig::get('se.enable', false)) {
					$se = HALOSearch::getInstance();
					$distance = isset($value['distance']) ? $value['distance'] : 5;//default distance is 1 km
					$center = array($value['lat'], $value['lng']);
					$se->filter->must(array('geo_distance' => array('location' => implode(', ', $center), 'distance' => $distance . 'km')));

				} else {
					$query = $query->whereHas('location', function ($q) use ($value) {
						$distance = isset($value['distance']) ? $value['distance'] : 5;//default distance is 1 km
						$q->whereRaw(HALOLocationModel::getDistanceCondition($value['lng'], $value['lat'], $distance));
					});
				}
				return true;

			} catch (\Exception $e) {
				return false;
			}
		}

		return true;
	}


	/**
	 * display filter handler for post searching by location
	 * 
	 * @param  HALOParams $params
	 * @param  mixed   $uiType 
	 * @return string
	 */
	public static function getBrowsePriceDisplayFilter(HALOParams $params, $uiType) 
	{
		$params->set('uiType', 'browse.price_filter');
		return '';
	}


	/**
	 * apply filter handler to apply  post searching by location
	 * 
	 * @param  Illuminate\Database\Query\Builder $query 
	 * @param  array $value 
	 * @param  mixed $params
	 * @return bool
	 */
	public static function getBrowsePriceApplyFilter(&$query, $value, $params) 
	{
		//$value is an array of ('location', 'diameter')
		if (!empty($value) && isset($value['range']) && isset($value['unit'])) {
			$range = explode(',', $value['range']);
			if (count($range) == 2) {
				//sanity $range
				foreach ($range as $index => $r) {
					$range[$index] = (float) $r * (float) $value['unit'];
				}
				try {
					if (HALOConfig::get('se.enable', false)) {
						$se = HALOSearch::getInstance();
						$priceFieldId = Cache::rememberForever('price_field', function () {
							return HALOFieldModel::where('fieldcode', 'FIELD_PRICE')->lists('id');
						});
						$priceFieldId = empty($priceFieldId) ? -1 : array_shift($priceFieldId);

						$se->filter->must(array('term' => array('field_id' => $priceFieldId)));

						$se->filter->must(array('range' => array('value' => array('gte' => min($range), 'lte' => max($range)))));

					} else {
						$query = self::addPostPriceCondition($query);
						$query = $query->whereRaw("CAST(REGEXP_REPLACE('0' || COALESCE(halo_post_fields_values.value,'0'), '[^0-9].*', '', 'g') AS float) between " . implode(' and ', $range))
						               ->select('halo_posts.*')
						;
					}
					return true;
				} catch (\Exception $e) {
					//var_dump($e->getMessage());exit;
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * display filter handler for post searching by location
	 * 
	 * @param  HALOParams $params
	 * @param  mixed   $uiType
	 * @return array
	 */
	public static function getBrowseSortDisplayFilter(HALOParams $params, $uiType) 
	{
		$params->set('uiType', 'browse.sort_filter');
		$options = array();
		$options['location'] = array(HALOObject::getInstance(array('name' => __halotext('Nearby'), 'value' => 'nearby')), //location value
		);
		$options['distance'] = array(HALOObject::getInstance(array('name' => __halotext('Distance'), 'value' => '5')), //diamater value in km
		);
		return $options;
	}

	/**
	 * apply filter handler to apply  post searching by location
	 * 
	 * @param  Illuminate\Database\Query\Builder $query
	 * @param  array $value
	 * @param  mixed $params
	 * @return bool
	 */
	public static function getBrowseSortApplyFilter(&$query, $value, $params) 
	{
		$rtn = false;
		//default sort
		if (!isset($value['sort'])) {
			$value['sort'] = 'default';
			$value['dir'] = 'desc';
		}
		if (!empty($value) && isset($value['sort'])) {
			try {
				$dir = (isset($value['dir']) && in_array($value['dir'], array('desc', 'asc'))) ? $value['dir'] : 'desc';
				if (HALOConfig::get('se.enable', false)) {
					$se = HALOSearch::getInstance();
					switch ($value['sort']) {
						case 'date':
							$se->sort(array('created_at' => array('order' => $dir)));
							break;
						case 'price':
							$priceFieldId = Cache::rememberForever('price_field', function () {
								return HALOFieldModel::where('fieldcode', 'FIELD_PRICE')->lists('id');
							});
							$priceFieldId = empty($priceFieldId) ? -1 : array_shift($priceFieldId);
							$se->filter->must(array('nested' => array('path' => 'numeric_fields'
								, 'filter' => array('bool' => array('must' => array(array('term' => array('field_id' => $priceFieldId))))))));

							$se->sort(array('numeric_fields.value' => array('mode' => 'max', 'order' => $dir, 'nested_filter' => array('term' => array('field_id' => $priceFieldId)))));
							break;
						case 'rating':
							$se->sort(array('rating' => array('order' => $dir)));
							break;

						default:

							//default sorting
							$se->sort(array('created_at' => array('order' => 'desc')));
							/*
						$se->sort(array('promote_at'=>array('order'=>'desc')
						,'created_at'=>array('order'=>'desc')
						));
						 */
							break;

					}

				} else {
					switch ($value['sort']) {
						case 'date':
							$query = $query->orderBy('created_at', $dir);
							break;
						case 'price':
							$query = self::addPostPriceCondition($query);

							$query = $query->orderBy('__price', $dir)
							               ->selectRaw("halo_posts.*, CAST(REGEXP_REPLACE('0' || COALESCE(halo_post_fields_values.value,'0'), '[^0-9].*', '', 'g') AS float) as __price")
							;

							break;
						case 'rating':
							$query = $query->orderBy('rating', $dir);
							break;

						default:

							//default sorting
							//$query = $query->orderBy('promote_point','desc')
							$query = $query->orderBy('promote_at', 'desc')
							               ->orderBy('created_at', 'desc');
							break;

					}
				}
				$rtn = true;

			} catch (\Exception $e) {
				return false;
			}
		}
		//post owner
		if (!empty($value) && isset($value['owner']) && !empty($value['owner'])) {
			try {
				$owner = intval($value['owner']);
				if (HALOConfig::get('se.enable', false)) {
					$se = HALOSearch::getInstance();
					$se->filter->must(array('term' => array('creator_id' => $owner)));

				} else {
					$query = $query->where('halo_posts.creator_id', $owner)
					;
				}
				$rtn = true;

			} catch (\Exception $e) {
				return false;
			}
		}

		if ($rtn) {
			return $rtn;
		}
	}
	/**
	 * join Post Values Table
	 * 
	 * @param  Illuminate\Database\Query\Builder $query
	 * @return Illuminate\Database\Query\Builder
	 */
	public static function joinPostValuesTable($query) 
	{
		static $ran = false;
		if (!$ran) {
			$query = $query->leftJoin('halo_post_fields_values', 'halo_post_fields_values.post_id', '=', 'halo_posts.id');
		}
		$ran = true;
		return $query;
	}
	/**
	 * add Post Price Condition
	 * 
	 * @param Illuminate\Database\Query\Builder $query 
	 * @return  Illuminate\Database\Query\Builder
	 */
	public static function addPostPriceCondition($query) 
	{
		static $ran = false;
		if (!$ran) {
			$priceFieldId = Cache::rememberForever('price_field', function () {
				return HALOFieldModel::where('fieldcode', 'FIELD_PRICE')->lists('id');
			});
			$priceFieldId = !empty($priceFieldId) ? $priceFieldId : array(-1);//dummy
			$query = self::joinPostValuesTable($query);
			$query->whereIn('halo_post_fields_values.field_id', $priceFieldId);
		}
		$ran = true;
		return $query;
	}

	/**
	 * fetch opengraph content
	 * 
	 * @param  string  $url
	 * @param  bool $cacheImage
	 * @return object
	 */
	public static function fetchUrl($url, $cacheImage = true) 
	{
		$key = md5(trim($url));
		Cache::forget('fetchUrl' . $key);
		$info = Cache::remember('fetchUrl' . $key, 24 * 60, function () use ($url, $cacheImage) {

			$rtn = HALOUtilHelper::preCatchFBShare($url);
			if(isset($rtn->title) && isset($rtn->description) && isset($rtn->image) && isset($rtn->url)) {
				$info = new stdClass();
				$info->title = isset($rtn->title)?$rtn->title:'';
				$info->description = isset($rtn->description)?$rtn->description:'';
				$info->image = isset($rtn->image)?$rtn->image:'';
				$info->url = isset($rtn->url)?$rtn->url:'';
			} else {
				$options = array(
					'facebookProvider' => true,
					'minImageWidth' => 400,
					'minImageHeight' => 400,
					'getBiggerImage' => true,
				);

				$info = Embed\Embed::create($url, $options);
			}

			if (!$info) {
				return $info;
			}
			//404 url
			//internal image Ids
			$info->photos = array();
			$my = HALOUserModel::getUser();
			if ($my && $cacheImage) {
				//prefer image from OpenGraph
				$images = array();
				if (empty($images) && isset($info->providers)) {
					$images = $info->providers['OpenGraph']->get('image');
				}
				//then image from fb
				if (empty($images) && isset($info->providers)) {
					$images = $info->providers['Facebook']->get('image');
				}

				//last call just get the main image
				if (empty($images) && isset($info->image)) {
					$images = (array)$info->image;
				}

				//add-hoc checking
				//for video url
				if(empty($images) && $video = HALOVideoProvider::detectProvider($url)){
					$video->getVideoMeta();
					$thumbnail = $video->getThumbnail();
					if($thumbnail) {
						$images = (array)$thumbnail;
					}
				}
				
				foreach ($images as $imageData) {
					//verify photo
					if (is_array($imageData)) {
						if (isset($imageData['url'])) {
							$image = $imageData['url'];
						} else {
							//unknow image record
							continue;
						}
					} else if (is_object($imageData)) {
						if (isset($imageData->url)) {
							$image = $imageData->url;
						} else {
							//unknow image record
							continue;
						}
					} else {
						$image = $imageData;
					}

					$media = new HALOPhotoModel();
					if (!$media->verifyFileType($image)) {
						continue;
					}
					//verify photo size
					if (!$media->verifyFileSize($image)) {
						continue;
					}
					
					$photo = HALOPhotoModel::where('storage', 'remote')
						->where('path', $image)
						->first();

					if (!$photo) {
						//verify min size
						try {
							$file = Image::make($image);
							if(($file->width() <= HALO_PHOTO_THUMB_SIZE * 2) || ($file->height() <= HALO_PHOTO_THUMB_SIZE * 2)) {
								continue;
							}
						} catch (\Exception $e) {
								continue;
						}
						
						//trigger on before adding file
						if (Event::fire('photo.onBeforeAdding', array($image), true) === false) {
							continue;
						}

						$photo = new HALOPhotoModel();
						$photo->caption = pathinfo($image, PATHINFO_FILENAME);
						$photo->owner_id = $my->id;
						$photo->path = $image;
						$photo->storage = 'remote';
						$photo->album_id = $my->getDefaultAlbumId();
						//setup cache path for remote file
						if(isset($file) && $file){
							try {
								//create external folder if not exists
								if(!file_exists( HALO_MEDIA_BASE_DIR . '/external')) {
									File::makeDirectory(HALO_MEDIA_BASE_DIR . '/external');
								}
								//try to cache file to local host
								$path = 'external/' . md5(pathinfo($photo->path, PATHINFO_FILENAME) . time()) . '.' . pathinfo($photo->path, PATHINFO_EXTENSION);
								$file->save(HALO_MEDIA_BASE_DIR . '/' . $path);
								$photo->setParams('cache_path', $path);
							} catch(\Exception $e) {
							
							}
						}
						$photo->save();
					}
					$info->photos[] = $photo ? $photo : '';
				}
			}
			return $info;
		});
		return $info;
	}
}
