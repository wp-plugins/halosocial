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
			{{ HALOFilter::getDisplayFilterUI('admin.categories.index')->fetch() }}
		</div>
		<form name="halo-admin-form" id="halo-admin-form">
		<table class="table table-bordered table-hover" id="categories">
			<thead>
				<tr>
					<th width="1%">{{__halotext('ID')}}</th>
					<th>{{__halotext('Name')}}</th>
					<th align="center" width="10%">{{__halotext('Parent')}}</th>
					<th align="center" width="10%">{{__halotext('Level')}}</th>
					<th align="center" width="10%">{{__halotext('Ordering')}}</th>
					<th width="1%">{{__halotext('Published')}}</th>
				</tr>
			</thead>
			<tbody>
			@foreach ($categories as $category)
				<tr>
					<td align="center">{{$category->id}}</td>
					<td>{{{ $category->name }}}</td>
					<td>@if($category->parent){{ $category->parent->getDisplayName() }}@endif</td>
					<td>{{ $category->getLevel() }}</td>
					<td align="center" >
					@if($category->canMoveLeft())
					{{HALOUIBuilder::getInstance('','move_updown',array('direction'=>'up','title'=>__halotext('Move Up'),'onClick'=>"halo.category.moveUp('".$category->id."','".$category->getContext()."')"))->fetch()}}
					@endif
					@if($category->canMoveRight())
					{{HALOUIBuilder::getInstance('','move_updown',array('direction'=>'down','title'=>__halotext('Move Down'),'onClick'=>"halo.category.moveDown('".$category->id."','".$category->getContext()."')"))->fetch()}}
					@endif
					</td>
					<td align="center">{{ $category->getStateHtml('published') }}</td>
				</tr>
			@endforeach
			</tbody>
		</table>
		<div class="row">
		{{$categories->links('ui.pagination')}}
		</div>
		</form>
	</div>
</div>
@stop

{{-- Scripts --}}
@section('scripts')
@stop