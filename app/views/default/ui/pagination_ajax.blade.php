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
 
    $presenter = new HALOPaginationPresenterAjax($paginator);
	$uid = uniqid();
?>
<div class="row" data-halo-pagination>
<div class="clearfix"></div>
<form class="halo-pagniation-frm halo-pagination-ajax" name="pagination_form_{{$uid}}" id="pagination_form_{{$uid}}">
<?php $params = Input::except('csrf_token','pg');?>
{{HALOUIBuilder::getInstance('','form.hidden_input',array('name'=>'','value'=>$params,'prefix'=>''))->fetch()}}
<?php if ($paginator->getLastPage() > 1): ?>
	<div class="text-center">
	<ul class="pagination pagination-sm">
		<?php echo $presenter->render(); ?>
	</ul>
	</div>
<?php endif; ?>
</form>
</div>
