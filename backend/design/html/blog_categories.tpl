{* Title *}
{$meta_title=$btr->general_categories scope=global}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {$btr->general_categories|escape} - {$categoriesCount}
            </div>
            <div class="box_btn_heading">
                <a class="fn_learning_create_category btn btn_small btn-info" href="{url controller=BlogCategoryAdmin return=$smarty.server.REQUEST_URI}">
                    {include file='svg_icon.tpl' svgId='plus'}
                    <span>{$btr->categories_add|escape}</span>
                </a>
            </div>
        </div>
    </div>
</div>

{*Главная форма страницы*}
<div class="boxed fn_toggle_wrap">
    {$block = {get_design_block block="blog_categories_custom_block"}}
    {if !empty($block)}
        <div class="fn_toggle_wrap custom_block">
            {$block}
        </div>
    {/if}
    {if $categories}
        <form method="post" class="fn_form_list fn_fast_button">
            <input type="hidden" name="session_id" value="{$smarty.session.id}" />
            <div class="okay_list products_list fn_sort_list">
                {*Шапка таблицы*}
                <div class="okay_list_head">
                    <div class="okay_list_heading okay_list_subicon">
                        <a href="javascript:;" class="fn_open_all">
                            <i class="fa fa-plus-square"></i>
                        </a>
                    </div>
                    <div class="okay_list_heading okay_list_drag"></div>
                    <div class="okay_list_heading okay_list_check">
                        <input class="hidden_check fn_check_all" type="checkbox" id="check_all_1" name="" value=""/>
                        <label class="okay_ckeckbox" for="check_all_1"></label>
                    </div>
                    <div class="okay_list_heading okay_list_photo hidden-sm-down">{$btr->general_photo|escape}</div>
                    <div class="okay_list_heading okay_list_categories_name">{$btr->general_name|escape}</div>
                    <div class="okay_list_heading okay_list_status">{$btr->general_enable|escape}</div>
                    <div class="okay_list_heading okay_list_setting">{$btr->general_activities|escape}</div>
                    <div class="okay_list_heading okay_list_close"></div>
                </div>

                {*Параметры элемента*}
                <div class="okay_list_body categories_wrap sortable fn_categories_block">
                    {include file="blog_categories_ajax.tpl" level=1}
                </div>

                {*Блок массовых действий*}
                <div class="okay_list_footer fn_action_block">
                    <div class="okay_list_foot_left">
                        <div class="okay_list_heading okay_list_subicon"></div>
                        <div class="okay_list_heading okay_list_drag"></div>
                        <div class="okay_list_heading okay_list_check">
                            <input class="hidden_check fn_check_all" type="checkbox" id="check_all_2" name="" value=""/>
                            <label class="okay_ckeckbox" for="check_all_2"></label>
                        </div>
                        <div class="okay_list_option">
                            <select name="action" class="selectpicker form-control">
                                <option value="enable">{$btr->general_do_enable|escape}</option>
                                <option value="disable">{$btr->general_do_disable|escape}</option>
                                <option value="delete">{$btr->general_delete|escape}</option>
                                <option value="duplicate">{$btr->general_create_dublicate|escape}</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn_small btn_blue">
                        {include file='svg_icon.tpl' svgId='checked'}
                        <span>{$btr->general_apply|escape}</span>
                    </button>
                </div>
            </div>
        </form>
    {else}
        <div class="heading_box mt-1">
            <div class="text_grey">{$btr->categories_no|escape}</div>
        </div>
    {/if}
</div>

<script>
    $(document).on("click", ".fn_open_all", function () {
        let that = $(this);
        $.ajax({
            dataType: 'json',
            url: "{url controller='BlogCategoriesAdmin@getAllCategories'}",
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
                
                {if $config->dev_mode}
                $('.fn_backend_block_name').parent().addClass('backend_block_parent_element');
                $('.fn_backend_block_name').on('mouseover', function () {
                    $(this).parent().addClass('focus');
                });
                $('.fn_backend_block_name').on('mouseout', function () {
                    $(this).parent().removeClass('focus');
                });
                {/if}
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
                url: "{url controller='BlogCategoriesAdmin@getSubCategories'}",
                data: {
                    category_id: category_id
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

                    {if $config->dev_mode}
                        $('.fn_backend_block_name').parent().addClass('backend_block_parent_element');
                        $('.fn_backend_block_name').on('mouseover', function () {
                            $(this).parent().addClass('focus');
                        });
                        $('.fn_backend_block_name').on('mouseout', function () {
                            $(this).parent().removeClass('focus');
                        });
                    {/if}

                }
            });
        } else {
            elem.closest(".fn_row").children(".fn_ajax_categories").slideToggle(500);
            elem.find("i").toggleClass("fa-minus-square");
        }
    });

    // Дублировать товар
    $(document).on("click", ".fn_copy", function () {
        $('.fn_form_list input[type="checkbox"][name*="check"]').attr('checked', false);
        $(this).closest(".fn_form_list").find('select[name="action"] option[value=duplicate]').attr('selected', true);
        $(this).closest(".okay_list_row").find('input[type="checkbox"][name*="check"]').attr('checked', true).click();
        $(this).closest(".fn_form_list").submit();
    });

</script>
