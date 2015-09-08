@extends('admin.layouts.default')
{{-- Web site Title --}}
@section('title')
    {{{ __halotext('Welcome') }}} :: @parent
@stop

{{-- Content --}}
@section('content')
<div class="halo-welcome-wrapper">
<div class="row">
    <div class="col-md-12 halo-welcome-top">
       {{ HALOUIBuilder::getInstance('', 'module.halo_newslettersub', array('zone' => 'halo.welcome.newslettersub'))->fetch() }}
    </div>
</div>
<div class="row">
    <div class="col-md-6 halo-welcome-left">
        <div data-halozone="halo.welcome.getstarted">
            <div class="halo-jax-loading"><div class="halo-loading-icon-wrapper fa-2x"><i class="fa fa-spinner fa-spin fa-lg"></i></div></div>
        </div>
        {{-- HALOUIBuilder::getInstance('', 'module.halo_getstarted', array('zone' => 'halo.welcome.getstarted'))->fetch() --}}
    </div>
    <div class="col-md-6 halo-welcome-right">
        <div data-halozone="halo.welcome.pricing">
            <div class="halo-jax-loading"><div class="halo-loading-icon-wrapper fa-2x"><i class="fa fa-spinner fa-spin fa-lg"></i></div></div>
        </div>
        {{-- HALOUIBuilder::getInstance('', 'module.halo_pricing', array('zone' => 'halo.welcome.pricing'))->fetch() --}}
    </div>
</div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function() {
        halo.jax.call("admin,dashboard", "UpdateWelcome", function() {
            jQuery('.halo-pricing-tip').tooltip();
        });
    });
</script>
@stop
{{-- Scripts --}}
@section('scripts')
@stop