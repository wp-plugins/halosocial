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
 
use Illuminate\Support\Contracts\RenderableInterface as Renderable;

class HALOUIBuilder implements GUIBuildable
{
    public $_template = '';
    public $children;
    public static $_instances = array();

    protected static $_func = array();
    protected static $loadedUI = array();
    protected static $views = array();
    private $_name = '';
    private $_type = '';
    private $_lastPos = null;

    /**
     * Construction
     *
     * @param string $name the reference name for this UI
     * @param string $type the ui type that will be used for rendering in template
     */
    private function HALOUIBuilder($name = '', $type = '')
    {
        $this->_name = $name;
        $this->_type = $type;
        $this->children = new HALOUIBuilderArray();
    }

    /**
     * initialize a new UI object
     *
     * @param string $name the UI object name used for reference
     * @param string $type the UI type used for rendering in template
     * @param array $attrs UI attribite value
     * @return HALOUIBuilder new object
     */
    public static function getInstance($name, $type, array $attrs = array())
    {
        if (empty($name)) {
            //treat as dummy UI builder, no need to cache it
			$instance = new HALOUIBuilder($name, $type);
			$instance->setAttrs($attrs);
            return $instance;
        }

        if (!isset(self::$_instances[$name])) {
			$instance = new HALOUIBuilder($name, $type);
			$instance->setAttrs($attrs);
            self::$_instances[$name] = $instance;
        }
        return self::$_instances[$name];
    }

    /**
     * another initialize method with json object as parameter
     *
     * @param object $obj json object
     * @return HALOUIBuilder new object
     */
    public static function getInstanceFromJSON($obj)
    {
        $type = isset($obj->_type) ? $obj->_type : '';
        $name = isset($obj->_name) ? $obj->_name : '';
        $ui = self::getInstance($name, $type, get_object_vars($obj));
        //add children ui
        if (isset($obj->children)) {
            foreach ((array) ($obj->children) as $position => $child) {
                $ui->addUIJSON($position, $child);
            }
        }
        return $ui;
    }

    /**
     * add another HALOUIBuilder object as a child
     *
     * @param string position
     * @param HALOUIBuilder the child object to be added
     * @return HALOUIBuilder this object
     */
    public function addUI($position, $ui)
    {

        $part = explode('@', $position);
        if (count($part) == 2 && $part[1] == 'array') {
            if (!isset($this->children[$position]) || get_class($this->children[$position]) != 'HALOUIBuilderArray') {
                //init HALOUIBuilderArray instance
                $this->children[$position] = HALOUIBuilderArray::getInstance();
            }
            $this->children[$position][] = $ui;
        } else {
            $this->children[$position] = $ui;
        }

        //for remove under condition
        $this->_lastPos = $position;
        return $this;
    }

    /**
     * add another JSON object as a child
     *
     * @param string position
     * @param HALOUIBuilder the child object to be added
     * @return HALOUIBuilder this object
     */
    public function addUIJSON($position, $uiJSON)
    {

        $part = explode('@', $position);
        if (count($part) == 2 && $part[1] == 'array') {
            if (!isset($this->children[$position]) || get_class($this->children[$position]) != 'HALOUIBuilderArray') {
                //init HALOUIBuilderArray instance
                $this->children[$position] = HALOUIBuilderArray::getInstance();
            }
            $this->children[$position]->array_merge(HALOUIBuilderArray::getInstanceFromJSON($uiJSON));
        } else {
            $this->children[$position] = HALOUIBuilder::getInstanceFromJSON($uiJSON);
        }
        //for remove under condition
        $this->_lastPos = $position;
        return $this;
    }
    /**
     * get Name
     * 
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }
    /**
     * 
     * @param  string  $type
     * @return bool
     */
    public function hasType($type)
    {

        return ($this->_type == $type);
    }
    /**
     * 
     * 
     * @param mixed $attr
     * @param mixed $value
     * @return   HALOUIBuilder
     */
    public function set($attr, $value)
    {
        //only allow attr not in deniedList
        if ($this->acceptedAttr($attr)) {
            $this->$attr = $value;
        }
        return $this;
    }
    /**
     * 
     * @param  mixed $condition
     * @return HALOUIBuilder
     */
    public function where($condition)
    {
        //remove last push UI if $condition is false
        if (!$condition && !is_null($this->_lastPos) && isset($this->children[$this->_lastPos])) {
            array_pop($this->children[$this->_lastPos]);
            //reset the lastpost
            $this->_lastPos = null;
        }
        return $this;
    }
    /**
     * set Attribute
     * 
     * @param array $arr 
     * @return  HALOUIBuilder
     */
    public function setAttrs(array $arr)
    {
        foreach ($arr as $key => $value) {
            $this->set($key, $value);
        }
        return $this;
    }
    /**
     * accepted Attribute
     * 
     * @param  mixed $attr
     * @return HALOUIBuilder
     */
    private function acceptedAttr($attr)
    {
        $deniedList = array('_name', '_type', '_lastPos', 'children');
        return !in_array($attr, $deniedList);
    }
    /**
     * 
     * @param  string $template
     * @return string
     */
    public function fetch($template = 'ui/default', $cacheKey ='')
    {
		if(is_null($template)) $template = 'ui/default';
        $enableDebug = HALOConfig::get('global.enableTemplateDebug');
        // $enableDebug = 1;
        if ($enableDebug) {
            $uid = uniqid();
            // $logName = 'ui fetching (name: ' . $this->_name . ', type: ' . $this->_type . ') ' . $uid;
            $logName = $this->_type . '_' . $uid;
            HALOLogging::start($logName);
        }
		
		//check for cache if required
		if($cacheKey) {
			$that = $this;
			return Cache::rememberForever($cacheKey, function() use($that, $template) {
				return $that->fetch($template);
			});
		}
		
        $this->_template = $template;
        //get the template path from type
        $par = explode('.', $this->_type);
        if (count($par) >= 2) {
            $this->_template = 'ui/' . implode('/', array_slice($par, 0, count($par) - 1)) . '/default';
        }

        $builder = $this;
        $my = HALOUserModel::getUser();

        $view = $builder->getView($builder->_template);
        $view->with('builder', $builder)
             ->with('my', $my);
        $rtn = trim($this->renderHALOUITemplate($view));
        if ($enableDebug) {
            HALOLogging::stop($logName);
        }
        return $rtn;
    }

    /**
     * return view object by giving viewName
     * 
     * @param  string $viewName
     * @return Illuminate\View\View
     */
    public function getView($viewName)
    {
        if (!isset(HALOUIBuilder::$views[$viewName])) {
            HALOUIBuilder::$views[$viewName] = View::make($viewName);
        }
        return HALOUIBuilder::$views[$viewName];
    }

    /**
     * set UI render function
     * 
     * @param string $name
     * @param mixed $f
     */
    public static function setFunc($name, $f)
    {
        self::$_func[$name] = $f;
    }

    /**
     * get UI render function
     * 
     * @param  string $name
     * @return bool
     */
    public static function getFunc($name)
    {
        return isset(self::$_func[$name]) ? self::$_func[$name] : null;
    }


    /**
     * return UI render function for this UI instance
     * 
     * @return string
     */
    public function getRenderFunc()
    {
        return HALOUIBuilder::getFunc($this->_type);
    }


    /**
     * render html for this UI instance
     * 
     * @param  mixed $view
     * @return string
     */
    public function renderHALOUITemplate($view)
    {
        if (!isset(HALOUIBuilder::$loadedUI[$this->_template])) {
            ob_start();
            $this->loadHALOUITemplate($view);
            HALOUIBuilder::$loadedUI[$this->_template] = true;
            $c = ob_get_contents();
            ob_end_clean();
        }
        $rtn = '';
        //gather Data
        $engine = $view->getEngine();
        $path = $view->getPath();

        $data = array_merge($view->getEnvironment()->getShared(), $view->getData());

        foreach ($data as $key => $value) {
            if ($value instanceof Renderable) {
                $data[$key] = $value->render();
            }
        }

        if (!is_null($func = $this->getRenderFunc())) {
            $rtn = call_user_func_array($func, array($data));
        }
        return $rtn;
    }

    /**
     * load UI template file
     * 
     * @param  mixed $view
     * @return string
     */
    public function loadHALOUITemplate($view)
    {
        $engine = $view->getEngine();
        $path = $view->getPath();

        if (method_exists($engine, 'getCompiler')) {
            $compiler = $engine->getCompiler();
            if ($compiler->isExpired($path)) {
                $compiler->compile($path);
            }

            $compiled = $compiler->getCompiledPath($path);
        } else {
            $compiled = $path;
        }
        include_once $compiled;
    }
    /**
     * 
     * 
     * @param  string  $position 
     * @return bool
     */
    public function hasChild($position)
    {
        return isset($this->children[$position]);
    }
    /**
     * get Children
     * 
     * @return mixed
     */
    public function getChildren()
    {
        return $this->children;
    }
    /**
     * get Child
     * 
     * @param  mixed $position
     * @return object
     */
    public function getChild($position)
    {
        return isset($this->children[$position]) ? $this->children[$position] : new HALOUIBuilder();
    }
    /**
     * get On Click
     * 
     * @return string 
     */
    public function getOnClick()
    {
        if (!empty($this->onClick)) {
            return ' onClick="' . $this->onClick . '"';
        } else {
            return '';
        }
    }
    /**
     * get OnChange
     * 
     * @return string
     */
    public function getOnChange()
    {
        if (!empty($this->onChange)) {
            return ' onChange="' . $this->onChange . '"';
        } else {
            return '';
        }
    }
    /**
     * get OnKeyup
     * 
     * @return string
     */
    public function getOnKeyup()
    {
        if (!empty($this->onKeyup)) {
            return ' onkeyup="' . $this->onKeyup . '"';
        } else {
            return '';
        }
    }
	
    /**
     * get Icon
     * 
     * @return string
     */
    public function getIcon()
    {
        if (!empty($this->icon)) {
            return '<i class="fa fa-' . $this->icon . '"></i> ';
        } else {
            return '';
        }
    }
    /**
     * 
     * @param  string  $name
     * @return string
     */
    public static function icon($name)
    {
        return '<i class="fa fa-' . $name . '"></i>';
        //return HALOUIBuilder::getInstance('','icon',array('icon'=>$name))->fetch();
    }
    /**
     * get Id
     * 
     * @return string 
     */
    public function getId()
    {
        if (!empty($this->id)) {
            return 'id="' . $this->id . '"';
        } else {
            return '';
        }
    }
    /**
     * get Zone
     * 
     * @param  int $level
     * @param  int $id
     * @param  string $default
     * @return string
     */
    public function getZone($level = null, $id = null, $default = '')
    {
        if (!empty($this->zone)) {
            $levelZone = '';
            $idZone = '';
            if (!is_null($level)) {
                $level = intval($level);
                for ($i = 1; $i <= $level; $i++) {
                    $levelZone = $levelZone . '_l' . $i;
                }
            }
            if (!is_null($id)) {
                $idZone = '_i' . $id;
            }
            return 'data-halozone="' . $this->zone . $levelZone . $idZone . '"';
        } else {
            //rollback to default
            if ($default) {
                return 'data-halozone="' . $default . '"';
            }
            return '';
        }
    }

    /**
     * 
     * @param  mixed $key
     * @return string
     */
    public function getData($key = null)
    {
		return HALOOutputHelper::getHtmlData($this->data, $key);
    }

    /**
     * return raw data value of a key
     * 
     * @param  mixed $key     [description]
     * @param  string $default [description]
     * @return mixed
     */
    public function getRawData($key, $default = '')
    {
        $value = $default;
        if (!empty($this->data)) {
            $data = !is_array($this->data) ? ((array) $this->data) : $this->data;
            $value = isset($data[$key]) ? $data[$key] : $value;
        }
        return $value;
    }
    /**
     * set raw data
     * 
     * @param mixed $key
     * @param string $value
     * @return   string
     */
    public function setRawData($key, $value)
    {
        $this->data = empty($this->data) ? array() : (array) $this->data;
        $this->data[$key] = $value;
        return $value;
    }
    /**
     * get Data Role
     * 
     * @param  mixed $key
     * @return array
     */
    public function getDataRole($key = null)
    {
        $dataArr = [];
        if (!empty($this->data)) {
            $data = !is_array($this->data) ? ((array) $this->data) : $this->data;
            if (!empty($data[$key])) {
                return $data[$key];
            }
            foreach ($data as $name => $val) {
                $dataArr[$name] = $val;
            }
        }

        return $dataArr;
    }
    /**
     * get Url
     * 
     * @return string
     */
    public function getUrl()
    {
        if (!empty($this->url)) {
            if ($this->url == 'javascript') {
                return "javascript:void(0);";
            } else {
                return URL::to($this->url);
            }
        } else {
            return '#';
        }
    }
    /**
     * get Size
     * 
     * @return string
     */
    public function getSize()
    {
        if (!empty($this->size)) {
            return 'col-md-' . $this->size;
        } else {
            return '';
        }
    }
    /**
     * get Class
     * 
     * @param  string $otherClass
     * @return string
     */
    public function getClass($otherClass = '')
    {
        if (!empty($this->class)) {
            return 'class="' . $this->class . ' ' . $otherClass . '"';
        } else {
            if ($otherClass !== '') {
                return 'class="' . $otherClass . '"';
            }
            return '';
        }
    }
    /**
     * get Options
     * 
     * @return array
     */
    public function getOptions()
    {
        if (!empty($this->options)) {
            //try to convert object to array
            if (is_object($this->options)) {
                $this->options = json_decode(json_encode($this->options), true);
            }
            if (is_array($this->options)) {
                foreach ($this->options as $key => $option) {
                    if (is_string($option)) {
                        //detect json format
                        $json = json_decode($option);
                        if ($json != null) {
                            $option = $json;
                        }
                    }
                    $this->options[$key] = HALOObject::getInstance($option);
                }
                return $this->options;
            } else {
                //invalid options
                return array();
            }
        } else {
            return array();
        }
    }
    /**
     * get Disabled 
     * 
     * @return string
     */
    public function getDisabled()
    {
        if (!empty($this->disabled) && $this->disabled == 'disabled') {
            return 'disabled';
        } else {
            return '';
        }
    }
    /**
     * get Read Only
     * 
     * @return string
     */
    public function getReadOnly()
    {
        if (!empty($this->readonly) && $this->readonly == 'readonly') {
            return 'readonly';
        } else {
            return '';
        }
    }
    /**
     * get Row
     * 
     * @return bool
     */
    public function getRow()
    {
        return (isset($this->row) && !empty($this->row));
    }
    /**
     * set Row
     * 
     * @param mixed $val
     * @return  HALOUIBuilder
     */
    public function setRow($val)
    {
        $this->row = $val;
        return $this;
    }
    /**
     * get Validation
     * 
     * @return string
     */
    public function getValidation()
    {
        if (!empty($this->validation)) {
            $convRules = array('minval' => 'min', 'maxval' => 'max', 'min' => 'minlength', 'max' => 'maxlength');
            $rules = explode('|', $this->validation);
            $validationStr = array();
            foreach ($rules as $rule) {
                if ($rule == 'required') {
                    $validationStr[] = 'required';
                } else {
                    //rule  are in 2 parts  seperated by colon ':'
                    $parts = explode(':', $rule);
                    if (trim($parts[0]) != '') {
                        //rule name must not be empty
                        $convRule = (count($parts) > 1 && isset($convRules[$parts[0]])) ? $convRules[$parts[0]] : $parts[0];
                        $validationStr[] = (count($parts) == 1) ? 'data-rule-' . $parts[0] . '="true"' : 'data-rule-' . $convRule . '="' . $parts[1] . '"';
                    }
                }
            }
            if (!empty($validationStr)) {
                return implode(' ', $validationStr);
            }
        }

        return '';
    }
    /**
     * get Validation Label
     * 
     * @return string
     */
    public function getValidationLabel()
    {
        if (!empty($this->validation)) {
            if (strpos($this->validation, 'required') !== false) {
                return 'required';
            }
        }

        return '';
    }

    /******** Magic function define ***************/
    /**
     * 
     * 
     * @param  string $name
     * @return string
     */
    public function __get($name)
    {
        //specific treatment for error attribute
        if ($name == 'error') {
            $errors = HALOError::getErrors();
            $error = (!empty($errors) && $errors->has($this->name) ? $errors->first($this->name) : '');
            //display the error message with field title instead of field name
            if ($error) {
                $error = str_replace($this->name, '"' . $this->title . '"', $error);
            }

            //cache the error message
            $this->error = $error;
            return $error;
        }
        return '';
    }
    /**
     *
     * @return string 
     */
    public function __toString()
    {
        return $this->fetch()->toString();
    }
    /**
     * get Attribute
     * 
     * @param  string $name
     * @param  string $default
     * @return string
     */
    public function getAttr($name, $default = '')
    {
        return (isset($this->$name) ? $this->$name : $default);
    }

    /**
     * copy the current ui builder object with all its attributes, just replace the name and type
     * 
     * @param  string $name 
     * @param  string $type 
     * @return object HALOUIBuilder
     */
    public function copyAttributes($name, $type)
    {
        $object = clone $this;
        $object->_name = $name;
        $object->_type = $type;
        return $object;
    }

    /**
     * return help block for this UI
     * 
     * @param  mixed $helptext
     * @return string
     */
    public function getHelpText($helptext = null)
    {
        $helptext = is_null($helptext) ? $this->helptext : $helptext;
        if (!empty($helptext)) {
            return '<a href="javascript:void(0);" class="halo-form-helpblock" data-toggle="tooltip" title="' . htmlentities($helptext) . '">' . HALOUIBuilder::icon('question-circle') . '</a>';
        } else {
            return '';
        }
    }

    /**
     * get error class for this ui
     * 
     * @return string
     */
    public function getErrorClass()
    {
        return $this->error ? 'has-error has-feedback' : '';
    }

    /**
     * get Pagination Text
     * 
     * @param  mixed $pagination
     * @return string  result pagination html
     */
    public static function getPaginationText($pagination)
    {
        if ($pagination && method_exists($pagination, 'getFrom')) {
            return sprintf(__halotext('Result from %d to %d in total %d'), $pagination->getFrom(), $pagination->getTo(), $pagination->getTotal());
        } else {
            return '';
        }
    }

    /**
     * get Pagination Attr
     * 
     * @param  mixed $pagination
     * @return string  result pagination html
     */
    public static function getPageAttr($pagination)
    {
        if ($pagination && method_exists($pagination, 'getCurrentPage')) {
			return 'data-halopage="' . $pagination->getCurrentPage() . '"';
        } else {
            return '';
        }
    }
	
}
