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
			{{ HALOFilter::getDisplayFilterUI('admin.fields.index')->fetch() }}
		</div>
		<form name="halo-admin-form" id="halo-admin-form">
		<table class="table table-bordered table-hover" id="fields">
			<thead>
				<tr>
					<th width="1%">{{HALOPaginationPresenter::getSortableLink($fields,__halotext('ID'),'id')}}</th>
					<th width="1%">
						<input type="checkbox" name="toggle" value="" onclick="halo.form.checkAllBox(this)" />
						<span class="lbl"></span>
					</th>
					<th>{{HALOPaginationPresenter::getSortableLink($fields,__halotext('Name'),'name')}}</th>
					<th width="10%">{{HALOPaginationPresenter::getSortableLink($fields,__halotext('Field Code'),'id')}}</th>
					<th align="center" width="10%">{{HALOPaginationPresenter::getSortableLink($fields,__halotext('Type'),'type')}}</th>
				</tr>
			</thead>
			<tbody>
			@foreach ($fields as $field)
				<tr>
					<td>{{$field->id}}</td>
					<td>
						<input type="checkbox" name="cid[]" value="{{ $field->id }}" onclick="" />
						<span class="lbl"></span>
					</td>
					<td><a href="javascript:void(0)" onclick="halo.field.showEditFieldForm('{{$field->id}}')">{{{ $field->name }}}</a></td>
					<td>{{ $field->getFieldCode() }}</td>
					<td>{{ $field->type }}</td>
				</tr>
			@endforeach
			</tbody>
		</table>
		<div class="row">
		{{$fields->links('ui.pagination')}}
		</div>
		</form>
	</div>
</div>
@stop

{{-- Scripts --}}
@section('scripts')
@stop