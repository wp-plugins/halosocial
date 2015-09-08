<div class="halo-resp-container halo-banner">
	<div class="halo-resp-block halo-ratio-2-1">
	
	</div>
	<div class="halo-resp-content halo-banner-image">
		<div class="halo-banner-images">
			<img src="{{HALOAssetHelper::to('assets/images/front-page-banner.jpg')}}" >
		</div>
		<div class="halo-row-md-9 halo-row-sm-8 halo-row-xs-6 halo-row-xxs-3">
		</div>
		<div class="halo-resp-container halo-row-md-3 halo-row-sm-4 halo-row-xs-6 halo-row-xxs-9 halo-banner-title">
			<div class="halo-resp-block">
			</div>
			<div class="halo-resp-content">
                <p>{{__halotext('Welcome to our community')}}</p>
				@if (UserModel::canRegister())
				<h1>
					<a class="halo-btn halo-btn-lg halo-btn-primary halo-btn-join" href="{{UserModel::getRegisterLink()}}">{{__halotext("Join now to get connected!")}}</a>
				</h1>
				@endif
			</div>
		</div>
		
	</div>
</div>