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

{{-- ///////////////////////////////////// Text Search Filter in browse page///////////////////////////////////// --}}
@beginUI('browse.nav_search_filter')
<?php $searchText = isset($builder->value['text']) ? $builder->value['text'] : '';
$searchCat = isset($builder->value['cat']) ? $builder->value['cat'] : '';
$catOptions = HALOUtilHelper::collection2options(HALOPostCategoryModel::getFirstCategories(), 'id', 'name', false);
array_unshift($catOptions, array('value' => '', 'title' => __halotext('All Categories')));
?>
<div class="halo-input-dropdown-append halo-btn-block hidden-xs">
		<div class="halo-input-control halo-input-group-prepend">
			<i class="fa fa-search halo-input-icon"></i>
			<input type="text" class="form-control halo-input-control" name="{{$builder->filter->getInputName()}}[text]"
			       value="{{{$searchText}}}"
			       placeholder="{{__halotext('What do you want to search?')}}" onchange="halo.browse.reloadResults()"/>
		</div>
</div>
@endUI

{{-- ///////////////////////////////////// Text Search Filter in browse page///////////////////////////////////// --}}
@beginUI('browse.search_filter')
<?php $searchText = isset($builder->value['text']) ? $builder->value['text'] : '';
$searchCat = isset($builder->value['cat']) ? $builder->value['cat'] : '';
$catOptions = HALOUtilHelper::collection2options(HALOPostCategoryModel::getFirstCategories(), 'id', 'name', false);
array_unshift($catOptions, array('value' => '', 'title' => __halotext('All Categories')));
?>
<div class="col-lg-8 form-group halo-search-box-keyword ">
	<div class="halo-input-dropdown-append halo-btn-block">
		<div class="halo-input-control halo-input-group-prepend">
			<a class="halo-btn halo-btn-primary halo-input-icon" href="javascript:void(0)" onclick="halo.browse.reloadResults();"><i class="fa fa-search"></i></a>
			<input type="text" class="form-control halo-input-control" name="{{$builder->filter->getInputName()}}[text]"
			       value="{{{$searchText}}}"
			       placeholder="{{__halotext('What do you want to search?')}}" onchange="halo.browse.reloadResults()"/>
		</div>
        {{HALOUIBuilder::getInstance('','form.select',array('name'=>$builder->filter->getInputName().'[cat]','class'=>'halo-filter-cat
        halo-dropdown'
        , 'value'=>$searchCat, 'options'=>$catOptions,
        'onChange'=>"halo.browse.reloadResults()",'collapse'=>'collapse'))->fetch()}}
	</div>
</div>
@endUI

{{-- ///////////////////////////////////// Filter Location Select UI ///////////////////////////////////// --}}
@beginUI('form.filter_location')
<?php //the tree sort is a combination of 2 filter_tree_radio UIs for sort critical and direction
$value = $builder->value;
$distance = isset($value['distance']) ? trim($value['distance']) : '';
$name = isset($value['name']) ? trim($value['name']) : '';
$lng = isset($value['lng']) ? trim($value['lng']) : '';
$lat = isset($value['lat']) ? trim($value['lat']) : '';
$builder->input = empty($builder->name) ? $builder->input : $builder->name;
$uid = HALOUtilHelper::uniqidInt();
?>
@if(is_object($builder->filter))
<div class="halo-filter-rule row halo-location-control halo-filter-{{Str::slug($builder->filter->getParams('title'))}}"
     id="{{$uid}}">
	<div class="halo-filter-title-wrapper col-md-2">
		<h4 class="halo-filter-title">{{__halotext($builder->filter->getParams('title'))}}{{$builder->getHelpText()}}</h4>
	</div>
	@endif
	<div class="halo-filter-content-wrapper col-md-10">
		<div class="input-group halo-filter-location-input-wrapper">
			<input class="form-control halo-field-location-name halo-filter-location {{ $builder->class }}"
			       id="{{HALOUtilHelper::uniqidInt()}}" type="text" placeholder="{{{$builder->placeholder}}}"
			       value="{{{$name}}}" data-halo-input="{{$builder->input}}[name]" data-halo-value="{{{$name}}}"/>
			<input type="hidden" name="{{$builder->input}}[name]" value="{{{$name}}}">
        <span class="input-group-btn">
            <button class="halo-btn halo-btn-nobg" onclick="halo.location.showCheckin('{{$uid}}')" type="button">
	            {{HALOUIBuilder::icon('search-plus')}}
            </button>
        </span>
		</div>
		<input class="halo-field-location-lat" name="{{$builder->input}}[lat]" type="hidden" value="{{{$lat}}}"/>
		<input class="halo-field-location-lng" name="{{$builder->input}}[lng]" type="hidden" value="{{{$lng}}}"/>
		<label> {{__halotext('Distance')}} (km)</label>
		{{HALOUIBuilder::getInstance('','form.filter_text',array('name'=>$builder->name .
		'[distance]','value'=>$distance,'class'=>'halo-slider-input halo-filter-slider'
		,'data'=>array('slider-min'=>0,'slider-max'=>20,'slider-step'=>1,'slider-value'=>$distance)))->fetch()}}
	</div>
	@if(is_object($builder->filter))
</div>
@endif
@endUI


{{-- ///////////////////////////////////// Location Filter in browse page///////////////////////////////////// --}}
@beginUI('browse.location_filter')
<?php
$value = $builder->value;
$distance = isset($value['distance']) ? trim($value['distance']) : 5;
/*
$defaultLocation = HALOBrowseHelper::getDefaultLocation();
$placeholder = $defaultLocation->getCity();
$name = isset($value['name']) ? trim($value['name']) : '';
$lng = isset($value['lng']) ? trim($value['lng']) : $defaultLocation->getLng();
$lat = isset($value['lat']) ? trim($value['lat']) : $defaultLocation->getLat();
*/
$placeholder = '';
$name = isset($value['name']) ? trim($value['name']) : '';
$lng = isset($value['lng']) ? trim($value['lng']) : '';
$lat = isset($value['lat']) ? trim($value['lat']) : '';

$builder->input = empty($builder->name) ? $builder->input : $builder->name;
$uid = HALOUtilHelper::uniqidInt();
$distOptions = array(array('title' => '1km', 'value' => 1), array('title' => '5km', 'value' => 5), array('title' => '10km', 'value' => 10)
, array('title' => '20km', 'value' => 20), array('title' => '50km', 'value' => 50), array('title' => '100km', 'value' => 100));
$inputName = $builder->filter->getInputName();
?>
<div class="col-lg-4 form-group halo-search-box-location hidden-xs">
	<label>{{__halotext('in')}}</label>

	<div class="halo-input-dropdown-append">
		<div class="halo-input-control halo-input-group-prepend">
			<a onclick="halo.browse.loadCurrentLocation();" class="halo-btn halo-btn-nobg halo-input-icon"
			   title="{{__halotext('Get my location')}}"><i
					class="fa fa-location-arrow"></i></a>
			<input class="form-control halo-input-control halo-field-location-name halo-filter-location {{ $builder->class }}"
			       id="{{HALOUtilHelper::uniqidInt()}}" type="text" placeholder="{{{$placeholder}}}"
			       value="{{{$name}}}" data-halo-input="{{$inputName}}[name]" data-halo-value="{{{$name}}}"
			       name="{{$inputName}}[name]"/>
			<input class="halo-field-location-lat" name="{{$inputName}}[lat]" type="hidden" value="{{{$lat}}}"/>
			<input class="halo-field-location-lng" name="{{$inputName}}[lng]" type="hidden" value="{{{$lng}}}"/>
			<input name="{{$inputName}}[toplat]" type="hidden" value=""/>
			<input name="{{$inputName}}[toplng]" type="hidden" value=""/>
			<input name="{{$inputName}}[btmlat]" type="hidden" value=""/>
			<input name="{{$inputName}}[btmlng]" type="hidden" value=""/>
		</div>
		{{HALOUIBuilder::getInstance('','form.select',array('name'=>$inputName.'[distance]','class'=>'halo-dropdown'
		, 'value'=>$distance, 'options'=>$distOptions,
		'onChange'=>"halo.browse.reloadResults()",'collapse'=>'collapse'))->fetch()}}

	</div>
</div>
@endUI

{{-- ///////////////////////////////////// Price Filter in browse page///////////////////////////////////// --}}
@beginUI('browse.price_filter')
<?php
$value = $builder->value;
$unitOptions = array(array('title' => 'k', 'value' => 1000), array('title' => __halotext('milion'), 'value' => 1000000));
$range = (isset($value['range']) && !empty($value['range'])) ? trim($value['range']) : '0,100000';
$unit = isset($value['unit']) ? trim($value['unit']) : 1000;
?>
<div class="col-lg-12 form-horizontal clearfix halo-filter-price-wrapper  hidden-xs">
	<label>{{__halotext('Price Range')}}</label>

	<div class="halo-input-dropdown-append halo-price-slider">
		<input type="text" class="halo-slider-input halo-filter-input" data-slider-value="[{{{$range}}}]"
		       data-slider-min="0" data-slider-step="100"
		       data-slider-max="100000"
		       data-slider-step="1" data-slider-orientation="horizontal"
		       name="{{$builder->filter->getInputName()}}[range]">
	</div>
	{{HALOUIBuilder::getInstance('','form.select',array('name'=>$builder->filter->getInputName().'[unit]','class'=>'halo-dropdown
	halo-price-slider-unit'
	, 'value'=>$unit, 'options'=>$unitOptions,
	'onChange'=>"halo.browse.reloadResults()",'collapse'=>'collapse'))->fetch()}}
</div>
@endUI

{{-- ///////////////////////////////////// Sort Filter in browse page///////////////////////////////////// --}}
@beginUI('browse.sort_filter')
<?php
$value = $builder->value;
$active = isset($value['sort']) ? $value['sort'] : '';
$dir = isset($value['dir']) ? $value['dir'] : 'desc';
$my = HALOUserModel::getUser();
$userId = ($my) ? $my->id : '';
$owner = isset($value['owner']) ? $value['owner'] : '';
?>
<div class="col-lg-12 halo-filter-tag-wrapper hidden-xs">
	<div class="halo-filter-tag">
		@if($my)
		<a class="halo-owner-btn halo-toggle-btn halo-btn halo-btn-default halo-btn-nobg @if($owner) active @endif"
		   data-value="{{{$userId}}}">{{__halotext('My Posts')}}<i class="fa fa-times"></i></a>
		@endif
		{{HALOUIBuilder::getInstance('','browse.sort_btn',array('active'=>$active,'dir'=>$dir,'title'=> __halotext('By date'), 'value'=>'date'))->fetch()}}
		{{HALOUIBuilder::getInstance('','browse.sort_btn',array('active'=>$active,'dir'=>$dir,'title'=> __halotext('By price'),'value'=>'price'))->fetch()}}
		{{HALOUIBuilder::getInstance('','browse.sort_btn',array('active'=>$active,'dir'=>$dir,'title'=> __halotext('By rating'),'value'=>'rating'))->fetch()}}
		<input type="hidden" class="halo-sort-input" value="{{$active}}"
		       name="{{$builder->filter->getInputName()}}[sort]">
		<input type="hidden" class="halo-dir-input" value="{{$dir}}" name="{{$builder->filter->getInputName()}}[dir]">
		<input type="hidden" class="halo-owner-input" value="{{$owner}}"
		       name="{{$builder->filter->getInputName()}}[owner]">
	</div>
</div>
@endUI

{{-- ///////////////////////////////////// Sort Filter in browse page///////////////////////////////////// --}}
@beginUI('browse.sort_btn')
<?php $dir = ($builder->value == $builder->active) ? $builder->dir : 'desc'; ?>
<a class="halo-sort-btn halo-toggle-btn halo-btn halo-btn-default halo-btn-nobg @if($builder->value == $builder->active) active @endif"
   data-value="{{{$builder->value}}}">{{$builder->title}}<i class="fa fa-sort-amount-{{{$dir}}}"></i></a>
@endUI
{{-- ///////////////////////////////////// Filter List UI ///////////////////////////////////// --}}
@beginUI('browse.filter_form')
<?php $uid = uniqid() ?>
<div class="halo-section-heading halo-browser-filter {{$builder->class}} clearfix">
		<div class="halo-filter-wrapper">
			<form id="filter_form" class="filter_form halo_browse_form" action="{{URL::to('/')}}">
				<div class="halo-search-box">
					<div class="input-group clearfix halo-btn-block">
						<!-- /btn-group -->
						<div class="filter_label_container halo_filter_label{{$uid}}"></div>
						<input id="halo_filter_label{{$uid}}" type="text" class="form-control hidden" disabled
						{{$builder->getOnChange()}}>
					</div>

					<div class="row">
						<!-- /input-group -->
						@foreach($builder->filters as $filter)
						{{$filter->getDisplayUI()->fetch()}}
						@endforeach
					</div>
				</div>
			</form>
		</div>
</div>
@endUI

{{-- ///////////////////////////////////// Filter List UI ///////////////////////////////////// --}}
@beginUI('browse.filter_form_mobile')
<?php $uid = uniqid() ?>
<div class="halo-section-heading halo-browser-filter {{$builder->class}} clearfix">
		<div class="halo-filter-wrapper">
			<form id="filter_form" class="filter_form halo_browse_form" action="{{URL::to('/')}}">
				<div class="halo-search-box">
					<div class="input-group clearfix halo-btn-block">
						<!-- /btn-group -->
						<div class="filter_label_container halo_filter_label{{$uid}}"></div>
						<input id="halo_filter_label{{$uid}}" type="text" class="form-control hidden" disabled
						{{$builder->getOnChange()}}>
					</div>

					<div class="row">
						<!-- /input-group -->
						@foreach($builder->filters as $filter)
							@if($filter->name == 'browse.listing.search')
							{{$filter->getDisplayUI()->fetch()}}
							@endif
						@endforeach
					</div>
				</div>
			</form>
		</div>
</div>
@endUI
