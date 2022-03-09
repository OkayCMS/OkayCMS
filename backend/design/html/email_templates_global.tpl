{* Title *}
{$meta_title = $btr->email_templates_debug scope=global}

<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {$btr->email_templates_debug|escape}
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="alert alert--icon">
            <div class="alert__content">
                <div class="alert__title">{$btr->alert_description|escape}</div>
                <p>{$btr->general_design_message3|escape}</p>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="boxed match fn_toggle_wrap tabs">
            <div class="tabs">
                <div class="heading_tabs">
                    <div class="tab_navigation">
                        <a href="#tab1" class="heading_box tab_navigation_link">{$btr->general_templates_email_admin|escape}</a>
                        <a href="#tab2" class="heading_box tab_navigation_link">{$btr->general_templates_email_user|escape}</a>
                    </div>
                </div>
                <div class="tab_container">
                    <div id="tab1" class="tab">
                        <div class="design_tabs">
                            <div class="design_container">
                                <a class="design_tab focus" href="{url debug=emailOrderAdmin order_id=1}">emailOrderAdmin</a>
                                <a class="design_tab focus" href="{url debug=emailCommentAdmin comment_id=1}">emailCommentAdmin</a>
                                <a class="design_tab focus" href="{url debug=emailCallbackAdmin callback_id=1}">emailCallbackAdmin</a>
                                <a class="design_tab focus" href="{url debug=emailFeedbackAdmin feedback_id=1}">emailFeedbackAdmin</a>
                                <a class="design_tab focus" href="{url debug=emailPasswordRecoveryAdmin}">emailPasswordRecoveryAdmin</a>
                            </div>
                        </div>
                    </div>
                    <div id="tab2" class="tab">
                        <div class="design_tabs">
                            <div class="design_container">
                                <a class="design_tab focus" href="{url debug=emailOrderUser order_id=1}">emailOrderUser</a>
                                <a class="design_tab focus" href="{url debug=emailCommentAnswerToUser}">emailCommentAnswerToUser</a>
                                <a class="design_tab focus" href="{url debug=emailFeedbackAnswerFoUser}">emailFeedbackAnswerFoUser</a>
                                <a class="design_tab focus" href="{url debug=emailPasswordRemind user_id=1}">emailPasswordRemind</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
