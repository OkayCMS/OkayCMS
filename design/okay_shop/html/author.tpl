<div class="clearfix">
    {* Sidebar with blog *}
    <div class="fn_mobile_toogle sidebar sidebar--right position_sticky d-lg-flex flex-lg-column">
        {include 'blog_sidebar.tpl'}
    </div>
    <div class="blog_container blog_container--left d-flex flex-column">
        <div class="blog_container__boxed author_card">
            <div class="author_card__image">
                {if $author->image}
                <a data-fancybox="author_image" href="{$author->image|resize:800:800:false:$config->resized_authors_dir}">
                    <picture>
                        {if $settings->support_webp}
                            <source type="image/webp" data-srcset="{$author->image|resize:320:500:false:$config->resized_authors_dir}.webp">
                        {/if}
                        <source data-srcset="{$author->image|resize:320:500:false:$config->resized_authors_dir}">
                        <img class="lazy" data-src="{$author->image|resize:320:500:false:$config->resized_authors_dir}" src="{$rootUrl}/design/{get_theme}/images/xloading.gif" alt="{$author->name|escape}" title="{$author->name|escape}"/>
                    </picture>
                </a>
                {else}
                <div class="author_card__no_image d-flex align-items-start">
                    {include file="svg.tpl" svgId="comment-user_icon"}
                </div>
                {/if}
            </div>
            <div class="author_card__info">
                <h1 class="author_card__name h1">
                    <span data-author="{$author->id}">{$h1|escape}</span>
                </h1>
                {if $author->position_name}
                <div class="author_card__position">
                    {$author->position_name|escape}
                </div>
                {/if}
                {if is_array($author->socials)}
                <div class="author_card__social">
                    {foreach $author->socials as $social}
                    <a class="fn_social_image social__link {$social.domain|escape}" rel="noreferrer" aria-label="{$social_domain}" href="{if !preg_match('~^https?://.*$~', $social.url)}https://{/if}{$social.url|escape}" target="_blank" title="{$social.domain|escape}">
                        <i class="fa fa-{$social.domain|escape}"></i>
                    </a>
                    {/foreach}
                </div>
                {/if}
                {if $description}
                <div class="author_card__description">{$description}</div>
                {/if}
            </div>
        </div>
        <div class="block block--boxed block--border">
            <div class="block__header">
                <div class="block__title">
                    <span data-language="author_posts">{$lang->author_posts}</span>
                </div>
            </div>
            <div class="block__body article">
                <div class="article_list f_row">
                    {foreach $posts as $post}
                    <div class="article_item f_col-sm-6 f_col-lg-4">
                        {include 'post_list.tpl'}
                    </div>
                    {/foreach}
                </div>
            </div>
            {* Pagination *}
            <div class="products_pagination">
                {include file='pagination.tpl'}
            </div>
        </div>
    </div>
</div>
