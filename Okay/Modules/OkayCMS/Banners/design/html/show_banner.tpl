<div class="fn_banner_{$banner_data->group_name|escape} banner_group banner_group--{$banner_data->id}  {if $banner_data->settings.as_slider}swiper-container{/if}">
    {if $banner_data->settings.as_slider}<div class="swiper-wrapper">{/if}
        {foreach $banner_data->items as $bi}
            {if $bi->settings.variant_show == Okay\Modules\OkayCMS\Banners\Entities\BannersImagesEntity::SHOW_DEFAULT}
            <div class="banner_group__item swiper-slide block--border banner_group__variant1" data-slide="{$banner_data->id}:{$bi->id}">
            {elseif $bi->settings.variant_show == Okay\Modules\OkayCMS\Banners\Entities\BannersImagesEntity::SHOW_DARK}
            <div class="banner_group__item swiper-slide block--border banner_group__variant2" data-slide="{$banner_data->id}:{$bi->id}">
            {elseif $bi->settings.variant_show == Okay\Modules\OkayCMS\Banners\Entities\BannersImagesEntity::SHOW_IMAGE_LEFT}
            <div class="banner_group__item swiper-slide block--border banner_group__variant3" data-slide="{$banner_data->id}:{$bi->id}">
            {elseif $bi->settings.variant_show == Okay\Modules\OkayCMS\Banners\Entities\BannersImagesEntity::SHOW_IMAGE_RIGHT}
            <div class="banner_group__item swiper-slide block--border banner_group__variant4" data-slide="{$banner_data->id}:{$bi->id}">
            {/if}
                {if $bi->url}
                <a class="banner_group__link" aria-label="{$bi->title}" href="{$bi->url|escape}" target="_blank"></a>
                {/if}
                <div class="banner_group__image">
                    <picture>
                        {if $settings->support_webp}
                            <source type="image/webp" srcset="{$bi->image|resize:$bi->settings.mobile.w:$bi->settings.mobile.h:false:$config->resized_banners_images_dir}.webp" media="(max-width: 767px)"> 
                            <source type="image/webp" srcset="{$bi->image|resize:$bi->settings.desktop.w:$bi->settings.desktop.h:false:$config->resized_banners_images_dir:center:center}.webp">
                        {/if}
                        <source type="image/jpg" srcset="{$bi->image|resize:$bi->settings.mobile.w:$bi->settings.mobile.h:false:$config->resized_banners_images_dir}" media="(max-width: 767px)">
                        <source type="image/jpg" srcset="{$bi->image|resize:$bi->settings.desktop.w:$bi->settings.desktop.h:false:$config->resized_banners_images_dir:center:center}">
                            
                        <img src="{$bi->image|resize:$bi->settings.desktop.w:$bi->settings.desktop.h:false:$config->resized_banners_images_dir:center:center}" data-src-retina="{$bi->image|resize:$bi->settings.desktop.w:$bi->settings.desktop.h:false:$config->resized_banners_images_dir:center:center}" alt="{$bi->alt}" title="{$bi->title}"/>
                    </picture>
                </div>
                <div class="banner_group__content">
                    <div class="banner_group__text">
                        {if $bi->title}
                            <div class="banner_group__title">{$bi->title}</div>
                        {/if}

                        {if $bi->description}
                            <div class="banner_group__description">{$bi->description}</div>
                        {/if}
                    </div>
                </div>
            </div>
        {/foreach}
        {if $banner_data->settings.as_slider}
        </div>
        {if isset($banner_data->settings.nav) && !empty($banner_data->settings.nav)}
            <div class="swiper-button-next "></div>
            <div class="swiper-button-prev "></div>
        {/if}
        {if isset($banner_data->settings.dots) && !empty($banner_data->settings.dots)}
            <div class="swiper-pagination"></div>
        {/if}
    {/if}
</div>


{if $banner_data->settings.as_slider}
<script>
    document.addEventListener('DOMContentLoaded', function(){
        $('.fn_banner_{$banner_data->group_name}').each(function(){
            var swiper = new Swiper(this, {
            loop: {if isset($banner_data->settings.loop) && !empty($banner_data->settings.loop)}true{else}false{/if},
            {if isset($banner_data->settings.autoplay) && !empty($banner_data->settings.autoplay)}
                autoplay: {
                    delay: {if isset($banner_data->settings.rotation_speed) && !empty($banner_data->settings.rotation_speed)}{$banner_data->settings.rotation_speed|intval}{else}2500{/if},
                },
            {/if}
            {if isset($banner_data->settings.nav) && !empty($banner_data->settings.nav)}
                navigation: {
                    nextEl: this.querySelector('.swiper-button-next'),
                    prevEl: this.querySelector('.swiper-button-prev'),
                },
            {/if}

            {if isset($banner_data->settings.dots) && !empty($banner_data->settings.dots)}
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
