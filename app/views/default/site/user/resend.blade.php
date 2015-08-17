@extends('site.layouts.default')

{{-- Web site Title --}}
@section('title')
{{{ __halotext('Resend activation code') }}} ::
@parent
@stop

{{-- Content --}}
@section('content')

<div class="page-header">
	<h1>{{__halotext('Resend activation link')}}</h1>
</div>
<?php 
	$messages = HALOResponse::getMessage()->get('error');
?>
@if (!empty($messages))
<div class="alert alert-danger alert-block">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
    @if(is_array($messages))
    @foreach ($messages as $m)
    {{ $m }}
    @endforeach
    @else
    {{ $messages }}
    @endif
</div>
@endif
<form class="form-horizontal" method="POST" action="{{ URL::to('?view=user&task=resend_code') }}" accept-charset="UTF-8">
	<input type="hidden" name="_token" value="{{ csrf_token() }}">
	<fieldset>
		<div class="form-group">
			<label class="col-md-2 control-label" for="email">{{ __halotext('Email') }}</label>

			<div class="col-md-10">
				<input class="form-control" tabindex="1" placeholder="{{ __halotext('Email') }}" type="text"
				       name="email" id="email" value="{{{ Input::old('email') }}}">
			</div>
		</div>
		<div class="form-group">
			<div class="col-md-offset-2 col-md-10">
				<button tabindex="3" type="submit" class="halo-btn halo-btn-primary">{{ __halotext('Resend activation email') }}</button>
			</div>
		</div>
	</fieldset>
</form>

@stop
