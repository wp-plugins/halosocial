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

{{-- ///////////////////////////////////// User brief info UI ///////////////////////////////////// --}}
@beginUI('brief.user')
<?php $user = $builder->user ?>
<div class="halo-brief-wrapper" {{$builder->getZone()}}>
	<div class="halo-brief-content-wrapper">
		<div class="halo-brief-cover">
			<img id='{{$user->id}}' class="focusbox-image cover-image" src="{{$user->getCover(HALO_PHOTO_SMALLCOVER_SIZE)}}" alt="cover photo"/>
		</div>
		<div class="halo-brief-content">
			<div class="media">
				<a class="halo-pull-left thumbnail halo-user-{{$user->id}}" href="{{$user->getUrl()}}">
					<img class="img-responsive" src="{{$user->getAvatar(120)}}" alt="{{{ $user->getDisplayName() }}}" />
				</a>
				<div class="media-body">
					<h4 class="halo-ellipsis">{{ $user->getDisplayLink('',false) }}</h4>
					<div class="halo-user-brief-info">
					{{HALOUIBuilder::getInstance('','user.info',array('user'=>$user,'class'=>'haloc-list list-unstyled'))->fetch()}}
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="halo-brief-actions">
		<div class="halo-brief-btn-group clearfix">
			{{HALOUIBuilder::getInstance('','user.responseActions',array('user'=>$user))->fetch()}}
		</div>
	</div>
</div>
@endUI