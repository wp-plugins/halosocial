@extends('site.layouts.default')

{{-- Web site Title --}}
@section('title')
{{{ __halotext('Stream') }}}
@parent
@stop

{{-- Content --}}
@section('content')

{{-- Stream content --}}
@include('site/stream/single')
{{-- End Stream content --}}

@stop