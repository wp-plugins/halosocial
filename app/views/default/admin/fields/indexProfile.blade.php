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
		<h4>
			{{{ $title }}} - {{ $profile->name }}
		</h4>
	</div>
	<div class="panel-body">
		<form name="halo-admin-form" id="halo-admin-form">
		<table class="table table-bordered table-hover" id="fields">
			<thead>
				<tr>
					<th width="1%">{{HALOPaginationPresenter::getSortableLink($fields,__halotext('ID'),'id')}}</th>
					<th width="1%">
						<input type="checkbox" name="toggle" value="" onclick="halo.form.checkAllBox(this)" />
						<span class="lbl"></span>
					</th>
					<th>{{HALOPaginationPresenter::getSortableLink($fields,'Name','name')}}</th>
					<th width="8%"></th>
					<th width="5%">{{HALOPaginationPresenter::getSortableLink($fields,'Field Code','id')}}</th>
					<th align="center" width="5%">{{HALOPaginationPresenter::getSortableLink($fields,__halotext('Type'),'type')}}</th>
					<th align="center" width="5%">{{HALOPaginationPresenter::getSortableLink($fields,__halotext('Ordering'),'ordering')}}</th>
					<th align="center" width="5%">{{HALOPaginationPresenter::getSortableLink($fields,__halotext('Required'),'required')}}</th>
					<th align="center" width="5%">{{HALOPaginationPresenter::getSortableLink($fields,__halotext('Published'),'published')}}</th>
					<th align="center" width="5%">{{__halotext('Highlight')}}</th>
					<th align="center" width="5%">{{__halotext('Enabled Privacy')}}</th>
				</tr>
			</thead>
			<tbody>
			@foreach ($fields as $key=>$field)
				<?php 	$pivotField = new HALOProfilefieldModel();
						$pivotField->required = $field->pivot->required;
						$pivotField->published = $field->pivot->published;
						$pivotField->id = $field->pivot->id;
						$enabledPrivacy = HALOParams::getInstance($field->pivot->params)->get('enablePrivacy',0);
						$highlight = HALOParams::getInstance($field->pivot->params)->get('highlight',0);
						$indicatorClass = '';
						
						switch($field->type) {
							case 'tab':
								$indicatorClass = 'success';
								break;
							case 'separator':
								$indicatorClass = 'warning';
								break;
						}
				?>
				<tr class="{{$indicatorClass}}">
					<td>{{$field->id}}</td>
					<td>
						<input type="checkbox" name="cid[]" value="{{ $field->id }}" onclick="" />
						<span class="lbl"></span>
					</td>
					<td><a href="javascript:void(0)" onclick="halo.profile.showAttachFieldForm('{{$profile->id}}','{{$field->id}}')">{{{ $field->name }}}</a></td>
					<td><a href="javascript:void(0)" onclick="halo.field.showEditFieldForm('{{$field->id}}')">{{ __halotext('Edit Field')}}</a></td>
					<td>{{ $field->getFieldCode() }}</td>
					<td>{{ $field->getFieldType() }}</td>
					<td align="center" >{{ $field->pivot->ordering }}
					@if($key != 0){{HALOUIBuilder::getInstance('','move_updown',array('direction'=>'up','title'=>__halotext('Move Up'),'onClick'=>"halo.profile.moveFieldUp('".$profile->id."','".$field->id."')"))->fetch()}}@endif
					@if($key != (count($fields) - 1)){{HALOUIBuilder::getInstance('','move_updown',array('direction'=>'down','title'=>__halotext('Move Down'),'onClick'=>"halo.profile.moveFieldDown('".$profile->id."','".$field->id."')"))->fetch()}}@endif</td>
					@if($field->isReadOnly())
						<td align="center"></td>
					@else
						<td align="center">{{ $pivotField->getStateHtml('required') }}</td>
					@endif
					<td align="center">{{ $pivotField->getStateHtml('published') }}</td>
					@if($field->isReadOnly())
						<td align="center"></td>
					@else
					<td align="center">{{($highlight)?__halotext('Yes'):__halotext('No')}}</td>
					@endif
					@if($field->isReadOnly())
						<td align="center"></td>
					@else
					<td align="center">{{($enabledPrivacy)?__halotext('Yes'):__halotext('No')}}</td>
					@endif
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