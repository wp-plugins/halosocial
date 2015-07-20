@extends('site.layouts.default')

{{-- Web site Title --}}
@section('title')
{{{ __halotext('User Profile') }}}
@parent
@stop

{{-- Content --}}
@section('content')

{{-- Focus content --}}
@include('site/user/profile_focus')
{{-- End Focus content --}}

{{-- Edit Profile content --}}
<div class="panel panel-default">
	<div class="panel-heading">
		{{__halotext('Edit Profile')}} | <a href="{{$user->getUrl()}}"> {{__halotext('Finish Editing')}}</a>
	</div>
	<div class="panel-body">
		<form name="halo-admin-form" id="halo-admin-form" method="post" action="@if (isset($user)){{ URL::to('?view=user&task=edit&uid=' . $user->id) }}@endif">
			<!-- CSRF Token -->
			<input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
			<!-- ./ csrf token -->
			<?php
			$profileTypeHtml = '<label>' . __halotext('Your current profile type is: ') .'</label> <span>'.$user->getProfile()->first()->name.'</span>';
			if (HALOAuth::can('user.changeProfile',$user) && count(HALOProfileModel::getProfileListOption('user')) > 1) {
				$profileTypeHtml .= '[<a href="javascript:void(0);" onclick="halo.user.showChangeProfile();">'.__halotext('Change').'</a>]</span>';
			}
			$tab = HALOUIBuilder::getInstance('','tabcontainer',array());
			$generalTabContent = HALOUIBuilder::getInstance('','content',array())
								->addUI('profile_type',HALOUIBuilder::getInstance('','form.raw',array('content'=>$profileTypeHtml)))
								->addUI('username',HALOUIBuilder::getInstance('','form.text',array('name'=>'username','id'=>'username','readonly'=>'readonly','value'=> $user->getUserName(),
																		'title'=>__halotext('Username'),'placeholder'=>__halotext('Enter Username'))))
								->addUI('display_name',HALOUIBuilder::getInstance('','form.text',array('name'=>'display_name','id'=>'display_name','value'=> Input::old('display_name', isset($user) ? $user->getDisplayName() : null),
																		'title'=>__halotext('Display Name'),'placeholder'=>__halotext('Display Name'),'validation'=>'required', 'class'=>($errors->has('display_name') ? 'error' : ''))));
			//if seo is enabled
			if(HALOConfig::seo()){
			$generalTabContent->addUI('slug',HALOUIBuilder::getInstance('','form.static',array('text'=> $user->getSlugDisplay(),'title'=>__halotext('Profile vanity URL'))));
			}
			$generalTabContent->addUI('email',HALOUIBuilder::getInstance('','form.text',array('name'=>'email','id'=>'email','readonly'=>'readonly','value'=> $user->getEmail(),
																		'title'=>__halotext('Email'),'placeholder'=>__halotext('Enter Email Address'))))
								->addUI('change_password',HALOUIBuilder::getInstance('','form.link',array('name'=>'change_password','value'=> '','url'=>'javascript','onClick'=>'halo.user.showChangePassword()',
																		'title'=>__halotext('Change Password'),'placeholder'=>__halotext('Change Password'))))
								;
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

			{{-- Form Action --}}
			<button type="submit" class="halo-btn halo-btn-primary">{{__halotext('Save')}}</button>
			<button type="button" onClick="halo.util.redirect('{{$user->getUrl()}}')" class="halo-btn halo-btn-default"> {{__halotext('Finish Editing')}}</button>
		</form>
	</div>
</div>
{{-- End Edit Profile content --}}
@stop

{{-- Scripts --}}
@section('scripts')
@stop

