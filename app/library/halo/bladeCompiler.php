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
 
use Illuminate\View\Compilers\BladeCompiler;

class HALOBladeCompiler extends BladeCompiler
{

    /**
     * All of the available compiler functions.
     *
     * @var array
     */
    protected $compilers = array('Extensions',
						        'Extends',
						        'Comments',
						        'Echos',
						        'Openings',
						        'Closings',
						        'OpeningUI',
						        'ClosingUI',
						        'Else',
						        'Unless',
						        'EndUnless',
						        'Includes',
						        'Each',
						        'Yields',
						        'Shows',
						        'Language',
						        'SectionStart',
						        'SectionStop',
						        'SectionAppend',
						        'SectionOverwrite');

    /**
     * Compile Blade structure openings into valid PHP.
     *
     * @param  string  $value
     * @return string
     */
    protected function compileOpeningUI($value)
    {
        $pattern = $this->createMatcher('beginUI');

        $replace = '$1<?php HALOUIBuilder::setFunc($2,function($data){ extract($data);ob_start();?>';

        return preg_replace($pattern, $replace, $value);

    }

    /**
     * Compile Blade structure closings into valid PHP.
     *
     * @param  string  $value
     * @return string
     */
    protected function compileClosingUI($value)
    {
        $pattern = '/(\s*)@(endUI)(\s*)/';

        return preg_replace($pattern, '$1<?php return ltrim(ob_get_clean());}); ?>$3', $value);
    }

}
