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
			{{ HALOFilter::getDisplayFilterUI('admin.filters.index')->fetch() }}
		</div>
		<form name="halo-admin-form" id="halo-admin-form">
		<table class="table table-bordered table-hover" id="filters">
			<thead>
				<tr>
					<th width="1%">{{HALOPaginationPresenter::getSortableLink($filters,'ID','id')}}</th>
					<th width="1%">
						<input type="checkbox" name="toggle" value="" onclick="halo.form.checkAllBox(this)" />
						<span class="lbl"></span>
					</th>
					<th>{{HALOPaginationPresenter::getSortableLink($filters,'Name','name')}}</th>
					<th align="center" width="10%">{{HALOPaginationPresenter::getSortableLink($filters,'Type','type')}}</th>
					<th width="30%">{{HALOPaginationPresenter::getSortableLink($filters,'Description','description')}}</th>
					<th align="center" width="10%">{{HALOPaginationPresenter::getSortableLink($filters,__halotext('Ordering'),'ordering')}}</th>
					<th width="1%">{{HALOPaginationPresenter::getSortableLink($filters,__halotext('Published'),'published')}}</th>
				</tr>
			</thead>
			<tbody>
			@foreach ($filters as $key=>$filter)
				<tr>
					<td>{{ $filter->id }}</td>
					<td>
						<input type="checkbox" name="cid[]" value="{{ $filter->id }}" onclick="" />
						<span class="lbl"></span>
					</td>
					<td><a href="javascript:void(0)" onclick="halo.filter.showEditFilterForm('{{$filter->id}}')">{{{ $filter->getDisplayName() }}} </a></td>
					<td>{{ $filter->type }}</td>
					<td>{{ $filter->description }}</td>
					<td align="center" >{{ $filter->ordering }}
					@if($key != 0){{HALOUIBuilder::getInstance('','move_updown',array('direction'=>'up','title'=>__halotext('Move Up'),'onClick'=>"halo.filter.moveUp('".$filter->id."')"))->fetch()}}@endif
					@if($key != (count($filters) - 1)){{HALOUIBuilder::getInstance('','move_updown',array('direction'=>'down','title'=>__halotext('Move Down'),'onClick'=>"halo.filter.moveDown('".$filter->id."')"))->fetch()}}@endif</td>
					<td align="center">{{ $filter->getStateHtml('published') }}</td>
				</tr>
			@endforeach
			</tbody>
		</table>
		<div class="row">
		{{$filters->links('ui.pagination')}}
		</div>
		</form>
	</div>
</div>
@stop

{{-- Scripts --}}
@section('scripts')
@stop