<div class="box_adswitch">

    {*Импорт товаров*}
    {if $smarty.get.controller == 'ImportAdmin'}
        <a class="btn_admin btn_vid_help hint-bottom-middle-t-info-s-small-mobile  hint-anim" data-hint="{$btr->video_help}" target="_blank" href="https://youtu.be/B0PnB-8VMcs">
            {include file='svg_icon.tpl' svgId='video_icon'}
            {*<span class="btn_vid_help_text quickview_hidden">{$btr->video_help}</span>*}
        </a>

    {*SEO товаров или автоматизация SEO*}
    {elseif $smarty.get.controller == 'SeoPatternsAdmin'}
        <a class="btn_admin btn_vid_help hint-bottom-middle-t-info-s-small-mobile  hint-anim" data-hint="{$btr->video_help}" target="_blank" href="https://youtu.be/5TfYmhwncss">
            {include file='svg_icon.tpl' svgId='video_icon'}
        </a>

    {*Настройка сайта*}
    {elseif $smarty.get.controller == 'SettingsGeneralAdmin'}
        <a class="btn_admin btn_vid_help hint-bottom-middle-t-info-s-small-mobile  hint-anim" data-hint="{$btr->video_help}" target="_blank" href="https://youtu.be/l5xHrK52Rqw">
            {include file='svg_icon.tpl' svgId='video_icon'}
        </a>

    {*Настройка уведомлений*}
    {elseif $smarty.get.controller == 'SettingsNotifyAdmin'}
        <a class="btn_admin btn_vid_help hint-bottom-middle-t-info-s-small-mobile  hint-anim" data-hint="{$btr->video_help}" target="_blank" href="https://youtu.be/VtM8xV4J84s">
            {include file='svg_icon.tpl' svgId='video_icon'}
        </a>

    {*Настройка каталога*}
    {elseif $smarty.get.controller == 'SettingsCatalogAdmin'}
        <a class="btn_admin btn_vid_help hint-bottom-middle-t-info-s-small-mobile  hint-anim" data-hint="{$btr->video_help}" target="_blank" href="https://youtu.be/pfSCyAgWdU0">
            {include file='svg_icon.tpl' svgId='video_icon'}
        </a>

    {*Способы оплаты*}
    {elseif $smarty.get.controller == 'PaymentMethodsAdmin' || $smarty.get.controller == 'PaymentMethodAdmin'}
        <a class="btn_admin btn_vid_help hint-bottom-middle-t-info-s-small-mobile  hint-anim" data-hint="{$btr->video_help}" target="_blank" href="https://youtu.be/1MfdlulArkA">
            {include file='svg_icon.tpl' svgId='video_icon'}
        </a>

    {*Способы доставки*}
    {elseif $smarty.get.controller == 'DeliveriesAdmin' || $smarty.get.controller == 'DeliveryAdmin'}
        <a class="btn_admin btn_vid_help hint-bottom-middle-t-info-s-small-mobile  hint-anim" data-hint="{$btr->video_help}" target="_blank" href="https://youtu.be/W1qEAb0RbD4">
            {include file='svg_icon.tpl' svgId='video_icon'}
        </a>

    {*Менеджеры*}
    {elseif $smarty.get.controller == 'ManagersAdmin'}
        <a class="btn_admin btn_vid_help hint-bottom-middle-t-info-s-small-mobile  hint-anim" data-hint="{$btr->video_help}" target="_blank" href="https://youtu.be/2SdCZ9NmVPM">
            {include file='svg_icon.tpl' svgId='video_icon'}
        </a>

    {*Валюты*}
    {elseif $smarty.get.controller == 'CurrencyAdmin'}
        <a class="btn_admin btn_vid_help hint-bottom-middle-t-info-s-small-mobile  hint-anim" data-hint="{$btr->video_help}" target="_blank" href="https://youtu.be/CZPSSlnjXFs">
            {include file='svg_icon.tpl' svgId='video_icon'}
        </a>

    {*Покупатели, подписчики *}
    {elseif $smarty.get.controller == 'SubscribeMailingAdmin'}
        <a class="btn_admin btn_vid_help hint-bottom-middle-t-info-s-small-mobile  hint-anim" data-hint="{$btr->video_help}" target="_blank" href="https://youtu.be/dl53ep5e8XE">
            {include file='svg_icon.tpl' svgId='video_icon'}
        </a>

    {*Настройка заказов*}
    {elseif $smarty.get.controller == 'OrderSettingsAdmin'}
        <a class="btn_admin btn_vid_help hint-bottom-middle-t-info-s-small-mobile  hint-anim" data-hint="{$btr->video_help}" target="_blank" href="https://youtu.be/zGU9mDfdUqM">
            {include file='svg_icon.tpl' svgId='video_icon'}
        </a>

    {*Список заказов *}
    {elseif $smarty.get.controller == 'OrdersAdmin'}
        <a class="btn_admin btn_vid_help hint-bottom-middle-t-info-s-small-mobile  hint-anim" data-hint="{$btr->video_help}" target="_blank" href="https://youtu.be/fh6XnFrchAc">
            {include file='svg_icon.tpl' svgId='video_icon'}
        </a>

    {*Работа с заказом *}
    {elseif $smarty.get.controller == 'OrderAdmin'}
        <a class="btn_admin btn_vid_help hint-bottom-middle-t-info-s-small-mobile  hint-anim" data-hint="{$btr->video_help}" target="_blank" href="https://youtu.be/-mkBI-Q6Snk">
            {include file='svg_icon.tpl' svgId='video_icon'}
        </a>

    {*Работа со списком товаров*}
    {elseif $smarty.get.controller == 'ProductsAdmin'}
        <a class="btn_admin btn_vid_help hint-bottom-middle-t-info-s-small-mobile  hint-anim" data-hint="{$btr->video_help}" target="_blank" href="https://youtu.be/IETA0dyLaOE">
            {include file='svg_icon.tpl' svgId='video_icon'}
        </a>

    {*Товары*}
    {elseif $smarty.get.controller == 'ProductAdmin'}
        <a class="btn_admin btn_vid_help hint-bottom-middle-t-info-s-small-mobile  hint-anim" data-hint="{$btr->video_help}" target="_blank" href="https://youtu.be/i1wZ130rsq8">
            {include file='svg_icon.tpl' svgId='video_icon'}
        </a>

    {*Список страниц*}
    {elseif $smarty.get.controller == 'PagesAdmin'}
        <a class="btn_admin btn_vid_help hint-bottom-middle-t-info-s-small-mobile  hint-anim" data-hint="{$btr->video_help}" target="_blank" href="https://youtu.be/4paNOW__dR0">
            {include file='svg_icon.tpl' svgId='video_icon'}
        </a>

    {*Обратный звонок*}
    {elseif $smarty.get.controller == 'CallbacksAdmin'}
        <a class="btn_admin btn_vid_help hint-bottom-middle-t-info-s-small-mobile  hint-anim" data-hint="{$btr->video_help}" target="_blank" href="https://youtu.be/0-SA7NFszFg">
            {include file='svg_icon.tpl' svgId='video_icon'}
        </a>

    {*Купоны*}
    {elseif $smarty.get.controller == 'CouponsAdmin'}
        <a class="btn_admin btn_vid_help hint-bottom-middle-t-info-s-small-mobile  hint-anim" data-hint="{$btr->video_help}" target="_blank" href="https://youtu.be/U6vhBPOB5lY">
            {include file='svg_icon.tpl' svgId='video_icon'}
        </a>

    {*Комментарии*}
    {elseif $smarty.get.controller == 'CommentsAdmin'}
        <a class="btn_admin btn_vid_help hint-bottom-middle-t-info-s-small-mobile  hint-anim" data-hint="{$btr->video_help}" target="_blank" href="https://youtu.be/ozO8CyXeW7Y">
            {include file='svg_icon.tpl' svgId='video_icon'}
        </a>

    {*Категории*}
    {elseif $smarty.get.controller == 'CategoriesAdmin' || $smarty.get.controller == 'CategoryAdmin'}
        <a class="btn_admin btn_vid_help hint-bottom-middle-t-info-s-small-mobile  hint-anim" data-hint="{$btr->video_help}" target="_blank" href="https://youtu.be/B0oc25RkE3U">
            {include file='svg_icon.tpl' svgId='video_icon'}
        </a>

    {*Бренды*}
    {elseif $smarty.get.controller == 'BrandsAdmin' || $smarty.get.controller == 'BrandsAdmin'}
        <a class="btn_admin btn_vid_help hint-bottom-middle-t-info-s-small-mobile  hint-anim" data-hint="{$btr->video_help}" target="_blank" href="https://youtu.be/-vxM0bR8yHg">
            {include file='svg_icon.tpl' svgId='video_icon'}
        </a>

    {*Блог*}
    {elseif $smarty.get.controller == 'BlogAdmin' || $smarty.get.controller == 'PostAdmin'}
        <a class="btn_admin btn_vid_help hint-bottom-middle-t-info-s-small-mobile  hint-anim" data-hint="{$btr->video_help}" target="_blank" href="https://youtu.be/JnhkMaI9Tto">
            {include file='svg_icon.tpl' svgId='video_icon'}
        </a>

    {*Создание группы и баннера*}
    {elseif $smarty.get.controller == 'BannersAdmin' || $smarty.get.controller == 'BannerAdmin' || $smarty.get.controller == 'BannersImagesAdmin' || $smarty.get.controller == 'BannersImageAdmin'}
        <a class="btn_admin btn_vid_help hint-bottom-middle-t-info-s-small-mobile  hint-anim" data-hint="{$btr->video_help}" target="_blank" href="https://youtu.be/S9AkoK6sQP4">
            {include file='svg_icon.tpl' svgId='video_icon'}
        </a>

    {*Seo – синонимы свойства*}
    {elseif $smarty.get.controller == 'FeaturesAliasesAdmin'}
        <a class="btn_admin btn_vid_help hint-bottom-middle-t-info-s-small-mobile  hint-anim" data-hint="{$btr->video_help}" target="_blank" href="https://youtu.be/cK42rT3-MpE">
            {include file='svg_icon.tpl' svgId='video_icon'}
        </a>

    {*Seo – seo фильтров*}
    {elseif $smarty.get.controller == 'SeoFilterPatternsAdmin'}
        <a class="btn_admin btn_vid_help hint-bottom-middle-t-info-s-small-mobile  hint-anim" data-hint="{$btr->video_help}" target="_blank" href="https://youtu.be/jkPemJqETJg">
            {include file='svg_icon.tpl' svgId='video_icon'}
        </a>

    {*Seo - Пользовательские скрипты *}
    {elseif $smarty.get.controller == 'SettingsCounterAdmin'}
        <a class="btn_admin btn_vid_help hint-bottom-middle-t-info-s-small-mobile  hint-anim" data-hint="{$btr->video_help}" target="_blank" href="https://youtu.be/nabCRKyzSTA">
            {include file='svg_icon.tpl' svgId='video_icon'}
        </a>

    {*Seo – robots.txt*}
    {elseif $smarty.get.controller == 'RobotsAdmin'}
        <a class="btn_admin btn_vid_help hint-bottom-middle-t-info-s-small-mobile  hint-anim" data-hint="{$btr->video_help}" target="_blank" href="https://youtu.be/Mx05vxRQ-nM">
            {include file='svg_icon.tpl' svgId='video_icon'}
        </a>

    {else}
        {*<a class="btn_admin btn_vid_help hint-bottom-middle-t-info-s-small-mobile  hint-anim" data-hint="{$btr->video_help}" href="https://www.youtube.com/channel/UCxqbdarNc5wJVw2PM6Q4HUQ">
            {include file='svg_icon.tpl' svgId='video_icon'}
        </a>*}
    {/if}

</div>
