@extends('site.layouts.default')

{{-- Web site Title --}}
@section('title')
{{{ __halotext('user/user.register') }}} ::
@parent
@stop

{{-- Content --}}
@section('content')
<script type="text/javascript">
    jQuery(function(){
        if(window.opener){
            window.close();
        }

    })
</script>
<div id="signup-container" class="signup-form">
<div class="page-header">
	<h3>{{__halotext('Register new account')}}</h3>
</div>

    <div class="text-desc">Connect with a social network</div>
    <div class="social">
        <a href="{{ URL::to('oauth/facebook') }}" class="facebook"><i class="ico-facebook"></i></a>
        <a href="{{ URL::to('oauth/twitter') }}" class="twitter"><i class="ico-twitter"></i></a>
        <a href="{{ URL::to('oauth/google') }}" class="google"><i class="ico-gplus"></i></a>
    </div>
<div class="hr"></div>
{{ Confide::makeSignupForm()->render() }}
</div>
@stop
