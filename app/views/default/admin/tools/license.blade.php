@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
	{{{ $title }}} :: @parent
@stop

{{-- Toolbar --}}
@section('toolbar')
	<div class="row">

	</div>
@stop

{{-- Content --}}
@section('content')
<style>
.halo-starter-notice {display:none !important}
</style>
<div class="panel panel-default">
	<div class="panel-heading page-header halo-admin-page-header">
		<h4>{{{ $title }}}</h4>
	</div>
	<div class="panel-body">
		{{$builder->fetch()}}
		<div class="clearfix"></div>
		<div data-halozone="license_log"></div>
	</div>
</div>
@stop
