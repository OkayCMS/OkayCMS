<div class="article__preview">
    <div class="article__body">
        <div class="article__image">
            <a class="article__image_link" aria-label="{$post->name|escape}" href="{url_generator route='post' url=$post->url}">
                {if $post->image}
                    <picture>
                        {if $settings->support_webp}
                            <source class="lazy" type="image/webp" data-srcset="{$post->image|resize:340:240:false:$config->resized_blog_dir:center:center}.webp" media="(max-width: 440px)" srcset="{$rootUrl}/design/{get_theme}/images/xloading.gif"> 
                            <source class="lazy" type="image/webp" data-srcset="{$post->image|resize:380:240:false:$config->resized_blog_dir:center:center}.webp" srcset="{$rootUrl}/design/{get_theme}/images/xloading.gif">
                        {/if}
                        <source class="lazy" data-srcset="{$post->image|resize:340:240:false:$config->resized_blog_dir:center:center}" media="(max-width: 440px)" srcset="{$rootUrl}/design/{get_theme}/images/xloading.gif">
                        <source class="lazy" data-srcset="{$post->image|resize:380:240:false:$config->resized_blog_dir:center:center}" srcset="{$rootUrl}/design/{get_theme}/images/xloading.gif">
                            
                        <img class="lazy" data-src="{$post->image|resize:380:240:false:$config->resized_blog_dir:center:center}" src="{$rootUrl}/design/{get_theme}/images/xloading.gif" alt="{$post->name|escape}" title="{$post->name|escape}"/>
                    </picture>
                {else}
                    <div class="article__no_image d-flex align-items-start">
                        {include file="svg.tpl" svgId="no_image"}
                    </div>
                {/if}
            </a>
            {if !empty($post->categories)}
            <div class="article__labels">
                {foreach $post->categories as $c}
                    {if $c->visible}
                        <a class="article__label" href="{url_generator route='blog_category' url=$c->url}">{$c->name|escape}</a>
                    {/if}
                {/foreach}
            </div>
            {/if}
        </div>

        <a class="article__title theme_link--color" href="{url_generator route='post' url=$post->url}" data-post="{$post->id}">{$post->name|escape}</a>

        <div class="article__info">
            <div class="article__info_item">
                {include file="svg.tpl" svgId="calendar_icon"}
                <span>{$post->date|date:"d cFR Y"}</span>
            </div>
            <div class="article__info_item " title="{$lang->blog_count_comments}">
                {include file="svg.tpl" svgId="chat_icon"}
                <span>{if $post->comments_count}{$post->comments_count}{else}0{/if}</span>
            </div>
            {if $post->read_time > 0}
                <div class="article__info_item " title="{$lang->blog_time_read} {$post->read_time} {$post->read_time|plural:$lang->blog_time_read_minute_1:$lang->blog_time_read_minute_2:$lang->blog_time_read_minute_3}">
                    {include file="svg.tpl" svgId="time_read_icon"}
                    <span>{$post->read_time} {$post->read_time|plural:$lang->blog_time_read_minute_1:$lang->blog_time_read_minute_2:$lang->blog_time_read_minute_3}</span>
                </div>
            {/if}
        </div>

        {if $post->annotation}
            <div class="article__annotation">{$post->annotation}</div>
        {/if}
    </div>
    {if !empty($post->author)}
        <div class="article__footer d-flex justify-content-between align-items-center">
            <div class="article__info_item article__info_item--author" title="{$lang->blog_author}">
                <div class="article__avatar">
                    {if $post->author->image}
                    <picture>
                        {if $settings->support_webp}
                            <source type="image/webp" data-srcset="{$post->author->image|resize:24:24:false:$config->resized_authors_dir:center:center}.webp">
                        {/if}
                        <source data-srcset="{$post->author->image|resize:24:24:false:$config->resized_authors_dir:center:center}">
                        <img class="lazy" data-src="{$post->author->image|resize:24:24:false:$config->resized_authors_dir:center:center}" src="{$rootUrl}/design/{get_theme}/images/xloading.gif" alt="{$post->author->name|escape}" title="{$post->author->name|escape}"/>
                    </picture>
                    {else}
                        {include file="svg.tpl" svgId="avatar_icon"}
                    {/if}
                </div>
                {if $post->author->visible}
                    <a class="article__author article__author--link" href="{url_generator route='author' url=$post->author->url}">{$post->author->name|escape}</a>
                {else}
                    <span class="article__author">{$post->author->name|escape}</span>
                {/if}
            </div>
        </div>
    {/if}
</div>
