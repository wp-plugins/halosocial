<div class="halo-resp-container halo-banner">
	<div class="halo-resp-block halo-ratio-8-3" style="position:relative;">	
		<div class="halo-banner-images" style="background-image:url({{HALOAssetHelper::to('assets/images/front-page-banner.jpg')}}); ">
		</div>
		<div class="halo-banner-title">
			<div class="row">
				<div class="col-md-8">
					<h3>{{__halotext('Welcome to our community')}}</h3>						
				</div>
				@if (UserModel::canRegister())
				<div class="col-md-4">						
						<a class="halo-btn halo-btn-lg halo-btn-success halo-btn-join" href="{{UserModel::getRegisterLink()}}">{{__halotext("Join now to get connected!")}}</a>										
				</div>
				@endif	
			</div>
		</div>
	</div>
	<?php echo HALOUIBuilder::getInstance('ajaxlogin','ajaxlogin',array('name'=>'loginForm','msg'=>''))->fetch();?>
</div>

