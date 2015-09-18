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
<div class="panel panel-default">
	<div class="panel-heading page-header halo-admin-page-header">
		<h4>{{{ $title }}}</h4>
	</div>
	<div class="panel-body">
		<form name="halo-admin-form" id="halo-admin-form" method="post" action="{{URL::to('?app=admin&view=config&task=saveConfig')}}">
			<!-- Nav tabs -->
			<div class="halo-tab-overflow">
				<ul class="halo-nav nav-tabs halo-nav-hashlink">
				@foreach($config as $name => $group)
					<li @if( $name == 'global') class="active" @endif><a href="#{{$name}}" data-htoggle="tab">{{{$group->title}}}</a></li>
				@endforeach
				</ul>
				<!-- Tab panes -->
				<div class="halo-tab-content">
				@foreach($config as $name => $group)
					<div class="tab-pane halo-tab @if( $name == 'global') active @endif" id="{{$name}}">
						{{$group->builder->fetch()}}
					</div>
				@endforeach
				</div>
			</div>
			<input type="hidden" name="_token" value="{{csrf_token()}}"/>
		</form>
	</div>
</div>
@stop

{{-- Scripts --}}
@section('scripts')
@stop