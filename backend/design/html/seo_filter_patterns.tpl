{$meta_title = $btr->seo_filter_patterns_auto scope=global}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="heading_page">{$btr->seo_filter_patterns_auto|escape}</div>
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

                        {$btr->general_settings_saved|escape}
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
    <input type="hidden" name="category_id" value="" />
    <input type="hidden" name="template_type" value="" />
    <input type="hidden" name="action" value="set" />

    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="boxed fn_toggle_wrap ">
                <div class="row">
                    {*Параметры элемента*}
                    <div class="col-lg-5 col-md-12">
                        <div class="heading_box">{$btr->seo_patterns_cat_name|escape}</div>
                        <div class="">
                            <div class="fn_preloader"></div>
                            <div>
                                <div class="seo_cateogories_wrap scrollbar-inner">
                                    <div class="seo_item fn_get_category" data-template_type="default" data-category_id="0">{$btr->seo_patterns_all_categories|escape}</div>
                                    {if $categories}
                                        {function name=category_seo}
                                            {foreach $cats as $cat}
                                                <div class="seo_item fn_get_category" data-template_type="category" data-category_id="{$cat->id}" style="padding-left: {$level*10}px" >{$cat->name|escape}</div>
                                                {category_seo cats=$cat->subcategories level=$level+1}
                                            {/foreach}
                                        {/function}
                                        {category_seo cats=$categories level=1}
                                    {/if}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7 col-md-12">
                        <div class="fn_result_ajax clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="fn_new_template hidden fn_template_block">
    <div class="boxed">
    <div class="row">
        <div class="col-md-6">
            <div class="heading_box fn_heading_box"></div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-12">
                    <div class="heading_label">H1</div>
                    <div class="mb-1">
                        <input name="seo_filter_patterns[h1][]" class="fn_auto_meta_h1 form-control mb-h fn_ajax_area" value="" />
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="heading_label">Auto Meta-description</div>
                    <div class="mb-1">
                        <input name="seo_filter_patterns[meta_description][]" class="fn_auto_meta_desc form-control fn_ajax_area" value="" />
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-12">
                    <div class="heading_label">Auto Meta-title</div>
                    <div class="mb-1">
                        <input name="seo_filter_patterns[meta_title][]" class="fn_auto_meta_title form-control mb-h fn_ajax_area" value="" />
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="heading_label">Auto Meta-keywords</div>
                    <div class="mb-1">
                        <input name="seo_filter_patterns[meta_keywords][]" class="fn_auto_meta_keywords form-control fn_ajax_area" value="" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="heading_label">{$btr->seo_filter_patterns_ajax_description|escape}</div>
            <div class="mb-1">
                <textarea name="seo_filter_patterns[description][]" class="fn_auto_description form-control okay_textarea fn_ajax_area"></textarea>
            </div>
        </div>
    </div>
     <div class="row">
        <div class="col-md-12">
            <div class="pull-right">
                <button type="button" class="fn_delete_template btn btn_mini btn-danger float-md-right" >
                    {include file='svg_icon.tpl' svgId='delete'}
                    <span>{$btr->seo_filter_patterns_delete_template|escape}</span>
                </button>
            </div>
        </div>
     </div>
    <input name="seo_filter_patterns[type][]" class="fn_pattern_type form-control" value="" type="hidden" />
    <input name="seo_filter_patterns[feature_id][]" class="fn_feature_id form-control" value="" type="hidden" />
    <input name="seo_filter_patterns[second_feature_id][]" class="fn_second_feature_id form-control" value="" type="hidden" />
    </div>
</div>

{* Подключаем Tiny MCE *}
{include file='tinymce_init.tpl'}
{* On document load *}

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

        var new_template = $('.fn_new_template').clone();
        $('.fn_new_template').remove();

        var new_templates_counter = 0;

        $(document).on("click", ".fn_delete_template", function () {
            $(this).closest('.fn_template_block').fadeOut(200, function() {
                $(this).remove();
            });
        });

        $(document).on("click", ".fn_copy_seo_templates", function () {
            var category_from_copy_id = $('select[name="category_from_copy_id"]').val();
            if (typeof category_from_copy_id === 'undefined' || category_from_copy_id == false) {
                toastr.error(msg, "{/literal}{$btr->toastr_error|escape}{literal}");
            } else {
                $(".fn_preloader ").addClass("ajax_preloader ");
                var elem = $("div.seo_item.fn_get_category.active");

                if (typeof elem === 'undefined') {
                    toastr.error(msg, "{/literal}{$btr->toastr_error|escape}{literal}");
                    return false;
                }
                var category_to_copy_id = parseInt(elem.data("category_id")) ? parseInt(elem.data("category_id")) : null,
                    template_type = elem.data("template_type"),
                    action = "copy_patterns_from_category",
                    link = window.location.href,
                    session_id = '{/literal}{$smarty.session.id}{literal}';

                $.ajax({
                    url: link,
                    method : 'post',
                     data: {
                    ajax: 1,
                    session_id: session_id,
                    category_from_copy_id: category_from_copy_id,
                    category_to_copy_id: category_to_copy_id,
                    template_type: template_type,
                    action : action,
                },
                    dataType: 'json',
                    success: function(data){
                        if(data.success) {
                            $(".fn_preloader ").removeClass("ajax_preloader ");
                            elem.trigger('click');
                        } else {
                            toastr.error(msg, "{/literal}{$btr->toastr_error|escape}{literal}");
                            $(".fn_preloader ").removeClass("ajax_preloader ");
                        }
                    }
                });
                return false;
            }
        });

        $(document).on("click", ".fn_add_seo_template", function () {
            var template = new_template.clone(),
                pattern_type_elem = $('.fn_pattern_type'),
                pattern_type_class = pattern_type = pattern_type_elem.children(':selected').val(),
                feature_elem = $('.fn_features'),
                second_feature_elem = $('.fn_features_second'),
                feature_id   = feature_elem.children(':selected').val(),
                second_feature_id   = second_feature_elem.children(':selected').val();

            if ((pattern_type == 'feature' || pattern_type == 'brand_feature') && feature_id) {
                pattern_type_class += '_'+feature_id;
            }

            if (pattern_type == 'feature_feature') {
                if (feature_id && second_feature_id && feature_id == second_feature_id)  {
                    toastr.error(msg, "Template double feature");
                    return false;
                }
                if (!feature_id && second_feature_id) {
                    toastr.error(msg, "Selected feature must be always first");
                    return false;
                }

                let pattern_type_another_check = pattern_type_class;

                if (feature_id) {
                    pattern_type_class += '_'+feature_id;
                }

                if (second_feature_id) {
                    pattern_type_class += '_'+second_feature_id;
                }

                if (second_feature_id && feature_id) {
                    pattern_type_another_check += '_'+second_feature_id+'_'+feature_id;
                    if ($('.fn_'+pattern_type_another_check).size() > 0) {
                        toastr.error(msg, "Template already exists");
                        return false;
                    }
                }
            }


            if ($('.fn_'+pattern_type_class).size() > 0) {
                toastr.error(msg, "Template already exists");
            } else {
                template.addClass('fn_'+pattern_type_class);

                if (pattern_type == 'feature_feature') {
                    template.find('.fn_heading_box').text(
                        '{/literal}{$btr->seo_filter_patterns_category}{literal} '
                        +'+{/literal}{$btr->seo_filter_patterns_feature}{literal}'
                        +(feature_id ? ' ('+feature_elem.children(':selected').text()+')' : '')
                        +' +{/literal}{$btr->seo_filter_patterns_feature}{literal}'
                        +(second_feature_id ? ' ('+second_feature_elem.children(':selected').text()+')' : '')
                    );
                } else {
                    template.find('.fn_heading_box').text(
                        '{/literal}{$btr->seo_filter_patterns_category}{literal} '
                        +pattern_type_elem.children(':selected').text()
                        +(feature_id ? ' ('+feature_elem.children(':selected').text()+')' : '')
                    );
                }

                template.find('.fn_pattern_type').val(pattern_type);
                if (feature_id) {
                    template.find('.fn_feature_id').val(feature_id);
                }
                if (second_feature_id) {
                    template.find('.fn_second_feature_id').val(second_feature_id);
                }

                template.removeClass('hidden');
                new_templates_counter++;
                $('.fn_templates').append(template);

                setTimeout(function () {
                setChanges();
                }, 100);

                function setChanges() {
                    $('.fn_'+pattern_type_class).each(function () {
                        $('html, body').animate({
                            scrollTop: $('.fn_'+pattern_type_class).offset().top - 70
                        }, 2000);
                    });
                }
            }
        });

       function do_ajax_get_features(link, session_id, category_id, template_type, action, second_features=false){
            $.ajax({
                url: link,
                method : 'post',
                data: {
                    ajax: 1,
                    session_id: session_id,
                    category_id: category_id,
                    template_type: template_type,
                    action : action,
                },
                dataType: 'json',
                success: function(data){
                    if(data.success && data.features) {
                        var features_html = '<option value="">{/literal}{$btr->seo_filter_patterns_all_features|escape}{literal}</option>';
                        for(var i=0; i<data.features.length; i++) {
                            var feature = data.features[i];
                            features_html += '<option value="'+feature.id+'">'+feature.name+'</option>';
                        }
                        $('.fn_features').html(features_html).prop('disabled', false).removeClass('hidden');
                        if (second_features) {
                            $('.fn_features_second').html(features_html).prop('disabled', false).removeClass('hidden');
                        }
                    }
                }
            });
        }

        $(document).on("change", ".fn_pattern_type", function () {
            var elem = $(this),
                category_elem = $('.fn_get_category.active'),
                pattern_type = elem.children(':selected').val(),
                category_id = parseInt(category_elem.data("category_id")) ? parseInt(category_elem.data("category_id")) : null,
                template_type = category_elem.data("template_type"),
                action = "get_features",
                link = window.location.href,
                session_id = '{/literal}{$smarty.session.id}{literal}';

            if (pattern_type == 'brand') {
                $('.fn_features').prop('disabled', true).addClass('hidden').children(':first').prop('selected', true);
                $('.fn_features_second').prop('disabled', true).addClass('hidden').children(':first').prop('selected', true);
            } else if (pattern_type == 'brand_feature') {
                $('.fn_features').prop('disabled', true).addClass('hidden').children(':first').prop('selected', true);
                $('.fn_features_second').prop('disabled', true).addClass('hidden').children(':first').prop('selected', true);
                do_ajax_get_features(link, session_id, category_id, template_type, action);
            } else if (pattern_type == 'feature') {
                $('.fn_features_second').prop('disabled', true).addClass('hidden').children(':first').prop('selected', true);
                do_ajax_get_features(link, session_id, category_id, template_type, action);
            } else if (pattern_type == 'feature_feature') {
                do_ajax_get_features(link, session_id, category_id, template_type, action, true);
            }
        });

        $(document).on("click", ".fn_get_category", function () {
            $(".fn_preloader ").addClass("ajax_preloader");
            $(".fn_get_category").removeClass("active");
            var elem = $(this),
                category_id = parseInt(elem.data("category_id")) ? parseInt(elem.data("category_id")) : null,
                template_type = elem.data("template_type"),
                action = "get",
                link = window.location.href,
                session_id = '{/literal}{$smarty.session.id}{literal}';


            $.ajax({
                url: link,
                method : 'post',
                data: {
                    ajax: 1,
                    session_id: session_id,
                    category_id: category_id,
                    template_type: template_type,
                    action : action,
                },
                dataType: 'json',
                success: function(data){
                    if(data.success) {
                        $(".fn_result_ajax").html(data.tpl);
                        toastr.success(msg, "{/literal}{$btr->toastr_success|escape}{literal}");
                        elem.addClass("active");
                        $(".fn_preloader ").removeClass("ajax_preloader");

                        sclipboard();
                        $('.selectpicker_for_copy').selectpicker();
                    } else {
                        toastr.error(msg, "{/literal}{$btr->toastr_error|escape}{literal}");
                        $(".fn_preloader ").removeClass("ajax_preloader");
                    }
                }
            });
        });

        $(document).on("click", ".fn_update_category", function () {
            $(".fn_preloader ").addClass("ajax_preloader ");
            var elem = $(this),
                category_id = parseInt(elem.data("category_id")) ? parseInt(elem.data("category_id")) : null,
                template_type = elem.data("template_type"),
                action = "set",
                link = window.location.href,
                session_id = '{/literal}{$smarty.session.id}{literal}';

            $('input[name="category_id"]').val(category_id);
            $('input[name="template_type"]').val(template_type);
            $('input[name="action"]').val(action);

            $.ajax({
                url: link,
                method : 'post',
                data: $(this).closest('form').serialize(),
                dataType: 'json',
                success: function(data){
                    if(data.success) {
                        $(".fn_result_ajax").html(data.tpl);
                        toastr.success(msg, "{/literal}{$btr->toastr_success|escape}{literal}");
                        $(".fn_preloader ").removeClass("ajax_preloader ");
                        sclipboard();
                        $('.selectpicker_for_copy').selectpicker();
                    } else {
                        toastr.error(msg, "{/literal}{$btr->toastr_error|escape}{literal}");
                        $(".fn_preloader ").removeClass("ajax_preloader ");
                    }
                }
            });
           return false;
        });
    });
</script>
{/literal}
