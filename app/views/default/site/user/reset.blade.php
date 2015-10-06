@extends('site.layouts.default')

{{-- Web site Title --}}
@section('title')
{{{ __halotext('Forgot Password') }}}
@parent
@stop

{{-- Content --}}
@section('content')
<div class="page-header">
	<h1>{{__halotext('Forgot Password')}}</h1>
</div>
<div  class="panel panel-default">
	<div class="panel-body">
	<div class="alert alert-info"><h3>{{__halotext('Please enter your new password')}}</h3></div>
	{{ Confide::makeResetPasswordForm($token)->render() }}
	</div>
</div>
@stop
