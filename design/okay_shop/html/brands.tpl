{* The brand page template *}

<div class="block">
	{* The page heading *}
	<div class="block__header block__header--boxed block__header--border">
		<h1 class="block__heading"><span data-page="{$page->id}">{if $page->name_h1|escape}{$page->name_h1|escape}{else}{$page->name|escape}{/if}</span></h1>
	</div>
	{if $brands}
		<div class="block__body block--boxed block--border">
			{* The list of the brands *}
			{if $brands}
				<div class="brand f_row">
					{foreach $brands as $b}
						<div class="brand__item f_col-xs-6 f_col-sm-4 f_col-lg-2">
							<div class="brand__preview">
								<a class="d-flex align-items-center justify-content-center brand__link" data-brand="{$b->id}" href="{url_generator route='brand' url=$b->url}">
									{if $b->image}
										<div class="brand__image">
											<picture>
												{if $settings->support_webp}
													<source type="image/webp" data-srcset="{$b->image|resize:120:100:false:$config->resized_brands_dir}.webp">
												{/if}
												<source data-srcset="{$b->image|resize:120:100:false:$config->resized_brands_dir}">
												<img class="brand_img lazy" data-src="{$b->image|resize:120:100:false:$config->resized_brands_dir}" src="{$rootUrl}/design/{get_theme}/images/xloading.gif" alt="{$b->name|escape}" title="{$b->name|escape}"/>
											</picture>
										</div>
									{else}
										<div class="brand__name"><span>{$b->name|escape}</span></div>
									{/if}
								</a>
							</div>
						</div>
					{/foreach}
				</div>
			{/if}
		</div>
	{/if}
</div>

{* The page body *}
{if $description}
<div class="block block--boxed block--border">
	<div class="fn_readmore">
		<div class="block__description block__description--style">{$description}</div>
	</div>
</div>
{/if}


