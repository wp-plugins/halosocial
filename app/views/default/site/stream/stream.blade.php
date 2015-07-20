<div class="halo-stream-header">
{{-- stream header --}}
{{ HALOUIBuilder::getInstance('','stream.header',array('streamFilters' => $streamFilters))->fetch()}}
{{-- ./stream header --}}
</div>
<div class="halo-stream-list">
{{-- stream content --}}
{{ HALOUIBuilder::getInstance('','stream.content',array('acts' => $acts,'zone'=>'stream_content','showLoadMore' => $showLoadMore))->fetch()}}
{{-- ./stream content --}}
</div>

