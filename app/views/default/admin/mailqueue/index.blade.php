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
	<form name="halo-admin-form" id="halo-admin-form">
	<table class="table table-bordered table-hover" id="emails">
		<thead>
			<tr>
				<th width="1%">{{HALOPaginationPresenter::getSortableLink($emails,__halotext('ID'),'id')}}</th>
				<th width="1%">
					<input type="checkbox" name="toggle" value="" onclick="halo.form.checkAllBox(this)" />
					<span class="lbl"></span>
				</th>
				<th>{{HALOPaginationPresenter::getSortableLink($emails,__halotext('To'),'to')}}</th>
				<th width="20%">{{__halotext('Subject')}}</th>
				<th align="center" width="10%">{{HALOPaginationPresenter::getSortableLink($emails,__halotext('Status'),'status')}}</th>
				<th align="center">{{HALOPaginationPresenter::getSortableLink($emails,__halotext('Template'),'template')}}</th>
				<th align="center">{{HALOPaginationPresenter::getSortableLink($emails,__halotext('Scheduled At'),'scheduled')}}</th>
			</tr>
		</thead>
		<tbody>
		@foreach ($emails as $email)
			<tr>
				<td>{{$email->id}}</td>
				<td>
					<input type="checkbox" name="cid[]" value="{{ $email->id }}" onclick="" />
					<span class="lbl"></span>
				</td>
				<td>{{ $email->to }}</td>
				<td>{{ $email->subject }}</td>
				<td align="center">{{ $email->getStateHtml('status') }}</td>
				<td>{{ $email->template }}</td>
				<td>{{ $email->scheduled }}</td>
			</tr>
		@endforeach
		</tbody>
	</table>
	<div class="row">
	{{$emails->links('ui.pagination')}}
	</div>
	</form>
	</div>
</div>
@stop

{{-- Scripts --}}
@section('scripts')
@stop