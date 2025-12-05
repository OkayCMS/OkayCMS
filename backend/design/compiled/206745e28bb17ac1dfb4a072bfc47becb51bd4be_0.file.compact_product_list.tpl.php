<?php
/* Smarty version 3.1.40, created on 2025-11-26 12:56:41
  from '/var/www/html/backend/design/html/components/compact_product_list.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.40',
  'unifunc' => 'content_6926dce96ff724_05873399',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '206745e28bb17ac1dfb4a072bfc47becb51bd4be' => 
    array (
      0 => '/var/www/html/backend/design/html/components/compact_product_list.tpl',
      1 => 1764098168,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:svg_icon.tpl' => 4,
  ),
),false)) {
function content_6926dce96ff724_05873399 (Smarty_Internal_Template $_smarty_tpl) {
echo '<script'; ?>
 src="design/js/autocomplete/jquery.autocomplete-min.js"><?php echo '</script'; ?>
>
<div class="heading_box">
    <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['title']->value, ENT_QUOTES, 'UTF-8', true);?>

    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
    </div>
</div>
<div class="toggle_body_wrap on fn_card fn_sort_list">
    <div class="okay_list ok_related_list">
        <div class="okay_list_body <?php echo $_smarty_tpl->tpl_vars['name']->value;?>
_compact_product_list sortable">
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['products']->value, 'product');
$_smarty_tpl->tpl_vars['product']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['product']->value) {
$_smarty_tpl->tpl_vars['product']->do_else = false;
?>
                <div class="fn_row okay okay_list_body_item fn_sort_item">
                    <div class="okay_list_row">
                        <div class="okay_list_boding okay_list_drag move_zone">
                            <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'drag_vertical'), 0, true);
?>
                        </div>
                        <div class="okay_list_boding okay_list_related_photo">
                            <input type="hidden" name=<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
[] value='<?php echo $_smarty_tpl->tpl_vars['product']->value->id;?>
'>
                            <a href="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0], array( array('controller'=>'ProductAdmin','id'=>$_smarty_tpl->tpl_vars['product']->value->id),$_smarty_tpl ) );?>
">
                                <?php if ($_smarty_tpl->tpl_vars['product']->value->image) {?>
                                    <img class="product_icon" src='<?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'resize' ][ 0 ], array( $_smarty_tpl->tpl_vars['product']->value->image->filename,40,40 ));?>
'>
                                <?php } elseif ($_smarty_tpl->tpl_vars['product']->value->images[0]) {?>
                                    <img class="product_icon" src='<?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'resize' ][ 0 ], array( $_smarty_tpl->tpl_vars['product']->value->images[0]->filename,40,40 ));?>
'>
                                <?php } else { ?>
                                    <img class="product_icon" src="design/images/no_image.png" width="40">
                                <?php }?>
                            </a>
                        </div>
                        <div class="okay_list_boding okay_list_related_name">
                            <a class="link" href="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0], array( array('controller'=>'ProductAdmin','id'=>$_smarty_tpl->tpl_vars['product']->value->id),$_smarty_tpl ) );?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product']->value->name, ENT_QUOTES, 'UTF-8', true);?>
</a>
                        </div>
                        <div class="okay_list_boding okay_list_close">
                            <button data-hint="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_delete_product, ENT_QUOTES, 'UTF-8', true);?>
" type="button" class="btn_close fn_remove_item hint-bottom-right-t-info-s-small-mobile  hint-anim">
                                <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'delete'), 0, true);
?>
                            </button>
                        </div>
                    </div>
                </div>
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>


            <div id="<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
_compact_product_list_item_add" class="fn_row okay okay_list_body_item fn_sort_item" style='display:none;'>
                <div class="okay_list_row">
                    <div class="okay_list_boding okay_list_drag move_zone">
                        <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'drag_vertical'), 0, true);
?>
                    </div>
                    <div class="okay_list_boding okay_list_related_photo">
                        <input type="hidden" name="<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
[]" value="">
                        <img class=product_icon src="">
                    </div>
                    <div class="okay_list_boding okay_list_related_name">
                        <a class="link compact_list_product_name" href=""></a>
                    </div>
                    <div class="okay_list_boding okay_list_close">
                        <button data-hint="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_delete_product, ENT_QUOTES, 'UTF-8', true);?>
" type="button" class="btn_close fn_remove_item hint-bottom-right-t-info-s-small-mobile  hint-anim">
                            <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'delete'), 0, true);
?>
                        </button>
                    </div>
                </div>
            </div>


        </div>
    </div>
    <div class="heading_label"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['label']->value, ENT_QUOTES, 'UTF-8', true);?>
</div>
    <div class="autocomplete_arrow">
        <input type=text name=related id="<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
_compact_product_list" class="form-control" placeholder='<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['placeholder']->value, ENT_QUOTES, 'UTF-8', true);?>
'>
    </div>
</div>



<?php echo '<script'; ?>
>

    //> Добавление товара в список
    var <?php echo $_smarty_tpl->tpl_vars['name']->value;?>
_compact_product_list_item_add = $('#<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
_compact_product_list_item_add').clone(true);
    $('#<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
_compact_product_list_item_add').remove();
    <?php echo $_smarty_tpl->tpl_vars['name']->value;?>
_compact_product_list_item_add.removeAttr('id');
    $("input#<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
_compact_product_list").devbridgeAutocomplete({
        serviceUrl:'ajax/search_products.php',
        type: 'POST',
        minChars:0,
        orientation:'auto',
        noCache: false,
        params: {
            filter: <?php echo json_encode($_smarty_tpl->tpl_vars['filter']->value);?>

        },
        onSelect:
            function(suggestion){
                $("input#<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
_compact_product_list").val('').focus().blur();
                new_item = <?php echo $_smarty_tpl->tpl_vars['name']->value;?>
_compact_product_list_item_add.clone().appendTo('.<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
_compact_product_list');
                new_item.find('a.compact_list_product_name').html(suggestion.data.name);
                new_item.find('a.compact_list_product_name').attr('href', 'index.php?controller=ProductAdmin&id='+suggestion.data.id);
                new_item.find('input[name*="<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
"]').val(suggestion.data.id);
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
                var reEscape = new RegExp('(\\' + ['/', '.', '*', '+', '?', '|', '(', ')', '[', ']', '<?php echo ', ';?>
', '\\'].join('|\\') + ')', 'g');
                var pattern = '(' + currentValue.replace(reEscape, '\\$1') + ')';
                return "<div>" + (suggestions.data.image?"<img align=absmiddle src='"+suggestions.data.image+"'> ":'') + "</div>" +  "<span>" + suggestions.value.replace(new RegExp(pattern, 'gi'), '<strong>$1<\/strong>') + "</span>";
            }
    });
    //> Удаление товара из списка
    $(document).on( "click", ".fn_remove_item", function() {
        $(this).closest(".fn_row").fadeOut(200, function() { $(this).remove(); });
        return false;
    });
<?php echo '</script'; ?>
> <?php }
}
