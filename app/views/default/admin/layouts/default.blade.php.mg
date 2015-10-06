<script >
	var halo_jax_targetUrl = "{{ HALO_ROOT_URL . 'social/ajax' }}";
 
</script>
<meta name="csrf_token" content="{{ csrf_token() }}">
<!-- Container -->
<div id="halo-wrap" class="container">
	{{-- Navbar --}}
	<div class="navbar halo-navbar navbar-inverse" role="navigation">
	{{ HALOUIBuilder::getInstance('top_bar','navbar.top_bar')
					->addUI('title', HALOUIBuilder::getInstance('','',array('url'=>'?app=admin','title'=>'HALO'))) 
					->addUI('left_nav@array', HALOUIBuilder::getInstance('','',array('url'=>'?app=admin','title'=>__halotext('Dashboard')))) 
					->addUI('left_nav@array', HALOUIBuilder::getInstance('','',array('url'=>'?app=admin&view=config','title'=>__halotext('Configuration')))) 
					->addUI('left_nav@array', HALOUIBuilder::getInstance('','',array('url'=>'?app=admin&view=profiles','title'=>__halotext('Profiles')))
													->addUI('dropdown@array', HALOUIBuilder::getInstance('','',array('url'=>'?app=admin&view=profiles','title'=>__halotext('Profiles')))) 
													->addUI('dropdown@array', HALOUIBuilder::getInstance('','',array('url'=>'?app=admin&view=fields','title'=>__halotext('Profile Fields')))) 
					) 
					->addUI('left_nav@array', HALOUIBuilder::getInstance('','',array('url'=>'?app=admin&view=categories','title'=>__halotext('Categories')))) 
					->addUI('left_nav@array', HALOUIBuilder::getInstance('','',array('url'=>'?app=admin&view=reports','title'=>__halotext('Reports')))) 
					->addUI('left_nav@array', HALOUIBuilder::getInstance('','',array('url'=>'?app=admin&view=filters','title'=>__halotext('Filters')))) 
					->addUI('left_nav@array', HALOUIBuilder::getInstance('','',array('url'=>'?app=admin&view=users','title'=>__halotext('Users')))
													->addUI('dropdown@array', HALOUIBuilder::getInstance('','',array('url'=>'?app=admin&view=users','title'=>__halotext('Users')))) 
													->addUI('dropdown@array', HALOUIBuilder::getInstance('','',array('url'=>'?app=admin&view=users&task=getOnlineIndex','title'=>__halotext('User Log')))) 
													->addUI('dropdown@array', HALOUIBuilder::getInstance('','',array('url'=>'?app=admin&view=roles','title'=>__halotext('Roles')))) 
													->addUI('dropdown@array', HALOUIBuilder::getInstance('','',array('url'=>'?app=admin&view=roles&task=getPermissions','title'=>__halotext('Permissions')))) 
					) 
					->addUI('left_nav@array', HALOUIBuilder::getInstance('','',array('url'=>'?app=admin&view=tools','title'=>__halotext('Tools')))
													->addUI('dropdown@array', HALOUIBuilder::getInstance('','',array('url'=>'?app=admin&view=plugins','title'=>__halotext('Plugins')))) 
													->addUI('dropdown@array', HALOUIBuilder::getInstance('','',array('url'=>'?app=admin&view=mailqueue','title'=>__halotext('Mail Queue'))))) 
					->fetch()
	}}
	{{-- ./ navbar --}}
	</div>
	{{-- Container --}}
	<div class="container">

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
