{* The brand page template *}

<div class="block">
	{* The page heading *}
	<div class="block__header block__header--boxed block__header--border">
		<h1 class="block__heading"><span data-page="{$page->id}">{if $page->name_h1|escape}{$page->name_h1|escape}{else}{$page->name|escape}{/if}</span></h1>
	</div>
	{if $authors}
		<div class="block__body block--boxed block--border">
			{* The list of the authors *}
			{if $authors}
				<div class="author_list f_row">
					{foreach $authors as $a}
						<div class="author_list__item f_col-xs-6 f_col-sm-4 f_col-lg-2">
							<div class="author_list__preview">
								<a class="d-flex align-items-center justify-content-center flex-column author_list__link" data-author="{$a->id}" href="{url_generator route='author' url=$a->url}">
									<div class="author_list__image">
									{if $a->image}
										<picture>
											{if $settings->support_webp}
												<source type="image/webp" data-srcset="{$a->image|resize:320:500:false:$config->resized_authors_dir}.webp">
											{/if}
											<source data-srcset="{$a->image|resize:320:500:false:$config->resized_authors_dir}">
											<img class="lazy" data-src="{$a->image|resize:320:500:false:$config->resized_authors_dir}" src="{$rootUrl}/design/{get_theme}/images/xloading.gif" alt="{$a->name|escape}" title="{$a->name|escape}"/>
										</picture>
									{else}
										<div class="author_card__no_image d-flex align-items-start">
											{include file="svg.tpl" svgId="comment-user_icon"}
										</div>
									{/if}
									</div>
									<div class="author_list__name">{$a->name|escape}</div>
								</a>
							</div>
						</div>
					{/foreach}
				</div>
			{/if}
		</div>
	{/if}
</div>

{* Pagination *}
<div class="products_pagination">
	{include file='pagination.tpl'}
</div>

{* The page body *}
{if $description}
<div class="block block--boxed block--border">
	<div class="fn_readmore">
		<div class="block__description block__description--style">{$description}</div>
	</div>
</div>
{/if}


