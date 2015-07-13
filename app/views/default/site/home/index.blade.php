@extends('site.layouts.default')

{{-- Web site Title --}}
@section('title')
{{{ __halotext('Homepage') }}}
@parent
@stop

{{-- OpenGraph Meta --}}
@section('ogp_meta')
<meta property="og:image" content="{{HALOPhotoHelper::getSiteBanner()}}"/>
<meta property="og:description" content="{{HALOUtilHelper::getSiteDescription()}}"/>
@stop

{{-- Content --}}
@section('content')

{{-- Banner content--}}
@if(empty($user->user_id) && (Input::get('usec','stream') == 'stream'))
@include('site/home/banner')
@endif
{{-- End Banner content --}}

<div class="halo-modules-content-top">
	<?php Event::fire('module.onLoadModule', 'content_top', 'default'); ?>
</div>
<div class="halo-focus-actions">
	<div class="halo-tab-overflow">
		<?php
		$uid = uniqid(); $infoCount = 0;
		$active = Input::get('usec','stream');
		$actionsUI = HALOUIBuilder::getInstance('','tabcontainer',array('class' => 'hidden'));

		//prepare params for pagination ajax loading
		Input::merge(array('com'=>'home','func'=>'DisplaySection'));
		//load usec zone content
		Event::fire('system.onDisplaySiteInfo',array($active,array()));
		$siteInfo = HALOConfigModel::getSiteShortInfo();
		foreach($siteInfo as $info){
			$infoCount++;
			$actionsUI->addUI('tab@array', HALOUIBuilder::getInstance('','',array('url'=>$info->url,'tooltip'=>$info->title,'name'=>$info->value
																				,'id'=>$infoCount.'_'.$uid,'content'=>$info->content
																				,'onDisplayContent'=>$info->onDisplayContent
																				,'active'=>($info->name==$active)?'active':'')));
		}
		?>

		{{$actionsUI->fetch()}}
	</div>
</div>

@stop

