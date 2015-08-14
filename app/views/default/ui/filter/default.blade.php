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

{{-- ///////////////////////////////////// Filter date range UI ///////////////////////////////////// --}}
@beginUI('filter.date_range')
	@if(is_object($builder->filter))
	<?php $uid = HALOUtilHelper::uniqidInt();
		$startDate = isset($builder->value['startdate'])?$builder->value['startdate']:'';
		$endDate = isset($builder->value['enddate'])?$builder->value['enddate']:'';
		$within = isset($builder->value['within'])?$builder->value['within']:'';
		$minVal = $builder->params->get('minVal', 1);
		$morethan = isset($builder->value['morethan'])?$builder->value['morethan']:'';
		$title = __halotext($builder->filter->getParams('title'));
		$now = new Carbon('now');
	?>
	<div class="halo-popupfilter-wrapper" data-filter-type="DateFilter">
		<div class="halo-btn-group bootstrap-select halo-popupfilter-content">
			<button type="button" class="halo-btn halo-btn-default halo-dropdown-toggle selectpicker" data-htoggle="dropdown">
			<span class="filter-option halo-pull-left">{{$title}}</span>&nbsp;
			<span class="caret"></span>
			</button>
			<div class="halo-dropdown-menu open halo-popoverfilter-input" id="{{$uid}}" role="menu">
				<div class="input-group" data-type="dateBetween">
					<div class="halo-option-title">
						<span class="halo-option-item text-success"></span><label>{{__halotext('Between')}}</label>
					</div>
					<div class="input-group halo_filter_start_date date form_date" data-date="{{Carbon::now()}}" data-date-format="dd-mm-yyyy" data-date-enddate="{{$now}}">
						<input class="form-control halo-popoverfilter-sub-input start-date-input {{ $builder->class }}" type="text" placeholder="{{__halotext('Start date')}}" value="{{{$startDate}}}" readonly
						{{$builder->getValidation()}} id="{{HALOUtilHelper::uniqidInt()}}"
						name="{{$builder->name}}[startdate]"
						data-halo-label="{{{__halotext('From date')}}}">
						<span class="input-group-addon"><span class="fa fa-times"></span></span>
						<span class="input-group-addon"><span class="fa fa-calendar"></span></span>
					</div>
					<div class="input-group halo_filter_end_date date form_date" data-date="{{Carbon::now()}}" data-date-format="dd-mm-yyyy" data-date-enddate="{{$now}}">
						<input class="form-control halo-popoverfilter-sub-input end-date-input {{ $builder->class }}" type="text" placeholder="{{__halotext('End date')}}" value="{{{$endDate}}}" readonly
						{{$builder->getValidation()}} id="{{HALOUtilHelper::uniqidInt()}}" name="{{$builder->name}}[enddate]"
						data-halo-label="{{{__halotext('To date')}}}">
						<span class="input-group-addon"><span class="fa fa-times"></span></span>
						<span class="input-group-addon"><span class="fa fa-calendar"></span></span>
					</div>
				</div>
				<div class="input-group" data-type="dateWithin">
					<div class="halo-option-title">
						<span class="halo-option-item text-success"></span><label>{{__halotext('Within the last')}}</label>
					</div>
					<div class="input-group form_date">
						<input class="form-control halo-popoverfilter-sub-input within-date-input" type="number" value="{{{$within}}}" min="{{$minVal}}"
						{{$builder->getValidation()}} id="{{HALOUtilHelper::uniqidInt()}}" name="{{$builder->name}}[within]"
						data-halo-label="{{{__halotext('Within')}}}">
						<span class="input-group-addon">{{__halotext('Day(s)')}}</span>
					</div>
				</div>
				<div class="input-group" data-type="dateMorethan">
					<div class="halo-option-title">
						<span class="halo-option-item text-success"></span><label>{{__halotext('More than')}}</label>
					</div>
					<div class="input-group form_date">
						<input class="form-control halo-popoverfilter-sub-input morethan-date-input" type="number" value="{{{$morethan}}}" min="{{$minVal}}"
						{{$builder->getValidation()}} id="{{HALOUtilHelper::uniqidInt()}}" name="{{$builder->name}}[morethan]"
						data-halo-label="{{{__halotext('More than')}}}">
						<span class="input-group-addon">{{__halotext('Day(s) ago')}}</span>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
	__haloReady(function() {
		HALOModernizr.load({
			load    : "{{HALOAssetHelper::to('/assets/js/bootstrap-datetimepicker.js')}}",
			complete: function () {
				halo.field.initDateRange('{{$uid}}','.halo_filter_start_date','.halo_filter_end_date');
			}
		});
	});
	</script>
	@endif
@endUI

{{-- ///////////////////////////////////// Filter date range UI ///////////////////////////////////// --}}
@beginUI('filter.range')
	@if(is_object($builder->filter))
	<?php $uid = HALOUtilHelper::uniqidInt();
		$min = isset($builder->value['min'])?$builder->value['min']:'';
		$max = isset($builder->value['max'])?$builder->value['max']:'';
		$min = HALOUtilHelper::str2Number($min, '.', ',');
		$max = HALOUtilHelper::str2Number($max, '.', ',');
		
		$minVal = $builder->params->get('minVal', 0);
		$na = isset($builder->value['na'])?$builder->value['na']:'';
		$title = __halotext($builder->filter->getParams('title'));
		$unit = __halotext($builder->filter->getParams('unit', __halotext('vnd')));
		$now = new Carbon('now');
		$minId = HALOUtilHelper::uniqidInt();
		$maxId = HALOUtilHelper::uniqidInt();
	?>
	<div class="halo-popupfilter-wrapper" data-filter-type="RangeFilter">
		<div class="halo-btn-group bootstrap-select halo-popupfilter-content">
			<button type="button" class="halo-btn halo-btn-default halo-dropdown-toggle selectpicker" data-htoggle="dropdown">
			<span class="filter-option halo-pull-left">{{$title}}</span>&nbsp;
			<span class="caret"></span>
			</button>
			<div class="halo-dropdown-menu open halo-popoverfilter-input" id="{{$uid}}" role="menu">
				<div class="input-group" data-type="rangeBetween">
					<div class="halo-option-title">
						<span class="halo-option-item text-success"></span><label>{{__halotext('Between')}}</label>
					</div>
					<div class="input-group form_date">
						<input class="form-control halo-popoverfilter-sub-input haloj-auto-numeric min-range-input {{ $builder->class }}" type="text" placeholder="{{__halotext('Min')}}" value="{{{$min}}}" 
							data-a-dec="," data-a-sep="." data-m-dec="0" data-v-max="999999999999" data-v-min="{{$minVal}}"
							{{$builder->getValidation()}} id="{{$minId}}" name="{{$builder->name}}[min]" data-halo-label="{{{__halotext('From')}}}">
						<span class="input-group-addon">{{$unit}}</span>
					</div>
					<div class="input-group form_date">
						<input class="form-control halo-popoverfilter-sub-input haloj-auto-numeric max-range-input {{ $builder->class }}" type="text" placeholder="{{__halotext('Max')}}" value="{{{$max}}}" 
							data-a-dec="," data-a-sep="." data-m-dec="0" data-v-max="999999999999" data-v-min="{{$minVal}}"
							{{$builder->getValidation()}} id="{{$maxId}}" name="{{$builder->name}}[max]" data-halo-label="{{{__halotext('To')}}}">
						<span class="input-group-addon">{{$unit}}</span>
					</div>
				</div>
				<div class="input-group" data-type="rangeNa">
					<div class="halo-option-title">
						<span class="halo-option-item text-success"></span>
					</div>
					<div class="halo-checkbox-filter">
						<label class="halo-btn">
							<input class="halo-popoverfilter-sub-input na-range-input halo-checkbox-input" type="hidden" value="{{$na}}"
							name="{{$builder->name}}[na]" data-halo-label="{{{__halotext('Negotiate')}}}"> 
							{{__halotext('Negotiate')}}
						</label>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
	</script>
	@endif
@endUI

{{-- ///////////////////////////////////// Filter Location Select UI ///////////////////////////////////// --}}
@beginUI('filter.location')
	@if(is_object($builder->filter))
		<?php //the tree sort is a combination of 2 filter_tree_radio UIs for sort critical and direction
		$value = $builder->value;
		$distance = isset($value['distance']) ? trim($value['distance']) : 5;
		$name = isset($value['name']) ? trim($value['name']) : '';
		$lng = isset($value['lng']) ? trim($value['lng']) : '';
		$lat = isset($value['lat']) ? trim($value['lat']) : '';
		$builder->input = empty($builder->name) ? $builder->input : $builder->name;
		$title = __halotext($builder->filter->getParams('title'));
		$uid = HALOUtilHelper::uniqidInt();
		?>
		<div class="halo-popupfilter-wrapper" data-filter-type="LocationFilter">
			<div class="halo-btn-group bootstrap-select halo-popupfilter-content">
				<button type="button" class="halo-btn halo-btn-default halo-dropdown-toggle selectpicker" data-htoggle="dropdown">
				<span class="filter-option halo-pull-left">{{$title}}</span>&nbsp;
				<span class="caret"></span>
				</button>
				<div class="halo-dropdown-menu open halo-popoverfilter-input" id="{{$uid}}" role="menu">
					<div class="halo-row-wrapper">
					{{HALOUIBuilder::getInstance('','form.location',array('title'=>__halotext('Address'),'name'=>$builder->input
																		,'value'=>array('name'=>$name,'lat'=>$lat,'lng'=>$lng)
																		,'class'=>'halo-popoverfilter-sub-input'
																		,'data'=>array('placeType'=>'gmap')))->fetch()}}
					</div>
					<div class="halo-row-wrapper">
						<div class="form-group">
							<label> {{__halotext('Distance')}}</label>
							<div class="input-group halo-input-group-append">
								<input class="form-control halo-popoverfilter-sub-input halo-field-location-distance" type="number" value="{{{$distance}}}"
								{{$builder->getValidation()}} id="{{HALOUtilHelper::uniqidInt()}}" name="{{$builder->name}}[distance]"
								data-halo-label="{{{__halotext('Around')}}}">
								<span class="halo-input-icon">{{__halotext('km')}}</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	@endif
@endUI

{{-- ///////////////////////////////////// Filter Sort Select UI ///////////////////////////////////// --}}
@beginUI('filter.sort')
	<?php //the tree sort is a combination of 2 filter_tree_radio UIs for sort critical and direction
	$value = (array)$builder->value;

	$options = $builder->options;
	$sortOpts = $options['sort'];
	$dirOpts = $options['dir'];
	?>
	@if(is_object($builder->filter) && !empty($sortOpts) && !empty($dirOpts))
	<div class="halo-popupfilter-wrapper" data-filter-type="SelectFilter">
		<select name="{{$builder->name}}[]" data-header="{{__halotext('Sort by')}}" class="halo-popoverfilter-input halo-popupfilter-content" title="{{__halotext('Sort: None')}}" multiple {{$builder->getData()}}>
			<optgroup label="{{__halotext('Criteria')}}" data-max-options="1">
			@foreach($sortOpts as $opt)
				<option value="{{$opt->value}}" @if(in_array($opt->value,$value)) selected="selected" @endif>{{__halotext($opt->name)}}</option>
			@endforeach
			</optgroup>
			<optgroup label="{{__halotext('Order By')}}" data-max-options="1">
			@foreach($dirOpts as $opt)
				<option value="{{$opt->value}}" @if(in_array($opt->value,$value)) selected="selected" @endif>{{__halotext($opt->name)}}</option>
			@endforeach
			</optgroup>
		</select>						
	</div>
	@endif
@endUI

{{-- ///////////////////////////////////// Filter tree Select UI ///////////////////////////////////// --}}
@beginUI('filter.tree')
	<?php //the tree sort is a combination of 2 filter_tree_radio UIs for sort critical and direction
	$value = (array)$builder->value;
	$title = __halotext($builder->filter->getParams('title'));
	$options = $builder->options;
	?>
	@if(is_object($builder->filter) && !empty($options))
	<div class="halo-popupfilter-wrapper" data-filter-type="SelectFilter">
		<select name="{{$builder->name}}[]" data-header="{{$title}}" class="halo-popoverfilter-input halo-popupfilter-content" title="{{$title}}" multiple {{$builder->getData()}}>
			@foreach($options as $option)
				<option class="haloc-tree-select-parent" value="{{$option->name}}" @if(in_array($option->name,$value)) selected="selected" @endif>{{__halotext($option->name)}}</option>
				@foreach($option->children as $child)
				<option class="haloc-tree-select-child" value="{{$child->name}}" @if(in_array($child->name,$value)) selected="selected" @endif>{{__halotext($child->name)}}</option>					
				@endforeach
			@endforeach
		</select>						
	</div>
	@endif
@endUI

{{-- ///////////////////////////////////// Filter Single select tree UI ///////////////////////////////////// --}}
@beginUI('filter.single_select')
	<?php 	
		$options = $builder->options;
		$title = __halotext($builder->filter->getParams('title'));
		$value = $builder->value;
	?>
	@if(!empty($options))
		@if(is_object($builder->filter))
			<div class="halo-popupfilter-wrapper" data-filter-type="SelectFilter">
				<select name="{{$builder->name}}" class="halo-popoverfilter-input halo-popupfilter-content" title="{{$title}}" {{$builder->getData()}}>
					@foreach($options as $opt)
						<option value="{{$opt->value}}" @if($opt->value == $value) selected="selected" @endif>{{__halotext($opt->name)}}</option>
					@endforeach
				</select>						
			</div>		
		@endif
	@endif
@endUI

{{-- ///////////////////////////////////// Filter Multiple select tree UI ///////////////////////////////////// --}}
@beginUI('filter.multiple_select')
	<?php 
		$options = $builder->options;
		$title = __halotext($builder->filter->getParams('title'));
		$value = (array)$builder->value;
	?>
	@if(!empty($options))
		@if(is_object($builder->filter))
			<div class="halo-popupfilter-wrapper" data-filter-type="SelectFilter">
				<select name="{{$builder->name}}[]" data-header="{{$title}}" class="halo-popoverfilter-input halo-popupfilter-content" title="{{$title}}" multiple {{$builder->getData()}}>
					@foreach($options as $opt)
						<option value="{{$opt->value}}" @if(in_array($opt->value,$value)) selected="selected" @endif>{{__halotext($opt->name)}}</option>
					@endforeach
				</select>						
			</div>		
		@endif
	@endif
@endUI

{{-- ///////////////////////////////////// Filter Text UI ///////////////////////////////////// --}}
@beginUI('filter.text')
	@if(is_object($builder->filter))
		<div class="halo-popupfilter-wrapper" data-filter-type="TextFilter">				
			<div class="halo-input-group-append halo-popupfilter-content">
				<span class="halo-input-icon">
					<button class="halo-btn halo-btn-nobg" onclick="" type="button">
						{{HALOUIBuilder::icon('search')}}
					</button>
				</span>
				<input {{$builder->getClass('form-control halo-popoverfilter-input')}} type="text" placeholder="{{{__halotext($builder->title)}}}"
					   value="{{$builder->value}}" name="{{$builder->name}}"/>
			</div>
		</div>
	@endif
@endUI

{{-- ///////////////////////////////////// Filter Tag UI ///////////////////////////////////// --}}
@beginUI('filter.tag')
<?php $builder->input = empty($builder->name) ? $builder->input : $builder->name; 
$uid = HALOUtilHelper::uniqidInt();
?>
	@if(is_object($builder->filter))
		<div class="halo-popupfilter-wrapper" data-filter-type="TextFilter">				
			<div class="halo-popupfilter-content">
				<div class="halo-hash-tag-container"></div>
				<input type="text" value="{{{$builder->value}}}" {{$builder->getData()}}
				class="halo-hash-tag input-sm form-control {{$builder->class}}" placeholder="{{{__halotext($builder->title)}}}"
				data-output=".filter_tag_{{$uid}}" data-only-tag-list="true" data-sync-on-change="true">
				<input type="hidden" class="halo-filter-tag filter_tag_{{$uid}}" id="{{HALOUtilHelper::uniqidInt()}}" 
					name="{{$builder->name}}" value="{{{$builder->value}}}">
			</div>
		</div>
	@endif
@endUI
