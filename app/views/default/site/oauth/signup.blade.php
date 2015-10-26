@extends('site.layouts.default')

{{-- Web site Title --}}
@section('title')
{{{ __halotext('Login') }}}
@parent
@stop

{{-- Content --}}

@section('content')
<script type="text/javascript">
    jQuery(function () {
        if (window.opener) {
            window.close();
            window.opener.location.href = ""
        }
    })
</script>
<div id="login-container" class="login-form">
    <div class="page-header">
        <h3>Facebook sync into halo.social</h3>
    </div>
    <form class="form-wrapper-01" method="POST" action="{{ URL::to('user/login') }}" accept-charset="UTF-8">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <fieldset>
            <label for="username">{{ __halotext('confide::confide.username') }}</label>
            <input class="input-box" tabindex="1" placeholder="{{ __halotext('confide::confide.username') }}" type="text"
                   name="username" id="username" value="{{{ $user->username}}}">

            <label for="password">
                {{ __halotext('confide::confide.password') }}
            </label>
            <input class="input-box" tabindex="2" placeholder="{{ __halotext('confide::confide.password') }}"
                   type="password" name="password" id="password">

            @if ( Session::get('error') )
            <div class="alert alert-error">{{ Session::get('error') }}</div>
            @endif

            @if ( Session::get('notice') )
            <div class="alert">{{ Session::get('notice') }}</div>
            @endif
            <div>

                <button tabindex="3" type="submit" class="halo-btn">{{ __halotext('confide::confide.login.submit') }}</button>
                <button tabindex="3" type="submit" class="halo-btn">Skip this</button>
            </div>
        </fieldset>
    </form>
    @stop