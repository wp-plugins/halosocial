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
		{{ HALOFilter::getDisplayFilterUI('admin.permissions.index')->fetch() }}
	</div>
	<form name="halo-admin-form" id="halo-admin-form">
	<table class="table table-bordered table-hover" id="permissions">
		<thead>
			<tr>
				<th width="1%">{{HALOPaginationPresenter::getSortableLink($permissions,__halotext('ID'),'id')}}</th>
				<th width="1%">
					<input type="checkbox" name="toggle" value="" onclick="halo.form.checkAllBox(this)" />
					<span class="lbl"></span>
				</th>
				<th>{{HALOPaginationPresenter::getSortableLink($permissions,__halotext('Permission'),'name')}}</th>
				<th>{{__halotext('Source')}}</th>
				<th>{{__halotext('Description')}}</th>
				<th>{{__halotext('Assigned To Roles')}}</th>
			</tr>
		</thead>
		<tbody>
		@foreach ($permissions as $permission)
			<tr>
				<td>{{$permission->id}}</td>
				<td>
					<input type="checkbox" name="cid[]" value="{{ $permission->id }}" onclick="" />
					<span class="lbl"></span>
				</td>
				<td>
					@if(HALOConfig::isDev() && HALOAuth::can('feature.acl'))
					<a href="javascript:void(0)" onclick="halo.role.showEditPermissionForm('{{$permission->id}}')">{{{ $permission->getDisplayName() }}}</a>
					@else
					{{{ $permission->getDisplayName() }}}
					@endif
				</td>
				<td>{{{ $permission->getSourceName() }}}</td>
				<td>{{{ __halotext($permission->description) }}}</td>
				<td>
				@if(HALOAuth::can('feature.acl'))
				@foreach($permission->roles as $role)
					{{HALOUIBuilder::getInstance('','form.tag',array('title'=>$role->getDisplayName()
																								,'onRemove'=>"halo.role.removePermissionFromRole('".$permission->id."','".$role->id."')"))->fetch()}}
					
				@endforeach
				<span><a class="right" href="javascript:void(0)" onclick="halo.role.showEditRoleToPermission('{{$permission->id}}')">{{__halotext('Edit')}}</a></span>
				@else
				@foreach($permission->roles as $role)
					<span class="halo-text-inline badge">{{$role->getDisplayName()}}</span>					
				@endforeach				
				@endif
				</td>
			</tr>
		@endforeach
		</tbody>
	</table>
	<div class="row">
	{{$permissions->links('ui.pagination')}}
	</div>
	</form>
	</div>
</div>
@stop

{{-- Scripts --}}
@section('scripts')
@stop