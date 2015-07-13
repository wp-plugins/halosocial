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
			{{ HALOFilter::getDisplayFilterUI('admin.cities.index')->fetch() }}
		</div>
		<form name="halo-admin-form" id="halo-admin-form">
		<table class="table table-bordered table-hover" id="cities">
			<thead>
				<tr>
					<th width="1%">{{HALOPaginationPresenter::getSortableLink($cities,__halotext('ID'),'id')}}</th>
					<th width="1%">
						<input type="checkbox" name="toggle" value="" onclick="halo.form.checkAllBox(this)" />
						<span class="lbl"></span>
					</th>
					<th>{{HALOPaginationPresenter::getSortableLink($cities,__halotext('Name'),'name')}}</th>
					<th width="10%">{{__halotext('District Count')}}</th>
			</tr>
			</thead>
			<tbody>
			@foreach (HALOUtilHelper::paginatorLoad($cities,array('districts')) as $city)
				<tr>
					<td>{{$city->id}}</td>
					<td>
						<input type="checkbox" name="cid[]" value="{{ $city->id }}" onclick="" />
						<span class="lbl"></span>
					</td>
					<td><a href="javascript:void(0)" onclick="halo.city.showEditCityForm('{{$city->id}}')">{{{ $city->getDisplayName() }}}</a></td>
					<td>{{ $city->districts->count() }}</td>
				</tr>
			@endforeach
			</tbody>
		</table>
		<div class="row">
		{{$cities->links('ui.pagination')}}
		</div>
		</form>
	</div>
</div>
@stop

{{-- Scripts --}}
@section('scripts')
@stop