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

use \Michelf\MarkdownExtra;

class HALOOutputHelper
{

    /**
     * Return a a html string that displayable on browser
     *
     * @param  string  $string
     * @param  bool $strip_tags
     * @return string
     */
    public static function text2html($string, $strip_tags = false)
    {
        $string = self::nl2br(rtrim($string));
        $key = md5($string);
        //@todo: add cache for performance concern
        $string = Cache::remember("text2html.{$key}", 60, function () use ($string) {
            //convert newline to br
            //$string = nl2br($string);
            //convert markdown syntax
			require_once(dirname(__FILE__) . '/Parsedown.php');
			require_once(dirname(__FILE__) . '/ParsedownExtra.php');
			$parser = new ParsedownExtra();
            $string = $parser->text($string);
            //purify the text
            $string = Purifier::clean($string);

            //make url link clickable
            $string = HALOOutputHelper::parseUrl($string);
            return $string;
        });
        $string = $strip_tags ? strip_tags($string) : $string;
        return $string;
    }
	
    /**
     * clean an input text (replacement for htmlentities)
     *
     * @param  string  $string
     * @return string
     */
	public static function cleanText($string){
        $key = md5($string);
        $string = Cache::remember("cleantext.{$key}", 60, function () use ($string) {
            $string = Purifier::clean($string);
			$string = strip_tags($string);
			return $string;
        });
        return $string;
	}
    /**
     * replace new line to <br>
     *
     * @param  string $str
     * @return mixed
     */
    public static function nl2br($str)
    {
        $order = array("\r\n", "\n", "\r");
        $replace = '<br />';
        return str_replace($order, $replace, $str);
    }
    /**
     * replace <tag>
     *
     * @param  string $str
     * @param  array  $tags
     * @return mixed
     */
    public static function striptags($str, array $tags)
    {
        $r = array();
        foreach ($tags as $tag) {
            $r[] = '<' . $tag . '>';
            $r[] = '</' . $tag . '>';
        }
        return str_replace($r, "", $str);
    }
    /**
     * ellipsis
     *
     * @param  string  $string
     * @param  integer $len
     * @param  string  $ellipsis
     * @return string
     */
	public static function ellipsis($string, $len = 80, $ellipsis = ' ...'){
		$key = md5($string . '_' . $len . '_' . $ellipsis);
		$truncate = Cache::remember($key,60,function() use($string,$len,$ellipsis){
			return HALOOutputHelper::truncate($string,$len,array('ending'=>$ellipsis,'exact'=>false,'html'=>true));
		});
		return $truncate;
	}

    /**
     * Truncates text.
     *
     * Cuts a string to the length of $length and replaces the last characters
     * with the ending if the text is longer than length.
     *
     * ### Options:
     *
     * - `ending` Will be used as Ending and appended to the trimmed string
     * - `exact` If false, $text will not be cut mid-word
     * - `html` If true, HTML tags would be handled correctly
     *
     * @param string  $text String to truncate.
     * @param integer $length Length of returned string, including ellipsis.
     * @param array $options An array of html attributes and options.
     * @return string Trimmed string.
     * @access public
     * @link http://book.cakephp.org/view/1469/Text#truncate-1625
     */
    public static function truncate($text, $length = 100, $options = array())
    {
        $default = array(
            'ending' => '...', 'exact' => true, 'html' => false,
        );
        $options = array_merge($default, $options);
        extract($options);

        if ($html) {
            if (mb_strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
                return $text;
            }
            $totalLength = mb_strlen(strip_tags($ending));
            $openTags = array();
            $truncate = '';

            preg_match_all('/(<\/?([\w+]+)[^>]*>)?([^<>]*)/', $text, $tags, PREG_SET_ORDER);
            foreach ($tags as $tag) {
                if (!preg_match('/img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param/s', $tag[2])) {
                    if (preg_match('/<[\w]+[^>]*>/s', $tag[0])) {
                        array_unshift($openTags, $tag[2]);
                    } else if (preg_match('/<\/([\w]+)[^>]*>/s', $tag[0], $closeTag)) {
                        $pos = array_search($closeTag[1], $openTags);
                        if ($pos !== false) {
                            array_splice($openTags, $pos, 1);
                        }
                    }
                }
                $truncate .= $tag[1];

                $contentLength = mb_strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $tag[3]));
                if ($contentLength + $totalLength > $length) {
                    $left = $length - $totalLength;
                    $entitiesLength = 0;
                    if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $tag[3], $entities, PREG_OFFSET_CAPTURE)) {
                        foreach ($entities[0] as $entity) {
                            if ($entity[1]+1 - $entitiesLength <= $left) {
                                $left--;
                                $entitiesLength += mb_strlen($entity[0]);
                            } else {
                                break;
                            }
                        }
                    }

                    $truncate .= mb_substr($tag[3], 0, $left + $entitiesLength);
                    break;
                } else {
                    $truncate .= $tag[3];
                    $totalLength += $contentLength;
                }
                if ($totalLength >= $length) {
                    break;
                }
            }
        } else {
            if (mb_strlen($text) <= $length) {
                return $text;
            } else {
                $truncate = mb_substr($text, 0, $length - mb_strlen($ending));
            }
        }
        if (!$exact) {
            $spacepos = mb_strrpos($truncate, ' ');
            if (isset($spacepos)) {
                if ($html) {
                    $bits = mb_substr($truncate, $spacepos);
                    preg_match_all('/<\/([a-z]+)>/', $bits, $droppedTags, PREG_SET_ORDER);
                    if (!empty($droppedTags)) {
                        foreach ($droppedTags as $closingTag) {
                            if (!in_array($closingTag[1], $openTags)) {
                                array_unshift($openTags, $closingTag[1]);
                            }
                        }
                    }
                }
                $truncate = mb_substr($truncate, 0, $spacepos);
            }
        }
        $truncate .= $ending;

        if ($html) {
            foreach ($openTags as $tag) {
                $truncate .= '</' . $tag . '>';
            }
        }

        return $truncate;
    }

    /**
     * function to render comment block
     *
     * @param  object $target
     * @param  string $limit
     * @param  string $zone
     * @return HALOUIBuilder new object
     */
    public static function renderCommentHtml($target, $limit, $zone)
    {
        $key = 'comment.' . $target->getContext() . '.' . $target->id . '.' . $limit . '.' . $zone;
        //$html = Cache::rememberForever($key, function() use($target,$limit,$zone){
        return HALOUIBuilder::getInstance('', 'comment.wrapper', array('target' => $target, 'limit' => $limit, 'zone' => $zone))->fetch();
        //});
		
        return $html;
    }

    /**
     * shorten a url to display host name only
     *
     * @param  string  $url
     * @param  string  $take
     * @param  bool $ucase
     * @return string
     */
    public static function shortenUrl($url, $take = PHP_URL_HOST, $ucase = true)
    {
        $shortenUrl = parse_url($url, $take);

        //trim url and make it ucase
        if ($ucase) {
            $shortenUrl = trim(strtoupper($shortenUrl));
            if (strpos($shortenUrl, 'WWW.') === 0) {
                $shortenUrl = substr($shortenUrl, 4);
            }
        }
        return $shortenUrl;
    }

    /**
     * format and return an external url
     *
     * @param  string $url
     * @return string
     */
    public static function getExternalUrl($url)
    {
        $allowBackLink = HALOConfig::get('global.allowBackLink', 1);
        if (!$allowBackLink && strpos($url, HALO_ROOT_URL) !== 0) {
            $url = URL::to('?view=redirect&ref=' . $url);
        }
        return $url;
    }

    /**
     * check if mobile user
     * 
     * @return bool
     */
    public static function isMobile()
    {
        static $isMobile = null;
        if (is_null($isMobile)) {
			$browser = new Browser();
            $isMobile = $browser->isMobile();
        }
        return $isMobile;
    }

    /*
    check if tablet user
     */
    /**
     * check if tablet user
     * 
     * @return bool
     */
    public static function isTablet()
    {
        static $isTablet = null;
        if (is_null($isTablet)) {
			$browser = new Browser();
            $isTablet = $browser->isTablet();
        }
        return $isTablet;
    }

    /**
     * check if desktop user
     * 
     * @return bool
     */
    public static function isDesktop()
    {
        static $isDesktop = null;
        if (is_null($isDesktop)) {
			//$browser = new Browser();
            $isDesktop = !self::isTablet() && !self::isMobile();
        }
        return $isDesktop;
    }
    /**
     * get Page Title
     * 
     * @param  string $prefix
     * @param  string $usec
     * @return string
     */
    public static function getPageTitle($prefix, $usec)
    {
        $titles = array('stream' => __halotext('Stream'),
			            'member' => __halotext('Members'),
			            'group' => __halotext('Groups'),
			            'shop' => __halotext('Shops'),
			            'friend' => __halotext('Friends'),
			            'photo' => __halotext('Photos'),
			            'video' => __halotext('Videos'),
			            'category' => __halotext('Categories'),
			            'follower' => __halotext('Followers'));
        $postfix = isset($titles[$usec]) ? $titles[$usec] : '';
        $parts = array();
        if ($prefix) {
            $parts[] = $prefix;
        }
        if ($postfix) {
            $parts[] = $postfix;
        }
        return implode(' - ', $parts);
    }

    /**
     * Return meta tag value of this object
     * 
     * @param  string $type meta tag type
     * @param  mixed $model target model
     * @return string meta tag value
     */
	public static function getMetaTags($type, $model = null){
	
		static $metaData = null;
		//init meta data
		if(is_null($metaData)){
			$metaData = new stdClass();
		}
		//trigger event 
		if(!isset($metaData->$type)){
			Event::fire('metatag.onBeforeGetting', array(&$metaData, $model, $type));
		}
		if(isset($metaData->$type)){
			return $metaData->$type;
		}
		
		if(!is_null($model) && method_exists($model, 'getMetaTags')){
			$metaData->$type = call_user_func_array(array($model, 'getMetaTags'), array($type));
			return $metaData->$type;
		} else {
			//homepage meta tags
			$meta = '';
			//for searching page meta tags
			$metaTags = HALOFilter::getFilterMetaTags($type);
			if(!empty($metaTags)){
				//add prefix and postfix meta tags
				//prefix
				if(in_array($type, array('title', 'ogtitle', 'description', 'ogdescription'))){
					$prefix = HALOOutputHelper::getSearchMetaPrefix();
					if($prefix){
						$metaTags[-1] = $prefix;
					}
				}
				//postfix
				if(in_array($type, array('title'))){
					if($postfix = HALOOutputHelper::getSearchMetaPostfix()){
						$metaTags[10000] = $postfix;
					}
				}
				ksort($metaTags);
				return trim(strip_tags(implode(' ', $metaTags)));
			}
			switch($type){
				case 'title':
				case 'ogtitle':
					$titles =  array(HALOAssetHelper::getPageTitle());
					$titles[] = __halotext('Homepage');
					$meta = implode(' | ', $titles);
					$meta = HALOOutputHelper::getPageTitle($meta,Input::get('usec','stream'));
					break;
				case 'cover':
					$meta =  HALOPhotoHelper::getSiteBanner();
					break;
				case 'description':
				case 'ogdescription':
					$meta = HALOOutputHelper::ellipsis(HALOUtilHelper::getSiteDescription(), 200);

					$meta = trim(strip_tags($meta));

					break;
				case 'keywords':
					break;
				default:
					
			}
			$metaData->$type = $meta;
			return $metaData->$type;
		}
	}
    	
    /**
     * Return search meta tag prefix
     * 
     * @return string meta tag prefix
     */
	public static function getSearchMetaPrefix(){
		$usec = Input::get('usec',null);
		if($usec){
			return __halotext('Search ' . $usec);
		}
		return '';
	}
    /**
     * Return search meta tag postfix
     * 
     * @return string meta tag postfix
     */
	public static function getSearchMetaPostfix(){
		return '| ' . HALOAssetHelper::getPageTitle();
	}
    /**
     * view mode processing functions
     * 
     * @param  string $viewName
     * @return mixed
     */
    public static function getDefaultViewMode($viewName)
    {
		return $default = HALOConfig::get('viewmode.default.' . $viewName, 'list');
        // $defaultList = array('vpost' => 'thumbnail', 'vevent' => 'thumbnail', 'vgroup' => 'thumbnail', 'vpage' => 'thumbnail', 'vshop' => 'thumbnail');
        // $default = isset($defaultList[$viewName]) ? $defaultList[$viewName] : '';
        // return HALOConfig::get($viewName, $default);
    }
    /**
     * store View Mode
     * 
     */
    public static function storeViewMode()
    {
        $views = array('vpost', 'vevent', 'vgroup', 'vpage', 'vshop');//list of support view modes
        foreach ($views as $view) {
            $mode = Input::get($view);
            if ($mode) {
                Session::put($view, $mode);
            }
        }
    }
    /**
     * check View Mode
     * 
     * @param  mixed  $view
     * @param  mixed  $mode
     * @return bool
     */
    public static function hasViewMode($view, $mode)
    {
        $currMode = self::getViewMode($view);
        return ($currMode == $mode);
    }
    /**
     * get View Mode 
     * @param  string $view 
     * @return mixed
     */
    public static function getViewMode($view)
    {
        $viewName = 'v' . $view;
        $currMode = Input::get($viewName, Session::get($viewName, self::getDefaultViewMode($viewName, '')));
        return $currMode;
    }
    /**
     * check Active View
     * @param  mixed  $view 
     * @param  mixed  $mode 
     * @return string
     */
    public static function hasActiveView($view, $mode)
    {
        if (self::hasViewMode($view, $mode)) {
            return 'active';
        } else {
            return '';
        }
    }
    /**
     * apply View Mode Settings
     * 
     * @param  Illuminate\Database\Query\Builder $query
     * @return string
     */
    public static function applyViewModeSettings(&$query)
    {
        if (HALOOutputHelper::hasActiveView('post', 'single')) {
            //build post carousel content
            $current = Input::get('pg', 1) - 1;//zero based index
            $skip = ($current <= 2) ? 0 : ($current - 2);
            $posts = $query->skip($skip)->take(20)->get();
            $curIdx = $current - $skip;
            $postCarouselHtml = HALOUIBuilder::getInstance('', 'post.carousel', array('posts' => $posts,
                																	'skip' => $skip,
                 																	'class' => 'halo-post-carousel',
                 																	'data' => array('mode' => 'fullview',
                																					'showModes' => 0,
                																					'current' => $curIdx, 'targetId' =>'rg-gallery-' . HALOUtilHelper::uniqidInt())))->fetch();
            //HALOResponse::setData('post_carousel',$postCarouselHtml);

            //single view mode, set limit pagination to 1
            $postData['limit'] = 1;
            Input::merge($postData);

            if (HALOResponse::ajax()) {
                HALOResponse::queueScriptCall('halo.post.addCarousel', $postCarouselHtml);
            } else {
                HALOResponse::addZoneScript('halo-posts-wrapper', 'halo.post.addCarousel', $postCarouselHtml);
            }
        }
    }
    /**
     * add Post View Modes 
     * 
     * @param  object $actions
     * @return HALOOutputHelper
     */
    public static function addPostViewModes(&$actions, $view = 'post')
    {
        $actions->addUI('view_thumnail', HALOUIBuilder::getInstance('', 'content', array('title' => '',
            'tooltip' => __halotext('Thumbnail view mode'),
            'onClick' => "halo.util.setViewMode('$view','thumbnail')",
            'icon' => 'th',
            'class' => 'halo-btn halo-btn-default halo-btn-nobg' . HALOOutputHelper::hasActiveView($view, 'thumbnail'))));
        $actions->addUI('view_list', HALOUIBuilder::getInstance('', 'content', array('title' => '',
            'tooltip' => __halotext('Listing view mode'),
            'onClick' => "halo.util.setViewMode('$view','list')",
            'icon' => 'align-justify',
            'class' => 'halo-btn halo-btn-default halo-btn-nobg' . HALOOutputHelper::hasActiveView($view, 'list'))));
        // $actions->addUI('view_single', HALOUIBuilder::getInstance('', 'content', array('title' => '',
            // 'tooltip' => __halotext('Single view mode'),
            // 'onClick' => "halo.util.setViewMode('$view','single')",
            // 'icon' => 'list-alt',
            // 'class' => 'halo-btn halo-btn-default halo-btn-nobg' . HALOOutputHelper::hasActiveView($view, 'single'))));
    }
    /**
     * decode url
     * 
     * @param  string $str
     * @return [type]      [description]
     */
    public static function utf8_urldecode($str)
    {
        $str = preg_replace("/%u([0-9a-f]{3,4})/i", "&#x\\1;", urldecode($str));
        return html_entity_decode($str, null, 'UTF-8');
    }

    /**
     * Breaks a string into chunks by splitting at whitespace characters.
     * The length of each returned chunk is as close to the specified length goal as possible,
     * with the caveat that each chunk includes its trailing delimiter.
     * Chunks longer than the goal are guaranteed to not have any inner whitespace.
     *
     * Joining the returned chunks with empty delimiters reconstructs the input string losslessly.
     *
     * Input string must have no null characters (or eventual transformations on output chunks must not care about null characters)
     *
     * <code>
     * self::splitStrByWhitespace( "1234 67890 1234 67890a cd 1234   890 123456789 1234567890a    45678   1 3 5 7 90 ", 10 ) ==
     * array (
     *   0 => '1234 67890 ',  // 11 characters: Perfect split
     *   1 => '1234 ',        //  5 characters: '1234 67890a' was too long
     *   2 => '67890a cd ',   // 10 characters: '67890a cd 1234' was too long
     *   3 => '1234   890 ',  // 11 characters: Perfect split
     *   4 => '123456789 ',   // 10 characters: '123456789 1234567890a' was too long
     *   5 => '1234567890a ', // 12 characters: Too long, but no inner whitespace on which to split
     *   6 => '   45678   ',  // 11 characters: Perfect split
     *   7 => '1 3 5 7 9',    //  9 characters: End of $string
     * );
     * </code>
     *
     * @access private
     *
     * @param string $string The string to split.
     * @param int $goal The desired chunk length.
     * @return array Numeric array of chunks.
     */
    private static function splitStrByWhitespace($string, $goal)
    {
        $chunks = array();

        $string_nullspace = strtr($string, "\r\n\t\v\f ", "\000\000\000\000\000\000");

        while ($goal < strlen($string_nullspace)) {
            $pos = strrpos(substr($string_nullspace, 0, $goal + 1), "\000");

            if (false === $pos) {
                $pos = strpos($string_nullspace, "\000", $goal + 1);
                if (false === $pos) {
                    break;
                }
            }

            $chunks[] = substr($string, 0, $pos + 1);
            $string = substr($string, $pos + 1);
            $string_nullspace = substr($string_nullspace, $pos + 1);
        }

        if ($string) {
            $chunks[] = $string;
        }

        return $chunks;
    }

    /**
     * Callback to convert URL match to HTML A element.
     *
     * Regex callback for {@link
     * parseUrl()}.
     *
     * @access private
     *
     * @param array $matches Single Regex Match.
     * @return string HTML A element with URL address.
     */
    private static function makeWebFtpClickableCb($matches)
    {
        $ret = '';
        $dest = $matches[2];
        $dest = 'http://' . $dest;
        if (empty($dest)) {
            return $matches[0];
        }

        // removed trailing [.,;:)] from URL
        if (in_array(substr($dest, -1), array('.', ',', ';', ':', ')')) === true) {
            $ret = substr($dest, -1);
            $dest = substr($dest, 0, strlen($dest) - 1);
        }
        return $matches[1] . "<a target=\"_blank\" href=\"$dest\" rel=\"nofollow\">$dest</a>$ret";
    }

    /**
     * Callback to convert email address match to HTML A element.
     *
     * Regex callback for {@link
     * parseUrl()}.
     *
     * @access private
     *
     * @param array $matches Single Regex Match.
     * @return string HTML A element with email address.
     */
    private static function makeEmailClickableCb($matches)
    {
        $email = $matches[2] . '@' . $matches[3];
        return $matches[1] . "<a href=\"mailto:$email\">$email</a>";
    }

    /**
     * Callback to convert URI match to HTML A element.
     *
     * Regex callback for {@link
     * parseUrl()}.
     *
     * @access private
     *
     * @param array $matches Single Regex Match.
     * @return string HTML A element with URI address.
     */
    private static function makeUrlClickableCb($matches)
    {
        $url = $matches[2];

        if (')' == $matches[3] && strpos($url, '(')) {
            // If the trailing character is a closing parethesis, and the URL has an opening parenthesis in it, add the closing parenthesis to the URL.
            // Then we can let the parenthesis balancer do its thing below.
            $url .= $matches[3];
            $suffix = '';
        } else {
            $suffix = $matches[3];
        }

        // Include parentheses in the URL only if paired
        while (substr_count($url, '(') < substr_count($url, ')')) {
            $suffix = strrchr($url, ')') . $suffix;
            $url = substr($url, 0, strrpos($url, ')'));
        }

        return $matches[1] . '<a target="_blank" href="' . self::getExternalUrl($url) . '" rel="nofollow">' . $url . '</a>' . $suffix;
    }

    /**
     * Convert plaintext URI to HTML links.
     *
     * Converts URI, www and ftp, and email addresses. Finishes by fixing links
     * within links.
     *
     *
     * @param string $text Content to convert URIs.
     * @return string Content with converted URIs.
     */
    public static function parseUrl($text)
    {
        $r = '';
        $textarr = preg_split('/(<[^<>]+>)/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);// split out HTML tags
        $nested_code_pre = 0;// Keep track of how many levels link is nested inside <pre> or <code>
        foreach ($textarr as $piece) {

            if (preg_match('|^<code[\s>]|i', $piece) || preg_match('|^<pre[\s>]|i', $piece)) {
                $nested_code_pre++;
            } elseif (('</code>' === strtolower($piece) || '</pre>' === strtolower($piece)) && $nested_code_pre) {
                $nested_code_pre--;
            }

            if ($nested_code_pre || empty($piece) || ($piece[0] === '<' && !preg_match('|^<\s*[\w]{1,20}+://|', $piece))) {
                $r .= $piece;
                continue;
            }

            // Long strings might contain expensive edge cases ...
            if (10000 < strlen($piece)) {
                // ... break it up
                foreach (self::splitStrByWhitespace($piece, 2100) as $chunk) {
                    // 2100: Extra room for scheme and leading and trailing paretheses
                    if (2101 < strlen($chunk)) {
                        $r .= $chunk;// Too big, no whitespace: bail.
                    } else {
                        $r .= self::parseUrl($chunk);
                    }
                }
            } else {
                $ret = " $piece ";// Pad with whitespace to simplify the regexes

                $url_clickable = '~
				([\\s(<.,;:!?])                                        # 1: Leading whitespace, or punctuation
				(                                                      # 2: URL
					[\\w]{1,20}+://                                # Scheme and hier-part prefix
					(?=\S{1,2000}\s)                               # Limit to URLs less than about 2000 characters long
					[\\w\\x80-\\xff#%\\~/@\\[\\]*(+=&$-]*+         # Non-punctuation URL character
					(?:                                            # Unroll the Loop: Only allow puctuation URL character if followed by a non-punctuation URL character
						[\'.,;:!?)]                            # Punctuation URL character
						[\\w\\x80-\\xff#%\\~/@\\[\\]*(+=&$-]++ # Non-punctuation URL character
					)*
				)
				(\)?)                                                  # 3: Trailing closing parenthesis (for parethesis balancing post processing)
			~xS';// The regex is a non-anchored pattern and does not have a single fixed starting character.
                // Tell PCRE to spend more time optimizing since, when used on a page load, it will probably be used several times.

                $ret = preg_replace_callback($url_clickable, 'HALOOutputHelper::makeUrlClickableCb', $ret);

                $ret = preg_replace_callback('#([\s>])((www|ftp)\.[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]+)#is', 'HALOOutputHelper::makeWebFtpClickableCb', $ret);
                $ret = preg_replace_callback('#([\s>])([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})#i', 'HALOOutputHelper::makeEmailClickableCb', $ret);

                $ret = substr($ret, 1, -1);// Remove our whitespace padding.
                $r .= $ret;
            }
        }

        // Cleanup of accidental links within links
        $r = preg_replace('#(<a([ \r\n\t]+[^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i', "$1$3</a>", $r);
        return $r;
    }
	
	/*
		transform my short info text
	*/
	public static function transformMyShortInfoText($text) {
		$pattern = '/^(\d+)(.*)/';
		if(preg_match($pattern, $text, $matches)) {
			$count = intval($matches[1]);
			if($count < 20) {
				$badgeColor = 'badge-primary';
			} else if($count < 50) {
				$badgeColor = 'badge-success';
			} else {
				$badgeColor = 'badge-danger';
			}
			return __halotext('My') . ' ' . $matches[2];// . ' <span class="badge ' . $badgeColor . '">' . $matches[1] . '</span>';
		}
		return __halotext('My') . ' ' . $text;
	}
	
	/*
		transform array to html5 data string
	*/
	public static function getHtmlData($data, $key=null) {
        $dataArr = array();
        if (!empty($data)) {
            $data = !is_array($data) ? ((array) $data) : $data;
            foreach ($data as $name => $val) {
                if ($key == $name || $key === null) {
                    //name must be converted form camelCase to snake-case format
                    $dataArr[] = 'data-' . snake_case($name, '-') . '="' . htmlspecialchars($val) . '"';
                }
            }
        }

        return implode(' ', $dataArr);	
	}
	
	/*
		output an redirect javascript command
	*/
	public static function redirect($url) {
		static $sent;
		if(!$sent) {
			echo "<script> location.href = '" . $url . "';</script>";
			$sent = true;
		}
	}
}
