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

{{-- ///////////////////////////////////// Form Basic UI: Text ///////////////////////////////////// --}}
@beginUI('form.text')
@if($builder->getRow())
<div class="row"> @endif
	<div
		class="form-group {{$builder->class}} {{$builder->getSize()}} {{($builder->getErrorClass())}}">
		@if($builder->title)<label for="{{$builder->name}}" class="{{$builder->getValidationLabel()}}">{{{$builder->title}}}{{$builder->getHelpText()}}</label>@endif
		<input class="form-control {{ $builder->class }}" name="{{$builder->name}}" type="text"
		       placeholder="{{{$builder->placeholder}}}" value="{{{$builder->value}}}" {{$builder->getValidation()}} {{$builder->getOnChange()}} {{$builder->getOnKeyup()}}
		{{$builder->getDisabled()}} {{$builder->getReadOnly()}} {{$builder->getData()}}/>
		@if($builder->error)
		<span class="fa fa-times form-control-feedback"></span>
		<span class="help-block">{{{$builder->error}}}</span>
		@endif
	</div>
	@if($builder->getRow())
</div> @endif
@endUI

{{-- ///////////////////////////////////// Form Basic UI: Number ///////////////////////////////////// --}}
@beginUI('form.number')
@if($builder->getRow())
<div class="row"> @endif
	<div
		class="form-group {{$builder->class}} {{$builder->getSize()}} {{($builder->getErrorClass())}}">
		@if($builder->title)<label for="{{$builder->name}}" class="{{$builder->getValidationLabel()}}">{{{$builder->title}}}{{$builder->getHelpText()}}</label>@endif
		<input class="form-control {{ $builder->class }}" name="{{$builder->name}}" type="number" min="{{$builder->min}}"
		       placeholder="{{{$builder->placeholder}}}" value="{{{$builder->value}}}" {{$builder->getValidation()}} {{$builder->getOnChange()}} {{$builder->getOnKeyup()}}
		{{$builder->getDisabled()}} {{$builder->getReadOnly()}} {{$builder->getData()}}/>
		@if($builder->error)
		<span class="fa fa-times form-control-feedback"></span>
		<span class="help-block">{{{$builder->error}}}</span>
		@endif
	</div>
	@if($builder->getRow())
</div> @endif
@endUI

{{-- ///////////////////////////////////// Form Basic UI: Submit ///////////////////////////////////// --}}
@beginUI('form.submit')
@if($builder->getRow())
<div class="row"> @endif
    <div class="form-group {{$builder->getSize()}} {{($builder->getErrorClass())}}">
        @if($builder->title)<label for="{{$builder->name}}" class="{{$builder->getValidationLabel()}}">{{{$builder->title}}}{{$builder->getHelpText()}}</label>@endif
        <input class="{{ $builder->class }}" name="{{$builder->name}}" type="submit"
               placeholder="{{{$builder->placeholder}}}" value="{{{$builder->value}}}" {{$builder->getValidation()}}
        {{$builder->getDisabled()}} {{$builder->getReadOnly()}} {{$builder->getData()}}/>
        @if($builder->error)
        <span class="fa fa-times form-control-feedback"></span>
        <span class="help-block">{{{$builder->error}}}</span>
        @endif
    </div>
    @if($builder->getRow())
</div> @endif
@endUI

{{-- ///////////////////////////////////// Form Basic UI: Alert ///////////////////////////////////// --}}
@beginUI('form.alert')
@if($builder->getRow())
<div class="row"> @endif
	<div class="form-group {{$builder->class}} {{$builder->getSize()}} {{($builder->getErrorClass())}}">
		<div class="alert alert-{{$builder->type}} fade in">
			{{{$builder->title}}}
		</div>
	</div>
	@if($builder->getRow())
</div> @endif
@endUI

{{-- ///////////////////////////////////// Form Basic UI: Countdown ///////////////////////////////////// --}}
@beginUI('form.countdown')
@if($builder->getRow())
<div class="row"> @endif
	<div class="form-group {{$builder->class}} {{$builder->getSize()}} {{($builder->getErrorClass())}}">
		<div class="alert alert-{{$builder->type}} fade in">
			{{{$builder->title}}} : <span class="halo-countdown" data-countdown="{{$builder->value}}" {{$builder->getOnChange()}}><span>
		</div>
	</div>
	@if($builder->getRow())
</div> @endif
@endUI

{{-- ///////////////////////////////////// Form Basic UI: Static ///////////////////////////////////// --}}
@beginUI('form.static')
@if($builder->getRow())
<div class="row"> @endif
	<div class="form-group {{$builder->class}} {{$builder->getSize()}}">
		<label for="{{$builder->name}}">{{{$builder->title}}}</label>

		<div>
			<p class="form-control-static">{{$builder->text}}</p>
		</div>
	</div>
	@if($builder->getRow())
</div> @endif
@endUI

{{-- ///////////////////////////////////// Form Basic UI: Link ///////////////////////////////////// --}}
@beginUI('form.link')
@if($builder->getRow())
<div class="row"> @endif
	<div class="form-group {{$builder->class}} {{$builder->getSize()}} {{($builder->getErrorClass())}}">
		<a {{$builder->getClass()}}{{$builder->getOnClick()}} href="{{$builder->getUrl()}}"
		title="{{{$builder->placeholder}}}" {{$builder->getData()}}>{{{$builder->title}}}</a>
	</div>
	@if($builder->getRow())
</div> @endif
@endUI

{{-- ///////////////////////////////////// Form Basic UI: Button ///////////////////////////////////// --}}
@beginUI('form.button')
@if($builder->getRow())
<div class="row"> @endif
	<div class="form-group {{$builder->getSize()}} {{($builder->getErrorClass())}} @if($builder->inline) halo-inline-btn @endif" {{$builder->getZone()}}>
		<button type="button" {{$builder->getOnClick()}} {{$builder->getClass('halo-btn halo-btn-default')}}>{{{$builder->title}}}</button>
	</div>
	@if($builder->getRow())
</div> @endif
@endUI

{{-- ///////////////////////////////////// Form Basic UI: Raw content ///////////////////////////////////// --}}
@beginUI('form.raw')
@if($builder->getRow())
<div class="row"> @endif
	<div class="form-group {{$builder->class}} {{$builder->getSize()}} {{($builder->getErrorClass())}}">
		{{$builder->content}}
	</div>
	@if($builder->getRow())
</div> @endif
@endUI

{{-- ///////////////////////////////////// Form Basic UI: Password ///////////////////////////////////// --}}
@beginUI('form.password')
@if($builder->getRow())
<div class="row"> @endif
	<div class="form-group {{$builder->class}} {{$builder->getSize()}} {{($builder->getErrorClass())}}">
		<label class="{{$builder->getValidationLabel()}}">{{{$builder->title}}}{{$builder->getHelpText()}}</label>
		<input class="form-control {{ $builder->class }}" name="{{$builder->name}}" type="password"
		       placeholder="{{{$builder->placeholder}}}" value="{{{$builder->value}}}" {{$builder->getValidation()}} {{$builder->getOnChange()}} {{$builder->getOnKeyup()}}
		{{$builder->getDisabled()}} {{$builder->getReadOnly()}} {{$builder->getData()}}/>
		@if($builder->error)
		<span class="fa fa-times form-control-feedback"></span>
		<span class="help-block">{{{$builder->error}}}</span>
		@endif
	</div>
	@if($builder->getRow())
</div> @endif
@endUI

{{-- ///////////////////////////////////// Form Basic UI: Textarea ///////////////////////////////////// --}}
@beginUI('form.textarea')
@if($builder->getRow())
<div class="row"> @endif
	<div class="form-group {{$builder->getSize()}} {{($builder->getErrorClass())}}">
		@if($builder->title)<label class="{{$builder->getValidationLabel()}} @if(!$builder->getAttr('label', true)) {{'hide'}} @endif">{{{$builder->title}}}{{$builder->getHelpText()}}</label>@endif
		<textarea class="form-control {{ $builder->class }}" name="{{$builder->name}}"
		          placeholder="{{{$builder->placeholder}}}" {{$builder->getValidation()}} {{$builder->getDisabled()}}
		{{$builder->getReadOnly()}} {{$builder->getData()}} rows="{{$builder->rows}}">{{{$builder->value}}}</textarea>
		@if($builder->error)<span class="help-block">{{{$builder->error}}}</span>@endif
	</div>
	@if($builder->getRow())
</div> @endif
@endUI

{{-- ///////////////////////////////////// Form Basic UI: Select ///////////////////////////////////// --}}
@beginUI('form.select_original')
@if($builder->getRow())
<div class="row"> @endif
	<div class="form-group {{$builder->class}} {{$builder->getSize()}} {{($builder->getErrorClass())}}" {{$builder->getData()}}>
		<label class="{{$builder->getValidationLabel()}} @if(!$builder->getAttr('label', true)) {{'hide'}} @endif">{{{$builder->title}}}{{$builder->getHelpText()}}</label>
		<select class="form-control {{ $builder->class }}" name="{{$builder->name}}" {{$builder->getValidation()}}
		{{$builder->getOnChange()}} {{$builder->getDisabled()}} {{$builder->getReadOnly()}} {{$builder->getData()}}>
		@foreach($builder->getOptions() as $opt)
		<option value="{{{$opt->value}}}"
		@if($opt->value == $builder->value) selected="true" @endif>{{{$opt->title}}}</option>
		@endforeach
		</select>
		@if($builder->error)<span class="help-block">{{{$builder->error}}}</span>@endif
	</div>
	@if($builder->getRow())
</div> @endif
@endUI

{{-- ///////////////////////////////////// Form Basic UI: Select ///////////////////////////////////// --}}
@beginUI('form.select')
@if($builder->getRow())
<div class="row"> @endif
	<div class="form-group {{$builder->class}} {{$builder->getSize()}} {{($builder->getErrorClass())}}">
		<label class="{{$builder->getValidationLabel()}} @if(!$builder->getAttr('label', true)) {{'hide'}} @endif">{{{$builder->title}}}{{$builder->getHelpText()}}</label>
		<select class="form-control haloj-selectpicker {{ $builder->class }}" name="{{$builder->name}}" {{$builder->getValidation()}}
		{{$builder->getOnChange()}} {{$builder->getDisabled()}} {{$builder->getReadOnly()}} {{$builder->getData()}}>
		@foreach($builder->getOptions() as $opt)
		<option value="{{{$opt->value}}}"
		@if($opt->value == $builder->value) selected="true" @endif>{{{$opt->title}}}</option>
		@endforeach
		</select>
		@if($builder->error)<span class="help-block">{{{$builder->error}}}</span>@endif
	</div>
	@if($builder->getRow())
</div> @endif
@endUI

{{-- ///////////////////////////////////// Form Basic UI: Select ext///////////////////////////////////// --}}
@beginUI('form.select_ext')
@if($builder->getRow())
<div class="row"> @endif
	<div class="form-group {{$builder->class}} {{$builder->getSize()}} {{($builder->getErrorClass())}}" {{$builder->getData()}}>
		<label class="{{$builder->getValidationLabel()}} @if(!$builder->getAttr('label', true)) {{'hide'}} @endif">{{{$builder->title}}}{{$builder->getHelpText()}}</label>
		<select class="form-control haloj-selectpicker {{ $builder->class }}" name="{{$builder->name}}" {{$builder->getValidation()}}
		{{$builder->getOnChange()}} {{$builder->getDisabled()}} {{$builder->getReadOnly()}} {{$builder->getData()}}>
		@foreach($builder->getOptions() as $opt)
		<option value="{{{$opt->value}}}" @if(isset($opt->data)) {{HALOOutputHelper::getHtmlData($opt->data)}} @endif
		@if($opt->value == $builder->value) selected="true" @endif>{{{$opt->title}}}</option>
		@endforeach
		</select>
		@if($builder->error)<span class="help-block">{{{$builder->error}}}</span>@endif
	</div>
	@if($builder->getRow())
</div> @endif
@endUI

{{-- ///////////////////////////////////// Form Basic UI: Select ///////////////////////////////////// --}}
@beginUI('form.select_old')
@if($builder->getRow())
<div class="row"> @endif
	<div class="{{$builder->class}} {{$builder->getSize()}}">
		<label class="{{$builder->getValidationLabel()}}" for="{{$builder->name}}">{{{$builder->title}}}{{$builder->getHelpText()}}</label>

		<div data-tree-select
		     class="halo-btn-group @if($builder->collapse) btn-select-collapse @else btn-select-full @endif {{($builder->getErrorClass())}}">
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
	@if($builder->getRow())
</div> @endif
@endUI

{{-- ///////////////////////////////////// Select Option UI///////////////////////////////////// --}}
@beginUI('form.select_option')
<?php $options = $builder->options; ?>
@if(!empty($options))
@foreach($options as $opt)
<li><a href="javascript:void(0);" onclick="halo.form.changeTreeSelectOption(this)" data-halo-input="{{$builder->input}}"
       data-halo-value="{{{$opt->value}}}">{{ isset($opt->icon)?HALOUIBuilder::icon($opt->icon):'' }}<span>{{{ $opt->title }}}</span></a>
</li>
@endforeach
@endif
@endUI


{{-- ///////////////////////////////////// Form Basic UI: Unit ///////////////////////////////////// --}}
@beginUI('form.unit')
<div data-tree-select class="halo-btn-group halo-form-unit {{$builder->getSize()}}" data-unit-target="{{$builder->target}}">
	<button type="button" class="halo-btn halo-btn-nobg halo-btn-xs halo-btn-default halo-dropdown-toggle {{$builder->getAttr('btnsize')}}"
	        data-htoggle="dropdown">
		{{$builder->value}}
	</button>
	<ul class="halo-dropdown-menu text-left" role="menu" aria-labelledby="dLabel">
		<?php $baseIndex = $builder->field? $builder->field->getBaseUnitIndex(): null; ?>
		@foreach($builder->unitList as $index => $unit)
		<?php $isBase = ($index === $baseIndex)?1:0; ?>
		<li class="@if($builder->value == $unit[HALO_UNIT_TITLE_IND]) {{'active'}} @endif"><a href="javascript:void(0);"
		                                                                                   onclick="halo.field.changeFieldUnit(this)"
		                                                                                   data-halo-rate="{{{$unit[HALO_UNIT_RATE_IND]}}}"
		                                                                                   data-halo-base="{{$isBase}}"
		                                                                                   data-halo-input="{{$builder->name}}"
		                                                                                   data-halo-value="{{{$unit[HALO_UNIT_TITLE_IND]}}}">{{{
				$unit[HALO_UNIT_TITLE_IND] }}}</a></li>
		@endforeach
	</ul>
	<input type="hidden" name="{{$builder->name}}" value="{{{$builder->value}}}" {{$builder->getValidation()}}
	{{$builder->getOnChange()}} {{$builder->getDisabled()}} {{$builder->getReadOnly()}} {{$builder->getData()}}/>
</div>
@endUI

{{-- ///////////////////////////////////// Form Basic UI: Select Privacy ///////////////////////////////////// --}}
@beginUI('form.privacy')
<?php $options = HALOPrivacy::getPrivacyOptions();
$value = empty($builder->value) ? HALO_PRIVACY_PUBLIC : $builder->value;
$current = isset($options[$value]) ? $options[$value] : null;
?>
@if(!is_null($current))
@if(!empty($builder->title))
<div class="form-group">
	<label for="{{{$builder->title}}}">{{{$builder->title}}}{{$builder->getHelpText()}}</label>
	@endif
	<div data-tree-select class="halo-btn-group {{$builder->getSize()}}">
		<button type="button" class="halo-privacy-setting halo-btn halo-btn-nobg halo-dropdown-toggle {{$builder->getAttr('btnsize',
        'btn-xs')}}"
		        data-htoggle="dropdown">
			{{HALOUIBuilder::icon($current->icon)}} <span>{{{$current->name}}}</span><i class="fa fa-caret-down"></i>
		</button>
		<ul class="halo-dropdown-menu text-left" role="menu" aria-labelledby="dLabel">
			{{HALOUIBuilder::getInstance('','form.tree_select_option',array('options'=>$options,'input'=>$builder->name))->fetch()}}
		</ul>
		<input type="hidden" class="{{ $builder->class }}" name="{{$builder->name}}" value="{{{$value}}}"
		{{$builder->getOnChange()}} {{$builder->getDisabled()}} {{$builder->getReadOnly()}}/>
	</div>
	@if(!empty($builder->title))
</div>
@endif
@else
<input type="hidden" name="{{$builder->name}}" value="{{{$value}}}"/>
@endif
@endUI

{{-- ///////////////////////////////////// Form Basic UI: View Privacy ///////////////////////////////////// --}}
@beginUI('form.viewprivacy')
<?php $options = HALOPrivacy::getPrivacyOptions();
$value = empty($builder->value) ? HALO_PRIVACY_PUBLIC : $builder->value;
$current = isset($options[$value]) ? $options[$value] : null;
?>
@if(!is_null($current))
<span title="{{{$current->name}}}">{{HALOUIBuilder::icon($current->icon)}}</span>
@endif
@endUI

{{-- ///////////////////////////////////// Form Basic UI: Select Multiple///////////////////////////// --}}
@beginUI('form.multiple_select')
@if($builder->getRow())
<div class="row"> @endif
	<div class="form-group {{$builder->class}} {{$builder->getSize()}} {{($builder->getErrorClass())}}">
		<label class="{{$builder->getValidationLabel()}}">{{{$builder->title}}}{{$builder->getHelpText()}}</label>
		<select title="{{$builder->placeholder}}" placeholder="{{$builder->placeholder}}" {{$builder->getClass('haloj-selectpicker form-control')}} name="{{$builder->name}}[]" {{$builder->getValidation()}}
		{{$builder->getOnChange()}} {{$builder->getDisabled()}} {{$builder->getReadOnly()}} {{$builder->getData()}}
		multiple>
		@foreach($builder->getOptions() as $opt)
		<option value="{{{$opt->value}}}"
		@if(in_array($opt->value,(array)$builder->value)) selected="true" @endif>{{{$opt->title}}}</option>
		@endforeach
		</select>
		@if($builder->error)<span class="help-block">{{{$builder->error}}}</span>@endif
	</div>
	@if($builder->getRow())
</div> @endif
@endUI

{{-- ///////////////////////////////////// Form Basic UI: Group Select Multiple///////////////////////////// --}}
@beginUI('form.group_select')
@if($builder->getRow())
<div class="row"> @endif
    <div class="form-group {{$builder->class}} {{$builder->getSize()}} {{($builder->getErrorClass())}}">
        <label class="{{$builder->getValidationLabel()}}">{{{$builder->title}}}{{$builder->getHelpText()}}</label>
        <select @if ($builder->placeholder)title="{{$builder->placeholder}}"@endif @if ($builder->placeholder) placeholder="{{$builder->placeholder}}" @endif {{$builder->getClass('haloj-selectpicker form-control')}} name="{{$builder->name}}[]" {{$builder->getValidation()}}
        {{$builder->getOnChange()}} {{$builder->getDisabled()}} {{$builder->getReadOnly()}} {{$builder->getData()}} multiple>
        @foreach($builder->getOptions() as $opt)
            @if ($opt->children)
                <optgroup label="{{ $opt->title }}" @if (!$opt->multiple) data-max-options="1" @endif>
                    @foreach($opt->children as $child)
                    <option @if ($child->style) data-content="<span class='label label-{{$child->style}}'>&nbsp;</span>&nbsp;&nbsp;{{{$child->title}}}" @endif value="{{{$child->value}}}" @if(in_array($child->value,(array)$builder->value)) selected="true" @endif>{{{$child->title}}}</option>
                    @endforeach
                </optgroup>
            @else
                <option value="{{{$opt->value}}}" @if(in_array($opt->value,(array)$builder->value)) selected="true" @endif>{{{$opt->title}}}</option>
            @endif
        @endforeach
        </select>
        @if($builder->error)<span class="help-block">{{{$builder->error}}}</span>@endif
    </div>
    @if($builder->getRow())
</div> @endif
@endUI

{{-- ///////////////////////////////////// Form Basic UI: Radio ///////////////////////////////////// --}}
@beginUI('form.radio')
@if($builder->getRow())
<div class="row"> @endif
	<div class="form-group {{$builder->class}} {{$builder->getSize()}}  {{($builder->getErrorClass())}}">
		<label class="{{$builder->getValidationLabel()}}">{{{$builder->title}}}{{$builder->getHelpText()}}</label>
		@foreach($builder->getOptions() as $opt)
		<label class="radio-inline">
			<input type="radio" name="{{$builder->name}}" value="{{{$opt->value}}}" @if(isset($opt->id))
			id="{{$opt->id}}" @endif @if($opt->value == $builder->value) checked="checked" @endif {{$builder->getOnChange()}}
			{{$builder->getDisabled()}} {{$builder->getReadOnly()}}> {{{$opt->title}}}
		</label>
		@endforeach
		@if($builder->error)<span class="help-block">{{{$builder->error}}}</span>@endif
	</div>
	@if($builder->getRow())
</div> @endif
@endUI

{{-- ///////////////////////////////////// Form Basic UI: Radio ///////////////////////////////////// --}}
@beginUI('form.radio_helper')
@if($builder->getRow())
<div class="row"> @endif
    <div class="form-group {{$builder->class}} {{$builder->getSize()}}  {{($builder->getErrorClass())}}">
        <label class="{{$builder->getValidationLabel()}}">{{{$builder->title}}}{{$builder->getHelpText()}}</label>
        @foreach($builder->getOptions() as $opt)
        <?php $uid = HALOUtilHelper::uniqidInt(); ?>
        <label class="radio-inline">
            <input type="radio" name="{{$builder->name}}" value="{{{$opt->value}}}" @if(isset($opt->id))
            id="{{$opt->id}}" @endif @if($opt->value == $builder->value) checked="checked" @endif {{$builder->getOnChange()}}
            {{$builder->getDisabled()}} {{$builder->getReadOnly()}} aria-describedby="{{ $uid }}" {{$builder->getValidation()}}> {{{$opt->title}}}
            <div class="help-block" id="{{ $uid }}" >{{ $opt->description }}</div>
        </label>
        @endforeach
        @if($builder->error)<span class="help-block">{{{$builder->error}}}</span>@endif
    </div>
    @if($builder->getRow())
</div> @endif
@endUI

{{-- ///////////////////////////////////// Form Basic UI: Checkbox ///////////////////////////////////// --}}
@beginUI('form.checkbox')
@if($builder->getRow())
<div class="row"> @endif
	<div class="form-group {{$builder->class}} {{$builder->getSize()}}  {{($builder->getErrorClass())}}">
		<label class="{{$builder->getValidationLabel()}}">{{{$builder->title}}}{{$builder->getHelpText()}}</label>
		@foreach($builder->getOptions() as $opt)
		<label class="checkbox-inline">
			<input {{$builder->getValidation()}} type="checkbox" name="{{$builder->name}}" value="{{{$opt->value}}}"
			@if(isset($opt->id)) id="{{$opt->id}}" @endif @if($opt->value == $builder->value) checked @endif
			{{$builder->getDisabled()}} {{$builder->getReadOnly()}}> {{{$opt->title}}}
		</label>
		@endforeach
		@if($builder->error)<span class="help-block">{{{$builder->error}}}</span>@endif
	</div>
	@if($builder->getRow())
</div> @endif
@endUI

{{-- ///////////////////////////////////// Form Basic UI: File ///////////////////////////////////// --}}
@beginUI('form.file')
@if($builder->getRow())
<div class="row"> @endif
	<div class="form-group {{$builder->class}} {{$builder->getSize()}} {{($builder->getErrorClass())}}">
		<label for="{{$builder->name}}">{{{$builder->title}}}{{$builder->getHelpText()}}</label>
		<input name="{{$builder->name}}" type="file"/>
		@if($builder->error)<span class="help-block">{{{$builder->error}}}</span>@endif
	</div>
	@if($builder->getRow())
</div> @endif
@endUI

{{-- ///////////////////////////////////// Form Basic UI: hidden ///////////////////////////////////// --}}
@beginUI('form.hidden')
<input name="{{$builder->name}}" type="hidden" value="{{{$builder->value}}}" {{$builder->getValidation()}}/>
@endUI

{{-- ///////////////////////////////////// Form Basic UI: togglable button ///////////////////////////////////// --}}
@beginUI('form.toggleState')
<a {{$builder->getZone()}} class="halo-btn halo-btn-micro" href="javascript:void(0)" onclick="halo.util.toggleState('{{$builder->id}}','{{$builder->model}}','{{$builder->field}}')" title="{{{$builder->state['title']}}}">{{$builder->icon($builder->state['icon'])}}</a>
@endUI

{{-- ///////////////////////////////////// Form Basic UI: readonly button ///////////////////////////////////// --}}
@beginUI('form.readonlyState')
<span {{$builder->getZone()}} title="{{{$builder->state['title']}}}">{{$builder->icon($builder->state['icon'])}}</span>
@endUI

{{-- ///////////////////////////////////// Form UI ///////////////////////////////////// --}}
@beginUI('form.form')
<form {{ $builder->getClass() }} id="{{$builder->name}}" name="{{$builder->name}}" @if ($builder->method) method="{{$builder->method}}" @endif action="{{$builder->action}}" onsubmit="{{$builder->onsubmit}}" target="{{$builder->target}}">
	@foreach($builder->getChildren() as $child)
	{{ $child->setRow(0)->fetch() }}
	@endforeach
</form>
@endUI

{{-- ///////////////////////////////////// Form UI ///////////////////////////////////// --}}
@beginUI('form.form_data')
<form id="{{$builder->name}}" name="{{$builder->name}}" method="post" enctype="multipart/form-data">
	@foreach($builder->getChildren() as $child)
	{{ $child->setRow(0)->fetch() }}
	@endforeach
</form>
@endUI

{{-- ///////////////////////////////////// Filter Form UI ///////////////////////////////////// --}}
@beginUI('form.filter_form')
<form id="{{$builder->name}}" name="{{$builder->name}}" class="form-inline" role="form" method="get"
      action="{{URL::full()}}">
	@foreach($builder->getChildren() as $child)
	{{ $child->fetch() }}
	@endforeach
</form>
@endUI

{{-- ///////////////////////////////////// Wizard Form UI ///////////////////////////////////// --}}
@beginUI('form.wizard')
<?php $steps = $builder->getChildren(); ?>
<form id="{{$builder->name}}" name="{{$builder->name}}" @if ($builder->method) method="{{$builder->method}}" @endif action="{{$builder->action}}" onsubmit="{{$builder->onsubmit}}" target="{{$builder->target}}">
	@if(!empty($steps))
	<div class="halo-wizard-wrapper" {{$builder->getZone()}} {{$builder->getData()}}>
		<?php $activeStep = isset($builder->activeStep)? $builder->activeStep : 1;?>
		<div class="halo-wizard-steps">
			<?php $stepIndex = 0; ?>
			@foreach($steps as $step)
				<?php $stepIndex ++;
					$stepTitle = isset($step->title)?$step->title:sprintf(__halotext('Step %s'), $stepIndex);
					$stepStatus = '';
					if($stepIndex < $activeStep) {
						$stepStatus = 'completed';
					} else if($stepIndex == $activeStep) {
						$stepStatus = 'active';
					} else {
						$stepStatus = 'disabled';
					}
				?>
				<div class="halo-wizard-step-title {{$stepStatus}} {{$step->class}}">
					<a href="" data-target="#halo_wizard_step{{$stepIndex}}" data-htoggle="wizard"><span>{{$stepIndex}}</span>{{$stepTitle}}</a>
				</div>
			@endforeach
		</div>
		<div class="clearfix"></div>
		<div class="halo-wizard-content">
			<?php $stepIndex = 0; ?>
			@foreach($steps as $step)
				<?php $stepIndex ++;
					$stepContent = isset($step->content)?$step->content:'';
				?>
				<div class="@if($stepIndex == $activeStep) active @endif halo-wizard-step-content" id="halo_wizard_step{{$stepIndex}}">
					{{$stepContent}}
				</div>
		
			@endforeach
		</div>
		<div class="halo-wizard-actions halo-center-block">
			<div class="halo-btn-group">
			  <button type="button" @if($builder->onPrevious) onclick="{{$builder->onPrevious}}" @endif class="halo-wizard-btn-previous halo-btn halo-btn-default">{{__halotext('Previous')}}</button>
			  <button type="button" @if($builder->onNext) onclick="{{$builder->onNext}}" @endif class="halo-wizard-btn-next halo-btn halo-btn-success">{{__halotext('Next')}}</button>
			  @if($builder->onFinish)
			  <button type="button" onclick="{{$builder->onFinish}}" class="halo-wizard-btn-finish halo-btn halo-btn-primary">@if($builder->getData('finishTitle')) {{$builder->data['finishTitle']}} @else {{__halotext('Finish')}} @endif</button>
			  @endif 
			  @if($builder->onCancel)
			  <button type="button" onclick="{{$builder->onCancel}}" class="halo-wizard-btn-cancel halo-btn halo-btn-default">{{__halotext('Cancel')}}</button>
			  @endif
			</div>
		</div>
	</div>
	@endif
	<input type="hidden" name="_token" value="{{ csrf_token() }}">
	@if($builder->hidden && is_array($builder->hidden))
		@foreach($builder->hidden as $name => $value)
			<input type="hidden" name="{{$name}}" value="{{$value}}">
		@endforeach
	@endif
</form>
@endUI

{{-- ///////////////////////////////////// Wizard Step UI ///////////////////////////////////// --}}
@beginUI('form.wizard_step')
	
@endUI

{{-- ///////////////////////////////////// Filter Select UI ///////////////////////////////////// --}}
@beginUI('form.filter_select')
<div class="form-group">
	<label for="{{$builder->name}}">{{{$builder->title}}}{{$builder->getHelpText()}}</label>
	<select name="{{$builder->name}}" class="form-control" onchange="halo.pagination.changeFilter()"
	{{$builder->getDisabled()}} {{$builder->getReadOnly()}}>
	<option value=""
	@if(trim($builder->value) === '') selected="true" @endif>{{__halotext('No Filter')}}</option>
	@foreach($builder->getOptions() as $opt)
	<option value="{{{$opt->value}}}"
	@if(trim($opt->value) === trim($builder->value)) selected="true" @endif>{{{$opt->title}}}</option>
	@endforeach
	</select>
</div>
@endUI

{{-- ///////////////////////////////////// Filter Text UI ///////////////////////////////////// --}}
@beginUI('form.filter_columnsearch')
<div class="form-group">
	<label for="{{$builder->name}}">{{{$builder->title}}}{{$builder->getHelpText()}}</label>
	<input type="text" value="{{$builder->value}}" name="{{$builder->name}}" class="form-control" onchange="halo.pagination.changeFilter()"
	{{$builder->getDisabled()}} {{$builder->getReadOnly()}}/>
</div>
@endUI

{{-- ///////////////////////////////////// Filter Sort Select UI ///////////////////////////////////// --}}
@beginUI('form.filter_sort_select')
<?php
$value = $builder->value;
$sort = isset($value['sort']) ? trim($value['sort']) : '';
$dir = isset($value['dir']) ? trim($value['dir']) : '';
$options = $builder->options;
$sortOpts = $options['sort'];
$dirOpts = $options['dir'];
?>
<div class="form-group">
	<label for="{{$builder->name}}">{{{$builder->title}}}{{$builder->getHelpText()}}</label>
	<select name="{{$builder->name}}[sort]" class="form-control" onchange="halo.pagination.changeFilter()"
	{{$builder->getDisabled()}} {{$builder->getReadOnly()}}>
	<option value=""
	@if( $sort === '') selected="true" @endif>{{__halotext('--No Select--')}}</option>
	@foreach($sortOpts as $opt)
	<option value="{{{$opt->value}}}"
	@if(trim($opt->value) === $sort) selected="true" @endif>{{{$opt->title}}}</option>
	@endforeach
	</select>
	<select name="{{$builder->name}}[dir]" class="form-control" onchange="halo.pagination.changeFilter()"
	{{$builder->getDisabled()}} {{$builder->getReadOnly()}}>
	<option value=""
	@if($dir === '') selected="true" @endif>{{__halotext('--No Select--')}}</option>
	@foreach($dirOpts as $opt)
	<option value="{{{$opt->value}}}"
	@if(trim($opt->value) === $dir) selected="true" @endif>{{{$opt->title}}}</option>
	@endforeach
	</select>
</div>
@endUI

{{-- ///////////////////////////////////// Filter Multiple select tree UI ///////////////////////////////////// --}}
@beginUI('form.filter_tree_chechbox')
<?php $options = $builder->options;
$builder->input = empty($builder->name) ? $builder->input : $builder->name;
$filterLevel = empty($builder->level) ? 0 : $builder->level;
$nextLevel = intval($filterLevel) + 1; ?>
@if(!empty($options))
	@if(is_object($builder->filter))
	<div class="halo-filter-rule row halo-filter-{{Str::slug($builder->filter->getParams('title'))}}">
		<div class="halo-filter-title-wrapper col-md-2">
			<h4 class="halo-filter-title">{{__halotext($builder->filter->getParams('title'))}}{{$builder->getHelpText()}}</h4>
		</div>
		<ul class="halo-filter-content-wrapper col-md-10 haloc-list list-unstyled @if(!empty($filterLevel)) hidden filter-lv-{{$filterLevel}} halo-toggle-display@endif">
			@foreach($options as $opt)
			@if(empty($opt->_children))
			<li class="halo-filter-item col-md-4"><a
					class="@if($filterLevel==0) halo-filter-item-title-lv0 @endif halo-filter-toggle a-unstyled"
					id="{{HALOUtilHelper::uniqidInt()}}" data-halo-input="{{$builder->input}}"
					data-halo-value="{{{$opt->value}}}">{{HALOUIBuilder::icon('square-o')}} {{ $opt->name }} {{
					isset($opt->icon)?HALOUIBuilder::icon($opt->icon):'' }}</a></li>
			@else
			<li class="halo-filter-item col-md-4"><a class="halo-filter-toggle halo-tree-node a-unstyled"
																						 id="{{HALOUtilHelper::uniqidInt()}}" data-halo-input="{{$builder->input}}"
																						 data-halo-value="{{{$opt->value}}}">{{HALOUIBuilder::icon('square-o')}}</a>
				<a class="@if($filterLevel==0) halo-filter-item-title-lv0 @endif a-unstyled" data-htoggle="display"
					 data-siblings=".filter-lv-{{$nextLevel}}">{{HALOUIBuilder::icon('caret-right')}} {{ $opt->name
					}}{{$builder->getIcon()}}</a>
				{{HALOUIBuilder::getInstance('','form.filter_tree_chechbox',array('options'=>$opt->_children,'input'=>$builder->input,'level'=>$nextLevel))->fetch()}}
			</li>
			@endif
			@endforeach
		</ul>
		@endif
		@if(!empty($builder->name))
		<input type="hidden" value="{{{$builder->value}}}" name="{{$builder->name}}"/>
	</div>
	@endif
@endif
@endUI
{{-- ///////////////////////////////////// Filter date range UI ///////////////////////////////////// --}}
@beginUI('form.filter_date_range')
@if(is_object($builder->filter))
<?php $uid = HALOUtilHelper::uniqidInt();
	$startDate = isset($builder->value['startdate'])?$builder->value['startdate']:'';
	$endDate = isset($builder->value['enddate'])?$builder->value['enddate']:'';
	$title = __halotext($builder->filter->getParams('title'));
	$now = new Carbon('now');
?>
<div class="halo-filter-rule row halo-filter-{{Str::slug($builder->filter->getParams('title'))}}">
	<div class="halo-filter-title-wrapper col-md-2">
		<h4 class="halo-filter-title">{{$title}}{{$builder->getHelpText()}}</h4>
	</div>
	<div class="halo-filter-content-wrapper col-md-10">
		<div class="" id="{{$uid}}">
		<div class="col-md-6 input-group halo_filter_start_date date form_date" data-date="{{Carbon::now()}}" data-date-format="dd-mm-yyyy" data-date-enddate="{{$now}}">
			<input class="form-control halo-filter-text {{ $builder->class }}" type="text" placeholder="{{__halotext('Start date')}}" value="{{{$startDate}}}" readonly
			{{$builder->getValidation()}} id="{{HALOUtilHelper::uniqidInt()}}"
			value="{{{$startDate}}}" data-halo-input="{{$builder->name}}[startdate]" data-halo-value="{{{$startDate}}}"
			data-halo-label="{{$title}} ({{{__halotext('>')}}})">
			<span class="input-group-addon"><span class="fa fa-times"></span></span>
			<span class="input-group-addon"><span class="fa fa-calendar"></span></span>
			<input type="hidden" name="{{$builder->name}}[startdate]" value="{{{$startDate}}}">
		</div>
		<div class="col-md-6 input-group halo_filter_end_date date form_date" data-date="{{Carbon::now()}}" data-date-format="dd-mm-yyyy" data-date-enddate="{{$now}}">
			<input class="form-control halo-filter-text {{ $builder->class }}" type="text" placeholder="{{__halotext('End date')}}" value="{{{$endDate}}}" readonly
			{{$builder->getValidation()}} id="{{HALOUtilHelper::uniqidInt()}}"
			value="{{{$endDate}}}" data-halo-input="{{$builder->name}}[enddate]" data-halo-value="{{{$endDate}}}"
			data-halo-label="{{$title}} ({{{__halotext('<')}}})">
			<span class="input-group-addon"><span class="fa fa-times"></span></span>
			<span class="input-group-addon"><span class="fa fa-calendar"></span></span>
			<input type="hidden" name="{{$builder->name}}[enddate]" value="{{{$endDate}}}">
		</div>
		</div>
	</div>
</div>
<script type="text/javascript">
__haloReady(function() {
	HALOModernizr.load({
		load    : "{{URL::to('/assets/js/bootstrap-datetimepicker.js')}}",
		complete: function () {
			halo.field.initDateRange('{{$uid}}','.halo_filter_start_date','.halo_filter_end_date');
		}
	});
});
</script>
@endif
@endUI
{{-- ///////////////////////////////////// Filter Single select tree UI ///////////////////////////////////// --}}
@beginUI('form.filter_tree_radio')
<?php $options = $builder->options;
$builder->input = empty($builder->name) ? $builder->input : $builder->name;
$filterLevel = empty($builder->level) ? 0 : $builder->level;
$nextLevel = intval($filterLevel) + 1 ?>
@if(!empty($options))
	@if(is_object($builder->filter))
		<div class="halo-filter-rule row halo-filter-{{Str::slug($builder->filter->getParams('title'))}}">
			<div class="halo-filter-title-wrapper col-md-2">
				<h4 class="halo-filter-title">{{__halotext($builder->filter->getParams('title'))}}{{$builder->getHelpText()}}</h4>
			</div>
	@endif
		<ul class="halo-filter-content-wrapper col-md-10 halo-single-select list-unstyled @if(!empty($filterLevel)) hidden filter-lv-{{$filterLevel}} halo-toggle-display@endif">
			@foreach($options as $opt)
				@if(empty($opt->_children))
					<li class="halo-filter-item col-md-4"><a
							class="@if($filterLevel==0) halo-filter-item-title-lv0 @endif halo-filter-toggle a-unstyled"
							id="{{HALOUtilHelper::uniqidInt()}}" data-halo-input="{{$builder->input}}"
							data-halo-value="{{{$opt->value}}}">{{HALOUIBuilder::icon('square-o')}} {{ $opt->name }} {{
							isset($opt->icon)?HALOUIBuilder::icon($opt->icon):'' }}</a></li>
				@else
					<li class="halo-filter-item col-md-4"><a class="halo-filter-toggle a-unstyled" id="{{HALOUtilHelper::uniqidInt()}}"
														   data-halo-input="{{$builder->input}}" data-halo-value="{{{$opt->value}}}">{{HALOUIBuilder::icon('square-o')}}</a>
						<a class="@if($filterLevel==0) halo-filter-item-title-lv0 @endif a-unstyled" data-htoggle="display"
						   data-siblings=".filter-lv-{{$nextLevel}}">{{HALOUIBuilder::icon('caret-right')}} {{ $opt->name
							}}{{$builder->getIcon()}}</a>
						{{HALOUIBuilder::getInstance('','form.filter_tree_radio',array('options'=>$opt->_children,'input'=>$builder->input,'level'=>$nextLevel))->fetch()}}
					</li>
				@endif
			@endforeach
		</ul>
	@if(!empty($builder->name))
		<input type="hidden" value="{{{$builder->value}}}" name="{{$builder->name}}"/>
	@endif
	@if(is_object($builder->filter))
	</div>
	@endif
@endif
@endUI

{{-- ///////////////////////////////////// Custom Filter UI ///////////////////////////////////// --}}
@beginUI('form.filter_custom')
<?php $options = $builder->options;
$builder->input = empty($builder->name) ? $builder->input : $builder->name; ?>
@if(!empty($options))
@if(is_object($builder->filter))
<div class="halo-filter-rule row halo-filter-{{Str::slug($builder->filter->getParams('title'))}}">
	<div class="halo-filter-title-wrapper col-md-2">
		<h4 class="halo-filter-title">{{__halotext($builder->filter->getParams('title'))}}{{$builder->getHelpText()}}</h4>
	</div>
	@endif
	<div class="halo-filter-content-wrapper col-md-10">
		<ul class="halo-single-select list-unstyled">
			@foreach($options as $opt)
			<li class="halo-filter-item col-md-4"><a class="halo-filter-toggle a-unstyled"
			                                       id="{{HALOUtilHelper::uniqidInt()}}"
			                                       data-halo-input="{{$builder->input}}"
			                                       data-halo-value="{{{$opt->value}}}">{{HALOUIBuilder::icon('square-o')}}
					{{{ $opt->name }}} {{ isset($opt->icon)?HALOUIBuilder::icon($opt->icon):'' }}</a></li>
			@endforeach
		</ul>
	</div>
	@endif
	@if(!empty($builder->name))
	<input type="hidden" value="{{{$builder->value}}}" class="halo-customfilter" data-filterid="{{$builder->filter->id}}"
	       name="{{$builder->name}}"/>
	@endif
	@if(is_object($builder->filter))
</div>
@endif
@endUI

{{-- ///////////////////////////////////// Filter Sort Select UI ///////////////////////////////////// --}}
@beginUI('form.filter_tree_sort')
<?php //the tree sort is a combination of 2 filter_tree_radio UIs for sort critical and direction
$value = $builder->value;
$sort = isset($value['sort']) ? trim($value['sort']) : '';
$dir = isset($value['dir']) ? trim($value['dir']) : '';
$options = $builder->options;
$sortOpts = $options['sort'];
$dirOpts = $options['dir'];
?>
@if(is_object($builder->filter))
<div class="halo-filter-rule row halo-filter-{{Str::slug($builder->filter->getParams('title'))}}">
	<div class="halo-filter-title-wrapper col-md-2">
		<h4 class="halo-filter-title">{{__halotext($builder->filter->getParams('title'))}}</h4>
	</div>
	@endif
	<div class="halo-filter-content-wrapper col-md-10">
		{{HALOUIBuilder::getInstance('','form.filter_tree_radio',array('name'=>$builder->name .
		'[sort]','value'=>$sort,'options'=>$sortOpts))->fetch()}}
		{{HALOUIBuilder::getInstance('','form.filter_tree_radio',array('name'=>$builder->name .
		'[dir]','value'=>$dir,'options'=>$dirOpts))->fetch()}}
	</div>
	@if(is_object($builder->filter))
</div>
@endif
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

{{-- ///////////////////////////////////// Filter Date Range Select UI ///////////////////////////////////// --}}
@beginUI('form.filter_daterange')
<?php
$uid = HALOUtilHelper::uniqidInt();
?>
@if (is_object($builder->filter))
<div class="halo-filter-rule row halo-daterange-control halo-filter-{{Str::slug($builder->filter->getParams('title'))}}" id="{{$uid}}">
	<div class="halo-filter-title-wrapper col-md-2">
    	<h4 class="halo-filter-title">{{__halotext($builder->filter->getParams('title'))}}{{$builder->getHelpText()}}</h4>
    </div>
    <div class="halo-filter-content-wrapper col-md-10">
    	<div class="input-group halo-filter-daterange-input-wrapper">
			{{HALOUIBuilder::getInstance('', 'form.date', array('name' => 'start_date', 'title' => __halotext('From'), 'value' => '', 'onchange' => "halo.home.refreshSection('post')"))->fetch()}}
			{{HALOUIBuilder::getInstance('', 'form.date', array('name' => 'end_date', 'title' => __halotext('To'), 'value' => '', 'onchange' => "halo.home.refreshSection('post')"))->fetch()}}
    	</div>
    </div>
</div>
@endif
@endUI

{{-- ///////////////////////////////////// Filter Text UI ///////////////////////////////////// --}}
@beginUI('form.filter_text')
<?php $builder->input = empty($builder->name) ? $builder->input : $builder->name; ?>
@if(is_object($builder->filter))
<div class="halo-filter-rule row halo-filter-{{Str::slug($builder->filter->getParams('title'))}}">
	<div class="halo-filter-title-wrapper col-md-2">
		<h4 class="halo-filter-title">{{__halotext($builder->filter->getParams('title'))}}{{$builder->getHelpText()}}</h4>
	</div>
	@endif
	<div class="halo-filter-content-wrapper col-md-10">
		<div class="form-group">
			<input type="text" value="{{{$builder->value}}}" id="{{HALOUtilHelper::uniqidInt()}}" {{$builder->getData()}}
			class="halo-filter-text input-sm form-control {{$builder->class}}" placeholder="{{{$builder->placeholder}}}"
			data-halo-input="{{$builder->input}}" data-halo-value="{{{$builder->value}}}">
			<input type="hidden" name="{{$builder->name}}" value="{{{$builder->value}}}"
			       onchange="halo.pagination.changeFilter()">
		</div>
	</div>
	@if(is_object($builder->filter))
</div>
@endif
@endUI

{{-- ///////////////////////////////////// Filter Tag UI ///////////////////////////////////// --}}
@beginUI('form.filter_tag')
<?php $builder->input = empty($builder->name) ? $builder->input : $builder->name; 
$uid = HALOUtilHelper::uniqidInt();
?>
@if(is_object($builder->filter))
<div class="halo-filter-rule row halo-filter-{{Str::slug($builder->filter->getParams('title'))}}">
	<div class="halo-filter-title-wrapper col-md-2">
		<h4 class="halo-filter-title">{{__halotext($builder->filter->getParams('title'))}}{{$builder->getHelpText()}}</h4>
	</div>
	@endif
	<div class="halo-filter-content-wrapper col-md-10">
		<div class="form-group">
			<div class="halo-hash-tag-container"></div>
			<input type="text" value="{{{$builder->value}}}" {{$builder->getData()}}
			class="halo-hash-tag input-sm form-control {{$builder->class}}" placeholder="{{{$builder->placeholder}}}"
			data-output=".filter_tag_{{$uid}}" data-only-tag-list="true" data-sync-on-change="true">
			<input type="hidden" class="halo-filter-tag filter_tag_{{$uid}}" id="{{HALOUtilHelper::uniqidInt()}}" 
				name="{{$builder->name}}" value="{{{$builder->value}}}">
		</div>
	</div>
	@if(is_object($builder->filter))
</div>
@endif
@endUI


{{-- ///////////////////////////////////// Edit Field Form ///////////////////////////////////// --}}
@beginUI('form.edit_field')
<form id="{{$builder->name}}" name="{{$builder->name}}">
	{{$builder->getChild('name')->fetch()}}
	{{$builder->getChild('type')->fetch()}}
	{{$builder->getChild('published')->fetch()}}
	{{$builder->getChild('required')->fetch()}}
	{{$builder->getChild('highlight')->fetch()}}
	{{$builder->getChild('privacy')->fetch()}}
	{{$builder->getChild('ordering')->fetch()}}
	{{$builder->getChild('fieldcode')->fetch()}}
	{{$builder->getChild('tooltip')->fetch()}}
	<div class="gp-wrapper" id="fieldConfig">
		{{$builder->getChild('config')->fetch()}}
	</div>
</form>
@endUI

{{-- ///////////////////////////////////// Profile readonly field ///////////////////////////////////// --}}
@beginUI('form.readonly_field')
<dl class="halo-readonly-field-{{$builder->type}}">
	<dt>{{{$builder->title}}}</dt>
	<dd>{{$builder->value}}</dd>
</dl>
@endUI

{{-- ///////////////////////////////////// Tree Select UI///////////////////////////////////// --}}
@beginUI('form.tree_select_old')
@if($builder->getRow())
<div class="row"> @endif
	<div class="form-group {{$builder->getSize()}}">
		<label class="{{$builder->getValidationLabel()}}" for="{{$builder->name}}">{{{$builder->title}}}{{$builder->getHelpText()}}</label>

		<div data-tree-select class="halo-btn-group {{$builder->getSize()}}  {{($builder->getErrorClass())}}">
			<button data-feedback="{{$builder->name}}" type="button" class="halo-btn halo-btn-default halo-dropdown-toggle"
			        data-htoggle="dropdown">
				{{$builder->title}} <i class="fa fa-caret-down"></i>
			</button>
			<ul class="halo-dropdown-menu" role="menu" aria-labelledby="dLabel">
				{{HALOUIBuilder::getInstance('','form.tree_select_option',array('options'=>$builder->options,'input'=>$builder->name,'leafOnly'=>$builder->leafOnly))->fetch()}}
			</ul>
			<input type="hidden" data-rule-feedback="{{$builder->name}}" class="{{ $builder->class }}"
			       name="{{$builder->name}}" value="{{{$builder->value}}}" {{$builder->getValidation()}}
			{{$builder->getOnChange()}} {{$builder->getDisabled()}} {{$builder->getReadOnly()}}/>
			@if($builder->error)<span class="help-block">{{{$builder->error}}}</span>@endif
		</div>
	</div>
	@if($builder->getRow())
</div> @endif
@endUI

{{-- ///////////////////////////////////// Tree Select Option UI///////////////////////////////////// --}}
@beginUI('form.tree_select_option')
<?php $options = $builder->options;
$builder->input = empty($builder->name) ? $builder->input : $builder->name; ?>
@if(!empty($options))
@foreach($options as $opt)
@if(empty($opt->_children))
<li><a href="javascript:void(0);" onclick="halo.form.changeTreeSelectOption(this)" data-halo-input="{{$builder->input}}"
       data-halo-value="{{{$opt->value}}}">{{ isset($opt->icon)?HALOUIBuilder::icon($opt->icon):'' }}<span>{{{ $opt->name }}}</span></a>
</li>
@else
<li class="halo-tree-select-submenu"><a data-htoggle="display" href="javascript:void(0);" @if(!$builder->leafOnly)
	onclick="halo.form.changeTreeSelectOption(this)" @else onclick="halo.form.selectTreeNode()" @endif
	data-halo-input="{{$builder->input}}" data-halo-value="{{{$opt->value}}}"><span>{{{ $opt->name }}}</span>{{$builder->getIcon()}}</a>
	<ul class="halo-tree-select-menu halo-toggle-display hidden" role="menu" aria-labelledby="dLabel">
		{{HALOUIBuilder::getInstance('','form.tree_select_option',array('options'=>$opt->_children,'input'=>$builder->input,'leafOnly'=>$builder->leafOnly))->fetch()}}
	</ul>
</li>
@endif
@endforeach
@endif
@endUI

{{-- ///////////////////////////////////// Tree Select UI///////////////////////////////////// --}}
@beginUI('form.tree_select')
@if($builder->getRow())
<div class="row"> @endif
	<div class="form-group {{$builder->getSize()}}">
		@if($builder->title)<label class="{{$builder->getValidationLabel()}}" for="{{$builder->name}}">{{{$builder->title}}}{{$builder->getHelpText()}}</label>@endif

		<div data-tree-select class="{{$builder->getSize()}}  {{($builder->getErrorClass())}}">
			<button data-feedback="{{$builder->name}}" type="button" class="halo-btn halo-btn-default halo-dropdown-toggle"
			        data-htoggle="display">
				@if($builder->title){{$builder->title}}@else{{$builder->placeholder}}@endif <i class="fa fa-caret-down"></i>
			</button>
			<ul class="halo-tree-select-menu halo-toggle-display hidden" role="menu" aria-labelledby="dLabel">
				{{HALOUIBuilder::getInstance('','form.tree_select_data',array('options'=>$builder->options,'input'=>$builder->name,'leafOnly'=>$builder->leafOnly))->fetch()}}
			</ul>
			<input type="hidden" data-rule-feedback="{{$builder->name}}" class="{{ $builder->class }}"
			       name="{{$builder->name}}" value="{{{$builder->value}}}" {{$builder->getValidation()}}
			{{$builder->getOnChange()}} {{$builder->getDisabled()}} {{$builder->getReadOnly()}}/>
			@if($builder->error)<span class="help-block">{{{$builder->error}}}</span>@endif
		</div>
	</div>
	@if($builder->getRow())
</div> @endif
@endUI


{{-- ///////////////////////////////////// Tree Select Option Data UI///////////////////////////////////// --}}
@beginUI('form.tree_select_data')
<?php $options = $builder->options;
$builder->input = empty($builder->name) ? $builder->input : $builder->name; ?>
@if(!empty($options))
@foreach($options as $opt)
@if(empty($opt->_children))
<li class="col-md-4"><a href="javascript:void(0);"
                        onclick="halo.form.changeTreeSelectOption(this)"
                        data-halo-input="{{$builder->input}}"
                        data-halo-value="{{{$opt->value}}}">{{ isset($opt->icon)?HALOUIBuilder::icon($opt->icon):'' }}<span>{{{ $opt->name }}}</span></a>
</li>
@else
<li class="halo-tree-select-submenu col-md-4"><a href="javascript:void(0);" class="halo-tree-level-title"
	@if(!$builder->leafOnly)
	onclick="halo.form.changeTreeSelectOption(this)" @else onclick="halo.form.selectTreeNode(this)" @endif
	data-halo-input="{{$builder->input}}" data-halo-value="{{{$opt->value}}}"><span>{{{ $opt->name }}}</span>{{$builder->getIcon()}}</a>
	<a href="javascript:void(0)" class="halo-tree-change-level">{{HALOUIBuilder::icon('angle-double-right')}}</a>
	<ul class="halo-tree-select-menu halo-toggle-display hidden" role="menu" aria-labelledby="dLabel">
		{{HALOUIBuilder::getInstance('','form.tree_select_data',array('options'=>$opt->_children,'input'=>$builder->input,'leafOnly'=>$builder->leafOnly))->fetch()}}
	</ul>
</li>
@endif
@endforeach
@endif
@endUI


{{-- ///////////////////////////////////// Filter tree node UI ///////////////////////////////////// --}}
@beginUI('form.tree_filter')
@if($builder->getRow())
<div class="row"> @endif
	<div class="form-group {{$builder->getSize()}}">
		<label for="{{$builder->name}}">{{{$builder->title}}}</label>

		<div data-tree-select class="halo-btn-group {{$builder->getSize()}}  {{($builder->getErrorClass())}}">
			<button type="button" class="halo-btn halo-btn-default halo-dropdown-toggle" data-htoggle="dropdown">
				{{$builder->title}} <i class="fa fa-caret-down"></i>
			</button>
			<ul class="halo-dropdown-menu" role="menu" aria-labelledby="dLabel">
				{{HALOUIBuilder::getInstance('','form.tree_select_option',array('options'=>$builder->options,'input'=>$builder->name))->fetch()}}
			</ul>
		</div>
		<input data-halo-multiple type="hidden" value="{{{$builder->value}}}" name="{{$builder->name}}"
		{{$builder->getOnChange()}} {{$builder->getDisabled()}} {{$builder->getReadOnly()}}/>
		@if($builder->error)<span class="help-block">{{{$builder->error}}}</span>@endif
	</div>
	@if($builder->getRow())
</div> @endif
@endUI


{{-- ///////////////////////////////////// Form Basic UI: json table ///////////////////////////////////// --}}
@beginUI('form.jsontable')
<div class="form-group halo-jsontable-container {{$builder->getSize()}} {{($builder->getErrorClass())}} {{{$builder->class}}}">
	<label for="{{$builder->name}}">{{{$builder->title}}}{{$builder->getHelpText()}}</label>

	<div class="input-group">
		<div class="halo-jsontable-wrapper table-responsive">
			<table style="table-layout:fixed;" class="table table-halo-json-editable table-condensed table-hover"
			       data-jsontable data-jsoninput="{{$builder->name}}"
			@if($builder->getReadOnly()){{'data-jsonreadonly="readonly"'}}@endif>
			</table>
		</div>
		<input type="hidden" value="{{{$builder->value}}}" data-default="{{{$builder->default}}}"
		       name='{{$builder->name}}' {{$builder->getOnChange()}} {{$builder->getReadOnly()}}/>
	</div>
	@if($builder->error)<span class="help-block">{{{$builder->error}}}</span>@endif
</div>
@endUI

{{-- ///////////////////////////////////// Hidden input UI ///////////////////////////////////// --}}
@beginUI('form.hidden_input')
@if(is_array($builder->value))
@foreach($builder->value as $key => $val)
{{HALOUIBuilder::getInstance('','form.hidden_input',array('name'=>$key,'value'=>$val
,'prefix'=>empty($builder->prefix)?$builder->name:$builder->prefix .'[' .$builder->name .']'))->fetch()}}
@endforeach
@else
<input type="hidden" name="{{ empty($builder->prefix)?$builder->name:$builder->prefix .'[' .$builder->name .']' }}"
       value="{{{$builder->value}}}"/>
@endif
@endUI

{{-- ///////////////////////////////////// Location input UI ///////////////////////////////////// --}}
@beginUI('form.location')
<?php $uid = HALOUtilHelper::uniqidInt(); ?>
<div class="form-group {{$builder->class}} halo-location-control {{$builder->getSize()}} {{($builder->getErrorClass())}}"
     id="{{$uid}}">
	@if($builder->title)<label for="{{$builder->name}}" class="{{$builder->getValidationLabel()}}">{{{$builder->title}}}{{$builder->getHelpText()}}</label>@endif

	<div class="halo-input-group-append halo-location-input-group">
		<a class="halo-btn halo-btn-nobg halo-input-icon halo-field-location-detect"
		   href="javascript:void(0);">
			{{HALOUIBuilder::icon('search-plus')}}
		</a>
		<input class="halo-input-control form-control halo-field-location-name {{ $builder->class }}"
		       name="{{$builder->name}}[name]"
		       type="text"
		       placeholder="{{{$builder->placeholder}}}"
		       value="{{{HALOOutputHelper::utf8_urldecode($builder->value['name'])}}}" 
		       {{$builder->getValidation()}} 
		       {{$builder->getData()}}
			   {{$builder->getDisabled()}} 
			   {{$builder->getReadOnly()}}/>

		<input class="halo-field-location-lat {{ $builder->class }}" name="{{$builder->name}}[lat]" type="hidden"
		       value="{{{$builder->value['lat']}}}"/>

		<input class="halo-field-location-lng {{ $builder->class }}" name="{{$builder->name}}[lng]" type="hidden"
		       value="{{{$builder->value['lng']}}}"/>
	</div>
	@if($builder->error)<span class="help-block">{{{$builder->error}}}</span>@endif
</div>
@endUI


{{-- ///////////////////////////////////// Filter date range UI ///////////////////////////////////// --}}
@beginUI('form.date_range')
<?php
	$startDate = isset($builder->value['startdate'])?$builder->value['startdate']:'';
	$endDate = isset($builder->value['enddate'])?$builder->value['enddate']:'';
	$timeZone = isset($builder->value['timezone'])?$builder->value['timezone']:date_default_timezone_get();
	$now = Carbon::now();
	$startId = HALOUtilHelper::uniqidInt();
	$endId = HALOUtilHelper::uniqidInt();
?>
<div class="form-group {{$builder->class}} halo-daterange-control {{$builder->getSize()}} {{($builder->getErrorClass())}}">
	@if($builder->titleFrom)<label for="{{$builder->name}}" class="{{$builder->getValidationLabel()}}">{{{$builder->titleFrom}}}{{$builder->getHelpText()}}</label>@endif
	<div class="halo-input-group-append">
		<div class="input-group">
			<div class="input-group start_date" data-date="{{Carbon::now()}}" data-date-format="dd-mm-yyyy" data-date-enddate="{{$now}}">
				<input class="form-control start-date-input {{ $builder->class }}" type="text" placeholder="{{__halotext('Start date')}}" value="{{{$startDate}}}" readonly
				{{$builder->getValidation()}} id="{{$startId}}"
				name="{{$builder->name}}_startdate"
				data-halo-label="{{{__halotext('From')}}}">
				<span class="input-group-addon"><span class="fa fa-times"></span></span>
			</div>
		</div>
	</div>
	@if($builder->error)<span class="help-block">{{{$builder->error}}}</span>@endif
</div>
<div class="form-group {{$builder->class}} halo-daterange-control {{$builder->getSize()}} {{($builder->getErrorClass())}}">
	@if($builder->titleTo)<label for="{{$builder->name}}">{{{$builder->titleTo}}}{{$builder->getHelpText()}}</label>@endif
	<div class="halo-input-group-append">
		<div class="input-group">
			<div class="input-group end_date" data-date="{{Carbon::now()}}" data-date-format="dd-mm-yyyy" data-date-enddate="{{$now}}">
				<input class="form-control end-date-input {{ $builder->class }}" type="text" placeholder="{{__halotext('End date')}}" value="{{{$endDate}}}" readonly
				{{$builder->getValidation()}} id="{{$endId}}" name="{{$builder->name}}_enddate"
				data-halo-label="{{{__halotext('To')}}}">
				<span class="input-group-addon"><span class="fa fa-times"></span></span>
			</div>
		</div>
	</div>
	@if($builder->error)<span class="help-block">{{{$builder->error}}}</span>@endif
</div>
{{HALOUIBuilder::getInstance('', 'form.select', array('name' => $builder->name . '_timezone', 'value' => $timeZone,
	'title' => __halotext('Timezone'), 'options' => HALOUtilHelper::getTimeZoneOptions()
))->fetch()}}

<script type="text/javascript">
__haloReady(function() {
	HALOModernizr.load({
		load    : "{{HALOAssetHelper::to('/assets/js/bootstrap-datetimepicker.js')}}",
		complete: function () {
			halo.form.initDateRange('{{$startId}}','{{$endId}}');
		}
	});
});
</script>
@endUI

{{-- ///////////////////////////////////// Location input UI ///////////////////////////////////// --}}
@beginUI('form.inline_location')
<?php $uid = HALOUtilHelper::uniqidInt(); 
?>
@if($builder->target)
<div class="form-group halo-inline-location {{$builder->class}}" id="{{$uid}}" data-halozone="inline.loc.{{$builder->target->getZone()}}">
	<div class="halo-input-group-prepend halo-location-input-group">
@if($builder->location)
		{{$builder->location->getDisplayLink()}}
		<?php $editTitle = __halotext('Edit');?>
@else
	<div>{{__halotext('No Location Found')}}</div>
	<?php $editTitle = __halotext('Add Location');?>
@endif
		@if(method_exists($builder->target,'getEditUrl'))
		<span> <a target="_blank" onclick="halo.form.editInlineLocation(this)" href="{{$builder->target->getEditUrl()}}" data-context="{{$builder->target->getContext()}}"
						data-targetid="{{$builder->target->id}}">{{$editTitle}}</a></span>
		@endif
	</div>
</div>
@endif
@endUI

{{-- ///////////////////////////////////// Tag input UI ///////////////////////////////////// --}}
@beginUI('form.tag')
<span class="tm-tag tm-tag-{{$builder->style ? $builder->style : 'success'}} {{$builder->class}}">
	<span>@if($builder->onClick)<a href="javascript:void(0)" onclick="{{$builder->onClick}}">@endif{{{$builder->title}}}@if($builder->onClick)</a>@endif</span>
	<a href="javascript:void(0)"
	   class="tm-tag-remove" @if($builder->onRemove)onclick="{{$builder->onRemove}}"@endif>x</a>
</span>
@endUI

{{-- ///////////////////////////////////// Form Basic UI: User tag ///////////////////////////////////// --}}
@beginUI('form.user_tag')
<div
	class="form-group {{$builder->class}} {{$builder->getSize()}} {{($builder->getErrorClass())}}">
	@if($builder->title)<label for="{{$builder->name}}" class="{{$builder->getValidationLabel()}}">{{{$builder->title}}}{{$builder->getHelpText()}}</label>@endif
	@if($builder->value && ($user = HALOUserModel::getUser($builder->value)))
		{{HALOUIBuilder::getInstance('','form.tag',array('title'=>$user->getDisplayName(),'onRemove'=>'halo.form.removeUserTag(this)'))->fetch()}}
	@endif
	<input class="form-control halo-user-tag {{ $builder->class }}" type="text"
				 placeholder="{{{$builder->placeholder}}}" {{$builder->getValidation()}}
	{{$builder->getDisabled()}} {{$builder->getReadOnly()}} {{$builder->getData()}}/>
	@if($builder->error)
	<span class="fa fa-times form-control-feedback"></span>
	<span class="help-block">{{{$builder->error}}}</span>
	@endif
	<input type="hidden" class="halo-user-tag-value" name="{{$builder->name}}" value="{{{$builder->value}}}" {{$builder->getOnChange()}}/>
</div>
@endUI

{{-- ///////////////////////////////////// Form Basic UI: tm tag ///////////////////////////////////// --}}
@beginUI('form.tm_tag')
<div class="form-group {{$builder->class}} {{$builder->getSize()}} {{($builder->getErrorClass())}}">
	<label for="{{$builder->name}}" class="{{$builder->getValidationLabel()}}">{{{$builder->title}}}{{$builder->getHelpText()}}</label>
	<div style="float: left; padding-right: 5px; position: relative; z-index: 1;" id="{{$builder->containerId ? $builder->containerId : 'halo-tm-tag-container'}}">
	</div>
	<input type="text" name="{{$builder->name}}" placeholder="{{$builder->placeholder}}"
		data-container-id="{{$builder->containerId ? $builder->containerId : 'halo-tm-tag-container'}}"  
		class="form-control halo-tm-tag" {{$builder->getData()}}/>
</div>
@endUI

{{-- ///////////////////////////////////// Form Basic UI: hash tag ///////////////////////////////////// --}}
@beginUI('form.hash_tag')
<div class="form-group {{$builder->class}} {{$builder->getSize()}} {{($builder->getErrorClass())}}">
	@if($builder->title)<label for="{{$builder->name}}" class="{{$builder->getValidationLabel()}}">{{{$builder->title}}}{{$builder->getHelpText()}}</label>@endif
	<div style="float: left; padding-right: 5px; position: relative; z-index: 1;" id="{{$builder->containerId ? $builder->containerId : 'halo-hash-tag-container'}}">
		<span class="tm-tag tm-tag-success tm-tag-title">
			<span>{{HALOUIBuilder::icon('tag')}} {{$builder->leftTitle ? $builder->leftTitle : 'Tags'}}</span>
		</span>
	</div>
	<input type="text" name="{{$builder->name ? $builder->name : 'tags'}}" placeholder="{{$builder->placeholder ? $builder->placeholder : 'Tags'}}" data-container-id="{{$builder->containerId ? $builder->containerId : 'halo-hash-tag-container'}}"  class="form-control halo-hash-tag" {{$builder->getData()}}/>
</div>
@endUI

{{-- ///////////////////////////////////// Form Basic UI: readonly hash tag ///////////////////////////////////// --}}
@beginUI('form.hash_tag_readonly')
@if ($builder->tags->count())
<div class="form-group {{$builder->class}} {{$builder->getSize()}} {{($builder->getErrorClass())}}">
	@if($builder->title)<label for="{{$builder->name}}" class="{{$builder->getValidationLabel()}}">{{{$builder->title}}}{{$builder->getHelpText()}}</label>@endif
	<div class="halo-hash-tag-container">
		<span class="tm-tag tm-tag-success tm-tag-title">
			<span>{{HALOUIBuilder::icon('tag')}} Tags</span>
		</span>
		@foreach($builder->tags as $tag)
		<span class="tm-tag tm-tag-success {{$builder->class}}">
			<span>{{$tag->getDisplayLink('', true, $builder->linkAttrs)}}</span>
		</span>
		@endforeach
	</div>
</div>
@endif
@endUI

{{-- ///////////////////////////////////// Location input UI ///////////////////////////////////// --}}
@beginUI('form.label_edit')
<?php 
$options = array();
$values = array();
foreach($builder->labelGroups as $labelGroup) {
    if (HALOConfig::get("{$builder->target->getContext()}.label.status", null) == $labelGroup->getCode()) {
        $grTitle = 'Statuses';
    } elseif (in_array($labelGroup->getCode(), HALOConfig::get("{$builder->target->getContext()}.label.badge", array()))) {
        $grTitle = 'Badges';
    }
    $options[] = HALOObject::getInstance(array(
        'title' => $labelGroup->name . (isset($grTitle) ? " ($grTitle)" : ''), 
        'multiple' => $labelGroup->group_type, 
        'children' => $labelGroup->getLabelOptions()
    ));
    $values = array_merge($values, $labelGroup->getLabelValue($builder->value,false));
}
?>
{{HALOUIBuilder::getInstance('','form.group_select',array('placeholder' => __halotext('-- Select labels --'), 'title'=> sprintf(__halotext('Pick %s for this %s'), 'lables', $builder->target->getContext()),'name'=>$builder->name,'value'=>$values,'options'=> $options, 'data' => array('live-search' => true)))->fetch()}}
@endUI

{{-- ///////////////////////////////////// Form Date UI ///////////////////////////////////// --}}
@beginUI('form.date')
<div class="form-group {{$builder->getSize()}} {{($builder->error?'error':'')}}">
    <label for="{{$builder->name}}" class="{{$builder->getValidationLabel()}}">{{{$builder->title}}}{{$builder->getHelpText()}}</label>
    <div class="input-group halo_field_date date form_date" data-date="{{Carbon::now()}}" data-date-format="dd-mm-yyyy">
        <input class="form-control" name="{{$builder->name}}" type="text" value="{{{$builder->value}}}" readonly
        {{$builder->getValidation()}} onchange={{$builder->onchange}}>
        <span class="input-group-addon"><span class="fa fa-times"></span></span>
        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
    </div>
    @if($builder->error)<span class="help-block">{{{$builder->error}}}</span>@endif
</div>
<script type="text/javascript">
__haloReady(function() {
	HALOModernizr.load({
		load    : "{{URL::to('/assets/js/bootstrap-datetimepicker.js')}}",
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

{{-- ///////////////////////////////////// Form Basic UI: media uploader ///////////////////////////////////// --}}
@beginUI('form.media')
<div class="form-group {{$builder->getSize()}} {{($builder->error?'error':'')}}">
    <label for="{{$builder->name}}" class="{{$builder->getValidationLabel()}}">{{{$builder->title}}}{{$builder->getHelpText()}}</label>
    <div class="halo-media-upload-wrapper">
        {{ HALOUIBuilder::getInstance('','photo.uploader',array('id'=>$builder->id,
        'name'=>$builder->name,
        'data'=>array('inputName' => $builder->name,
						'mediaValue' => $builder->value,
						'mediaType' => $builder->mediaType,
						'allowedExtensions' => $builder->extensions
						)
        ))
        ->fetch()}}
    </div>
    @if($builder->error)<span class="help-block">{{{$builder->error}}}</span>@endif
</div>
@endUI
