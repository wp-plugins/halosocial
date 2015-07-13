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
		{{ HALOFilter::getDisplayFilterUI('admin.reports.index')->fetch() }}
	</div>
	<form name="halo-admin-form" id="halo-admin-form">
	<table class="table table-bordered table-hover" id="reports">
		<thead>
			<tr>
				<th width="1%">{{HALOPaginationPresenter::getSortableLink($reports,__halotext('ID'),'id')}}</th>
				<th width="1%">
					<input type="checkbox" name="toggle" value="" onclick="halo.form.checkAllBox(this)" />
					<span class="lbl"></span>
				</th>
				<th>{{HALOPaginationPresenter::getSortableLink($reports,__halotext('Reporter'),'actor_id')}}</th>
				<th>{{HALOPaginationPresenter::getSortableLink($reports,__halotext('Owner'),'owner_id')}}</th>
				<th width="10%">{{__halotext('Content')}}</th>
				<th align="center" width="10%">{{HALOPaginationPresenter::getSortableLink($reports,__halotext('Type'),'type')}}</th>
				<th align="center" width="10%">{{HALOPaginationPresenter::getSortableLink($reports,__halotext('Message'),'message')}}</th>
			</tr>
		</thead>
		<tbody>
		@foreach ($reports as $report)
			<tr>
				<td>{{$report->id}}</td>
				<td>
					<input type="checkbox" name="cid[]" value="{{ $report->id }}" onclick="" />
					<span class="lbl"></span>
				</td>
				<td>{{$report->actor->getDisplayLink()}}</td>
				<td>{{ $report->owner->getDisplayLink() }}</td>
				<td>{{ $report->reportable->getDisplayLink() }}</td>
				<td>{{ $report->type }}</td>
				<td>{{ $report->getMessage() }}</td>
			</tr>
		@endforeach
		</tbody>
	</table>
	<div class="row">
	{{$reports->links('ui.pagination')}}
	</div>
	</form>
	</div>
</div>
@stop

{{-- Scripts --}}
@section('scripts')
@stop