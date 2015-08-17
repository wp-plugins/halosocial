{{-- Web site Title --}}
@section('title')
	{{{ $title }}} :: @parent
@stop

{{-- Toolbar --}}
@section('toolbar')
	<div class="row">
		
	</div>
@stop

{{-- Content --}}
@section('content')
<style>
.halo-starter-notice {display:none !important}
</style>
<div class="row">
	<div class="page-header">
		<h3>
			{{{ $title }}}
		</h3>
	</div>
</div>
@stop

{{-- Scripts --}}
@section('scripts')
@stop