<!-- To make sticky footer need to wrap in a div -->
<div id="halo-wrap">
	{{-- Navbar --}}
	@if(HALOConfig::get('global.showNavigation',1))
	<div class="navbar halo-navbar navbar-inverse" role="navigation">
	{{HALOUIBuilder::getInstance('top_bar','navbar.navigation')->fetch()}}
	</div>
	@endif
	{{-- ./ navbar --}}

	<!-- Container -->
	<div class="container-fluid">
		<div class="row row-offcanvas row-offcanvas-right">
			<div class="col-xs-12">
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
  <div class="container-fluid">
  </div>
</div>
