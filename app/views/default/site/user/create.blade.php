@extends('site.layouts.default')

{{-- Web site Title --}}
@section('title')
{{{ __halotext('User Registration') }}}
@parent
@stop

{{-- Content --}}
@section('content')
<div class="page-header">
	<h1>{{__halotext('Signup')}}</h1>
</div>
{{ Confide::makeSignupForm()->render() }}
@stop
