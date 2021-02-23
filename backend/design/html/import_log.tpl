{* Title *}
{$meta_title=$btr->import_log_products scope=global}

{*Название страницы*}
<div class="main_header">
    <div class="main_header__item">
        <div class="main_header__inner">
            {if $logs_count}
                <div class="box_heading heading_page">
                    {if $keyword}
                       {$logs_count|plural:'Найден':'Найдено':'Найдено'} {$logs_count} {$logs_count|plural:'товар':'товаров':'товара'}
                    {else}
                       {$logs_count} {$logs_count|plural:'товар':'товаров':'товара'}
                    {/if}
                </div>
            {else}
                <div class="box_heading heading_page">{$btr->import_log_empty|escape}</div>
            {/if}
        </div>
    </div>
    <div class="main_header__item">
        <div class="main_header__inner">
            <form class="search" method="get">
            <input type="hidden" name="controller" value="ImportLogAdmin">
            <div class="input-group input-group--search">
                <input name="keyword" class="form-control" placeholder="{$btr->general_search|escape}" type="text" value="{$keyword|escape}" >
                <span class="input-group-btn">
                    <button type="submit" class="btn btn_blue"><i class="fa fa-search"></i> <span class="hidden-md-down"></span></button>
                </span>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="boxed fn_toggle_wrap">
    {*Блок фильтров*}
    <div class="row">
        <div class="col-lg-12 col-md-12 ">
            <div class="boxed_sorting">
                <div class="row">
                    <div class="col-md-3 col-lg-3 col-sm-12">
                        <select class="selectpicker form-control" onchange="location = this.value;">
                            <option value="{url keyword=null page=null limit=null filter=null}" {if !$filter}selected{/if}>{$btr->general_all|escape}</option>
                            <option value="{url keyword=null page=null limit=null filter='added'}" {if $filter == 'added'}selected{/if}>{$btr->import_added|escape}</option>
                            <option value="{url keyword=null page=null limit=null filter='updated'}" {if $filter == 'updated'}selected{/if}>{$btr->import_updated|escape}</option>
                        </select>
                    </div>
                    <div class="col-lg-9 col-md-9 col-sm 12">
                        <div class="pull-right">
                            <select onchange="location = this.value;" class="selectpicker form-control">
                                <option value="{url limit=10}" {if $current_limit == 10}selected{/if}>{$btr->general_show_by|escape} 10</option>
                                <option value="{url limit=25}" {if $current_limit == 25}selected{/if}>{$btr->general_show_by|escape} 25</option>
                                <option value="{url limit=50}" {if $current_limit == 50}selected{/if}>{$btr->general_show_by|escape} 50</option>
                                <option value="{url limit=100}" {if $current_limit == 100}selected{/if}>{$btr->general_show_by|escape} 100</option>
                                <option value="{url limit=200}" {if $current_limit == 200}selected{/if}>{$btr->general_show_by|escape} 200</option>
                                <option value="{url limit=500}" {if $current_limit == 500}selected{/if}>{$btr->general_show_by|escape} 500</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {*Главная форма страницы*}
    {if $logs}
        <div class="row">
            <div class="col-md-12 col-lg-12 col-sm-12">
                {include file='pagination.tpl'}
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12">
                <form method="post">
                    <input type="hidden" name="session_id" value="{$smarty.session.id}">
                    <div class="okay_list">
                        {*Шапка таблицы*}
                        <div class="okay_list_head">
                           <div class="okay_list_heading okay_list_check">№</div>
                           <div class="okay_list_heading okay_list_photo">{$btr->general_photo|escape}</div>
                           <div class="okay_list_heading okay_list_log_name">{$btr->general_name|escape} </div>
                            <div class="okay_list_heading okay_list_log_status">
                               <span>{$btr->general_status|escape}</span>
                            </div>
                        </div>
                        {*Параметры элемента*}
                        <div class="okay_list_body">
                            {foreach $logs as $log}
                                <div class="fn_row okay_list_body_item">
                                    <div class="okay_list_row">
                                        <div class="okay_list_boding okay_list_check">{$log@iteration}</div>
                                        <div class="okay_list_boding okay_list_photo">
                                            {if $log->product->image}
                                                <a href="{url controller=ProductAdmin id=$log->product_id return=$smarty.server.REQUEST_URI}" target="_blank">
                                                    <img src="{$log->product->image->filename|escape|resize:55:55}"/>
                                                </a>
                                            {else}
                                                <img height="55" width="55" src="design/images/no_image.png"/>
                                            {/if}
                                        </div>

                                        <div class="okay_list_boding okay_list_log_name">
                                            <a class="link" href="{url controller=ProductAdmin id=$log->product_id return=$smarty.server.REQUEST_URI}" target="_blank">{$log->product_name|escape}</a>
                                            {if $log->variant_name}
                                                <span class="text_grey">({$log->variant_name|escape})</span>
                                            {/if}
                                        </div>
                                        <div class="okay_list_boding okay_list_log_status">
                                            {if $log->status == 'added'}
                                                <i class="fa fa-plus font-2xl text-success" title="{$log->status}"></i>
                                            {elseif $log->status == 'updated'}
                                                <i class="fa fa-refresh font-2xl text-info" title="{$log->status}"></i>
                                            {/if}
                                        </div>
                                    </div>
                                </div>
                            {/foreach}
                        </div>
                    </div>
                </form>
            </div>
        </div>
    {else}
        <div class="heading_box mt-1">
            <div class="text_grey">{$btr->import_log_empty|escape}</div>
        </div>
    {/if}
</div>
