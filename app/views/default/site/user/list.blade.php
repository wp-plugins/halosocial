@extends('site.layouts.default')

{{-- Web site Title --}}
@section('title')
{{$title}}
@parent
@stop

{{-- Content --}}
@section('content')
{{-- tool bar --}}

{{-- End tool bar --}}

{{-- Friend list content --}}
<div class="panel panel-default">
	<div class="panel-heading"><h3 class="panel-title">{{sprintf(__halotext("View users (%s out of %s)"), $usersList->count(), $users->count())}}</h3></div>
	<div class="panel-body">
		<div class="page-filters">
			{{ HALOFilter::getDisplayFilterUI('user.listing.index')->fetch() }}
		</div>
		{{$usersListHtml}}
		{{$usersList->links('ui.pagination')}}
	</div>
	
</div>

{{-- End Friend list content --}}

@stop

