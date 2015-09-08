@extends('site.layouts.default')

{{-- Web site Title --}}
@section('title')
{{{ __halotext('Forgot Password') }}}
@parent
@stop

{{-- Content --}}
@section('content')
<div class="page-header">
	<h1>{{{ __halotext('Forgot Password') }}}</h1>
</div>
<div class="panel panel-default">
	<div class="panel-body">
		<div class="alert alert-info"><p>{{__halotext('Please enter your username or email address. You will receive a
				link
				to create a new password via email.')}}</p>
		</div>
		{{ Confide::makeForgotPasswordForm() }}
	</div>
</div>
@stop
