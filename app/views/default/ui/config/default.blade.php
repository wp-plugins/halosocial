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

{{-- ///////////////////////////////////// Config UI: Group ///////////////////////////////////// --}}
@beginUI('config.group')
	@foreach($builder->getChildren() as $child)
		{{$child->fetch()}}
	@endforeach
@endUI

{{-- ///////////////////////////////////// Config UI: Section ///////////////////////////////////// --}}
@beginUI('config.section')
	<div class="col-md-6">
	<div class="panel panel-default">
		<div class="panel-heading">{{{$builder->title}}}{{$builder->getHelpText()}}</div>
		<div class="panel-body">
			@foreach($builder->getChildren() as $child)
				{{$child->fetch()}}
			@endforeach
		</div>
	</div>
	</div>
@endUI