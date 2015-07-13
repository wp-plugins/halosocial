@extends('site.layouts.default')

{{-- Web site Title --}}
@section('title')
{{ __halotext('Photo') }} {{{$photo->caption}}}
@parent
@stop

{{-- OpenGraph Meta --}}
@section('ogp_meta')
<meta property="og:image" content="{{$photo->getPhotoURL()}}"/>
@stop

{{-- Content --}}
@section('content')
{{-- Photo Content --}}
<div class="halo-photo-view panel panel-default">
    <div class="panel-heading">
        <a class="halo-back-album halo-pull-right" href="{{$photo->album->getUrl()}}">{{__halotext('Back to Album')}}</a>
        <h4 class="panel-title">{{sprintf(__halotext('%s\'s photo: %s'),$photo->owner->getDisplayLink(),$photo->caption)}}</h4>
    </div>
	<div class="panel-body haloj-content-block">
		{{HALOUIBuilder::getInstance('','photo.display',array('my'=>$my,'photo'=>$photo))->fetch()}}
		<div class="halo-gallery-right">
			<div class="halo-tagged-users-wrapper hidden">
				{{__halotext('Tagged:')}}
			</div>
            <div class="">
            <!-- Show time -->
            <span class="halo-timemark"><a href="#" {{HALOUtilHelper::getDataUTime($photo->created_at)}} title="{{{HALOUtilHelper::getDateTime($photo->created_at)}}}">{{{HALOUtilHelper::getElapsedTime($photo->created_at)}}}</a></span>
            </div>
			<div class="photo-stream-actions">
					<!-- Show likes -->
                    <!-- Show Comment -->
                    @if(HALOAuth::can('comment.create',$photo))
                    <span class="halo-pull-right halo-comment"><a href="javascript:void(0);" onclick="halo.comment.editFocus('{{$photo->getCommentId()}}');">{{__halotext('Comment')}} (<span class="comment-count">{{$photo->comments->count()}}</span>)</a></span>
					<!-- Show comment actor switcher-->	
						@if(method_exists($photo,'listDisplayActors'))
							{{$photo->listDisplayActors('comment.switchDisplayActors')}}
						@endif
                    @endif
					<span class="halo-like">
						{{ HALOLikeAPI::getLikeDislikeHtml($photo) }}
					</span>
				<div class="clearfix"></div>
			</div>
			<div class="halo-comment-popup-wrapper">
				<!-- comment -->
				<div class="halo-stream-respond">
				{{HALOUIBuilder::getInstance('','comment.wrapper',array('target'=>$photo,'limit'=> HALOConfig::get('global.commentDisplayLimit'),'zone'=>'comment.'.$photo->getZone()))->fetch()}}
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	__haloReady(function() {
		@foreach($tags as $tag)
			<?php $tagData = new stdClass();
			$tagData->username = $tag["user"]->getDisplayLink();
			$tagData->userId = $tag["user"]->user_id;
			$tagData->x1 = $tag["params"]->get("x1",0);
			$tagData->y1 = $tag["params"]->get("y1",0);
			$tagData->x2 = $tag["params"]->get("x2",0);
			$tagData->y2 = $tag["params"]->get("y2",0);
			$tagData->removable = $tag["removable"];
			?>
			halo.photo.showTagObject({{json_encode($tagData)}});
		@endforeach
	});
</script>
{{-- Photo Content --}}

@stop
