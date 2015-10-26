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

{{-- ///////////////////////////////////// label UI ///////////////////////////////////// --}}
@beginUI('label.label')
<?php $label = HALOLabelAPI::getLabelByGroup($builder->target,$builder->group_code);?>
	@if($label)
		@if($builder->mode == 'multiple' && is_array($label))
			{{$builder->copyAttributes('','label.multiple')
								->setAttrs(array('labels'=>$label))->fetch()}}
		@else
			{{$builder->copyAttributes('','label.single')
								->setAttrs(array('label'=>$label))->fetch()}}		
		@endif
	@endif
@endUI

{{-- ///////////////////////////////////// badge UI ///////////////////////////////////// --}}
@beginUI('label.badge')
<?php
$labels = array();
if (!is_array($builder->group_code)) {
    $builder->group_code = array($builder->group_code);
}
foreach ($builder->group_code as $code) {
    $grLabels = HALOLabelAPI::getLabelByGroup($builder->target, $code);
    if (is_array($grLabels)) {
        $labels = array_merge($labels, $grLabels);
    } else {
        $labels = array_merge($labels, array($grLabels));
    }
}
?>
@if($labels)
    @if($builder->mode == 'multiple' && is_array($labels))
        {{$builder->copyAttributes('','label.multiple')
                            ->setAttrs(array('labels'=>$labels, 'class' => $builder->class))->fetch()}}
    @else
        {{$builder->copyAttributes('','label.single')
                            ->setAttrs(array('label'=>$labels, 'class' =>  $builder->class))->fetch()}}       
    @endif
@endif
@endUI

{{-- ///////////////////////////////////// Single label UI ///////////////////////////////////// --}}
@beginUI('label.single')
<?php $label = is_array($builder->label)?array_shift($builder->label):$builder->label; ?>
	@if($label)
	<div class="{{$builder->prefix}} {{$builder->prefix}}-{{$label->getStyleClass()}} {{$builder->class}} ribbon ribbon-{{$builder->position}}" data-halozone="{{$label->getGroupZone()}}">
			<span class="ribbon-content">{{{$label->getDisplayName()}}}</span>
	</div>
	@endif
@endUI

{{-- ///////////////////////////////////// Multiple label UI ///////////////////////////////////// --}}
@beginUI('label.multiple')
<?php $labels = $builder->labels;?>
	@if($labels)
	<div {{ $builder->getClass($builder->prefix) }}>
		<ul>
			@foreach($labels as $label)
				<li class="{{$builder->prefix}} {{$builder->prefix}}-{{$label->getStyleClass()}}">{{{$label->getDisplayName()}}}</li>
			@endforeach
		</ul>
	</div>	
	@endif
@endUI

{{-- ///////////////////////////////////// Single Pending soft label UI ///////////////////////////////////// --}}
@beginUI('label.pending_soft')
<div class="{{$builder->prefix}} {{$builder->prefix}}-{{$builder->style_class}} {{$builder->class}} ribbon ribbon-{{$builder->position}}">
	<span class="ribbon-content">{{$builder->name}}</span>
</div>
@endUI

