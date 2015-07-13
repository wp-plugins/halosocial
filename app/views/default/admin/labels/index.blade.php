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
			{{ HALOFilter::getDisplayFilterUI('admin.labels.index')->fetch() }}
		</div>
		<form name="halo-admin-form" id="halo-admin-form">
		<table class="table table-bordered table-hover" id="labels">
			<thead>
				<tr>
					<th width="1%">{{HALOPaginationPresenter::getSortableLink($labelGroups,__halotext('ID'),'id')}}</th>
					<th width="1%">
						<input type="checkbox" name="toggle" value="" onclick="halo.form.checkAllBox(this)" />
						<span class="lbl"></span>
					</th>
					<th>{{HALOPaginationPresenter::getSortableLink($labelGroups,__halotext('Label Group Name'),'name')}}</th>
					<th align="center" width="10%">{{HALOPaginationPresenter::getSortableLink($labelGroups,__halotext('Type'),'type')}}</th>
					<th align="center" width="40%">{{__halotext('Labels')}}</th>
				</tr>
			</thead>
			<tbody>
			@foreach ($labelGroups as $labelGroup)
				<tr>
					<td>{{$labelGroup->id}}</td>
					<td>
						@if($labelGroup->group_code != 'HALO_SYSTEM_LABELS')
						<input type="checkbox" name="cid[]" value="{{ $labelGroup->id }}" onclick="" />
						@endif
						<span class="lbl"></span>
					</td>
					<td><a href="javascript:void(0)" onclick="halo.label.showEditLabelGroupForm('{{$labelGroup->id}}')">{{{ $labelGroup->name }}}</a></td>
					<td>@if($labelGroup->group_code != 'HALO_SYSTEM_LABELS'){{ $labelGroup->getType() }} @else {{__halotext('System')}} @endif</td>
					<td>
						<div class="halo-admin-label-list-wrapper">
						@if($labelGroup->group_code != 'HALO_SYSTEM_LABELS')
							@foreach($labelGroup->labels as $label)
								{{HALOUIBuilder::getInstance('','form.tag',array('title'=>$label->name
																				,'onClick'=>"halo.label.showEditLabelForm('".$label->id."','')"
																				,'onRemove'=>"halo.label.deleteLabel('".$label->id."')"))->fetch()}}
							@endforeach
						@else
						@foreach($labelGroup->labels as $label)
							{{HALOUIBuilder::getInstance('','form.tag',array('title'=>$label->name, 'class'=>'halo-system-label'
																			,'onClick'=>"halo.label.showEditLabelForm('".$label->id."','')"))->fetch()}}
						@endforeach						
						@endif
						</div>
						@if($labelGroup->group_code != 'HALO_SYSTEM_LABELS')
						<a href="javascript:void(0)" onclick="halo.label.showEditLabelForm('0','{{$labelGroup->id}}')">{{__halotext('Add Label')}}</a></td>
						@endif
				</tr>
			@endforeach
			</tbody>
		</table>
		<div class="row">
		{{$labelGroups->links('ui.pagination')}}
		</div>
		</form>
	</div>
</div>
@stop

{{-- Scripts --}}
@section('scripts')
@stop