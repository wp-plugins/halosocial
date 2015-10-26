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
	<form name="halo-admin-form" id="halo-admin-form" method="post" action="@if (isset($user)){{ URL::to('?app=admin&view=users&task=create') }}@endif">
		<!-- CSRF Token -->
		<input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
		<!-- ./ csrf token -->
		<?php
		$tab = HALOUIBuilder::getInstance('','tabcontainer',array());
		$generalTabContent = HALOUIBuilder::getInstance('','content',array())
							->addUI('username',HALOUIBuilder::getInstance('','form.text',array('name'=>'username','id'=>'username','value'=> Input::old('username', ''),
																	'title'=>__halotext('Username'),'placeholder'=>__halotext('Enter Username'), 'class'=>($errors->has('username') ? 'error' : ''))))
							->addUI('display_name',HALOUIBuilder::getInstance('','form.text',array('name'=>'name','id'=>'name','value'=> Input::old('name', ''),
																	'title'=>__halotext('Display Name'),'placeholder'=>__halotext('Enter Display Name'), 'class'=>($errors->has('display_name') ? 'error' : ''))))
							->addUI('email',HALOUIBuilder::getInstance('','form.text',array('name'=>'email','id'=>'email','value'=> Input::old('email', ''),
																	'title'=>__halotext('Email'),'placeholder'=>__halotext('Enter Email Address'), 'class'=>($errors->has('email') ? 'error' : ''))))
							->addUI('password',HALOUIBuilder::getInstance('','form.password',array('name'=>'password','id'=>'password','value'=> '',
																	'title'=>__halotext('Password'), 'class'=>($errors->has('password') ? 'error' : ''))))
							->addUI('password_confirm',HALOUIBuilder::getInstance('','form.password',array('name'=>'password_confirmation','id'=>'password_confirmation','value'=> '',
																	'title'=>__halotext('Confirm Password'), 'class'=>($errors->has('password_confirmation') ? 'error' : ''))))
							->addUI('profile_id',HALOUIBuilder::getInstance('','form.select',array('name'=>'profile_id','id'=>'profile_id','value'=> Input::old('profile_id', HALO_PROFILE_DEFAULT_USER_ID),
																	'title'=>__halotext('User Profile Type'),
																	'options'=>HALOProfileModel::getProfileListOption('user',true)
																	)))
							->addUI('point_count',HALOUIBuilder::getInstance('','form.text',array('name'=>'point_count','id'=>'point_count','value'=> Input::old('point_count', 0),
																	'title'=>__halotext('User Point'),'placeholder'=>__halotext('User Point'), 'class'=>($errors->has('point_count') ? 'error' : ''))))
							;
		$generalTabContent->addUI('block',HALOUIBuilder::getInstance('','form.radio',array('name'=>'block','id'=>'block','value'=> Input::old('block', 0),
																	'title'=>__halotext('Block User'), 'class'=>($errors->has('block') ? 'error' : ''),
																	'options'=>array(array('value'=>'1','title'=>__halotext('Yes')),
																					array('value'=>'0','title'=>__halotext('No'))
																					)
																	)));
		//role
		/*
			HALOUIBuilder::getInstance('','form.multiple_select',array('name'=>'roles','id'=>'roles','value'=>($mode=='create'?$selectedRoles:$user->currentRoleIds()),
																	'title'=>'Roles','row'=>true, 'class'=>($errors->has('roles')? 'error' : ''),
																	'options'=>$roles->map(function($role){
																								return array('value'=>$role->id,'title'=>$role->name);
																							})->toArray()
																	)
											)

		*/
		$generalTab = HALOUIBuilder::getInstance('general','tab',array('name'=>__halotext('General'),'class'=>'halo-tab','active'=>'active','id'=>'tab-general','content'=>$generalTabContent->fetch()));

		$profileTabContent = '<div class="halo-profile-fields">';
		foreach($profileFields as $field){
			$profileTabContent .= $field->toHALOField()->getEditableUI();
		}
		$profileTabContent .= '</div>';
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

