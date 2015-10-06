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

{{-- ///////////////////////////////////// Icon UI ///////////////////////////////////// --}}
@beginUI('icon')
<i class="fa fa-{{$builder->icon}}"></i>
@endUI

{{-- ///////////////////////////////////// Group Button UI ///////////////////////////////////// --}}
@beginUI('group_button')
<ul class="halo-btn-group {{ $builder->class}}">
	@if($builder->hasChild('button@array'))
	@foreach($builder->getChild('button@array') as $button)
	@if($button->url)
	<a href="{{ $button->getUrl() }}" class="halo-btn {{ $button->class }}" {{ $button->getOnClick() }} @if($button->target)target="{{ $button->target }}"@endif>
	{{ $button->getIcon() }} {{{ $button->title }}}
	</a>
	@else
	<button type="button" href="{{ $button->getUrl() }}" class="halo-btn {{ $button->class }}"
	{{ $button->getOnClick() }} >
	{{ $button->getIcon() }} {{{ $button->title }}}
	</button>
	@endif
	@endforeach
	@endif
</ul>
@endUI

{{-- ///////////////////////////////////// Extra UI from here: target to move to seperate file// --}}


{{-- ///////////////////////////////////// Ads Post stream entry UI///////////////////////////////////// --}}
@beginUI('post_attachment')
<div class="halo-attachment">
	<div class="halo-post-description" data-link="{{$builder->post->getUrl()}}">
		{{$builder->post->getDescription()}}
	</div>
	<div class="halo-post-media-container">
		@if(is_object($builder->media))
		@foreach(get_object_vars($builder->media) as $ele => $media)
		<div class="halo-post-media-{{$ele}}">
			{{$media->fetch()}}
		</div>
		@endforeach
		@endif
	</div>
</div>
@endUI

{{-- ///////////////////////////////////// Activity Layout UI ///////////////////////////////////// --}}
@beginUI('activity_layout')
<?php
$act = $builder->act;
$responseTarget = $act->getResponseTarget();
?>
<div id="profile-newsfeed-item-{{$builder->act->id}}" class="stream panel panel-default halo-activity-wrapper"
     data-actid="{{$builder->act->id}}" {{$builder->getZone()}}>
<div class="panel-body haloj-content-block">
	<div class="halo-activity">
		<div class="halo-stream-headline-wrapper clearfix">
			<div class="halo-stream-avatar halo-pull-left">
				<a class="thumbnail halo-user-{{$act->actor->id}}" {{$act->getDisplayActor()->getBriefDataAttribute()}}
				href="{{$act->getDisplayActor()->getUrl()}}">
				<img class="halo-avatar" data-actor="{{$act->actor->user_id}}" src="{{$act->getDisplayActor()->getAvatar(HALO_PHOTO_THUMB_SIZE)}}"
				     alt="{{{$act->getDisplayActor()->getDisplayName()}}}">
				</a>
			</div>
			<!-- head title -->
			<div class="halo-stream-headline">
				<div class="halo-btn-group halo-pull-right halo-dropdown">
					<a class="halo-dropdown-toggle halo-btn halo-btn-xs halo-btn-nobg" data-htoggle="dropdown"><i
							class="fa fa-chevron-down"></i></a>
					<ul class="halo-dropdown-menu" role="menu">
						@if(!$act->getParams('act_readonly',0) && HALOAuth::can('activity.edit',$act))
						<li><a href="javascript:void(0)" onclick="halo.status.editPost('{{$builder->act->id}}')"><i class="fa fa-pencil-square-o"></i> {{__halotext('Edit Post')}}</a>
						</li>
						@endif
						@if(HALOAuth::can('activity.delete',$act))
						<li><a href="javascript:void(0)" onclick="halo.status.delete('{{$builder->act->id}}')"><i class="fa fa-trash-o"></i> {{__halotext('Delete')}}</a>
						</li>
						@endif
						<li><a href="javascript:void(0)"
						       onclick="halo.report.showReport('{{$builder->act->getContext()}}','{{$builder->act->id}}')"><i class="fa fa-warning"></i> {{__halotext('Report')}}</a>
						</li>
					</ul>
				</div>
				@if(isset($act->attachment->headline))
				{{$act->attachment->headline}}
				@else
				{{$act->actor->getDisplayLink('halo-stream-author')}}
				@endif
				{{--tagged_list--}}
				@if(count($act->tagusers))
				<?php $tagUsers = array();
				foreach ($act->tagusers as $tag) {
					$tagUsers[] = $tag->taggable;
				}
				?>
				<span class="halo-cStream-tagusers"> - {{__halotext('with')}} {{HALOUIBuilder::getInstance('','user.list_inline',array('users'=>$tagUsers))->fetch()}}</span>
				@endif
				{{--privacy--}}
				@if(!$act->getParams('act_readonly',0))
				@if(HALOAuth::can('activity.edit',$act))
                <span class="halo-cStream-privacy">{{HALOUIBuilder::getInstance('','form.privacy',array('name'=>'act_privacy_'.$act->id,'onChange'=>'halo.status.changePrivacy(\''.$act->id.'\')',
                                                                                                    'btnsize'=>'halo-btn-xs','value'=>$act->access))->fetch()}}</span>
				@elseif(HALOConfig::get('global.activityShowPrivacy'))
				<span class="halo-cStream-privacy">{{HALOUIBuilder::getInstance('','form.viewprivacy',array('value'=>$act->access))->fetch()}}</span>
				@endif
				@endif
				<div class="halo-timemark"><a href="javascript:void(0);" {{HALOUtilHelper::getDataUTime($act->created_at)}}
					title="{{HALOUtilHelper::getDateTime($act->created_at)}}">{{HALOUtilHelper::getElapsedTime($act->created_at)}}</a>
				</div>

			</div>
		</div>
		<div class="halo-stream-content">
			<!-- message content -->
			<div class="halo-stream-message" data-halozone="halo-stream-message-{{$act->id}}" data-more-link="<a href='javascript:void(0);'>{{__halotext('Read More')}} </a>">
				{{HALOUIBuilder::getInstance('','ellipsis',array('message'=>$act->getMessage(),
																'link'=>$act->getUrl(),
																'data'=>array('height' => '100','length' => '2000')))->fetch()}}
				<span class="halo-act-info"> {{$act->getInfoHtml()}}</span>
			</div>

			<!-- attachment content -->
			<div class="halo-stream-attachment">@if(isset($act->attachment->content))
				{{$act->attachment->content}}
				@endif</div>
			<!-- sharelink if exists -->
			{{HALOUIBuilder::getInstance('','share_link_block_content',array('target'=>$act))->fetch()}}
			<!-- actions -->
			@if(!$act->getParams('act_readonly',0))
			<div class="halo-stream-actions">
				<div class="">
					<!-- Show likes -->
						<span class="halo-like">
							{{ HALOLikeAPI::getLikeDislikeHtml($responseTarget) }}
						</span>
					<!-- Show comment -->
						@if(HALOAuth::can('comment.create',$responseTarget))
						<span>
							<a href="javascript:void(0)" onclick="halo.activity.toggleComment(this)">{{HALOUIBuilder::icon('comment')}} {{__halotext('Comment')}} (<span class="comment-count">{{$responseTarget->comments->count()}}</span>)</a>
						</span>
						<!-- Show comment actor switcher-->
							@if(method_exists($responseTarget,'listDisplayActors'))
								{{$responseTarget->listDisplayActors('comment.switchDisplayActors')}}
							@endif
						@endif

				</div>
			</div>
			@if($responseTarget)
			<!-- comment -->
			<div class="halo-stream-respond">
				{{HALOOutputHelper::renderCommentHtml($responseTarget, HALOConfig::get('global.commentDisplayLimit'),
				'comment.'.$responseTarget->getZone())}}
			</div>
			@endif
			@endif
		</div>
	</div>
</div>
{{ $act->renderFooter() }}
</div>
@endUI

{{-- ///////////////////////////////////// edit activity UI ///////////////////////////////////// --}}
@beginUI('activity_edit')
<?php $act = $builder->act;?>
@if($act)
	<div class="halo-stream-message" data-halozone="halo-stream-message-{{$act->id}}">
		<form name="status_form_info" id="edit_post_form_{{$act->id}}" role="form">
			<input type="hidden" name="edited_message" id="edited_message_{{$act->id}}" value=""/>
			<textarea data-resetable data-raw-input="edited_message_{{$act->id}}" data-confirm class="form-control halo-status-box"
					  name="act_message" placeholder="{{{$builder->placeholder}}}">{{$act->message}}</textarea>
			<div class="halo-stream-edit-function">
				<div class="pull-right" style="margin-top: 4px;">
					<button type="button" onclick="halo.status.cancelEditPost('{{$act->id}}');"
							class="halo-btn halo-btn-default halo-btn-sm halo-status-function-btn halo-btn-post">
						{{__halotext('Cancel')}}
					</button>
					<button type="button" onclick="halo.status.doneEditPost('{{$act->id}}');"
							class="halo-btn halo-btn-primary halo-btn-sm halo-status-function-btn halo-btn-post">
						{{__halotext('Done Editing')}}
					</button>
				</div>
			</div>
		</form>
	</div>
@endif
@endUI

{{-- ///////////////////////////////////// edit activity UI ///////////////////////////////////// --}}
@beginUI('activity_message')
<?php $act = $builder->act;?>
@if($act)
	<div class="halo-stream-message" data-halozone="halo-stream-message-{{$act->id}}" data-more-link="<a href='javascript:void(0);'>{{__halotext('Read More')}} </a>">
		{{HALOUIBuilder::getInstance('','ellipsis',array('message'=>$act->getMessage(),
														'link'=>$act->getUrl(),
														'data'=>array('height' => '100','length' => '2000')))->fetch()}}
		<span class="halo-act-info"> {{$act->getInfoHtml()}}</span>
	</div>
@endif
@endUI

{{-- ///////////////////////////////////// footer activity UI ///////////////////////////////////// --}}
@beginUI('activity_footer')
<div class="halo-stream-more-activity" {{$builder->getZone()}}>
	<h4 class="text-muted">{{$builder->title}}</h4>
	<div {{$builder->getClass("halo-wrapper")}}>
	{{$builder->content}}
	</div>
</div>
@endUI

{{-- ///////////////////////////////////// ellipsis UI ///////////////////////////////////// --}}
@beginUI('ellipsis')
	@if(!Input::get('read_more',''))
		<?php
		//truncate the message if exceeds the data-length
			if($builder->getRawData('length',0)){
				//length defined
				$message = HALOOutputHelper::ellipsis($builder->message,$builder->getRawData('length'));
				if($builder->fixed || (strlen($message) < strlen($builder->message))){
					$link = $builder->link;
					$builder->setRawData('link',$link);
				}
			} else {
				//length not defined. do not truncate it
				$message = $builder->message;
			}
		?>
		<div {{$builder->getClass('haloj-ellipsis haloc-ellipsis')}} {{$builder->getData()}} style="display:none">
			{{$message}}
		</div>
	@else
		<div {{$builder->getClass('')}} {{$builder->getData()}}>
			{{$builder->message}}
		</div>
	@endif
@endUI

{{-- ///////////////////////////////////// Like UI ///////////////////////////////////// --}}
@beginUI('like')
<span {{$builder->getZone()}}>
<a class="halo-like-btn" title="{{{$builder->title}}}" {{$builder->getOnClick()}}>{{$builder->getIcon()}} {{HALOUIBuilder::getInstance('',$builder->like.'count',array('liked'=>$builder->liked,'likeList'=>$builder->likeList,'numberOnly'=>$builder->numberOnly))->fetch()}}</a>
</span>
@endUI

{{-- ///////////////////////////////////// Like Count UI ///////////////////////////////////// --}}
@beginUI('likecount')
@if($builder->numberOnly)
{{count($builder->likeList)}}
@elseif(empty($builder->likeList))
{{__halotext('Like')}}
@else
{{sprintf(__halotext('%s liked this'),HALOUIBuilder::getInstance('','user.list_inline',array('users'=>$builder->likeList))->fetch())}}
@endif
@endUI

{{-- ///////////////////////////////////// Like Count UI ///////////////////////////////////// --}}
@beginUI('dislikecount')
@if($builder->numberOnly)
{{count($builder->likeList)}}
@elseif(empty($builder->likeList))
0
@else
{{sprintf(__halotext('%s Disliked this'),HALOUIBuilder::getInstance('','user.list_inline',array('users'=>$builder->likeList))->fetch())}}
@endif
@endUI

{{-- ///////////////////////////////////// Follow UI ///////////////////////////////////// --}}
@beginUI('follow')
<a href="javascript:void(0)" {{$builder->getZone()}} {{$builder->getClass()}} title="{{{$builder->title}}}" {{$builder->getOnClick()}}>{{$builder->getIcon()}}{{$builder->title}}</a>
@endUI

{{-- ///////////////////////////////////// Post Profile Edit UI ///////////////////////////////////// --}}
@beginUI('inline_profile_edit')
<div
	class="halo-profile-edit halo-profile-fields @if(!count($builder->profileFields)) halo-profile-emtpy @endif" {{$builder->getZone()}}>
@if(count($builder->profileFields))
<div class="halo-profile-fields-header">
	<h3>{{__halotext('Details')}}</h3>
</div>
@endif
@foreach($builder->profileFields as $field)
{{$field->toHALOField()->getEditableUI()}}
@endforeach
</div>
@endUI

{{-- ///////////////////////////////////// Filter tree UI ///////////////////////////////////// --}}
@beginUI('filter_tree')
<form id="streamFilters">
	<div data-tree-select class="halo-btn-group {{$builder->getSize()}}  {{($builder->error?'error':'')}}">
		<button type="button" class="halo-btn halo-btn-default halo-dropdown-toggle" data-htoggle="dropdown">
			{{$builder->title}}: <span class="halo-filter-selected-val">{{__halotext('All')}}</span> <i
				class="fa fa-caret-down"></i>
		</button>
		<ul class="halo-dropdown-menu" role="menu" aria-labelledby="dLabel">
			@foreach($builder->filters as $filter)
			{{$filter->getDisplayUI('filter_tree_node')->fetch()}}
			@endforeach
		</ul>
	</div>
</form>
@endUI

{{-- ///////////////////////////////////// Filter tree node UI ///////////////////////////////////// --}}
@beginUI('filter_tree_node')
<?php $options = $builder->options;
$builder->input = empty($builder->name) ? $builder->input : $builder->name; ?>
@if(!empty($builder->name))
<input data-halo-multiple type="hidden" value="{{{$builder->value}}}"
       name="{{$builder->name}}" {{$builder->getOnChange()}} {{$builder->getDisabled()}} {{$builder->getReadOnly()}}/>
@endif
@if(!empty($options))
@foreach($options as $opt)
@if(empty($opt->_children))
<li><a href="javascript:void(0);" onclick="halo.form.changeTreeSelectOption(this)" data-halo-input="{{$builder->input}}"
       data-halo-value="{{{$opt->value}}}">{{ isset($opt->icon)?HALOUIBuilder::icon($opt->icon):'' }}{{ $opt->name }}</a>
</li>
@else
<li class="dropdown-submenu"><a data-htoggle="dropdown" href="javascript:void(0);">{{ $opt->name
		}}{{$builder->getIcon()}}</a>
	<ul class="halo-dropdown-menu" role="menu" aria-labelledby="dLabel">
		{{HALOUIBuilder::getInstance('','filter_tree_node',array('options'=>$opt->_children,'input'=>$builder->input))->fetch()}}
	</ul>
</li>
@endif
@endforeach
@endif
@endUI

{{-- ///////////////////////////////////// Filter List UI ///////////////////////////////////// --}}
@beginUI('filter_list')
<?php $uid = uniqid();
	$filters = HALOFilter::orderingFilters($builder->filters);
	$filterValues = Input::get('filters',null);
	$collapsed = $filterValues?false:true;
	$visiableFilterHtml = '';
	$hiddenFilterHtml = '';
	foreach($filters as $filter){
		if($filter && $filter->getParams('title','') != ''){
			$filterHtml = $filter->getDisplayUI('form.filter_tree_chechbox')->fetch();
			if(trim($filterHtml) !== '') {
				$visiableFilterHtml .= '<div class="halo-filter-col col-md-3">' . $filterHtml . '</div>';
			}
		} else {
			$hiddenFilterHtml .= '<input type="hidden" value="'. htmlentities($filter->value) . '" name="' . $filter->getInputName() . '"/>';
		}
	}

?>
<div class="halo-filter">
	<div class="row">
		<div class="col-md-12">
			<div class="input-group clearfix halo-btn-block">
				<div class="halo-pull-right @if($visiableFilterHtml ==='') hidden @endif">
					<button type="button" class="halo-btn halo-btn-xs halo-btn-nobg halo-dropdown-toggle" data-htoggle="filter"
					        data-filter-content="halo_filter_content{{$uid}}">
						<i class="fa fa-filter"></i>{{__halotext('Filters')}}
					</button>
				</div>
				<!-- /btn-group -->
				<div class="filter_label_container halo_filter_label{{$uid}}"></div>
				<input id="halo_filter_label{{$uid}}" type="hidden" class="halo-filter-label-input form-control hidden" disabled
				{{$builder->getOnChange()}}>
			</div>
			<!-- /input-group -->
			<div class="row halo-filter-content @if($collapsed) hidden @endif" id="halo_filter_content{{$uid}}"
			     data-filter-label="halo_filter_label{{$uid}}">
				{{$visiableFilterHtml}}{{$hiddenFilterHtml}}
				<div class="halo-filter-apply-wrapper">
					<button type="button" class="halo-btn halo-btn-primary halo-btn-xs halo-btn-apply-filter"
					        onclick="halo.filter.apply(this);">{{__halotext('Go')}}
					</button>
				</div>
			</div>
		</div>
	</div>
</div>
@endUI

{{-- ///////////////////////////////////// Location link UI ///////////////////////////////////// --}}
@beginUI('location_link')
<a href="javascript:void(0)" data-htoggle="popover" data-trigger="hover focus"
   data-halo-loc-id="map-dropdown-{{$builder->location->id}}" data-options="is_hover:true" data-halo-map-dropdown
   data-halo-loc-lat="{{$builder->location->getLat()}}" data-halo-loc-lng="{{$builder->location->getLng()}}"
   data-halo-loc-name="{{$builder->location->getName()}}" {{$builder->getData()}}>{{$builder->location->getDisplayName()}}</a>
<div id="map-dropdown-{{$builder->location->id}}" data-dropdown-content></div>
@endUI

{{-- ///////////////////////////////////// Ajax login UI ///////////////////////////////////// --}}
@beginUI('ajaxlogin')
<form class="halo-login-form" name="{{$builder->name}}" id="{{$builder->name}}" accept-charset="UTF-8"
      onsubmit="halo.user.login(); return false;">
	<input type="hidden" name="_token" value="{{ csrf_token() }}">
	<div class="row">
		<div class="col-md-5">
			<div class="input-group">
				<span class="input-group-addon"><i class="fa fa-user"></i></span>
				<input class="form-control" tabindex="1" placeholder="{{ __halotext('Your username') }}" type="text"
				       name="email" id="email" value="{{ Input::old('email') }}">
			</div>

		</div>
		<div class="col-md-5">
			<div class="input-group">
				<span class="input-group-addon"><i class="fa fa-key"></i></span>
				<input class="form-control" tabindex="2" placeholder="{{ __halotext('Your password') }}" type="password"
				       name="password" id="password">
			</div>
		</div>
		<div class="col-md-2">
			<button data-loading data-size="1" data-replace="false" data-complete="user.Login.haloLoading" tabindex="3" type="submit" class="halo-btn halo-btn-primary halo-btn-block">{{ __halotext('Login') }}</button>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12 last">
			<div style="display:inline-block;">
			{{ HALOUIBuilder::getInstance(
					'remember_me', 'form.checkbox', array('name' => 'remember', 'value' => 1, 'options' => array(HALOObject::getInstance(array('value'=>'1','title'=>__halotext('Remember Me'))))
			))->fetch() }}
			</div><div style="width:20px; display:inline-block"></div>
			<a class="" href="{{ URL::to(UserModel::getForgotLink()) }}">{{ __halotext('Forgot Password?') }}</a>
			<?php $socialSettings = HALOUtilHelper::getSocialSettings();
			$hasSocialLogin = false;
			foreach (HALOUtilHelper::getSettingList($socialSettings, 'social') as $secName => $secValue) {
				if (OAuthEx::hasConsumer($secName)) $hasSocialLogin = true;
			}
			?>

			@if($hasSocialLogin)
				{{__halotext('Or Sign in with')}}
				@foreach(HALOUtilHelper::getSettingList($socialSettings,'social') as $secName => $secValue)
					@if(OAuthEx::hasConsumer($secName))
					{{HALOUIBuilder::getInstance('','social.login_icon_' . lcfirst($secName),array())->fetch()}}
					@endif
				@endforeach
			@endif

		</div>
	</div>

	@if ( Session::get('error') )
	<div class="alert alert-danger">{{{ Session::get('error') }}}</div>
	@endif

	@if ( Session::get('notice') )
	<div class="alert">{{{ Session::get('notice') }}}</div>
	@endif

</form>
@endUI

{{-- ///////////////////////////////////// tab UI ///////////////////////////////////// --}}
@beginUI('tabcontainer')
<ul {{$builder->getClass("halo-nav nav-tabs")}}>
	@foreach($builder->getChild('tab@array') as $tab)
	<li class="{{$tab->active}}"><a href="{{$tab->getUrl()}}" {{$tab->getData()}} data-target="#{{$tab->id}}" data-htoggle="tab"@if(!empty($tab->onDisplayContent))
		data-onDisplay="{{$tab->onDisplayContent}}" @endif>{{$tab->name}}</a></li>
	@endforeach
</ul>
<!-- Tab panes -->
<div class="halo-tab-content">
	@foreach($builder->getChild('tab@array') as $tab)
	<div class="tab-pane {{$tab->active}} {{$tab->class}}" id="{{$tab->id}}">
		{{$tab->content}}
	</div>
	@endforeach
</div>
@endUI

{{-- ///////////////////////////////////// Container UI ///////////////////////////////////// --}}
@beginUI('content')
@if(isset($builder->zone) && !empty($builder->zone))
<div {{$builder->getZone()}} {{$builder->getClass()}}>
@endif
	@foreach($builder->getChildren() as $child)
	{{$child->fetch()}}
	@endforeach
@if(isset($builder->zone) && !empty($builder->zone))
</div>
@endif
@endUI

{{-- ///////////////////////////////////// Raw Html UI ///////////////////////////////////// --}}
@beginUI('html')
{{$builder->html}}
@endUI

{{-- ///////////////////////////////////// Clearfix UI ///////////////////////////////////// --}}
@beginUI('clearfix')
<div class="clearfix"></div>
@endUI

{{-- ///////////////////////////////////// Photo gallary UI///////////////////////////////////// --}}
@beginUI('file_list')
<div class="halo-attachment">
	<ul class="halo-file-list">
		@foreach($builder->files as $file)
		<li class="halo-file-item">
			<a data-file-id="{{$file->id}}" title="{{{$file->filename}}}" href="{{$file->getFileURL()}}"><img
					class="halo-file-thumbnail" src="{{$file->getThumbnail(16)}}"/> {{{$file->filename}}}</a>
		</li>
		@endforeach
	</ul>
</div>
<script>

</script>
@endUI

{{-- ///////////////////////////////////// Icon UI ///////////////////////////////////// --}}
@beginUI('move_updown')
<a href="javascript:void(0)" onclick="{{$builder->onClick}}" title="{{{$builder->title}}}"> <i
		class="fa fa-caret-{{$builder->direction}}"></i></a>
@endUI

{{-- ///////////////////////////////////// Userpoint Setting UI ///////////////////////////////////// --}}
@beginUI('userpoint_settings')
<table class="table table-condensed table-hover">
	<thead>
	<th>{{__halotext('Rule Description')}}</th>
	<th align="center">{{__halotext('Point')}}</th>
	<th>{{__halotext('Enabled')}}</th>
	</thead>
	@foreach($builder->settings as $groupName => $group)
	<tr class="halo-notification-settings-group info @if(count($group) == 1) hidden @endif">
		<td>{{{ucfirst($groupName)}}}</td>
		<td align="center"></td>
		<td align="center"><input type="checkbox" class="halo-groupcheck-ctrl" data-halo-groupcheck="{{{$groupName}}}_s">
		</td>
	</tr>
	@foreach($group as $ruleName => $values)
	<tr>
		<td class="halo-notification-settings-type">{{{$values['d']}}}</td>
		<td>
			<input type="text" name="userpoint.rules[{{{$groupName}}}][{{{$ruleName}}}][p]" value="{{{$values['p']}}}">
		</td>
		<td align="center">
			<input type="hidden" name="userpoint.rules[{{{$groupName}}}][{{{$ruleName}}}][s]" value="0">
			<input type="checkbox" class="halo-groupcheck-ele" data-halo-groupcheck="{{{$groupName}}}_s"
			       name="userpoint.rules[{{{$groupName}}}][{{{$ruleName}}}][s]" value="1"
			@if($values['s'])checked@endif>
		</td>
	</tr>
	@endforeach
	@endforeach
</table>
@endUI


{{-- ///////////////////////////////////// Userpoint karma image UI ///////////////////////////////////// --}}
@beginUI('karam_old')
<?php
$max = isset($builder->max) ? $builder->max : 5;
$color = isset($builder->color) ? $builder->color : 'text-danger';
$iFull = isset($builder->iFull) ? $builder->iFull : 'star ' . $color;
$iHalf = isset($builder->iHalf) ? $builder->iHalf : 'star-half-o ' . $color;
$iEmpty = isset($builder->iEmpty) ? $builder->iEmpty : 'star-o ' . $color;
$nFull = (int)floor($builder->value);
$nHalf = (int)(ceil($builder->value) - $nFull);
$nEmpty = $max - $nFull - $nHalf;
?>
<span>
		<span class="halo-karma-badge">
			UserPoint {{$builder->value}}
		</span>
		<span class="halo-karma-userpoint">
			{{$builder->value}}
		</span>

		@for($i = 0; $i < $nFull;$i++)
			{{ HALOUIBuilder::icon($iFull) }}
		@endfor
		@for($i = 0; $i < $nHalf;$i++)
			{{ HALOUIBuilder::icon($iHalf) }}
		@endfor
		@for($i = 0; $i < $nEmpty;$i++)
			{{ HALOUIBuilder::icon($iEmpty) }}
		@endfor
	</span>
@endUI

{{-- ///////////////////////////////////// Userpoint karma image UI ///////////////////////////////////// --}}
@beginUI('karam')
<?php
$max = isset($builder->max) ? $builder->max : 5;
$color = isset($builder->color) ? $builder->color : 'text-danger';
$iFull = isset($builder->iFull) ? $builder->iFull : 'star ' . $color;
$iHalf = isset($builder->iHalf) ? $builder->iHalf : 'star-half-o ' . $color;
$iEmpty = isset($builder->iEmpty) ? $builder->iEmpty : 'star-o ' . $color;
$nFull = (int)floor($builder->level);
$nHalf = (int)(ceil($builder->level) - $nFull);
$nEmpty = $max - $nFull - $nHalf;
?>
<span class="halo-karma-badge badge" title="{{__halotext('User Point')}} ({{$builder->value}})">
<?php for($i = 0; $i < $nFull; $i++){ //full start display?>
	{{HALOUIBuilder::icon($iFull)}}
<?php } ?>
<?php for($i = 0; $i < $nHalf; $i++){ //haf start display?>
	{{HALOUIBuilder::icon($iHalf)}}
<?php } ?>
<?php for($i = 0; $i < $nEmpty; $i++){ //empty start display?>
	{{HALOUIBuilder::icon($iEmpty)}}
<?php } ?>
</span>
@endUI

{{-- ///////////////////////////////////// Focus menu item UI ///////////////////////////////////// --}}
@beginUI('focus_menu_item')
<div class="halo-btn-group halo-focus-menu-item {{$builder->class}}">
	{{$builder->getIcon()}}{{$builder->title}}
</div>
@endUI

{{-- ///////////////////////////////////// Focus menu item About me UI ///////////////////////////////////// --}}
@beginUI('about_me')
<div class="halo-field-list-wrapper">
@if($builder->user)
<?php $allowedPrivacy = HALOFieldModel::getAllowedPrivacy($builder->user);?>
@foreach ($builder->user->getProfileFields()->get() as $field)
<?php $haloField = $field->toHALOField();
	$respectPrivacy = $haloField->isEnabledPrivacy();
?>
@if(!$respectPrivacy || ($respectPrivacy && in_array($haloField->access, $allowedPrivacy)))
	{{$haloField->getReadableUI()}}
@endif
@endforeach
@endif
</div>
@endUI

{{-- ///////////////////////////////////// Usection wrapper UI ///////////////////////////////////// --}}
@beginUI('usection')
<div class="halo-section-container halo-section-{{$builder->name}}">
	@if($builder->title || !empty($builder->filters))
	<div class="halo-section-heading panel panel-default">
		<div class="panel-heading clearfix">
			<div class="halo-section-title halo-pull-left">
				<h5 class="panel-title halo-pg-result-{{Str::slug($builder->title)}}">{{{$builder->title}}}</h5>
			</div>
			@if(!empty($builder->filters))
				{{-- filter --}}
				<div class="halo-filter-wrapper">
					<form id="filter_form_{{HALOUtilHelper::uniqidInt()}}" class="filter_form" onsubmit="return false;">
						{{ HALOUIBuilder::getInstance('','filter_list',array('title'=>__halotext('Filters'),'icon'=>'filter',
						'filters'=>$builder->filters
						,'onChange'=>$builder->onChange))->fetch()}}
					</form>
				</div>
			@endif
		</div>
	</div>
	@endif
	<div class="halo-section-action halo-section-list-toolbar clearfix">{{$builder->actions}}</div>
	@if(!empty($builder->zone))
	<div {{$builder->getClass('halo-section-body')}} {{$builder->getZone()}}>
	@if(empty($builder->content))
	{{HALOResponse::getZoneContent($builder->zone)}}
	@else
	{{$builder->content}}
	@endif
</div>
{{HALOResponse::getZoneScript($builder->zone)}}
{{HALOResponse::getZonePagination($builder->zone)}}
@else
<div class="halo-section-body {{$builder->class}}" {{$builder->getZone()}}>
{{$builder->content}}
</div>
@endif
</div>
@endUI

{{-- ///////////////////////////////////// Usection actions UI ///////////////////////////////////// --}}
@beginUI('usection_action')
@foreach($builder->getChildren() as $child)
<a href="javascript:void(0);" {{$child->getClass()}} {{$child->getOnClick()}} @if($child->tooltip)title="{{{$child->tooltip}}}"@endif>{{$child->getIcon()}}{{{$child->title}}}</a>
@endforeach
@endUI

{{-- ///////////////////////////////////// Share UI ///////////////////////////////////// --}}
@beginUI('share_me')
<?php $settings = HALOUtilHelper::getSocialSettings(); ?>
@if(HALOUtilHelper::isSocialEnabled($settings))
<div class="halo-share-toggle">
	<button type="button" class="halo-btn halo-btn-default halo-dropdown-toggle" data-htoggle="dropdown">
		{{HALOUIBuilder::icon('share-square-o')}}{{ __halotext('Share')}}
	</button>
	<ul class="halo-dropdown-menu" role="menu">
		@foreach(HALOUtilHelper::getSettingList($settings,'social') as $secName => $secValue)
		@if($settings->getNsValue('social.'.$secName. '.shareEnable.value',0) && ($shareOptions =
		$settings->getNsValue('social.'.$secName . '.shareOptions')))
		<li class="halo-share-item-wrapper" data-name="{{$shareOptions->name}}" data-counter="{{$shareOptions->countFn}}"
		    data-url="{{$builder->target->getUrl()}}">{{$shareOptions->html}}
		</li>
		@endif
		@endforeach
		{{-- Share by Email --}}
        <?php
            $html = '';
            Event::fire($builder->target->getContext() . '.onShareEmail', array($builder->target, &$html));
        ?>
        @if ($html)
        <li class="halo-share-item-wrapper" data-name="email" data-counter="0" data-url="{{$builder->target->getUrl()}}"> {{$html}} </li>
        @endif
	</ul>
</div>
@endif
@endUI

{{-- ///////////////////////////////////// Dropdown button UI ///////////////////////////////////// --}}
@beginUI('btn_dropdown')
<div class="halo-btn-group">
	<button type="button" class="halo-btn halo-btn-default halo-dropdown-toggle" data-htoggle="dropdown">
		{{$builder->title}}<i class="fa fa-caret-down"></i>
	</button>
	@if($builder->hasChild('dropdown@array'))
	<ul class="halo-dropdown-menu" role="menu">
		@foreach($builder->getChild('dropdown@array') as $li)
		<li><a href="{{{ $li->getUrl() }}}" {{ $li->getClass() }} {{ $li->getOnClick() }} >{{$li->getIcon()}}{{{
			$li->title }}}</a></li>
		@endforeach
	</ul>
	@endif
</div>
@endUI

{{-- ///////////////////////////////////// video upload UI ///////////////////////////////////// --}}
@beginUI('video_upload')
<div class="halo-grid-wrapper halo-share-grid ">
	<div class="row">
		<div class="col-md-7 col-xs-12">
			{{HALOUIBuilder::getInstance('','form.text',array('name'=>'video_path','title'=> __halotext('Video URL'),'placeholder'=> __halotext('Enter your video URL from YouTube, Vimeo, DailyMotion, MetaCafe...'),'value'=>'','validation'=>'required|url'))->fetch()}}
			{{HALOUIBuilder::getInstance('','form.text',array('name'=>'video_title','title'=> __halotext('Title'),'placeholder'=> __halotext('Enter your video title'),'value'=>'','validation'=>'required'))->fetch()}}
			{{HALOUIBuilder::getInstance('','form.hidden',array('name'=>'video_vid','value'=>'','validation'=>'required|feedback:video_vid'))->fetch()}}
			{{HALOUIBuilder::getInstance('','form.textarea',array('name'=>'video_description','title'=> __halotext('Description'),'placeholder'=> __halotext('Enter your video description'),'value'=>''))->fetch()}}
		</div>

		<div class="col-md-5 col-xs-12">
			<div id="widget"></div>
			<div id="player"></div>

			<div class="halo-sharebox-embeded-player" {{-- data-feedback="video_vid" --}}>
				<div class="halo-sharebox-default-video">
					<div class="halo-sharebox-video-image">
						<i class="fa fa-film fa-5x"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endUI
{{-- ///////////////////////////////////// video player UI ///////////////////////////////////// --}}
@beginUI('video_player')
<div class="halo-attachment-video col-sm-12 no-float halo-center-block">
	<div class="halo-video-popup-wrapper halo-center-block">
		<div class="halo-video-popup">
			<a href="{{$builder->video->path}}" class="halo-video-thumbnail" data-video-id="{{$builder->video->id}}">
				<img class="img-thumbnail img-responsive" src="{{$builder->video->getThumbnail()}}"/>
				<span class="play-icon">{{HALOUIBuilder::icon('play-circle-o fa-5x')}}</span>
			</a>
		</div>
	</div>
</div>
@endUI

{{-- ///////////////////////////////////// video list UI///////////////////////////////////// --}}
@beginUI('video_list')
<div class="halo-video-list-wrapper" {{$builder->getZone()}}>
@if(count($builder->videos) == 0)
<p class="text-center"> {{__halotext('No videos found')}} </p>
@else
@foreach($builder->videos as $video)
<div class="halo-video-wrapper col-xs-6 col-sm-4 halo-list-wrapper-video">
	<div class="halo-video-popup-wrapper halo-center-block">
		<div class="halo-video-popup">
			{{HALOUIBuilder::getInstance('','label.label',array('target'=>$video,'position'=>'left'
			,'group_code'=>HALOConfig::get('video.label.status')
			,'prefix'=>'halo-label-status','mode'=>'single'))->fetch()}}
			<a href="{{$video->path}}" class="halo-video-thumbnail" data-video-id="{{$video->id}}">
				<img class="img-thumbnail img-responsive" src="{{$video->getThumbnail()}}"/>
				<span class="play-icon">{{HALOUIBuilder::icon('play-circle-o fa-5x')}}</span>
			</a>
		@if($video->canDelete())
		<button class="halo-remove-btn" onclick="halo.video.remove('{{$video->id}}')" title="{{__halotext('Delete this video')}}"><i class="fa fa-times"></i></button>
		@endif
		</div>
	</div>
</div>
@endforeach
@endif
</div>
@endUI

{{-- ///////////////////////////////////// Quota Settings UI ///////////////////////////////////// --}}
@beginUI('quota_settings')
<p><i>Leave empty for unlimited</i></p>
<table class="table table-condensed table-hover">
	<thead>
	<th>{{__halotext('Quota Key')}}</th>
	<th>{{__halotext('Per Minute')}}</th>
	<th>{{__halotext('Per Day')}}</th>
	<th>{{__halotext('Per Month')}}</th>
	<th>{{__halotext('Limit')}}</th>
	</thead>
	@foreach($builder->settings as $groupName => $group)
	@foreach($group as $quotaName => $values)
	<tr>
		<td>{{$values['l']}}</td>
		<td align="center">
			<input type="text" name="quota.default[{{{$groupName}}}][{{{$quotaName}}}][i]" value="{{{$values['i']}}}"
			       size="6">
		</td>
		<td align="center">
			<input type="text" name="quota.default[{{{$groupName}}}][{{{$quotaName}}}][d]" value="{{{$values['d']}}}"
			       size="6">
		</td>
		<td align="center">
			<input type="text" name="quota.default[{{{$groupName}}}][{{{$quotaName}}}][m]" value="{{{$values['m']}}}"
			       size="6">
		</td>
		<td align="center">
			<input type="text" name="quota.default[{{{$groupName}}}][{{{$quotaName}}}][g]" value="{{{$values['g']}}}"
			       size="6">
		</td>
	</tr>
	@endforeach
	@endforeach
</table>
@endUI
{{-- ///////////////////////////////////// Dropdown menu UI ///////////////////////////////////// --}}
@beginUI('dropdown_menu')
<ul class="halo-dropdown-menu" role="menu" {{$builder->getZone()}}>
@foreach($builder->getChildren() as $child)
<li class="{{$child->getClass()}}">{{$child->getIcon()}}<a role="menuitem" href="{{$child->getUrl()}}"
	{{$child->getOnClick()}}{{$child->getData()}}>{{{$child->title}}}</a></li>
@endforeach
</ul>
@endUI

{{-- ///////////////////////////////////// Slug Display UI ///////////////////////////////////// --}}
@beginUI('slug_display')
<span data-halozone="slug.{{$builder->target->zone}}">{{$builder->target->getUrl()}} <a href="javascript:void(0)"
        class="halo-btn halo-btn-default halo-btn-xs"
                                                                                      onclick="halo.util.editSlug('{{$builder->target->getContext()}}','{{$builder->target->id}}')">{{__halotext('Edit')}}</a></span>
@endUI

{{-- ///////////////////////////////////// Slug Edit UI ///////////////////////////////////// --}}
@beginUI('slug_edit')
<span data-halozone="slug.{{$builder->target->zone}}">{{$builder->url}}/<input name="slug"
                                                                            data-confirm="{{$builder->target->getSlug()}}"
                                                                            onblur="halo.util.onBlurSlug(this);"
                                                                             value="{{$builder->target->getSlug()}}"
                                                                             class="form-control input-sm halo-slug-edit"/> <a
		href="javascript:void(0)"
        class="halo-btn halo-btn-default halo-btn-xs"
		onclick="halo.util.saveSlug(this, '{{$builder->target->getContext()}}', '{{$builder->target->id}}')">{{__halotext('Done')}}</a></span>
@endUI

{{-- ///////////////////////////////////// Content Wrapper UI ///////////////////////////////////// --}}
@beginUI('div_wrapper')
<div {{$builder->getClass()}} {{$builder->getZone()}}>
{{$builder->html}}
</div>
@endUI

{{-- ///////////////////////////////////// Share link block preview UI ///////////////////////////////////// --}}
@beginUI('share_link_block_preview')
@if(isset($builder->info->oembed) && $builder->info->oembed)
<div {{$builder->getClass('halo-share-link-block halo-link-preview')}} {{$builder->getZone()}}>
	<div class="halo-url-preview clearfix">
		<div class="halo-url-preview-detail">
			<div class="halo-oembed-content">
			{{$builder->info->oembed}}
			</div>
		</div>
		<div class="halo-url-preview-close">
			<a onclick="halo.share.removeShareLinkBlock(this)"><i class="fa fa-times"></i></a>
		</div>
	</div>
	<input type="hidden" class="halo-preview-url" name="urlpreview[url]" value="{{$builder->url}}">
	<input type="hidden" class="halo-preview-url" name="urlpreview[oembed]" value="1">

</div>

@else
<?php 
	if(isset($builder->info->photos) && count($builder->info->photos) > 0){
		$photo = array_slice($builder->info->photos, 0, 1);
		$photo = array_shift($photo);
	} else {
		$photo = null;
	}

	$photoView = '';
	if($photo) {
		$photoViewOptions = HALOUtilHelper::getSharePhotoViewOptions($photo);
		$photoView = $photoViewOptions['view'];
		$photoUrl = $photoViewOptions['url'];
	}
?>
<div {{$builder->getClass('halo-share-link-block halo-link-preview')}} {{$builder->getZone()}}>
	<div class="halo-url-preview clearfix {{$photoView}}">
		@if($photo)
			<img class="img-responsive halo-center-block {{$photoView}}" src="{{$photoUrl}}" alt="{{$photo->caption}}"/>
		@endif
		<div class="halo-url-preview-detail">
			<h4 class="halo-url-preview-title">{{HALOOutputHelper::ellipsis($builder->info->title,100)}}</h4>
			<p class="halo-url-preview-description">
				{{HALOOutputHelper::ellipsis($builder->info->description,400)}}
			</p>
			<span class="text-muted">{{HALOOutputHelper::shortenUrl($builder->url)}}</span>
			<a class="halo-share-target" target="_blank" href="{{HALOOutputHelper::getExternalUrl($builder->url)}}"></a>
		</div>
		<div class="halo-url-preview-close">
			<a onclick="halo.share.removeShareLinkBlock(this)"><i class="fa fa-times"></i></a>
		</div>
	</div>
	<input type="hidden" class="halo-preview-url" name="urlpreview[url]" value="{{$builder->url}}">
	<input type="hidden" name="urlpreview[title]" value="{{{$builder->info->title}}}">
	<input type="hidden" name="urlpreview[description]" value="{{{$builder->info->description}}}">
	@if($photo)
	<input type="hidden" name="urlpreview[image_id]" value="{{{$photo->id}}}">
	<input type="hidden" name="urlpreview[image_url]" value="{{{$photoUrl}}}">
	<input type="hidden" name="urlpreview[image_view]" value="{{{$photoView}}}">
	@endif
</div>
@endif
@endUI

{{-- ///////////////////////////////////// Share link block content UI ///////////////////////////////////// --}}
@beginUI('share_link_block_content')
<?php $urlData = $builder->target->getParams('urlpreview',null);
	$urlData = is_array($urlData)?HALOObject::getInstance($urlData):$urlData;
	$viewMode = isset($urlData->image_view)?$urlData->image_view:'';
?>
@if($urlData)
@if(isset($urlData->oembed) && $urlData->oembed)
<div {{$builder->getZone()}}>
	<div {{$builder->getClass('halo-url-preview clearfix ' . $viewMode)}}>
		<div class="halo-url-preview-detail">
			<div class="halo-oembed-content">
			@if(($oembed = HALOBrowseHelper::fetchUrl($urlData->url)) && $oembed->oembed)
				{{$oembed->oembed}}
			@else
			<p> {{__halotext('Could not fetch url content')}}</p>
			@endif
			</div>
		</div>
		@if(method_exists($builder->target,'canEditPreview') && $builder->target->canEditPreview())
		<div class="halo-url-preview-close">
			<a onclick="halo.share.removeLinkPreview(this,'{{$builder->target->getContext()}}','{{$builder->target->id}}')" title="{{__halotext('Remove Preview')}}"><i class="fa fa-times"></i></a>
		</div>
		@endif
	</div>
</div>
@else
<div {{$builder->getZone()}}>
	<div {{$builder->getClass('halo-url-preview clearfix ' . $viewMode)}}>
		@if( isset($urlData->image_url) && !empty($urlData->image_url))
			<img class="img-responsive halo-center-block {{$viewMode}}" src="{{$urlData->image_url}}"/>
		@endif
		<div class="halo-url-preview-detail">
			@if(isset($urlData->title) && !empty($urlData->title))
			<h4 class="halo-url-preview-title">{{$urlData->title}}</h4>
			@endif
			@if(isset($urlData->description) && !empty($urlData->description))
			<p class="halo-url-preview-description">
				{{$urlData->description}}
			</p>
			@endif
			<span class="text-muted">{{HALOOutputHelper::shortenUrl($urlData->url)}}</span>
			<a class="halo-share-target" target="_blank" href="{{HALOOutputHelper::getExternalUrl($urlData->url)}}"></a>
		</div>
		@if(method_exists($builder->target,'canEditPreview') && $builder->target->canEditPreview())
		<div class="halo-url-preview-close">
			<a onclick="halo.share.removeLinkPreview(this,'{{$builder->target->getContext()}}','{{$builder->target->id}}')" title="{{__halotext('Remove Preview')}}"><i class="fa fa-times"></i></a>
		</div>
		@endif
	</div>
</div>
@endif
@endif
@endUI

{{-- ///////////////////////////////////// Meta field UI///////////////////////////////////// --}}
@beginUI('meta_field_layout')
	{{{trim($builder->title)}}}:{{trim($builder->value)}}
@endUI

{{-- ///////////////////////////////////// Push Mobile Menu UI ///////////////////////////////////// --}}
@beginUI('push_menu')
<nav class="cbp-spmenu cbp-spmenu-vertical cbp-spmenu-left" id="cbp-spmenu-s1">
    <h3>Menu</h3>
</nav>
@endUI

{{-- ///////////////////////////////////// alert UI ///////////////////////////////////// --}}
@beginUI('alert')
<div {{$builder->getClass()}}>
	<div class="alert alert-{{$builder->type}} fade in alert-block">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		{{ $builder->message }}
	</div>
</div>
@endUI

{{-- ///////////////////////////////////// Paging nave UI///////////////////////////////////// --}}
@beginUI('paging_nav')
<?php
	$count = $builder->count;
	$currentIdx = $builder->value;
	$nextIdx = ($currentIdx + 1) % $count;
	$nextIdx = ($nextIdx <= 0)? ($nextIdx + $count) : $nextIdx;

	$prevIdx = ($currentIdx - 1) % $count;
	$prevIdx = ($prevIdx <= 0)? ($prevIdx + $count) : $prevIdx;
?>
	<span {{$builder->getClass("halo-paging-nav")}} data-count="{{$builder->count}}">
		<a class="text-primary halo-paging-nav-prev" data-pagination-index="{{$prevIdx}}" href="javascript:void(0);" @if($builder->onPrev) onclick="{{$builder->onPrev}}" @endif title="{{__halotext('Prev')}}">{{HALOUIBuilder::icon('caret-left')}}</a>
		<span class="text-muted halo-paging-nav-current" data-value="{{$builder->value}}">{{$builder->title}}</span>
		<a class="text-primary halo-paging-nav-next" data-pagination-index="{{$nextIdx}}" href="javascript:void(0);" @if($builder->onNext) onclick="{{$builder->onNext}}" @endif title="{{__halotext('Next')}}">{{HALOUIBuilder::icon('caret-right')}}</a>
	</span>
@endUI
