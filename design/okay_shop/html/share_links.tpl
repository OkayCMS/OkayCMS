{* sj_shares: array of enabled ids (copy_url, facebook, ...). If empty, show all. *}
{assign var="enabled_shares" value=$settings->sj_shares|default:[]}
{assign var="show_all" value=($enabled_shares|@count == 0)}

<div class="share__buttons">
    {* Share link — hardcoded block, always first *}
    {if $show_all || in_array('copy_url', $enabled_shares)}
    <button class="fn_button_copy share__link" data-content="{$share_url|escape}" type="button">
        <img class="copy_url__icon" src="{$rootUrl}/design/{get_theme}/images/default/copy.svg" width="32" height="32" alt="" />
        <img class="copy_url__icon_success hidden" src="{$rootUrl}/design/{get_theme}/images/default/copy_success.svg" width="32" height="32" alt="" />
    </button>
    {/if}

    {* Share networks from JsSocial *}
    {assign var="url_esc" value=$share_url|escape:'url'}
    {assign var="title_esc" value=$share_title|escape:'url'}
    {foreach $share_networks as $net}
        {if $show_all || in_array($net.id, $enabled_shares)}
            {if $net.shareUrlMobile && ($is_mobile || $is_tablet)}
                {assign var="share_href" value=$net.shareUrlMobile|replace:'{url}':$url_esc|replace:'{title}':$title_esc}
            {else}
                {assign var="share_href" value=$net.shareUrl|replace:'{url}':$url_esc|replace:'{title}':$title_esc}
            {/if}
            <a class="share__link" href="{$share_href}" target="_blank" rel="noopener" aria-label="{$lang->product_share|escape} {$net.label|escape}">
                <img src="{$rootUrl}/design/{get_theme}/images/{$share_icons_theme}/{$net.logo}.svg" width="32" height="32" alt="{$net.label|escape}" loading="lazy" />
            </a>
        {/if}
    {/foreach}
</div>
