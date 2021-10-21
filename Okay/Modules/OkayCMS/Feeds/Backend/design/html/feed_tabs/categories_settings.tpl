{if $feed}
    <div class="alert alert--icon alert--info fn_features_settings_alert">
        <div class="alert__content">
            <div class="alert__title">{$btr->alert_info}</div>
            <p>{$btr->okay_cms__feeds__feed__categories_settings__documentation}</p>
        </div>
    </div>
    <div class="boxed fn_categories_settings hidden-xs-down">
        <div class="heading_box">
            {$btr->okay_cms__feeds__feed__categories_settings__title|escape}
            <i class="fn_tooltips" title="{$btr->okay_cms__feeds__feed__categories_settings__faq|escape}">
                <svg width="20px" height="20px" viewBox="0 0 438.533 438.533"><path fill="currentColor" d="M409.133,109.203c-19.608-33.592-46.205-60.189-79.798-79.796C295.736,9.801,259.058,0,219.273,0c-39.781,0-76.47,9.801-110.063,29.407c-33.595,19.604-60.192,46.201-79.8,79.796C9.801,142.8,0,179.489,0,219.267c0,39.78,9.804,76.463,29.407,110.062c19.607,33.592,46.204,60.189,79.799,79.798c33.597,19.605,70.283,29.407,110.063,29.407s76.47-9.802,110.065-29.407c33.593-19.602,60.189-46.206,79.795-79.798c19.603-33.596,29.403-70.284,29.403-110.062C438.533,179.485,428.732,142.795,409.133,109.203z M255.82,356.309c0,2.662-0.862,4.853-2.573,6.563c-1.704,1.711-3.895,2.567-6.557,2.567h-54.823c-2.664,0-4.854-0.856-6.567-2.567c-1.714-1.711-2.57-3.901-2.57-6.563v-54.823c0-2.662,0.855-4.853,2.57-6.563c1.713-1.708,3.903-2.563,6.567-2.563h54.823c2.662,0,4.853,0.855,6.557,2.563c1.711,1.711,2.573,3.901,2.573,6.563V356.309z M325.338,187.574c-2.382,7.043-5.044,12.804-7.994,17.275c-2.949,4.473-7.187,9.042-12.709,13.703c-5.51,4.663-9.891,7.996-13.135,9.998c-3.23,1.995-7.898,4.713-13.982,8.135c-6.283,3.613-11.465,8.326-15.555,14.134c-4.093,5.804-6.139,10.513-6.139,14.126c0,2.67-0.862,4.859-2.574,6.571c-1.707,1.711-3.897,2.566-6.56,2.566h-54.82c-2.664,0-4.854-0.855-6.567-2.566c-1.715-1.712-2.568-3.901-2.568-6.571v-10.279c0-12.752,4.993-24.701,14.987-35.832c9.994-11.136,20.986-19.368,32.979-24.698c9.13-4.186,15.604-8.47,19.41-12.847c3.812-4.377,5.715-10.188,5.715-17.417c0-6.283-3.572-11.897-10.711-16.849c-7.139-4.947-15.27-7.421-24.409-7.421c-9.9,0-18.082,2.285-24.555,6.855c-6.283,4.565-14.465,13.322-24.554,26.263c-1.713,2.286-4.093,3.431-7.139,3.431c-2.284,0-4.093-0.57-5.424-1.709L121.35,145.89c-4.377-3.427-5.138-7.422-2.286-11.991c24.366-40.542,59.672-60.813,105.922-60.813c16.563,0,32.744,3.903,48.541,11.708c15.796,7.801,28.979,18.842,39.546,33.119c10.554,14.272,15.845,29.787,15.845,46.537C328.904,172.824,327.71,180.529,325.338,187.574z"></path></svg>                    
            </i>
        </div>
        <div class="okay_list products_list fn_sort_list">
            {*Шапка таблицы*}
            <div class="okay_list_head">
                <div class="okay_list_heading okay_list_subicon">
                    <a href="javascript:;" class="fn_open_all">
                        <i class="fa fa-plus-square"></i>
                    </a>
                </div>
                <div class="okay_list_heading okay_list_photo hidden-sm-down">{$btr->general_photo|escape}</div>
                <div class="okay_list_heading okay_list_feed_categories_settings_name">{$btr->general_name|escape}</div>
                <div class="okay_list_heading okay_list_feed_categories_settings_settings">{$btr->okay_cms__feeds__feed__categories_settings__table_settings}</div>
            </div>

            {*Параметры элемента*}
            <div class="okay_list_body categories_wrap sortable fn_categories_block">
                {include file="./categories_ajax.tpl" level=1}
            </div>
        </div>
    </div>
{/if}

<div class="alert alert--icon alert--warning fn_categories_settings_alert {if $feed}hidden{/if}">
    <div class="alert__content">
        <div class="alert__title">{$btr->alert_warning}</div>
        <p>{$btr->okay_cms__feeds__feed__categories_settings__save_notify}</p>
    </div>
</div>

<div class="alert alert--icon alert--warning hidden-sm-up">
    <div class="alert__content">
        <div class="alert__title">{$btr->okay_cms__feeds__feed__categories_settings__not_mobile}</div>
    </div>
</div>

{literal}
    <script>
        $(function() {
            $(document).on('change', '.fn_category_settings input, .fn_category_settings select', function() {
                let rowForm = $(this).closest('.fn_category_settings');
                let formData = new FormData();
                rowForm.find('input').each(function(i, el) {
                    formData.append($(el).attr('name'), $(el).val());
                });
                formData.append('preset', $('select.fn_preset_select').val());
                formData.append('feed_id', {/literal}{$smarty.get.id}{literal});
                formData.append('entity', 'category');
                formData.append('session_id', '{/literal}{$smarty.session.id}{literal}');

                $.ajax({
                    url: '{/literal}{url controller='OkayCMS.Feeds.FeedAdmin@updateEntitySettings'}{literal}',
                    type: 'POST',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false
                }).done(function(response){
                    if (response.hasOwnProperty('success') && response.success) {
                        toastr.success('', "{/literal}{$btr->toastr_success|escape}{literal}");
                    } else {
                        toastr.error('', "{/literal}{$btr->toastr_error|escape}{literal}");
                    }
                });
            });

            $(document).on("click", ".fn_open_all", function () {
                let that = $(this);
                $.ajax({
                    dataType: 'json',
                    url: "{/literal}{url controller='OkayCMS.Feeds.FeedAdmin@getAllCategories'}{literal}",
                    data: {
                        feed_id: {/literal}{$smarty.get.id}{literal}
                    },
                    success: function(data){
                        var msg = "";

                        if(data.success){
                            $(".fn_categories_block").html(data.cats);
                        } else {
                            toastr.error(msg, "Error");
                        }

                        var el = document.querySelectorAll("div.sortable , .fn_ajax_categories.sortable");
                        for (i = 0; i < el.length; i++) {
                            var sortable = Sortable.create(el[i], {
                                handle: ".move_zone",  // Drag handle selector within list items
                                sort: true,  // sorting inside list
                                animation: 150,  // ms, animation speed moving items when sorting, `0` — without animation
                                scroll: true, // or HTMLElement
                                ghostClass: "sortable-ghost",  // Class name for the drop placeholder
                                chosenClass: "sortable-chosen",  // Class name for the chosen item
                                dragClass: "sortable-drag",  // Class name for the dragging item
                                scrollSensitivity: 30, // px, how near the mouse must be to an edge to start scrolling.
                                scrollSpeed: 10, // px
                            });
                        }

                        that.hide();
                        }
                    });
                return false;
            });

            $(document).on("click", ".fn_ajax_toggle", function () {
                let elem = $(this);
                let toggle = parseInt(elem.data("toggle"));
                let category_id = parseInt(elem.data("category_id"));
                if(toggle == 0){
                    $.ajax({
                        dataType: 'json',
                        url: "{/literal}{url controller='OkayCMS.Feeds.FeedAdmin@getSubCategories'}{literal}",
                        data: {
                            category_id: category_id,
                            feed_id: {/literal}{$smarty.get.id}{literal}
                        },
                        success: function(data){
                            var msg = "";

                            if(data.success){
                                elem.closest(".fn_row").find(".fn_ajax_categories").html(data.cats);
                                elem.closest(".fn_row").find(".fn_ajax_categories").addClass("sortable");
                                elem.data("toggle",1);
                                elem.find("i").toggleClass("fa-minus-square");
                            } else {
                                toastr.error(msg, "Error");
                            }

                            var el = document.querySelectorAll("div.sortable , .fn_ajax_categories.sortable");
                            for (i = 0; i < el.length; i++) {
                                var sortable = Sortable.create(el[i], {
                                    handle: ".move_zone",  // Drag handle selector within list items
                                    sort: true,  // sorting inside list
                                    animation: 150,  // ms, animation speed moving items when sorting, `0` — without animation
                                    scroll: true, // or HTMLElement
                                    ghostClass: "sortable-ghost",  // Class name for the drop placeholder
                                    chosenClass: "sortable-chosen",  // Class name for the chosen item
                                    dragClass: "sortable-drag",  // Class name for the dragging item
                                    scrollSensitivity: 30, // px, how near the mouse must be to an edge to start scrolling.
                                    scrollSpeed: 10, // px
                                });
                            }
                            }
                        });
                } else {
                    elem.closest(".fn_row").children(".fn_ajax_categories").slideToggle(500);
                    elem.find("i").toggleClass("fa-minus-square");
                }
            });

            $(document).on('change', 'select.fn_preset_select', function() {
                $('.fn_categories_settings_alert').removeClass('hidden');
                $('.fn_categories_settings').addClass('hidden');
            })
        })
    </script>
{/literal}
