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

{{-- ///////////////////////////////////// Notification Layout UI ///////////////////////////////////// --}}
@beginUI('notification.notification_layout')
<?php $notification = $builder->notification;?>
@if(is_object($notification->attachment))
<div id="halo-notification-item-{{$builder->notification->id}}" class="halo-notification-item @if($builder->status==0) unread @endif" data-notifid="{{$builder->notification->id}}">
	<div class="media halo-notification">
		<div class="halo-notif-avatar halo-pull-left">
			<a class="halo-user-{{$notification->actor->id}} thumbnail" href="{{$notification->getDisplayActor()->getUrl()}}">
				<img class="halo-avatar" data-actor="{{$notification->actor->user_id}}" src="{{$notification->getDisplayActor()->getAvatar(HALO_PHOTO_SMALL_SIZE)}}" alt="{{{$notification->getDisplayActor()->getDisplayName()}}}">
			</a>
		</div>
		<div class="halo-notif-content media-body halo-stream-content">
			<!-- head title -->
			<div class="halo-notif-headline" style="display:block">
				{{$notification->attachment->headline}}
			</div>
			<!-- content -->
			<div class="halo-notif-content">
				{{$notification->attachment->content}}
			</div>

			<div class="halo-notification-action">
				@if($builder->status==0)
				<span><a class="halo-notifiation-markread" href="javascript:void(0)" title="{{__halotext('Mark this notifications as read')}}">{{HALOUIBuilder::icon('dot-circle-o')}}</a></span>
				@endif
				<span><a class="halo-notification-hide" href="javascript:void(0)" title="{{__halotext('Hide this notification')}}">{{HALOUIBuilder::icon('times')}}</a></span>
			</div>
			<!-- time -->
			<div class="halo-notif-time">
				<!-- Show time -->
				<a href="javascript:void(0);" {{HALOUtilHelper::getDataUTime($notification->updated_at)}} title="{{{HALOUtilHelper::getDateTime($notification->updated_at)}}}">{{{HALOUtilHelper::getElapsedTime($notification->updated_at)}}}</a>
			</div>
		</div>
	</div>
</div>
@endif
@endUI


{{-- ///////////////////////////////////// Notification Counter UI ///////////////////////////////////// --}}
@beginUI('notification.counter')
<span class="badge halo-notification-badge" data-trigger="click focus" {{$builder->getZone()}}>{{(empty($builder->counter)?'':$builder->counter)}}</span>
@endUI

{{-- ///////////////////////////////////// Notification Popover layout UI ///////////////////////////////////// --}}
@beginUI('notification.layout')
<div id="halo-notification-list" data-halozone="halo-notification-list">
	<div class="halo-notification-header">
		<div class="halo-notification-options halo-pull-right">
			<a href="javascript:void(0)" title="{{__halotext('Mark all notifications as read')}}" onclick="halo.notification.markAsRead('')">{{__halotext('Mark as Read')}}</a>
			<span> |</span> <a href="javascript:void(0)" title="{{__halotext('Notification Settings')}}" onclick="halo.notification.showSettings()">{{__halotext('Settings')}}</a>
		</div>
		<div class="halo-notification-title">
			<h5>{{__halotext('Notifications')}}</h5>
		</div>
	</div>
	<div class="halo-notification-content" data-halozone="halo-notification-content-nav">
		{{$builder->content}}
	</div>
	<div class="halo-notification-viewall text-center">
		<a href="{{URL::to('?view=notification&task=show')}}">{{__halotext('View all')}}</a>
	</div>
</div>
@endUI

{{-- ///////////////////////////////////// Notification Setting UI ///////////////////////////////////// --}}
@beginUI('notification.settings')
<form id="popupForm" name="popupForm">
<table class="table table-condensed table-hover">
	<thead>
	<th>{{__halotext('Notification')}}</th>
	<th>{{__halotext('Email')}}</th>
	<th>{{__halotext('Push Notifications')}}</th>
	</thead>
	@foreach($builder->settings as $groupName => $group)
	<tr class="halo-notification-settings-group info @if(count($group) == 1) hidden @endif">
		<td>{{__halotext(ucfirst($groupName))}}</td>
		<td align="center"><input type="checkbox" class="halo-groupcheck-ctrl" data-halo-groupcheck="{{$groupName}}_e"></td>
		<td align="center"><input type="checkbox" class="halo-groupcheck-ctrl" data-halo-groupcheck="{{$groupName}}_i"></td>
	</tr>
		@foreach($group as $notifName => $values)
	<tr>
		<td class="halo-notification-settings-type">{{$values['d']}}</td>
		<td align="center"><input type="checkbox" class="halo-groupcheck-ele" data-halo-groupcheck="{{$groupName}}_e" name="notif[{{$groupName}}][{{$notifName}}][e]" value="1" @if($values['e'])checked@endif></td>
		<td align="center"><input type="checkbox" class="halo-groupcheck-ele" data-halo-groupcheck="{{$groupName}}_i" name="notif[{{$groupName}}][{{$notifName}}][i]" value="1" @if($values['i'])checked@endif></td>
	</tr>			
		@endforeach
	@endforeach
</table>
</form>
@endUI

{{-- ///////////////////////////////////// Notification Default Setting UI ///////////////////////////////////// --}}
@beginUI('notification.default')
<table class="table table-condensed table-hover">
	<thead>
	<th>{{__halotext('Notification')}}</th>
	<th>{{__halotext('Email')}}</th>
	<th>{{__halotext('Push Notifications')}}</th>
	</thead>
	@foreach($builder->settings as $groupName => $group)
	<tr class="halo-notification-settings-group info @if(count($group) == 1) hidden @endif">
		<td>{{ucfirst($groupName)}}</td>
		<td align="center"><input type="checkbox" class="halo-groupcheck-ctrl" data-halo-groupcheck="{{$groupName}}_e"></td>
		<td align="center"><input type="checkbox" class="halo-groupcheck-ctrl" data-halo-groupcheck="{{$groupName}}_i"></td>
	</tr>
		@foreach($group as $notifName => $values)
	<tr>
		<td class="halo-notification-settings-type">{{$values['d']}}</td>
		<td align="center">
			<input type="hidden" name="notification.default[{{$groupName}}][{{$notifName}}][e]" value="0">
			<input type="checkbox" class="halo-groupcheck-ele" data-halo-groupcheck="{{$groupName}}_e" name="notification.default[{{$groupName}}][{{$notifName}}][e]" value="1" @if($values['e'])checked@endif>
		</td>
		<td align="center">
			<input type="hidden" name="notification.default[{{$groupName}}][{{$notifName}}][i]" value="0">
			<input type="checkbox" class="halo-groupcheck-ele" data-halo-groupcheck="{{$groupName}}_i" name="notification.default[{{$groupName}}][{{$notifName}}][i]" value="1" @if($values['i'])checked@endif>
		</td>
	</tr>			
		@endforeach
	@endforeach
</table>
@endUI
