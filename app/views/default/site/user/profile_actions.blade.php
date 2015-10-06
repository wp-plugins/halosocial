<?php 
$badgesHtml = HALOUIBuilder::getInstance('','label.badge',array('target'=>$user,'position'=>'right','group_code'=>HALOConfig::get('user.label.badge'),'prefix'=>'halo-label-badge','mode'=>'multiple'))->fetch();
?>
<div class="halo-focus-actions">
	<div class="halo-label-badge-wrapper hidden-sm hidden-md hidden-lg">{{ $badgesHtml }}</div>
	<div class="halo-tab-overflow">
		<?php 
		$uid = uniqid(); $infoCount = 0;
		$active = Input::get('usec','stream');
		$actionsUI = HALOUIBuilder::getInstance('','tabcontainer',array());
		
		//prepare params for pagination ajax loading
		Input::merge(array('com'=>'user','func'=>'DisplaySection','userid'=>$user->id));
		//load usec zone content
		Event::fire('user.onDisplayUserInfo',array($active,array()));
		
		//load stream content
        //get filters
        $streamFilters = HALOFilter::getFilterByName('activity.profile.*');
        //configure filters value
        foreach ($streamFilters as $filter) {
            if ($filter->name == 'activity.profile.user') {
                //default value for profile owner
                $filter->value = $user->user_id;
            }
            //for other filter, get from http request or session data
        }

        //stream content
        $acts = HALOActivityModel::getActivities(array('filters' => $streamFilters));
        $showLoadMore = true;
		
		//profile stream
		$streamHtml = HALOUIBuilder::getInstance('','content',array())
						->addUI('sharebox',HALOUIBuilder::getInstance('','html',array('html'=>HALOStatus::render('profile',$user->id))))
						->addUI('header',HALOUIBuilder::getInstance('','stream.header',array('streamFilters'=>$streamFilters)))
						->addUI('body',HALOUIBuilder::getInstance('','stream.content',array('acts' => $acts,'zone'=>'stream_content','showLoadMore' => $showLoadMore)))
						->fetch();
		$actionsUI->addUI('tab@array',HALOUIBuilder::getInstance('','',array('url'=>$user->getUrl(array('usec'=>'stream')), 'name'=>__halotext('Stream'),'id'=>'stream_'.$uid
																			,'content'=>$streamHtml
																			,'onDisplayContent'=>"halo.user.listStream()"
																			,'active'=>(Input::get('usec','stream')=='stream')?'active':'')));

		foreach($user->getShortInfo() as $info){
			$infoCount++;
			$actionsUI->addUI('tab@array', HALOUIBuilder::getInstance('','',array('url'=>$info->url,'tooltip'=>$info->title,'name'=>$info->value
																				,'id'=>$infoCount.'_'.$uid,'content'=>$info->content
																				,'onDisplayContent'=>$info->onDisplayContent
																				,'active'=>($info->name==$active)?'active':'')));
		}																							
		
		$aboutAction = HALOUIBuilder::getInstance('','usection_action',array());
		if(HALOAuth::can('user.edit',$user)){
			$aboutAction->addUI('create',HALOUIBuilder::getInstance('','content',array('title'=>__halotext('Edit'),'onClick'=>"halo.util.redirect('".URL::to('?view=user&task=edit&uid='.$user->id)."')")));
		}
		$actionsUI->addUI('tab@array',HALOUIBuilder::getInstance('','',array('url'=>URL::to('?view=user&task=profile&uid='.$user->id.'&usec=aboutme'),'name'=>__halotext('About'),'id'=>'aboutme_'.$uid
																			,'content'=>HALOUIBuilder::getInstance('','usection',array('title'=>__halotext('About')
																																	,'actions'=>$aboutAction->fetch()
																																	,'zone'=>'halo-aboutme-wrapper'
																																	,'content'=>HALOUIBuilder::getInstance('','user.about_me',array('user'=>$user))->fetch()))->fetch()
																			,'onDisplayContent'=>'halo.user.listAbout()'
																			,'active'=>($active=='aboutme')?'active':'')));
		?>
		
		{{$actionsUI->fetch()}}
	</div>
</div>
