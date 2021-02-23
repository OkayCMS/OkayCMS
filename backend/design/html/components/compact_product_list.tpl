<script src="design/js/autocomplete/jquery.autocomplete-min.js"></script>
<div class="heading_box">
    {$title|escape}
    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
    </div>
</div>
<div class="toggle_body_wrap on fn_card fn_sort_list">
    <div class="okay_list ok_related_list">
        <div class="okay_list_body {$name}_compact_product_list sortable">
            {foreach $products as $product}
                <div class="fn_row okay okay_list_body_item fn_sort_item">
                    <div class="okay_list_row">
                        <div class="okay_list_boding okay_list_drag move_zone">
                            {include file='svg_icon.tpl' svgId='drag_vertical'}
                        </div>
                        <div class="okay_list_boding okay_list_related_photo">
                            <input type="hidden" name={$name}[] value='{$product->id}'>
                            <a href="{url controller=ProductAdmin id=$product->id}">
                                {if $product->image}
                                    <img class="product_icon" src='{$product->image->filename|resize:40:40}'>
                                {elseif $product->images[0]}
                                    <img class="product_icon" src='{$product->images[0]->filename|resize:40:40}'>
                                {else}
                                    <img class="product_icon" src="design/images/no_image.png" width="40">
                                {/if}
                            </a>
                        </div>
                        <div class="okay_list_boding okay_list_related_name">
                            <a class="link" href="{url controller=ProductAdmin id=$product->id}">{$product->name|escape}</a>
                        </div>
                        <div class="okay_list_boding okay_list_close">
                            <button data-hint="{$btr->general_delete_product|escape}" type="button" class="btn_close fn_remove_item hint-bottom-right-t-info-s-small-mobile  hint-anim">
                                {include file='svg_icon.tpl' svgId='delete'}
                            </button>
                        </div>
                    </div>
                </div>
            {/foreach}


            <div id="{$name}_compact_product_list_item_add" class="fn_row okay okay_list_body_item fn_sort_item" style='display:none;'>
                <div class="okay_list_row">
                    <div class="okay_list_boding okay_list_drag move_zone">
                        {include file='svg_icon.tpl' svgId='drag_vertical'}
                    </div>
                    <div class="okay_list_boding okay_list_related_photo">
                        <input type="hidden" name="{$name}[]" value="">
                        <img class=product_icon src="">
                    </div>
                    <div class="okay_list_boding okay_list_related_name">
                        <a class="link compact_list_product_name" href=""></a>
                    </div>
                    <div class="okay_list_boding okay_list_close">
                        <button data-hint="{$btr->general_delete_product|escape}" type="button" class="btn_close fn_remove_item hint-bottom-right-t-info-s-small-mobile  hint-anim">
                            {include file='svg_icon.tpl' svgId='delete'}
                        </button>
                    </div>
                </div>
            </div>


        </div>
    </div>
    <div class="heading_label">{$label|escape}</div>
    <div class="autocomplete_arrow">
        <input type=text name=related id="{$name}_compact_product_list" class="form-control" placeholder='{$placeholder|escape}'>
    </div>
</div>



<script>

    //> Добавление товара в список
    var {$name}_compact_product_list_item_add = $('#{$name}_compact_product_list_item_add').clone(true);
    $('#{$name}_compact_product_list_item_add').remove();
    {$name}_compact_product_list_item_add.removeAttr('id');
    $("input#{$name}_compact_product_list").devbridgeAutocomplete({
        serviceUrl:'ajax/search_products.php',
        type: 'POST',
        minChars:0,
        orientation:'auto',
        noCache: false,
        params: {
            filter: {json_encode($filter)}
        },
        onSelect:
            function(suggestion){
                $("input#{$name}_compact_product_list").val('').focus().blur();
                new_item = {$name}_compact_product_list_item_add.clone().appendTo('.{$name}_compact_product_list');
                new_item.find('a.compact_list_product_name').html(suggestion.data.name);
                new_item.find('a.compact_list_product_name').attr('href', 'index.php?controller=ProductAdmin&id='+suggestion.data.id);
                new_item.find('input[name*="related_products"]').val(suggestion.data.id);
                if(suggestion.data.image) {
                    new_item.find('img.product_icon').attr("src", suggestion.data.image);
                }
                else {
                    new_item.find('img.product_icon').remove();
                }
                new_item.show();
            },
        formatResult:
            function(suggestions, currentValue){
                var reEscape = new RegExp('(\\' + ['/', '.', '*', '+', '?', '|', '(', ')', '[', ']', '{', '}', '\\'].join('|\\') + ')', 'g');
                var pattern = '(' + currentValue.replace(reEscape, '\\$1') + ')';
                return "<div>" + (suggestions.data.image?"<img align=absmiddle src='"+suggestions.data.image+"'> ":'') + "</div>" +  "<span>" + suggestions.value.replace(new RegExp(pattern, 'gi'), '<strong>$1<\/strong>') + "</span>";
            }
    });
    //> Удаление товара из списка
    $(document).on( "click", ".fn_remove_item", function() {
        $(this).closest(".fn_row").fadeOut(200, function() { $(this).remove(); });
        return false;
    });
</script> 