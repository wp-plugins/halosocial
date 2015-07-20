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

@beginUI('email.notification_layout')
<td style="font:14px/1.4285714 Arial,sans-serif;padding:0;background-color:#ffffff;border-radius:5px">
	<div style="border:1px solid #cccccc;border-radius:5px;padding:20px">
		<table style="width:100%;border-collapse:collapse">
			<tbody>
				<tr>
					<td style="font:14px/1.4285714 Arial,sans-serif;padding:0">
						<table style="width:100%;border-collapse:collapse">
							<tbody>
								<tr>
									<td style="font:14px/1.4285714 Arial,sans-serif;padding:0;width:32px;vertical-align:top">
										@if ($builder->actor)
										<img width="{{HALO_PHOTO_THUMB_SIZE}}" height="{{HALO_PHOTO_THUMB_SIZE}}" alt="{{$builder->actor->getDisplayName()}}" src="{{$builder->actor->getThumb()}}" style="border-radius:3px">
										@endif
									</td>
									<td style="font:14px/1.4285714 Arial,sans-serif;padding:0 0 0 10px">
										<table style="width:100%;border-collapse:collapse">
											<tbody>
												<tr>
													<td style="font:14px/1.4285714 Arial,sans-serif;padding:0;line-height:1">
														{{$builder->notification_headline}}
													</td>
												</tr>
												@if(!empty($builder->notification_msg))
												<tr>
													<td style="font:14px/1.4285714 Arial,sans-serif;padding:5px 0 0;font-weight:bold;line-height:1.2">
														{{$builder->notification_msg}}
													</td>
												</tr>
												@endif
												<tr>
													<td style="font:14px/1.4285714 Arial,sans-serif;padding:10px 0 0"></td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
							</tbody>
						</table>										
					</td>
				</tr>
				<tr>
					<td colspan="2" style="font:14px/1.4285714 Arial,sans-serif;padding:10px 0 0;border-top:1px solid #cccccc;line-height:1">
						<a href="{{$builder->target->getUrl()}}" style="color:#3b73af;text-decoration:none" target="_blank">{{__halotext('Click here')}}</a> {{__halotext('to view in detail.')}}
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</td>
@endUI

{{-- ///////////////////////////////////// Plain Email msg for new comment adding ///////////////////////////////////// --}}
@beginUI('email.commentNotif_plain')
	Hello {{$builder->toName}},
		{{sprintf(__halotext('%s commented on a %s that you are following'),$builder->actorsText,$builder->target->getDisplayName())}}
		{{$builder->commentText}}
		{{sprintf(__halotext('To see in detail, please go to %'),$builder->target->getUrl())}}
		
	Regards,
	Halo.Social
@endUI

{{-- ///////////////////////////////////// Html Email msg for new comment adding ///////////////////////////////////// --}}
@beginUI('email.commentNotif_html')
	{{$builder->copyAttributes('','email.notification_layout')
			->setAttrs(array('notification_headline'=>sprintf(__halotext('%s commented on a %s that you are following'),'<strong>'.$builder->actorsText.'</strong>',$builder->target->getDisplayLink())
							,'notification_msg'=>$builder->commentText))
			->fetch()}}
@endUI

{{-- ///////////////////////////////////// Plain Email msg for like action ///////////////////////////////////// --}}
@beginUI('email.likeNotif_plain')
	Hello {{$builder->toName}},
		{{sprintf(__halotext('%s liked your %s'),$builder->actorsText,$builder->target->getDisplayName())}}

		{{sprintf(__halotext('To see in detail, please go to %'),$builder->target->getUrl())}}
		
	Regards,
	Halo.Social
@endUI

{{-- ///////////////////////////////////// Html Email msg for like action ///////////////////////////////////// --}}
@beginUI('email.likeNotif_html')
	{{$builder->copyAttributes('','email.notification_layout')
			->setAttrs(array('notification_headline'=>sprintf(__halotext('%s liked your %s'),'<strong>'.$builder->actorsText.'</strong>',$builder->target->getDisplayLink())))
			->fetch()}}
@endUI

{{-- ///////////////////////////////////// Plain Email msg for user tagging ///////////////////////////////////// --}}
@beginUI('email.tagNotif_plain')
	Hello {{$builder->toName}},
		{{sprintf(__halotext('%s tagged you in a %s'),$builder->actorsText,$builder->target->getDisplayName())}}

		{{sprintf(__halotext('To see in detail, please go to %'),$builder->target->getUrl())}}
		
	Regards,
	Halo.Social
@endUI

{{-- ///////////////////////////////////// Html Email msg for user tagging ///////////////////////////////////// --}}
@beginUI('email.tagNotif_html')
	{{$builder->copyAttributes('','email.notification_layout')
			->setAttrs(array('notification_headline'=>sprintf(__halotext('%s tagged you in a %s'),'<strong>'.$builder->actorsText.'</strong>',$builder->target->getDisplayLink())))
			->fetch()}}
@endUI

{{-- ///////////////////////////////////// Plain Email msg for new message ///////////////////////////////////// --}}
@beginUI('email.messageNotif_plain')
	Hello {{$builder->toName}},
		{{sprintf(__halotext('%s sent you a message'),$builder->actorsText)}}
		{{$builder->messageText}}
		{{sprintf(__halotext('To see in detail, please go to %'),$builder->target->getUrl())}}
		
	Regards,
	Halo.Social
@endUI

{{-- ///////////////////////////////////// Html Email msg for new message ///////////////////////////////////// --}}
@beginUI('email.messageNotif_html')
	{{$builder->copyAttributes('','email.notification_layout')
			->setAttrs(array('notification_headline'=>sprintf(__halotext('%s sent you a message'),'<strong>'.$builder->actorsText.'</strong>',$builder->target->getDisplayLink())
							,'notification_msg'=>$builder->messageText))
			->fetch()}}
@endUI

{{-- ///////////////////////////////////// Plain Email msg for friend request action ///////////////////////////////////// --}}
@beginUI('email.friendRequestNotif_plain')
	Hello {{$builder->toName}},
		{{sprintf(__halotext('%s sent a friend request'),$builder->actorsText)}}

		{{sprintf(__halotext('To see in detail, please go to %'),$builder->target->getUrl())}}
		
	Regards,
	Halo.Social
@endUI

{{-- ///////////////////////////////////// Html Email msg for friend request action ///////////////////////////////////// --}}
@beginUI('email.friendRequestNotif_html')
	{{$builder->copyAttributes('','email.notification_layout')
			->setAttrs(array('notification_headline'=>sprintf(__halotext('%s sent a friend request'),'<strong>'.$builder->actorsText.'</strong>')))
			->fetch()}}
@endUI

{{-- ///////////////////////////////////// Plain Email msg for friend approve action ///////////////////////////////////// --}}
@beginUI('email.friendApproveNotif_plain')
	Hello {{$builder->toName}},
		{{sprintf(__halotext('You are now friends with %s'),$builder->actorsText)}}

		{{sprintf(__halotext('To see in detail, please go to %'),$builder->target->getUrl())}}
		
	Regards,
	Halo.Social
@endUI

{{-- ///////////////////////////////////// Html Email msg for friend approve action ///////////////////////////////////// --}}
@beginUI('email.friendApproveNotif_html')
	{{$builder->copyAttributes('','email.notification_layout')
			->setAttrs(array('notification_headline'=>sprintf(__halotext('You are now friends with %s'),'<strong>'.$builder->actorsText.'</strong>')))
			->fetch()}}
@endUI

{{-- ///////////////////////////////////// Plain Email msg for friend reject action ///////////////////////////////////// --}}
@beginUI('email.friendRejectNotif_plain')
	Hello {{$builder->toName}},
		{{sprintf(__halotext('%s rejected your friend request'),$builder->actorsText)}}

		{{sprintf(__halotext('To see in detail, please go to %'),$builder->target->getUrl())}}
		
	Regards,
	Halo.Social
@endUI

{{-- ///////////////////////////////////// Html Email msg for friend reject action ///////////////////////////////////// --}}
@beginUI('email.friendRejectNotif_html')
	{{$builder->copyAttributes('','email.notification_layout')
			->setAttrs(array('notification_headline'=>sprintf(__halotext('%s rejected your friend request'),'<strong>'.$builder->actorsText.'</strong>')))
			->fetch()}}
@endUI

{{-- ///////////////////////////////////// Plain Email msg for mention user ///////////////////////////////////// --}}
@beginUI('email.mentionNotif_plain')
	Hello {{$builder->toName}},
		{{sprintf(__halotext('%s mentioned you in a %s'),$builder->actorsText,$builder->target->getDisplayName())}}

		{{sprintf(__halotext('To see in detail, please go to %'),$builder->target->getUrl())}}
		
	Regards,
	Halo.Social
@endUI

{{-- ///////////////////////////////////// Html Email msg for mention user ///////////////////////////////////// --}}
@beginUI('email.mentionNotif_html')
	{{$builder->copyAttributes('','email.notification_layout')
			->setAttrs(array('notification_headline'=>sprintf(__halotext('%s mentioned you in a %s'),'<strong>'.$builder->actorsText.'</strong>',$builder->target->getDisplayLink())))
			->fetch()}}
@endUI

{{-- ///////////////////////////////////// Plain Email msg for post approve action ///////////////////////////////////// --}}
@beginUI('email.postApproveNotif_plain')
	Hello {{$builder->toName}},
		{{sprintf(__halotext('%s approved your post'),$builder->actorsText)}}

		{{sprintf(__halotext('To see in detail, please go to %'),$builder->target->getUrl())}}
		
	Regards,
	Halo.Social
@endUI

{{-- ///////////////////////////////////// Html Email msg for post approve action ///////////////////////////////////// --}}
@beginUI('email.postApproveNotif_html')
	{{$builder->copyAttributes('','email.notification_layout')
			->setAttrs(array('notification_headline'=>sprintf(__halotext('%s approved your post'),'<strong>'.$builder->actorsText.'</strong>')))
			->fetch()}}
@endUI

{{-- ///////////////////////////////////// Plain Email msg for review approve action ///////////////////////////////////// --}}
@beginUI('email.reviewApproveNotif_plain')
	Hello {{$builder->toName}},
		{{sprintf(__halotext('%s approved your review'),$builder->actorsText)}}

		{{sprintf(__halotext('To see in detail, please go to %'),$builder->target->getUrl())}}
		
	Regards,
	Halo.Social
@endUI

{{-- ///////////////////////////////////// Html Email msg for review approve action ///////////////////////////////////// --}}
@beginUI('email.reviewApproveNotif_html')
	{{$builder->copyAttributes('','email.notification_layout')
			->setAttrs(array('notification_headline'=>sprintf(__halotext('%s approved your review'),'<strong>'.$builder->actorsText.'</strong>')))
			->fetch()}}
@endUI

{{-- ///////////////////////////////////// Plain Email msg for review create action ///////////////////////////////////// --}}
@beginUI('email.reviewCreateNotif_plain')
	Hello {{$builder->toName}},
		{{sprintf(__halotext('%s added review on post'),$builder->actorsText)}}

		{{sprintf(__halotext('To see in detail, please go to %'),$builder->target->getUrl())}}
		
	Regards,
	Halo.Social
@endUI

{{-- ///////////////////////////////////// Html Email msg for review create action ///////////////////////////////////// --}}
@beginUI('email.reviewCreateNotif_html')
	{{$builder->copyAttributes('','email.notification_layout')
			->setAttrs(array('notification_headline'=>sprintf(__halotext('%s added review on post'),'<strong>'.$builder->actorsText.'</strong>')))
			->fetch()}}
@endUI


{{-- ///////////////////////////////////// Plain Email msg for group approve action ///////////////////////////////////// --}}
@beginUI('email.groupApproveNotif_plain')
	Hello {{$builder->toName}},
		{{sprintf(__halotext('You are now a member of group %s'),$builder->target->getDisplayName())}}

		{{sprintf(__halotext('To see in detail, please go to %'),$builder->target->getUrl())}}
		
	Regards,
	Halo.Social
@endUI

{{-- ///////////////////////////////////// Html Email msg for group approve action ///////////////////////////////////// --}}
@beginUI('email.groupApproveNotif_html')
	{{$builder->copyAttributes('','email.notification_layout')
			->setAttrs(array('notification_headline'=>sprintf(__halotext('You are now a member of group %s'),'<strong>'.$builder->target->getDisplayLink().'</strong>')))
			->fetch()}}
@endUI

{{-- ///////////////////////////////////// Plain Email msg for new group join request ///////////////////////////////////// --}}
@beginUI('email.groupRequestNotif_plain')
	Hello {{$builder->toName}},
		{{sprintf(__halotext('%s requested to join your group %s'),$builder->actorsText,$builder->target->getDisplayName())}}
		{{$builder->commentText}}
		{{sprintf(__halotext('To see in detail, please go to %'),$builder->target->getUrl())}}
		
	Regards,
	Halo.Social
@endUI

{{-- ///////////////////////////////////// Html Email msg for new group join request ///////////////////////////////////// --}}
@beginUI('email.groupRequestNotif_html')
	{{$builder->copyAttributes('','email.notification_layout')
			->setAttrs(array('notification_headline'=>sprintf(__halotext('%s requested to join your group %s'),'<strong>'.$builder->actorsText.'</strong>',$builder->target->getDisplayLink())
							,'notification_msg'=>$builder->commentText))
			->fetch()}}
@endUI

{{-- ///////////////////////////////////// Plain Email msg for new group join invitation ///////////////////////////////////// --}}
@beginUI('email.groupInviteNotif_plain')
	Hello {{$builder->toName}},
		{{sprintf(__halotext('%s invited you to join group %s'),$builder->actorsText,$builder->target->getDisplayName())}}
		{{$builder->commentText}}
		{{sprintf(__halotext('To see in detail, please go to %'),$builder->target->getUrl())}}
		
	Regards,
	Halo.Social
@endUI

{{-- ///////////////////////////////////// Html Email msg for new group join invitation ///////////////////////////////////// --}}
@beginUI('email.groupInviteNotif_html')
	{{$builder->copyAttributes('','email.notification_layout')
			->setAttrs(array('notification_headline'=>sprintf(__halotext('%s invited you to join group %s'),'<strong>'.$builder->actorsText.'</strong>',$builder->target->getDisplayLink())
							,'notification_msg'=>$builder->commentText))
			->fetch()}}
@endUI

{{-- ///////////////////////////////////// Plain Email msg for new group post ///////////////////////////////////// --}}
@beginUI('email.groupPostNotif_plain')
	Hello {{$builder->toName}},
		{{sprintf(__halotext('%s posted on a %s that you are following'),$builder->actorsText,$builder->target->getDisplayName())}}
		{{$builder->commentText}}
		{{sprintf(__halotext('To see in detail, please go to %'),$builder->target->getUrl())}}
		
	Regards,
	Halo.Social
@endUI

{{-- ///////////////////////////////////// Html Email msg for new group post ///////////////////////////////////// --}}
@beginUI('email.groupPostNotif_html')
	{{$builder->copyAttributes('','email.notification_layout')
			->setAttrs(array('notification_headline'=>sprintf(__halotext('%s posted on a %s that you are following'),'<strong>'.$builder->actorsText.'</strong>',$builder->target->getDisplayLink())
							,'notification_msg'=>$builder->commentText))
			->fetch()}}
@endUI

{{-- ///////////////////////////////////// Plain Email msg for group role changed ///////////////////////////////////// --}}
@beginUI('email.groupChangeRoleNotif_plain')
	Hello {{$builder->toName}},
		{{sprintf(__halotext('%s changed your role in group %s to %s'),$builder->actorsText,$builder->target->getDisplayName(),$builder->newRole)}}
		{{sprintf(__halotext('To see in detail, please go to %'),$builder->target->getUrl())}}
		
	Regards,
	Halo.Social
@endUI

{{-- ///////////////////////////////////// Html Email msg for group role changed ///////////////////////////////////// --}}
@beginUI('email.groupChangeRoleNotif_html')
	{{$builder->copyAttributes('','email.notification_layout')
			->setAttrs(array('notification_headline'=>sprintf(__halotext('%s changed your role in group %s to %s'),'<strong>'.$builder->actorsText.'</strong>',$builder->target->getDisplayLink(),$builder->newRole)
							,'notification_msg'=>''))
			->fetch()}}
@endUI

{{-- ///////////////////////////////////// Plain Email msg for changing password ///////////////////////////////////// --}}
@beginUI('email.changePasswordNotif_plain')
	Hello {{$builder->toName}},
		{{sprintf(__halotext('Your account password has been changed successfully'))}}
	Regards,
	Halo.Social
@endUI

{{-- ///////////////////////////////////// Html Email msg for changing password ///////////////////////////////////// --}}
@beginUI('email.changePasswordNotif_html')
	Hello {{$builder->toName}},
		{{sprintf(__halotext('Your account password has been changed successfully'))}}
	Regards,
	Halo.Social
@endUI

{{-- ///////////////////////////////////// Html Email msg for sharing post ///////////////////////////////////// --}}
@beginUI('email.sharePost_html')
{{
	$builder->copyAttributes('', 'email.notification_layout')
			->setAttrs(array('notification_headline' => sprintf(__halotext('%s has shared you a new post'), '<strong>' . $builder->actorsText . '</strong>'), 'notification_msg' => ''))
			->fetch()
}}
@endUI

{{-- ///////////////////////////////////// Plain Email msg for sharing post ///////////////////////////////////// --}}
@beginUI('email.sharePost_plain')
	{{__halotext('Hello')}},
	{{sprintf(__halotext('%s has shared you a new post'), $builder->actorsText)}}
	{{sprintf(__halotext('To see in detail, please go to %s'), $builder->target->getUrl())}}
@endUI
