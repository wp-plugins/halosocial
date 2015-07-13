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
	<div class="page-filters">
		{{ HALOFilter::getDisplayFilterUI('admin.plugins.index')->fetch() }}
	</div>
	<form name="halo-admin-form" id="halo-admin-form">
	<table class="table table-bordered table-hover" id="plugins">
		<thead>
			<tr>
				<th width="1%">{{HALOPaginationPresenter::getSortableLink($plugins,__halotext('ID'),'id')}}</th>
				<th width="1%">
					<input type="checkbox" name="toggle" value="" onclick="halo.form.checkAllBox(this)" />
					<span class="lbl"></span>
				</th>
				<th>{{__halotext('Name')}}</th>
				<th width="20%">{{__halotext('Description')}}</th>
				<th align="center" width="10%">{{HALOPaginationPresenter::getSortableLink($plugins,'Folder','folder')}}</th>
				<th align="center" width="10%">{{HALOPaginationPresenter::getSortableLink($plugins,'Element','element')}}</th>
				<th align="center" width="10%">{{HALOPaginationPresenter::getSortableLink($plugins,'Status','status')}}</th>
				<th align="center" width="10%">{{HALOPaginationPresenter::getSortableLink($plugins,'Ordering','ordering')}}</th>
			</tr>
		</thead>
		<tbody>
		@foreach ($plugins as $key => $plugin)
			<tr>
				<td>{{$plugin->id}}</td>
				<td>
					<input type="checkbox" name="cid[]" value="{{ $plugin->id }}" onclick="" />
					<span class="lbl"></span>
				</td>
				<td><a href="javascript:void(0)" >{{{ $plugin->getDisplayName() }}}</a></td>
				<td>{{{ $plugin->getDescription() }}}</td>
				<td>{{{ $plugin->folder }}}</td>
				<td>{{{ $plugin->element }}}</td>
				<td align="center">{{ $plugin->getStateHtml('status') }}</td>
				<td>{{{ $plugin->ordering }}}
					@if($key != 0){{HALOUIBuilder::getInstance('','move_updown',array('direction'=>'up','title'=>__halotext('Move Up'),'onClick'=>"halo.plugin.moveUp('".$plugin->id."')"))->fetch()}}@endif
					@if($key != (count($plugins) - 1)){{HALOUIBuilder::getInstance('','move_updown',array('direction'=>'down','title'=>__halotext('Move Down'),'onClick'=>"halo.plugin.moveDown('".$plugin->id."')"))->fetch()}}@endif
				</td>
			</tr>
		@endforeach
		</tbody>
	</table>
	<div class="row">
	{{$plugins->links('ui.pagination')}}
	</div>
	</form>
	</div>
</div>
@stop

{{-- Scripts --}}
@section('scripts')
@stop