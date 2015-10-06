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
				<th>{{HALOPaginationPresenter::getSortableLink($users,__halotext('Name'),HALO_USER_DISPLAY_NAME_COL)}}</th>
				<th>{{HALOPaginationPresenter::getSortableLink($users,__halotext('Username'),HALO_USER_USERNAME_COL)}}</th>
				<th>{{HALOPaginationPresenter::getSortableLink($users,__halotext('Email'),HALO_USER_EMAIL_COL)}}</th>
				<th width="1%">{{__halotext('Edit')}}</th>
				<th width="1%">{{__halotext('Allowed Access')}}</th>
				<th width="1%">{{__halotext('Confirmed')}}</th>
				<th width="20%">{{__halotext('Roles')}}</th>
			</tr>
		</thead>
		<tbody>
		@foreach ($users as $user)
			<tr>
				<td>{{$user->id}}</td>
				<td>
					<input type="checkbox" name="cid[]" value="{{ $user->id }}" onclick="" />
					<span class="lbl"></span>
				</td>
				<td>{{ $user->getDisplayLink() }}</td>
				<td>{{ $user->getUserName() }}</td>
				<td>{{ $user->getEmail() }}</td>
				<td><a href="{{URL::to('?app=admin&view=users&task=edit&uid='.$user->id)}}">{{__halotext('Edit')}}</a></td>
				<td align="center">{{ $user->getStateHtml('block') }}</td>
				<td align="center">{{ $user->user->getConfirmState()?HALOUIBuilder::icon('check-circle text-success') : HALOUIBuilder::icon('times-circle text-danger') }}</td>
				<td align="center">
				@foreach(HALOAuth::getUserRoles($user) as $role)
					{{HALOUIBuilder::getInstance('','form.tag',array('title'=>$role->getDisplayName(),'onClick'=>"halo.util.redirect('" . $role->getUrl() . "')"
																								,'onRemove'=>"halo.role.removeUserRole('".$user->id."','".$role->id."')"))->fetch()}}
				@endforeach
				<span><a class="right" href="javascript:void(0)" onclick="halo.role.editUserRoles('{{$user->id}}')">{{__halotext('Add')}}</a></span>
				</td>
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

<script>
	document.addEventListener("DOMContentLoaded", function(event) {
		halo.role.editUserRoles = function (userId) {
			var values = halo.util.getFormValues('popupForm');
			halo.jax.call("admin,croles", "EditUserRoles", userId, values);
		};
		halo.role.removeUserRole = function (userId,roleId) {
			halo.jax.call("admin,croles", "RemoveUserRole", userId, roleId);
		};
	});
</script>