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
	<table class="table table-bordered table-hover" id="users">
		<thead>
			<tr>
				<th width="1%">{{HALOPaginationPresenter::getSortableLink($users,__halotext('ID'),'id')}}</th>
				<th width="1%">
					<input type="checkbox" name="toggle" value="" onclick="halo.form.checkAllBox(this)" />
					<span class="lbl"></span>
				</th>
				<th>{{__halotext('Username')}}</th>
				<th>{{HALOPaginationPresenter::getSortableLink($users,__halotext('IP Address'),'ip_addr')}}</th>
				<th>{{HALOPaginationPresenter::getSortableLink($users,__halotext('Login Since'),'created_at')}}</th>
				<th>{{HALOPaginationPresenter::getSortableLink($users,__halotext('Client Type'),'client_type')}}</th>
				<th>{{HALOPaginationPresenter::getSortableLink($users,__halotext('Browser Name'),'b_name')}}</th>
				<th>{{HALOPaginationPresenter::getSortableLink($users,__halotext('Browser Version'),'b_version')}}</th>
				<th>{{HALOPaginationPresenter::getSortableLink($users,__halotext('Browser Platform'),'b_platform')}}</th>
			</tr>
		</thead>
		<tbody>
		@foreach ($users as $user)
			<?php $haloUser = HALOUserModel::getUser($user->user_id)?>
			<tr>
				<td>{{$user->id}}</td>
				<td>
					<input type="checkbox" name="cid[]" value="{{ $user->id }}" onclick="" />
					<span class="lbl"></span>
				</td>
				<td>{{ $haloUser->getDisplayLink() }}</td>
				<td>{{ $user->ip_addr }}</td>
				<td>{{ $user->created_at->diffForHumans() }}</td>
				<td>{{ $user->client_type }}</td>
				<td>{{ $user->b_name }}</td>
				<td>{{ $user->b_version }}</td>
				<td>{{ $user->b_platform }}</td>
			</tr>
		@endforeach
		</tbody>		
	</table>
	<div class="row">
	{{$users->links('ui.pagination')}}
	</div>
	</form>
	</div>
</div>
@stop

{{-- Scripts --}}
@section('scripts')
@stop