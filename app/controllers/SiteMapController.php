<?php
use Illuminate\Routing\Controller;
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

class SiteMapController extends Controller
{
    /**
     * get SiteMap
     *
     * @return string
     */
    public function getSiteMap()
    {
        // create new sitemap object
        $sitemap = App::make("sitemap");

        // set cache (key (string), duration in minutes (Carbon|Datetime|int), turn on/off (boolean))
        // by default cache is disabled
        //$sitemap->setCache('laravel.sitemap', 2 * 60);

        // check if there is cached sitemap and build new only if is not
        //if (!$sitemap->isCached())

        // add home page
        //2014-09-01T08:00:00+00:00
        $sitemap->add(URL::to('/'), date("Y-m-dTH:i:s"), '1.0', 'hourly',
            array(
                array(
                    'url' => 'http://www.halo.vn/assets/images/logo.png',
                    'caption' => 'HALO - Rao vặt miễn phí',
                ),
                array(
                    'url' => 'http://www.halo.vn/assets/images/logo_dark.png',
                    'caption' => 'HALO - Tìm là thấy',
                )
            ), // images array() (url|caption)
            'HALO - Tìm là thấy', // title
            null
            /*array(
        array(
        'url' => 'http://www.halo.vn/',
        'language' => 'vi'
        ),
        array(
        'url' => 'http://www.halo.vn/',
        'language' => 'en'
        )
        ) // language translations*/
        );

        //add home page 2
        /*$sitemap->add(URL::to('home'), date("Y-m-dTH:i:s"), '1.0', 'daily',
        array(
        array(
        'url' => 'http://www.halo.vn/assets/images/logo.png',
        'caption' => 'HALO - Rao vặt miễn phí'
        ),
        array(
        'url' => 'http://www.halo.vn/assets/images/logo_dark.png',
        'caption' => 'HALO - Tìm là thấy'
        )
        ), // images array() (url|caption)
        'HALO - Tìm là thấy', // title
        null
        );*/

        // get all posts from db

        HALOPostModel::where('published', 1)->orderBy('updated_at')->chunk(200, function ($posts) use ($sitemap) {

            // add every post to the sitemap
            foreach ($posts as $post) {
                //$postURL = URL::to('/') . '/post/show/' . $post->id . '/' . $post->slug;
								$postURL = $post->getUrl();

                //$media = new stdClass();
                $photo_ids = $post->getParams('photo_ids', '');
                $photoDescs = array();
                $postTitle = $this->xml_entities($post->title);

                if (!empty($photo_ids)) {
                    $photos = HALOPhotoModel::find(explode(',', $photo_ids));

                    foreach ($photos as $photo) {
                        $photoURL = $this->xml_entities($photo->getPhotoURL());
                        if ($photoURL) {
                            $photoDescs[] = array(
                                'url' => $photoURL,
                                'caption' => $postTitle,
                            );
                        }
                    }
                }

                $sitemap->add($postURL, $post->updated_at, /*$post->priority*/'1.0', /*$post->freq*/'daily',
                    $photoDescs, // images array() (url|caption)
                    $postTitle, // title
                    null
                );
            }
        });

        //$posts = HALOPostModel::where('published', 1)->orderBy('updated_at')->get();

        $groups = HALOGroupModel::where('published', 1)->orderBy('created_at')->get();

        foreach ($groups as $group) {
            //$groupURL = URL::to('/') . '/group/show/' . $group->id . '/' . $group->slug;
						$groupURL = $group->getUrl();

            //$coverPhoto = $group->cover()->get();
            $photoDescs = array();
            $groupName = $this->xml_entities($group->name);

            $photoURL = $this->xml_entities($group->getCover());//$coverPhoto->getPhotoURL();
            if ($photoURL) {
                $photoDescs[] = array(
                    'url' => $photoURL,
                    'caption' => $groupName,
                );
            }

            $sitemap->add($groupURL, $group->updated_at, 1, 'daily',
                $photoDescs, // images array() (url|caption)
                $groupName, // title
                null
            );
        }

        $shops = HALOShopModel::where('published', 1)->orderBy('created_at')->get();

        foreach ($shops as $shop) {
            //$shopURL = URL::to('/') . '/shop/show/' . $shop->id . '/' . $shop->slug;
            $shopURL = $shopt->getUrl();

            //$coverPhoto = $shop->cover()->get();
            $photoDescs = array();
            $shopName = $this->xml_entities($shop->getTitle());

            $photoURL = $shop->getCover();//$coverPhoto->getPhotoURL();
            if ($photoURL) {
                $photoDescs[] = array(
                    'url' => htmlentities($photoURL),
                    'caption' => $shopName,
                );
            }

            $sitemap->add($shopURL, $shop->updated_at, 1, 'daily',
                $photoDescs, // images array() (url|caption)
                $shopName, // title
                null
            );
        }

        // show your sitemap (options: 'xml' (default), 'html', 'txt', 'ror-rss', 'ror-rdf')
        return $sitemap->render('xml');
    }
    /**
     * [xml_entities description]
     *
     * @param  string $string
     * @return string
     */
    private function xml_entities($string)
    {
        return strtr(
            $string,
            array(
                "<" => "&lt;",
                ">" => "&gt;",
                '"' => "&quot;",
                "'" => "&apos;",
                "&" => "&amp;",
            )
        );
    }
}
