{$meta_title = $btr->description__title|escape scope=global}

<style>
    .navigation_secondary{
        margin: 0;
        padding-left: 10px;
    }
    .list_styling{
        margin: 25px 0 0;
        padding-left: 25px;
    }
    .navigation_secondary ul{
        margin: 5px 0 0;
        padding-left: 15px;
    }
    .navigation_secondary li {
        position: relative;
        margin-bottom: 5px;
        padding-left: 5px;
        color: rgb(119, 117, 122);
    }
    .list_styling li{
        position: relative;
        margin-bottom: 10px;
        padding-left: 2px;
        line-height: 1.5;
    }
    .navigation_secondary a {
        text-decoration: none;
        transition: all .185s ease;
        color: #2785E2;
        line-height: 1.5;
        font-size: 14px;
        word-break: break-word;
        font-weight: 400;
    }
    .navigation_secondary a:hover {
        text-decoration: underline;
        color: #2785E2;
    }
    .grid_example{
        margin-top: 20px;
        padding: 0px 15px;
        border: 1px solid rgb(217, 221, 229);
    }
    .grid_example .row > [class^="col-"] {
        padding-top: .75rem;
        padding-bottom: .75rem;
        background-color: rgba(228, 231, 236, 0.4);
        border: 1px solid rgb(217, 221, 229);
    }
    .grid_wrapper{
        display: flex;
        flex-wrap: wrap;
    }
    .grid_wrapper__items{
        display: flex;
        align-items: center;
        flex-direction: column;
        justify-content: center;
        margin: 0 5px 5px 0;
        border-radius: 4px;
        background-color: rgba(236, 236, 236, 0.5);
        width: calc(33.3% - 5px);
        height: 80px;
        transition: all 0.1s ease;
    }
    .grid_wrapper__items:hover{
        background-color: rgba(236, 236, 236, 1);
    }
    .grid_wrapper__icon svg{
        width: 18px;
        height: 18px;
    }
    .grid_wrapper__code{
        margin-top: 10px;
        font-size: 12px;
        font-weight: 500;
    }
    @media only screen and (min-width : 1200px){
        .pos_sticky{
            position: sticky;
            top: 60px;
            left: calc(75% + 70px)
        }
    }
    @media (min-width: 1200px) and (max-width: 1400px) {
        .col-xxl-12{
            width: 100%;
        }
    }
    @media only screen and (min-width : 768px){
        .grid_wrapper__items{
            width: calc(14.28% - 5px);
        }
    }
    @media only screen and (min-width : 575px){
        .grid_wrapper__items{
            width: calc(20% - 5px);
        }

    }
</style>

<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {$btr->description__title|escape}
            </div>
        </div>
    </div>
</div>

<div class="row d_flex">
    <div class="col-lg-12 col-md-12">
        <div class="alert alert--icon">
            <div class="alert__content">
                <div class="alert__title">{$btr->alert_description|escape}</div>
                <p>{$btr->description__description|escape}</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="pos_sticky push-xl-9 col-xl-3">
        <div class="boxed">
            <div class="heading_box">{$btr->description_context}:</div>
            <ol class="navigation_secondary">
                <li>
                    <a class="fn_ancor" href="#ancor_01">{$btr->description_title_grid}</a>
                </li>
                <li>
                    <a class="fn_ancor" href="#ancor_02">{$btr->description_title_typography}</a>
                </li>
                <li>
                    <a class="fn_ancor" href="#ancor_1">{$btr->description_title_alerts}</a>
                </li>
                <li>
                    <a class="fn_ancor" href="#ancor_2">{$btr->description_title_buttons}</a>
                </li>
                <li>
                    <a class="fn_ancor" href="#ancor_3">{$btr->description_title_tooltips}</a>
                </li>
                <li>
                    <a class="fn_ancor" href="#ancor_4">{$btr->description_title_forms}</a>
                    <ul>
                        <li>
                            <a class="fn_ancor" href="#ancor_5">input, input-group, textarea</a>
                        </li>
                        <li>
                            <a class="fn_ancor" href="#ancor_6">Select, dropdown</a>
                        </li>
                        <li>
                            <a class="fn_ancor" href="#ancor_7">Checkbox</a>
                        </li>
                        <li>
                            <a class="fn_ancor" href="#ancor_8">Radiobutton</a>
                        </li>
                        <li>
                            <a class="fn_ancor" href="#ancor_9">{$btr->description_title_switcher}</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a class="fn_ancor" href="#ancor_10">{$btr->description_title_tabs}</a>
                </li>
                <li>
                    <a class="fn_ancor" href="#ancor_11">{$btr->description_title_add_images}</a>
                </li>
                <li>
                    <a class="fn_ancor" href="#ancor_12">{$btr->description_title_clipboard}</a>
                </li>
                <li>
                    <a class="fn_ancor" href="#ancor_13">{$btr->description_title_icons}</a>
                </li>
            </ol>
         </div>
    </div>
    <div class="pull-xl-3 col-xl-9 pr-0">
        <div class="boxed">
            <div id="ancor_01"></div>
            <div class="heading_box font_24 mb-2">{$btr->description_title_grid}</div>
            <div class="mb-2">
                {$btr->description_info_grid}
            </div>
            <div class="heading_box mt-3 mb-2">{$btr->description_title2_grid}</div>
            <div class="mb-2">
                {$btr->description_info2_grid}
            </div>
            <div class="heading_box mt-2 mb-1">{$btr->description_title3_grid}</div>
            <div class="grid_example">
                <div class="row">
                    <div class="col-md-6 col-lg-3">.col-md-6 .col-lg-3</div>
                    <div class="col-md-6 col-lg-3">.col-md-6 .col-lg-3</div>
                    <div class="col-md-8 col-lg-3">.col-md-8 .col-lg-3</div>
                    <div class="col-md-4 col-lg-3">.col-md-4 .col-lg-3</div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-lg-4">.col-md-12 .col-lg-4</div>
                    <div class="col-md-6 col-lg-4">.col-md-6 .col-lg-4</div>
                    <div class="col-md-6 col-lg-4">.col-md-6 .col-lg-4</div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-lg-6">.col-md-9 .col-lg-6</div>
                    <div class="col-md-6 col-lg-6">.col-md-3 .col-lg-6</div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-lg-12">.col-md-12 .col-lg-12</div>
                </div>
            </div>
            <div class="mt-2">
                        <textarea class="fn_code_mirror01">
                            
<div class="row">
    <div class="col-md-6 col-lg-3">.col-md-6 .col-lg-3</div>
    <div class="col-md-6 col-lg-3">.col-md-6 .col-lg-3</div>
    <div class="col-md-8 col-lg-3">.col-md-8 .col-lg-3</div>
    <div class="col-md-4 col-lg-3">.col-md-4 .col-lg-3</div>
</div>
<div class="row">
    <div class="col-md-12 col-lg-4">.col-md-12 .col-lg-4</div>
    <div class="col-md-6 col-lg-4">.col-md-6 .col-lg-4</div>
    <div class="col-md-6 col-lg-4">.col-md-6 .col-lg-4</div>
</div>
<div class="row">
    <div class="col-md-6 col-lg-6">.col-md-9 .col-lg-6</div>
    <div class="col-md-6 col-lg-6">.col-md-3 .col-lg-6</div>
</div>
<div class="row">
    <div class="col-md-12 col-lg-12">.col-md-12 .col-lg-12</div>
</div>
                        </textarea>
            </div>
        </div>

        <div class="boxed">
            <div id="ancor_02"></div>
            <div class="heading_box font_24 mb-2">{$btr->description_title_typography}</div>
            <div class="mb-2">
                {$btr->description_info_typography}
            </div>
            <div class="heading_box mb-2">{$btr->description_title2_typography}</div>
            <table class="table_default">
                <tr class="table_default__row">
                    <td class="table_default__item"><strong>{$btr->description_typography_class}</strong></td>
                    <td class="table_default__item"><strong>{$btr->description_typography_example}</strong></td>
                </tr>
                <tr class="table_default__row">
                    <td class="table_default__item text_primary">.heading_page</td>
                    <td class="table_default__item">
                        <p class="heading_page">{$btr->description_typography_h_page}</p>
                    </td>
                </tr>
                <tr class="table_default__row">
                    <td class="table_default__item text_primary">.heading_box</td>
                    <td class="table_default__item">
                        <p class="heading_box">{$btr->description_typography_h_block}</p>
                    </td>
                </tr>
                <tr class="table_default__row">
                    <td class="table_default__item text_primary">.heading_label</td>
                    <td class="table_default__item">
                        <p class="heading_label">{$btr->description_typography_h_label}</p>
                    </td>
                </tr>
            </table>
            <div class="row mt-2">
                <div class="col-md-6 mb-2">
                    <div class="heading_box mb-2">{$btr->description_title3_typography}</div>
                    <div class="">
                        <p class="font_26">.font_26 (26px)</p>
                        <p class="font_24">.font_24 (24px)</p>
                        <p class="font_20">.font_20 (20px)</p>
                        <p class="font_18">.font_18 (18px)</p>
                        <p class="font_12">.font_12 (12px)</p>
                    </div>
                </div>
                <div class="col-md-6 mb-2">
                    <div class="heading_box mb-2">{$btr->description_title4_typography}</div>
                    <div class="">
                        <p class="text_primary">.text_primary</p>
                        <p class="text_success">.text_success</p>
                        <p class="text_green">.text_green</p>
                        <p class="text_warning">.text_warning</p>
                        <p class="text_grey">.text_grey</p>
                        <p class="text_white text_white__bg">.text_white</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="boxed">
            <div id="ancor_1"></div>
            <div class="heading_box font_24 mb-2">{$btr->description_title_alerts}</div>
            <div class="mb-2">
                {$btr->description_promo_alerts}
            </div>
            <div class="row">
                <div class="col-xxl-12 col-md-6">
                    <div class="alert alert--icon">
                        <div class="alert__content">
                            <div class="alert__title">{$btr->alert_description|escape}</div>
                            {$btr->description_alerts1}
                        </div>
                    </div>
                </div>
                <div class="col-xxl-12 col-md-6">
                    <div class="alert alert--icon alert--error">
                        <div class="alert__content">
                            <div class="alert__title">{$btr->alert_error|escape}</div>
                            {$btr->description_alerts2}
                        </div>
                    </div>
                </div>
                <div class="col-xxl-12 col-md-6">
                    <div class="alert alert--icon alert--success">
                        <div class="alert__content">
                            <div class="alert__title">{$btr->alert_success|escape}</div>
                            {$btr->description_alerts3}
                        </div>
                    </div>
                </div>
                <div class="col-xxl-12 col-md-6">
                    <div class="alert alert--icon alert--info">
                        <div class="alert__content">
                            <div class="alert__title">{$btr->alert_info|escape}</div>
                            {$btr->description_alerts4}
                        </div>
                    </div>
                </div>
                <div class="col-xxl-12 col-md-12">
                    <div class="alert alert--icon alert--warning">
                        <div class="alert__content">
                            <div class="alert__title">{$btr->alert_warning|escape}</div>
                            {$btr->description_alerts5}
                        </div>
                    </div>
                </div>
            </div>
            <textarea class="fn_code_mirror1">

<div class="alert alert--icon">
    <div class="alert__content">
        <div class="alert__title">{$btr->alert_description|escape}</div>
        {$btr->description_alerts1}
    </div>
</div>

<div class="alert alert--icon alert--error">
    <div class="alert__content">
        <div class="alert__title">{$btr->alert_error|escape}</div>
        {$btr->description_alerts2}
    </div>
</div>

<div class="alert alert--icon alert--success">
    <div class="alert__content">
        <div class="alert__title">{$btr->alert_success|escape}</div>
        {$btr->description_alerts3}
    </div>
</div>

<div class="alert alert--icon alert--info">
    <div class="alert__content">
        <div class="alert__title">{$btr->alert_info|escape}</div>
        {$btr->description_alerts4}
    </div>
</div>

<div class="alert alert--icon alert--warning">
    <div class="alert__content">
        <div class="alert__title">{$btr->alert_warning|escape}</div>
        {$btr->description_alerts5}
    </div>
</div>
            </textarea>
        </div>

        <div class="boxed">
            <div id="ancor_2"></div>
            <div class="heading_box font_24 mb-2">{$btr->description_title_buttons}</div>
            <div class="mb-2">
                {$btr->description_promo_buttons}
            </div>

            <div class="row mt-2">
                <div class="col-xxl-12 col-md-6  mb-2">
                    <div class="heading_box mb-2">{$btr->description_title2_buttons}</div>
                    <button class="btn btn_blue mb-q">Button 1</button>
                    <button class="btn btn-info mb-q">Button 2</button>
                    <button class="btn btn_yellow mb-q">Button 3</button>
                    <button class="btn btn-danger mb-q">Button 4</button>
                    <button class="btn btn-warning mb-q">Button 5</button>
                    <div class="mt-2">
                        <textarea class="fn_code_mirror2">

<button class="btn btn_blue">Button 1</button>
<button class="btn btn-info">Button 2</button>
<button class="btn btn_yellow">Button 3</button>
<button class="btn btn-danger">Button 4</button>
<button class="btn btn-warning">Button 5</button></textarea>
                    </div>
                </div>
                <div class="col-xxl-12 col-md-6 mb-2">
                    <div class="heading_box mb-2">{$btr->description_title3_buttons}</div>
                    <button class="btn btn_border_blue mb-q">Button 1</button>
                    <button class="btn btn_border-info mb-q">Button 2</button>
                    <button class="btn btn_border_yellow mb-q">Button 3</button>
                    <button class="btn btn-outline-danger mb-q">Button 4</button>
                    <button class="btn btn-outline-warning mb-q">Button 5</button>
                    <div class="mt-2">
                        <textarea class="fn_code_mirror3">

<button class="btn btn_border_blue">Button 1</button>
<button class="btn btn_border-info">Button 2</button>
<button class="btn btn_border_yellow">Button 3</button>
<button class="btn btn-outline-danger">Button 4</button>
<button class="btn btn-outline-warning">Button 5</button></textarea>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-xxl-12 col-md-4  mb-2">
                    <div class="heading_box mb-2">{$btr->description_title4_buttons}</div>
                    <button class="btn btn_blue btn_big mb-q">{include file='svg_icon.tpl' svgId='checked'} <span>Button 1</span></button>
                    <button class="btn btn-info btn_big mb-q"><span>Button 2</span></button>
                    <button class="btn btn-outline-danger btn_big mb-q"><span>Button 3</span></button>
                    <div class="mt-2">
                        <textarea class="fn_code_mirror4">

<button class="btn btn_blue btn_big">{include file='svg_icon.tpl' svgId='checked'} <span>Button 1</span></button>
<button class="btn btn-info btn_big"><span>Button 2</span></button>
<button class="btn btn-outline-danger btn_big"><span>Button 3</span></button></textarea>
                    </div>
                </div>
                <div class="col-xxl-12 col-md-4 mb-2">
                    <div class="heading_box mb-2">{$btr->description_title5_buttons}</div>
                    <button class="btn btn_blue btn_small mb-q">{include file='svg_icon.tpl' svgId='checked'} <span>Button 1</span></button>
                    <button class="btn btn-info btn_small mb-q"><span>Button 2</span></button>
                    <button class="btn btn-outline-danger btn_small mb-q"><span>Button 3</span></button>
                    <div class="mt-2">
                        <textarea class="fn_code_mirror5">

<button class="btn btn_blue btn_small">{include file='svg_icon.tpl' svgId='checked'} <span>Button 1</span></button>
<button class="btn btn-info btn_small"><span>Button 2</span></button>
<button class="btn btn-outline-danger btn_small"><span>Button 3</span></button></textarea>
                    </div>
                </div>
                <div class="col-xxl-12 col-md-4 mb-2">
                    <div class="heading_box mb-2">{$btr->description_title6_buttons}</div>
                    <button class="btn btn_border_blue btn_mini mb-q">{include file='svg_icon.tpl' svgId='checked'} <span>Button 1</span></button>
                    <button class="btn btn-info btn_mini mb-q"><span>Button 2</span></button>
                    <button class="btn btn-outline-danger btn_mini mb-q"><span>Button 3</span></button>
                    <div class="mt-2">
                        <textarea class="fn_code_mirror6">

<button class="btn btn_border_blue btn_mini">{include file='svg_icon.tpl' svgId='checked'} <span>Button 1</span></button>
<button class="btn btn-info btn_mini"><span>Button 2</span></button>
<button class="btn btn-outline-danger btn_mini"><span>Button 3</span></button></textarea>
                    </div>
                </div>
            </div>

        </div>

        <div  class="boxed">
            <div id="ancor_3"></div>
            <div class="heading_box font_24 mb-2">{$btr->description_title_tooltips}</div>
            <div class="mb-2">
                {$btr->description_info_tooltips}
            </div>
            <div class="row mt-2">
                <div class="col-md-12  mb-2">
                    <div class="heading_box mb-2">{$btr->description_title2_tooltips}</div>
                    <div class="mt-2">
                        <div class="heading_label heading_label--required">
                            <span>Meta-description</span>
                            <i class="fn_tooltips" title="Правильный description должен содержать ключевые слова, под которые вы намерены продвигать страницу. Самые частотные запросы должны быть расположены в начале описания. Не используйте в мета-теге более 3-4 ключевых фраз. Одно и то же слово не следует повторять более 5-7 раз">
                                {include file='svg_icon.tpl' svgId='icon_tooltips'}
                            </i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <textarea class="fn_code_mirror7">
<div class="heading_label">
    <span>Заголовок</span>
    <i class="fn_tooltips" title="Текст подсказки">
    {include file='svg_icon.tpl' svgId='icon_tooltips'}
    </i>
</div>
                        </textarea>
                    </div>
                </div>
                <div class="col-md-12  mb-2">
                    <div class="heading_box mb-2">{$btr->description_title3_tooltips}</div>
                    <div class="mt-2">
                        <button data-hint="Tooltip top" class="mb-q hint-top-middle-t-info-s-small-mobile  hint-anim btn btn-secondary btn_mini ">Tooltip top</button>
                        <button data-hint="Tooltip bottom" class="mb-q hint-bottom-middle-t-info-s-small-mobile  hint-anim btn btn-secondary btn_mini ">Tooltip bottom</button>
                        <button data-hint="Tooltip left" class="mb-q hint-left-middle-t-info-s-small-mobile  hint-anim btn btn-secondary btn_mini ">Tooltip left</button>
                        <button data-hint="Tooltip right" class="mb-q hint-right-middle-t-info-s-small-mobile  hint-anim btn btn-secondary btn_mini ">Tooltip right</button>
                        <button data-hint="Tooltip bottom right" class="mb-q hint-bottom-right-middle-t-info-s-small-mobile  hint-anim btn btn-secondary btn_mini ">Tooltip bottom right</button>
                        <button data-hint="Tooltip top left" class="mb-q hint-top-left-middle-t-info-s-small-mobile  hint-anim btn btn-secondary btn_mini ">Tooltip top left</button>
                    </div>
                    <div class="mt-2">
                        <textarea class="fn_code_mirror8">

<button data-hint="Tooltip top" class="hint-top-middle-t-info-s-small-mobile  hint-anim btn btn-secondary btn_mini ">Tooltip top</button>
<button data-hint="Tooltip bottom" class="hint-bottom-middle-t-info-s-small-mobile  hint-anim btn btn-secondary btn_mini ">Tooltip bottom</button>
<button data-hint="Tooltip left" class="hint-left-middle-t-info-s-small-mobile  hint-anim btn btn-secondary btn_mini ">Tooltip left</button>
<button data-hint="Tooltip right" class="hint-right-middle-t-info-s-small-mobile  hint-anim btn btn-secondary btn_mini ">Tooltip right</button>
<button data-hint="Tooltip bottom right" class="hint-bottom-right-middle-t-info-s-small-mobile  hint-anim btn btn-secondary btn_mini ">Tooltip bottom right</button>
<button data-hint="Tooltip top left" class="hint-top-left-middle-t-info-s-small-mobile  hint-anim btn btn-secondary btn_mini ">Tooltip top left</button>
                        </textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="boxed">
            <div id="ancor_4"></div>
            <div class="heading_box font_24 mb-2">{$btr->description_title_switcher}</div>
            <div class="mb-2">
                {$btr->description_info_switcher}
            </div>
            <div id="ancor_5"></div>
            <div class="heading_box mb-2">input, inputgroup, textarea</div>
            <div class="row mb-2">
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="heading_label heading_label--required">
                            <span>Name</span>
                        </div>
                        <input class="form-control" type="text" placeholder="Ivan Petryak">
                    </div>
                    <div class="form-group">
                        <div class="heading_label">Phone</div>
                        <div class="input-group">
                            <input class="form-control" type="phone" placeholder="+375(099) 999-99-99">
                            <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="heading_label">Toggle disabled</div>
                        <div class="input-group input-group--dabbl">
                            <span class="input-group-addon input-group-addon--left">URL</span>
                            <input name="url" class="form-control fn_url fn_disabled" type="text" value="https://okay-cms.com/" readonly="readonly">
                            <span class="input-group-addon fn_disable_url"><i class="fa fa-lock"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="heading_label heading_label--required">
                            <span>Message</span>
                        </div>
                        <textarea class="form-control okay_textarea"></textarea>
                    </div>
                </div>
            </div>
            <div class="mt-2">
                        <textarea class="fn_code_mirror9">

<div class="form-group">
    <div class="heading_label heading_label--required">
        <span>Name</span>
    </div>
    <input class="form-control" type="text" placeholder="Ivan Petryak">
</div>
<div class="form-group">
    <div class="heading_label">Phone</div>
    <div class="input-group">
        <input class="form-control" type="phone" placeholder="+375(099) 999-99-99">
        <span class="input-group-addon"><i class="fa fa-phone"></i></span>
    </div>
</div>
<div class="form-group">
    <div class="heading_label">Toggle disabled</div>
    <div class="input-group input-group--dabbl">
        <span class="input-group-addon input-group-addon--left">URL</span>
        <input name="url" class="form-control fn_url fn_disabled" type="text" value="https://okay-cms.com/" readonly="readonly">
        <span class="input-group-addon fn_disable_url"><i class="fa fa-lock"></i></span>
    </div>
</div>
                        </textarea>
            </div>
            <div id="ancor_6"></div>
            <div class="heading_box mt-3 mb-2">Select, dropdown</div>
            <div class="row mb-2">
                <div class="col-md-6">
                    <select class="selectpicker form-control mb-1">
                        <option value="0">Dropdown list</option>
                        <option value="1">List item 1</option>
                        <option value="2">List item 2</option>
                        <option value="3">List item 3</option>
                        <option value="4">List item 4</option>
                        <option value="5">List item 5</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <select class="selectpicker form-control mb-1" data-live-search="true">
                        <option value="0">Dropdown live search</option>
                        <option value="1">List item 1</option>
                        <option value="2">List item 2</option>
                        <option value="3">List item 3</option>
                        <option value="4">List item 4</option>
                        <option value="5">List item 5</option>
                    </select>
                </div>
            </div>
            <div class="mt-2">
                        <textarea class="fn_code_mirror10">

<select class="selectpicker form-control mb-1">
    <option value="0">Dropdown list</option>
    <option value="1">List item 1</option>
    <option value="2">List item 2</option>
    <option value="3">List item 3</option>
    <option value="4">List item 4</option>
    <option value="5">List item 5</option>
</select>

<select class="selectpicker form-control mb-1" data-live-search="true">
    <option value="0">Dropdown live search</option>
    <option value="1">List item 1</option>
    <option value="2">List item 2</option>
    <option value="3">List item 3</option>
    <option value="4">List item 4</option>
    <option value="5">List item 5</option>
</select>
                        </textarea>
            </div>
            <div class="row ">
                <div class="col-xxl-12 col-md-6 mt-3 mb-2">
                    <div id="ancor_7"></div>
                    <div class="heading_box mb-2">Checkbox</div>
                    <form action="">
                        <div class="okay_type_checkbox_wrap">
                            <input id="check1" class="hidden_check" name="checkbox" type="checkbox" value="" checked="">
                            <label for="check1" class="okay_type_checkbox">
                                <span>Checkbox items 1</span>
                            </label>
                        </div>
                        <div class="okay_type_checkbox_wrap">
                            <input id="check2" class="hidden_check" name="checkbox" type="checkbox" value="">
                            <label for="check2" class="okay_type_checkbox">
                                <span>Checkbox items 2</span>
                            </label>
                        </div>
                        <div class="okay_type_checkbox_wrap">
                            <input id="check3" class="hidden_check" name="checkbox" type="checkbox" value="">
                            <label for="check3" class="okay_type_checkbox">
                                <span>Checkbox items 3</span>
                            </label>
                        </div>
                    </form>
                    <div class="mt-2">
                        <textarea class="fn_code_mirror11">
<form action="">
    <div class="okay_type_checkbox_wrap">
        <input id="check1" class="hidden_check" name="checkbox" type="checkbox" value="" checked="">
        <label for="check1" class="okay_type_checkbox">
            <span>Checkbox items 1</span>
        </label>
    </div>
    <div class="okay_type_checkbox_wrap">
        <input id="check2" class="hidden_check" name="checkbox" type="checkbox" value="">
        <label for="check2" class="okay_type_checkbox">
            <span>Checkbox items 2</span>
        </label>
    </div>
    <div class="okay_type_checkbox_wrap">
        <input id="check3" class="hidden_check" name="checkbox" type="checkbox" value="">
        <label for="check3" class="okay_type_checkbox">
            <span>Checkbox items 3</span>
        </label>
    </div>
</form>
                        </textarea>
                    </div>
                </div>
                <div class="col-xxl-12 col-md-6 mt-3 mb-2">
                    <div id="ancor_8"></div>
                    <div class="heading_box mb-2">Radiobutton</div>
                    <form action="">
                    <div class="okay_type_radio_wrap">
                        <input id="radio1" class="hidden_check" name="radiobutton" type="radio" value="" checked="">
                        <label for="radio1" class="okay_type_radio">
                            <span>Radio items 1</span>
                        </label>
                    </div>
                    <div class="okay_type_radio_wrap">
                        <input id="radio2" class="hidden_check" name="radiobutton" type="radio" value="">
                        <label for="radio2" class="okay_type_radio">
                            <span>Radio items 2</span>
                        </label>
                    </div>
                    <div class="okay_type_radio_wrap">
                        <input id="radio3" class="hidden_check" name="radiobutton" type="radio" value="">
                        <label for="radio3" class="okay_type_radio">
                            <span>Radio items 3</span>
                        </label>
                    </div>
                    </form>
                    <div class="mt-2">
                        <textarea class="fn_code_mirror12">
<form action="">
    <div class="okay_type_radio_wrap">
        <input id="radio1" class="hidden_check" name="radiobutton" type="radio" value="" checked="">
        <label for="radio1" class="okay_type_radio">
            <span>Radio items 1</span>
        </label>
    </div>
    <div class="okay_type_radio_wrap">
        <input id="radio2" class="hidden_check" name="radiobutton" type="radio" value="">
        <label for="radio2" class="okay_type_radio">
            <span>Radio items 2</span>
        </label>
    </div>
    <div class="okay_type_radio_wrap">
        <input id="radio3" class="hidden_check" name="radiobutton" type="radio" value="">
        <label for="radio3" class="okay_type_radio">
            <span>Radio items 3</span>
        </label>
    </div>
</form>
                        </textarea>
                    </div>
                </div>
                <div class="col-md-12 mb-2 mt-3">
                    <div id="ancor_9"></div>
                    <div class="heading_box mb-2">{$btr->description_title2_switcher}</div>
                    <div class="activity_of_switch activity_of_switch--left">
                        <div class="activity_of_switch_item">
                            <div class="okay_switch clearfix">
                            <label class="switch_label">{$btr->description_title2_switcher_label}</label>
                            <label class="switch switch-default">
                                <input class="switch-input" name="enabled" value="1" type="checkbox" id="example_checkbox" checked="">
                                <span class="switch-label"></span>
                                <span class="switch-handle"></span>
                            </label>
                        </div>
                        </div>
                    </div>
                    <div class="mt-2">
                        <textarea class="fn_code_mirror13">

<div class="activity_of_switch activity_of_switch--left">
    <div class="activity_of_switch_item">
        <div class="okay_switch clearfix">
        <label class="switch_label">{$btr->description_title2_switcher_label}</label>
        <label class="switch switch-default">
            <input class="switch-input" name="enabled" value="1" type="checkbox" id="example_checkbox" checked="">
            <span class="switch-label"></span>
            <span class="switch-handle"></span>
        </label>
    </div>
    </div>
</div>
                        </textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="boxed">
            <div id="ancor_10"></div>
            <div class="heading_box font_24 mb-2">{$btr->description_title_tabs}</div>
            <div class="">
                <div class="tabs">
                    <div class="heading_tabs">
                        <div class="tab_navigation">
                            <a href="#tab1" class="heading_box tab_navigation_link">Tab 1</a>
                            <a href="#tab2" class="heading_box tab_navigation_link">Tab 2</a>
                            <a href="#tab3" class="heading_box tab_navigation_link">Tab 3</a>
                            <a href="#tab4" class="heading_box tab_navigation_link">Tab 4</a>
                        </div>
                    </div>
                    <div class="tab_container">
                        <div id="tab1" class="tab">
                            <div class="">Равным образом рамки и место обучения кадров влечет за собой процесс внедрения и модернизации системы обучения кадров, соответствует насущным потребностям. Равным образом консультация с широким активом требуют определения и уточнения модели развития.</div>
                        </div>
                        <div id="tab2" class="tab">
                            <div class="">Идейные соображения высшего порядка, а также рамки и место обучения кадров обеспечивает широкому кругу (специалистов) участие в формировании новых предложений. Повседневная практика показывает, что реализация намеченных плановых заданий в значительной степени обуславливает создание модели развития</div>
                        </div>
                        <div id="tab3" class="tab">
                            <div class="">Таким образом новая модель организационной деятельности способствует подготовки и реализации систем массового участия. Значимость этих проблем настолько очевидна, что консультация с широким активом играет важную роль в формировании новых предложений. Повседневная практика показывает, что реализация намеченных плановых заданий в значительной степени обуславливает создание модели развития.</div>
                        </div>
                        <div id="tab4" class="tab">
                            <div class="">С другой стороны рамки и место обучения кадров способствует подготовки и реализации модели развития. Если у вас есть какие то интересные предложения, обращайтесь! Студия Web-Boss всегда готова решить любую задачу. Равным образом консультация с широким активом требуют определения и уточнения модели развития.</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-1">
                        <textarea class="fn_code_mirror14">

<div class="tabs">
    <div class="heading_tabs">
        <div class="tab_navigation">
            <a href="#tab1" class="heading_box tab_navigation_link">Tab 1</a>
            <a href="#tab2" class="heading_box tab_navigation_link">Tab 2</a>
            <a href="#tab3" class="heading_box tab_navigation_link">Tab 3</a>
            <a href="#tab4" class="heading_box tab_navigation_link">Tab 4</a>
        </div>
    </div>
    <div class="tab_container">
        <div id="tab1" class="tab">
            <div class="">Tab text 1</div>
        </div>
        <div id="tab2" class="tab">
            <div class="">Tab text 2</div>
        </div>
        <div id="tab3" class="tab">
            <div class="">Tab text 3</div>
        </div>
        <div id="tab4" class="tab">
            <div class="">Tab text 4</div>
        </div>
    </div>
</div>
                        </textarea>
            </div>
        </div>

        <div class="boxed">
            <div id="ancor_11"></div>
            <div class="heading_box font_24 mb-2">{$btr->description_title_add_images}</div>
            <div class="mb-2">
                {$btr->description_info_add_images}
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div id="ancor_9"></div>
                    <div class="heading_box mb-2">{$btr->description_title2_add_images}</div>
                    <form action="#">
                        <div class="input_file_container">
                            <input class="input_file" id="my-file" type="file">
                            <label tabindex="0" for="my-file" class="input_file_trigger">
                                <svg width="20" height="17" viewBox="0 0 20 17"><path fill="currentcolor" d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"></path></svg>
                                <span>Выберите файл...</span>
                            </label>
                        </div>
                        <p class="input_file_return"></p>
                    </form>
                    <div class="mt-2">
                        <textarea class="fn_code_mirror15">

<form action="#">
    <div class="input_file_container">
        <input class="input_file" id="my-file" type="file">
        <label tabindex="0" for="my-file" class="input_file_trigger">
            <svg width="20" height="17" viewBox="0 0 20 17"><path fill="currentcolor" d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"></path></svg>
            <span>Выберите файл...</span>
        </label>
    </div>
    <p class="input_file_return"></p>
</form>
                        </textarea>
                    </div>
                </div>
                <div class="col-md-12 mt-2">
                    <div id="ancor_9"></div>
                    <div class="heading_box mb-2">{$btr->description_title3_add_images}</div>
                    <div class="mb-2">
                        {$btr->description_info3_add_images}
                    </div>
                    <ul class="fn_droplist_wrap product_images_list clearfix sortable" data-image="product">
                        <li class="fn_dropzone dropzone_block">
                            <i class="fa fa-plus font-5xl" aria-hidden="true"></i>
                            <input type="file" name="" data-name="dropped_images[]" multiple class="dropinput fn_template">
                        </li>
                        <li class="product_image_item hidden fn_sort_item">
                            <button type="button" class="fn_remove_image remove_image"></button>
                            <i class="move_zone">
                                {if $image}
                                <img class="product_icon" src="{$image->filename|resize:300:120}" alt=""/>
                                {else}
                                <img class="product_icon" src="design/images/no_image.png" width="40">
                                {/if}
                                <input type=hidden name='images_ids[]' value="{$image->id}">
                            </i>
                        </li>
                        <li class="fn_new_image_item product_image_item fn_sort_item">
                            <button type="button" class="fn_remove_image remove_image"></button>
                            <img src="" alt=""/>
                        </li>
                    </ul>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mt-2">
                                <textarea class="fn_code_mirror16">
<ul class="fn_droplist_wrap product_images_list clearfix sortable" data-image="product">
    <li class="fn_dropzone dropzone_block">
        <i class="fa fa-plus font-5xl" aria-hidden="true"></i>
        <input type="file" name="" data-name="dropped_images[]" multiple class="dropinput fn_template">
    </li>

     &#123;foreach $product_images as $image&#125; ----- заменить массив на нужный

    <li class="product_image_item hidden fn_sort_item">
        <button type="button" class="fn_remove_image remove_image"></button>
        <i class="move_zone">

            &#123;if $image&#125; ---- заменить переменную

                 <img class="product_icon" src="{$image->filename|resize:300:120}" alt=""/>
            &#123;else&#125;
                <img class="product_icon" src="design/images/no_image.png" width="40">
            &#123;/if&#125;
        </i>
    </li>

    &#123;/foreach}

    <li class="fn_new_image_item product_image_item fn_sort_item">
        <button type="button" class="fn_remove_image remove_image"></button>
        <img src="" alt=""/>
    </li>
</ul>
                                </textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mt-2">
                                <textarea class="fn_code_mirror17">
$(window).on("load", function() {
    var image_item_clone = $(".fn_new_image_item").clone(true);
    $(".fn_new_image_item").remove();
    var new_image_tem_clone = $(".fn_new_spec_image_item").clone(true);
    $(".fn_new_spec_image_item").remove();

    if(window.File && window.FileReader && window.FileList) {
        $(".fn_dropzone").on('dragover', function (e){
            e.preventDefault();
            $(this).css('background', '#bababa');
        });
        $(".fn_dropzone").on('dragleave', function(){
            $(this).css('background', '#f8f8f8');
        });

        function handleFileSelect(evt){
            let dropInput = $(this).closest(".fn_droplist_wrap").find("input.dropinput.fn_template").clone();
            dropInput.attr('name', dropInput.data('name')).removeClass('fn_template');
            var parent = $(this).closest(".fn_droplist_wrap");
            var files = evt.target.files; // FileList object
            for (var i = 0, f; f = files[i]; i++) {
                if (!f.type.match('image.*')) {
                    continue;
                }
                var reader = new FileReader();
                reader.onload = (function(theFile) {
                    return function(e) {
                        if(parent.data('image') == "product"){
                            var clone_item = image_item_clone.clone(true);
                        } else if(parent.data('image') == "special") {
                            var clone_item = new_image_tem_clone.clone(true);
                        }
                        clone_item.find("img").attr("onerror",'');
                        clone_item.find("img").attr("src", e.target.result);
                        clone_item.find("input").val(theFile.name);
                        clone_item.appendTo(parent);
                        parent.find(".fn_dropzone").append(dropInput);
                    };
                })(f);
                reader.readAsDataURL(f);
            }
            $(".fn_dropzone").removeAttr("style");
        }
        $(document).on('change','.dropinput',handleFileSelect);
    }
    $(document).on("click", ".fn_remove_image", function () {
        $(this).closest("li").remove();
    });

});
                                </textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="boxed">
            <div id="ancor_12"></div>
            <div class="heading_box font_24 mb-2">{$btr->description_title_clipboard}</div>
            <div class="mb-3">
                <a href="" class="fn_clipboard clipboard hint-bottom-middle-t-info-s-small-mobile" data-hint="Click to copy" data-hint-copied="✔ Copied to clipboard">{$btr->description_text_clip_clipboard}</a>

            </div>
            <div class="mt-2">
                        <textarea class="fn_code_mirror18">

<!--HTML code-->
<a href="" class="fn_clipboard hint-bottom-middle-t-info-s-small-mobile" data-hint="Click to copy" data-hint-copied="✔ Copied to clipboard">
    Нажмите, чтобы скопировать в буфер обмена.
</a>

<!--JAVASCRIPT code-->
<script>
    sclipboard();
</script>
                        </textarea>
            </div>
        </div>

        <div class="boxed">
            <div id="ancor_13"></div>
            <div class="heading_box font_24 mb-2">{$btr->description_title_icons}</div>
            <div class="mb-2">
                {$btr->description_info_icons}
            </div>
            <div class="heading_box mb-2">{$btr->description_title2_icons}</div>
            <div class="grid_wrapper">
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='trash'}
                    </div>
                    <div class="grid_wrapper__code">
                        trash
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='export'}
                    </div>
                    <div class="grid_wrapper__code">
                        export
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='print'}
                    </div>
                    <div class="grid_wrapper__code">
                        print
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='add'}
                    </div>
                    <div class="grid_wrapper__code">
                        add
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='mobile_menu'}
                    </div>
                    <div class="grid_wrapper__code">
                        mobile_menu
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='mobile_menu2'}
                    </div>
                    <div class="grid_wrapper__code">
                        mobile_menu2
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='order_list'}
                    </div>
                    <div class="grid_wrapper__code">
                        order_list
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='tag'}
                    </div>
                    <div class="grid_wrapper__code">
                        tag
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='check'}
                    </div>
                    <div class="grid_wrapper__code">
                        check
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='exchange'}
                    </div>
                    <div class="grid_wrapper__code">
                        exchange
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='checked'}
                    </div>
                    <div class="grid_wrapper__code">
                        checked
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='email'}
                    </div>
                    <div class="grid_wrapper__code">
                        email
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='phone'}
                    </div>
                    <div class="grid_wrapper__code">
                        phone
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='magic'}
                    </div>
                    <div class="grid_wrapper__code">
                        magic
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='infinity'}
                    </div>
                    <div class="grid_wrapper__code">
                        infinity
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='sorts'}
                    </div>
                    <div class="grid_wrapper__code">
                        sorts
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='icon_desktop'}
                    </div>
                    <div class="grid_wrapper__code">
                        icon_desktop
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='techsupport'}
                    </div>
                    <div class="grid_wrapper__code">
                        techsupport
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='sorting'}
                    </div>
                    <div class="grid_wrapper__code">
                        sorting
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='plus'}
                    </div>
                    <div class="grid_wrapper__code">
                        plus
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='minus'}
                    </div>
                    <div class="grid_wrapper__code">
                        minus
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='drag_vertical'}
                    </div>
                    <div class="grid_wrapper__code">
                        drag_vertical
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='notify'}
                    </div>
                    <div class="grid_wrapper__code">
                        notify
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='exit'}
                    </div>
                    <div class="grid_wrapper__code">
                        exit
                    </div>
                </div>

                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='delete'}
                    </div>
                    <div class="grid_wrapper__code">
                        delete
                    </div>
                </div>

                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='icon_copy'}
                    </div>
                    <div class="grid_wrapper__code">
                        icon_copy
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='icon_tooltips'}
                    </div>
                    <div class="grid_wrapper__code">
                        icon_tooltips
                    </div>
                </div>

                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='logout'}
                    </div>
                    <div class="grid_wrapper__code">
                        logout
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='left_pages'}
                    </div>
                    <div class="grid_wrapper__code">
                        left_pages
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='left_blog'}
                    </div>
                    <div class="grid_wrapper__code">
                        left_blog
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='sertificat'}
                    </div>
                    <div class="grid_wrapper__code">
                        sertificat
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='left_comments'}
                    </div>
                    <div class="grid_wrapper__code">
                        left_comments
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='left_auto'}
                    </div>
                    <div class="grid_wrapper__code">
                        left_auto
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='left_stats'}
                    </div>
                    <div class="grid_wrapper__code">
                        left_stats
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='left_seo'}
                    </div>
                    <div class="grid_wrapper__code">
                        left_seo
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='left_topvisor_title'}
                    </div>
                    <div class="grid_wrapper__code">
                        left_topvisor_title
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='left_design'}
                    </div>
                    <div class="grid_wrapper__code">
                        left_design
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='left_settings'}
                    </div>
                    <div class="grid_wrapper__code">
                        left_settings
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='refresh_icon'}
                    </div>
                    <div class="grid_wrapper__code">
                        refresh_icon
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='user_icon'}
                    </div>
                    <div class="grid_wrapper__code">
                        user_icon
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='pass_icon'}
                    </div>
                    <div class="grid_wrapper__code">
                        pass_icon
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='tag_social'}
                    </div>
                    <div class="grid_wrapper__code">
                        tag_social
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='tag_email'}
                    </div>
                    <div class="grid_wrapper__code">
                        tag_email
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='tag_search'}
                    </div>
                    <div class="grid_wrapper__code">
                        tag_search
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='tag_referral'}
                    </div>
                    <div class="grid_wrapper__code">
                        tag_referral
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='tag_unknown'}
                    </div>
                    <div class="grid_wrapper__code">
                        tag_unknown
                    </div>
                </div>



                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='return'}
                    </div>
                    <div class="grid_wrapper__code">
                        return
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='left_orders'}
                    </div>
                    <div class="grid_wrapper__code">
                        left_orders
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='left_users'}
                    </div>
                    <div class="grid_wrapper__code">
                        left_users
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='left_modules'}
                    </div>
                    <div class="grid_wrapper__code">
                        left_modules
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='left_faq_title'}
                    </div>
                    <div class="grid_wrapper__code">
                        left_faq_title
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='date'}
                    </div>
                    <div class="grid_wrapper__code">
                        date
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='left_catalog'}
                    </div>
                    <div class="grid_wrapper__code">
                        left_catalog
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='help_icon'}
                    </div>
                    <div class="grid_wrapper__code">
                        help_icon
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='warn_icon'}
                    </div>
                    <div class="grid_wrapper__code">
                        warn_icon
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='info_icon'}
                    </div>
                    <div class="grid_wrapper__code">
                        info_icon
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='icon_featured'}
                    </div>
                    <div class="grid_wrapper__code">
                        icon_featured
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='prev'}
                    </div>
                    <div class="grid_wrapper__code">
                        prev
                    </div>
                </div>
                <div class="grid_wrapper__items">
                    <div class="grid_wrapper__icon">
                        {include file='svg_icon.tpl' svgId='next'}
                    </div>
                    <div class="grid_wrapper__code">
                        next
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


{* Подключаем редактор кода *}
<link rel="stylesheet" href="../backend/design/js/codemirror/lib/codemirror.css">
<link rel="stylesheet" href="../backend/design/js/codemirror/theme/monokai.css">
<script src="../backend/design/js/codemirror/lib/codemirror.js"></script>
<script src="../backend/design/js/codemirror/mode/smarty/smarty.js"></script>
<script src="../backend/design/js/codemirror/mode/smartymixed/smartymixed.js"></script>
<script src="../backend/design/js/codemirror/mode/xml/xml.js"></script>
<script src="../backend/design/js/codemirror/mode/htmlmixed/htmlmixed.js"></script>
<script src="../backend/design/js/codemirror/mode/css/css.js"></script>
<script src="../backend/design/js/codemirror/mode/javascript/javascript.js"></script>
<script src="../backend/design/js/codemirror/addon/selection/active-line.js"></script>

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
    .CodeMirror-scroll{
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

{/literal}

{literal}
<script>
    sclipboard();

    $(document).ready(function(){
        $(".fn_ancor[href*=#]").on("click", function(e){
            var anchor = $(this);
            $('html, body').stop().animate({
                scrollTop: $(anchor.attr('href')).offset().top - 90
            }, 777);
            e.preventDefault();
            return false;
        });
    });

    $(window).on("load", function() {
        var image_item_clone = $(".fn_new_image_item").clone(true);
        $(".fn_new_image_item").remove();
        var new_image_tem_clone = $(".fn_new_spec_image_item").clone(true);
        $(".fn_new_spec_image_item").remove();
        // Или перетаскиванием
        if(window.File && window.FileReader && window.FileList) {

            $(".fn_dropzone").on('dragover', function (e){
                e.preventDefault();
                $(this).css('background', '#bababa');
            });
            $(".fn_dropzone").on('dragleave', function(){
                $(this).css('background', '#f8f8f8');
            });

            function handleFileSelect(evt){
                let dropInput = $(this).closest(".fn_droplist_wrap").find("input.dropinput.fn_template").clone();
                dropInput.attr('name', dropInput.data('name')).removeClass('fn_template');
                var parent = $(this).closest(".fn_droplist_wrap");
                var files = evt.target.files; // FileList object
                // Loop through the FileList and render image files as thumbnails.
                for (var i = 0, f; f = files[i]; i++) {
                    // Only process image files.
                    if (!f.type.match('image.*')) {
                        continue;
                    }
                    var reader = new FileReader();
                    // Closure to capture the file information.
                    reader.onload = (function(theFile) {
                        return function(e) {
                            // Render thumbnail.
                            if(parent.data('image') == "product"){
                                var clone_item = image_item_clone.clone(true);
                            } else if(parent.data('image') == "special") {
                                var clone_item = new_image_tem_clone.clone(true);
                            }
                            clone_item.find("img").attr("onerror",'');
                            clone_item.find("img").attr("src", e.target.result);
                            clone_item.find("input").val(theFile.name);
                            clone_item.appendTo(parent);
                            parent.find(".fn_dropzone").append(dropInput);
                        };
                    })(f);
                    // Read in the image file as a data URL.
                    reader.readAsDataURL(f);
                }
                $(".fn_dropzone").removeAttr("style");
            }
            $(document).on('change','.dropinput',handleFileSelect);
        }
        $(document).on("click", ".fn_remove_image", function () {
            $(this).closest("li").remove();
        });

    });

    var editor = CodeMirror.fromTextArea(document.querySelector(".fn_code_mirror01"), {
        mode: "smartymixed",
        lineNumbers: true,
        styleActiveLine: true,
        matchBrackets: false,
        enterMode: 'keep',
        indentWithTabs: false,
        indentUnit: 2,
        tabMode: 'classic',
        theme : 'monokai'
    });
    var editor1 = CodeMirror.fromTextArea(document.querySelector(".fn_code_mirror1"), {
        mode: "smartymixed",
        lineNumbers: true,
        styleActiveLine: true,
        matchBrackets: false,
        enterMode: 'keep',
        indentWithTabs: false,
        indentUnit: 2,
        tabMode: 'classic',
        theme : 'monokai'
    });
    var editor2 = CodeMirror.fromTextArea(document.querySelector(".fn_code_mirror2"), {
        mode: "smartymixed",
        lineNumbers: true,
        styleActiveLine: true,
        matchBrackets: false,
        enterMode: 'keep',
        indentWithTabs: false,
        indentUnit: 2,
        tabMode: 'classic',
        theme : 'monokai'
    });
    var editor3 = CodeMirror.fromTextArea(document.querySelector(".fn_code_mirror3"), {
        mode: "smartymixed",
        lineNumbers: true,
        styleActiveLine: true,
        matchBrackets: false,
        enterMode: 'keep',
        indentWithTabs: false,
        indentUnit: 2,
        tabMode: 'classic',
        theme : 'monokai'
    });
    var editor4 = CodeMirror.fromTextArea(document.querySelector(".fn_code_mirror4"), {
        mode: "smartymixed",
        lineNumbers: true,
        styleActiveLine: true,
        matchBrackets: false,
        enterMode: 'keep',
        indentWithTabs: false,
        indentUnit: 2,
        tabMode: 'classic',
        theme : 'monokai'
    });
    var editor5 = CodeMirror.fromTextArea(document.querySelector(".fn_code_mirror5"), {
        mode: "smartymixed",
        lineNumbers: true,
        styleActiveLine: true,
        matchBrackets: false,
        enterMode: 'keep',
        indentWithTabs: false,
        indentUnit: 2,
        tabMode: 'classic',
        theme : 'monokai'
    });
    var editor6 = CodeMirror.fromTextArea(document.querySelector(".fn_code_mirror6"), {
        mode: "smartymixed",
        lineNumbers: true,
        styleActiveLine: true,
        matchBrackets: false,
        enterMode: 'keep',
        indentWithTabs: false,
        indentUnit: 2,
        tabMode: 'classic',
        theme : 'monokai'
    });
    var editor7 = CodeMirror.fromTextArea(document.querySelector(".fn_code_mirror7"), {
        mode: "smartymixed",
        lineNumbers: true,
        styleActiveLine: true,
        matchBrackets: false,
        enterMode: 'keep',
        indentWithTabs: false,
        indentUnit: 2,
        tabMode: 'classic',
        theme : 'monokai'
    });
    var editor8 = CodeMirror.fromTextArea(document.querySelector(".fn_code_mirror8"), {
        mode: "smartymixed",
        lineNumbers: true,
        styleActiveLine: true,
        matchBrackets: false,
        enterMode: 'keep',
        indentWithTabs: false,
        indentUnit: 2,
        tabMode: 'classic',
        theme : 'monokai'
    });
    var editor9 = CodeMirror.fromTextArea(document.querySelector(".fn_code_mirror9"), {
        mode: "smartymixed",
        lineNumbers: true,
        styleActiveLine: true,
        matchBrackets: false,
        enterMode: 'keep',
        indentWithTabs: false,
        indentUnit: 2,
        tabMode: 'classic',
        theme : 'monokai'
    });
    var editor10 = CodeMirror.fromTextArea(document.querySelector(".fn_code_mirror10"), {
        mode: "smartymixed",
        lineNumbers: true,
        styleActiveLine: true,
        matchBrackets: false,
        enterMode: 'keep',
        indentWithTabs: false,
        indentUnit: 2,
        tabMode: 'classic',
        theme : 'monokai'
    });
    var editor11 = CodeMirror.fromTextArea(document.querySelector(".fn_code_mirror11"), {
        mode: "smartymixed",
        lineNumbers: true,
        styleActiveLine: true,
        matchBrackets: false,
        enterMode: 'keep',
        indentWithTabs: false,
        indentUnit: 2,
        tabMode: 'classic',
        theme : 'monokai'
    });
    var editor12 = CodeMirror.fromTextArea(document.querySelector(".fn_code_mirror12"), {
        mode: "smartymixed",
        lineNumbers: true,
        styleActiveLine: true,
        matchBrackets: false,
        enterMode: 'keep',
        indentWithTabs: false,
        indentUnit: 2,
        tabMode: 'classic',
        theme : 'monokai'
    });
    var editor13 = CodeMirror.fromTextArea(document.querySelector(".fn_code_mirror13"), {
        mode: "smartymixed",
        lineNumbers: true,
        styleActiveLine: true,
        matchBrackets: false,
        enterMode: 'keep',
        indentWithTabs: false,
        indentUnit: 2,
        tabMode: 'classic',
        theme : 'monokai'
    });
    var editor14 = CodeMirror.fromTextArea(document.querySelector(".fn_code_mirror14"), {
        mode: "smartymixed",
        lineNumbers: true,
        styleActiveLine: true,
        matchBrackets: false,
        enterMode: 'keep',
        indentWithTabs: false,
        indentUnit: 2,
        tabMode: 'classic',
        theme : 'monokai'
    });
    var editor15 = CodeMirror.fromTextArea(document.querySelector(".fn_code_mirror15"), {
        mode: "smartymixed",
        lineNumbers: true,
        styleActiveLine: true,
        matchBrackets: false,
        enterMode: 'keep',
        indentWithTabs: false,
        indentUnit: 2,
        tabMode: 'classic',
        theme : 'monokai'
    });
    var editor16 = CodeMirror.fromTextArea(document.querySelector(".fn_code_mirror16"), {
        mode: "smartymixed",
        lineNumbers: true,
        styleActiveLine: true,
        matchBrackets: false,
        enterMode: 'keep',
        indentWithTabs: false,
        indentUnit: 2,
        tabMode: 'classic',
        theme : 'monokai'
    });
    var editor17 = CodeMirror.fromTextArea(document.querySelector(".fn_code_mirror17"), {
        mode: "smartymixed",
        lineNumbers: true,
        styleActiveLine: true,
        matchBrackets: false,
        enterMode: 'keep',
        indentWithTabs: false,
        indentUnit: 2,
        tabMode: 'classic',
        theme : 'monokai'
    });
    var editor18 = CodeMirror.fromTextArea(document.querySelector(".fn_code_mirror18"), {
        mode: "smartymixed",
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

