@extends('admin.layouts.default')
{{-- Web site Title --}}
@section('title')
	{{{ __halotext('Dashboard') }}} :: @parent
@stop

{{-- Content --}}
@section('content')
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script src="https://apis.google.com/js/client.js"></script>
<div class="halo-dashboar-wrapper">
<div class="halo-statistic-items row">
	<div class="col-md-12">
	<div id="halo_statistic_counters" class="panel panel-default">
		<table class="table table-bordered">
			<tr class="text-center">
				<td>
					<div class="text-muted">{{HALOUIBuilder::icon('user')}} {{__halotext('Users')}}</div>					
					<div class="halo-dashboard-counter">{{$totalUsers = HALOUserModel::getTotalUsersCounter()}}</div>(<span class="" title="{{__halotext('Online')}}">{{HALOUIBuilder::icon('user text-online')}}{{$onlineUsers = HALOOnlineuserModel::getOnlineUsersCounter()}}</span> / <span class="text-muted" title="{{__halotext('Offline')}}">{{HALOUIBuilder::icon('user')}}{{$totalUsers - $onlineUsers}}</span>)
				</td>
				<td>
					<div class="text-muted">{{HALOUIBuilder::icon('camera')}} {{__halotext('Photos')}}</div>
					<div class="halo-dashboard-counter">{{$totalPhotos = HALOPhotoModel::getTotalPhotosCounter()}}</div>(<span class="" title="{{__halotext('Temporary Photos')}}">{{$tmpPhotos = HALOPhotoModel::getTotalPhotosCounter(HALO_MEDIA_STAT_TEMP)}}</span> / <span class="" title="{{__halotext('Storage Photos')}}">{{$totalPhotos - $tmpPhotos}}</span>)
				</td>
				<td>
					<div class="text-muted">{{HALOUIBuilder::icon('video-camera')}} {{__halotext('Videos')}}</div>
					<div class="halo-dashboard-counter">{{$totalFiles = HALOVideoModel::getTotalVideosCounter()}}</div>
				</td>
				<td>
					<div class="text-muted">{{HALOUIBuilder::icon('file-text')}} {{__halotext('Files')}}</div>
					<div class="halo-dashboard-counter">{{$totalFiles = HALOFileModel::getTotalFilesCounter()}}</div>(<span class="" title="{{__halotext('Temporary Files')}}">{{$tmpFiles = HALOFileModel::getTotalFilesCounter(HALO_MEDIA_STAT_TEMP)}}</span> / <span class="" title="{{__halotext('Storage Files')}}">{{$totalFiles - $tmpFiles}}</span>)
				</td>
				{{HALOUIBuilder::getInstance('','dashboard_statistic',array('icon'=>'bars','title'=>__halotext('Activities'),'counter'=>HALOActivityModel::getTotalActivitiesCounter()))->fetch()}}
				<?php Event::fire('dashboard.showStatistics') ?>
			</tr>
		</table>
	</div>
	</div>
	<div class="col-md-6">		
		<div class="halo_statistic_dimension_charts panel panel-default ">
			<div class="authorize-wrapper" style="display: none">
				<div class="alert alert-danger">{{__halotext('You need to authorize with Google Anaylytics to view the charts')}}
				<button class="halo-btn authorize-button">{{__halotext('Click here to authorize')}}</button><br/>
				</div>
			</div>
			<div class="halo-chart-form-wrapper">
			<form class="halo_dimension_charts_form">
				{{HALOUIBuilder::getInstance('','form.select',array('name'=>'chart_dimension',
																'title'=>__halotext('Select Chart'),
																'value'=>'',
																'size'=>6,
																'onChange'=>'halo.chart.draw_charts(this)',
																'options'=>array(array('title'=>__halotext('Visits'),'value'=>'VisitsChart')
																				,array('title'=>__halotext('Pageviews'),'value'=>'PageViewsChart')
																				,array('title'=>__halotext('Geo Network'),'value'=>'GeoNetworkChart')
																				,array('title'=>__halotext('Platform and Device'),'value'=>'PlatformDeviceChart')
																				,array('title'=>__halotext('Social Activities'),'value'=>'SocialActivitiesChart')
																				)))->fetch()}}
				<?php $now = Carbon::now();?>
				{{HALOUIBuilder::getInstance('','form.select',array('name'=>'chart_period',
																'title'=>__halotext('Select Period'),
																'value'=>'',
																'size'=>6,
																'onChange'=>'halo.chart.draw_charts(this)',
																'options'=>array(array('title'=>__halotext('Today'),'value'=>'0')
																				,array('title'=>__halotext('Yesterday'),'value'=>'1')
																				,array('title'=>__halotext('10 days ago'),'value'=>'10')
																				,array('title'=>__halotext('A month ago'),'value'=>'30')
																				,array('title'=>__halotext('3 months ago'),'value'=>'90')
																				)))->fetch()}}
			<div class="clearfix"></div>
			</form>
			</div>
			<div class="halo_chart_wrapper_content"></div>
			<div class="clearfix"></div>
		
		</div>
	</div>
	<div class="col-md-6">		
		<div class="halo_statistic_dimension_charts panel panel-default ">
			<div class="authorize-wrapper" style="display: none">
				<div class="alert alert-danger">{{__halotext('You need to authorize with Google Anaylytics to view the charts')}}
				<button class="halo-btn authorize-button">{{__halotext('Click here to authorize')}}</button><br/>
				</div>
			</div>
			<div class="halo-chart-form-wrapper">
			<form class="halo_dimension_charts_form">
				{{HALOUIBuilder::getInstance('','form.select',array('name'=>'chart_dimension',
																'title'=>__halotext('Select Chart'),
																'value'=>'',
																'size'=>6,
																'onChange'=>'halo.chart.draw_charts(this)',
																'options'=>array(array('title'=>__halotext('Visits'),'value'=>'VisitsChart')
																				,array('title'=>__halotext('Pageviews'),'value'=>'PageViewsChart')
																				,array('title'=>__halotext('Geo Network'),'value'=>'GeoNetworkChart')
																				,array('title'=>__halotext('Platform and Device'),'value'=>'PlatformDeviceChart')
																				,array('title'=>__halotext('Social Activities'),'value'=>'SocialActivitiesChart')
																				)))->fetch()}}
				<?php $now = Carbon::now();?>
				{{HALOUIBuilder::getInstance('','form.select',array('name'=>'chart_period',
																'title'=>__halotext('Select Period'),
																'value'=>'',
																'size'=>6,
																'onChange'=>'halo.chart.draw_charts(this)',
																'options'=>array(array('title'=>__halotext('Today'),'value'=>'0')
																				,array('title'=>__halotext('Yesterday'),'value'=>'1')
																				,array('title'=>__halotext('10 days ago'),'value'=>'10')
																				,array('title'=>__halotext('A month ago'),'value'=>'30')
																				,array('title'=>__halotext('3 months ago'),'value'=>'90')
																				)))->fetch()}}
			<div class="clearfix"></div>
			</form>
			</div>
			<div class="halo_chart_wrapper_content"></div>
			<div class="clearfix"></div>
		
		</div>
	</div>
</div>
<div id="halo_gatracking" data-gapagetype="{{HALOConfig::get('global.GAPageTypeDimension',0)}}" data-gauser="{{HALOConfig::get('global.GARegsiteredUserDimension',0)}}" data-gaprofile="{{HALOConfig::get('global.GAViewId')}}" data-ga="{{HALOConfig::get('global.GATrackingId')}}"></div>
<div id="halo_ggClientId" data-gg="{{HALOConfig::get('global.googleAPIClientId')}}"></div>
<div id="halo_ggApiKey" data-gg="{{HALOConfig::get('global.googleAPIKey')}}"></div>
<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery('[name="chart_dimension"]').each(function(){
			halo.chart.draw_charts(jQuery(this));
		});
	});
</script>		 

</div>
@stop

{{-- Scripts --}}
@section('scripts')
@stop