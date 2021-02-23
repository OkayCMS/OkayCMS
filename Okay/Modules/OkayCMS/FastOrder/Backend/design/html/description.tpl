{$meta_title = $btr->okay_cms__fast_order__title|escape scope=global}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {$btr->okay_cms__fast_order__title|escape}
            </div>
        </div>
    </div>
</div>

<div class="row d_flex">
    <div class="col-lg-12 col-md-12">
        <div class="alert alert--icon">
            <div class="alert__content">
                <div class="alert__title">{$btr->alert_description|escape}</div>
                <p>{$btr->okay_cms__fast_order__description|escape}</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="boxed">
            <h3 class="">{$btr->okay_cms__fast_order__code|escape}: {literal}{fast_order_btn product=$product}{/literal}</h3>
        </div>
    </div>
</div>
