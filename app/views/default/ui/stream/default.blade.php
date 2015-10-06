<?php 
/*
 * Plugin Name: HaloSocial
 * Plugin URL: https://halo.social
 * Description: Social Networking Plugin for WordPress
 * Author: HaloSocial
 * Author URL: https://halo.social
 * Version: 1.0
 * Copyright: (c) 2015 HaloSocial, Inc. All Rights Reserved.
 * License: GPLv3 or later
 * License URL: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: halosocial
 * Domain Path: /language
 *
 * HaloSocial is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * HaloSocial is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY. See the
 * GNU General Public License for more details.
 */
?>

{{-- ///////////////////////////////////// Stream Content UI ///////////////////////////////////// --}}
@beginUI('stream.content')
<div class="halo-media halo-stream-content" {{$builder->getZone()}}>
@foreach ($builder->acts as $act)
{{$act->render()}}
@endforeach
</div>
@if($builder->showLoadMore)
{{-- loadmore --}}
<div class="halo-media text-center" id="halo-stream-loadmore">
	<a href="javascript:void(0);" onclick="halo.activity.loadMore(this);return false;" class="halo-stream-loadmore-btn halo-btn halo-btn-xs">
		{{__halotext('Click to load more')}}  {{--<img src="{{HALOAssetHelper::getAssetUrl('assets/images/preloader.gif')}}"> --}}
	</a>
	<a href="javascript:void(0);" onclick="halo.activity.autoloadMore(this);return false;" class="halo-btn halo-btn-xs">
		{{__halotext('Auto load more')}}
	</a>
</div>
{{-- ./loadmore --}}
@endif
@endUI

{{-- ///////////////////////////////////// Stream Header UI ///////////////////////////////////// --}}
@beginUI('stream.header')
@if(count($builder->streamFilters))
<div class="panel panel-default halo-filter-panel">
	<div class="panel-body">
		{{-- stream header --}}
		<div class="halo-stream-header">
			<form id="streamFilters">
				{{ HALOUIBuilder::getInstance('','filter_list',array('title'=>__halotext('Filters'),'icon'=>'filter',
				'filters'=>$builder->streamFilters
				,'onChange'=>'halo.activity.refresh()'))->fetch()}}
			</form>
		</div>
	</div>
</div>
@endif
@endUI
