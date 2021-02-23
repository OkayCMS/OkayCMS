{if $style_file}
    {$meta_title = "`$btr->styles_styles` $style_file" scope=global}
{/if}

{* Подключаем редактор кода *}
<link rel="stylesheet" href="design/js/codemirror/lib/codemirror.css">
<link rel="stylesheet" href="design/js/codemirror/theme/monokai.css">
<script src="design/js/codemirror/lib/codemirror.js"></script>

<script src="design/js/codemirror/mode/css/css.js"></script>
<script src="design/js/codemirror/addon/selection/active-line.js"></script>
 
{literal}
<style type="text/css">

.CodeMirror{
    font-family:'Courier New';
    margin-bottom:10px;
    border:1px solid #c0c0c0;
    background-color: #ffffff;
    height: auto;
    min-height: 100px;
    width:100%;
}
.CodeMirror-scroll
{
    overflow-y: hidden;
    overflow-x: auto;
}
</style>

<script>
$(function() {    
    // Сохранение кода аяксом
    function save() {
        $('.CodeMirror').css('background-color','#e0ffe0');
        content = editor.getValue();
        $.ajax({
            type: 'POST',
            url: 'ajax/save_style.php',
            data: {'content': content, 'theme':'{/literal}{$theme}{literal}', 'style': '{/literal}{$style_file}{literal}', 'session_id': '{/literal}{$smarty.session.id}{literal}'},
            success: function(data){
                $('.CodeMirror').animate({'background-color': '#ffffff'});
                $('.CodeMirror').animate({'background-color': '#272822'},500);
            },
            dataType: 'json'
        });
    }
    // Нажали кнопку Сохранить
    $('.fn_save').on('click',function(){
        save();
        return false;
    });
});
</script>
{/literal}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {$btr->general_theme|escape} {$theme} {$btr->styles_style|escape} {$style_file}
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="alert alert--icon">
            <div class="alert__content">
                <div class="alert__title mb-q">{$btr->alert_description|escape}</div>
                <p>{$btr->general_design_message|escape}</p> <p><strong>{$btr->general_design_message2|escape}</strong></p>
            </div>
        </div>
    </div>
</div>

{*Вывод ошибок*}
{if $message_error}
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="alert alert--center alert--icon alert--error">
                <div class="alert__content">
                    <div class="alert__title">
                    {if $message_error == 'permissions'}
                        {$btr->general_permission|escape} {$style_file}
                    {elseif $message_error == 'theme_locked'}
                        {$btr->general_protected|escape}
                    {else}
                        {$message_error|escape}
                    {/if}
                    </div>
                </div>
            </div>
        </div>
    </div>
{/if}


<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="boxed match fn_toggle_wrap tabs">
            <div class="design_tabs">
                <div class="design_container">
                    {foreach $styles as $s}
                        <a class="design_tab {if $style_file == $s}focus{/if}" href='{url controller=StylesAdmin file=$s}'>{$s|escape}</a>
                    {/foreach}
                </div>
            </div>
        </div>
    </div>
</div>

{if $style_file}
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="boxed fn_toggle_wrap min_height_230px">
                <div class="heading_box">{$btr->styles_styles|escape} {$style_file|escape}</div>
                 <form class="fn_fast_button">
                    <textarea id="content" name="content" style="width:100%;height:500px;">{$style_content|escape}</textarea>
                </form>
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <button type="submit" name="save" class="fn_save btn btn_small btn_blue float-md-right">
                            {include file='svg_icon.tpl' svgId='checked'}
                            <span>{$btr->general_apply|escape}</span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {* Подключение редактора *}
    {literal}
    <script>

    var editor = CodeMirror.fromTextArea(document.getElementById("content"), {
            mode: "css",
            lineNumbers: true,
            styleActiveLine: true,
            matchBrackets: false,
            enterMode: 'keep',
            indentWithTabs: false,
            indentUnit: 1,
            tabMode: 'classic',
            theme : 'monokai'
        });
    </script>
    {/literal}

{/if}
