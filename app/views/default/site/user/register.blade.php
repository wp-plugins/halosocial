@extends('site.layouts.default')

{{-- Web site Title --}}
@section('title')
{{{ __halotext('Register new account') }}} ::
@parent
@stop

{{-- Content --}}
@section('content')

<div class="page-header">
	<h1>{{__halotext('Register new account')}}</h1>
</div>
<?php 
	$messages = HALOResponse::getMessage()->get('error');
    $blankUser = new HALOUserModel();
?>
@if (!empty($messages))
<div class="alert alert-danger alert-block">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	<h4>Error</h4>
    @if(is_array($messages))
    @foreach ($messages as $m)
    {{ $m }}
    @endforeach
    @else
    {{ $messages }}
    @endif
</div>
@endif
<?php
	$wizard = HALOUIBuilder::getInstance('', 'form.wizard', array('name' => 'registerForm', 'method' => 'post',
																'onCancel' => "halo.util.redirect('" . Url::to('?view=home') . "')",
																'onFinish' => "halo.user.postRegister(this)",
																'hidden' => array('view' => 'user', 'task' => 'register'),
                                                                'class' => 'haloj-form-validation',
																'data' => array('validate' => 'validate', 'finishTitle' => __halotext('Create my account'))
										));
	//basic info
	$basicInfo = HALOUIBuilder::getInstance('register_basic_info', 'content', array())
					->addUI('email', HALOUIBuilder::getInstance('','form.text',array('title'=>__halotext('Email Address'),'name'=>'email'
						,'value'=> Input::get('email',''), 'placeholder'=>__halotext('Your Email Address'), 'validation' => 'required',
						'onKeyup' => "halo.util.staticThrottle('validateEmail', function(e){halo.form.clearInputError(jQuery(e))},1000)(this)",
						'onChange' => "halo.user.validateNewUser()"
					)))
					->addUI('username', HALOUIBuilder::getInstance('','form.text',array('title'=>__halotext('Username'),'name'=>'username'
						,'value'=> Input::get('username',''), 'placeholder'=>__halotext('Your Username'), 'validation' => 'required',
						'onKeyup' => "halo.util.staticThrottle('validateUsername', function(e){halo.form.clearInputError(jQuery(e))},1000)(this)",
						'onChange' => "halo.user.validateNewUser()"
					)))
					->addUI('display_name', HALOUIBuilder::getInstance('','form.text',array('title'=>__halotext('Display Name'),'name'=>'display_name'
						,'value'=> Input::get('display_name', ''), 'validation' => $blankUser->getFieldValidateRule('display_name')
						,'placeholder'=>__halotext('Your Display Name')
					)))
					->addUI('password', HALOUIBuilder::getInstance('','form.password',array('title'=>__halotext('Password'),'name'=>'password'
						,'value'=> '', 'validation' => 'required'
						,'placeholder'=>__halotext('Your Password')
						,'onChange' => "halo.user.validatePassword('#registerForm')"
					)))
					->addUI('password_confirmation', HALOUIBuilder::getInstance('','form.password',array('title'=>__halotext('Confirm Password'),'name'=>'password_confirmation'
						,'value'=> '', 'validation' => 'required'
						,'placeholder'=>__halotext('Retype Your Password')
						,'onKeyup' => "halo.user.validatePassword('#registerForm')"
					)));
	if(!is_null(HALOReCaptcha::getSiteKey())){
        $basicInfo->addUI('recaptcha', HALOUIBuilder::getInstance('', 'form.recaptcha', array('title' => '', 'validation' => 'required', 'name' => 'recaptcha')));
	}
				
	$wizard->addUI('basicStep', HALOUIBuilder::getInstance('', 'form.wizard_step', array('content' => $basicInfo->fetch(), 'title' => __halotext('Account Info'))))
				;
	
	//profile select
	$profileOptions = HALOProfileModel::getProfileListOption('user', false);
	if( count($profileOptions) > 1) {	//only one profile option + option header
		$profileInfo = HALOUIBuilder::getInstance('register_profile_info', 'content', array())
						->addUI('profile_id',HALOUIBuilder::getInstance('','form.radio_helper',array('name'=>'profile_id','id'=>'profile_id','value'=> Input::get('profile_id'),
																'title'=>__halotext('Select User Profile Type'),
																'options'=>$profileOptions,
																'class' => 'halo-registration-select-profile',
																'validation' => 'required',
																'onChange' => 'halo.user.changeRegisterProfileType(this)'
																)))
						->addUI('has_profile', HALOUIBuilder::getInstance('', 'form.hidden', array('name' => 'has_profile', 'validation' => 'required', 'value' => '')))
					;
		$wizard->addUI('profileStep', HALOUIBuilder::getInstance('', 'form.wizard_step', array('content' => $profileInfo->fetch(), 'title' => __halotext('Select Profile'))));
	}
	
	//setup default profile fields
	if(count($profileOptions)) {
		$defaultProfile = $profileOptions[0]['value'];
		
		//profile field
		$tmpUser = new HALOUserModel();
		$tmpUser->id = 0;
		$tmpUser->profile_id = $defaultProfile;
		
		$profileFields = $tmpUser->getProfileFields()->get();
		$fieldInfo = HALOUIBuilder::getInstance('register_fields_info', 'content', array())
					->addUI('profile_fields', HALOUIBuilder::getInstance('', 'inline_profile_edit', array('zone' => 'user_profile_edit', 'profileFields' => $profileFields)))
				;
		//do not show profile field form if there is no attached profile fields
		$stepClass = count($profileFields)?'':'ignore';
		$wizard->addUI('fieldsStep', HALOUIBuilder::getInstance('', 'form.wizard_step', array('content' => $fieldInfo->fetch(), 'title' => __halotext('Profile Info'), 'class' => $stepClass)));
	}
	
	Event::fire('user.onShowRegisterForm', array(&$wizard));
?>
<div class="panel panel-default">
	<div class="panel-body">
		@if(HALOConfig::get('register.wizard', 1))
			{{$wizard->fetch()}}
		@else
		<form role="form" id="registerForm" class="form" method="POST" action="{{ URL::to('?view=user&task=register') }}"
		      accept-charset="UTF-8">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<fieldset>
				{{$basicInfo->fetch()}}
				<div class="form-group">
					<div class="halo-pull-right">
						<button tabindex="6" type="submit" id="halo_submit_register" class="halo-btn halo-btn-primary disabled">{{ __halotext('Create my account') }}
						</button>
					</div>
				</div>
			</fieldset>
		</form>
		@endif
	</div>
</div>
<script>
	__haloReady(function(){
		// halo.form.checkFormReady('#registerForm',function(){jQuery('#halo_submit_register').removeClass('disabled');}, function(){jQuery('#halo_submit_register').addClass('disabled')})
	});
</script>
@stop
