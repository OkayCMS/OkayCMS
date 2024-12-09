<div id="features_issues" style="width: 80vw;">
    <div class="fn_preloader"></div>

    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="wrap_heading">
                <div class="box_heading heading_page">
                    {$btr->issues_with_features_or_feature_values_have_been_detected}
                </div>
            </div>
        </div>
    </div>

    <div class="alert">
        <div class="alert__content">
            <p>{$btr->duplicates_feature_values_found_in_the_translit_field}.</p>
        </div>
    </div>

    <div class="okay_list_feature_issues_container">
        <div class="okay_list products_list okay_list_feature_issues_table fn_sort_list">
            <div class="okay_list_head">
                <div class="okay_list_heading okay_list_feature_issues_id_first">
                    {$btr->general_resolve_issue_feature_id|escape}
                </div>
                <div class="okay_list_heading okay_list_feature_issues_id">
                    {$btr->general_resolve_issue_feature_value_id|escape}
                </div>
                <div class="okay_list_heading okay_list_feature_issues_lang">
                    {$btr->general_resolve_issue_lang|escape} (id)
                </div>
                <div class="okay_list_heading okay_list_feature_issues_name">
                    {$btr->general_resolve_issue_feature_name|escape}
                </div>
                <div class="okay_list_heading okay_list_feature_issues_value">
                    {$btr->general_resolve_issue_value|escape}
                </div>
                <div class="okay_list_heading okay_list_feature_issues_translit">
                    {$btr->general_resolve_issue_translit|escape}
                </div>
            </div>

            <div class="okay_list_body">
                {$prevFvd = null}
                {$changeFvdBackground = true}
                {foreach $feature_values_duplicates as $fvd}
                    {if !$prevFvd || $prevFvd->feature_id != $fvd->feature_id || $prevFvd->lang_id != $fvd->lang_id || ($prevFvd->feature_id == $fvd->feature_id && $prevFvd->translit != $fvd->translit)}
                        {$changeFvdBackground = !$changeFvdBackground}
                    {/if}

                    <div class="okay_list_body_item feature_values_duplicate_item{if $changeFvdBackground} feature_values_duplicate_item_match{/if}">
                        <div class="okay_list_row feature_values_duplicate_row">
                            <div class="okay_list_boding okay_list_feature_issues_id_first">
                                {$fvd->feature_id}
                            </div>
                            <div class="okay_list_boding okay_list_feature_issues_id">
                                {$fvd->feature_value_id}
                            </div>
                            <div class="okay_list_boding okay_list_feature_issues_lang">
                                {$fvd->lang} ({$fvd->lang_id})
                            </div>
                            <div class="okay_list_boding okay_list_feature_issues_name">
                                {$fvd->feature_name}
                            </div>
                            <div class="okay_list_boding okay_list_feature_issues_value">
                                {$fvd->value}
                            </div>
                            <div class="okay_list_boding okay_list_feature_issues_translit">
                                {$fvd->translit}
                            </div>
                        </div>
                    </div>
                    {$prevFvd = $fvd}
                {/foreach}
            </div>

            <div class="okay_list_footer">
                <div class="okay_list_foot_left">
                    <button type="button" data-fancybox-close class="ml-1 btn btn_small btn-return">
                        <span>{$btr->general_cancel|escape}</span>
                    </button>
                    <button type="button" class="btn btn_small btn_blue" onclick="resolveFeatureValuesDuplicate()">
                        {include file='svg_icon.tpl' svgId='checked'}
                        <span>{$btr->general_resolve_issue_automatically|escape}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
{literal}
<script>
    function resolveFeatureValuesDuplicate() {
        const sessionId = $('input[type="hidden"][name="session_id"]').first().val();
        $(".fn_preloader").addClass("ajax_preloader");
        $.ajax( {
            url: {/literal}"{url controller='FeatureAdmin@resolveFeatureValuesIssues'}"{literal},
            data: {
                type:'duplicate',
                session_id: sessionId
            },
            method : 'post',
            dataType: 'json',
            success: function(data) {
                if (data.hasOwnProperty('success') && data.success) {
                    toastr.success('', "{/literal}{$btr->toastr_success|escape}{literal}");
                    loadFeatureIssues();
                } else {
                    toastr.error('', "{/literal}{$btr->toastr_error|escape}{literal}");
                }
                $.fancybox.close();
                $(".fn_preloader").removeClass("ajax_preloader");
                $("#features_issues").remove();
            },
            error: function (error) {
                toastr.error('', "{/literal}{$btr->toastr_error|escape}{literal}");
                $.fancybox.close();
                $(".fn_preloader").removeClass("ajax_preloader");
                $("#features_issues").remove();
            }
        })
    }
</script>
{/literal}
