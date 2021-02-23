{* Languages *}
{if $languages|count > 1}
	{$cnt = 0}
	{foreach $languages as $ln}
		{if $ln->enabled}
			{$cnt = $cnt+1}
		{/if}
	{/foreach}
	{if $cnt>1}
		<div class="switcher__item d-flex align-items-center switcher__language">
			<div class="switcher__visible d-flex align-items-center">
				{if is_file("{$config->lang_images_dir}{$language->label}.png")}
					<img alt="{$language->current_name}" width="20" src='{("{$language->label}.png")|resize:20:20:false:$config->lang_resized_dir}'/>
				{/if}
				<span class="switcher__name hidden-xs-up">{$language->name}</span>
				<span class="switcher__name">{$language->label}</span>
			</div>
			<div class="switcher__hidden">
				{foreach $languages as $l}
					{if $l->enabled}
						<a class="switcher__link d-flex align-items-center {if $language->id == $l->id} active{/if}" href="{preg_replace('/^(.+)\/$/', '$1', $l->url)}">
							{if is_file("{$config->lang_images_dir}{$l->label}.png")}
								<img alt="{$l->current_name}" width="20" src='{("{$l->label}.png")|resize:20:20:false:$config->lang_resized_dir}'/>
							{/if}
							<span class="switcher__name">{$l->name|escape}</span>
							<span class="switcher__name hidden-xl-up">{$l->label|escape}</span>
						</a>
					{/if}
				{/foreach}
			</div>
		</div>
	{/if}
{/if}

{* Currencies *}
{if $currencies|count > 1}
	<div class="switcher__item d-flex align-items-center switcher__currencies">
		<div class="switcher__visible">
			<span class="switcher__name hidden-lg-down">{$currency->name|escape}</span>
			<span class="switcher__name hidden-xl-up">{$currency->sign|escape}</span>
		</div>
		<div class="switcher__hidden">
			{foreach $currencies as $c}
				{if $c->enabled}
					<form method="POST">
						<button type="submit" name="prg_seo_hide" class="switcher__link d-flex align-items-center {if $currency->id== $c->id} active{/if}" value="{url currency_id=$c->id}">
							<span class="switcher__name hidden-lg-down">{$c->name|escape}</span>
							<span class="switcher__name hidden-xl-up">{$c->sign|escape}</span>
						</button>
					</form>
				{/if}
			{/foreach}
		</div>
	</div>
{/if}