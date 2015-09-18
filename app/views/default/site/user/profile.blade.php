@extends('site.layouts.default')

{{-- Web site Title --}}
@section('title')
{{ sprintf(__halotext('%s Profile'),$user->getDisplayName()) }}
@parent
@stop

{{-- OpenGraph Meta --}}
@section('ogp_meta')
<meta property="og:image" content="{{$user->getCover()}}"/>
@stop

{{-- Content --}}
@section('content')
{{-- Focus content --}}
@include('site/user/profile_focus')
{{-- End Focus content --}}

{{-- Actions content --}}
@include('site/user/profile_actions')
{{-- End Actions content --}}

@stop

