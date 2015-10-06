@extends('site.layouts.browse')

{{-- Web site Title --}}
@section('title')
{{{ __halotext('Browse') }}}
@parent
@stop

{{-- OpenGraph Meta --}}
@section('ogp_meta')
<meta property="og:image" content="{{HALOPhotoHelper::getSiteBanner()}}"/>
@stop

{{-- Map --}}
@section('map')
<div id="halo_browse_canvas" class="container"></div>

@stop

{{-- Content --}}
@section('content')
<div class="halo-section-container halo-section-browse">
	<div class="halo-section-heading halo-browser-filter">
		{{-- filter --}}
		<div class="halo-filter-wrapper">
			<form id="filter_form_{{HALOUtilHelper::uniqidInt()}}" class="filter_form">
				{{
				HALOUIBuilder::getInstance('','filter_list',array('title'=>__halotext('Filters'),'icon'=>'filter',
				'filters'=>$filters
				,'onChange'=>"halo.browse.refreshSection('post')"))->fetch()}}
			</form>
		</div>
	</div>
	<div class="halo-section-body halo-browse-listing" data-halozone="halo-browse-wrapper">
		<div class="halo-browser-selected-post hidden row">
			<h3>Prefer posts</h3>
		</div>
		{{HALOResponse::getZoneContent('halo-browse-wrapper')}}
	</div>
	{{HALOResponse::getZoneScript('halo-browse-wrapper')}}
	{{HALOResponse::getZonePagination('halo-browse-wrapper')}}
</div>
@stop

