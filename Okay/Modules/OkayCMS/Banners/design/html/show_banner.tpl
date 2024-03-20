{if $banner_data->group_name == advantage}
    <div class="block block--boxed block--border">
        <div class="banner_advantages f_row">
            {foreach $banner_data->items as $bi}
                <div class="banner_advantages__item f_col-6 f_col-md-3">
                    <div class="banner_advantages__preview d-flex align-items-center">
                        {if $bi->url}
                            <a class="banner_advantages__link" aria-label="{$bi->title|escape}" href="{$bi->url|escape}" target="_blank"></a>
                        {/if}
                        <div class="banner_advantages__icon d-flex align-items-center justify-content-center">
                            <picture>
                                {if $settings->support_webp}
                                    <source type="image/webp" srcset="{$bi->image|resize:$bi->settings->getMobileWidth():$bi->settings->getMobileHeight():false:$config->resized_banners_images_dir|webp}" media="(max-width: 767px)">
                                    <source type="image/webp" srcset="{$bi->image|resize:$bi->settings->getDesktopWidth():$bi->settings->getDesktopHeight():false:$config->resized_banners_images_dir:center:center|webp}">
                                {/if}
                                <source type="image/jpg" srcset="{$bi->image|resize:$bi->settings->getMobileWidth():$bi->settings->getMobileHeight():false:$config->resized_banners_images_dir}" media="(max-width: 767px)">
                                <source type="image/jpg" srcset="{$bi->image|resize:$bi->settings->getDesktopWidth():$bi->settings->getDesktopHeight():false:$config->resized_banners_images_dir:center:center}">

                                <img src="{$bi->image|resize:$bi->settings->getDesktopWidth():$bi->settings->getDesktopHeight():false:$config->resized_banners_images_dir:center:center}" data-src-retina="{$bi->image|resize:$bi->settings->getDesktopWidth():$bi->settings->getDesktopHeight():false:$config->resized_banners_images_dir:center:center}" alt="{$bi->alt|escape}" title="{$bi->title|escape}"/>
                            </picture>
                        </div>
                        {if $bi->title}
                            <div class="banner_advantages__title">{$bi->title|escape}</div>
                        {/if}

                        {if $bi->description}
                            <div class="banner_advantages__description">{$bi->description}</div>
                        {/if}
                    </div>
                </div>
            {/foreach}
        </div>
    </div>
{else}
    <div class="fn_banner_{$banner_data->group_name|escape} banner_group banner_group--{$banner_data->id}  {if $banner_data->settings->isAsSlider()}swiper-container{/if}">
        {if $banner_data->settings->isAsSlider()}<div class="swiper-wrapper">{/if}
            {foreach $banner_data->items as $bi}
                {if ($is_mobile == true && $is_tablet == false) && $bi->image_mobile}
                    {if $bi->settings->getMobileVariantShow() == Okay\Modules\OkayCMS\Banners\DTO\BannerImageSettingsDTO::SHOW_DEFAULT}
                    <div class="banner_group__item swiper-slide banner_group-{$banner_data->id}_id-{$bi->id} banner_group__variant1" data-slide="{$banner_data->id}:{$bi->id}">
                    {elseif $bi->settings->getMobileVariantShow() == Okay\Modules\OkayCMS\Banners\DTO\BannerImageSettingsDTO::SHOW_DARK}
                    <div class="banner_group__item swiper-slide banner_group-{$banner_data->id}_id-{$bi->id} banner_group__variant2" data-slide="{$banner_data->id}:{$bi->id}">
                    {elseif $bi->settings->getMobileVariantShow() == Okay\Modules\OkayCMS\Banners\DTO\BannerImageSettingsDTO::SHOW_IMAGE_LEFT}
                    <div class="banner_group__item swiper-slide banner_group-{$banner_data->id}_id-{$bi->id} banner_group__variant3" data-slide="{$banner_data->id}:{$bi->id}">
                    {elseif $bi->settings->getMobileVariantShow() == Okay\Modules\OkayCMS\Banners\DTO\BannerImageSettingsDTO::SHOW_IMAGE_RIGHT}
                    <div class="banner_group__item swiper-slide banner_group-{$banner_data->id}_id-{$bi->id} banner_group__variant4" data-slide="{$banner_data->id}:{$bi->id}">
                    {/if}
                {else}
                    {if $bi->settings->getVariantShow() == Okay\Modules\OkayCMS\Banners\DTO\BannerImageSettingsDTO::SHOW_DEFAULT}
                    <div class="banner_group__item swiper-slide banner_group-{$banner_data->id}_id-{$bi->id} banner_group__variant1" data-slide="{$banner_data->id}:{$bi->id}">
                    {elseif $bi->settings->getVariantShow() == Okay\Modules\OkayCMS\Banners\DTO\BannerImageSettingsDTO::SHOW_DARK}
                    <div class="banner_group__item swiper-slide banner_group-{$banner_data->id}_id-{$bi->id} banner_group__variant2" data-slide="{$banner_data->id}:{$bi->id}">
                    {elseif $bi->settings->getVariantShow() == Okay\Modules\OkayCMS\Banners\DTO\BannerImageSettingsDTO::SHOW_IMAGE_LEFT}
                    <div class="banner_group__item swiper-slide banner_group-{$banner_data->id}_id-{$bi->id} banner_group__variant3" data-slide="{$banner_data->id}:{$bi->id}">
                    {elseif $bi->settings->getVariantShow() == Okay\Modules\OkayCMS\Banners\DTO\BannerImageSettingsDTO::SHOW_IMAGE_RIGHT}
                    <div class="banner_group__item swiper-slide banner_group-{$banner_data->id}_id-{$bi->id} banner_group__variant4" data-slide="{$banner_data->id}:{$bi->id}">
                    {/if}
                {/if}
                    {if $bi->url}
                    <a class="banner_group__link" aria-label="{$bi->title|escape}" href="{$bi->url|escape}" target="_blank"></a>
                    {/if}
                    <div class="banner_group__image">
                        {if ($is_mobile == true && $is_tablet == false) && $bi->image_mobile}
                            <picture>
                                {if $settings->support_webp}
                                    <source type="image/webp" srcset="{$bi->image_mobile|resize:$bi->settings->getMobileWidth():$bi->settings->getMobileHeight():false:$config->resized_banners_images_dir:center:center|webp}">
                                {/if}
                                <source type="image/jpg" srcset="{$bi->image_mobile|resize:$bi->settings->getMobileWidth():$bi->settings->getMobileHeight():false:$config->resized_banners_images_dir:center:center}">

                                <img src="{$bi->image_mobile|resize:$bi->settings->getMobileWidth():$bi->settings->getMobileHeight():false:$config->resized_banners_images_dir:center:center}" data-src-retina="{$bi->image|resize:$bi->settings->getDesktopWidth():$bi->settings->getDesktopHeight():false:$config->resized_banners_images_dir:center:center}" alt="{$bi->alt|escape}" title="{$bi->title|escape}"/>
                            </picture>
                        {else}
                            <picture>
                                {if $settings->support_webp}
                                    <source type="image/webp" srcset="{$bi->image|resize:$bi->settings->getDesktopWidth():$bi->settings->getDesktopHeight():false:$config->resized_banners_images_dir:center:center|webp}">
                                {/if}
                                <source type="image/jpg" srcset="{$bi->image|resize:$bi->settings->getDesktopWidth():$bi->settings->getDesktopHeight():false:$config->resized_banners_images_dir:center:center}">

                                <img src="{$bi->image|resize:$bi->settings->getDesktopWidth():$bi->settings->getDesktopHeight():false:$config->resized_banners_images_dir:center:center}" data-src-retina="{$bi->image|resize:$bi->settings->getDesktopWidth():$bi->settings->getDesktopHeight():false:$config->resized_banners_images_dir:center:center}" alt="{$bi->alt|escape}" title="{$bi->title|escape}"/>
                            </picture>
                        {/if}
                    </div>
                    <div class="banner_group__content">
                        <div class="banner_group__text">
                            {if $bi->title}
                                <div class="banner_group__title">{$bi->title|escape}</div>
                            {/if}

                            {if $bi->description}
                                <div class="banner_group__description">{$bi->description}</div>
                            {/if}
                        </div>
                    </div>
                </div>
            {/foreach}
            {if $banner_data->settings->isAsSlider()}
            </div>
            {if $banner_data->settings->isNav()}
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            {/if}
            {if $banner_data->settings->isDots()}
                <div class="swiper-pagination"></div>
            {/if}
        {/if}
    </div>


    {if $banner_data->settings->isAsSlider()}
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            $('.fn_banner_{$banner_data->group_name|escape}').each(function(){
                var swiper = new Swiper(this, {
                loop: {if $banner_data->settings->isLoop()}true{else}false{/if},
                {if $banner_data->settings->isAutoplay()}
                    autoplay: {
                        delay: {$banner_data->settings->getRotationSpeed()},
                    },
                {/if}
                {if $banner_data->settings->isNav()}
                    navigation: {
                        nextEl: this.querySelector('.swiper-button-next'),
                        prevEl: this.querySelector('.swiper-button-prev'),
                    },
                {/if}

                {if $banner_data->settings->isDots()}
                pagination: {
                    el: this.querySelector('.swiper-pagination'),
                    clickable: true,
                    },
                {/if}
                slidesPerView: 1,
                watchOverflow: true,
                breakpoints: {
                    320: {
                        slidesPerView: 1,
                    },
                    991: {
                        slidesPerView: 1,
                    },
                }
            });
            });
        });
    </script>
    {/if}
{/if}
