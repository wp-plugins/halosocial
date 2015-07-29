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

class HALOQueue implements ArrayAccess, Serializable, Iterator, Countable {

	private $defaultPriority = 100;	//default priority queue

	private $data = array();
	
	private $flatData = array();
	
	private $position = 0;
	
    public function offsetSet($offset, $value) {
		$offset = is_null($offset)?$this->defaultPriority:$offset;
		if(!isset($this->data[$offset])) {
			$this->data[$offset] = array();
		}
		$this->data[$offset][] = $value;
		$this->syncFlatData();
    }

    public function offsetGet($offset) {
		if(isset($this->data[$offset])) {
			array_values($this->data[$offset])[0];
		} else {
			return null;
		}
    }
	
    public function offsetExists($offset) {
		return isset($this->data[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->data[$offset]);
    }
	
	public function syncFlatData() {
		$this->flatData = call_user_func_array('array_merge', $this->data);
	}
	
	public function rewind() {
        $this->position = 0;
    }

    public function current() {
        return $this->flatData[$this->position];
    }

    public function key() {
        return $this->position;
    }

    public function next() {
        ++$this->position;
    }

    public function valid() {
        return isset($this->flatData[$this->position]);
    }
	
	public function serialize() {
        return serialize($this->data);
    }
	
    public function unserialize($data) {
        $this->data = unserialize($data);
		$this->syncFlatData();
    }
	
	public function count() {
		return count($this->flatData);
	}
	
	public function extract($priority) {
		if(isset($this->data[$priority]) && isset($this->data[$priority][0])) {
			$val = array_slice($this->data[$priority], 0, 1);
			$this->syncFlatData();
			return $val;
		}
		return null;
	}
}
