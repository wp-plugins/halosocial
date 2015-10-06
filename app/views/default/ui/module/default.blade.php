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
?>

{{-- ///////////////////////////////////// Member module UI ///////////////////////////////////// --}}
@beginUI('module.member')
<div class="panel panel-default halo-module-member">
	<div class="panel-heading">
		<h3 class="panel-title">{{__halotext('HALO Members')}}</h3>
	</div>
	<div class="panel-body">
		<div class="halo-app-box-content">
			<div class="row">
				<?php $users = HALOUserModel::queryUsers(array('limit' => 12, 'orderBy' => 'created_at', 'orderDir' => 'desc'));
				foreach ($users as $haloUser) {
					?>
					<div class="col-md-3 col-sm-6 col-xs-6"><a href="{{$haloUser->getUrl()}}"
						{{$haloUser->getBriefDataAttribute()}} class="halo-user-{{$haloUser->id}} thumbnail">
						<img class="img-responsive"
						     src="{{$haloUser->getThumb(HALO_PHOTO_SMALL_SIZE)}}"
						     alt="{{{$haloUser->getDisplayName()}}}"> </a> </div>
				<?php
				}
				?>
			</div>
		</div>
		<div class="halo-app-box-footer">
			<a href="{{URL::to('/home?usec=member')}}">{{__halotext('View All Members')}}</a>
		</div>
	</div>
</div>
@endUI

{{-- ///////////////////////////////////// Head module UI ///////////////////////////////////// --}}
@beginUI('module.head')
{{-- Mobile Specific Metas ================================================== --}}
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" id="viewport" content="width=device-width, height=device-height, user-scalable=no, initial-scale=1, maximum-scale=1" />
<meta name="msapplication-tap-highlight" content="no" />

{{-- ajax cross site filter    ================================================== --}}
<meta name="csrf_token" content="<?= csrf_token() ?>">

{{-- HTML5 shim, for IE6-8 support of HTML5 elements --}}
<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
{{HALOAssetHelper::printInlineScript()}}
<script>
	var halo_my_id = "{{ UserModel::getCurrentUserId() }}";
	var halo_feature_message = parseInt("{{ HALOAuth::can('feature.message') & HALOConfig::get('message.enable', 1) }}");
	var halo_popup_message_win = parseInt("{{ HALOAuth::can('feature.message') & HALOConfig::get('message.showpopup', 1) }}");
	var halo_feature_push = parseInt("{{ HALOAuth::can('feature.push') & HALOConfig::get('pushserver.enable', 0) }}"); 
	var halo_socket_address = "{{ HALOConfig::get('pushserver.address') }}";
</script>
@endUI

{{-- ///////////////////////////////////// Footer module UI ///////////////////////////////////// --}}
@beginUI('module.footer')
<div class="col-xs-12 col-sm-12 hidden-xs">
	<div class="row">
		<div class="col-xs-3">
			<p>
				<b>{{__halotext('Explore footer')}}</b>
				<br/>
				<a href="{{URL::to('/')}}">{{__halotext('Search by location footer')}}</a>
				<br/>
				<a href="{{URL::to('?view=home&usec=category')}}">{{__halotext('Show all categories footer')}}</a>
				<br/>
				<a href="{{URL::to('?view=home&usec=member')}}">{{__halotext('Show all members footer')}}</a>
			</p>
		</div>
		<div class="col-xs-3">
			<p>
				<b>{{__halotext('Guidline and tips footer')}}</b>
				<br/>
				<a href="{{URL::to('?view=content&task=aboutus')}}">{{__halotext('About us footer')}}</a>
				<br/>
				<a href="{{URL::to('?view=content&task=guidline')}}">{{__halotext('Guidline footer')}}</a>
			</p>
		</div>
		<div class="col-xs-3">
			<p>
				<b>{{__halotext('Policy and regular footer')}}</b>
				<br/>
				<a href="{{URL::to('?view=content&task=term')}}">{{__halotext('Term and policy footer')}}</a>
			</p>
		</div>
		<div class="col-xs-3">
			<p>
				<b>{{__halotext('Connecting footer')}}</b>
				<br/>
				<a href="https://www.facebook.com/halovietnam">{{__halotext('Facebook fanpage footer')}}</a>
				<br/>
				<a href="{{URL::to('?view=content&task=contact')}}">{{__halotext('Contact footer')}}</a>
			</p>		
		</div>
	</div>
</div>
<div class="col-xs-12 col-sm-12">
	<div class="row">
		<div class="col-xs-9">
			<div>
				<p>&copy; {{Carbon::today()->year}} - {{__halotext('_COMPANY_COPYRIGHT_')}}<br/>
				{{__halotext('_COMPANY_CERTIFICATION_')}}<br/>
				{{__halotext('_COMPANY_ADDRESS_')}}<br/>
				{{__halotext('_COMPANY_OFFICE_')}}<br/>
				{{__halotext('_COMPANY_CONTACT_')}}</p>
			</div>
		</div>
	</div>
</div>
@endUI

{{-- ///////////////////////////////////// metatag head UI ///////////////////////////////////// --}}
@beginUI('module.metatag')
<?php $target = isset($builder->target)?$builder->target:null;?>
<meta property="og:title" content="{{HALOOutputHelper::getMetaTags('title', $target)}}"/>
<meta property="og:type" content="website"/>
<meta property="og:image" content="{{HALOOutputHelper::getMetaTags('cover', $target)}}"/>
<meta property="og:description" content="{{HALOOutputHelper::getMetaTags('description', $target)}}"/>
<meta name="description" content="{{HALOOutputHelper::getMetaTags('description', $target)}}"/>
<meta name="keywords" content="{{HALOOutputHelper::getMetaTags('keywords', $target)}}"/>
@endUI

{{-- ///////////////////////////////////// Footer module UI ///////////////////////////////////// --}}
@beginUI('module.scrollTop')
<div class="halo-scroll-top-wrapper" style="display:none;position: fixed; top: 90%; left: 75%;">
<a style="display: block;" class="halo-scroll-top halo-btn halo-btn-primary" href="javascript:void(0)">
	<span><i class="fa fa-arrow-circle-up fa-lg"></i></span>
</a>
</div>
@endUI


{{-- ///////////////////////////////////// Footer module UI ///////////////////////////////////// --}}
@beginUI('module.molt')
	<?php $posts = $builder->post->getMoreLikeThis(); ?>
	@if (!empty($posts) && $posts->count())
	<div class="halo-post-similar-wrapper panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">{{__halotext('More like this post')}}</h3>
		</div>
		<div class="panel-body">
			<ul class="halo-post-similar">
				@foreach ($posts as $key => $post)
				<li class="clearfix">
					<div class="halo-post-thumb">
						<a class="halo-post-media-cover" href="{{$post->getUrl()}}">
							<img class="img-responsive" src="{{$post->getCover(HALO_PHOTO_SMALLCOVER_SIZE)}}" alt="{{ $post->getDisplayName() }}"/>
						</a>
					</div>
					<a class="halo-post-title" href="{{$post->getUrl()}}"><h4>{{$post->title}}</h4></a>
					<span class="text-primary halo-post-price-val">{{$post->getPriceField()}}</span>
					<a><span class="halo-post-category"> - {{$post->category->getDisplayLink()}}</span></a>
					{{{HALOUtilHelper::getDiffForHumansTrans($post->created_at->diffForHumans())}}}
					@if ($post->location)
					<span class="halo-post-location">
                    {{__halotext('at')}} {{$post->location->getDisplayLink('', array('post-id' => $post->id))}}
                	</span>
                	@endif
				</li>
				@endforeach
			</ul>
		</div>
	</div>
	@endif
@endUI

{{-- ///////////////////////////////////// Pending action module UI ///////////////////////////////////// --}}
@beginUI('module.pending')
<div class="halo-module-pending panel panel-default" {{$builder->getZone(null,null,'halo-module-pending')}}>
	@if($builder->count)
	<div class="halo-module-pending-header panel-heading">
		<i class="fa fa-info-circle text-success"></i> <a class="text-success" data-toggle="collapse" href="#notif">{{__halontext('You have %s pending action','You have %s pending actions',$builder->count)}}</a>
	</div>
	<div id="notif" class="halo-module-pending-content collapse">
		<ul class="halo-pending-actions-list haloc-list list-unstyled">
			@foreach ($builder->actions as $key => $action)
			<li class="clearfix">
				<i class="fa fa-caret-right"></i> <a class="halo-pending-action" href="{{$action->link}}">{{$action->title}}</a>
			</li>
			@endforeach
		</ul>
	</div>
	@endif
</div>
@endUI

{{-- ///////////////////////////////////// Getting Started module UI ///////////////////////////////////// --}}
@beginUI('module.halo_pricing')
<?php
$jsonStr = '[{"name":"Stream","description":"Our powerful stream lets your members share anything! From status updates to pictures, and from hosted videos to moods. Members can even set the privacy level that suits them, allowing friends and followers to comment, like, dislike, share or report.","ver":{"free":1,"starter":1,"agency":1}},{"name":"Profiles","description":"Profiles are the core of HaloSocial. Members can upload avatars and covers, post a status update on their streams, and manage their friends, groups, events, and more.","ver":{"free":1,"starter":1,"agency":1}},{"name":"Avatars & Covers","description":"Members can upload an avatar and a cover photo and reposition the cover photo.","ver":{"free":1,"starter":1,"agency":1}},{"name":"Photo albums","description":"Members can create photo albums to be displayed on their profile or on the strea.","ver":{"free":1,"starter":1,"agency":1}},{"name":"Template overrides","description":"Make HaloSocial look the way you want it with overrides. When you upgrade, the changes stay intact.","ver":{"free":1,"starter":1,"agency":1}},{"name":"Responsive","description":"HaloSocial looks great on any device.","ver":{"free":1,"starter":1,"agency":1}},{"name":"Dynamic SEO","description":"HaloSocial was created with SEO in mind. Each page generates a search engine-friendly URL that makes it easy for search engines to find you.","ver":{"free":1,"starter":1,"agency":1}},{"name":"Hover cards","description":"When hovering over a member’s name or avatr, a card with information about them is displayed.","ver":{"free":1,"starter":1,"agency":1}},{"name":"Custom profiles","description":"Custom profiles allow you to create fields of any kind on profiles, groups, events, pages, and shops.","ver":{"free":0,"starter":1,"agency":1}},{"name":"Chat","description":"Your community members want to connect. Chat lets them communicate directly with each other in real time, cementing real relationships.","ver":{"free":0,"starter":1,"agency":1}},{"name":"Friends","description":"Community sites aim to turn members into friends. The Friends feature lets members upgrade their relationships and limit their content to a close circle.","ver":{"free":0,"starter":1,"agency":1}},{"name":"Follow","description":"Members can keep track of the activity of other people on the site even before they’re approved as friends.","ver":{"free":0,"starter":1,"agency":1}},{"name":"Events","description":"Real-life events bring people together. Members can set a time, choose a place and send out the invitations.","ver":{"free":0,"starter":1,"agency":1}},{"name":"Groups","description":"Groups let people discuss a shared interest, whether it’s a location, a cause or anything else. Members can post status updates, photos and videos that others can like, dislike, share and comment.","ver":{"free":0,"starter":1,"agency":1}},{"name":"Widgets","description":"Show members information about the latest groups, upcoming events, pages and shops.","ver":{"free":0,"starter":1,"agency":1}},{"name":"Blogs","description":"The Blogs feature lets you show the latest blog posts right inside your community.","ver":{"free":0,"starter":0,"agency":1}},{"name":"Pages","description":"Anyone can create a page, follow other pages, invite or add friends, share and post in the stream.","ver":{"free":0,"starter":0,"agency":1}},{"name":"Classifieds","description":"Members can post classified ads and share them with their community easily. They get fast sales. You get the gratitude.","ver":{"free":0,"starter":0,"agency":1}},{"name":"Meta tags","description":"Create custom meta tags for your HaloSocial pages.","ver":{"free":0,"starter":0,"agency":1}},{"name":"Real-time notifications","description":"Notifications are addictive. When notifications show in real time, your members stay engaged — and stay on the site longer.","ver":{"free":0,"starter":0,"agency":1}},{"name":"Labels","description":"Add eye-catching labels to your content! Tell members that a post is “New”, “Special”, “Exclusive” or anything else. Assign labels to events, profiles, groups, pages, and more. You can even give them an expiration time.","ver":{"free":0,"starter":0,"agency":1}},{"name":"ACL","description":"Our ACL lets you set the permissions for your community. Decide who gets to create, delete and moderate, as well as ban, feature and more.","ver":{"free":0,"starter":0,"agency":1}}]';
$features = json_decode($jsonStr);
?>
<div {{ $builder->getZone() }}>
    <div class="panel panel-default">
    <!-- <div class="panel-heading"><h4>{{ __halotext('Pricing') }}</h4></div> -->
    <div class="panel-body">
        <table class="table table-condensed table-hover">
        <thead class="halo-pricing-head">
            <tr class="panel-heading halo-version-title">
                <th width="40%"></th>
                <th width="20%"><h4>Starter</h4></th>
                <th width="20%"><h4>Professional</h4></th>
                <th width="20%"><h4>Agency</h4></th>
            </tr>
            <tr class="halo-pricing-title">
                <td></td>
                <td>{{ __halotext('Free') }}</td>
                <td>$99</td>
                <td>$149</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td><a href="{{URL::to('http://www.halo.social/pricing/')}}" class="halo-btn-warning">{{ __halotext('Buy') }}</a></td>
                <td><a href="{{URL::to('http://www.halo.social/pricing/')}}" class="halo-btn-warning">{{ __halotext('Buy') }}</a></td>
            </tr>
        </thead>
        <tbody class="halo-pricing-body">
            @foreach ($features as $each)
            <tr>
                <td>{{__halotext($each->name)}} <span class="halo-pricing-tip" data-toggle="tooltip" data-trigger="hover" title="{{__halotext($each->description)}}">{{ HALOUIBuilder::icon('question') }}</span></td>
                <td><span>{{ $each->ver->free ? HALOUIBuilder::icon('check fa-lg') : HALOUIBuilder::icon('close fa-lg') }}</span></td>
                <td><span>{{ $each->ver->starter ? HALOUIBuilder::icon('check fa-lg') : HALOUIBuilder::icon('close fa-lg') }}</span></td>
                <td><span>{{ $each->ver->agency ? HALOUIBuilder::icon('check fa-lg') : HALOUIBuilder::icon('close fa-lg') }}</span></td>
            </tr>
            @endforeach
        </tbody>
        <tfoot class="halo-pricing-footer">
        	<tr>
                <td></td>
                <td></td>
                <td><a href="{{URL::to('http://www.halo.social/pricing/')}}" class="halo-btn-warning">{{__halotext('Buy')}}</a></td>
                <td><a href="{{URL::to('http://www.halo.social/pricing/')}}" class="halo-btn-warning">{{__halotext('Buy')}}</a></td>
            </tr>
        </tfoot>
    </table>
    </div>
</div>
</div>
@endUI

{{-- ///////////////////////////////////// Getting Started module UI ///////////////////////////////////// --}}
@beginUI('module.halo_getstarted')
<?php
$videos = array(
    array('title' => 'Installing HaloSocial', 'id' => 'U__4TI8UzLw'),
    array('title' => 'Uninstalling HaloSocial', 'id' => 'GcztIWYI-VQ'),
    array('title' => 'Upgrading HaloSocial', 'id' => '3ml3TijApGE')
);
?>
<div {{ $builder->getZone() }}>
    <div class="panel panel-default">
    <div class="panel-heading"><h4>{{ __halotext('Getting started with HaloSocial') }}</h4></div>
    <div class="panel-body">
        <div class="row">
            @foreach ($videos as $each)
            <div class="halo-welcome-video-item col-md-12">
                <div class="halo-video-title">
                    <h4>{{ __halotext($each['title']) }}</h4>
                </div>
                <div class="halo-video-iframe">
                    <iframe src="https://www.youtube.com/embed/{{ $each['id'] }}" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
            @endforeach
            <div class="halo-welcome-video-item col-md-12">
                <div class="halo-welcome-video-more text-center">
                {{ sprintf(__halotext('For more video tutorials, visit %s'), '<a href="http://www.halo.social/videos/" target="_blank">halo.social/videos</a>') }}
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endUI

@beginUI('module.halo_newslettersub')
<?php
if ($my = HALOUserModel::getUser()) {
    $user = $my->user;
    $firstName = $user->getFirstName();
    $lastName = $user->getLastName();
    $email = $user->getEmail();
} else {
    $firstName = $lastName = $email = '';
}
?>
<div {{ $builder->getZone() }}>
     <div class="panel panel-default">
            <div class="panel-body">
                <div class="halo-welcome-mailchimp-wrapper">
                    {{-- Form Subscriber --}}
                    <div class="halo-welcome-mc-img">
                        <div class="">
                            <img src="{{ HALOAssetHelper::getAssetUrl('assets/images/newsletter.jpg') }}" alt=""/>
                        </div>
                    </div>
                    <div class="halo-welcome-mc-form">
                        <div class="halo-mailchimp-title">Top 10 mistakes when creating an online community and how to avoid them</div>
                        <div class="halo-mailchimp-desc">A lot can go wrong when starting an online community. Don't make the mistakes other make. In this special report, we explain what are the top mistakes and how to avoid them in order to create a hugely successful online community.</div>
                        {{ HALOUIBuilder::getInstance('form', 'form.form', array('name'=>'popupForm', 'class' => 'row halo-mailchimp-form', 'onsubmit' => 'halo.form.subscribeNews(); return false;'))
                            ->addUI('first_name', HALOUIBuilder::getInstance('', 'form.text', array('name' => 'first_name', 'id' => 'first_name', 'value' => $firstName,
                                'placeholder' => __halotext('First Name'), 'validation' => 'required',
                                'class' => 'col-md-3',
                                'data' => array('confirm' => '')
                            )))
                            ->addUI('last_name', HALOUIBuilder::getInstance('', 'form.text', array('name' => 'last_name', 'id' => 'last_name', 'value' => $lastName,
                                'placeholder' => __halotext('Last Name'), 'validation' => 'required',
                                'class' => 'col-md-3',
                                'data' => array('confirm' => '')
                            )))
                            ->addUI('email', HALOUIBuilder::getInstance('', 'form.text', array('name' => 'email', 'id' => 'email', 'value' => $email,
                                'placeholder' => __halotext('Email'), 'validation' => 'email|required',
                                'class' => 'col-md-3',
                                'data' => array('confirm' => '')
                            )))
                            ->addUI('submit', HALOUIBuilder::getInstance('div', 'div_wrapper', array(
                                'class' => 'col-md-3 form-group',
                                'html' => HALOUIBuilder::getInstance('', 'form.submit', array(
                                    'name' => 'submit',
                                    'value' => __halotext('Send me the report'), 
                                    'validation' => 'required',
                                    'class' => 'halo-btn halo-btn-danger halo-btn-subscribe'
                                ))->fetch()
                            )))->fetch() }}
                    </div>
                    {{-- End Form Subscriber --}}
                </div>
            </div>
        </div>
</div>
@endUI