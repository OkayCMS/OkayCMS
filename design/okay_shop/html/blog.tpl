{* The blog page template *}

<div class="d-lg-flex align-items-lg-start justify-content-lg-between flex-lg-row-reverse clearfix">
    {* Sidebar with blog *}
    <div class="fn_mobile_toogle sidebar sidebar--right d-lg-flex flex-lg-column">
        {include 'blog_sidebar.tpl'}
    </div>
    {* Content with blog *}
    <div class="blog_container blog_container--left d-flex flex-column">
        <div class="blog_container__boxed">
            <h1 class="blog__heading h1">
                <span {if $page->id}data-page="{$page->id}"{elseif $category->id}data-blog_category="{$category->id}"{/if}>{$h1|escape}</span>
            </h1>
            {* Mobile button catalog *}
            <div class="fn_switch_mobile_filter switch_mobile_filter hidden-lg-up">
                {include file="svg.tpl" svgId="catalog_icon"}
                <span data-language="blog_catalog">{$lang->blog_catalog}</span>
            </div>
            {if $description}
                <div class="boxed boxed--big">
                    <div class="fn_readmore">
                        <div class="block__description">{$description}</div>
                    </div>
                </div>
            {/if}
            <div class="article_list f_row">
                {foreach $posts as $post}
                <div class="article_item f_col-sm-6 f_col-md-4 f_col-lg-4">{include 'post_list.tpl'}</div>
                {/foreach}
            </div>
            {* Pagination *}
            <div class="products_pagination">
                {include file='pagination.tpl'}
            </div>
        </div>
    </div>
</div>
