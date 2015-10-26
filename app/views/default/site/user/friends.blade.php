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
	<div class="panel-heading">{{sprintf(__halotext("%s's friends (%s out of %s)"),$user->getDisplayLink(), $users->count(), $user->friends()->count())}}</div>
	<div class="panel-body">
		<div class="page-filters">
			{{ HALOFilter::getDisplayFilterUI('user.listing.index')->fetch() }}
		</div>
		{{$friendList}}
		{{$users->links('ui.pagination')}}
	</div>

</div>

{{-- End Friend list content --}}

@stop

