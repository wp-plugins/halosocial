@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
	{{{ $title }}} :: @parent
@stop

{{-- Content --}}
@section('content')
	<div class="page-header halo-admin-page-header">
		<h2>{{{ $title }}}</h2>
	</div>

	<table class="table table-bordered table-hover" id="roles" class="table table-striped table-hover">
		<thead>
			<tr>
				<th class="col-md-6">{{{ __halotext('admin/roles/table.name') }}}</th>
				<th class="col-md-2">{{{ __halotext('admin/roles/table.users') }}}</th>
				<th class="col-md-2">{{{ __halotext('admin/roles/table.created_at') }}}</th>
				<th class="col-md-2">{{{ __halotext('table.actions') }}}</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
@stop

{{-- Scripts --}}
@section('scripts')
	<script type="text/javascript">
		var oTable;
		jQuery(document).ready(function() {
				oTable = jQuery('#roles').dataTable( {
				"sDom": "<'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
				"sPaginationType": "bootstrap",
				"oLanguage": {
					"sLengthMenu": "_MENU_ records per page"
				},
				"bProcessing": true,
		        "bServerSide": true,
		        "sAjaxSource": "{{ URL::to('admin/roles/data') }}",
		        "fnDrawCallback": function ( oSettings ) {
	           		jQuery(".iframe").colorbox({iframe:true, width:"80%", height:"80%"});
	     		}
			});
		});
	</script>
@stop