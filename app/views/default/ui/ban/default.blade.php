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

{{-- ///////////////////////////////////// Ban form UI ///////////////////////////////////// --}}
@beginUI('ban.actions')
	<div class="halo-ban-form">
		<table>
			<thead>
				<tr>
					<th width="50%">{{__halotext('Description')}}</th>
					<th width="10%">{{__halotext('Restriction')}}</th>
					<th class="text-center" width="20%">{{__halotext('Expired In')}}</th>
					<th width="20%">{{__halotext('Duration(h)')}}</th>
				</tr>
			</thead>
			<tbody>
			@foreach($builder->settings as $key => $setting)
			<tr class="halo-ban-setting">
				<td> {{{$setting['description']}}} {{$builder->getHelpText(HALOBanModel::getHelpText($setting))}}</td>
				<td> 
					<input class="halo-ban-checkbox" type="checkbox" name="ban_actions[]" onclick="halo.user.toggleBan(this)" value="{{ $key }}" @if(!empty($setting['value'])) checked="checked" @endif/>
					<span class="lbl"></span>
				</td>
				<td class="text-center">
				@if(!empty($setting['value']))
					<span class="halo-countdown" data-countdown="{{$setting['value']->expired_at->diffInSeconds(Carbon::now())}}"><span>
				@endif
				</td>
				<td>
					<input class="halo-ban-duration @if(empty($setting['value'])) hidden @endif" type="number" name="ban_duration[]" value="" min="1"/>
				</td>
			</tr>
			@endforeach
			</tbody>
		</table>
	</div>
@endUI