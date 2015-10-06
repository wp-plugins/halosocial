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
	<form name="halo-admin-form" id="halo-admin-form" method="post" action="@if (isset($category)){{ URL::to('?app=admin&view=commonCategories&task=edit&uid=' . $category->id) }}@endif">
		<!-- CSRF Token -->
		<input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
		<input type="hidden" id="halo-form-repeat" name="repeat" value="0" />
		<!-- ./ csrf token -->
		<?php
		$tab = HALOUIBuilder::getInstance('','tabcontainer',array());

		$categories = HALOCategoryModel::buildCategoriesTree();
		$root = HALOCategoryModel::roots()->get()->first();
		$root->value = $root->id;
		$categoryOptions = empty($categories->_children)?array():$categories->_children;
		$categoryOptions = array_merge(array($root),$categoryOptions);
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
							->addUI('profile_id',HALOUIBuilder::getInstance('','form.select',array('name'=>'profile_id','id'=>'profile_id','value'=> $category->profile_id,
																	'title'=>__halotext('Category Profile'),
																	'options'=>HALOProfileModel::getProfileListOption('category',true)
																	)))
							->addUI('params.post_profile_id',HALOUIBuilder::getInstance('','form.select',array('name'=>'params[post_profile_id]','id'=>'post_profile_id','value'=> $category->getParams('post_profile_id'),
																	'title'=>__halotext('Profile For Posts in this Category'),
																	'options'=>HALOProfileModel::getProfileListOption('post',true)
																	)))
							->addUI('post_profile_name',HALOUIBuilder::getInstance('','form.alert',array('title'=>sprintf(__halotext('Calculated Post Profile for this category: %s'),$category->getPostProfileName()),
																										'type'=>'info'
																	)))
							->addUI('published',HALOUIBuilder::getInstance('','form.radio',array('name'=>'published','id'=>'published','value'=> $category->published,
																	'title'=>__halotext('Published'),
																	'options'=>array(array('value'=>'1','title'=>__halotext('Yes')),
																					array('value'=>'0','title'=>__halotext('No'))
																					)
																	)));
		$generalTab = HALOUIBuilder::getInstance('general','tab',array('name'=>__halotext('General'),'class'=>'halo-tab','active'=>'active','id'=>'tab-general','content'=>$generalTabContent->fetch()));

		$profileTabContent = '';
		foreach($profileFields as $field){
			$profileTabContent .= $field->toHALOField()->getEditableUI();
		}
		$profileTab = HALOUIBuilder::getInstance('profile','tab',array('name'=>__halotext('Profile'),'class'=>'halo-tab','active'=>'','id'=>'tab-profile','content'=>$profileTabContent));
		$tab->addUI('tab@array',$generalTab)
			->addUI('tab@array',$profileTab);
		?>
		{{$tab->fetch()}}
	</form>
	</div>
</div>
@stop

{{-- Scripts --}}
@section('scripts')
@stop
