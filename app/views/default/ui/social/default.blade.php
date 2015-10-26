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

{{-- ///////////////////////////////////// Share Google UI///////////////////////////////////// --}}
@beginUI('social.share_google')
<a class="halo-btn-social" onclick="halo.share.google();return false;">
	{{ HALOUIBuilder::icon('google-plus-square fa-lg text-danger')}} <span id="count_{{$builder->name}}"></span>
	{{sprintf(__halotext('Share on %s'),'Google+')}}
</a>
@endUI

{{-- ///////////////////////////////////// Share Facebook UI///////////////////////////////////// --}}
@beginUI('social.share_facebook')
<a class="halo-btn-social" onclick="halo.share.facebook();return false;">
	{{ HALOUIBuilder::icon('facebook-square fa-lg text-primary')}} <span id="count_{{$builder->name}}"></span>
	{{sprintf(__halotext('Share on %s'),'Facebook')}}
</a>
@endUI

@beginUI('social.login_icon_google')
<a class="halo-btn-social" href="{{URL::to('?view=user&task=oauthLogin&uid=google', array('noSEO' => true)) . '&redirect_url=' . urlencode(HALOResponse::getData('redirect_url',''))}}">
	{{HALOUIBuilder::icon('google-plus-square fa-2x')}}
</a>
@endUI

{{-- ///////////////////////////////////// Share Facebook UI///////////////////////////////////// --}}
@beginUI('social.login_icon_facebook')
<a class="halo-btn-social" href="{{URL::to('?view=user&task=oauthLogin&uid=facebook', array('noSEO' => true)) . '&redirect_url=' . urlencode(HALOResponse::getData('redirect_url',''))}}">
	{{HALOUIBuilder::icon('facebook-square fa-2sx')}}
</a>
@endUI

{{-- ///////////////////////////////////// Share by Email UI///////////////////////////////////// --}}
@beginUI('social.share_email')
<a class="halo-btn-social" onclick="halo.share.email({{$builder->post->id}});return false;" href="javascript:void(0);">
	{{HALOUIBuilder::icon('envelope-o')}}
	{{sprintf(__halotext('Share on %s'),'Email')}}
</a>
@endUI
