{{-- Web site Title --}}
@section('title')
	{{{ $title }}} :: @parent
@stop
{{HALOAssetHelper::printInlineScript()}}
<style>
.halo-starter-notice {display:none !important}
</style>
<div class="panel panel-default">
	<div class="panel-body">
		@if((get_option( 'halo_plugin_ver' ) != HALO_PLUGIN_VER) || (isset($isValid) && $isValid))
		<h3>
			{{{ $title }}} - Version: {{get_option( 'halo_plugin_ver' )}} to {{HALO_PLUGIN_VER}}
		</h3>
		<div class="alert alert-success">{{__halotext('Your HaloSocial plugin is required to do the upgrade process. Please make sure you have internet connection then click the Upgrade button below')}}</div>
		<div class="block-center" data-halozone="tools-btn">
			<button id="halo_tools_btn" data-timer="6" type="button" onclick="halo.tools.installDatabase('install')" class="btn btn-default">{{__halotext('Upgrade')}}</button>
		</div>
		<div class="halo-tool-console" data-halozone="tools-console">
		
		</div>
		@else
		<h3>
			{{{ $title }}} - Version: {{get_option( 'halo_plugin_ver' )}}
		</h3>
			{{HALOUIBuilder::getInstance('installPlugin','form.form_data',array('name'=>'upgradeForm'))
					->addUI('name', HALOUIBuilder::getInstance('','form.file',array('name'=>'upgrade_pkg','title'=>'Select package to upgrade:')))
					->addUI('csrf', HALOUIBuilder::getInstance('','form.hidden',array('name'=>'_token','value' => csrf_token())))
					->addUI('task', HALOUIBuilder::getInstance('','form.hidden',array('name'=>'task','value' => 'upgrade')))
					->addUI('view', HALOUIBuilder::getInstance('','form.hidden',array('name'=>'view','value' => 'tools')))
					->addUI('page', HALOUIBuilder::getInstance('','form.hidden',array('name'=>'page','value' => 'halo_dashboard')))
					->addUI('page', HALOUIBuilder::getInstance('','form.submit',array('name'=>'submit','value' => __halotext('Upgrade'))))
					->fetch()
			}}
		@endif
	</div>
</div>
