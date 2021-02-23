{$meta_title = $btr->feature_feature_aliases scope=global}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="heading_page">{$btr->feature_feature_aliases|escape}</div>
    </div>
</div>

{*Вывод успешных сообщений*}
{if $message_success}
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="alert alert--center alert--icon alert--success">
                <div class="alert__content">
                    <div class="alert__title">
                        {if $message_success == 'saved'}
                            {$btr->general_settings_saved|escape}
                        {/if}
                    </div>
                </div>
                {if $smarty.get.return}
                <a class="alert__button" href="{$smarty.get.return}">
                    {include file='svg_icon.tpl' svgId='return'}
                    <span>{$btr->general_back|escape}</span>
                </a>
                {/if}
            </div>
        </div>
    </div>
{/if}

{*Главная форма страницы*}
<form method="post" enctype="multipart/form-data">
    <input type=hidden name="session_id" value="{$smarty.session.id}">
    <input type="hidden" name="lang_id" value="{$lang_id}" />
    <input type="hidden" name="ajax" value="1" />
    <input type="hidden" name="action" value="set" />

    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="boxed fn_toggle_wrap ">
                <div class="row">
                    {*Параметры элемента*}
                    <div class="col-lg-4 col-md-12">
                        <div class="heading_box">{$btr->feature_feature_aliases|escape}</div>
                        <div class="">
                            <div class="fn_preloader"></div>
                            <div>
                                <div class="seo_cateogories_wrap scrollbar-inner">
                                    {if $features}
                                        {foreach $features as $feature}
                                            <div class="seo_item fn_get_feature" data-feature_id="{$feature->id}">{$feature->name|escape}</div>
                                        {/foreach}
                                    {/if}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8 col-md-12">
                        <div class="fn_aliases_result_ajax clearfix"></div>
                        <div class="fn_row okay okay_list_body_item fn_sort_item fn_new_feature_alias" style="display: none;">
                            <div class="okay_list_row">
                                <input type="hidden" class="fn_feature_alias_id" name="features_aliases[id][]" value="">
                                <div class="okay_list_boding okay_list_drag move_zone">
                                    {include file='svg_icon.tpl' svgId='drag_vertical'}
                                </div>
                                <div class="okay_list_boding feature_alias_name">
                                    <input type="text" class="form-control fn_feature_alias_name" name="features_aliases[name][]" value="">
                                </div>
                                <div class="okay_list_boding feature_alias_variable">
                                    <input type="text" class="form-control fn_feature_alias_variable" name="" value="" readonly="">
                                </div>
                                <div class="okay_list_boding feature_alias_value">
                                    <input type="text" class="form-control" name="feature_aliases_value[value][]" value="">
                                    <input type="hidden" name="feature_aliases_value[id][]" value="">
                                </div>
                                <div class="okay_list_boding okay_list_close">
                                    <button data-hint="{$btr->feature_delete_alias|escape}" type="button" class="btn_close fn_remove_item hint-bottom-right-t-info-s-small-mobile  hint-anim">
                                        {include file='svg_icon.tpl' svgId='delete'}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12 mt-1">
                        <div class="fn_aliases_values_result_ajax clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

{literal}
<script>
    $(function() {

        toastr.options = {
            closeButton: true,
            newestOnTop: true,
            progressBar: true,
            positionClass: 'toast-top-right',
            preventDuplicates: false,
            onclick: null
        };
        msg = '';

        $(document).on('click', '.fn_remove_item', function(){
            $(this).closest('.fn_row').fadeOut(200, function() {
                $(this).remove();
            });
        });
        $(document).on('change', '.fn_feature_alias_name', function(){
            var elem = $(this),
                variable_elem = elem.closest('.fn_row').find('.fn_feature_alias_variable'),
                id = elem.closest('.fn_row').find('.fn_feature_alias_id').val(),
                name = elem.val(),
                variable = translit(name);

            if (name != '' && !id) {
                variable = variable.replace(/[^a-z0-9]/gim, '').toLowerCase();
                variable_elem.val('{$f_alias_'+variable+'}');
            }
        });

        var feature_alias = $(".fn_new_feature_alias").clone(false);
        $(".fn_new_feature_alias").remove();
        $(document).on("click", ".fn_add_feature_alias", function () {
            var feature_alias_clone = feature_alias.clone(true);
            feature_alias_clone.show();
            $(".fn_feature_aliases_list").append(feature_alias_clone);
            return false;
        });

        $(document).on("click", ".fn_save_aliases", function () {
            var action = "set",
                link = window.location.href;

            $('input[name="action"]').val(action);

            $.ajax({
                url: link,
                method : 'post',
                data: $(this).closest('form').serialize(),
                dataType: 'json',
                success: function(data){
                    if(data.success) {
                        $(".fn_aliases_result_ajax").html(data.feature_aliases_tpl);
                        $(".fn_aliases_values_result_ajax").html(data.feature_aliases_values_tpl);
                        set_sortable();
                        toastr.success(msg, "{/literal}{$btr->toastr_success|escape}{literal}");
                        $(".fn_preloader ").removeClass("ajax_preloader ");
                    } else {
                        toastr.error(msg, "{/literal}{$btr->toastr_error|escape}{literal}");
                        $(".fn_preloader ").removeClass("ajax_preloader ");
                    }
                }
            });
            return false;
        });

        $(document).on("click", ".fn_get_feature", function () {
            $(".fn_preloader ").addClass("ajax_preloader");
            $(".fn_get_feature").removeClass("active");
            var elem = $(this),
                feature_id = parseInt(elem.data("feature_id")) ? parseInt(elem.data("feature_id")) : null,
                action = "get",
                link = window.location.href,
                session_id = '{/literal}{$smarty.session.id}{literal}';

            $.ajax({
                url: link,
                method : 'post',
                data: {
                    ajax: 1,
                    session_id: session_id,
                    feature_id: feature_id,
                    action : action,
                },
                dataType: 'json',
                success: function(data){
                    if(data.success) {
                        $(".fn_aliases_result_ajax").html(data.feature_aliases_tpl);
                        $(".fn_aliases_values_result_ajax").html(data.feature_aliases_values_tpl);
                        set_sortable();
                        toastr.success(msg, "{/literal}{$btr->toastr_success|escape}{literal}");
                        elem.addClass("active");
                        $(".fn_preloader ").removeClass("ajax_preloader");
                    } else {
                        toastr.error(msg, "{/literal}{$btr->toastr_error|escape}{literal}");
                        $(".fn_preloader ").removeClass("ajax_preloader");
                    }
                }
            });
        });

        function set_sortable() {
            $(".sortable").each(function() {
                Sortable.create(this, {
                    handle: ".move_zone",  // Drag handle selector within list items
                    sort: true,  // sorting inside list
                    animation: 150,  // ms, animation speed moving items when sorting, `0` — without animation

                    ghostClass: "sortable-ghost",  // Class name for the drop placeholder
                    chosenClass: "sortable-chosen",  // Class name for the chosen item
                    dragClass: "sortable-drag",  // Class name for the dragging item
                    scrollSensitivity: 30, // px, how near the mouse must be to an edge to start scrolling.
                    scrollSpeed: 10, // px
                });
            });
        }
    });
</script>
{/literal}
