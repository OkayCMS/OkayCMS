{* Title *}
{$meta_title=$btr->modules_license_info_title scope=global}

{*Название страницы*}
<div class="row">
    <div class="col-lg-7 col-md-7">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {$btr->modules_license_info_title|escape}
            </div>
        </div>
    </div>
</div>

{*Текст лицензии*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="boxed fn_toggle_wrap min_height_335px">
            <div class="toggle_body_wrap on fn_card">
                <div class="row">
                    <div class="alert alert--icon alert--error">
                        <div class="alert__content">
                            <div class="alert__title">{$btr->modules_license_important}</div>
                            <p> {$btr->modules_license_info_text}
                                <br>
                                <a href="index.php?controller=ModulesAdmin@marketplace"">
                                    {include file="svg_icon.tpl" svgId="logout"}
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>