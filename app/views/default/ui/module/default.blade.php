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
{{-- Favicons ================================================== --}}
<link rel="apple-touch-icon" sizes="57x57" href="{{{ HALOAssetHelper::getAssetUrl('assets/ico/apple-touch-icon-57x57.png') }}}">
<link rel="apple-touch-icon" sizes="114x114" href="{{{ HALOAssetHelper::getAssetUrl('assets/ico/apple-touch-icon-114x114.png') }}}">
<link rel="apple-touch-icon" sizes="72x72" href="{{{ HALOAssetHelper::getAssetUrl('assets/ico/apple-touch-icon-72x72.png') }}}">
<link rel="apple-touch-icon" sizes="144x144" href="{{{ HALOAssetHelper::getAssetUrl('assets/ico/apple-touch-icon-144x144.png') }}}">
<link rel="apple-touch-icon" sizes="60x60" href="{{{ HALOAssetHelper::getAssetUrl('assets/ico/apple-touch-icon-60x60.png') }}}">
<link rel="apple-touch-icon" sizes="120x120" href="{{{ HALOAssetHelper::getAssetUrl('assets/ico/apple-touch-icon-120x120.png') }}}">
<link rel="apple-touch-icon" sizes="76x76" href="{{{ HALOAssetHelper::getAssetUrl('assets/ico/apple-touch-icon-76x76.png') }}}">
<link rel="apple-touch-icon" sizes="152x152" href="{{{ HALOAssetHelper::getAssetUrl('assets/ico/apple-touch-icon-152x152.png') }}}">
<link rel="icon" type="image/png" href="{{{ HALOAssetHelper::getAssetUrl('assets/ico/favicon-196x196.png') }}}" sizes="196x196">
<link rel="icon" type="image/png" href="{{{ HALOAssetHelper::getAssetUrl('assets/ico/favicon-160x160.png') }}}" sizes="160x160">
<link rel="icon" type="image/png" href="{{{ HALOAssetHelper::getAssetUrl('assets/ico/favicon-96x96.png') }}}" sizes="96x96">
<link rel="icon" type="image/png" href="{{{ HALOAssetHelper::getAssetUrl('assets/ico/favicon-16x16.png') }}}" sizes="16x16">
<link rel="icon" type="image/png" href="{{{ HALOAssetHelper::getAssetUrl('assets/ico/favicon-32x32.png') }}}" sizes="32x32">
<meta name="msapplication-TileColor" content="#1e8cbe">
<meta name="msapplication-TileImage" content="{{{ HALOAssetHelper::getAssetUrl('assets/ico/mstile-144x144.png') }}}">

<meta http-equiv="content-language" content="en" />
<link rel="alternate" href="{{Request::url()}}" hreflang="en-us" />
<link rel="alternate" href="{{Request::url()}}" hreflang="x-default" />

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
