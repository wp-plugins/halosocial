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

{{-- ///////////////////////////////////// Comment wrapper UI ///////////////////////////////////// --}}
@beginUI('comment.wrapper')
<div
	class="halo-stream-comment-wrapper halo-comment-content @if(count($builder->target->comments) === 0 && !HALOConfig::get('global.commentDisplayInput', 1)) hidden @endif" {{$builder->getZone()}}>
	@if(count($builder->target->comments) > $builder->limit)
		<div class="halo-stream-comment-item">
			<a href="#more" onclick="halo.comment.viewall('{{$builder->target->id}}','{{$builder->target->getContext()}}')">{{sprintf(__halotext('view %s more'), count($builder->target->comments) - $builder->limit)}} </a>
		</div>
	@endif
@foreach($builder->target->comments->slice(-$builder->limit) as $comment)
{{HALOUIBuilder::getInstance('','comment.entry',array('comment'=>$comment,'zone'=>$comment->getZone()))->fetch()}}
@endforeach
@if(HALOAuth::can('comment.create',$builder->target))
{{HALOUIBuilder::getInstance('','comment.input',array('id'=>$builder->target->getCommentId(),'actor'=>$my,'target'=>$builder->target,
'zone'=>'comment_input.'.$builder->target->getZone()))->fetch()}}
@endif</div>
@endUI

{{-- ///////////////////////////////////// Comment Entry UI ///////////////////////////////////// --}}
@beginUI('comment.entry')
<div class="halo-stream-comment-item clearfix" id="comment_{{$builder->comment->id}}" data-comment-id="{{$builder->comment->id}}" {{$builder->getZone()}}>
<div class="halo-comment-avatar halo-pull-left">
	<a class="thumbnail" href="{{$builder->comment->getDisplayActor()->getUrl()}}">
		<img class="halo-avatar" data-actor="{{$builder->comment->actor->user_id}}"
		     src="{{$builder->comment->getDisplayActor()->getAvatar(HALO_PHOTO_THUMB_SIZE)}}"
		     alt="{{{$builder->comment->getDisplayActor()->getDisplayName()}}}">
	</a>
</div>
<div class="halo-comment-body">
	<div class="halo-comment-message" data-halozone="comment.message.{{$builder->comment->id}}">
		<span class="halo-comment-headline">{{$builder->comment->getDisplayActor()->getDisplayLink('halo-stream-author')}}</span>
		{{HALOUIBuilder::getInstance('','ellipsis',array('message' =>$builder->comment->getMessage(), 
														'data' =>array('height' => '60')))->fetch()}} 
	</div>
	@if($builder->comment->getParams('photo_id',null) && $builder->comment->photo->count())
		<div class="halo-comment-attachment halo-gallery-popup">
			<a data-photo-id="{{$builder->comment->getPhoto()->id}}" title="{{{$builder->comment->getPhoto()->caption}}}" 
			href="{{$builder->comment->getPhoto()->getPhotoURL()}}">
				<img class="" src="{{$builder->comment->getPhotoUrl()}}">
			</a>
		</div>
		@if(HALOAuth::can('comment.edit',$builder->comment))
		<div class="halo-comment-photo-remove">
			<a href="javascript:void(0)" onclick="halo.comment.removePhoto(this)" title="{{__halotext('Remove Photo')}}"><i class="fa fa-times"></i>{{__halotext('Remove Photo')}}</a>
		</div>
		@endif
	@else
	{{HALOUIBuilder::getInstance('','share_link_block_content',array('target'=>$builder->comment,'class'=>'preview-transparent'))->fetch()}}
	@endif
	<div class="halo-comment-actions halo-stream-actions">
		{{ HALOUIBuilder::getInstance('','comment.action',array('comment'=>$builder->comment))->fetch()}}
	</div>
	<div class="halo-comment-reply hidden"></div>
</div>
</div>
@endUI

{{-- ///////////////////////////////////// Comment Count Entry UI ///////////////////////////////////// --}}
@beginUI('comment.count')
<span class="halo-comment-count" {{$builder->getZone()}}>{{HALOUIBuilder::icon('comment')}} {{$builder->target->comments->count()}} {{__halotext('Comment')}}</span>
@endUI

{{-- ///////////////////////////////////// Comment Action UI ///////////////////////////////////// --}}
@beginUI('comment.action')
<div class="halo-pull-left">
	<!-- Show likes -->
		<span class="halo-like">
			{{ HALOLikeAPI::getLikeDislikeHtml($builder->comment) }}
		</span>
	<!-- Show time -->
	<span class="halo-timemark"><a href="javascript:void(0);" {{HALOUtilHelper::getDataUTime($builder->comment->created_at)}} title="{{HALOUtilHelper::getDateTime($builder->comment->created_at)}}">{{HALOUtilHelper::getElapsedTime($builder->comment->created_at)}}</a></span>
</div>
@if(HALOAuth::can('comment.edit',$builder->comment) || HALOAuth::can('comment.delete',$builder->comment))
<div class="halo-pull-right">
	<div class="halo-btn-group halo-dropdown">
		<a class="halo-dropdown-toggle halo-btn halo-btn-xs halo-btn-nobg" data-htoggle="dropdown"><i class="fa fa-chevron-down"></i></a>
		<ul class="halo-dropdown-menu" role="menu">
			@if(HALOAuth::can('comment.edit',$builder->comment))
			<!-- Show Edit -->
			<li><a href="javascript:void(0);" onclick="halo.comment.edit('{{$builder->comment->id}}')"
			       title="{{__halotext('Edit')}}">{{__halotext('Edit')}}</a></li>
			@endif
			@if(HALOAuth::can('comment.delete',$builder->comment))
			<!-- Show Delete -->
			<li><a href="javascript:void(0)" onclick="halo.comment.delete('{{$builder->comment->id}}')"
			       title="{{__halotext('Delete')}}">{{__halotext('Delete')}}</a></li>
			@endif
			</li>
		</ul>
	</div>
</div>
@endif
@endUI


{{-- ///////////////////////////////////// Comment Input UI ///////////////////////////////////// --}}
@beginUI('comment.input')
<?php $uniqid = uniqid();?>
<div
	class="halo-stream-comment-item halo-comment-input @if(!HALOConfig::get('global.commentDisplayInput', 1))hidden@endif" {{$builder->getZone()}}>
<div class="halo-comment-avatar halo-pull-left">
	<a class="thumbnail" href="{{$builder->actor->getUrl()}}">
		<img class="halo-avatar" data-actor="{{$builder->actor->user_id}}"
		     src="{{$builder->actor->getThumb(HALO_PHOTO_SMALL_SIZE)}}" alt="{{{$builder->actor->getDisplayName()}}}">
	</a>
</div>
<div class="halo-comment-body">
	<form id="comment_form_{{$builder->id}}" class="comment_form">
		<div class="halo-input-group-append">
			<textarea rows="1" data-resetable data-raw-input="message_{{$builder->id}}"
					  data-iconlibrary="fa" data-height="60" data-uid={{$uniqid}} class="input-control halo-comment-box form-control"
					  name="comment_{{$builder->id}}" placeholder="{{__halotext('Write a comment...')}}"></textarea>
			<button type="button" data-upload-uid="{{$uniqid}}" class="haloj-uploader-btn halo-input-icon">
				<i class="fa fa-camera"></i>
			</button>
		</div>
		<div class="haloc-comment-preview haloj-comment-preview" data-upload-holder-uid="{{$uniqid}}"></div>
		<input type="hidden" data-resetable name="message" id="message_{{$builder->id}}" value=""/>
		<input type="hidden" name="target_id" value="{{$builder->target->id}}"/>
		<input type="hidden" name="context" value="{{$builder->target->getContext()}}"/>
	</form>
</div>
</div>
@endUI

{{-- ///////////////////////////////////// Comment Edit UI ///////////////////////////////////// --}}
@beginUI('comment.edit')
<div class="media halo-stream-comment-item"
     id="comment_edit_{{$builder->comment->id}}" {{$builder->getZone()}} style="display:none;">
<div class="halo-comment-avatar halo-pull-left">
	<a class="thumbnail" href="{{$builder->actor->getUrl()}}">
		<img class="halo-avatar" data-actor="{{$builder->actor->user_id}}"
		     src="{{$builder->actor->getThumb(HALO_PHOTO_SMALL_SIZE)}}" alt="{{{$builder->actor->getDisplayName()}}}">
	</a>
</div>
<div class="halo-comment-body">
	<form id="comment_edit_form_{{$builder->id}}">
		<textarea data-resetable data-raw-input="message_edit_{{$builder->id}}"
		          data-iconlibrary="fa" data-height="60" data-uid={{uniqid()}} class="halo-comment-box"
		          name="comment_{{$builder->id}}" placeholder="{{__halotext('Write a comment...')}}"></textarea>
		<input type="hidden" data-resetable name="message" id="message_edit_{{$builder->id}}"
		       value="{{{$builder->comment->message}}}"/>
		<input type="hidden" name="comment_id" value="{{$builder->id}}"/>
	</form>
</div>
</div>
@endUI

@beginUI('comment.switchDisplayActors')
	@if(count($builder->options))
	<span class="halo-comment-actor-switcher">
		<select class="selectpicker">
		@foreach($builder->options as $option)
			@if(isset($option['actor']))
			<?php 
				$dataContent = '<img class="halo-pull-left halo-inline-img" src="' . $option['actor']->getAvatar(20) . '">';
				$isActive = ($builder->active->getContext() == $option['actor']->getContext()) && ($builder->active->id == $option['actor']->id);
				$displayName = $option['actor']->getDisplayName();
			?>
			<option @if($isActive) selected @endif value="{{$option['actor']->getContext()}}.{{$option['actor']->id}}" 
				data-content="{{str_replace('"', "'", $dataContent . '<span>' . $displayName . '</span>')}}" title="{{sprintf(__halotext('Commenting as %s'), $displayName)}}"></option>
			@endif
		@endforeach
		</select>
		<input type="hidden" name="comment_display_context" value="{{$builder->active->getContext()}}"/>
		<input type="hidden" name="comment_display_id" value="{{$builder->active->id}}"/>
	</span>
	@endif
@endUI