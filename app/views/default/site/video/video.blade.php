@extends('site.layouts.default')

{{-- Web site Title --}}
@section('title')
{{ __halotext('Video') }} {{{$video->getTitle()}}}
@parent
@stop

{{-- OpenGraph Meta --}}
@section('ogp_meta')
<meta property="og:video" content="{{$video->->path}}"/>
@stop

{{-- Content --}}
@section('content')
{{-- Video Content --}}
<div class="panel panel-default">
	<div class="panel-body">
		<h4>{{sprintf(__halotext('%s\'s video: %s'),$video->owner->getDisplayLink(),$video->getTitle())}}</h4>
		{{HALOUIBuilder::getInstance('','video_player',array('video'=>$video))->fetch()}}
	</div>
</div>
{{-- Video Content --}}

@stop

