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

{{-- ///////////////////////////////////// user response actions UI ///////////////////////////////////// --}}
@beginUI('user.responseActions')
<?php $user = $builder->user ?>
<?php $my = HALOUserModel::getUser(); ?>
<div class="halo-stacked-list halo-user-response-actions"
     data-stacked-class="halo-btn halo-btn-default halo-btn-xs"
     data-halozone="user-response-actions-{{$user->id}}" style="visibility: hidden;">
	<ul class="list-inline halo-list-container">
	@if($my)
		@if($my->user_id != $user->user_id && HALOAuth::can('feature.message') && HALOConfig::get('message.enable', 1))
		<li class="">
			<a href="javascript:void(0)"
			   onclick="halo.message.openConvByUserId('{{$user->user_id}}')"
			   class="halo-btn halo-btn-xs halo-btn-default">{{HALOUIBuilder::icon('comment-o')}} {{__halotext('Send Message')}}</a>
		</li>
		@endif
		@if(HALOAuth::can('feature.friend'))
            @if($my->isFriend($user))
                <li>
                    <button type="button" class="halo-btn halo-btn-default halo-btn-xs halo-dropdown-toggle" data-htoggle="dropdown">
                        {{HALOUIBuilder::icon('users')}} {{__halotext('Friend')}} <i class="fa fa-caret-down"></i>
                    </button>
                    <ul class="halo-dropdown-menu text-left" role="menu">
                        <li><a href="javascript:void(0)" onclick="halo.friend.unFriend('{{$user->user_id}}')">{{__halotext('Unfriend')}}</a>
                        </li>
                    </ul>
                </li>
            @elseif($my->isRequestingFriend($user))
                <li>
                    <a href="#" class="halo-btn halo-btn-default halo-btn-xs halo-dropdown-toggle" data-htoggle="dropdown">
                        {{HALOUIBuilder::icon('users')}} {{__halotext('Requested Friend')}} <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="halo-dropdown-menu" role="menu">
                        <li><a href="javascript:void(0)" onclick="halo.friend.sendRequest('{{$user->user_id}}')">{{HALOUIBuilder::icon('users')}}
                                {{__halotext('Resend Friend Request')}}</a></li>
                    </ul>
                </li>
            @elseif($my->isRequestedFriend($user))
                <li>
                    <a href="#" class="halo-btn halo-btn-default halo-btn-xs halo-dropdown-toggle" data-htoggle="dropdown">
                        {{HALOUIBuilder::icon('users')}} {{__halotext('Response Friend Request')}} <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="halo-dropdown-menu" role="menu">
                        <li>
                            <a href="javascript:void(0)"
                               onclick="halo.friend.approveRequest('{{$user->user_id}}','1')"
                               class="halo-btn halo-btn-xs halo-btn-default">{{HALOUIBuilder::icon('plus')}} {{__halotext('Accept')}}</a>
                        </li>
                        <li>
                            <a href="javascript:void(0)"
                               onclick="halo.friend.rejectRequest('{{$user->user_id}}','1')"
                               class="halo-btn halo-btn-xs halo-btn-default">{{HALOUIBuilder::icon('minus')}} {{__halotext('Not Now')}}</a>
                        </li>
                    </ul>
                </li>
            @elseif($my->id != $user->id)
                <li>
                    <a href="javascript:void(0)"
                       onclick="halo.friend.sendRequest('{{$user->user_id}}')"
                       class="halo-btn halo-btn-xs halo-btn-default">{{HALOUIBuilder::icon('plus')}} {{__halotext('Add Friend')}}</a>
                </li>
            @endif
		@endif
		@if( HALOAuth::can('feature.follow') && !HALOAuth::hasRole('owner',$user))
		<li class="">
			{{HALOFollowerModel::getFollowerHtml($user)}}
		</li>
		@endif
		@if( HALOAuth::can('feature.label') && HALOAuth::can('backend.view')
			&& HALOLabelGroupModel::hasLabels('user') 
		)
		<li class="">
			<a href="javascript:void(0)"
			   onclick="halo.label.showAssignLabel('{{$user->getContext()}}','{{$user->id}}')"
			   class="halo-btn halo-btn-xs halo-btn-default">{{HALOUIBuilder::icon('tags')}}
				{{__halotext('Set Labels')}}</a>
		</li>
		@endif
		@if( HALOAuth::can('user.edit',$user) )
		<li class="">
			<a href="javascript:void(0)"
			   onclick="halo.photo.changeAvatar('{{$user->getContext()}}','{{$user->user_id}}')"
			   class="halo-btn halo-btn-xs halo-btn-default">{{HALOUIBuilder::icon('pencil-square-o')}} {{__halotext('Change Avatar')}}</a>
		</li>
		<li class="">
			<a href="javascript:void(0)"
			   onclick="halo.photo.changeCover('{{$user->getContext()}}','{{$user->user_id}}')"
			   class="halo-btn halo-btn-xs halo-btn-default">{{HALOUIBuilder::icon('pencil-square-o')}} {{__halotext('Change Cover')}}</a>
		</li>
		@endif
		@if( HALOAuth::can('user.edit',$user) )
		<li class="">
			<a href="{{URL::to('?view=user&task=edit&uid=' . $user->id )}}" class="halo-btn halo-btn-xs halo-btn-default">{{HALOUIBuilder::icon('pencil-square-o')}}
				{{__halotext('Edit User')}}</a>
		</li>
		@endif
		@if($my->id != $user->id)
		<li class="">
			<a href="javascript:void(0)"
			   onclick="halo.report.showReport('{{$user->getContext()}}','{{$user->user_id}}')"
			   class="halo-btn halo-btn-xs halo-btn-default">{{HALOUIBuilder::icon('warning')}} {{__halotext('Report')}}</a>
		</li>
		@endif
		@if($my->id == $user->id && !HALOAuth::can('backend.view'))
		<li class="">
			<a href="javascript:void(0)" onclick="halo.user.deleteMe()" class="halo-btn halo-btn-xs halo-btn-default">{{HALOUIBuilder::icon('trash-o')}}
				{{__halotext('Delete Account')}}</a>
		</li>
		@endif
	@endif
	<?php Event::fire('user.loadResponseActions',array($user)); ?>
	</ul>
</div>
@endUI

{{-- ///////////////////////////////////// user list UI ///////////////////////////////////// --}}
@beginUI('user.responseActionItem')
	<li class="">
		<a href="javascript:void(0)" {{$builder->getOnClick()}} class="halo-btn halo-btn-xs halo-btn-default">{{$builder->getIcon()}}
			{{$builder->title}}
		</a>
	</li>
@endUI

{{-- ///////////////////////////////////// user list UI ///////////////////////////////////// --}}
@beginUI('user.list')
<div class="halo-user-list-wrapper" {{$builder->getZone()}} {{HALOUIBuilder::getPageAttr($builder->users)}}>
@if(count($builder->users) == 0)
<p class="text-center"> {{__halotext('No users found')}} </p>
@else
<?php Event::fire('user.onLoadInfoCounters', array(&$builder->users)); //lazy load counters ?>
@foreach($builder->users as $user)
<div class="col-md-6 col-sm-12 halo-list-wrapper-user halo-list-thumb">
	<div class="row halo-list-item-user" data-halo-userid="{{$user->id}}">
		{{HALOUIBuilder::getInstance('','label.label',array('target'=>$user,'position'=>'left'
		,'group_code'=>HALOConfig::get('user.label.status')
		,'prefix'=>'halo-label-status','mode'=>'single'))->fetch()}}
		<div class="col-xs-4 thumbnail-wrapper">
			<a class="halo-pull-left thumbnail halo-user-{{$user->id}}" href="{{$user->getUrl()}}">
				<img class="img-responsive" src="{{HALO_IMAGE_PLACE_HOLDER_S}}" data-src="{{$user->getAvatar(160)}}" alt="{{{ $user->getDisplayName() }}}"/>
			</a>
		</div>
		<div class="col-xs-8 halo-list-item-user-info clearfix">
			<h4 class="halo-ellipsis">{{ $user->getDisplayLink('',false) }}</h4>

			<div class="halo-badge-wrapper">
				{{HALOUserpointAPI::getUserpointKarma($user->getUserPoint())}}
			</div>
			<div class="halo-user-list-info haloj-list-ellipsis-more">
				{{HALOUIBuilder::getInstance('','user.info',array('user'=>$user,'class'=>'haloc-list list-unstyled'))->fetch()}}
			</div>
		</div>
		<div class="col-xs-12 halo-action-wrapper-user">
			<div class="halo-label-badge-wrapper halo-pull-right">
                {{HALOUIBuilder::getInstance('','label.badge',array('target'=>$user,'position'=>'right'
                                                                        ,'group_code'=>HALOConfig::get('user.label.badge')
                                                                        ,'prefix'=>'halo-label-badge','mode'=>'multiple'))->fetch()}}
            </div>
            <div class="clearfix"></div>
			{{HALOUIBuilder::getInstance('','user.responseActions',array('user'=>$user))->fetch()}}
		</div>
	</div>
</div>
@endforeach
<div class="clearfix"></div>
@endif

</div>
@endUI

{{-- ///////////////////////////////////// user info UI ///////////////////////////////////// --}}
@beginUI('user.info')
<?php $user = $builder->user;
?>
<ul class="{{$builder->class}} clearfix">
	@foreach($user->getShortInfo() as $info)
	<li class="col-xs-6">
		<a class="{{$info->class}}" href="{{$info->url}}" title="{{{$info->title}}}">{{{$info->value}}}</a>
	</li>
	@endforeach
</ul>

@endUI

{{-- ///////////////////////////////////// user inline list UI ///////////////////////////////////// --}}
@beginUI('user.list_inline')
<?php
//make sure users are loaded
$textOnly = !empty($builder->textOnly);
if(is_a($builder->users, 'HALOActorList') || is_a($builder->users, 'HALOUserModel')) {
	$actors = $builder->users;
} else if(is_array($builder->users)){
	$userIds = array_map(function ($user) {
						return $user->id;
					}, (array)$builder->users);
	$actors = HALOActorList::fromArray($userIds);
} else {
	return '';
}
?>
@if(count($actors) != 0)
	@if($textOnly)
		{{$actors->getDisplayName()}}
	@else
	<span class="halo-user-list-text-wrapper">
		{{$actors->getDisplayLink()}}
	</span>
	@endif
@endif
@endUI

{{-- ///////////////////////////////////// Focus menu item About me UI ///////////////////////////////////// --}}
@beginUI('user.about_me')
<div class="halo-field-list-wrapper">
	<?php $allowedPrivacy = HALOFieldModel::getAllowedPrivacy($builder->user);
	?>
	@foreach ($builder->user->getProfileFields()->get() as $field)
		<?php $haloField = $field->toHALOField();
			$respectPrivacy = $haloField->isEnabledPrivacy();
		?>
		@if(!$respectPrivacy || ($respectPrivacy && in_array($haloField->access, $allowedPrivacy)))
			{{$haloField->getReadableUI()}}
		@endif
	@endforeach
	
	{{HALOUIBuilder::getInstance('member_since','form.readonly_field',array('title'=>__halotext('Member since'),'value'=>$builder->user->created_at))->fetch()}}
	{{HALOUIBuilder::getInstance('last_seen','form.readonly_field',array('title'=>__halotext('Last seen'),'value'=>$builder->user->updated_at))->fetch()}}
</div>
@endUI