<script >
	var halo_jax_targetUrl = "{{ HALO_ROOT_URL . 'social/ajax' }}";
 
</script>
<meta name="csrf_token" content="{{ csrf_token() }}">
{{-- Web site Title --}}
@section('title')
	{{{ $title }}} :: @parent
@stop
<div class="panel panel-default">
	<div class="panel-body">
		<h3>
			{{{ $title }}}
		</h3>
		<div class="alert alert-success">{{Lang::get('Welcome to HaloSocial. Please make sure you have internet connection before starting the installation')}}</div>
		<div class="block-center" data-halozone="tools-btn">
			<button id="halo_tools_btn" data-timer="6" type="button" onclick="halo.tools.installDatabase('install')" class="btn btn-default">{{Lang::get('Install')}}</button>
		</div>
		<div class="halo-tool-console" data-halozone="tools-console">
		
		</div>
	</div>
</div>
