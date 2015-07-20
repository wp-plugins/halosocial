<?php
/*
 * Plugin Name: HaloSocial
 * Plugin URL: https://halo.social
 * Description: Social Networking Plugin for WordPress
 * Author: HaloSocial
 * Author URL: https://halo.social
 * Version: 1.0
 * Copyright: (c) 2015 HaloSocial, Inc. All Rights Reserved.
 * License: GPLv3 or later
 * License URL: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: halosocial
 * Domain Path: /language
 *
 * HaloSocial is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * HaloSocial is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY. See the
 * GNU General Public License for more details.
 */
?>

{{-- ///////////////////////////////////// Rating UI ///////////////////////////////////// --}}
@beginUI('review.rating')
<?php
$max = isset($builder->max) ? $builder->max : 5;
$color = isset($builder->color) ? $builder->color : 'text-warning';
$iFull = isset($builder->iFull) ? $builder->iFull : 'star ' . $color;
$iHalf = isset($builder->iHalf) ? $builder->iHalf : 'star-half-o ' . $color;
$iEmpty = isset($builder->iEmpty) ? $builder->iEmpty : 'star-o ' . $color;
$nFull = (int)floor($builder->target->rating);
$nHalf = (int)(ceil($builder->target->rating) - $nFull);
$nEmpty = $max - $nFull - $nHalf;
$count = $builder->target->review_count;
?>
@if ($count)
<div class="halo-rating-wrapper" data-halozone="halo_rating.{{$builder->target->getZone()}}">
		<span>
			<a href="javascript:void(0)" @if($builder->target->review_count && $builder->target->review) {{$builder->target->review->getBriefDataAttribute()}} @endif>
				@for($i = 0; $i < $nFull;$i++)
					{{ HALOUIBuilder::icon($iFull) }}
				@endfor
				@for($i = 0; $i < $nHalf;$i++)
					{{ HALOUIBuilder::icon($iHalf) }}
				@endfor
				@for($i = 0; $i < $nEmpty;$i++)
					{{ HALOUIBuilder::icon($iEmpty) }}
				@endfor
			</a>
		</span>
	<a href="{{$builder->target->getUrl().'#reviews'}}">
		{{sprintf(__halotext('(%s)'),$builder->target->review_count)}}
	</a>
	@if($builder->showAdd && HALOAuth::can('review.create',$builder->target))
	<span class="halo-rating-add">
		<a href="javascript:void(0)"
		   onclick="halo.review.submit('{{$builder->target->getContext()}}','{{$builder->target->id}}')">
			{{sprintf(__halotext('Add your review'))}}
		</a>
	</span>
	@endif
</div>
@else
    {{-- TODO: no reviews to show --}}
@endif
@endUI

{{-- ///////////////////////////////////// Rating UI ///////////////////////////////////// --}}
@beginUI('review.item_rating')
<?php
$max = isset($builder->max) ? $builder->max : 5;
$color = isset($builder->color) ? $builder->color : 'text-warning';
$iFull = isset($builder->iFull) ? $builder->iFull : 'star ' . $color;
$iHalf = isset($builder->iHalf) ? $builder->iHalf : 'star-half-o ' . $color;
$iEmpty = isset($builder->iEmpty) ? $builder->iEmpty : 'star-o ' . $color;
$nFull = (int)floor($builder->review->rating);
$nHalf = (int)(ceil($builder->review->rating) - $nFull);
$nEmpty = $max - $nFull - $nHalf;
?>
<span class="halo-star-bar">
		@for($i = 0; $i < $nFull;$i++)
			{{ HALOUIBuilder::icon($iFull) }}
		@endfor
		@for($i = 0; $i < $nHalf;$i++)
			{{ HALOUIBuilder::icon($iHalf) }}
		@endfor
		@for($i = 0; $i < $nEmpty;$i++)
			{{ HALOUIBuilder::icon($iEmpty) }}
		@endfor
	</span>
@endUI

{{-- ///////////////////////////////////// Review list UI ///////////////////////////////////// --}}
@beginUI('review.list')
<?php $reviews = $builder->target->getReviews(0); ?>
<div class="halo-review-list-wrapper" {{$builder->getZone()}}>
<p class="halo-no-reviews text-muted @if(count($reviews) != 0) hidden @endif">{{__halotext('No Reviews')}}.
	@if(HALOAuth::can('review.create',$builder->target))
	{{__halotext('Click')}} <a href="javascript:void()"
	                         onclick="halo.review.submit('{{$builder->target->getContext()}}','{{$builder->target->id}}')">{{__halotext('here')}}</a>
	{{__halotext('to post your review')}}
	@endif
	</p>
@foreach($reviews->slice(0,$builder->limit) as $review)
{{HALOUIBuilder::getInstance('','review.item',array('review'=>$review,'zone'=>$review->getZone()))->fetch()}}
@endforeach
@if(count($reviews) > $builder->limit)
<div class="halo-stream-review-item halo-review-viewall">
	<a href="#more" onclick="halo.review.viewall('{{$builder->target->id}}','{{$builder->target->getContext()}}')">{{sprintf(__halotext('view	%s more'), count($reviews) - $builder->limit)}} </a>
</div>
@endif
</div>
@endUI

{{-- ///////////////////////////////////// Review list UI ///////////////////////////////////// --}}
@beginUI('review.list_wrapper')
<?php
	$rating = $builder->target->rating?round($builder->target->rating, 1): __halotext('N/A');
	$reviewCount = $builder->target->review_count?$builder->target->review_count:0;
?>
<div class="halo-reviews-panel">
	@if($reviewCount)
	<div class="col-xs-12 col-sm-3 halo-reviews-summary">
		{{$builder->target->reviews()->first()->getBriefBuilder()->fetch()}}
	</div>
	<div class="col-xs-12 col-sm-9 halo-reviews-detail">
		<div class="halo-review-list-wrapper" data-halozone="reviews.{{$builder->target->getZone()}}">
		{{HALOUIBuilder::getInstance('','review.list',array('target'=>$builder->target, 'zone'=>$builder->zone, 'limit'=>$builder->limit))->fetch()}}
		</div>
	</div>
	@else
	<div class="halo-review-list-wrapper" data-halozone="reviews.{{$builder->target->getZone()}}">
	{{HALOUIBuilder::getInstance('','review.list',array('target'=>$builder->target, 'zone'=>$builder->zone, 'limit'=>$builder->limit))->fetch()}}
	</div>	
	@endif
</div>
@endUI

{{-- ///////////////////////////////////// Review item UI ///////////////////////////////////// --}}
@beginUI('review.item')
<div class="halo-review-item halo-stream-review-item" id="review_{{$builder->review->id}}" {{$builder->getZone()}}>
<div class="halo-review-headline">
	{{$builder->review->actor->getDisplayLink('halo-stream-author')}}{{HALOUIBuilder::getInstance('','review.item_rating',array('review'=>$builder->review))->fetch()}}
</div>
<div class="halo-review-message" data-halozone="review.message.{{$builder->review->id}}">
	{{HALOUIBuilder::getInstance('','ellipsis',array('message' =>$builder->review->getMessage(), 
													'data' =>array('height' => '100')))->fetch()}} 
</div>
<div class="halo-review-actions halo-stream-actions clearfix">
	{{ HALOUIBuilder::getInstance('','review.action',array('review'=>$builder->review))->fetch()}}
</div>
</div>
@endUI

{{-- ///////////////////////////////////// Review Action UI ///////////////////////////////////// --}}
@beginUI('review.action')
<div class="halo-pull-left">
	<!-- Show likes -->
		<span class="halo-like">
			{{ HALOLikeAPI::getLikeDislikeHtml($builder->review) }}
		</span>
	@if(HALOAuth::can('review.edit',$builder->review))
	<!-- Show Edit -->
	<span><a class="text-muted" href="javascript:void(0);" onclick="halo.review.edit('{{$builder->review->id}}')"
	         title="{{__halotext('Edit')}}">{{__halotext('Edit')}}</a></span>
	@endif
	@if(HALOAuth::can('review.delete',$builder->review))
	<!-- Show Delete -->
	<span><a class="text-muted" href="javascript:void(0)" onclick="halo.review.deleteMe('{{$builder->review->id}}')"
	         title="{{__halotext('Delete')}}">{{__halotext('Delete')}}</a></span>
	@endif
	@if(!$builder->review->isModerated())
	@if(!HALOAuth::can('review.approve'))
	<!-- Show Approve -->
	<span class="text-warning"><b>[{{__halotext('Waiting for approval')}}]</b></span>
	@else
	<!-- Show Approve -->
	<span><a class="text-primary" href="javascript:void(0)"
	         onclick="halo.review.approve('{{$builder->review->id}}')"
	         title="{{__halotext('Approve')}}"><b>{{__halotext('Approve')}}</b></a></span>
	@endif
	@endif
</div>
<div class="halo-pull-right">
	<!-- Show time -->
	<span><a class="text-muted" href="javascript:void(0);" {{HALOUtilHelper::getDataUTime($builder->review->created_at)}} title="{{HALOUtilHelper::getDateTime($builder->review->created_at)}}">{{HALOUtilHelper::getElapsedTime($builder->review->created_at)}}</a></span>
</div>

@endUI

{{-- ///////////////////////////////////// Post brief info UI ///////////////////////////////////// --}}
@beginUI('review.brief')
<?php
$review = $builder->review;
$target = $review->reviewable;
$total = $target->reviews()->count();
$rating = $target->rating?round($target->rating, 1): __halotext('N/A');
?>
<div class="halo-brief-wrapper" {{$builder->getZone()}}>
<div class="halo-brief-content-wrapper halo-review-stats">
	<div class="halo-brief-content">
		<div class="halo-reviews-summary-header">
			<div class="halo-reviews-rating badge"><span>{{$rating}}</span>{{HALOUIBuilder::icon('star')}}</div>
			@if($total)
			<?php 
			$oneStar = $target->reviews()->where('rating', 1)->count();
			$oneStarPer = round(($oneStar / $total) * 100, 2);
			$twoStar = $target->reviews()->where('rating', 2)->count();
			$twoStarPer = round(($twoStar / $total) * 100, 2);
			$threeStar = $target->reviews()->where('rating', 3)->count();
			$threeStarPer = round(($threeStar / $total) * 100, 2);
			$fourStar = $target->reviews()->where('rating', 4)->count();
			$fourStarPer = round(($fourStar / $total) * 100, 2);
			$fiveStar = $target->reviews()->where('rating', 5)->count();
			$fiveStarPer = round(($fiveStar / $total) * 100, 2);
			?>
			<div class="halo-reviews-counters">
				<div class="halo-reviews-counters">
					{{sprintf(__halotext('%s of 5 stars'), $rating)}}
				</div>
				<div class="halo-reviews-counters">
					{{sprintf(__halotext('%s reviews'), $total)}}
				</div>				
			</div>
			@endif
		</div>
		@if($total)
		<div class="halo-reviews-summary-body">
		<ul>
			<li>
				<div>
					<i class="fa fa-star text-warning"></i> <i class="fa fa-star text-warning"></i> <i
						class="fa fa-star text-warning"></i> <i class="fa fa-star text-warning"></i> <i
						class="fa fa-star text-warning"></i><span> ({{$fiveStar}}) </span>
				</div>
				<div class="progress">
					<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="{{$fiveStarPer}}"
					     aria-valuemin="0" aria-valuemax="100" style="width: {{$fiveStarPer}}%">
					</div>
				</div>
			</li>
			<li>
				<div>
					<i class="fa fa-star text-warning"></i> <i class="fa fa-star text-warning"></i> <i
						class="fa fa-star text-warning"></i> <i class="fa fa-star text-warning"></i>
					<span> ({{$fourStar}}) </span>
				</div>

				<div class="progress">
					<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="{{$fourStarPer}}"
					     aria-valuemin="0" aria-valuemax="100" style="width: {{$fourStarPer}}%">
					</div>
				</div>
			</li>
			<li>
				<div>
					<i class="fa fa-star text-warning"></i> <i class="fa fa-star text-warning"></i> <i
						class="fa fa-star text-warning"></i><span> ({{$threeStar}}) </span>
				</div>
				<div class="progress">
					<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="{{$threeStarPer}}"
					     aria-valuemin="0" aria-valuemax="100" style="width: {{$threeStarPer}}%">
					</div>
				</div>
			</li>
			<li>
				<div>
					<i class="fa fa-star text-warning"></i> <i
						class="fa fa-star text-warning"></i><span> ({{$twoStar}}) </span>
				</div>
				<div class="progress">
					<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="{{$twoStarPer}}"
					     aria-valuemin="0" aria-valuemax="100" style="width: {{$twoStarPer}}%">
					</div>
				</div>
			</li>
			<li>
				<div>
					<i class="fa fa-star text-warning"></i>
					<span> ({{$oneStar}}) </span>
				</div>
				<div class="progress">
					<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="{{$oneStarPer}}"
					     aria-valuemin="0" aria-valuemax="100" style="width: {{$oneStarPer}}%">
					</div>
				</div>
			</li>
		</ul>
		</div>
		@endif
	</div>
</div>
</div>
@endUI