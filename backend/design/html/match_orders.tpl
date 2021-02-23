<div class="match_order">
    <div class="match_order__head">
        <div class="match_order__cell match_order__cell--id">
            {$btr->order_match_id}
        </div>
        <div class="match_order__cell match_order__cell--name">
            {$btr->general_name}
        </div>
        <div class="match_order__cell match_order__cell--identify match_order__cell--center hidden-sm-down">
            {$btr->order_match_by}
        </div>
        <div class="match_order__cell  match_order__cell--status match_order__cell--center hidden-sm-down">
            {$btr->general_status|escape}
        </div>
        <div class="match_order__cell  match_order__cell--prices match_order__cell--center hidden-xs-down">
            {$btr->order_match_total_price}
        </div>
    </div>
    <div class="match_order__body">
        {foreach $match_orders as $match_order}
        <div class="match_order__row">
            <div class="match_order__cell match_order__cell--id">
                <a class="text_600 mb-h" href="{url controller=OrderAdmin id=$match_order->id}">{$btr->orders_order|escape} #{$match_order->id}</a>
                <div class="hidden-md-up">
                    {if $match_order->match_by_email && $match_order->match_by_phone}
                    <div class="tag tag-ind_email">{$btr->order_match_by_email}</div>
                    <div class="tag tag-ind_phone">{$btr->order_match_by_phone}</div>
                    {elseif $match_order->match_by_email}
                    <div class="tag tag-ind_email">{$btr->order_match_by_email}</div>
                    {elseif $match_order->match_by_phone}
                    <div class="tag tag-ind_phone">{$btr->order_match_by_phone}</div>
                    {else}
                    <div class="tag tag-ind_unknown">{$btr->order_match_by_unknown}</div>
                    {/if}
                </div>
            </div>
            <div class="match_order__cell match_order__cell--name">
                <div class="text_400 mb-h">{$match_order->name}</div>
                <div class="font_12 text_600 text_grey mb-h">{$match_order->date|date}|{$match_order->date|time}</div>
                <div class="hidden-sm-up"><div class="tag tag--np text_700 mb-h" style="color: #{$order->status_color};">{$order->status_id}</div></div>

                <div class="input-group input-group--small hidden-sm-up">
                    <span class="form-control">
                        {$match_order->total_price}
                    </span>
                    <span class="input-group-addon">
                        {$currency->sign|escape}
                    </span>
                </div>
            </div>
            <div class="match_order__cell match_order__cell--identify match_order__cell--center hidden-sm-down">
                {if $match_order->match_by_email && $match_order->match_by_phone}
                    <div class="tag tag-ind_email">{$btr->order_match_by_email}</div>
                    <div class="tag tag-ind_phone">{$btr->order_match_by_phone}</div>
                {elseif $match_order->match_by_email}
                    <div class="tag tag-ind_email">{$btr->order_match_by_email}</div>
                {elseif $match_order->match_by_phone}
                    <div class="tag tag-ind_phone">{$btr->order_match_by_phone}</div>
                {else}
                    <div class="tag tag-ind_unknown">{$btr->order_match_by_unknown}</div>
                {/if}
            </div>
            <div class="match_order__cell  match_order__cell--status match_order__cell--center hidden-sm-down">
                <div class="tag--np tag text_700" style="color: #{$order->status_color};">{$match_order->status_name}</div>
            </div>
            <div class="match_order__cell match_order__cell--prices match_order__cell--center hidden-xs-down">
                <div class="input-group input-group--small">
                    <span class="form-control">
                        {$match_order->total_price}
                    </span>
                    <span class="input-group-addon">
                        {$currency->code|escape}
                    </span>
                </div>
            </div>
        </div>
        {/foreach}
    </div>

    {if $current_page !== 'all'}
        {include 'pagination.tpl'}
    {/if}
</div>

{literal}
<script>

    $('.tab_navigation_link').on('mouseup', function() {
        const matchOrdersTab = $(this).hasClass('fn_match_orders_tab_title');

        if (matchOrdersTab && ! hasMatchOrdersTabParam(window.location.href)) {
            const url = addQueryParam(window.location.href, 'match_orders_tab_active', 1);
            window.history.pushState('', '', url);
            return;
        }

        if (!matchOrdersTab && hasMatchOrdersTabParam(window.location.href)) {
            const url = removeQueryParam(window.location.href, 'match_orders_tab_active');
            window.history.pushState('', '', url);
        }
    });

    $('.page-item a').on('click', function(e) {
        e.preventDefault();
        const link = $(this).attr('href');

        let target = '';
        if (! hasMatchOrdersTabParam(link) && hasMatchOrdersTabParam(window.location.href)) {
            target = addQueryParam(link, 'match_orders_tab_active', 1);
        } else if(hasMatchOrdersTabParam(link) && ! hasMatchOrdersTabParam(window.location.href)) {
            target = removeQueryParam(link, 'match_orders_tab_active');
        } else {
            target = link;
        }
        window.location = target;
    });

    function addQueryParam(url, paramName, paramValue) {
        return `${url}&${paramName}=${paramValue}`;
    }

    function removeQueryParam(url, paramName) {
        return url.split('&')
                  .map(item => item.split('='))
                  .filter(item => item[0] !== paramName)
                  .map(item => item.join('='))
                  .join('&');
    }

    function hasMatchOrdersTabParam(url) {
        return url.substr(window.location.href.indexOf('?') + 1)
                  .split('&')
                  .map(item => item.split('='))
                  .reduce((acc, item) => item[0] === 'match_orders_tab_active' ? true : acc, false);
    }

</script>
{/literal}

<style>
    .match-order {
        display: flex;
        flex-wrap: wrap;
        align-items: normal;
    }
    .match-order__field {
        width: 25%;
        padding: 5px;
    }
</style>