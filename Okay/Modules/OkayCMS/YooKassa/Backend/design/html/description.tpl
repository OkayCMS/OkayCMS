{$meta_title = $btr->okaycms__yandex_money_api__title|escape scope=global}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {$btr->okaycms__yandex_money_api__title|escape}
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="alert alert--icon alert--info">
            <div class="alert__content">
                <div class="alert__title">{$btr->alert_info|escape}</div>
                <p>{$btr->okaycms__yandex_money_api__description_part_1}: <b>{url_generator route='OkayCMS.YooKassa.Callback' absolute=1}?action=notify</b> {$btr->okaycms__yandex_money_api__description_part_2}
                    <br><br>
                    {$btr->okaycms__yandex_money_api__description_part_3}</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="boxed">
            <div>
                <img src="{$rootUrl}/Okay/Modules/OkayCMS/YooKassa/Backend/design/images/yooKassa.jpg">
            </div>
        </div>
    </div>
</div>
