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

{{-- ///////////////////////////////////// Status Box UI ///////////////////////////////////// --}}
@beginUI('sharebox.sharebox')
<?php $activeKey = key($builder->getChildren()); ?>
<?php $target = HALOModel::getCachedModel($builder->context, $builder->target_id);?>
<div class="status_form_wrapper row" data-context="{{$builder->context}}" data-target="{{$builder->target_id}}"
     data-halozone="halo-sharebox">
	<div class="col-lg-12">
		<div class="panel panel-default halo-share-box">
			<div class="panel-heading halo-status-heading">
				<h3 class="panel-title">
					<ul class="halo-nav nav-tabs">
						@foreach($builder->getChildren() as $key=>$child)
						<li class="@if($key == $activeKey) active @endif {{$child->class}}">
							<a class="halo-status halo-tab-panel" data-htoggle="tab" data-share-action="{{$child->action}}" href="#share-{{$key}}">{{$child->getIcon()}}{{{$child->title}}}</a></li>
						@endforeach
					</ul>
				</h3>
			</div>
			<div class="panel-body">
				<div class="halo-tab-content halo-status-attachment">
					@foreach($builder->getChildren() as $key=>$child)
					<?php $attchmentHtml = $child->getChild('attachment')->fetch(); ?>
					<div
						class="@if($key == $activeKey) active @endif tab-pane {{$child->class}} @if(empty($attchmentHtml)) hidden @endif"
						id="share-{{$key}}">
						<form name="status_form_{{$key}}" id="status_form_{{$key}}">
							{{$attchmentHtml}}
						</form>
					</div>
					@endforeach
				</div>
				<form class="halo-data-confirm-wrapper" name="status_form" id="status_form" role="form">
					<input type="hidden" name="share_action" value=""/>
					<input type="hidden" name="share_context" value="{{$builder->context}}"/>
					<input type="hidden" name="share_target_id" value="{{$builder->target_id}}"/>
					<input type="hidden" name="share_tagged_list" value=""/>
					<input type="hidden" name="share_message" id="share_message" value=""/>
					<textarea data-resetable data-raw-input="share_message" data-confirm class="form-control halo-status-box"
					          name="{{$builder->name}}" placeholder="{{{$builder->placeholder}}}"></textarea>
				</form>
				<form name="status_form_info" id="status_form_info" role="form">
					<div class="halo-sharebox-info">
						<div class="halo-sharebox-info-input" id="halo-sharebox-info-input">
							<div class="halo-share-info-label"></div>
							{{-- tag friend --}}
							<div id="halo-share-info-users"
							     class="halo-innerWrap halo-share-info halo-share-info-taggeduser hidden">
								<div class="input-group">
									<input type="text"
									       class="share_info_input halo-share-info-tag-input"
									       autocomplete="off" aria-autocomplete="list" aria-expanded="false"
									       aria-owns="typeahead_list_u_a_b" role="combobox" spellcheck="false"
									       aria-label="{{__halotext('Start typing your friends name')}}"
									       placeholder="{{__halotext('Start typing your friends name')}}" tabindex="-1">
								</div>
							</div>
							{{-- location --}}
							<div id="halo-share-info-location"
							     class="halo-innerWrap halo-share-info halo-share-info-location halo-location-control hidden">
								<div class="input-group">
									<input type="text" value="" name="share_location_name" data-halo-location-share
									       class="halo-field-location-name share_info_input form-control"
									       autocomplete="off" aria-autocomplete="list" aria-expanded="false"
									       aria-owns="typeahead_list_u_a_b" role="combobox" spellcheck="false"
									       aria-label="{{{__halotext('Where are you?')}}}"
									       placeholder="{{{__halotext('Where are you?')}}}" tabindex="-1"/>
						<span class="input-group-btn halo-location-choose-btn">
							<button class="halo-btn halo-btn-nobg" onclick="halo.location.showCheckin('halo-share-info-location')"
							        type="button">{{HALOUIBuilder::icon('search-plus')}}
							</button>
						</span>
									<input class="halo-field-location-lat" name="share_location_lat" type="hidden" value=""/>
									<input class="halo-field-location-lng" name="share_location_lng" type="hidden" value=""/>
								</div>
							</div>
						</div>
						<div class="halo-sharebox-info-display">
						</div>
					</div>
					<div class="row halo-status-function hidden">
						<div class="col-md-4 text-left">
							<a href="javascript:void(0)" data-htoggle="display" data-target="#halo-share-info-users"
							   data-siblings=".halo-share-info" title="{{__halotext('Tag Friends')}}"
							   class="halo-share-info-tag-icon halo-pull-left"> <span class="halo-1dst"> <i
										class="fa fa-plus-circle"></i></span></a>
							<a href="javascript:void(0)" data-htoggle="display" data-target="#halo-share-info-location"
							   data-siblings=".halo-share-info" title="{{__halotext('Location')}}"
							   class="halo-share-info-loc-icon halo-pull-left">
								<div class="halo-1dst"><i class="fa fa-map-marker"></i><span class="halo-loc-text"></span>
								</div>
							</a>
						</div>
						<div class="halo-status-post-option col-md-8 text-right">
							<button type="button" onclick="halo.status.submit();"
							        class="halo-btn halo-btn-primary halo-btn-sm halo-status-function-btn halo-btn-post">
								{{__halotext('Post')}}
							</button>
							{{HALOUIBuilder::getInstance('','form.privacy',array('name'=>'share_privacy','btnsize'=>'',
							'value'=>$builder->options['defaultPrivacy']))->fetch()}}
							@if(method_exists($target,'listDisplayActors'))
								{{$target->listDisplayActors('sharebox.switchDisplayActors')}}
							@endif
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

	<script>
	__haloReady(function() {
		//init the share_action value
		jQuery("input[name='share_action']").val(jQuery('.halo-status-heading .nav-tabs > li.active > a').data('share-action'));
		jQuery(".halo-tab-panel").bind("click", function () {
			jQuery("input[name='share_action']").val(jQuery(this).data('share-action'));
			jQuery('.halo-status-function').removeClass('hidden');
		});	
	});
	</script>
</div>
@endUI

@beginUI('sharebox.switchDisplayActors')
	@if(count($builder->options))
	<div class="halo-sharebox-actor-switcher">
		<select class="selectpicker">
		@foreach($builder->options as $option)
			@if(isset($option['actor']))
			<?php 
				$dataContent = '<img class="halo-pull-left halo-inline-img" src="' . $option['actor']->getAvatar(20) . '">';
				$isActive = ($builder->active->getContext() == $option['actor']->getContext()) && ($builder->active->id == $option['actor']->id);
				$displayName = $option['actor']->getDisplayName();
			?>
			<option @if($isActive) selected @endif value="{{$option['actor']->getContext()}}.{{$option['actor']->id}}" 
				data-content="{{str_replace('"', "'", $dataContent . $displayName )}}" title="{{sprintf(__halotext('Posting as %s'), $displayName)}}"></option>
			@endif
		@endforeach
		</select>
		<input type="hidden" name="share_display_context" value="{{$builder->active->getContext()}}"/>
		<input type="hidden" name="share_display_id" value="{{$builder->active->id}}"/>
	</div>
	<script>
		__haloReady(function() {
			//init the actor switcher
			jQuery('.halo-sharebox-actor-switcher .selectpicker').selectpicker();
			jQuery(document).on('change', '.halo-sharebox-actor-switcher .selectpicker', function() {
				var actorParts = jQuery(this).val().split('.');
				if(actorParts.length == 2) {
					var displayContext = halo.util.closest(jQuery(this), '[name="share_display_context"]');
					if(displayContext && displayContext.length) {
						displayContext.val(actorParts[0]);
					}
					var displayId = halo.util.closest(jQuery(this), '[name="share_display_id"]');
					if(displayId && displayId.length) {
						displayId.val(actorParts[1]);
					}
				}
			});
		});
	</script>
	@endif
@endUI