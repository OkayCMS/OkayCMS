{if $feed}
    <div class="okay_list products_list fn_sort_list">
        {*Шапка таблицы*}
        <div class="okay_list_head">
            <div class="okay_list_heading okay_list_subicon">
                <a href="javascript:;" class="fn_open_all">
                    <i class="fa fa-plus-square"></i>
                </a>
            </div>
            <div class="okay_list_heading okay_list_drag"></div>
            <div class="okay_list_heading okay_list_photo hidden-sm-down">{$btr->general_photo|escape}</div>
            <div class="okay_list_heading okay_list_feed_categories_settings_name">{$btr->general_name|escape}</div>
            <div class="okay_list_heading okay_list_feed_categories_settings_settings">Настройки</div>
        </div>

        {*Параметры элемента*}
        <div class="okay_list_body categories_wrap sortable fn_categories_block">
            {include file="./categories_ajax.tpl" level=1}
        </div>
    </div>
{else}
    <div class="alert alert--icon alert--warning">
        <div class="alert__content">
            <div class="alert__title">{$btr->alert_warning}</div>
            <p>{$btr->okay_cms__feeds__feed__categories_settings__save_notify}</p>
        </div>
    </div>
{/if}

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
        })
    </script>

    <style>
        .okay_list_feed_categories_settings_name{
            width: calc(100% - 1000px);
            position: relative;
            text-align: left;
        }
        .okay_list .subcategories_level_1 .okay_list_feed_categories_settings_name{
            width: calc(100% - 1000px);
        }
        .okay_list .subcategories_level_2 .okay_list_feed_categories_settings_name{
            width: calc(100% - 1000px);
        }
        .okay_list_feed_categories_settings_settings {
            width: 100%;
        }
    </style>
{/literal}