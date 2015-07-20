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

class HALOPluginModel extends HALOModel
{
	public static $steps = array("copySource", "initDatabase", "runScript");

    protected $table = 'halo_plugins';

    protected $fillable = array('name', 'description', 'status', 'folder', 'element');

    protected $toggleable = array('status');

    private $validator = null;

    private $_params = null;

    private $_meta = null;
    private $_pkg_dir = '';

    private $_stack = array();
    private $src = null;
    private $subscriber = '';
    private $destFolder = '';
    private $isNew = true;
    private $_db_migrated = false;

    /**
     * Get Validate Rule 
     * 
     * @return array
     */
    public function getValidateRule()
    {
        return array_merge(array(
            'name' => 'required',
            'folder' => 'required',
            'element' => 'required',
        ), $this->toHALOField()->getValidateRule());

    }

    /**
     * Get validate value rule
     * 
     * 
     */
    public function getValidateValueRule()
    {

    }
	
	public function isNew(){
		return $this->isNew;
	}

    /**
     * Get intance
     * 
     * @param  string $id      
     * @param  string $pkg_dir 
     * @return object         
     */
    public static function getInstance($id, $pkg_dir = '')
    {
        if (!empty($id)) {
            //get instance from id
            $obj = HALOPluginModel::find($id);
            if (!$obj) {
                return null;
            }

            $pkg_dir = HALO_APP_PATH . '/plugins/' . $obj->folder . '/' . $obj->element;
            $plg_meta_file = $pkg_dir . '/plugin.json';
            if (!file_exists($plg_meta_file)) {
                return null;
            }

            $plg_meta_contents = File::get($plg_meta_file);
            $plg_meta = json_decode($plg_meta_contents);
        } else {
            //get instance from installation dir
            //read the meta setting file
            $plg_meta_file = $pkg_dir . '/plugin.json';
            if (!file_exists($plg_meta_file)) {
                return null;
            }
            $plg_meta_contents = File::get($plg_meta_file);
            $plg_meta = json_decode($plg_meta_contents);
            //check whether if new installation or update
            $obj = HALOPluginModel::where('element', '=', $plg_meta->element)
								 ->whereAnd('folder', '=', $plg_meta->folder)->get()->first();
            if (!$obj) {
                $obj = new HALOPluginModel();
				//default setting
				$obj->status = 0;
            } else {
                $obj->isNew = false;
            }

        }

        //assign field from meta data

        try {
            //define all required field here
            $obj->name = $plg_meta->name;
            $obj->folder = $plg_meta->folder;
            $obj->element = $plg_meta->element;
            $obj->description = $plg_meta->description;
            //$obj->subscriber = $plg_meta->subscriber;
            $obj->src = $plg_meta->src;

            $obj->_meta = $plg_meta;
            $obj->_pkg_dir = $pkg_dir;
            $obj->destFolder = HALO_APP_PATH . '/plugins/' . $obj->folder . '/' . $obj->element;
        } catch (\Exception $e) {
			var_dump($e);
            return null;
        }

        return $obj;
    }

    /**
     * Load the plugin.json file of this plugin
     * 
     */	
	public function loadMeta() {
		$pkg_dir = HALO_APP_PATH . '/plugins/' . $this->folder . '/' . $this->element;
		$plg_meta_file = $pkg_dir . '/plugin.json';
		if (!file_exists($plg_meta_file)) {
			return null;
		}

		$plg_meta_contents = File::get($plg_meta_file);
		$plg_meta = json_decode($plg_meta_contents);
		$this->_meta = $plg_meta;
		return $this->_meta;
	}
    /**
     * Install 
     * 
     * @param  int  $step 
     * @return array       
     */
    public function install($step = 0)
    {
        if (!isset(HALOPluginModel::$steps[$step])) {
            return HALOError::passed();
        }
        //finished
        $func = HALOPluginModel::$steps[$step];
        $func_up = $func . '_up';
        //run the function_up
        array_push($this->_stack, $step);
        $rtn = call_user_func_array(array($this, $func_up), array());
        //on failure, rollback
        if ($rtn->any()) {
            $this->rollback();
        } else {
            $nextStep = $step + 1;

            $rtn = $this->install($nextStep);
        }
        return $rtn;
    }

    /**
     * Uninstall 
     * 
     * @param   int $step 
     * @return  array        
     */
    public function uninstall($step = null)
    {
        if (is_null($step)) {
            $step = count(HALOPluginModel::$steps) - 1;
        }

        if (!isset(HALOPluginModel::$steps[$step])) {
            return HALOError::passed();
        }
        //finished
        $func = HALOPluginModel::$steps[$step];
        $func_down = $func . '_down';

        //run the function_up
        array_push($this->_stack, $step);
        $rtn = call_user_func_array(array($this, $func_down), array());
        //on failure, rollback
        if ($rtn->any()) {
            return $rtn;
        } else {
            $nextStep = $step - 1;

            $rtn = $this->uninstall($nextStep);
        }
        return $rtn;

    }

    /**
     * Rollback 
     * 
     */
    public function rollback()
    {
        while (!empty($this->_stack)) {
            $step = array_shift($this->_stack);
            $func = HALOPluginModel::$steps[$step];
            $func_down = $func . '_down';
            call_user_func_array(array($this, $func_down), array());
        }
    }

    /**
     * Copy Source_up 
     * 
     * @return HALOError
     */
    public function copySource_up()
    {
        $dstFolder = $this->destFolder;
        //create the dst folder
        try {
            File::createDir($dstFolder);
        } catch (\Exception $e) {
            return HALOError::failed('Could not create plugin folder');
        }
        try {
            //copy folders
            if (!empty($this->src->folders)) {
                $folders = (array) $this->src->folders;
                foreach ($folders as $folder) {
                    File::recurse_copy($this->_pkg_dir . '/' . $folder, $dstFolder . '/' . $folder);
                }
            }
            //copy files
            if (!empty($this->src->files)) {
                $files = (array) $this->src->files;
                foreach ($files as $file) {
                    File::copy($this->_pkg_dir . '/' . $file, $dstFolder . '/' . $file);
                }
            }
            //copy meta file
            $file = 'plugin.json';
            File::copy($this->_pkg_dir . '/' . $file, $dstFolder . '/' . $file);
        } catch (\Exception $e) {
            return HALOError::failed('Could not copy file');
        }

        //update autoload
        $this->updateAutoload();
        return HALOError::passed();
    }

    /**
     * Copy Source_down 
     * 
     * @return HALOError
     */
    public function copySource_down()
    {
        $dstFolder = $this->destFolder;
        //create the dst folder
        File::deleteDirectory($dstFolder);

        //update autoload
        $this->updateAutoload();
        return HALOError::passed();

    }

	/*
		update ordering of this element
	*/
	public function updateOrdering() {
		if(!$this->ordering) {
			$maxOrdering = DB::table('halo_plugins')->max('ordering');
			$this->ordering = $maxOrdering + 1;
		}
	}
	
    /**
     * Init Database_up 
     * 
     * @return HALOError
     */
    public function initDatabase_up()
    {
        //Artisan::call('migrate:install');
        if (isset($this->_meta->db->migration)) {
            $migration_folder = 'app/plugins/' . $this->folder . '/' . $this->element . '/' . $this->_meta->db->migration;
			//run the migration if exists
			if (!empty($migration_folder)) {
				Artisan::call('migrate', array('--path' => $migration_folder));
				$this->_db_migrated = true;
			}
        }
		//run the db seeder script
		$seeder = $this->getSeederInstance();
		if($seeder && method_exists($seeder, 'run')) {
			try {
				$seeder->run();
			} catch (\Exception $e) {
				return HALOError::failed('Plugin seeding failed');
			}
		}
		//update plugin ordering
		$this->updateOrdering();
		
        //store or update the plugin
        $this->save();
        return HALOError::passed();
    }

	/*
		return the seeder object that configured to this plugin
	*/
	public function getSeederInstance(){
		$instance = null;
        if (isset($this->_meta->db->seed)) {
            $seederScript = HALO_PLUGIN_PATH . '/app/plugins/' . $this->folder . '/' . $this->element . '/' . $this->_meta->db->seed . '/seeder.php';
			if(file_exists($seederScript)) {
				require_once($seederScript);
				$seederClassName = ucfirst($this->folder) . ucfirst($this->element) . 'Seeder';
				if(class_exists($seederClassName)){
					$instance = new $seederClassName();
				}
			}
        }
	
		return $instance;
	}
	
	public function getMigrationDir() {
		if (isset($this->_meta->db->migration)) {
			$migration_folder = 'app/plugins/' . $this->folder . '/' . $this->element . '/' . $this->_meta->db->migration;
			return $migration_folder;
		} else {
			return '';
		}		
	}
    /**
     * Init Database_down
     * 
     * @return HALOError
     */
    public function initDatabase_down()
    {

        if ($this->_db_migrated) {
            Artisan::call('migrate:rollback');
        }
        if (!empty($this->id) && $this->isNew) {
            $this->delete();
        }
		//run the db seeder script
		$seeder = $this->getSeederInstance();
		if($seeder && method_exists($seeder, 'clean')) {
			try {
				$seeder->clean();
			} catch (\Exception $e) {
				return HALOError::failed('Plugin seeder clean up failed');
			}
		}
        return HALOError::passed();
    }

	/**
	 * run Script_up 
	 * 
	 * @return HALOError
	 */
    public function runScript_up()
    {
        return HALOError::passed();
    }

    /**
     * run Script_down 
     * 
     * @return HALOError
     */
    public function runScript_down()
    {

        return HALOError::passed();
    }

    /**
     * update Autoload
     * 
     * @return HALOError
     */
    public function updateAutoload()
    {
        //Artisan::call('dump-autoload');

        return HALOError::passed();
    }

	/*
		refresh plugin ordering
	*/
    public static function rebuildOrdering()
    {
        $filters = HALOPluginModel::orderBy('ordering', 'asc')
            ->orderBy('updated_at', 'desc')    ->get();
        if ($filters) {
            $ordering = 0;
            foreach ($filters as $filter) {
                $newOrdering = ++$ordering;
                if ($filter->ordering != $newOrdering) {
                    $filter->ordering = $newOrdering;
                    $filter->save();
                }
            }
        }
    }
	
	/*
		return all active plugin
	*/
	public static function getActivePlugins() {
		static $plugins = null;
		if(is_null($plugins)) {
			$plugins = HALOPluginModel::where('status', '=', 1)->orderBy('ordering', 'asc')->get();
		}
		return $plugins;
	}
	
	/*
		check if a plugin is installed and activated
	*/
	public static function isActive($name, $folder = null) {
		$activePlugins = self::getActivePlugins();
		foreach($activePlugins as $plugin) {
			if($plugin->name == $name && (is_null($folder) || $plugin->folder == $folder)) {
				return true;
			}
		}
		return false;
	}
	
	/*
		return display name for this plugin
	*/
	public function getDisplayName() {
		$meta = $this->loadMeta();
		$displayName = isset($meta->display_name)?$meta->display_name:$this->name;
		return $displayName;
	}

	/*
		return display description for this plugin
	*/
	public function getDescription() {
		$meta = $this->loadMeta();
		$description = isset($meta->description)?$meta->description:$this->description;
		return $description;
	}
}
