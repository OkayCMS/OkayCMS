{if $script_file}
    {$meta_title = "`$btr->scripts_scripts` $script_file" scope=global}
{/if}

{* Подключаем редактор кода *}
<link rel="stylesheet" href="design/js/codemirror/lib/codemirror.css">
<link rel="stylesheet" href="design/js/codemirror/theme/monokai.css">

<script src="design/js/codemirror/lib/codemirror.js"></script>

<script src="design/js/codemirror/mode/smarty/smarty.js"></script>
<script src="design/js/codemirror/mode/smartymixed/smartymixed.js"></script>
<script src="design/js/codemirror/mode/xml/xml.js"></script>
<script src="design/js/codemirror/mode/htmlmixed/htmlmixed.js"></script>
<script src="design/js/codemirror/mode/css/css.js"></script>
<script src="design/js/codemirror/mode/javascript/javascript.js"></script>

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
        .cm-s-monokai .cm-smarty.cm-tag{color: #ff008a;}
        .cm-s-monokai .cm-smarty.cm-string {color: #007000;}
        .cm-s-monokai .cm-smarty.cm-variable {color: #ff008a;}
        .cm-s-monokai .cm-smarty.cm-variable-2 {color: #ff008a;}
        .cm-s-monokai .cm-smarty.cm-variable-3 {color: #ff008a;}
        .cm-s-monokai .cm-smarty.cm-property {color: #ff008a;}
        .cm-s-monokai .cm-comment {color: #505050;}
        .cm-s-monokai .cm-smarty.cm-attribute {color: #ff20Fa;}
    </style>

<script>
    $(function() {
        // Сохранение кода аяксом
        function save() {
            $('.CodeMirror').css('background-color','#e0ffe0');
            content = editor.getValue();
            $.ajax({
                type: 'POST',
                url: 'ajax/save_script.php',
                data: {'content': content, 'theme':'{/literal}{$theme}{literal}', 'script': '{/literal}{$script_file}{literal}', 'session_id': '{/literal}{$smarty.session.id}{literal}'},
                success: function(data){
                    $('.CodeMirror').animate({'background-color': '#fff'},500);
                    $('.CodeMirror').animate({'background-color': '#272822'},500);
                },
                dataType: 'json'
            });
        }

        $('.fn_save').on('click',function(){
            save();
            return false;
        });
    });
</script>
{/literal}

<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {$btr->general_theme|escape} {$theme|escape}
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
                        {$btr->general_permission|escape} {$template_file|escape}
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
                    {foreach $scripts as $s}
                        <a class="design_tab {if $script_file == $s}focus{/if}" href='{url controller=ScriptsAdmin file=$s}'>{$s|escape}</a>
                    {/foreach}
                </div>
            </div>
        </div>
	</div>
</div>


{if $script_file}
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="boxed fn_toggle_wrap min_height_230px">
                <div class="heading_box">{$btr->scripts_scripts|escape} {$script_file}</div>

                <form class="fn_fast_button">
                    <textarea id="script_content" name="script_content" style="width:700px;height:500px;">{$script_content|escape}</textarea>
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
        var editor = CodeMirror.fromTextArea(document.getElementById("script_content"), {
            mode: "javascript",
            lineNumbers: true,
            styleActiveLine: true,
            matchBrackets: false,
            enterMode: 'keep',
            indentWithTabs: false,
            indentUnit: 2,
            tabMode: 'classic',
            theme : 'monokai'
        });
    </script>
    {/literal}

{/if}
