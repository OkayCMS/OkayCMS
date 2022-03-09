{$meta_title = $btr->license_text scope=global}

{*Название страницы*}
<div class="row">
    <div class="col-lg-7 col-md-7">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {$btr->license_text|escape}
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
                    <div class="col-lg-12 col-md-12">
                        <textarea readonly disabled class="col-lg-12 col-md-12 col-xs-12" rows="50">
{$btr->license_text_all|escape}
                        </textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>