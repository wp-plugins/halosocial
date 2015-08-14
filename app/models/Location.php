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

class HALOLocationModel extends HALOModel
{
    protected $table = 'halo_locations';

    protected $hidden = array('created_at', 'updated_at');

    private $validator = null;

    private $_params = null;

    /**
     * Get Validate Rule 
     * 
     * @return array
     */
    public function getValidateRule()
    {
        return array('name' => 'required');

    }

    protected static $currentLocation = null;

    //////////////////////////////////// Define Relationships /////////////////////////

    //////////////////////////////////// Define Relationships /////////////////////////

    /**
     * Function to init and set current location
     * 
     * @param string $locName
     * @param float $locLat  
     * @param float $locLng  
     * @return HALOLocationModel
     */
    public static function setCurrentLocation($locName, $locLat, $locLng)
    {
        $loc = self::findLocation($locName, $locLat, $locLng);
        self::$currentLocation = $loc ? $loc->id : null;
        return self::$currentLocation;
    }

    /**
     * Function to find a location by using $locName, $locLat, $locLng
     * 
     * @param  string $locName 
     * @param  float $locLat  
     * @param  float $locLng  
     * @return HALOLocationModel       
     */
    public static function findLocation($locName, $locLat, $locLng)
    {
        if (empty($locName)) {
            //location input is not available
            return null;
        } else {
            //format the lat,lng
            $locLat = round((float) $locLat, 6);
            $locLng = round((float) $locLng, 6);

            //find the loc model that match the location data
            $loc = HALOLocationModel::where('name', '=', $locName)
                ->where('lng', '=', $locLng)
                ->where('lat', '=', $locLat)
                ->get()
            ;
            if (!count($loc)) {
                //insert new location to database
                $loc = new HALOLocationModel();
                $loc->name = $locName;
                $loc->lat = $locLat;
                $loc->lng = $locLng;
                //$loc->location = DB::raw("ST_GeographyFromText('POINT(" . $locLng . " " . $locLat . ")')");
                $loc->save();
            } else {
                $loc = $loc->first();
            }
            return $loc;
        }

    }

    /**
     * Function gto get current location
     * 
     * @return HALOLocationModel
     */
    public static function getCurrentLocation()
    {
        return self::$currentLocation;
    }

    /**
     * Set Current Location Model 
     * 
     * @param  HALOLocationModel $loc 
     * @return HALOLocationModel
     */
    public static function setCurrentLocationModel($loc)
    {
        self::$currentLocation = $loc ? $loc->id : null;
        return self::$currentLocation;
    }

    /**
     * Attach location model to a target model with the provided data. If the location does not exists, add it
     * 
     * @param  HALOModel $targetModel 
     * @param  mixed $defaultLoc  
     * @return HALOLocationModel              
     */
    public static function assignCurrentLocation(&$targetModel, $defaultLoc = null)
    {
        $currentLocation = self::getCurrentLocation();
        $targetModel->location_id = is_null($currentLocation) ? $defaultLoc : $currentLocation;
        return $targetModel;
    }

    /**
     * Return latitude of this location
     * 
     * @return float;
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Return longtitude of this location
     * 
     * @return float
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * Return name of this location
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * A list of locations that ready for autocomplete rendering
     * 
     * @param  string $term    
     * @param  array $filters 
     * @return array          
     */
    public static function getSearch($term, $filters)
    {
        $model = new HALOLocationModel();

        //@todo: apply filters to get the list of result user_ids
        $locations = $model->whereRaw(HALOUtilHelper::getTextSearchCondition('name', $term))->get();

        $list = array();
        //prepare user data
        foreach ($locations as $location) {
            $obj = new stdClass();
            $obj->name = $location->name;
            $obj->value = $location->name;
            $obj->lat = $location->lat;
            $obj->lng = $location->lng;
            $list[] = $obj;
        }
        return $list;
    }

    /*
    r
     */
    /**
     * Return location value in array format
     * 
     * @param  HALOLocaltionModel $location 
     * @return array           
     */
    public static function getLocationValues($location)
    {
        if ($location) {
            return array('name' => $location->getName(), 'lat' => $location->getLat(), 'lng' => $location->getLng());
        } else {
            //return empty location data
            return array('name' => '', 'lat' => '', 'lng' => '');
        }
    }

    /**
     * Return location link of this location
     * 
     * @param  string $class 
     * @param  array  $data  
     * @return HALOUIBuilder        
     */
    public function getDisplayLink($class = '', $data = array())
    {
        return HALOUIBuilder::getInstance('', 'location_link', array('location' => $this, 'data' => $data))->fetch();
    }

    /**
     * Return google map url for this location
     * 
     * @param  string $class 
     * @param  array  $data  
     * @return HALOUIBuilder        
     */
    public function getMapImageUrl($params = array())
    {
		$lat = $this->getLat();
		$lng = $this->getLng();
		$params['center'] = $lat .','.$lng;
		$parasm['markers'] = $lat .','.$lng;
		if(!isset($params['size'])) {
			$params['size'] = '200x200';
		}
		if(!isset($params['zoom'])) {
			$params['zoom'] = '11';
		}
		
		$pString = http_build_query($params);
		return 'https://maps.googleapis.com/maps/api/staticmap?' . $pString;
    }

    /**
     * Return location name of this location
     * 
     * @return string
     */
    public function getDisplayName()
    {
        return $this->getDistrict();
        return $this->getName();
    }

    /**
     * Get condition query for location
     * 
     * @param  float $lng      
     * @param  float $lat      
     * @param  int $distance 
     * @return string           
     */
    public static function getDistanceCondition($lng, $lat, $distance)
    {
        //format input
        $lat = round((float) $lat, 6);
        $lng = round((float) $lng, 6);
        $distance = round((float) $distance, 2);
        $distance = $distance ? $distance : 1;//default distance is 1 km
		$tablePrefix = DB::getTablePrefix();
        return "(DEGREES(ACOS(SIN(RADIANS(" . $tablePrefix ."halo_locations.lat)) 
				* SIN(RADIANS(" . $lat . "))
				+ COS(RADIANS(" . $tablePrefix ."halo_locations.lat)) 
				* COS(RADIANS(" . $lat . ")) 
				* COS(RADIANS(" . $tablePrefix ."halo_locations.lng - " . $lng . "))))
				* 60 * 1.1515 * 1.609344) < " . $distance;
    }

    /**
     * getRawDistance Calculate distance between two postions
     *
     * @param  int $lat
     * @param  int $lng
     * @return string a raw query
     * @author  Phuong Ngo <phuongngo@halo.vn>
     */
    public static function getRawDistance($lat, $lng)
    {
        return "(DEGREES(ACOS(SIN(RADIANS(halo_locations.lat)) * SIN(RADIANS(" . $lat . ")) + COS(RADIANS(halo_locations.lat)) * COS(RADIANS(" . $lat . ")) * COS(RADIANS(halo_locations.lng - " . $lng . ")))) * 60 * 1.1515 * 1.609344)";
    }

    /**
     * Return addrress for this location
     * 
     * @return string
     */
    public function getAddress()
    {
        return $this->getName();
    }

    /**
     * Return district form input address string
     * 
     * @param  boolean $reload 
     * @return string          
     */
    public function getDistrict($reload = true)
    {
        if (!$reload) {
            //check if this model has district_id
            if (!is_null($this->district_name)) {
                return $this->district_name;
            }
        }

        return Cache::rememberForever('location.district.' . $this->id, function () {
            if (Schema::hasTable('vnm_adm3')) {
                //postgis enabled
                $address = $this->getAddress();
                $condition = "ST_Contains(polygon, ST_SetSRID(ST_MakePoint('" . $this->lng . "','" . $this->lat . "'),4326))";
                $district_name = DB::table('vnm_adm3')->whereRaw($condition)->pluck('name_3');
                if ($district_name) {
                    $this->district_id = 9999;
                    $this->district_name = $district_name;
                    $this->save();
                    return $this->district_name;

                }
                return $address;
            } else {
                $address = $this->getAddress();
                $val = DB::getPdo()->quote($address);
                $condition = "lower(" . $val . ") like concat('%',lower(name),'%')";
                $district = HALODistrictModel::whereRaw($condition)->get()->first();
                if ($district) {
                    //update this location.
                    $this->district_id = $district->id;
                    $this->district_name = $district->name;
                    $this->save();
                    return $district->name;
                }
                return $address;

            }
        });
    }

    /**
     * Return city from input address string
     * 
     * @return string
     */
    public function getCity($reload = true)
    {
        if (!$reload) {
            //check if this model has district_id
            if (!is_null($this->city_name)) {
                return $this->city_name;
            }
        }

        return Cache::rememberForever('location.city.' . $this->id, function () {
            if (Schema::hasTable('vnm_adm3')) {
                //postgis enabled
                $address = $this->getAddress();
                $condition = "ST_Contains(polygon, ST_SetSRID(ST_MakePoint('" . $this->lng . "','" . $this->lat . "'),4326))";
                $city_name = DB::table('vnm_adm3')->whereRaw($condition)->pluck('name_2');
                if ($city_name) {
                    $this->city_id = 9999;
                    $this->city_name = $city_name;
                    $this->save();
                    return $this->city_name;

                }
                return $address;
            } else {
				$address = $this->getAddress();
				$val = DB::getPdo()->quote($address);
				$condition = "lower(" . $val . ") like concat('%',name,'%')";
				$city = HALOCityModel::whereRaw($condition)->get()->first();
				if ($city) {
                    $this->city_id = $city->id;
                    $this->city_name = $city->name;
                    $this->save();
					return $city->name;
				}

				return $address;
            }
        });
    }

    /**
     * Return city slug url
     * 
     * @return string
     */
    public function getCitySlug()
    {
		if(!($slug = $this->getParams('city_slug',''))){
			$slug = Str::slug($this->getCity());
			$this->setParams('city_slug',$slug);
			$this->save();
		}
		return $slug;
    }
}
