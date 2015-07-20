<script >
	var halo_jax_targetUrl = "{{ HALO_ROOT_URL . 'social/ajax' }}";
	var halo_my_id = "{{ UserModel::getCurrentUserId() }}";
	var halo_socket_address = "{{ HALOConfig::get('pushserver.address') }}";
</script>
<meta name="csrf_token" content="{{ csrf_token() }}">
<!-- To make sticky footer need to wrap in a div -->
<div id="halo-wrap">
	{{-- Navbar --}}
	<?php 
	$top_bar = HALOUIBuilder::getInstance('top_bar','navbar.top_bar',array('icon'=>'home'))
						->addUI('title', HALOUIBuilder::getInstance('','',array('url'=>'/','title'=>Mage::app()->getStore()->getCode()))) 
						->addUI('left_nav@array', HALOUIBuilder::getInstance('','',array('url'=>'?view=home','title'=>__halotext('Home')))) 
						->addUI('left_nav@array', HALOUIBuilder::getInstance('','',array('url'=>'?view=user&task=list','title'=>__halotext('Browse Members'))));
	if (HALOAuth::hasRole('registered')){
		if (HALOAuth::hasRole('admin'))
			$top_bar->addUI('left_nav@array', HALOUIBuilder::getInstance('','',array('url'=>'?view=user&task=profile&uid='.HALOUserModel::getUser()->user_id,'title'=>'<i class="fa fa-user"></i>' . HALOUserModel::getUser()->getDisplayName() )));
		//Notification counter	
		$notifCount = HALONotificationAPI::getNotificationCount();
		$top_bar->addUI('left_nav@array', HALOUIBuilder::getInstance('','',array('url'=>'javascript',
																				'title'=>HALOUIBuilder::icon('bell').HALOUIBuilder::getInstance('','notification.counter',array('counter'=>$notifCount,'zone'=>'notification-counter'))->fetch(),
																				'class'=>'halo-notification-toggle','onClick'=>"halo.notification.list(this,'".HALOUserModel::getUser()->user_id."')")));
		
		$top_bar->addUI('left_nav@array', HALOUIBuilder::getInstance('','',array('url'=>'?view=user&task=logout','title'=>HALOUIBuilder::icon('power-off') . ' ' . __halotext('Logout'))));
	} else {
		$top_bar->addUI('left_nav@array', HALOUIBuilder::getInstance('','',array('url'=>'javascript',
																				'title'=>HALOUIBuilder::icon('key') . ' ' . __halotext('Login'),
																				'onClick'=>"halo.user.showLogin()")))
				->addUI('left_nav@array', HALOUIBuilder::getInstance('','',array('url'=>'?view=user&task=register','title'=>__halotext('Sign Up'))));
	}
	?>
	<div class="navbar halo-navbar navbar-inverse" role="navigation">
	{{HALOUIBuilder::getInstance('top_bar','navbar.top_bar')->fetch()}}
	</div>
	{{-- ./ navbar --}}

	<!-- Container -->
	<div class="container">
		<div class="row row-offcanvas row-offcanvas-right">
			<div class="col-xs-12 col-sm-8">
			<!-- Notifications -->
			@include('notifications')
			<!-- ./ notifications -->

			<!-- Content -->
			@yield('content')
			<!-- ./ content -->
			</div>
		</div>
	</div>
	<!-- ./ container -->

	<!-- the following div is needed to make a sticky footer -->
	<div id="push"></div>
</div>
<!-- ./wrap -->


{{-- trigger event on content rendered --}}
<?php 
	Event::fire('system.onContentRendered');
?>
<div id="footer">
  <div class="container">
  </div>
</div>
