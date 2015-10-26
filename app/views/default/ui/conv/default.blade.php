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

{{-- ///////////////////////////////////// Conversation Layout UI ///////////////////////////////////// --}}
@beginUI('conv.layout')
<?php $messages = $builder->messages; ?>
<div class="halo-conv-wrapper" {{$builder->getZone()}}>
<div class="halo-conv-entry-wrapper" {{$builder->getZone(1)}}>
@foreach($messages as $message)
{{HALOUIBuilder::getInstance('','conv.entry',array('message'=>$message,'conv'=>$builder->conv))->fetch()}}
@endforeach
</div>
<div class="halo-conv-message-input">
    <form name="message-input-{{$builder->conv->id}}" id="message-input-{{$builder->conv->id}}">
        <div class="halo-message-input-outter">
            <textarea class="halo-message-input halo-status-box"
                      data-resetable
                      name="message"
                      placeholder="{{__halotext('Enter your message')}}"></textarea>
        </div>
        <input type="hidden" name="conv_id" value="{{$builder->conv->id}}"/>
    </form>
</div>
</div>
@endUI

{{-- ///////////////////////////////////// Conversation Entry UI ///////////////////////////////////// --}}
@beginUI('conv.entry')
<div class="halo-conv-message-entry @if(HALOUserModel::getUser()->user_id == $builder->message->actor_id) halo-msg-reply @endif"
     data-msgid="{{$builder->message->id}}">
    <div class="halo-conv-message-info">
        <div class="halo-conv-message-timer halo-pull-right text-muted">
            <small>
                {{HALOUtilHelper::getElapsedTime($builder->message->created_at)}}@if($builder->conv->isUnreadMessage($builder->message))
                <span class="halo-message-unread badge halo-badge small">&nbsp;</span>@endif
            </small>
        </div>
        <div class="halo-conv-message-actor">
            @if(HALOUserModel::getUser()->user_id == $builder->message->actor_id)
            {{$builder->message->actor->getDisplayLink()}}
            @else
            {{$builder->message->actor->getDisplayLink()}}
            @endif
        </div>
    </div>
    <div class="halo-conv-message-content">
        <!-- message content -->
        <div class="halo-conv-message">
            {{HALOOutputHelper::parseUrl($builder->message->getMessage())}}
        </div>

    </div>
</div>
@endUI

{{-- ///////////////////////////////////// Conversation Window UI ///////////////////////////////////// --}}
@beginUI('conv.window')
<li class="halo-dropdown" {{$builder->getZone()}} data-convid="{{$builder->conv->id}}" data-convuserid="{{implode(',',$builder->conv->getAttenderIds(HALOUserModel::getUser()->id))}}">
<a href="javascript:void(0)"
   data-htoggle="conv"
   data-toggledown="conv_window_{{$builder->conv->id}}"
   class="halo-conv-window-title halo-ellipsis">{{{$builder->title}}}</a>
<div class="halo-message-nav">
    <a href="javascript:void(0)"
       title="{{__halotext('Show/Hide')}}"
       onclick="halo.message.toggleDisplayConv({{$builder->conv->id}})">{{HALOUIBuilder::icon('minus-square')}}</a>
    &nbsp;&nbsp;<a href="{{$builder->conv->getUrl()}}" title="{{__halotext('Full View')}}">{{HALOUIBuilder::icon('external-link')}}</a>
    &nbsp;&nbsp;<a href="javascript:void(0)"
                   onclick="halo.message.removeConv({{$builder->conv->id}})"
                   title="{{__halotext('Close')}}">{{HALOUIBuilder::icon('times')}}</a>
</div>
<ul class="halo-dropdown-menu" role="menu" aria-labelledby="dLabel">
    <div id="conv_window_{{$builder->conv->id}}" class="halo-message-content">
        {{$builder->content}}
    </div>
</ul>
</li>
@endUI

{{-- ///////////////////////////////////// Conversation Panel UI ///////////////////////////////////// --}}
@beginUI('conv.panel')
<li style="position: absolute" class="halo-dropdown" {{$builder->getZone()}} data-convid="panel">
<a href="javascript:void(0)" data-htoggle="conv" class="halo-conv-window-title">{{__halotext('Chat Panel')}}</a>
<div class="halo-message-nav"><a href="javascript:void(0)" class="hidden" title="{{__halotext('Options')}}">{{HALOUIBuilder::icon('cog')}}</a>&nbsp;&nbsp;<a
        href="javascript:void(0)"
        onclick="halo.message.toggleDisplayConv('panel')"
        title="{{__halotext('Show/Hide')}}">{{HALOUIBuilder::icon('minus-square')}}</a></div>
<ul class="halo-dropdown-menu" role="menu" aria-labelledby="dLabel">
    <div id="conv_window_list" class="halo-message-content halo-chat-panel">
        <form role="form">
            <input type="text" class="hidden" placeholder="{{__halotext('Search...')}}" id="halo-search-chat-panel">
        </form>
        <ul class="halo-nav nav-tabs">
            <li><a href="#conv-contacts" data-htoggle="tab"><i class="fa fa-group"></i> {{__halotext('Contacts')}}</a></li>
            <li class="active"><a data-htoggle="tab" href="#conv-recent"><i class="fa fa-comments-o"></i>
                    {{__halotext('Recent')}}</a></li>
        </ul>
        <div class="halo-tab-content">
            {{HALOUIBuilder::getInstance('conv_contacts_list','conv.contactslist',array('zone'=>'conv.panel.contactslist','conv_groups'=>$builder->conv_groups,'contacts'=>$builder->contacts))->fetch()}}
            {{HALOUIBuilder::getInstance('conv_recent_list','conv.recentlist',array('zone'=>'conv.panel.recentlist','showOlder'=>true,'conv_groups'=>$builder->conv_groups,'contacts'=>$builder->contacts))->fetch()}}
        </div>
    </div>
</ul>
</li>
@endUI

{{-- ///////////////////////////////////// Conversation Window UI ///////////////////////////////////// --}}
@beginUI('conv.container')
<div id="halo-message-wrapper" class="halow">
    <div data-halozone="message-zone">
        <ul class="halo-conv-nav-wrapper halo-nav nav-pills" data-halozone="message-zone-list">
            {{HALOUIBuilder::getInstance('conv.panel','conv.panel',array('zone'=>'conv.panel','conv_groups'=>$builder->conv_groups,'contacts'=>$builder->contacts))->fetch()}}
        </ul>
    </div>
</div>
@endUI

{{-- ///////////////////////////////////// Conversation recent List UI ///////////////////////////////////// --}}
@beginUI('conv.recentlist')
<div id="conv-recent" class="tab-pane halo-conv-entry-wrapper active" {{$builder->getZone()}}>
<ul class="halo-nav halo-contacts-list">
    <?php $today = Carbon::today(); ?>
    @foreach($builder->conv_groups as $day => $conv_group)
    @if($day == 0)
    <li><span class="halo-contacts-recent-time">{{__halotext('Today')}}</span></li>
    @elseif($day == 1)
    <li><span class="halo-contacts-recent-time">{{__halotext('Yesterday')}}</span></li>
    @else
    <li><span class="halo-contacts-recent-time">{{$today->copy()->subDays($day)->formatLocalized('%A, %d %B, %Y')}}</span>
    </li>
    @endif
    @foreach($conv_group as $conv)
    <li data-recent-convid="{{$conv->id}}"><a class="halo-ellipsis" href="javascript:void(0)"
                                              onclick="halo.message.openConvByConvId({{$conv->id}})"
                                              title="">{{HALOUIBuilder::icon('user onl')}} {{$conv->getDisplayName()}}
            @if($conv->hasUnreadMessage())<span class="halo-message-unread badge halo-badge small">&nbsp;</span>@endif</a>
    </li>
    @endforeach
    @endforeach
    @if($builder->showOlder === true)
    <li><a class="halo-show-earlier-msg" href="javascript:void(0)" onclick="halo.message.showOlderConvs()" title="">{{__halotext('Show earlier messages')}}</a></li>
    @endif
</ul>
</div>
@endUI

{{-- ///////////////////////////////////// Conversation recent List in fullview mode UI ///////////////////////////////////// --}}
@beginUI('conv.recentlist_fullview')
<?php $my = HALOUserModel::getUser(); ?>
<div class="halo-conv-entry-wrapper active" data-railoffset='{"left": 5}' {{$builder->getZone()}}>
<ul class="haloc-list list-unstyled halo-contacts-list">
    <?php $today = Carbon::today(); ?>
    @foreach($builder->conv_groups as $day => $conv_group)
    @if($day == 0)
    <li class="halo-contacts-recent-time"><span>{{__halotext('Today')}}</span></li>
    @elseif($day == 1)
    <li class="halo-contacts-recent-time"><span>{{__halotext('Yesterday')}}</span></li>
    @else
    <li class="halo-contacts-recent-time"><span>{{$today->copy()->subDays($day)->formatLocalized('%A, %d %B, %Y')}}</span>
    </li>
    @endif
    @foreach($conv_group as $conv)
    <?php $attenders = $conv->getAttenderIds();
    $userId = array_diff($attenders, array($my->id));
    if ($userId) {
        $userId = reset($userId);
        $user = HALOUserModel::getUser($userId);
		$user = $user?$user:$my;
    } else {
        $user = $my;
    }
    ?>
    <li class="halo-contact-entry clearfix"
        data-recent-convid="{{$conv->id}}"
        onclick="halo.message.changeConv({{$conv->id}})">
        <a class="halo-pull-left thumbnail halo-user-{{$user->id}}" {{$user->getBriefDataAttribute()}}
        href="{{$user->getUrl()}}">
        <img class="halo-avatar"
             data-actor="{{$user->user_id}}"
             src="{{$user->getThumb()}}"
             alt="{{{$user->getDisplayName()}}}">
        </a>
        <a class="halo-contact-entry-name halo-ellipsis" href="javascript:void(0)" title="">{{$conv->getDisplayName()}}
            @if($conv->hasUnreadMessage())<span class="halo-message-unread badge halo-badge small">&nbsp;</span>@endif</a>

        <p class="halo-contact-entry-last-msg">{{$conv->getLastMessage()}}</p>
    </li>
    @endforeach
    @endforeach
    @if($builder->showOlder === true)
    <li class="halo-show-earlier-msg"><a href="javascript:void(0)" onclick="halo.message.showOlderConvs()" title="">{{__halotext('Show earlier messages >>')}}</a></li>
    @endif
</ul>
</div>
@endUI

{{-- ///////////////////////////////////// Conversation contact List UI ///////////////////////////////////// --}}
@beginUI('conv.contactslist')
<div id="conv-contacts" class="tab-pane halo-conv-entry-wrapper" {{$builder->getZone()}}>
<ul class="halo-nav halo-contacts-list">
    @foreach($builder->contacts as $user)
    <?php //add user to the online check list
    HALOOnlineuserModel::checkOnline($user);
    ?>
    <li><a class="halo-ellipsis" href="javascript:void(0)" {{$user->getBriefDataAttribute()}} class="halo-user-{{$user->id}}"
        onclick="halo.message.openConvByUserId({{$user->id}})"
        title="{{{$user->getDisplayName()}}}">{{HALOUIBuilder::icon('user')}} {{{$user->getDisplayName()}}}</a> </li>
    @endforeach
</ul>

</div>
@endUI

{{-- ///////////////////////////////////// Conversation FullView Layout UI ///////////////////////////////////// --}}
@beginUI('conv.fullview_layout')
<?php $messages = $builder->messages; ?>
<div class="halo-conv-wrapper halo-conv-fullview-wrapper" {{$builder->getZone()}} data-fullview-convid="{{$builder->conv->id}}">
<div class="halo-conv-entry-wrapper" {{$builder->getZone(1)}}>
@if($builder->conv->messages()->count() > count($messages))
<div class="halo-conv-load-more text-center">
    {{__halotext('Load Older Messages')}}
</div>
@endif
@foreach($messages as $message)
{{HALOUIBuilder::getInstance('','conv.fullview_entry',array('message'=>$message,'conv'=>$builder->conv))->fetch()}}
@endforeach
</div>
<div class="halo-conv-message-input">
    <form name="message-input-{{$builder->conv->id}}" id="message-input-{{$builder->conv->id}}">
        <div class="halo-message-input-outter">
            <textarea class="halo-message-input halo-status-box"
                      data-resetable
                      name="message"
                      placeholder="{{__halotext('Enter your message')}}"></textarea>
        </div>
        <input type="hidden" name="conv_id" value="{{$builder->conv->id}}"/>
        <input type="hidden" name="fullview" value="1"/>
    </form>
</div>
</div>
@endUI

{{-- ///////////////////////////////////// Conversation FullView title UI ///////////////////////////////////// --}}
@beginUI('conv.fullview_container')
<div class="panel panel-default" data-halozone="halo-fullview-container">
	@if($builder->messages && $builder->conv)
    <div class="panel-heading halo-conv-title">
        <h4>{{sprintf(__halotext('Conversation: %s'),$builder->conv->getDisplayName())}}</h4>
    </div>
    <div class="panel-body">
        {{HALOUIBuilder::getInstance('','conv.fullview_layout',array('messages' =>
        $builder->messages,'conv'=>$builder->conv,'zone'=>$builder->conv->getZone()))->fetch()}}
    </div>
	@endif
</div>
@endUI

{{-- ///////////////////////////////////// Conversation FullView Entry UI ///////////////////////////////////// --}}
@beginUI('conv.fullview_entry')
<div class="halo-conv-message-entry @if(HALOUserModel::getUser()->user_id == $builder->message->actor_id) halo-msg-reply @endif"
     data-msgid="{{$builder->message->id}}">

    <div class="media halo-activity">
        <div class="halo-stream-avatar halo-pull-left">
            <a class="halo-conv-fullview thumbnail halo-user-{{$builder->message->actor->id}}"
            {{$builder->message->actor->getBriefDataAttribute()}} href="{{$builder->message->actor->getUrl()}}">
            <img class="halo-avatar"
                 data-actor="{{$builder->message->actor->user_id}}"
                 src="{{$builder->message->actor->getThumb()}}"
                 alt="{{{$builder->message->actor->getDisplayName()}}}">
            </a>
        </div>
        <div class="media-body halo-stream-content">
            <div class="halo-conv-message-info">
                <div class="halo-conv-message-timer halo-pull-right text-muted">
                    <small>{{HALOUtilHelper::getElapsedTime($builder->message->created_at)}}</small>
                </div>
                <div class="halo-conv-message-actor">
                    @if(HALOUserModel::getUser()->user_id == $builder->message->actor_id)
                    {{$builder->message->actor->getDisplayLink('halo-text-muted')}}
                    @else
                    {{$builder->message->actor->getDisplayLink()}}
                    @endif
                </div>
            </div>
            <div class="halo-conv-message-content">
                <!-- message content -->
                <div class="halo-conv-message">
                    {{HALOOutputHelper::parseUrl($builder->message->getMessage())}}
                </div>
            </div>
        </div>
    </div>
</div>
@endUI
