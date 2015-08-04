{{HALOAssetHelper::printInlineScript()}}
<script >
	var halo_my_id = "{{ UserModel::getCurrentUserId() }}";
	var halo_feature_message = 0;
	var halo_socket_address = "{{ HALOConfig::get('pushserver.address') }}";
</script>
<meta name="csrf_token" content="<?= csrf_token() ?>">
<!-- Container -->
<div id="halo-wrap" class="container-fluid halo-admin-wrapper">
	{{-- Navbar --}}
	<div class="navbar halo-navbar navbar-inverse" role="navigation">
	<?php
		$nav = HALOUIBuilder::getInstance('top_bar','navbar.top_bar')
					->addUI('title', HALOUIBuilder::getInstance('','',array('url'=>'?app=admin', 'logo' => HALOAssetHelper::to('assets/images/logo.png')))) 
					// ->addUI('left_nav@array', HALOUIBuilder::getInstance('','',array('url'=>'?app=admin','title'=>__halotext('Dashboard')))) 
					->addUI('left_nav@array', HALOUIBuilder::getInstance('','',array('url'=>'?app=admin&view=config','title'=>__halotext('Configuration'))));
		$nav->addUI('left_nav@array', HALOUIBuilder::getInstance('','',array('url'=>'?app=admin&view=users','title'=>__halotext('Users')))
													->addUI('dropdown@array', HALOUIBuilder::getInstance('','',array('url'=>'?app=admin&view=users','title'=>__halotext('Users')))) 
													->addUI('dropdown@array', HALOUIBuilder::getInstance('','',array('url'=>'?app=admin&view=users&task=getOnlineIndex','title'=>__halotext('Online Users')))) 
													->addUI('dropdown@array', HALOUIBuilder::getInstance('','',array('url'=>'?app=admin&view=roles','title'=>__halotext('Roles')))) 
													->addUI('dropdown@array', HALOUIBuilder::getInstance('','',array('url'=>'?app=admin&view=roles&task=getPermissions','title'=>__halotext('Permissions')))) 
					);
		$nav->addUI('left_nav@array', HALOUIBuilder::getInstance('','',array('url'=>'?app=admin&view=profiles','title'=>__halotext('Profiles')))
													->addUI('dropdown@array', HALOUIBuilder::getInstance('','',array('url'=>'?app=admin&view=profiles','title'=>__halotext('Profiles')))) 
													->addUI('dropdown@array', HALOUIBuilder::getInstance('','',array('url'=>'?app=admin&view=fields','title'=>__halotext('Profile Fields')))) 
					)
			->addUI('left_nav@array', HALOUIBuilder::getInstance('admin_nav_category','',array('url'=>'#', 'class' => 'halo-hide-last-child', 'title'=>__halotext('Categories')))
					);
		if(HALOConfig::isDev() && HALOAuth::can('feature.filter')){
			$nav->addUI('left_nav@array', HALOUIBuilder::getInstance('','',array('url'=>'?app=admin&view=filters','title'=>__halotext('Filters'))));
		}
		if(HALOAuth::can('feature.plugin')){
			$nav->addUI('left_nav@array', HALOUIBuilder::getInstance('','',array('url'=>'?app=admin&view=plugins','title'=>__halotext('Plugins'))));
		}
		if(HALOAuth::can('feature.label')){
			$nav->addUI('left_nav@array', HALOUIBuilder::getInstance('','',array('url'=>'?app=admin&view=labels','title'=>__halotext('Labels'))));
		}
		$nav->addUI('left_nav@array', HALOUIBuilder::getInstance('','',array('url'=>'?app=admin&view=tools','title'=>__halotext('Tools')))
										->addUI('dropdown@array', HALOUIBuilder::getInstance('','',array('url'=>'?app=admin&view=reports','title'=>__halotext('Reports')))) 
										->addUI('dropdown@array', HALOUIBuilder::getInstance('','',array('url'=>'?app=admin&view=mailqueue','title'=>__halotext('Mail Queue'))))
										->addUI('dropdown@array', HALOUIBuilder::getInstance('','',array('url'=>'?app=admin&view=tools&task=upgrade', 'title'=>__halotext('Upgrade'))))
		);
		
		if(HALO_PLUGIN_PRODUCT_TYPE != 'STARTER'){
			$nav->addUI('left_nav@array', HALOUIBuilder::getInstance('','',array('url'=>'?app=admin&view=tools&task=license', 'title'=>__halotext('License'))));
		}
		
        $nav->addUI('right_nav@array', HALOUIBuilder::getInstance('', '', array('class' => 'haloc-backend-version', 'url' => 'javascript', 'title' => 'v' . HALOConfig::get('global.version'))));
		//trigger event on loading admin navbar
		Event::fire('system.onLoadingAdminNav', array());		
	?>
	{{$nav->fetch()}}
	{{-- ./ navbar --}}
	</div>
	{{-- Container --}}
	@if(HALO_PLUGIN_PRODUCT_TYPE === 'STARTER' && get_option( 'halo_social_notice_dismiss' ))
	<div class="halo-notice-section text-center">
		<div><p>{{__halotext('Do you like HaloSocial Starter? Upgrade to Professional or Agency to enjoy features such as: groups, events, chat, classifieds, roles, labels, and so much more.')}}</p></div>
		<div><a class="halo-btn halo-btn-success" target="_blank" href="http://tiny.cc/halosocial-pricing">{{__halotext('Compare all versions')}}</a></div>
	</div>
	@endif
	<div class="container-fluid">

		<!-- Notifications -->
		@include('notifications')
		<!-- ./ notifications -->

		{{-- Toolbar --}}
		<div class="right halo-toolbar-wrapper">
		{{ HALOToolbar::render() }} 
		</div>
		{{-- ./ toolbar --}}
		
		<!-- Content -->
		@yield('content')
		<!-- ./ content -->
	</div>
	<!-- Footer -->
	<footer class="clearfix">
		@yield('footer')
	</footer>
	<!-- ./ Footer -->

</div>
<!-- ./ container -->
<!-- ./ container -->
