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

{{-- ///////////////////////////////////// Profile text field ///////////////////////////////////// --}}
@beginUI('field.edit_layout')
<div class="row halo-field-layout-wrapper">
    <div class="col-md-10 col-xs-12">
        {{$builder->fieldHtml}}
    </div>
</div>
@endUI

{{-- ///////////////////////////////////// Profile text field ///////////////////////////////////// --}}
@beginUI('field.text')
<div class="form-group {{$builder->getSize()}} {{($builder->error?'error':'')}}">
    <label for="{{$builder->name}}" class="{{$builder->getValidationLabel()}}">{{{$builder->title}}}{{$builder->getHelpText()}}</label>{{HALOField::renderPrivacyConfigHtml($builder->halofield)}}
    <input class="form-control {{ $builder->class }}" name="{{$builder->name}}" type="text"
           placeholder="{{{$builder->placeholder}}}" value="{{{$builder->value}}}" {{$builder->getValidation()}}
    {{$builder->getDisabled()}} {{$builder->getReadOnly()}} {{$builder->getData()}}/>
    @if($builder->error)<span class="help-block">{{{$builder->error}}}</span>@endif
</div>
@endUI

{{-- ///////////////////////////////////// Profile textarea field ///////////////////////////////////// --}}
@beginUI('field.textarea')
<div class="form-group {{$builder->getSize()}} {{($builder->error?'error':'')}}">
    <label for="{{$builder->name}}" class="{{$builder->getValidationLabel()}}">{{{$builder->title}}}{{$builder->getHelpText()}}</label>{{HALOField::renderPrivacyConfigHtml($builder->halofield)}}
    <textarea class="form-control {{ $builder->class }}" name="{{$builder->name}}"
              placeholder="{{{$builder->placeholder}}}" {{$builder->getValidation()}} {{$builder->getDisabled()}}
    {{$builder->getReadOnly()}} {{$builder->getData()}}/>{{{$builder->value}}}</textarea>
    @if($builder->error)<span class="help-block">{{{$builder->error}}}</span>@endif
</div>
@endUI

{{-- ///////////////////////////////////// Profile select field ///////////////////////////////////// --}}
@beginUI('field.select')
<div class="form-group {{$builder->getSize()}} {{($builder->error?'error':'')}}">
    <label for="{{$builder->name}}" class="{{$builder->getValidationLabel()}}">{{{$builder->title}}}{{$builder->getHelpText()}}</label>{{HALOField::renderPrivacyConfigHtml($builder->halofield)}}
    <select data-halo-value="{{{$builder->value}}}" class="form-control {{ $builder->class }}" name="{{$builder->name}}"
    {{$builder->getValidation()}} {{$builder->getOnChange()}} {{$builder->getDisabled()}} {{$builder->getReadOnly()}}>
    @foreach($builder->getOptions() as $opt)
    <option value="{{{$opt->value}}}"
    @if($opt->value == $builder->value) selected="true" @endif>{{{$opt->title}}}</option>
    @endforeach
    </select>
    @if($builder->error)<span class="help-block">{{{$builder->error}}}</span>@endif
</div>
@endUI

{{-- ///////////////////////////////////// Profile select field ///////////////////////////////////// --}}
@beginUI('field.select_dropdown')
<div class="form-group {{$builder->getSize()}} {{($builder->error?'error':'')}}">
    <label for="{{$builder->name}}" class="{{$builder->getValidationLabel()}}">{{{$builder->title}}}{{$builder->getHelpText()}}</label>

    <div data-tree-select class="halo-btn-group {{($builder->error?'error':'')}}">
        <button data-feedback="{{$builder->name}}" type="button" class="halo-btn halo-btn-default halo-dropdown-toggle"
                data-htoggle="dropdown">
            {{$builder->title}} <i class="fa fa-caret-down"></i>
        </button>
        <ul class="halo-dropdown-menu" role="menu" aria-labelledby="dLabel">
            {{HALOUIBuilder::getInstance('','form.select_option',array('options'=>$builder->getOptions(),'input'=>$builder->name))->fetch()}}
        </ul>
        <input type="hidden" data-rule-feedback="{{$builder->name}}" class="{{ $builder->class }}"
               name="{{$builder->name}}" value="{{{$builder->value}}}" {{$builder->getValidation()}}
        {{$builder->getOnChange()}} {{$builder->getDisabled()}} {{$builder->getReadOnly()}}/>
        @if($builder->error)<span class="help-block">{{{$builder->error}}}</span>@endif
    </div>
</div>
@endUI

{{-- ///////////////////////////////////// Profile multiple select field ///////////////////////////////////// --}}
@beginUI('field.select_multiple')
<div class="form-group {{$builder->getSize()}} {{($builder->error?'error':'')}}">
    <label for="{{$builder->name}}" class="{{$builder->getValidationLabel()}}">{{{$builder->title}}}{{$builder->getHelpText()}}</label>{{HALOField::renderPrivacyConfigHtml($builder->halofield)}}
    <select multiple class="form-control {{ $builder->class }}" name="{{$builder->name}}" {{$builder->getValidation()}}
    {{$builder->getOnChange()}} {{$builder->getDisabled()}} {{$builder->getReadOnly()}}>
    @foreach($builder->getOptions() as $opt)
    <option value="{{{$opt->value}}}"
    @if(in_array($opt->value,(array)$builder->value)) selected="true" @endif>{{{$opt->title}}}</option>
    @endforeach
    </select>
    @if($builder->error)<span class="help-block">{{{$builder->error}}}</span>@endif
</div>
@endUI

{{-- ///////////////////////////////////// Profile radio field ///////////////////////////////////// --}}
@beginUI('field.radio')
<div class="form-group {{$builder->getSize()}} {{($builder->error?'error':'')}}">
    <label for="{{$builder->name}}">{{{$builder->title}}}{{$builder->getHelpText()}}</label>{{HALOField::renderPrivacyConfigHtml($builder->halofield)}}
    <div class="input-group">
        @foreach($builder->getOptions() as $opt)
        <label class="radio-inline">
            <input type="radio" name="{{$builder->name}}" value="{{{$opt->value}}}" @if(isset($opt->id))
            id="{{$opt->id}}" @endif @if($opt->value == $builder->value) checked="checked" @endif
            {{$builder->getDisabled()}} {{$builder->getReadOnly()}}>{{{$opt->title}}}
        </label>
        @endforeach
    </div>
    @if($builder->error)<span class="help-block">{{{$builder->error}}}</span>@endif
</div>
@endUI

{{-- ///////////////////////////////////// Profile checkbox field ///////////////////////////////////// --}}
@beginUI('field.checkbox')
<div class="form-group {{$builder->getSize()}} {{($builder->error?'error':'')}}">
    <label for="{{$builder->name}}">{{{$builder->title}}}{{$builder->getHelpText()}}</label>{{HALOField::renderPrivacyConfigHtml($builder->halofield)}}
    <div class="input-group">
        @foreach($builder->getOptions() as $opt)
        <label class="checkbox-inline">
            <input type="checkbox" name="{{$builder->name}}" value="{{{$opt->value}}}" @if(isset($opt->id))
            id="{{$opt->id}}" @endif @if(in_array($opt->value,$builder->value)) checked="checked" @endif
            {{$builder->getDisabled()}} {{$builder->getReadOnly()}}>{{{$opt->title}}}
        </label>
        @endforeach
    </div>
    @if($builder->error)<span class="help-block">{{{$builder->error}}}</span>@endif
</div>
@endUI

{{-- ///////////////////////////////////// Profile switch field ///////////////////////////////////// --}}
@beginUI('field.switch')
<div class="form-group {{$builder->getSize()}} {{($builder->error?'error':'')}}">
    <label for="{{$builder->name}}">{{{$builder->title}}}{{$builder->getHelpText()}}</label>{{HALOField::renderPrivacyConfigHtml($builder->halofield)}}
    <div class="input-group">
        <input class="halo-switch-input" data-on-text="{{__halotext($builder->onState)}}" data-size="small"
               data-off-text="{{__halotext($builder->offState)}}" type="checkbox" name="{{$builder->name}}"
        @if($builder->value) checked="checked" @endif {{$builder->getDisabled()}} {{$builder->getReadOnly()}}>
    </div>
    @if($builder->error)<span class="help-block">{{{$builder->error}}}</span>@endif
</div>
@endUI

{{-- ///////////////////////////////////// Profile switch field ///////////////////////////////////// --}}
@beginUI('field.readable_switch')
<div class="halo-field-readonly">
    <input class="halo-switch-input" data-on-text="{{__halotext($builder->onState)}}" data-size="small"
           data-off-text="{{__halotext($builder->offState)}}" type="checkbox" name="{{$builder->name}}"
    @if($builder->value) checked="checked" @endif {{$builder->getDisabled()}} {{$builder->getReadOnly()}}>
</div>
@endUI

{{-- ///////////////////////////////////// Profile datetime field ///////////////////////////////////// --}}
@beginUI('field.datetime')
<div class="form-group {{$builder->getSize()}} {{($builder->error?'error':'')}}">
    <label for="{{$builder->name}}" class="{{$builder->getValidationLabel()}}">{{{$builder->title}}}{{$builder->getHelpText()}}</label>{{HALOField::renderPrivacyConfigHtml($builder->halofield)}}
    <div class="input-group halo_field_datetime date form_datetime" data-date="{{Carbon::now()}}"
         data-date-format="{{{$builder->field->getParams('format','dd-mm-yyyy hh:ii')}}}">
        <input placeholder="{{ $builder->placeholder }}" class="form-control" name="{{$builder->name}}" type="text" value="{{{$builder->value}}}" {{$builder->getData()}} {{ $builder->readonly }}>
        <span class="input-group-addon"><span class="fa fa-times"></span></span>
        <span class="input-group-addon"><span class="fa fa-th"></span></span>
    </div>
    @if($builder->error)<span class="help-block">{{{$builder->error}}}</span>@endif
</div>
<script type="text/javascript">
__haloReady(function() {
	HALOModernizr.load({
		load    : "{{HALOAssetHelper::to('/assets/js/bootstrap-datetimepicker.js')}}",
		complete: function () {
			jQuery('.halo_field_datetime').datetimepicker({
				weekStart     : 1,
				todayBtn      : 1,
				autoclose     : 1,
				todayHighlight: 1,
				startView     : 2,
				forceParse    : 0,
				showMeridian  : 1,
				pickerPosition: "bottom-left"
			});
		}
	});
});
</script>
@endUI

{{-- ///////////////////////////////////// Profile date field ///////////////////////////////////// --}}
@beginUI('field.date')
<div class="form-group {{$builder->getSize()}} {{($builder->error?'error':'')}}">
    <label for="{{$builder->name}}" class="{{$builder->getValidationLabel()}}">{{{$builder->title}}}{{$builder->getHelpText()}}</label>{{HALOField::renderPrivacyConfigHtml($builder->halofield)}}
    <div class="input-group halo_field_date date form_date" data-date="{{Carbon::now()}}" data-date-format="dd-mm-yyyy">
        <input placeholder="{{ $builder->placeholder }}" class="form-control" name="{{$builder->name}}" type="text" value="{{{$builder->value}}}" {{ $builder->readonly }}
        {{$builder->getValidation()}} {{$builder->getData()}}>
        <span class="input-group-addon"><span class="fa fa-times"></span></span>
        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
    </div>
    @if($builder->error)<span class="help-block">{{{$builder->error}}}</span>@endif
</div>
<script type="text/javascript">
__haloReady(function() {
	HALOModernizr.load({
		load    : "{{HALOAssetHelper::to('/assets/js/bootstrap-datetimepicker.js')}}",
		complete: function () {
			jQuery('.halo_field_date').datetimepicker({
				weekStart     : 1,
				todayBtn      : 1,
				autoclose     : 1,
				todayHighlight: 1,
				startView     : 2,
				minView       : 2,
				forceParse    : 0,
				pickerPosition: "bottom-left"
			});
		}
	});
});
</script>
@endUI

{{-- ///////////////////////////////////// Profile date range field ///////////////////////////////////// --}}
@beginUI('field.daterange')
<?php $uid = uniqid() ?>
<div class="row halo-field-layout-wrapper">
    <div id="halo-field-range-wrapper-{{$uid}}">
        <div class="col-md-6 halo-startdate">

        </div>
        <div class="col-md-6 halo-enddate">

        </div>
    </div>
</div>
<script type="text/javascript">
	__haloReady(function() {
		jQuery(document).one('afterHALOInit', function (scope) {
			halo.field.assignFieldDateRange(jQuery.parseJSON('{{json_encode($builder->rangeFields)}}'), 'halo-field-range-wrapper-{{$uid}}');
		});
	});
</script>
@endUI

{{-- ///////////////////////////////////// Profile time field ///////////////////////////////////// --}}
@beginUI('field.time')
<div class="form-group {{$builder->getSize()}} {{($builder->error?'error':'')}}">
    <label for="{{$builder->name}}" class="{{$builder->getValidationLabel()}}">{{{$builder->title}}}{{$builder->getHelpText()}}</label>{{HALOField::renderPrivacyConfigHtml($builder->halofield)}}
    <div class="input-group halo_field_time date form_time" data-date="{{{$builder->value}}}"
         data-date-format="{{$builder->field->getParams('format','hh:ii')}}">
        <input placeholder="{{ $builder->placeholder }}" class="form-control" name="{{$builder->name}}" type="text" value="{{{$builder->value}}}" {{$builder->getData()}} {{ $builder->readonly }}>
        <span class="input-group-addon"><span class="fa fa-times"></span></span>
        <span class="input-group-addon"><span class="fa fa-clock-o"></span></span>
    </div>
    @if($builder->error)<span class="help-block">{{{$builder->error}}}</span>@endif
</div>
<script type="text/javascript">
__haloReady(function() {
	HALOModernizr.load({
		load    : "{{HALOAssetHelper::to('/assets/js/bootstrap-datetimepicker.js')}}",
		complete: function () {
			jQuery('.halo_field_time').datetimepicker({
				weekStart     : 1,
				todayBtn      : 1,
				autoclose     : 1,
				todayHighlight: 1,
				startView     : 1,
				minView       : 0,
				maxView       : 1,
				forceParse    : 0,
				pickerPosition: "bottom-left"
			});
		}
	});
});
</script>
@endUI

{{-- ///////////////////////////////////// Profile float field ///////////////////////////////////// --}}
@beginUI('field.float')
<div class="form-group {{$builder->getSize()}} {{($builder->error?'error':'')}}">
    <label for="{{$builder->name}}" class="{{$builder->getValidationLabel()}}">{{{$builder->title}}}{{$builder->getHelpText()}}</label>{{HALOField::renderPrivacyConfigHtml($builder->halofield)}}
    <input class="form-control haloj-auto-numeric"
           id="halo_field_float{{$builder->field->id}}"
           name="{{$builder->name}}"
           placeholder="{{{$builder->placeholder}}}"
           type="text"
           value="{{{$builder->value}}}" {{$builder->getData()}}" {{$builder->getValidation()}}>
    @if($builder->error)<span class="help-block">{{{$builder->error}}}</span>@endif
</div>
@endUI

{{-- ///////////////////////////////////// Profile unit field ///////////////////////////////////// --}}
@beginUI('field.unit')
<?php $uid = 'halo_field_unit' . $builder->field->id . uniqid(); ?>
<div class="form-group {{$builder->getSize()}} {{($builder->error?'error':'')}}">
    <label for="{{$builder->name}}" class="{{$builder->getValidationLabel()}}">{{{$builder->title}}}{{$builder->getHelpText()}}</label>{{HALOField::renderPrivacyConfigHtml($builder->halofield)}}
    <div class="input-group">
        <input class="form-control haloj-auto-numeric"
               id="{{$uid}}"
               name="{{$builder->name}}"
               placeholder="{{{$builder->placeholder}}}"
               type="text"
               value="{{{$builder->value}}}"
        {{$builder->getData()}} {{$builder->getValidation()}}>
        <div class="input-group-btn">
            {{ HALOUIBuilder::getInstance('','form.unit',array('name'=>$builder->unit_name,
            'unitList'=> $builder->unitList,
            'target'=>$uid,
            'value'=> $builder->preferedUnit
            ))
            ->fetch()}}
        </div>
    </div>
    @if($builder->error)<span class="help-block">{{{$builder->error}}}</span>@endif
</div>
@endUI

{{-- ///////////////////////////////////// Profile unit field ///////////////////////////////////// --}}
@beginUI('field.readable_unit')
<?php $uid = 'halo_field_unit' . $builder->field->id . uniqid(); ?>
<div class="halo-field-readonly">
    <span id="{{$uid}}" class="haloj-auto-numeric" name="{{$builder->name}}" data-halo-value="{{{$builder->value}}}"
    {{$builder->getData()}}">{{{$builder->value}}}</span>
    {{ HALOUIBuilder::getInstance('','form.unit',array('name'=>$builder->unit_name,
    'unitList'=> $builder->unitList,
    'target'=>$uid,
    'value'=> $builder->preferedUnit
    ))
    ->fetch()}}
</div>
@endUI

{{-- ///////////////////////////////////// Profile chain select field ///////////////////////////////////// --}}
@beginUI('field.select_chain')
<script type="text/javascript">
    var chain_select_options_{{$builder->id}}=jQuery.parseJSON('{{$builder->field->getParams("subOptions")}}');
    var chain_select_fields_{{$builder->id}}=jQuery.parseJSON('{{json_encode($builder->subFields)}}');
	__haloReady(function() {
		jQuery(document).one('afterHALOInit', function (scope) {
			halo.field.initChainSelect(chain_select_options_{{$builder->id}},chain_select_fields_{{$builder->id}});
		});
	});
</script>
@endUI

{{-- ///////////////////////////////////// Profile group field ///////////////////////////////////// --}}
@beginUI('field.group')
<?php $uid = uniqid() ?>
<div class="row halo-field-layout-wrapper">
    <div class="col-md-10 col-xs-12">
        <div class="panel panel-default">
            <div class="panel-heading">{{__halotext($builder->title)}}{{$builder->getHelpText()}}</div>
            <div id="halo-field-group-wrapper-{{$uid}}" class="panel-body">

            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
	__haloReady(function() {
		jQuery(document).one('afterHALOInit', function (scope) {
			halo.field.assignFieldToGroup(jQuery.parseJSON('{{json_encode($builder->subFields)}}'), 'halo-field-group-wrapper-{{$uid}}');
		})
	});
</script>
@endUI

{{-- ///////////////////////////////////// Profile tab field ///////////////////////////////////// --}}
@beginUI('field.tab')
<div class="halo-field-tab" data-tab-name="{{__halotext($builder->title)}}"></div>
@endUI

{{-- ///////////////////////////////////// Profile tab field ///////////////////////////////////// --}}
@beginUI('field.separator')
<div class="row halo-field-layout-wrapper">
	<div class="col-md-10 col-xs-12 halo-field-separator">
	<h4>{{__halotext($builder->title)}}</h4>
	</div>
</div>
@endUI

{{-- ///////////////////////////////////// Form Basic UI: json table ///////////////////////////////////// --}}
@beginUI('field.jsontable')
<div class="form-group halo-jsontable-container {{$builder->getSize()}} {{($builder->error?'error':'')}}">
    <label for="{{$builder->name}}" class="{{$builder->getValidationLabel()}}">{{{$builder->title}}}{{$builder->getHelpText()}}</label>{{HALOField::renderPrivacyConfigHtml($builder->halofield)}}
    <div class="input-group">
        <div class="halo-jsontable-wrapper table-responsive">
            <table style="table-layout:fixed;" class="table table-halo-json-editable table-condensed table-hover"
                   data-jsontable data-jsoninput="{{$builder->name}}"
            @if($builder->getReadOnly()){{'data-jsonreadonly="readonly"'}}@endif>
            </table>
        </div>
        <input type="hidden" value="{{{$builder->value}}}" data-default="{{{$builder->default}}}"
               name='{{$builder->name}}' {{$builder->getValidation()}} {{$builder->getOnChange()}}
        {{$builder->getReadOnly()}}/>
    </div>
    @if($builder->error)<span class="help-block">{{{$builder->error}}}</span>@endif
</div>
@endUI

{{-- ///////////////////////////////////// Form Basic UI: json table readonly mode///////////////////////////////////// --}}
@beginUI('field.readable_jsontable')
<div class="halo-jsontable-readable halo-jsontable-container">
    <div class="halo-jsontable-wrapper table-responsive">
        <table style="table-layout:fixed;" class="table table-halo-json-editable table-condensed table-hover"
               data-jsontable data-jsoninput="{{$builder->name}}"
        @if($builder->getReadOnly()){{'data-jsonreadonly="readonly"'}}@endif>
        </table>
    </div>
    <input type="hidden" value="{{{$builder->value}}}" data-default="{{{$builder->default}}}" name='{{$builder->name}}'
    {{$builder->getOnChange()}} {{$builder->getReadOnly()}}/>
</div>
@endUI

{{-- ///////////////////////////////////// Form Basic UI: media uploader ///////////////////////////////////// --}}
@beginUI('field.media')
<div class="form-group {{$builder->getSize()}} {{($builder->error?'error':'')}}">
    <label for="{{$builder->name}}" class="{{$builder->getValidationLabel()}}">{{{$builder->title}}}{{$builder->getHelpText()}}</label>{{HALOField::renderPrivacyConfigHtml($builder->halofield)}}
    <div class="halo-media-upload-wrapper">
        {{ HALOUIBuilder::getInstance('','photo.uploader',array('id'=>$builder->id,
        'name'=>$builder->name,
        'data'=>array('inputName' => $builder->name,
        'mediaValue' => $builder->value,
        'mediaType' => $builder->field->getParams('mediaType','photo'),
        'allowedExtensions' => $builder->field->getParams('extensions','*')
        )
        ))
        ->fetch()}}
    </div>
    @if($builder->error)<span class="help-block">{{{$builder->error}}}</span>@endif
</div>
@endUI

{{-- ///////////////////////////////////// Location field ///////////////////////////////////// --}}
@beginUI('field.location')
<?php $uid = HALOUtilHelper::uniqidInt(); ?>
<div class="form-group halo-location-control {{$builder->getSize()}} {{($builder->error?'error':'')}}" id="{{$uid}}">
    <label for="{{$builder->name}}" class="{{$builder->getValidationLabel()}}">{{{$builder->title}}}{{$builder->getHelpText()}}</label>{{HALOField::renderPrivacyConfigHtml($builder->halofield)}}
    <div class="halo-input-group-prepend halo-location-input-group">
        <a class="halo-btn halo-btn-nobg halo-input-icon"
           onclick="halo.location.showCheckin('{{$uid}}')"
           href="javascript:void(0);">
            {{HALOUIBuilder::icon('search-plus')}}
        </a>
        <input class="halo-field-location-name form-control halo-input-control {{$builder->class}}"
               name="{{$builder->name}}[name]"
               type="text"
               placeholder="{{{$builder->placeholder}}}"
               value="{{{$builder->value['name']}}}"
        {{$builder->getValidation()}} {{$builder->getDisabled()}} {{$builder->getReadOnly()}} {{$builder->getData()}}/>
        <input class="halo-field-location-lat" name="{{$builder->name}}[lat]" type="hidden"
               value="{{{$builder->value['lat']}}}"/>
        <input class="halo-field-location-lng" name="{{$builder->name}}[lng]" type="hidden"
               value="{{{$builder->value['lng']}}}"/>
    </div>
    @if($builder->error)<span class="help-block">{{{$builder->error}}}</span>@endif
</div>
@endUI