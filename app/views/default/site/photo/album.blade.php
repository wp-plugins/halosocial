@extends('site.layouts.default')

{{-- Web site Title --}}
@section('title')
{{ __halotext('Album') }} {{{$album->name}}}
@parent
@stop
{{-- Content --}}
@section('content')
{{-- Album Content --}}
<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">{{sprintf(__halotext('%s\'s album: %s'),$album->owner->getDisplayLink(),$album->name)}}</h4>
    </div>
	<div class="panel-body">
		{{HALOUIBuilder::getInstance('','photo.gallery_full',array('photos'=>$album->photos))->fetch()}}
	</div>
</div>
{{-- Album Content --}}

@stop
