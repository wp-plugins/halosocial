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
	<div class="page-roles">
		{{ HALOFilter::getDisplayFilterUI('admin.roles.index')->fetch() }}
	</div>
	<form name="halo-admin-form" id="halo-admin-form">
	<table class="table table-bordered table-hover" id="roles">
		<thead>
			<tr>
				<th width="1%">{{HALOPaginationPresenter::getSortableLink($roles,__halotext('ID'),'id')}}</th>
				@if(HALOConfig::isDev())
				<th width="1%">
					<input type="checkbox" name="toggle" value="" onclick="halo.form.checkAllBox(this)" />
					<span class="lbl"></span>
				</th>
				@endif
				<th>{{__halotext('Name')}}</th>
				<th>{{__halotext('Description')}}</th>
				@if(HALOConfig::isDev())
				<th width="1%">{{__halotext('Type')}}</th>
				@endif
				<th>{{__halotext('Permissions')}}</th>
			</tr>
		</thead>
		<tbody>
		@foreach ($roles as $role)
			<tr>
				<td>{{$role->id}}</td>
				@if(HALOConfig::isDev())
				<td>
					<input type="checkbox" name="cid[]" value="{{ $role->id }}" onclick="" />
					<span class="lbl"></span>
				</td>
				@endif
				<td>
				@if(HALOAuth::can('feature.acl'))
					<a href="javascript:void(0)" onclick="halo.role.showEditRoleForm('{{$role->id}}')">{{{ $role->getDisplayName() }}}</a>
				@else
					<b>{{{ $role->getDisplayName() }}}</b>
				@endif
				</td>
				<td>{{{ $role->description }}}</td>
				@if(HALOConfig::isDev())
				<td>{{{ $role->type }}}</td>
				@endif
				<td>
				@if(HALOAuth::can('feature.acl'))
				@foreach($role->permissions as $permission)
					{{HALOUIBuilder::getInstance('','form.tag',array('title'=>$permission->getDisplayName()
																	,'onRemove'=>"halo.role.removePermissionFromRole('".$permission->id."','".$role->id."')"))->fetch()}}
					
				@endforeach
				<span><a class="right" href="javascript:void(0)" onclick="halo.role.showEditPermissionToRole('{{$role->id}}')">{{__halotext('Assign')}}</a></span>
				@else
				@foreach($role->permissions as $permission)
					<span class="halo-text-inline badge">{{$permission->getDisplayName()}}</span>					
				@endforeach				
				@endif
				</td>
			</tr>
		@endforeach
		</tbody>
	</table>
	<div class="row">
	{{$roles->links('ui.pagination')}}
	</div>
	</form>
	</div>
</div>
@stop

{{-- Scripts --}}
@section('scripts')
@stop