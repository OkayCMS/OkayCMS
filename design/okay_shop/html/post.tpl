{* Post page *}

<div class="d-lg-flex align-items-lg-start justify-content-lg-between flex-lg-row-reverse">
    {* Sidebar with post *}
    <div class="fn_mobile_toogle sidebar sidebar--right position_sticky d-lg-flex flex-lg-column">
        {include 'blog_sidebar.tpl'}
    </div>

    {* Content with post *}
    <div class="post_container post_container--left">
        <div class="post_container__wrapper">
            <div class="post_container__wallpaper" style="background: #F4F6F9 url({$post->image|resize:1100:600:false:$config->resized_blog_dir}) no-repeat center center;background-size: cover;">
                {* Post category label *}
                {if !empty($post->categories)}
                    <div class="post__labels">
                        {foreach $post->categories as $c}
                            {if $c->visible}
                                <a class="post__label" href="{url_generator route='blog_category' url=$c->url}">{$c->name|escape}</a>
                            {/if}
                        {/foreach}
                    </div>
                {/if}
            </div>
            <div class="post_container__boxed">
                <div class="post_container__header">
                    {* The page heading *}
                    <h1 class="post__heading">
                        <span data-post="{$post->id}">{$h1|escape}</span>
                    </h1>
                    {* Mobile button catalog *}
                    <div class="fn_switch_mobile_filter switch_mobile_filter hidden-lg-up">
                        {include file="svg.tpl" svgId="catalog_icon"}
                        <span data-language="blog_catalog">{$lang->blog_catalog}</span>
                    </div>
                    <div class="post_information">
                        {* Article author *}
                        {if $post->author}
                        <div class="post_information__item" title="{$lang->blog_author}">
                            
                            <div class="post_information__avatar">
                                {if $post->author->image}
                                    <img src="{$post->author->image|resize:30:30:false:$config->resized_authors_dir:center:center}" alt="{$post->author->name|escape}">
                                {else}
                                    {include file="svg.tpl" svgId="avatar_icon"}
                                {/if}
                            </div>
                            {if $post->author->visible}
                                <a href="{url_generator route='author' url=$post->author->url}">{$post->author->name|escape}</a>
                            {else}
                                <span>{$post->author->name|escape}</span>
                            {/if}
                        </div>
                        {/if}
                        {* Post date *}
                        {if $post->date}
                            <div class="post_information__item" title="{$lang->blog_date_public}">
                                {include file="svg.tpl" svgId="calendar_icon"}
                                <span>{$post->date|date:"d cFR Y, cD"}</span>
                            </div>
                        {/if}
                        {* Reading time *}
                        {if $post->read_time > 0}
                            <div class="post_information__item" title="{$lang->blog_time_read} {if $post->read_time > 0}{$post->read_time} {$post->read_time|plural:$lang->blog_time_read_minute_1:$lang->blog_time_read_minute_2:$lang->blog_time_read_minute_3}{else}{$lang->blog_unknown_time_read}{/if}">
                                {include file="svg.tpl" svgId="time_read_icon"}
                                <span>{$lang->blog_time_read} {$post->read_time} {$post->read_time|plural:$lang->blog_time_read_minute_1:$lang->blog_time_read_minute_2:$lang->blog_time_read_minute_3}</span>
                            </div>
                        {/if}
                        {* Counts of comments *}
                        <div class="post_information__item" title="{$lang->blog_count_comments}">
                            {include file="svg.tpl" svgId="chat_icon"}
                            <span>{$comments|count}</span>
                        </div>
                    </div>
                    {* Update date *}
                    {if $post->updated_date > 0}
                        <div class="post__update_date">
                            {include file="svg.tpl" svgId="update_date_icon"}
                            <span class="post__update_date_text">{$lang->blog_update_date} {$post->updated_date|date:"d cFR Y, cD"}</span>
                        </div>
                    {/if}
                    {* Table contents *}
                    {if !empty($table_of_content)}
                        <div class="post__table_contents">
                            <div class="post__table_contents_title">{$lang->blog_table_contents}</div>
                            <ol>
                                {foreach $table_of_content as $content_item}
                                    <li style="margin-left: {$content_item.header_level*15-15}px">
                                        <a class="fn_ancor_post" href="{$content_item.url|escape}">{$content_item.anchor_text|escape}</a>
                                    </li>
                                {/foreach}
                            </ol>
                        </div>
                    {/if}
                </div>
                <div class="post_container__body">
                    {* Post content *}
                    <div class="block__description block__description--style">
                        {$description}
                    </div>

                </div>
                <div class="post_container__footer">
                    {* Post tags *}
                    {if !empty($post->categories)}
                    <div class="post_tags">
                        {include file="svg.tpl" svgId="tag_icon"}
                        {foreach $post->categories as $c}
                        {if $c->visible}
                        <a class="post_tag" href="{url_generator route='blog_category' url=$c->url}">{$c->name|escape}</a>
                        {/if}
                        {/foreach}
                    </div>
                    {/if}

                    <div class="post_share">
                        <div id="post_{$post->id}" class="post__rating product__rating fn_rating" data-rating_post_url="{url_generator route='ajax_post_rating'}">
                            <div class="share__text post_share__text">
                                <span data-language="product_share">{$lang->post_rating_title}</span>
                            </div>
                            <span class="rating_starOff">
                                <span class="rating_starOn" style="width:{$post->rating*90/5|string_format:'%.0f'}px;"></span>
                            </span>
                            {*Вывод количества голосов данного товара, скрыт ради микроразметки*}
                            {if $post->rating > 0}
                            <span class="rating_text">( <span>{$post->votes|string_format:"%.0f"}</span> )</span>
                            <span class="rating_text hidden">( <span>{$post->rating|string_format:"%.1f"}</span> )</span>
                            {*Вывод лучшей оценки товара для микроразметки*}
                            <span class="rating_text hidden" style="display:none;">5</span>
                            {else}
                            <span class="rating_text hidden">({$post->rating|string_format:"%.1f"})</span>
                            {/if}
                        </div>

                        {* Share buttons *}
                        <div class="post_share_boxed">
                            <div class="share">
                                {*<div class="share__text post_share__text">
                                <span data-language="product_share">{$lang->product_share}:</span>
                            </div>*}
                                <div class="fn_share jssocials share__icons"></div>
                            </div>
                        </div>
                    </div>

                    {* Article author *}
                    {if $post->author}
                    <div class="post_author">
                        <div class="post_author__images">
                            <div class="post_author__img">
                                {if $post->author->image}
                                <img src="{$post->author->image|resize:100:100:false:$config->resized_authors_dir:center:center}" alt="{$post->author->name|escape}">
                                {else}
                                {include file="svg.tpl" svgId="avatar_icon"}
                                {/if}
                            </div>
                        </div>
                        <div class="post_author__infobox">
                            <div class="post_author__name">
                                {if $post->author->visible}
                                <a href="{url_generator route='author' url=$post->author->url}">{$post->author->name|escape}</a>
                                {else}
                                <span>{$post->author->name|escape}</span>
                                {/if}
                            </div>
                            {if $post->author->position_name}
                            <div class="post_author__position">
                                {$post->author->position_name|escape}
                            </div>
                            {/if}

                            {if is_array($post->author->socials)}
                            <div class="post_author__social">
                                {foreach $post->author->socials as $social}
                                <a class="fn_social_image social__link {$social.domain|escape}" rel="noreferrer" aria-label="{$social_domain}" href="{if !preg_match('~^https?://.*$~', $social.url)}https://{/if}{$social.url|escape}" target="_blank" title="{$social.domain|escape}">
                                    <i class="fa fa-{$social.domain|escape}"></i>
                                </a>
                                {/foreach}
                            </div>
                            {/if}
                        </div>
                    </div>
                    {/if}

                    {* Previous/Next posts *}
                    {if $prev_post || $next_post}
                    <nav>
                        <ol class="pager row">
                            <li class="col-xs-12{if $next_post} col-sm-6{else} col-sm-12{/if}">
                                {if $prev_post}
                                {$prev_post_url = {url_generator route='post' url=$prev_post->url}}
                                <a class="" href="{$prev_post_url}">
                                    {include file="svg.tpl" svgId="arrow_up_icon"}
                                    <span>{$prev_post->name}</span>
                                </a>
                                {/if}
                            </li>
                            <li class="col-xs-12 col-sm-6">
                                {if $next_post}
                                {$next_post_url = {url_generator route='post' url=$next_post->url}}
                                <a href="{$next_post_url}">
                                    <span>{$next_post->name}</span>
                                    {include file="svg.tpl" svgId="arrow_up_icon"}
                                </a>
                                {/if}
                            </li>
                        </ol>
                    </nav>
                    {/if}
                </div>
            </div>
        </div>

        {* Related products *}
        {if $related_products}
        <div class="block block--boxed block--border">
            <div class="block__header">
                <div class="block__title">
                    <span data-language="product_recommended_products">{$lang->product_recommended_products}</span>
                </div>
            </div>

            <div class="block__body">
                <div class="products_list row">
                    {foreach $related_products as $p}
                    <div class="product_item col-xs-6 col-sm-3 col-md-3 col-xl-4">
                        {include "product_list.tpl" product = $p}
                    </div>
                    {/foreach}
                </div>
            </div>
        </div>
        {/if}

        <div class="block block--boxed block--border">
            <div class="block__header">
                <div class="block__title">
                    <span data-language="post_comments">{$lang->post_comments}</span>
                </div>
            </div>

            <div id="comments">
                <div class="comment-wrap ">
                    <div class="comment ">
                        {if $comments}
                        {function name=comments_tree level=0}
                        {foreach $comments as $comment}
                        <div class="comment__item {if $level > 0} admin_note{/if}">
                            {* Comment anchor *}
                            <a name="comment_{$comment->id}"></a>
                            {* Comment list *}
                            <div class="comment__inner">
                                <div class="comment__icon">
                                    {if $level > 0}
                                    {include file="svg.tpl" svgId="comment-admin_icon"}
                                    {else}
                                    {include file="svg.tpl" svgId="comment-user_icon"}
                                    {/if}
                                </div>
                                <div class="comment__boxed">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between comment__header">
                                        {* Comment name *}
                                        <div class="d-flex flex-wrap align-items-center comment__author">
                                            <span class="comment__name">{$comment->name|escape}</span>
                                            {* Comment status *}
                                            {if !$comment->approved}
                                            <span class="comment__status" data-language="post_comment_status">({$lang->post_comment_status})</span>
                                            {/if}
                                        </div>
                                        {* Comment date *}
                                        <div class="comment__date">
                                            <span>{$comment->date|date}, {$comment->date|time}</span>
                                        </div>
                                    </div>

                                    {* Comment content *}
                                    <div class="comment__body">
                                        {$comment->text|escape|nl2br}
                                    </div>
                                </div>
                            </div>
                            {if !empty($comment->children)}
                            {comments_tree comments=$comment->children level=$level+1}
                            {/if}
                        </div>
                        {/foreach}
                        {/function}
                        {comments_tree comments=$comments}
                        {else}
                        <div class="boxed boxed--big boxed--notify">
                            <span data-language="product_no_comments">{$lang->product_no_comments}</span>
                        </div>
                        {/if}
                    </div>
                    <div class="form_wrap ">
                        {* Comment form *}
                        <form id="fn_blog_comment" class="fn_validate_post form form--boxed"  method="post" action="">
                            {if $settings->captcha_type == "v3"}
                            <input type="hidden" class="fn_recaptcha_token fn_recaptchav3" name="recaptcha_token" />
                            {/if}

                            <div class="form__header">
                                <div class="form__title">
                                    {include file="svg.tpl" svgId="comment_icon"}
                                    <span data-language="post_write_comment">{$lang->post_write_comment}</span>
                                </div>
                            </div>
                            <div class="form__body">
                                {* Form error messages *}
                                {if $error}
                                <div class="message_error">
                                    {if $error=='captcha'}
                                    <span data-language="form_error_captcha">{$lang->form_error_captcha}</span>
                                    {elseif $error=='empty_name'}
                                    <span data-language="form_enter_name">{$lang->form_enter_name}</span>
                                    {elseif $error=='empty_comment'}
                                    <span data-language="form_enter_comment">{$lang->form_enter_comment}</span>
                                    {elseif $error=='empty_email'}
                                    <span data-language="form_enter_email">{$lang->form_enter_email}</span>
                                    {/if}
                                </div>
                                {/if}

                                {* User's name *}
                                <div class="form__group">
                                    <input class="form__input form__placeholder--focus" type="text" name="name" value="{if $request_data.name}{$request_data.name|escape}{elseif $user->name}{$user->name|escape}{/if}" />
                                    <span class="form__placeholder">{$lang->form_name}*</span>
                                </div>

                                {* User's email *}
                                <div class="form__group">
                                    <input class="form__input form__placeholder--focus" type="text" name="email" value="{if $request_data.email}{$request_data.email|escape}{elseif $user->email}{$user->email|escape}{/if}" data-language="form_email" />
                                    <span class="form__placeholder">{$lang->form_email}</span>
                                </div>

                                {* User's comment *}
                                <div class="form__group">
                                    <textarea class="form__textarea form__placeholder--focus" rows="3" name="text" >{$request_data.text}</textarea>
                                    <span class="form__placeholder">{$lang->form_enter_comment}*</span>
                                </div>
                            </div>
                            <div class="form__footer">
                                {* Captcha *}
                                {if $settings->captcha_comment}
                                {if $settings->captcha_type == "v2"}
                                <div class="captcha">
                                    <div id="recaptcha1"></div>
                                </div>
                                {elseif $settings->captcha_type == "default"}
                                {get_captcha var="captcha_comment"}
                                <div class="captcha">
                                    <div class="secret_number">{$captcha_comment[0]|escape} + ? =  {$captcha_comment[1]|escape}</div>
                                    <div class="form__captcha">
                                        <input class="form__input form__input_captcha form__placeholder--focus" type="text" name="captcha_code" value="" />
                                        <span class="form__placeholder">{$lang->form_enter_captcha}*</span>
                                    </div>
                                </div>
                                {/if}
                                {/if}

                                <input type="hidden" name="comment" value="1">
                                {* Submit button *}
                                <button class="form__button button--blick g-recaptcha" type="submit" name="comment" {if $settings->captcha_type == "invisible"}data-sitekey="{$settings->public_recaptcha_invisible}" data-badge='bottomleft' data-callback="onSubmit"{/if} value="{$lang->form_send}">
                                    <span  data-language="form_send">{$lang->form_send}</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{literal}
<script type="application/ld+json">

    { "@context": "http://schema.org",
        "@type": "Article",
        "mainEntityOfPage": {
            "@type": "WebPage",
            "@id": "{/literal}{$canonical}{literal}"
        },
        "headline": "{/literal}{$h1|escape}{literal}",
        "alternativeHeadline": "{/literal}{$h1|escape}{literal}",
        "image": "{/literal}{$post->image|resize:800:800:false:$config->resized_blog_dir}{literal}",
        "author": {
            "@type": "Person",
            "name": "{/literal}{$post->author->name|escape}{literal}"
        },
        "publisher": {
            "@type": "Organization",
            "name": "{/literal}{$settings->site_name|escape}{literal}",
            "logo": {
                "@type": "ImageObject",
                "url": "{/literal}{$rootUrl}/{$config->design_images}{$settings->site_logo}{literal}",
                "width": 230,
                "height": 40
            }
        },
        "url": "{/literal}{$canonical}{literal}",
        "datePublished": "{/literal}{$post->date|date_format:'%Y-%m-%d'}{literal}",
        "dateCreated": "{/literal}{$post->date|date_format:'%Y-%m-%d'}{literal}",
        {/literal}
        {if $post->updated_date > 0}
        {literal}
        "dateModified": "{/literal}{$post->updated_date|date_format:'%Y-%m-%d'}{literal}",
        {/literal}
        {else}
        {literal}
        "dateModified": "{/literal}{$post->date|date_format:'%Y-%m-%d'}{literal}",
        {/literal}
        {/if}
        {literal}
        "description": "{/literal}{$post->annotation|strip_tags|escape}{literal}",
        "articleBody": "{/literal}{$description|strip_tags|escape}{literal}"
    }

</script>
{/literal}
