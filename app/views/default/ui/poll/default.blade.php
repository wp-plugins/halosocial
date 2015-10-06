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

{{-- Poll Siderbar UI --}}
@beginUI('poll.sidebar')
<?php $poll = $builder->poll; ?>
@if ($poll)
<div class="panel panel-default halo-module-poll" {{$builder->getZone()}}>
    <div class="panel-heading">
        <h3 class="panel-title">
            <a href="#">{{$poll->question}}</a>
        </h3>
    </div>
    <form id="PollAnswersForm" name="PollAnswersForm" method="post" onsubmit="halo.poll.submitAnswer(); return false;">
        <div class="panel-body">
            <div class="halo-app-box-content">
                <div class="row">
                    <ul class="list-group">
                        @foreach ($poll->answers as $answer)
                        <li class="list-group-item">
                            @if ($answer->type == 'checkbox')
                            <div class="radio">
                                <label>
                                    <input type="radio" name="poll_answer_id" value="{{$answer->id}}">
                                    {{$answer->answer}}
                                </label>
                            </div>
                            @elseif ($answer->type == 'textbox')
                            {{HALOUIBuilder::getInstance('', 'form.text', array('name'  => 'email', 'id' => 'email', 'value' => '', 'title' => __halotext('Email'), 'validation' => 'required'))->fetch()}}
                            {{HALOUIBuilder::getInstance('', 'form.text', array('name'  => 'full_name', 'id' => 'full_name', 'value' => '', 'title' => __halotext('Name'), 'validation' => 'required'))->fetch()}}
                            {{HALOUIBuilder::getInstance('', 'form.textarea', array('name'  => 'comment', 'id' => 'comment', 'value' => '', 'title' => __halotext('Messages'), 'validation' => 'required'))->fetch()}}
                            {{HALOUIBuilder::getInstance('', 'form.hidden', array('name' => 'poll_answer_id', 'value' => $answer->id))->fetch()}}
                            @endif
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <button type="submit" class="halo-btn halo-btn-primary halo-btn-sm">Vote</button>
            {{-- <a href="javascript:void();" onclick="halo.poll.getOther({{$poll->id}}, 'prev');" class="halo-btn halo-btn-sm">Prev</a> --}}
            {{-- <a href="javascript:void();" onclick="halo.poll.getOther({{$poll->id}}, 'next');" class="halo-btn halo-btn-sm">Next</a> --}}
        </div>
        <input name="poll_id" value="{{$poll->id}}" type="hidden"/>
     </form>
</div>
@endif
@endUI

{{-- Feedback Components UI --}}
@beginUI('poll.googleform')
<div class="panel panel-default halo-module-poll" {{$builder->getZone()}}>
<div class="panel-heading">
    <h3 class="panel-title">
        <a href="#">{{__halotext('Feedback')}}</a>
    </h3>
</div>
<div class="panel-body">
    <div class="halo-app-box-content">
        <div class="row">
        <?php
        $feedbackComs = HALOAssetHelper::getFeedbackComponents();
        $options = array(array('value' => '', 'title' => __halotext('Feedback Components')));
        foreach ($feedbackComs['components'] as $com) {
            $options[] = array(
                'title' => $com,
                'value' => $com
            );
        }
        ?>
        <p>{{__halotext('FEEDBACK_DESCRIPTION')}}</p>
        <script type="text/javascript">
            var submittedGoogleForm = false;
            function onGoogleFormRedirection() {
                jQuery("[name='entry.1197293239'] option:eq(0)").attr('selected', 'selected');
                jQuery("[name='entry.1815715185']").val('');
                halo.popup.showMsg(__halotext('Thank you for your feedback.'));
            }
        </script>
        <iframe name="HideGoogleForm" id="HideGoogleForm" style="display: none;" onload="if(submittedGoogleForm){onGoogleFormRedirection();}"></iframe>
        {{
            HALOUIBuilder::getInstance('', 'form.form', array('name' => 'GoogleForm', 'action' => 'https://docs.google.com/forms/d/1v9pdyuMrQkm6p6R79O9zhGCvWCZi-k6eq-SVFzRyQC4/formResponse', 'method' => 'POST', 'id' => 'GoogleForm', 'target' => 'HideGoogleForm', 'onsubmit' => 'submittedGoogleForm=true;'))
            ->addUI('component', HALOUIBuilder::getInstance('', 'form.select_original', array('name'  => 'entry.1197293239', 'options' => $options, 'validation' => 'required', 'title' => __halotext('Feedback Components'), 'label' => false)))
            ->addUI('description', HALOUIBuilder::getInstance('', 'form.textarea', array('name'  => 'entry.1815715185', 'rows' => 4, 'value' => '', 'validation' => 'required', 'title' => __halotext('Feedback Description'), 'label' => false, 'placeholder' => __halotext('Feedback Description'))))
            ->addUI('draftResponse', HALOUIBuilder::getInstance('', 'form.hidden', array('name'    => 'draftResponse', 'value' => '[,,"-1860710113751705348"]')))
            ->addUI('pageHistory', HALOUIBuilder::getInstance('', 'form.hidden', array('name'    => 'pageHistory', 'value' => '0')))
            ->addUI('fbzx', HALOUIBuilder::getInstance('', 'form.hidden', array('name'    => 'fbzx', 'value' => '-1860710113751705348')))
            ->addUI('submit', HALOUIBuilder::getInstance('', 'form.submit', array('name' => 'submit', 'value' => __halotext('Feedback Submit'), 'id' => 'ss-submit', 'class' => 'halo-btn halo-btn-primary')))
            ->fetch()
        }}
        </div>
    </div>
</div>
</div>
@endUI
