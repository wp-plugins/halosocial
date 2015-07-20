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
			{{ HALOFilter::getDisplayFilterUI('admin.districts.index')->fetch() }}
		</div>
		<form name="halo-admin-form" id="halo-admin-form">
		<table class="table table-bordered table-hover" id="districts">
			<thead>
				<tr>
					<th width="1%">{{HALOPaginationPresenter::getSortableLink($districts,__halotext('ID'),'id')}}</th>
					<th width="1%">
						<input type="checkbox" name="toggle" value="" onclick="halo.form.checkAllBox(this)" />
						<span class="lbl"></span>
					</th>
					<th>{{HALOPaginationPresenter::getSortableLink($districts,__halotext('Name'),'name')}}</th>
					<th>{{HALOPaginationPresenter::getSortableLink($districts,__halotext('City'),'city_id')}}</th>
					<th>#{{__halotext('Location')}}</th>
			</tr>
			</thead>
			<tbody>
			@foreach (HALOUtilHelper::paginatorLoad($districts,array('city')) as $district)
				<tr>
					<td>{{$district->id}}</td>
					<td>
						<input type="checkbox" name="cid[]" value="{{ $district->id }}" onclick="" />
						<span class="lbl"></span>
					</td>
					<td><a href="javascript:void(0)" onclick="halo.location.showEditDistrictForm('{{$district->id}}')">{{{ $district->getDisplayName() }}}</a></td>
					<td>@if($district->city){{ $district->city->getDisplayName() }}@endif</td>
					<td>{{ $district->getLocationsCount() }}</td>
				</tr>
			@endforeach
			</tbody>
		</table>
		<div class="row">
		{{$districts->links('ui.pagination')}}
		</div>
		</form>
	</div>
</div>
@stop

{{-- Scripts --}}
@section('scripts')
@stop