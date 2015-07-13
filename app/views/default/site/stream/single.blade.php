<div class="panel panel-default">
	<div class="panel-body">
		{{-- stream header --}}		
		<div class="halo-stream-header">
		<form id="streamFilters">
			<input type="hidden" value="{{$actId}}" name="filters[{{$streamFilters->first()->id}}]"/>
		</form>
		</div>
		@if(count($acts))
		{{-- stream content --}}
		{{ HALOUIBuilder::getInstance('','stream.content',array('acts' => $acts,'zone'=>'stream_content','showLoadMore' => $showLoadMore))->fetch()}}
		{{-- ./stream content --}}
		@else
			<p>{{__halotext('No Activities Found')}}</p>
		@endif
	</div>
</div>
