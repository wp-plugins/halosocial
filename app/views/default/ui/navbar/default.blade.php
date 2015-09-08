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

{{-- ///////////////////////////////////// Top bar UI ///////////////////////////////////// --}}
@beginUI('navbar.top_bar')
<div class="container-fluid">
	<div class="navbar-header">
		<div class="navbar-header-wrapper">
			@if($builder->hasChild('title'))
			<a class="navbar-brand halo-pull-left" href="{{ $builder->getChild('title')->getUrl() }}">
                @if ($builder->getChild('title')->logo) 
                    <img src="{{ $builder->getChild('title')->logo }}"/>
                @endif
                @if ($builder->getChild('title')->version) 
                <span class="halo-menu-version">v{{ $builder->getChild('title')->version }}</span>
                @endif
                {{$builder->getIcon()}}
				{{$builder->getChild('title')->title}}</a>
			@endif
			@if($builder->hasChild('mobile_nav@array'))
			<ul class="navbar-center">
			@foreach($builder->getChild('mobile_nav@array') as $li)
		    @if($li->hasChild('dropdown@array'))
		    <li class="halo-dropdown {{ (Request::is($li->url) ? ' active' : '') }}">
			    <a href="#" class="halo-dropdown-toggle" data-htoggle="dropdown">{{ $li->title }}<b class="caret"></b></a>
			    <ul class="halo-dropdown-menu" role="menu">
				    @foreach($li->getChild('dropdown@array') as $sub_li)
				    <li><a href="{{{ $sub_li->getUrl() }}}" {{ $sub_li->getClass() }} {{ $sub_li->getOnClick() }} >{{
					    $sub_li->title }}</a></li>
				    @endforeach
			    </ul>
		    </li>
		    @elseif(isset($li->html) && $li->html)
		    <li>{{$li->title}}</li>
		    @else
		    <li
		    {{ (Request::is($li->url) ? ' class="active"' : '') }}><a href="{{{ $li->getUrl() }}}" {{ $li->getClass() }}
		    {{ $li->getOnClick() }} >{{$li->title}}</a></li>
		    @endif
			@endforeach
			</ul>
			@endif
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".halo-top-bar">
				<span class="sr-only">{{__halotext('Toggle Navigation')}}</span>
				<i class="fa fa-bars fa-2x"></i>
			</button>
		</div>
	</div>
    <div class="collapse navbar-collapse halo-top-bar">
	    {{-- Left Nav Section --}}
	    @if ($builder->hasChild('left_nav@array'))
	    <ul class="halo-nav halo-navbar-nav navbar-left">
            @if ($builder->hasChild('left_nav@array'))
		    @foreach($builder->getChild('left_nav@array') as $li)
		    @if($li->hasChild('dropdown@array'))
		    <li class="halo-dropdown {{ (Request::is($li->url) ? ' active' : '') }}">
			    <a href="#" class="halo-dropdown-toggle" data-htoggle="dropdown">{{ $li->title }}<b class="caret"></b></a>
			    <ul class="halo-dropdown-menu" role="menu">
				    @foreach($li->getChild('dropdown@array') as $sub_li)
				    <li><a href="{{{ $sub_li->getUrl() }}}" {{ $sub_li->getClass() }} {{ $sub_li->getOnClick() }} >{{
					    $sub_li->title }}</a></li>
				    @endforeach
			    </ul>
		    </li>
		    @elseif(isset($li->html) && $li->html)
		    <li>{{$li->title}}</li>
		    @else
		    <li
		    {{ (Request::is($li->url) ? ' class="active"' : '') }}><a href="{{{ $li->getUrl() }}}" {{ $li->getClass() }}
		    {{ $li->getOnClick() }} >{{$li->title}}</a></li>
		    @endif
		    @endforeach
            @endif
	    </ul>
	    @endif
        {{-- Right Nav Section --}}
        @if($builder->hasChild('right_nav@array'))
        <ul class="halo-nav halo-navbar-nav navbar-right">
            @foreach($builder->getChild('right_nav@array') as $li)
            @if($li->hasChild('dropdown@array'))
            <li class="halo-dropdown">
                <a href="#" class="halo-dropdown-toggle" data-htoggle="dropdown">{{{ $li->title }}}<b class="caret"></b></a>
                <ul class="halo-dropdown-menu" role="menu">
                    @foreach($li->getChild('dropdown@array') as $sub_li)
                    <li><a href="{{{ $sub_li->getUrl() }}}" {{ $sub_li->getClass() }} {{ $sub_li->getOnClick() }} >{{
                        $sub_li->title }}</a></li>
                    @endforeach
                </ul>
            </li>
            @elseif(isset($li->html) && $li->html)
            <li>{{$li->title}}</li>
            @else
            <li><a href="{{{ $li->getUrl() }}}" {{ $li->getClass() }} {{ $li->getOnClick() }} >{{$li->title}}</a></li>
            @endif
            @endforeach
        </ul>
        @endif
    </div>
	<!-- /.nav-collapse -->
</div>
<!-- /.container -->
@endUI

{{-- ///////////////////////////////////// Navbar Mobile UI ///////////////////////////////////// --}}
@beginUI('navbar.mobile_bar')

@endUI

{{-- ///////////////////////////////////// Navbar Desktop UI ///////////////////////////////////// --}}
@beginUI('navbar.desktop_bar')
<div class="navbar halo-navbar navbar-static-top navbar-inverse" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <div class="navbar-header-wrapper">
                <button type="button" class="navbar-toggle hidden-sm" data-toggle="offcanvas" data-target="#halo_mobileNavMenu" data-canvas="body">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                @if($builder->hasChild('title'))
                <a class="navbar-brand halo-pull-left hidden-xs" href="{{ $builder->getChild('title')->getUrl() }}">{{$builder->getIcon()}}
									<img src="{{HALOAssetHelper::to('assets/images/logo_light.png')}}" alt="{{$builder->getChild('title')->title}}"/>
                    </a>
                @endif
                {{HALOUIBuilder::getInstance('','navbar.menu_search_box',array())->fetch()}}
                @if ($builder->hasChild('left_nav@array'))
                <ul class="halo-nav halo-navbar-nav navbar-left hidden-xs">
                    @foreach($builder->getChild('left_nav@array') as $li)
                    @if($li->hasChild('dropdown@array'))
                    <li class="halo-dropdown {{ (Request::is($li->url) ? ' active' : '') }}">
                        <a href="#" class="halo-dropdown-toggle" data-htoggle="dropdown">{{ $li->title }}<b class="caret"></b></a>
                        <ul class="halo-dropdown-menu" role="menu">
                            @foreach($li->getChild('dropdown@array') as $sub_li)
                            <li><a href="{{{ $sub_li->getUrl() }}}" {{ $li->getClass() }} {{ $sub_li->getOnClick() }} >{{
                                $sub_li->title }}</a></li>
                            @endforeach
                        </ul>
                    </li>
                    @elseif(isset($li->html) && $li->html)
                    <li>{{$li->title}}</li>
                    @else
                    <li
                    {{ (Request::is($li->url) ? ' class="active"' : '') }}><a href="{{{ $li->getUrl() }}}" {{ $li->getClass() }}
                    {{ $li->getOnClick() }} >{{$li->title}}</a></li>
                    @endif
                    @endforeach
                </ul>
                @endif
                @if ($builder->hasChild('right_nav@array'))
                <ul class="halo-nav halo-navbar-nav navbar-right hidden-xs">
                    @foreach($builder->getChild('right_nav@array') as $li)
                    @if($li->hasChild('dropdown@array'))
                    <li class="halo-dropdown">
                        <a href="#" class="halo-dropdown-toggle" data-htoggle="dropdown">{{{ $li->title }}}<b class="caret"></b></a>
                        <ul class="halo-dropdown-menu" role="menu">
                            @foreach($li->getChild('dropdown@array') as $sub_li)
                            <li><a href="{{{ $sub_li->getUrl() }}}" {{ $li->getClass() }} {{ $sub_li->getOnClick() }} >{{
                                $sub_li->title }}</a></li>
                            @endforeach
                        </ul>
                    </li>
                    @elseif(isset($li->html) && $li->html)
                    <li>{{$li->title}}</li>
                    @else
                    <li class="{{$li->parentClass}}"><a href="{{{ $li->getUrl() }}}" {{ $li->getClass() }} {{ $li->getOnClick() }} >{{$li->title}}</a></li>
                    @endif
                    @endforeach
                </ul>
                @endif
                @if ($builder->hasChild('mobile_nav@array'))
                <ul class="halo-nav halo-navbar-nav navbar-right visible-xs visible-xs-table-cell navbar-mobile" style="padding-right: 20px;">
                    @foreach($builder->getChild('mobile_nav@array') as $li)
                    @if($li->hasChild('dropdown@array'))
                    <li class="halo-dropdown">
                        <a href="#" class="halo-dropdown-toggle" data-htoggle="dropdown">{{{ $li->title }}}<b class="caret"></b></a>
                        <ul class="halo-dropdown-menu" role="menu">
                            @foreach($li->getChild('dropdown@array') as $sub_li)
                            <li><a href="{{{ $sub_li->getUrl() }}}" {{ $li->getClass() }} {{ $sub_li->getOnClick() }} >{{
                                $sub_li->title }}</a></li>
                            @endforeach
                        </ul>
                    </li>
                    @elseif(isset($li->html) && $li->html)
                    <li>{{$li->title}}</li>
                    @else
                    <li {{ $li->getClass($li->parentClass) }}><a href="{{{ $li->getUrl() }}}" {{ $li->getOnClick() }} >{{$li->title}}</a></li>
                    @endif
                    @endforeach
                </ul>
                @endif
            </div>
        </div>
    </div>
</div>
@endUI

{{-- ///////////////////////////////////// Tool bar UI ///////////////////////////////////// --}}
@beginUI('navbar.toolbar')
<div class="container-fluid">
	<div class="navbar-header">
		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".halo-tool-bar"><span
				class="sr-only">{{__halotext('Toggle Navigation')}}</span> <span class="icon-bar"></span> <span
				class="icon-bar"></span> <span class="icon-bar"></span></button>
		@if($builder->hasChild('title'))
		<a class="navbar-brand" href="{{ $builder->getChild('title')->getUrl() }}">{{$builder->getIcon()}}
			{{$builder->getChild('title')->title}}</a>
		@endif
	</div>
	<div class="collapse navbar-collapse halo-tool-bar">
		{{-- Left Nav Section --}}
		@if($builder->hasChild('left_nav@array'))
		<ul class="halo-nav halo-navbar-nav">
			@foreach($builder->getChild('left_nav@array') as $li)
			@if($li->hasChild('dropdown@array'))
			<li class="halo-dropdown {{ (Request::is($li->url) ? ' active' : '') }}">
				<a href="#" class="halo-dropdown-toggle" data-htoggle="dropdown">{{ $li->title }}<b class="caret"></b></a>
				<ul class="halo-dropdown-menu" role="menu">
					@foreach($li->getChild('dropdown@array') as $sub_li)
					<li><a href="{{{ $sub_li->getUrl() }}}" {{ $li->getClass() }} {{ $sub_li->getOnClick() }} >{{
						$sub_li->title }}</a></li>
					@endforeach
				</ul>
			</li>
			@elseif(isset($li->html) && $li->html)
			<li>{{$li->title}}</li>
			@else
			<li
			{{ (Request::is($li->url) ? ' class="active"' : '') }}><a href="{{{ $li->getUrl() }}}" {{ $li->getClass() }}
			{{ $li->getOnClick() }} >{{$li->title}}</a></li>
			@endif
			@endforeach
		</ul>
		@endif
		{{-- Right Nav Section --}}
		@if($builder->hasChild('right_nav@array'))
		<ul class="halo-nav halo-navbar-nav navbar-right">
			@foreach($builder->getChild('right_nav@array') as $li)
			@if($li->hasChild('dropdown@array'))
			<li class="halo-dropdown">
				<a href="#" class="halo-dropdown-toggle" data-htoggle="dropdown">{{ $li->title }}<b class="caret"></b></a>
				<ul class="halo-dropdown-menu" role="menu">
					@foreach($li->getChild('dropdown@array') as $sub_li)
					<li><a href="{{{ $sub_li->getUrl() }}}" {{ $li->getClass() }} {{ $sub_li->getOnClick() }} >{{
						$sub_li->title }}</a></li>
					@endforeach
				</ul>
			</li>
			@elseif(isset($li->html) && $li->html)
			<li>{{$li->title}}</li>
			@else
			<li><a href="{{{ $li->getUrl() }}}" {{ $li->getClass() }} {{ $li->getOnClick() }} >{{$li->title}}</a></li>
			@endif
			@endforeach
		</ul>
		@endif
	</div>
	<!-- /.nav-collapse -->
</div>
<!-- /.container -->
@endUI


{{-- ///////////////////////////////////// Tool bar UI ///////////////////////////////////// --}}
@beginUI('navbar.focusaction')
<div class="halo-focus-actions-contain">
	<div class="navbar-header">
		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".halo-focus-action"><span
				class="sr-only">{{__halotext('Toggle Navigation')}}</span> <span class="icon-bar"></span> <span
				class="icon-bar"></span> <span class="icon-bar"></span></button>
		@if($builder->hasChild('title'))
		<a class="navbar-brand" href="{{ $builder->getChild('title')->getUrl() }}">{{$builder->getIcon()}}
			{{$builder->getChild('title')->title}}</a>
		@endif
	</div>
	<div class="collapse navbar-collapse halo-focus-action">
		{{-- Left Nav Section --}}
		@if($builder->hasChild('left_nav@array'))
		<ul class="halo-nav halo-navbar-nav">
			@foreach($builder->getChild('left_nav@array') as $li)
			@if($li->hasChild('dropdown@array'))
			<li class="halo-dropdown {{ (Request::is($li->url) ? ' active' : '') }}">
				<a href="#" class="halo-dropdown-toggle" data-htoggle="dropdown">{{$li->getIcon()}}{{ $li->title }}<b
						class="caret"></b></a>
				<ul class="halo-dropdown-menu" role="menu">
					@foreach($li->getChild('dropdown@array') as $sub_li)
					<li><a href="{{{ $sub_li->getUrl() }}}" {{ $li->getClass() }} {{ $sub_li->getOnClick() }}
						>{{$sub_li->getIcon()}}{{ $sub_li->title }}</a></li>
					@endforeach
				</ul>
			</li>
			@elseif(isset($li->html) && $li->html)
			<li>{{$li->title}}</li>
			@else
			<li><a href="{{{ $li->getUrl() }}}" {{ $li->getClass() }} {{ $li->getOnClick() }}
				>{{$li->getIcon()}}{{$li->title}}</a></li>
			@endif
			@endforeach
		</ul>
		@endif
		{{-- Right Nav Section --}}
		@if($builder->hasChild('right_nav@array'))
		<ul class="halo-nav halo-navbar-nav navbar-right">
			@foreach($builder->getChild('right_nav@array') as $li)
			@if($li->hasChild('dropdown@array'))
			<li class="halo-dropdown">
				<a href="#" class="halo-dropdown-toggle" data-htoggle="dropdown">{{$li->getIcon()}}{{ $li->title }}<b
						class="caret"></b></a>
				<ul class="halo-dropdown-menu" role="menu">
					@foreach($li->getChild('dropdown@array') as $sub_li)
					<li><a href="{{{ $sub_li->getUrl() }}}" {{ $li->getClass() }} {{ $sub_li->getOnClick() }}
						>{{$sub_li->getIcon()}}{{ $sub_li->title }}</a></li>
					@endforeach
				</ul>
			</li>
			@elseif(isset($li->html) && $li->html)
			<li>{{{$li->title}}}</li>
			@else
			<li><a href="{{ $li->getUrl() }}" {{ $li->getClass() }} {{ $li->getOnClick() }} >{{ $li->getIcon()
				}}{{$li->title}}</a></li>
			@endif
			@endforeach
		</ul>
		@endif
	</div>
	<!-- /.nav-collapse -->
</div>
<!-- /.container -->
@endUI


{{-- ///////////////////////////////////// Navigation navbar UI ///////////////////////////////////// --}}
@beginUI('navbar.navigation')
<?php
	$siteInfo = Cache::remember('siteInfoCache', 60, function(){ return HALOConfigModel::getSiteShortInfo();});
	$view = Input::get('view', '');
	$active = Input::get('usec',$view);
	if($active == 'home' || $active == '') $active = 'stream';
	$maxExpandMenu = HALOConfig::get('global.maxNavLinks', 4);
	$menuCount = count($siteInfo) - 1;
	$counter = 0;
	Event::fire('system.onDisplaySiteInfo',array($active,array()));
?>
<div class="container-fluid halo-toolbar-wrapper">
	<div class="navbar-header">
		<div class="navbar-header">
		  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#halo-navigation-collapse">
			<span class="sr-only">{{__halotext('Naviation')}}</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		  </button>
		</div>
	</div>
    <div class="collapse navbar-collapse halo-top-bar" id="halo-navigation-collapse">
	    {{-- Left Nav Section --}}
		  <ul class="halo-nav halo-navbar-nav navbar-left">
			<li class="{{('stream' == $active)?'active':''}}"><a href="{{Url::to('?view=home')}}" title="{{__halotext('Home')}}">
				{{HALOUIBuilder::icon('home')}}
			</a></li>
			@if (HALOAuth::hasRole('registered'))
				<?php
					$my = HALOUserModel::getUser();
                    // $shortInfo = $my->getShortInfo();
					$shortInfo = array();
                    $acceptedInfo = array();
				?>
				{{-- Profile dropdown --}}
				<li class="halo-dropdown">
				  <a href="#" class="halo-dropdown-toggle" data-htoggle="dropdown" role="button" aria-expanded="false">
					<span class="halo-nav-avatar">{{--<image src="{{$my->getAvatar(20)}}" title="{{$my->getDisplayname()}}"/>--}}</span>{{substr($my->getDisplayName(), 0, 10)}}
					<span class="caret"></span>
				  </a>
				  <ul class="halo-dropdown-menu halo-nicescroll" role="menu">
					<li class="{{ HALOUtilHelper::getActiveNav('profile') }}"><a href="{{$my->getUrl()}}">{{__halotext('My Profile')}}</a></li>
                    <li class="divider"></li>
                    <li class="{{ HALOUtilHelper::getActiveNav('message') }}"><a href="{{ URL::to('?view=message&task=show') }}">{{__halotext('My Messages')}}</a></li>
                    <li class="{{ HALOUtilHelper::getActiveNav('user_edit') }}"><a href="{{ URL::to('?view=user&task=edit&uid=' . $my->id) }}">{{__halotext('Edit User')}}</a></li>
					@foreach($shortInfo as $info)
					<li><a href="{{$info->url}}">{{HALOOutputHelper::transformMyShortInfoText($info->value)}}</a></li>
					@endforeach
				  </ul>
				</li>            
			@endif
			@foreach($siteInfo as $index => $info)
			@if($info->value == __halotext('Stream'))
				<?php continue;?>
			@endif
			@if($counter < $maxExpandMenu)
			<li class="{{($info->name==$active)?'active':''}}"><a href="{{$info->url}}">
				{{$info->value}}
			</a></li>
			@else
				@if($counter == $maxExpandMenu)
			<li class="halo-dropdown">
			  <a href="#" class="halo-dropdown-toggle" data-htoggle="dropdown" role="button" aria-expanded="false">{{__halotext('More')}} <span class="caret"></span></a>
			  <ul class="halo-dropdown-menu" role="menu">				
				@endif
				<li class="{{($info->name==$active)?'active':''}}"><a href="{{$info->url}}">
					{{$info->value}}
				</a></li>				
				@if($counter == $menuCount - 1)
			  </ul>
			</li>				
				@endif
			@endif
			<?php $counter ++; ?>
			@endforeach
		  </ul>		
        {{-- Right Nav Section --}}
        <ul class="halo-nav halo-navbar-nav navbar-right">
			@if (HALOAuth::hasRole('registered'))
				<?php
					$my = HALOUserModel::getUser();
					$notifCount = HALONotificationAPI::getNewNotifyCount();
				?>
				@if(HALOAuth::can('feature.message') & HALOConfig::get('message.enable', 1))
				<li><a href="{{URL::to('?view=message&task=show')}}" title="{{__halotext('Message')}}">
					{{HALOUIBuilder::icon('comments-o')}}
				</a></li>
				@endif
				{{-- Notification counter --}}
				<li><a class="halo-notification-toggle" href="javascript:void(0)" onclick="halo.notification.list(this,'{{$my->user_id}}')">
					{{HALOUIBuilder::icon('bell').HALOUIBuilder::getInstance('','notification.counter',array('counter'=>$notifCount,'zone'=>'notification-counter'))->fetch()}}
				</a></li>
				{{-- Logout --}}
				<li><a href="{{URL::to('?view=user&task=logout')}}" title="{{__halotext('Logout')}}">
					{{HALOUIBuilder::icon('power-off')}}
				</a></li>
			@else
				@if (UserModel::canRegister())
				<li><a href="{{UserModel::getRegisterLink()}}" title="{{__halotext('Sign Up')}}">
					{{HALOUIBuilder::icon('user-plus')}}
				</a></li>
				@endif
				<li><a class="" href="javascript:void(0)" onclick="halo.user.showLogin()" title="{{__halotext('Login')}}">
					{{HALOUIBuilder::icon('key')}}
				</a></li>
			@endif
        </ul>
    </div>
	<!-- /.nav-collapse -->
</div>
<!-- /.container -->
@endUI

