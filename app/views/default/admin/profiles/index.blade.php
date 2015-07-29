@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
	{{{ $title }}} :: @parent
@stop

{{-- Content --}}
@section('content')
<div class="panel panel-default">
	<div class="panel-heading page-header halo-admin-page-header">
		<h4>{{{ $title }}}</h4>
	</div>
	<div class="panel-body">
	<div class="page-filters">
		{{ HALOFilter::getDisplayFilterUI('admin.profiles.index')->fetch() }}
	</div>
	<form name="halo-admin-form" id="halo-admin-form">
	<table class="table table-bordered table-hover" id="profiles">
		<thead>
			<tr>
				<th width="1%">{{HALOPaginationPresenter::getSortableLink($profiles,__halotext('ID'),'id')}}</th>
				<th width="1%">
					<input type="checkbox" name="toggle" value="" onclick="halo.form.checkAllBox(this)" />
					<span class="lbl"></span>
				</th>
				<th width="66*">{{HALOPaginationPresenter::getSortableLink($profiles,__halotext('Name'),'name')}}</th>
				<th width="20%"></th>
				<th align="center" width="10%">{{HALOPaginationPresenter::getSortableLink($profiles,__halotext('Type'),'type')}}</th>
				<th width="1%">{{HALOPaginationPresenter::getSortableLink($profiles,__halotext('Default'),'default')}}</th>
				<th width="1%">{{HALOPaginationPresenter::getSortableLink($profiles,__halotext('Published'),'published')}}</th>
			</tr>
		</thead>
		<tbody>
		@foreach ($profiles as $profile)
			<tr>
				<td>{{$profile->id}}</td>
				<td>
					<input type="checkbox" name="cid[]" value="{{ $profile->id }}" onclick="" />
					<span class="lbl"></span>
				</td>
				<td><a href="javascript:void(0)" onclick="halo.profile.showEditProfileForm('{{$profile->id}}')">{{{ $profile->name }}}</a></td>
				<td>{{sprintf(__halotext('%s Field(s)'),count($profile->getFields))}} <a class="halo-btn halo-btn-sm halo-btn-primary" href="{{URL::to('?app=admin&view=profiles&task=fields&uid=' . $profile->id )}}" >{{__halotext('Assign Fields')}}</a></td>
				<td>{{ $profile->type }}</td>
				<td align="center">{{ $profile->getStateHtml('default') }}</td>
				<td align="center">{{ $profile->getStateHtml('published') }}</td>
			</tr>
		@endforeach
		</tbody>
	</table>
	<div class="row">
	{{$profiles->links('ui.pagination')}}
	</div>
	</form>
	</div>
</div>
@stop

{{-- Scripts --}}
@section('scripts')
@stop