{* The brand page template *}

<div class="block">
	{* The page heading *}
	<div class="block__header block__header--boxed block__header--border">
		<h1 class="block__heading">
			<span data-page="{$page->id}">{$h1|escape}</span>
		</h1>

		<div class="d-flex align-items-center justify-content-end">
			{* Mobile button filters *}
			<div class="fn_switch_mobile_filter switch_mobile_filter hidden-lg-up">
				{include file="svg.tpl" svgId="filter_icon"}
				<span data-language="filters">{$lang->filters}</span>
			</div>
		</div>
	</div>

	{* Sidebar with filters *}
	<div class="fn_mobile_toogle sidebar d-lg-flex flex-lg-column">
		<div class="fn_mobile_toogle sidebar__header sidebar__boxed hidden-lg-up">
			<div class="fn_switch_mobile_filter sidebar__header--close">
				{include file="svg.tpl" svgId="remove_icon"}
				<span data-language="mobile_filter_close">{$lang->mobile_filter_close}</span>
			</div>
			<div class="sidebar__header--reset">
				<form method="post">
					<button type="submit" name="prg_seo_hide" class="fn_filter_reset mobile_filter__reset" value="{url_generator route="brands" absolute=1}">
						{include file="svg.tpl" svgId="reset_icon"}
						<span>{$lang->mobile_filter_reset}</span>
					</button>
				</form>
			</div>
		</div>

		<div class="fn_selected_features">
			{if !$settings->deferred_load_features}
				{include file='selected_features.tpl'}
			{/if}
		</div>

		<div class="fn_features">
			{if !$settings->deferred_load_features}
				{include file='features.tpl'}
			{else}
				{* Deferred load features *}
				<div class='fn_skeleton_load'>
					{section name=foo start=1 loop=7 step=1}
						<div class='skeleton_load__item skeleton_load__item--{$smarty.section.foo.index}'></div>
					{/section}
				</div>
			{/if}
		</div>

		{* Browsed products *}
		<div class="browsed products">
			{include file='browsed_products.tpl'}
		</div>
	</div>

	<div class="products_container">
		<div class="block__body block--boxed block--border">
			{* Product list *}
			<div id="fn_products_content" class="fn_categories">
				{include file="brands_content.tpl"}
			</div>

			{if $brands}
				{* Friendly URLs Pagination *}
				<div class="fn_pagination products_pagination">
					{include file='chpu_pagination.tpl'}
				</div>
			{/if}

			{* The page body *}
			{if $description}
				<div class="block block--boxed">
					<div class="fn_readmore">
						<div class="block__description block__description--style">{$description}</div>
					</div>
				</div>
			{/if}
		</div>
	</div>
</div>


