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
 <?php
 
    $presenter = new HALOPaginationPresenter($paginator);
?>
<div class="row" data-halo-pagination>

	<div class="halo-pagination-limit hidden-xs pagination pagination-sm form-group halo-pull-right">
		<div class="input-group">
			<span class="input-group-addon">{{__halotext('Display')}}</span>
			<select name="limit" class="form-control" onchange="halo.pagination.changeLimit(this.value)">
			<?php $options = array();
					$option = new stdClass();$option->value = 5; $option->title = '5'; $options[] = $option;
					$option = new stdClass();$option->value = 10; $option->title = '10'; $options[] = $option;
					$option = new stdClass();$option->value = 20; $option->title = '20'; $options[] = $option;
					$option = new stdClass();$option->value = 50; $option->title = '50'; $options[] = $option;
					$option = new stdClass();$option->value = 100; $option->title = '100'; $options[] = $option;
			?>
			@foreach($options as $opt)
			<option value="{{$opt->value}}" @if($opt->value == $paginator->getPerPage()) selected="true" @endif>{{$opt->title}}</option>
			@endforeach
			</select>
		</div>	
	</div>

<?php if ($paginator->getLastPage() > 1): ?>
	<div class="text-center">
	<ul class="pagination pagination-sm">
		<?php echo $presenter->render(); ?>
	</ul>
	</div>
<?php endif; ?>
</div>