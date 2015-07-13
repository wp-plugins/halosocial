@extends('site.layouts.default')

{{-- Web site Title --}}
@section('title')
{{{ __halotext('Conv') }}} ::{{{$title}}}
@parent
@stop
{{-- Content --}}
@section('content')
{{-- Conv Content --}}
<div class="halo-inbox-wrapper">
    <div class="halo-inbox-conv-list col-sm-4">
        <div class="panel panel-default">
            <div class="panel-body">
				@if(count($convsArr))
				{{HALOUIBuilder::getInstance('conv_recent_list','conv.recentlist_fullview',array('zone'=>'conv.fullview.recentlist'
																								,'showOlder'=>false,'conv_groups'=>$convsArr))->fetch()}}
				@else
					{{__halotext("You don't have any recent conversation")}}
				@endif
            </div>
        </div>
    </div>
    <div class="halo-inbox-conv-view col-sm-8">
			{{HALOUIBuilder::getInstance('','conv.fullview_container',array('messages' => $messages,'conv'=>$conv))->fetch()}}
    </div>
</div>
<script>
	window.halo_popup_message_win = 0;
</script>
{{-- Conv Content --}}

@stop

