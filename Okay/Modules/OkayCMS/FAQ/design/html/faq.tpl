{$canonical="{url_generator route="OkayCMS_FAQ_main" url=$product->url absolute=1}" scope=global}

<div class="block">
    {* The page heading *}
    <div class="block__header block__header--boxed block__header--border">
        <h1 class="block__heading">
            <span>{if $page->name_h1|escape}{$page->name_h1|escape}{else}{$page->name|escape}{/if}</span>
        </h1>
    </div>
    <div class="block__body block--boxed block--border">
        {if $faqs|count}
        <div class="faq">
            <ul class="fn_faq faq__list">
                {foreach $faqs as $faq}
                <li class="faq__item faq__item--boxed {if $faq@first}visible{/if}">
                    <div class="faq__question {if $faq@first}active{/if}">
                        <svg class="faq__arrow" width="20px" height="20px" viewBox="0 0 512 512"><path fill="currentColor" d="m256 512c-68.378906 0-132.667969-26.628906-181.019531-74.980469-48.351563-48.351562-74.980469-112.640625-74.980469-181.019531s26.628906-132.667969 74.980469-181.019531c48.351562-48.351563 112.640625-74.980469 181.019531-74.980469s132.667969 26.628906 181.019531 74.980469c48.351563 48.351562 74.980469 112.640625 74.980469 181.019531s-26.628906 132.667969-74.980469 181.019531c-48.351562 48.351563-112.640625 74.980469-181.019531 74.980469zm0-472c-119.101562 0-216 96.898438-216 216s96.898438 216 216 216 216-96.898438 216-216-96.898438-216-216-216zm138.285156 182-28.285156-28.285156-110 110-110-110-28.285156 28.285156 138.285156 138.285156zm0 0"/></svg>
                        <span>{$faq->question|escape}</span>
                    </div>
                    <div class="faq__content" {if $faq@first} style="display: block;"{/if}>
                        <div class="faq__answer">
                            <div>{$faq->answer}</div>
                        </div>
                    </div>
                </li>
                {/foreach}
            </ul>
        </div>
        {/if}

        {* The page body *}
        {if $page->description}
            <div class="page-description__text boxed__description">{$page->description}</div>
        {/if}
    </div>
</div>
