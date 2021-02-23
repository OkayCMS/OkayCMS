<div class="order_history">
    <div class="order_history__item">
        <div class="order_history__icon order_history__icon--success">{include file='svg_icon.tpl' svgId='check'}</div>
        <div class="order_history__content">
            <div class="boxed__content">
                <div class="order_history__title">
                    <span>{$btr->order_history_created}</span>
                    <span class="tag tag-chanel_unknown">{$order->date|date} | {$order->date|time}</span>
                    {if $order->referer_channel}
                        <span>{$btr->order_from}</span>
                        {if $order->referer_channel == Okay\Core\UserReferer\UserReferer::CHANNEL_EMAIL}
                            <span class="tag tag-chanel_email" title="{$order->referer_source}">
                                {include file='svg_icon.tpl' svgId='tag_email'} {$order->referer_channel}
                            </span>
                        {elseif $order->referer_channel == Okay\Core\UserReferer\UserReferer::CHANNEL_SEARCH}
                            <span class="tag tag-chanel_search" title="{$order->referer_source}">
                                {include file='svg_icon.tpl' svgId='tag_search'} {$order->referer_channel}
                            </span>
                        {elseif $order->referer_channel == Okay\Core\UserReferer\UserReferer::CHANNEL_SOCIAL}
                            <span class="tag tag-chanel_social" title="{$order->referer_source}">
                                {include file='svg_icon.tpl' svgId='tag_social'} {$order->referer_channel}
                            </span>
                        {elseif $order->referer_channel == Okay\Core\UserReferer\UserReferer::CHANNEL_REFERRAL}
                            <span class="tag tag-chanel_referral" title="{$order->referer_source}">
                                {include file='svg_icon.tpl' svgId='tag_referral'} {$order->referer_channel}
                            </span>
                        {else}
                            <span class="tag tag-chanel_unknown" title="{$order->referer_source}">
                                {include file='svg_icon.tpl' svgId='tag_unknown'} {$order->referer_channel}
                            </span>
                        {/if}
                    {/if}
                </div>
            </div>
        </div>
    </div>
    {if $order->comment}
        <div class="order_history__item">
            <div class="order_history__icon">{include file='svg_icon.tpl' svgId='left_comments'}</div>
            <div class="order_history__content">
                <div class="order_history__title">
                    {$btr->order_client_comment|escape}
                </div>
                <div class="boxed boxed--success">
                    <div class="boxed__content">
                        {$order->comment}
                    </div>
                </div>
            </div>
        </div>
    {/if}

    {if $order_history}
        {foreach $order_history as $history_item}
            {if $history_item->new_status_id}
                <div class="order_history__item">
                    <div class="order_history__icon order_history__icon--exchange">{include file='svg_icon.tpl' svgId='exchange'}</div>
                    <div class="order_history__content">
                        <div class="order_history__title">
                            <span>{$btr->order_history_changed_on}</span>
                            <span style="color: #{$all_status[$history_item->new_status_id]->color};">{$all_status[$history_item->new_status_id]->name|escape}</span>
                            <span class="tag tag-chanel_unknown">{$history_item->date|date} | {$history_item->date|time}</span>
                            <span>{$btr->order_history_by_manager|escape}</span>
                            <span>{$history_item->manager_name|escape}</span>
                        </div>
                    </div>

                </div>
            {else}
                <div class="order_history__item">
                    <div class="order_history__icon">{include file='svg_icon.tpl' svgId='left_comments'}</div>
                    <div class="order_history__content">
                        <div class="order_history__title">
                            <span>{$btr->order_history_changed|escape}</span>
                            <span class="tag tag-chanel_unknown">{$history_item->date|date} | {$history_item->date|time}</span>
                            <span>{$btr->order_history_by_manager|escape}</span>
                            <span>{$history_item->manager_name|escape}</span>
                        </div>
                        <div class="boxed boxed--grey">
                            <div class="boxed__content">
                                {$history_item->text}
                            </div>
                        </div>
                    </div>
                </div>
            {/if}
        {/foreach}
    {/if}
</div>