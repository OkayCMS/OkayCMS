<?php
/* Smarty version 3.1.40, created on 2025-11-26 11:57:47
  from '/var/www/html/backend/design/js/admintooltip/tooltip.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.40',
  'unifunc' => 'content_6926cf1b8a9642_53632652',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '3fa8b0c3c3c25bb1e02360673746497b24d54f78' => 
    array (
      0 => '/var/www/html/backend/design/js/admintooltip/tooltip.tpl',
      1 => 1764098168,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6926cf1b8a9642_53632652 (Smarty_Internal_Template $_smarty_tpl) {
?>$(function() {
    tooltip = $(".fn_tooltip");
    $(document).on('mouseleave', '.tooltip', function(){ tooltipcanclose=true;setTimeout("close_tooltip();", 700); });
    $(document).on('mouseover', '.tooltip', function(){ tooltipcanclose=false; });
    $(document).on('click', '.openTools', function() { $(this).closest('.admTools').toggleClass('open'); });

    if(typeof(Storage) !== "undefined") {

        function setTools() {
            if ( localStorage.getItem("adminTooltip") == "set" ) {
                $(document).on('mouseover', <?php echo $_smarty_tpl->tpl_vars['menu_selector']->value;?>
, show_tooltip);
                $('.changeTools').addClass('on').attr('title', '<?php echo $_smarty_tpl->tpl_vars['btr']->value->admintooltip_disable;?>
');
            } else {
                $(document).off('mouseover', <?php echo $_smarty_tpl->tpl_vars['menu_selector']->value;?>
, show_tooltip);
                $('.changeTools').removeClass('on').attr('title', '<?php echo $_smarty_tpl->tpl_vars['btr']->value->admintooltip_enable;?>
');
            }
        }

        setTools();

        $(document).on('click', '.changeTools', function() {
            if ( localStorage.getItem("adminTooltip") == "set" ) {
                localStorage.setItem("adminTooltip", "unset");
                setTools();
            } else {
                localStorage.setItem("adminTooltip", "set");
                setTools();
            }
        });
        
        // Скрываем тултип при клике вне его
        $(document).on('click', function(e) {
            let tool = $(".admTools");
            if (!tool.is(e.target) && tool.has(e.target).length === 0) {
                tool.removeClass('open')
            }
        });

    } else {
        $(document).on('mouseover', <?php echo $_smarty_tpl->tpl_vars['menu_selector']->value;?>
, show_tooltip);
    }
});

function show_tooltip()
{
    tooltipcanclose=false;
    tooltip.show();
    $(this).on('mouseleave', function(){ tooltipcanclose=true;setTimeout("close_tooltip();", 700); });

    flip = !($(this).offset().left+tooltip.width()+25 < $('body').width());

    tooltip.css('top',  $(this).outerHeight() + 8 + $(this).offset().top + 'px');
    tooltip.css('left', ($(this).offset().left + $(this).outerWidth()*0.1 - (flip ? tooltip.width()-40 : 0)  + 0) + 'px');

    from = encodeURIComponent(window.location.pathname + window.location.search + window.location.hash);
    tooltipcontent = '';
    var lang = '&lang_id=<?php echo $_smarty_tpl->tpl_vars['front_lang_id']->value;?>
';
    if(typeof  lang_id != 'undefined') {
        lang = '&lang_id=' + lang_id;
    }

    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['fast_menu']->value, 'menu', false, 'dataProperty');
$_smarty_tpl->tpl_vars['menu']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['dataProperty']->value => $_smarty_tpl->tpl_vars['menu']->value) {
$_smarty_tpl->tpl_vars['menu']->do_else = false;
?>
    if(id = $(this).attr('data-<?php echo $_smarty_tpl->tpl_vars['dataProperty']->value;?>
'))
    {
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['menu']->value, 'menuItem');
$_smarty_tpl->tpl_vars['menuItem']->index = -1;
$_smarty_tpl->tpl_vars['menuItem']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['menuItem']->value) {
$_smarty_tpl->tpl_vars['menuItem']->do_else = false;
$_smarty_tpl->tpl_vars['menuItem']->index++;
$_smarty_tpl->tpl_vars['menuItem']->first = !$_smarty_tpl->tpl_vars['menuItem']->index;
$__foreach_menuItem_1_saved = $_smarty_tpl->tpl_vars['menuItem'];
?>
            <?php $_smarty_tpl->_assignInScope('paramsString', '');?>
            
            <?php if (!empty($_smarty_tpl->tpl_vars['menuItem']->value['params'])) {?>
                
                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['menuItem']->value['params'], 'param', false, 'paramName');
$_smarty_tpl->tpl_vars['param']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['paramName']->value => $_smarty_tpl->tpl_vars['param']->value) {
$_smarty_tpl->tpl_vars['param']->do_else = false;
?>
                    <?php $_smarty_tpl->_assignInScope('paramsString', ($_smarty_tpl->tpl_vars['paramsString']->value).('&').($_smarty_tpl->tpl_vars['paramName']->value).('="+').($_smarty_tpl->tpl_vars['param']->value).('+"'));?>
                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
            <?php }?>
            
            <?php if ((isset($_smarty_tpl->tpl_vars['menuItem']->value['params']['id'])) || $_smarty_tpl->tpl_vars['menuItem']->value['action'] == 'edit') {?> 
                <?php $_smarty_tpl->_assignInScope('item_class', 'admin_tooltip_edit');?>
            <?php } else { ?>
                <?php $_smarty_tpl->_assignInScope('item_class', 'admin_tooltip_add');?>
            <?php }?>
            
            tooltipcontent <?php if ($_smarty_tpl->tpl_vars['menuItem']->first) {?>=<?php } else { ?>+=<?php }?> "<a href='backend/index.php?controller=<?php echo $_smarty_tpl->tpl_vars['menuItem']->value['controller'];
echo $_smarty_tpl->tpl_vars['paramsString']->value;?>
&return="+from+lang+"' class=<?php echo $_smarty_tpl->tpl_vars['item_class']->value;?>
><?php if ($_smarty_tpl->tpl_vars['item_class']->value == 'admin_tooltip_edit') {?><svg height='22px' viewBox='-27 0 512 512' width='22px' xmlns='http://www.w3.org/2000/svg'><path fill='currentColor' d='m285 492c0 11.046875-8.953125 20-20 20h-185c-44.113281 0-80-35.886719-80-80v-287.144531c0-20.765625 8.050781-40.324219 22.667969-55.070313l66.042969-66.628906c14.585937-14.714844 34.835937-23.15625 55.558593-23.15625h181.621094c44.109375 0 80 35.886719 80 80v187c0 11.046875-8.957031 20-20 20-11.046875 0-20-8.953125-20-20v-187c0-22.054688-17.945313-40-40-40h-181.621094c-10.125 0-20.023437 4.125-27.148437 11.316406l-66.042969 66.628906c-3.230469 3.257813-5.796875 7-7.648437 11.054688h55.570312c16.539062 0 30-13.457031 30-30 0-11.046875 8.953125-20 20-20s20 8.953125 20 20c0 38.597656-31.402344 70-70 70h-59v263c0 22.054688 17.941406 40 40 40h185c11.046875 0 20 8.953125 20 20zm155.425781 2.425781c-11.695312 11.695313-27.0625 17.546875-42.425781 17.546875s-30.730469-5.851562-42.425781-17.546875l-109.367188-109.570312c-2.4375-2.441407-4.199219-5.46875-5.117187-8.792969l-22.363282-80.722656c-1.949218-7.03125.085938-14.5625 5.308594-19.65625 5.21875-5.09375 12.800782-6.941406 19.777344-4.820313l78.726562 23.914063c3.152344.957031 6.019532 2.675781 8.34375 5.007812l109.5625 109.804688c23.375 23.378906 23.375 61.441406-.019531 84.835937zm-162.195312-134.109375 73.515625 73.652344 28.289062-28.289062-73.925781-74.089844-39.128906-11.882813zm133.910156 77.542969-3.851563-3.863281-28.285156 28.285156 3.867188 3.875c7.785156 7.785156 20.472656 7.785156 28.269531-.011719 7.800781-7.800781 7.800781-20.488281 0-28.285156zm0 0'/></svg><?php } else { ?><svg height='22px' viewBox='-27 0 512 512' width='22px' xmlns='http://www.w3.org/2000/svg'><path fill='currentColor' d='m323 242c-74.4375 0-135 60.5625-135 135s60.5625 135 135 135 135-60.5625 135-135-60.5625-135-135-135zm0 230c-52.382812 0-95-42.617188-95-95s42.617188-95 95-95 95 42.617188 95 95-42.617188 95-95 95zm60-95c0 11.046875-8.953125 20-20 20h-20v20c0 11.046875-8.953125 20-20 20s-20-8.953125-20-20v-20h-20c-11.046875 0-20-8.953125-20-20s8.953125-20 20-20h20v-20c0-11.046875 8.953125-20 20-20s20 8.953125 20 20v20h20c11.046875 0 20 8.953125 20 20zm-203 115c0 11.046875-8.953125 20-20 20h-80c-44.113281 0-80-35.886719-80-80v-287.144531c0-20.765625 8.050781-40.320313 22.667969-55.066407l66.042969-66.632812c14.585937-14.714844 34.835937-23.15625 55.558593-23.15625h181.621094c44.109375 0 80 35.886719 80 80v106c0 11.046875-8.957031 20-20 20-11.046875 0-20-8.953125-20-20v-106c0-22.054688-17.945313-40-40-40h-181.621094c-10.125 0-20.023437 4.125-27.148437 11.316406l-66.042969 66.628906c-3.230469 3.257813-5.796875 7-7.644531 11.054688h55.566406c16.542969 0 30-13.460938 30-30 0-11.046875 8.953125-20 20-20s20 8.953125 20 20c0 38.597656-31.402344 70-70 70h-59v263c0 22.054688 17.945312 40 40 40h80c11.046875 0 20 8.953125 20 20zm0 0'/></svg><?php }
echo $_smarty_tpl->tpl_vars['btr']->value->getTranslation($_smarty_tpl->tpl_vars['menuItem']->value['translation']);?>
</a>";
        <?php
$_smarty_tpl->tpl_vars['menuItem'] = $__foreach_menuItem_1_saved;
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    }
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>

    $('.tooltip').html(tooltipcontent);
}

function close_tooltip()
{
    if(tooltipcanclose)
    {
        tooltipcanclose=false;
        tooltip.hide();
    }
}<?php }
}
