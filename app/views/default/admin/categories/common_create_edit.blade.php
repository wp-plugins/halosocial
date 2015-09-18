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
	<div class="panel-heading">
		<h4>{{{ $title }}}</h4>
	</div>
	<div class="panel-body">
	<form name="halo-admin-form" id="halo-admin-form" method="post" action="{{$actionUrl}}">
		<!-- CSRF Token -->
		<input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
		<input type="hidden" id="halo-form-repeat" name="repeat" value="0" />
		<!-- ./ csrf token -->
		<?php
		$tab = HALOUIBuilder::getInstance('','tabcontainer',array());

		$generalTabContent = HALOUIBuilder::getInstance('','content',array())
							->addUI('name',HALOUIBuilder::getInstance('','form.text',array('name'=>'name','id'=>'name','value'=> $category->name,
																	'title'=>__halotext('Category Name'),'placeholder'=>__halotext('Enter Category Name')
																	)))
							->addUI('description',HALOUIBuilder::getInstance('','form.textarea',array('name'=>'description','id'=>'description','value'=> $category->description,
																	'title'=>__halotext('Description'),'placeholder'=>__halotext('Enter Category Description')
																	)))
							->addUI('parent',HALOUIBuilder::getInstance('','form.tree_select',array('name'=>'parent_id','id'=>'parent_id','value'=> $category->parent_id,
																	'title'=>__halotext('Parent Category'),
																	'options'=> $categoryOptions
																	)))
							->addUI('published',HALOUIBuilder::getInstance('','form.radio',array('name'=>'published','id'=>'published','value'=> $category->published,
																	'title'=>__halotext('Published'),
																	'options'=>array(array('value'=>'1','title'=>__halotext('Yes')),
																					array('value'=>'0','title'=>__halotext('No'))
																					)
																	)));
		$generalTab = HALOUIBuilder::getInstance('general','tab',array('name'=>__halotext('General'),'class'=>'halo-tab','active'=>'active','id'=>'tab-general','content'=>$generalTabContent->fetch()));

		$tab->addUI('tab@array',$generalTab);
		?>
		{{$tab->fetch()}}
	</form>
	</div>
</div>
@stop

{{-- Scripts --}}
@section('scripts')
@stop
