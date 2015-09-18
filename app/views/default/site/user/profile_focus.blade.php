<?php 
$badgesHtml = HALOUIBuilder::getInstance('','label.badge',array('target'=>$user,'position'=>'right','group_code'=>HALOConfig::get('user.label.badge'),'prefix'=>'halo-label-badge','mode'=>'multiple'))->fetch();
?>
<div class="halo-focus-container">
	<div class="halo-resp-container halo-focus-body">
	{{HALOUIBuilder::getInstance('','label.label',array('target'=>$user,'position'=>'right'
                                                                ,'group_code'=>HALOConfig::get('user.label.status')
                                                                ,'prefix'=>'halo-label-status','mode'=>'single'))->fetch()}}
		<div class="halo-resp-block halo-ratio-8-3"></div>
		<div class="halo-resp-background">
			<img id='{{$user->id}}' class="focusbox-image cover-image" src="{{$user->getCover(1900)}}" alt="cover photo">
			<div class="profile-cover"></div>
		</div>
		<div class="halo-resp-content">
			<div class="halo-row-md-6 halo-row-sm-7 halo-row-xs-6 halo-row-xxs-3">
			</div>
			<div class="halo-resp-container halo-row-md-6 halo-row-sm-5 halo-row-xs-6 halo-row-xxs-9">
				<div class="halo-resp-block">
				</div>
				<div class="halo-resp-content halo-focus-desc">
					<div class="row">
						<div class="col-sm-3 col-xs-4">
							<div class="halo-focus-thumb-wrapper">
								<img class="halo-focus-thumb" src="{{$user->getAvatar(144)}}" alt="{{{ $user->getDisplayName() }}}" />
							</div>
						</div>
						<div class="col-sm-9 col-xs-8 halo-focus-desc-body">
							<h3 class="halo-ellipsis @if($badgesHtml)halo-top-bedge@endif">{{ $user->getDisplayLink('halo-focus-header-title',false) }}</h3>
							<div class="halo-label-badge-wrapper hidden-xs">{{ $badgesHtml }}</div>
							<div class="halo-response-actions">
								{{HALOUIBuilder::getInstance('','user.responseActions',array('user'=>$user))->fetch()}}
							</div>
						</div>
					</div>
				</div>
			</div>		
		</div>
	</div>
	<div class="halo-focus-footer">
		<div class="row">
			<div class="col-sm-3 col-xs-4">
			</div>
			<div class="col-sm-9 col-xs-8 halo-focus-counters">
				{{HALOUIBuilder::getInstance('','focus_menu_item',array('title'=>HALOUserpointAPI::getUserpointKarma($user->getUserPoint())))->fetch()}}
				{{HALOUIBuilder::getInstance('','focus_menu_item',array('icon'=>'eye','title'=> $user->getViewCount(true)))->fetch()}}
				{{HALOUIBuilder::getInstance('','focus_menu_item',array('title'=>$user->getLikeCount()))->fetch()}}
				{{HALOUIBuilder::getInstance('','focus_menu_item',array('class'=>'halo-pull-right halo-shareme-btn','title'=>HALOUIBuilder::getInstance('','share_me',array('target'=>$user))->fetch()))->fetch()}}
			</div>
		</div>
	</div>
</div>
