@extends('site.layouts.default')

{{-- Web site Title --}}
@section('title')
{{{ __halotext('User Notification') }}}
@parent
@stop

{{-- Content --}}
@section('content')
<div class="halo-section-container halo-section-notification">
	<div class="halo-section-header halo-notification-header">
		<div class="halo-notification-options halo-pull-right">
			<a href="javascript:void(0)" title="{{__halotext('Mark all notifications as read')}}" onclick="halo.notification.markAsRead('')">{{__halotext('Mark as Read')}}</a>
			<span> |</span> <a href="javascript:void(0)" title="{{__halotext('Notification Settings')}}" onclick="halo.notification.showSettings()">{{__halotext('Settings')}}</a>
		</div>
		<div class="halo-notification-title halo-pull-left">
			<h5>{{__halotext('Notifications')}}</h5>
		</div>
        <div class="clearfix"></div>
	</div>
	<div class="halo-section-body halo-notification-listing" data-halozone="halo-notification-content-body">
		{{HALOResponse::getZoneContent('halo-notification-content-body')}}
	</div>
	{{HALOResponse::getZoneScript('halo-notification-content-body')}}
	{{HALOResponse::getZonePagination('halo-notification-content-body')}}
</div>
@stop

