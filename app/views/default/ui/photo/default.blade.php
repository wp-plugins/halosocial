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

{{-- ///////////////////////////////////// Upload photo UI///////////////////////////////////// --}}
@beginUI('photo.uploader')
<div id="plupload-upload-ui-{{$builder->id}}" data-uploader-id="{{$builder->id}}"
     class="row halo-photo-upload halo-uploader-wrapper" {{$builder->getData()}}>
<div id="drag-drop-area-{{$builder->id}}">
	<div class="drag-drop-inside-{{$builder->id}}">
		<div id="media-items-{{$builder->id}}">
			@if(!empty($builder->name))
			<input type="hidden" name="{{$builder->name}}" value="" {{$builder->getValidation()}}>
			@endif
			<div class="halo-media-items">
				<div class="col-md-2 col-xs-4 halo-photo-upload-wrap halo-media-item"
				     id="halo-upload-button-{{$builder->id}}">
					<div class="halo-dummy-square"></div>
					<div id="halo-plupload-upload-ui-{{$builder->id}}"
					     class="halo-add-more-wrap halo-media-item-wrapper halo-in-square" data-feedback="{{$builder->id}}">
						<a id="plupload-browse-button-{{$builder->id}}" type="button" value="Select Files"
						   class="halo-media-browse-button halo-add-more">
							<span class="fa fa-plus fa-3x"></span>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
@endUI

{{-- ///////////////////////////////////// Edit Photo UI///////////////////////////////////// --}}
@beginUI('photo.edit')

<div class="halo-photo-edit">
	<ul class="halo-nav nav-tabs halo-photo-upload-tabs">
		<li class="active"><a href="#halo-popup-upload2edit-photo" data-htoggle="tab">{{__halotext('Upload Photos')}}</a>
		</li>
		<li><a href="#halo-popup-select2edit-photo" data-htoggle="tab">{{__halotext('Select Album/Photo')}}</a></li>
	</ul>

	<!-- Tab panes -->
	<div class="halo-tab-content">
		<div class="tab-pane active" id="halo-popup-upload2edit-photo">
			<div class="halo-photo-edit-wrapper">
				<a href="#" class="no-float thumbnail halo-center-block @if($builder->size == 6) halo-photo-edit-avatar @else halo-photo-edit-cover @endif">
					<div class="halo-photo-view-port-wrap" data-viewport-ratio="{{$builder->viewRatio}}">
						<img class="img-dragtoedit"
						     src="@if(empty($builder->photo->id)) {{$builder->default}} @else {{ $builder->photo->getPhotoURL() }} @endif"
						     style="width:100%;top:0px;visibility: hidden">

						<div class="halo-instructions"><span> <i class="fa fa-arrows"></i>{{__halotext('Drag to reposition cover image')}}</span>
						</div>
					</div>
				</a>
			</div>
			<div class="halo-photo-edit-toolbar">
				<div class="no-float halo-center-block col-md-8 text-center">
					<span class="halo-pull-left">{{HALOUIBuilder::icon('minus')}}</span>
					{{__halotext('Zoom')}}
					<span class="halo-pull-right">{{HALOUIBuilder::icon('plus')}}</span>
					<input type="text" name="photoZoom" value="{{$builder->photoZoom}}">
					<input type="hidden" name="photoLeft" value="{{$builder->photoLeft}}">
					<input type="hidden" name="photoTop" value="{{$builder->photoTop}}">
					<input type="hidden" name="photoWidth" value="{{$builder->photoWidth}}">
					<input type="hidden" name="photoId" value="{{$builder->photo->id}}">

					<div data-halo-photo-upload-btn class="halo-photo-upload-status-wrapper"></div>
				</div>
			</div>

		</div>
		<div class="tab-pane" id="halo-popup-select2edit-photo">
			<?php $my = HALOUserModel::getUser();
			$my->load('albums', 'albums.photos');
			?>
			<div class="halo-select2edit-photo-outter" data-halozone="select2edit-album-photo">
				{{HALOUIBuilder::getInstance('','photo.album_listing',array('albums'=>$my->albums))->fetch()}}
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
</div>
@endUI


{{-- ///////////////////////////////////// Album listing UI///////////////////////////////////// --}}
@beginUI('photo.album_listing')
<div class="halo-popup-album-wrapper">
	@foreach($builder->albums as $album)
	<div class="halo-album-thumb-wrapper halo-list-thumb col-md-3 col-sm-2">
		<a href="javascript:void(0)" onclick="halo.photo.changeAlbumListing('{{$album->id}}')" class="thumbnail"
		   title=""><img class="" src="{{HALO_IMAGE_PLACE_HOLDER_S}}" data-src="{{$album->getCover(HALO_PHOTO_SMALLCOVER_SIZE)}}" alt=""></a>
		<span class="halo-album-name">{{$album->name}}</span>
		<span class="halo-album-counter">{{sprintf(__halotext('%s Photos'), $album->photos->count())}}</span>
	</div>
	@endforeach
	@if(count($builder->albums) == 0)
	<p class="text-muted text-center">{{__halotext('No Albums found')}}</p>
	@endif
</div>
@endUI

{{-- ///////////////////////////////////// Photo Album listing UI///////////////////////////////////// --}}
@beginUI('photo.photo_listing')
<div class="halo-popup-album-wrapper">
	<p class="text-center"><a href="javascript:void(0)" onclick="halo.photo.showAlbumListing()">{{__halotext('Back to albums')}}</a></p>
	@foreach($builder->photos as $photo)
	<div class="halo-album-thumb-wrapper halo-list-thumb col-md-2 col-sm-2">
		<a href="javascript:void(0)" onclick="halo.photo.selectListingPhoto('{{$photo->id}}')" class="thumbnail"
		   title=""><img class="" src="{{HALO_IMAGE_PLACE_HOLDER_S}}" data-src="{{$photo->getResizePhotoURL('center',HALO_PHOTO_AVATAR_SIZE * 3)}}" alt=""></a>
	</div>
	@endforeach
	@if(count($builder->photos) == 0)
	<p class="text-muted text-center">{{__halotext('No Photos found')}}</p>
	@endif
</div>
@endUI

{{-- ///////////////////////////////////// Albums view UI///////////////////////////////////// --}}
@beginUI('photo.albums_view')
<div class="halo-popup-album-wrapper" data-halozone="halo-album-view">
	@foreach($builder->albums as $album)
	<div class="halo-album-thumb-wrapper col-md-3 col-sm-2 halo-list-thumb">
		<a href="javascript:void(0)" onclick="halo.photo.showAlbumPhotos('{{$album->id}}')" class="thumbnail"
		   title=""><img class="" src="{{$album->getCover(HALO_PHOTO_SMALLCOVER_SIZE)}}" alt=""></a>
		<span class="halo-album-name">{{$album->name}}</span>
		<span class="halo-album-counter">{{sprintf(__halotext('%s Photos'), $album->photos->count())}}</span>

        <div class="halo-album-action-wrapper">
            {{HALOUIBuilder::getInstance('' ,'photo.album_responseActions',array('album'=>$album))->fetch()}}
        </div>
	</div>
	@endforeach
	@if(count($builder->albums) == 0)
	<p class="text-muted text-center">{{__halotext('No Albums found')}}</p>
	@endif
</div>
@endUI

{{-- ///////////////////////////////////// Photo Album view UI///////////////////////////////////// --}}
@beginUI('photo.photo_album_view')
<div class="halo-popup-album-wrapper" data-halozone="halo-albums-wrapper">
	<div class="halo-gallery-popup">
	@foreach($builder->photos as $photo)
	<div class="halo-album-thumb-wrapper halo-list-thumb col-sm-3 col-xs-4">
		<a href="{{$photo->getPhotoURL()}}" class="thumbnail" data-photo-id="{{$photo->id}}"
		   title="{{$photo->caption}}"><img class="" src="{{HALO_IMAGE_PLACE_HOLDER_S}}" data-src="{{$photo->getResizePhotoURL('center',HALO_PHOTO_AVATAR_SIZE * 3)}}"
											alt=""></a>
	</div>
	@endforeach
	@if(count($builder->photos) == 0)
	<p class="text-muted text-center">{{__halotext('No Photos found')}}</p>
	@endif
	</div>
</div>
@endUI

{{-- ///////////////////////////////////// Photo Album Header UI///////////////////////////////////// --}}
@beginUI('photo.album_view_header')
<div class="halo-album-header">
    <div class="halo-pull-left">
        <span class="halo-inline-text"> {{ $builder->album->name }} </span> | <a href="javascript:void(0)" title="{{ __halotext('Back to albums') }}" onclick="halo.photo.showUserAlbums({{ $builder->user_id }})">{{ __halotext('Back') }}</a>
    </div>
    <div class="halo-pull-right">
        {{ HALOUIBuilder::getInstance('', 'photo.album_responseActions', array('album' => $builder->album))->fetch() }}
    </div>
    <div class="clearfix"></div>
</div>
@endUI

{{-- ///////////////////////////////////// Photo Album Actions UI///////////////////////////////////// --}}
@beginUI('photo.album_responseActions')
<?php $album = $builder->album ?>
<?php $my = HALOUserModel::getUser(); ?>
<div class="halo-btn-group halo-user-response-actions halo-dropdown" data-halozone="album-response-actions-{{$album->id}}">
    <a class="halo-dropdown-toggle halo-btn halo-btn-xs halo-btn-nobg" data-htoggle="dropdown"><i class="fa fa-chevron-down"></i></a>
    <ul class="halo-dropdown-menu" role="menu">
        @if($my)
            <li class="">
                <a href="{{ URL::to('?view=photo&task=album&uid=' . $album->id) }}" onclick="" class="halo-btn halo-btn-default halo-btn-xs">{{HALOUIBuilder::icon('external-link-square')}}
                    {{__halotext('View Album')}}</a>
            </li>
            @if($album->canEdit())
                <li class="">
                    <a href="javascript:void(0)" onclick="halo.photo.editAlbumForm('{{$album->id}}')"
                       class="halo-btn halo-btn-default halo-btn-xs">{{HALOUIBuilder::icon('pencil-square-o')}}
                        {{__halotext('Edit Album')}}</a>
                </li>
                <li class="">
                    <a href="javascript:void(0)" onclick="halo.photo.deleteAlbum('{{$album->id}}')" class="halo-btn halo-btn-default halo-btn-xs">{{HALOUIBuilder::icon('trash-o')}}
                        {{__halotext('Delete Album')}}</a>
                </li>
            @endif
        @endif
    </ul>
</div>
@endUI

{{-- ///////////////////////////////////// Photo list UI///////////////////////////////////// --}}
@beginUI('photo.list')
<div class="halo-photo-list-wrapper clearfix" {{$builder->getZone()}} {{HALOUIBuilder::getPageAttr($builder->photos)}}>
@if(count($builder->photos) == 0)
<p class="text-center"> {{__halotext('No photos found')}} </p>
@else
@foreach($builder->photos as $photo)
<div class="halo-album-thumb-wrapper halo-list-thumb col-sm-3 col-xs-4">
	{{HALOUIBuilder::getInstance('','label.label',array('target'=>$photo,'position'=>'left'
	,'group_code'=>HALOConfig::get('photo.label.status')
	,'prefix'=>'halo-label-status','mode'=>'single'))->fetch()}}
	<a href="{{$photo->getPhotoURL()}}" class="thumbnail" data-photo-id="{{$photo->id}}"
	   title="{{$photo->caption}}"><img class="" src="{{$photo->getResizePhotoURL('center',HALO_PHOTO_AVATAR_SIZE * 3)}}"
	                                    alt=""></a>
	@if($photo->canDelete())
	<button class="halo-remove-btn" onclick="halo.photo.remove('{{$photo->id}}')" title="{{__halotext('Delete this photo')}}"><i class="fa fa-times"></i></button>
	@endif
</div>
@endforeach
@endif
</div>
@endUI

{{-- ///////////////////////////////////// Photo gallary UI///////////////////////////////////// --}}
@beginUI('photo.gallery')
<div class="halo-attachment">
	<div class="rg-gallery"
	{{$builder->getData()}}>
	<div class="rg-image-wrapper" id="rg-image-wrapper">
		<div class="rg-image-nav">
			<a href="#" class="rg-image-nav-prev"><i class="fa fa-chevron-left fa-2x"></i></a>
			<a href="#" class="rg-image-nav-next"><i class="fa fa-chevron-right fa-2x"></i></a>
		</div>
		<div class="rg-image"></div>
		<div class="rg-loading"></div>
		<div class="rg-caption-wrapper">
			<div class="rg-caption" style="display:none;">
				<p></p>
			</div>
		</div>
	</div>
	<?php $hideClass = (count($builder->photos) == 1 && $builder->getDataRole('mode') == 'fullview') ? 'hide' : ''; ?>
	<div class="rg-thumbs {{$hideClass}}">
		<!-- Elastislide Carousel Thumbnail Viewer -->
		<div class="es-carousel-wrapper clearfix">
			<div class="es-carousel">
				<ul class="">
					@foreach($builder->photos as $photo)
					<li><a data-photo-target="{{$photo->id}}" title="{{{$photo->caption}}}"
					       href="{{$photo->getPhotoURL()}}"><img class="carousel-img halo-center-block"
					                                             src="{{$photo->getResizePhotoURL('center',HALOConfig::get('photo.gallerySize',160))}}"
					                                             data-large="{{$photo->getResizePhotoURL(null,400)}}"
					                                             alt="{{{$photo->caption}}}"/></a></li>
					@endforeach
				</ul>
				<div class="halo-gallery-popup">
					@foreach($builder->photos as $photo)
					<a data-photo-id="{{$photo->id}}" title="{{{$photo->caption}}}"
					   href="{{$photo->getPhotoURL()}}"></a>
					@endforeach
				</div>
			</div>
		</div>
		<!-- End Elastislide Carousel Thumbnail Viewer -->
	</div>
	<!-- rg-thumbs -->
</div><!-- rg-gallery -->
</div>
@endUI

{{-- ///////////////////////////////////// Photo gallary thumbnail mode UI///////////////////////////////////// --}}
@beginUI('photo.gallery_thumbnail')
<div class="">
	<div {{$builder->getData()}} {{$builder->getClass('rg-gallery')}}>
	<div class="rg-thumbs">
		<!-- Elastislide Carousel Thumbnail Viewer -->
		<div class="es-carousel-wrapper clearfix">
			<div class="es-carousel">
				<ul class="">
					@foreach($builder->photos as $photo)
					<li><a data-photo-target="{{$photo->id}}" title="{{{$photo->caption}}}"
					       href="{{$photo->getPhotoURL()}}"><img class="carousel-img halo-center-block"
					                                             src="{{$photo->getResizePhotoURL('center',HALOConfig::get('photo.gallerySize',160))}}"
					                                             data-large="{{$photo->getResizePhotoURL(null,400)}}"
					                                             alt="{{{$photo->caption}}}"/></a></li>
					@endforeach
				</ul>
				<div class="halo-gallery-popup">
					@foreach($builder->photos as $photo)
					<a data-photo-id="{{$photo->id}}" title="{{{$photo->caption}}}"
					   href="{{$photo->getPhotoURL()}}"></a>
					@endforeach
				</div>
			</div>
		</div>
		<!-- End Elastislide Carousel Thumbnail Viewer -->
	</div>
	<!-- rg-thumbs -->
</div><!-- rg-gallery -->
</div>
@endUI

{{-- ///////////////////////////////////// Photo gallary UI///////////////////////////////////// --}}
@beginUI('photo.gallery_full')
{{$builder->copyAttributes('','photo.gallery')->setAttrs(array('data'=>array('mode'=>'fullview','showModes'=>'0')))->fetch()}}
@endUI

{{-- ///////////////////////////////////// Photo gallary UI///////////////////////////////////// --}}
@beginUI('photo.gallery_thumb')
{{$builder->copyAttributes('','photo.gallery')->setAttrs(array('data'=>array('mode'=>'carousel','showModes'=>'0')))->fetch()}}
@endUI


{{-- ///////////////////////////////////// Popup Photo Layout UI ///////////////////////////////////// --}}
@beginUI('photo.popup_content')
<?php $target = $builder->target; ?>
<div class="container-fluid halo-gallery-right-wrapper" {{$builder->getZone()}}>
<div class="media halo-activity">
	<div class="halo-stream-avatar halo-pull-left">
		<a class="" href="{{$target->owner->getUrl()}}">
			<img class="halo-avatar" data-actor="{{$target->owner->user_id}}" src="{{$target->owner->getThumb()}}"
			     alt="{{{$target->owner->getDisplayName()}}}">
		</a>
	</div>
	<div class="media-body halo-stream-content">
		<!-- head title -->
		<div class="halo-stream-headline">
			{{$target->owner->getDisplayLink('halo-stream-author')}}
			<!-- Show time -->
			<div>
				<span class="halo-timemark"><a href="javascript:void(0);" {{HALOUtilHelper::getDataUTime($target->created_at)}} title="{{{HALOUtilHelper::getDateTime($target->created_at)}}}">{{HALOUtilHelper::getElapsedTime($target->created_at)}}</a></span>
			</div>
		</div>

		<!-- message content -->
		<div class="halo-stream-message">
		</div>
	</div>
</div>
<div class="halo-tagged-users-wrapper hidden">
	{{__halotext('In this photo:')}}
</div>
<div class="halo-stream-content haloj-content-block">
	<!-- actions -->
	<div class="halo-stream-actions">
		<div class="">
			<!-- Show likes -->
                <span class="halo-like">
                    {{ HALOLikeAPI::getLikeDislikeHtml($target) }}
                </span>
				@if(HALOAuth::can('comment.create',$target))
				<span>
					<a href="javascript:void(0)" onclick="halo.activity.toggleComment(this)">{{HALOUIBuilder::icon('comment')}} {{__halotext('Comment')}} (<span class="comment-count">{{$target->comments->count()}}</span>)</a>
				</span>
				<!-- Show comment actor switcher-->	
					@if(method_exists($target,'listDisplayActors'))
						{{$target->listDisplayActors('comment.switchDisplayActors')}}
					@endif
				@endif
		</div>
	</div>
	<div class="halo-comment-popup-wrapper">
		<!-- comment -->
		<div class="halo-stream-respond">
			{{HALOUIBuilder::getInstance('','comment.wrapper',array('target' => $target, 'limit' =>	HALOConfig::get('global.commentDisplayLimit'), 
																'zone'=>'comment.'.$target->getZone()))->fetch()}}
		</div>
	</div>
</div>
</div>
@endUI
{{-- ///////////////////////////////////// Popup Photo Layout UI ///////////////////////////////////// --}}
@beginUI('photo.popup_actions')
<?php $my = HALOUserModel::getUser() ?>
<div class="halo-photo-actions" data-halozone="halo-photo-actions">
	@if($my)
	<div class="halo-tag-action halo-pull-right">
		<a href="javascript:void(0)" class="halo-photo-tag-btn halo-photo-begin-tagging"
		   onclick="halo.photo.startTagging()">{{__halotext('Tag Photo')}}</a>
		<a href="javascript:void(0)" class="halo-photo-tag-btn halo-photo-end-tagging hidden"
		   onclick="halo.photo.doneTagging()">{{__halotext('Done Tagging')}}</a>
	</div>
	@endif
</div>
@endUI

{{-- ///////////////////////////////////// Photo display UI///////////////////////////////////// --}}
@beginUI('photo.display')
<div class="halo-photo-display-wrapper halo-gallery-left">
	<img class="halo-photo-thumbnail halo-center-block thumbnail" src="{{$builder->photo->getPhotoURL()}}?random={{uniqid()}}"
	     alt=""/>

	<div class="halo-photo-tags"></div>
</div>
@endUI


{{-- ///////////////////////////////////// Photo display on stream UI///////////////////////////////////// --}}
@beginUI('photo.stream_thumbnail')
<div class="halo-attachment">
	<div class="halo-photo-stream-thumbnail-wrapper haloj-ellipsis haloj-truncated es-carousel" data-height="335">
		<ul class="halo-photo-stream-thumbnail-list halo-gallery-popup">
		@foreach($builder->photos as $photo)
			<li  class="halo-photo-stream-thumbnail-item">
				<a data-photo-id="{{$photo->id}}" data-photo-target="{{$photo->id}}" title="{{{$photo->caption}}}" href="{{$photo->getPhotoURL()}}">
					<img 
						src="{{HALO_IMAGE_PLACE_HOLDER_S}}"
						data-src="{{$photo->getResizePhotoURL('center',HALOConfig::get('photo.gallerySize',160))}}" 
						data-large="{{$photo->getResizePhotoURL(null, 400)}}" 
						alt="{{{$photo->caption}}}"/>
				</a>
			</li>
		@endforeach
		</ul>
	</div>
</div>
@endUI