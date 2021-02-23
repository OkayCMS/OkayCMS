{* Title *}
{$meta_title=$btr->modules_list_title scope=global}

{*Название страницы*}
<div class="boxed boxed_marketplace--bg">
    <div class="row">
        <div class="col-md-12">
            <div class="wrap_heading">
                <div class="heading_page">
                    <span>{$btr->marketplace_heading|escape} <span class="marketplace--beta">BETA</span></span>    
                </div>
                <div class="heading_box">
                    {$btr->marketplace_text|escape}
                </div>
                <div class="marketplace_search">
                    <input class="form-control fn_search_modules" name="name" type="text" value="" autocomplete="off" placeholder="{$btr->marketplace_placeholder_search|escape}"/>
                </div>
            </div>
        </div>
    </div>
</div>

{*Главная форма страницы*}
<div class="fn_market_modules">
    <div class="fn_form_list">
        <div class="okay_list products_list bg_white mb-1 fn_sort_list ">
            <input type="hidden" name="session_id" value="{$smarty.session.id}">
            {*Шапка таблицы*}
            <div class="okay_list_head">
                <div class="okay_list_heading okay_list_photo">{$btr->general_photo|escape}</div>
                <div class="okay_list_heading okay_list_marketplace_name">{$btr->general_name|escape}</div>
                <div class="okay_list_heading okay_list_market_version_m hidden-md-down">{$btr->s_module_version|escape}</div>
                <div class="okay_list_heading okay_list_market_version_s hidden-md-down">{$btr->m_module_version|escape}</div>
                <div class="okay_list_heading okay_list_marketplace_demo hidden-sm-down">{$btr->m_module_demo|escape}</div>
                <div class="okay_list_heading okay_list_marketplace_buy hidden-xs-down">{$btr->m_module_checkout|escape}</div>
            </div>

            {*Параметры элемента*}
            <div class="deliveries_wrap okay_list_body fn_m_modules_list">
                {include 'search_modules.tpl'}
            </div>
            
            {if $search_modules->links->next}
                <a class="fn_m_modules_next_page btn btn-warning btn_big my-2" href="{url controller='ModulesAdmin@ajaxPagination' next_page=$search_modules->links->next}">{$btr->m_modules_next_page|escape}</a>
            {/if}
        </div>
    </div>
</div>

<script src="design/js/autocomplete/jquery.autocomplete-min.js"></script>

<script>
    $(".fn_m_modules_next_page").on('click', function (e) {
        e.preventDefault();
        let that = $(this),
            url = that.attr('href');
        if (that.attr('disabled')) {
            return false;
        }
        
        that.attr('disabled', true);
        $.ajax({
            url: url,
            dataType: 'json',
            success: function (data) {
                if (data.result) {
                    $('.fn_m_modules_list').append(data.result);
                    that.attr('disabled', false);
                }
                if (data.next_page) {
                    that.attr('href', data.next_page);
                } else {
                    that.hide();
                }
            }
        })
    });
    
    var ajax;
    var flagNum = 0;
    
    $(".fn_search_modules").on('keyup', function (e) {
        
        let localFlag = ++flagNum;
        
        let that = $(this),
            nextPage = $(".fn_m_modules_next_page"),
            query = that.val();

        if (query.length < 3) {
            return false;
        }
        
        setTimeout(function () {
            ajax = $.ajax({
                url: '{url controller='ModulesAdmin@ajaxSearch'}',
                data: {
                    query: query,
                },
                dataType: 'json',
                beforeSend : function() {
                    if (ajax) {
                        ajax.abort();
                    }
                    if (localFlag !== flagNum) {
                        return false;
                    }
                },
                success: function (data) {
                    if (data.hasOwnProperty('result')) {
                        $('.fn_m_modules_list').html(data.result);
                    }
                    if (data.hasOwnProperty('next_page')) {
                        nextPage.attr('href', data.next_page).show();
                    } else {
                        nextPage.hide();
                    }
                    ajax = null;
                }
            });
        }, 1000);
        
    });
</script>
