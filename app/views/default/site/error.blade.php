@extends('site.layouts.default')

{{-- Web site Title --}}
@section('title')
	{{$title}}
@parent
@stop
{{-- Content --}}
@section('content')
<div class="alert alert-danger alert-block">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	<h4>Error</h4>
    @foreach ($messages->all(':message') as $message)
		<div>{{{$message}}}</div>
	@endforeach
</div>	
@stop

