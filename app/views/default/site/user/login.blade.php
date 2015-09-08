@extends('site.layouts.default')

{{-- Web site Title --}}
@section('title')
{{{ __halotext('Login') }}} ::
@parent
@stop

{{-- Content --}}
@section('content')

<div class="page-header">
	<h1>{{__halotext('Log in into your account')}}</h1>
</div>
<?php 
	$err_code = Input::get('err', null);
	$err_msg = '';
	$succ_msg = '';
	switch($err_code) {
		case 1:
			$err_msg = __halotext('Wrong username or password');
			break;
		case 2:
			$err_msg = __halotext('Your account has been blocked. Please contact the administrator for more information.');
			break;
		case 3:
			$err_msg = __halotext('Your account has not been activated. Please check your email and follow the activation link.');
			break;
		case 4:
			$succ_msg = __halotext('Your account was created successfully. A confirmation email was sent to your email address. You need to activate your account by following activate link in the confirmation email to be able to login.');
			break;
		case 5:
			$succ_msg = __halotext('Your account was created successfully. Now you can log in.');
			break;
		case 6:
			$succ_msg = __halotext('Your account has been activated; you can now log in.');
			break;
		default;
			break;
	}
?>
@if ($err_msg)
<div class="alert alert-danger alert-block">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	<h4>Error</h4>{{$err_msg}}
</div>
@endif
@if ($succ_msg)
<div class="alert alert-info alert-block">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	{{$succ_msg}}
</div>
@endif
<form class="form-horizontal" method="POST" action="{{ URL::to('?view=user&task=login') }}" accept-charset="UTF-8">
	<input type="hidden" name="_token" value="{{ csrf_token() }}">
	<input type="hidden" name="view" value="user">
	<input type="hidden" name="task" value="login">
	<fieldset>
		<div class="form-group">
			<label class="col-md-2 control-label" for="email">{{ __halotext('Username') }}</label>

			<div class="col-md-10">
				<input class="form-control" tabindex="1" placeholder="{{ __halotext('Your Username') }}" type="text"
				       name="email" id="email" value="{{{ Input::old('email') }}}">
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-2 control-label" for="password">
				{{ __halotext('Password') }}
			</label>

			<div class="col-md-10">
				<input class="form-control" tabindex="2" placeholder="{{ __halotext('Your password') }}" type="password"
				       name="password" id="password">
			</div>
		</div>

		<div class="form-group">
			<div class="col-md-offset-2 col-md-10">
				<label class="checkbox-inline">
					<input tabindex="3" type="checkbox" name="remember" id="remember" value="1" checked>
					{{ __halotext('Remember Me') }}
				</label>
			</div>
		</div>

		@if ( Session::get('error') )
		<div class="alert alert-danger">{{{ Session::get('error') }}}</div>
		@endif

		@if ( Session::get('notice') )
		<div class="alert">{{{ Session::get('notice') }}}</div>
		@endif

		<div class="form-group">
			<div class="col-md-offset-2 col-md-10">
				<button tabindex="3" type="submit" class="halo-btn halo-btn-primary">{{ __halotext('Login') }}</button>
				<a class="halo-btn halo-btn-default" href="{{URL::to('?view=user&task=forgot')}}">{{ __halotext('Forgot Password')
					}}</a>
			</div>
		</div>
		<?php $socialSettings = HALOUtilHelper::getSocialSettings();
		$hasSocialLogin = false;
		foreach (HALOUtilHelper::getSettingList($socialSettings, 'social') as $secName => $secValue) {
			if (OAuthEx::hasConsumer($secName)) $hasSocialLogin = true;
		}
		?>
		@if($hasSocialLogin)
		<div class="form-group">
			<div class="col-md-offset-2 col-md-10 halo-oauth">
				<hr/>
				<p class="halo-oauth-text"> {{__halotext('Or Sign in with')}}</p>
				@foreach(HALOUtilHelper::getSettingList($socialSettings,'social') as $secName => $secValue)
				@if(OAuthEx::hasConsumer($secName))
				{{HALOUIBuilder::getInstance('','social.login_icon_' . lcfirst($secName),array())->fetch()}}
				@endif
				@endforeach
			</div>
		</div>
		@endif
	</fieldset>
</form>

@stop
