
    {* Full base address *}
    <base href="{$base}/">

    {* Include fonts *}

    <link href="{$rootUrl}/design/{$settings->theme}/fonts/montserrat/Montserrat-SemiBold.woff2" rel="preload" as="font" crossorigin="anonymous" type="font/woff2">
    <link href="{$rootUrl}/design/{$settings->theme}/fonts/montserrat/Montserrat-Bold.woff2" rel="preload" as="font" crossorigin="anonymous" type="font/woff2">
    <link href="{$rootUrl}/design/{$settings->theme}/fonts/montserrat/Montserrat-Regular.woff2" rel="preload" as="font" crossorigin="anonymous" type="font/woff2">
    <link href="{$rootUrl}/design/{$settings->theme}/fonts/montserrat/Montserrat-Medium.woff2" rel="preload" as="font" crossorigin="anonymous" type="font/woff2">

    <style>
        @font-face {
            font-family: 'Montserrat';
            font-display: swap;
            src: local('Montserrat SemiBold'), local('Montserrat-SemiBold'),
            url('{$rootUrl}/design/{$settings->theme}/fonts/montserrat/Montserrat-SemiBold.woff2') format('woff2'),
            url('{$rootUrl}/design/{$settings->theme}/fonts/montserrat/Montserrat-SemiBold.woff') format('woff');
            font-weight: 600;
            font-style: normal;
        }
        @font-face {
            font-family: 'Montserrat';
            font-display: swap;
            src: local('Montserrat Bold'), local('Montserrat-Bold'),
            url('{$rootUrl}/design/{$settings->theme}/fonts/montserrat/Montserrat-Bold.woff2') format('woff2'),
            url('{$rootUrl}/design/{$settings->theme}/fonts/montserrat/Montserrat-Bold.woff') format('woff');
            font-weight: bold;
            font-style: normal;
        }
        @font-face {
            font-family: 'Montserrat';
            font-display: swap;
            src: local('Montserrat Regular'), local('Montserrat-Regular'),
            url('{$rootUrl}/design/{$settings->theme}/fonts/montserrat/Montserrat-Regular.woff2') format('woff2'),
            url('{$rootUrl}/design/{$settings->theme}/fonts/montserrat/Montserrat-Regular.woff') format('woff');
            font-weight: normal;
            font-style: normal;
        }
        @font-face {
        font-family: 'Montserrat';
        font-display: swap;
        src: local('Montserrat Medium'), local('Montserrat-Medium'),
        url('{$rootUrl}/design/{$settings->theme}/fonts/montserrat/Montserrat-Medium.woff2') format('woff2'),
        url('{$rootUrl}/design/{$settings->theme}/fonts/montserrat/Montserrat-Medium.woff') format('woff');
        font-weight: 500;
        font-style: normal;
        }
    </style>

    {$ok_head}
    
    {strip}
    <script>
        const ut_tracker = {
            start: function(name) {
                performance.mark(name + ':start');
            },
            end: function(name) {
                performance.mark(name + ':end');
                performance.measure(name, name + ':start', name + ':end');
                console.log(name + ' duration: ' + performance.getEntriesByName(name)[0].duration);
            }
        }
    </script>
    {/strip}

    {* Schema Website *}
    {literal}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org/",
        "@type": "WebSite",
        "name": "{/literal}{$settings->site_name}{literal}",
        "url": "{/literal}{url_generator route='main' absolute=1}{literal}",
        "potentialAction": {
        "@type": "SearchAction",
        "target": "{/literal}{url_generator route='search' absolute=1}{literal}?keyword={search_term_string}",
        "query-input": "required name=search_term_string"
        }
    }
    </script>
    {/literal}

    {* Schema Organization *}
    {literal}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "{/literal}{$settings->site_name}{literal}",
        "url": "{/literal}{url_generator route='main' absolute=1}{literal}",
        "logo": "{/literal}{$rootUrl}/{$config->design_images}{$settings->site_logo}{literal}"{/literal}{if $site_social}{literal},
        "sameAs": [
        {/literal}{foreach $site_social as $social}{literal}
            "{/literal}{if !preg_match('~^https?://.*$~', $social.url)}{literal}https://{/literal}{/if}{$social.url|escape}{literal}"{/literal}{if !$social@last}{literal},{/literal}{/if}{literal}
        {/literal}{/foreach}{literal}
        ]
        {/literal}{/if}{literal}
    }
    </script>
    {/literal}
    
    {* Title *}
    <title>{$meta_title|escape}</title>

    {* Meta tags *}
    {if !empty($meta_keywords)}
        <meta name="keywords" content="{$meta_keywords|escape}"/>
    {/if}
    
    {if !empty($meta_description)}
        <meta name="description" content="{$meta_description|escape}"/>
    {/if}

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    
    {if $noindex_nofollow}
        <meta name="robots" content="noindex,nofollow">
    {elseif $noindex_follow}
        <meta name="robots" content="noindex,follow">
    {else}
        <meta name="robots" content="index,follow">
    {/if}

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="generator" content="OkayCMS {$config->version}">

    {* rel prev next для блога *}
    {if $controller == "BlogController" && $total_pages_num > 1}
        {if $current_page_num == $total_pages_num}
            {if $current_page_num == 2}
                <link rel="prev" href="{url page=null}"/>
            {else}
                <link rel="prev" href="{url page=$current_page_num-1}"/>
            {/if}
        {elseif $current_page_num == 1}
            <link rel="next" href="{url page=2}"/>
        {else}
            {if $current_page_num == 2}
                <link rel="prev" href="{url page=null}"/>
            {else}
                <link rel="prev" href="{url page=$current_page_num-1}"/>
            {/if}
            <link rel="next" href="{url page=$current_page_num+1}"/>
        {/if}
    {/if}

    {* rel prev next для каталога товаров *}
    {$rel_prev_next}

    {* Product image/Post image for social networks *}
    {if $controller == 'ProductController'}
        <meta property="og:url" content="{$canonical}">
        <meta property="og:type" content="website">
        <meta property="og:title" content="{$product->name|escape}">
        <meta property="og:description" content='{$product->annotation|strip_tags|escape}'>
        <meta property="og:image" content="{$product->image->filename|resize:330:300}">
        <link rel="image_src" href="{$product->image->filename|resize:330:300}">
        {*twitter*}
        <meta name="twitter:card" content="product"/>
        <meta name="twitter:url" content="{$canonical}">
        <meta name="twitter:site" content="{$settings->site_name|escape}">
        <meta name="twitter:title" content="{$product->name|escape}">
        <meta name="twitter:description" content="{$product->annotation|strip_tags|escape}">
        <meta name="twitter:image" content="{$product->image->filename|resize:330:300}">
        <meta name="twitter:data1" content="{$lang->cart_head_price}">
        <meta name="twitter:label1" content="{$product->variant->price|convert:null:false} {$currency->code|escape}">
        <meta name="twitter:data2" content="{$lang->meta_organization}">
        <meta name="twitter:label2" content="{$settings->site_name|escape}">
    {elseif $controller == "CategoryController"} 
        <meta property="og:title" content="{$h1|escape}">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{$canonical}">
        <meta property="og:image" content="{$category->image|resize:330:300:false:$config->resized_categories_dir}">
        <meta property="og:site_name" content="{$settings->site_name|escape}">
        <meta property="og:description" content="{$description|strip_tags|escape}">
        <link rel="image_src" href="{$category->image|resize:330:300:false:$config->resized_categories_dir}">
        {*twitter*}
        <meta name="twitter:url" content="{$canonical}">
        <meta name="twitter:site" content="{$settings->site_name|escape}">
        <meta name="twitter:title" content="{$h1|escape}">
        <meta name="twitter:description" content="{$description|strip_tags|escape}">
        <meta name="twitter:image" content="{$category->image|resize:330:300:false:$config->resized_categories_dir}">
    {elseif $controller == 'BlogController' && $post}
        <meta property="og:url" content="{$canonical}">
        <meta property="og:type" content="article">
        <meta property="og:title" content="{$post->name|escape}">
        {if $post->image}
            <meta property="og:image" content="{$post->image|resize:400:300:false:$config->resized_blog_dir}">
            <link rel="image_src" href="{$post->image|resize:400:300:false:$config->resized_blog_dir}">
        {else}
            <meta property="og:image" content="{$rootUrl}/{$config->design_images}{$settings->site_logo}">
            <meta name="twitter:image" content="{$rootUrl}/{$config->design_images}{$settings->site_logo}">
        {/if}
        <meta property="og:description" content='{$post->annotation|strip_tags|escape}'>
        {*twitter*}
        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="{$post->name|escape}">
        <meta name="twitter:description" content="{$post->annotation|strip_tags|escape}">
        <meta name="twitter:image" content="{$post->image|resize:400:300:false:$config->resized_blog_dir}">
    {else}
        <meta property="og:title" content="{$settings->site_name|escape}">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{$rootUrl}">
        <meta property="og:image" content="{$rootUrl}/{$config->design_images}{$settings->site_logo}">
        <meta property="og:site_name" content="{$settings->site_name|escape}">
        <meta property="og:description" content="{$meta_description|escape}">
        <link rel="image_src" href="{$rootUrl}/{$config->design_images}{$settings->site_logo}">
        {*twitter*}
        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="{$settings->site_name|escape}">
        <meta name="twitter:description" content="{$meta_description|escape}">
        <meta name="twitter:image" content="{$rootUrl}/{$config->design_images}{$settings->site_logo}">
    {/if}

    {* The canonical address of the page *}
    {if isset($canonical)}
        <link rel="canonical" href="{$canonical|escape}">
    {/if}

    {* Language attribute *}
    {foreach $languages as $l}
        {if $l->enabled}
            <link rel="alternate" hreflang="{$l->href_lang}" href="{$l->url|escape}">
        {/if}
    {/foreach}

    {if $settings->captcha_type == "v3"}
        <script>ut_tracker.start('render:recaptcha');</script>
        <script src="https://www.google.com/recaptcha/api.js?render={$settings->public_recaptcha_v3|escape}"></script>
        <script>
            grecaptcha.ready(function () {
                {if $controller == 'ProductController' || $controller == 'BlogController'}
                    var recaptcha_action = 'product';
                {elseif $controller == 'CartController'}
                    var recaptcha_action = 'cart';
                {else}
                    var recaptcha_action = 'other';
                {/if}

                var allCaptchеs = document.getElementsByClassName('fn_recaptchav3');
                grecaptcha.execute('{$settings->public_recaptcha_v3|escape}', { action: recaptcha_action })
                    .then(function (token) {
                        for (capture of allCaptchеs) {
                            capture.value = token;
                        }
                    });
            });
        </script>
        <script>ut_tracker.end('render:recaptcha');</script>
    {elseif $settings->captcha_type == "v2"}
        <script>ut_tracker.start('render:recaptcha');</script>
        <script type="text/javascript">
            var onloadCallback = function() {
                mysitekey = "{$settings->public_recaptcha|escape}";
                if($('#recaptcha1').length>0){
                    grecaptcha.render('recaptcha1', {
                        'sitekey' : mysitekey
                    });
                }
                if($('#recaptcha2').length>0){
                    grecaptcha.render('recaptcha2', {
                        'sitekey' : mysitekey
                    });
                }
            };
        </script>
        <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
        <script>ut_tracker.end('render:recaptcha');</script>
    {elseif $settings->captcha_type == "invisible"}
        <script>ut_tracker.start('render:recaptcha');</script>
        <script>
            function onSubmit(token) {
                document.getElementById("captcha_id").submit();
            }
            function onSubmitCallback(token) {
                document.getElementById("fn_callback").submit();
            }
            function onSubmitBlog(token) {
                document.getElementById("fn_blog_comment").submit();
            }
        </script>
        <script>
            var onloadReCaptchaInvisible = function() { };
        </script>
        <script src='https://www.google.com/recaptcha/api.js?onload=onloadReCaptchaInvisible'></script>
        <script>ut_tracker.end('render:recaptcha');</script>
    {/if}

    <link rel="search" type="application/opensearchdescription+xml" title="{$rootUrl} Search" href="{url_generator route='opensearch' absolute=1}" />

    {* Favicon *}
    <link href="{$rootUrl}/{$config->design_images|escape}{$settings->site_favicon|escape}?v={$settings->site_favicon_version|escape}" type="image/x-icon" rel="icon">
    <link href="{$rootUrl}/{$config->design_images|escape}{$settings->site_favicon|escape}?v={$settings->site_favicon_version|escape}" type="image/x-icon" rel="shortcut icon">

    {* JQuery *}
    <script>ut_tracker.start('parsing:page');</script>

    {if !empty($counters['head'])}
    <script>ut_tracker.start('parsing:head:counters');</script>
    {foreach $counters['head'] as $counter}
    {$counter->code}
    {/foreach}
    <script>ut_tracker.end('parsing:head:counters');</script>
    {/if}
