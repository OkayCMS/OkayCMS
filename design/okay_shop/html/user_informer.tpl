{* User account *}
{if $user}
	<a class="d-inline-flex align-items-center account__link" href="{url_generator route='user'}">
		{include file="svg.tpl" svgId="user_icon"}
        <span class="account__text" data-language="index_account">{$lang->index_account} </span>
		<span>{$user->name|escape}</span>
	</a>
{else}
	<a class="d-inline-flex align-items-center account__link" href="javascript:;" onclick="document.location.href = '{url_generator route="login"}'" title="{$lang->index_login}">
		{include file="svg.tpl" svgId="user_icon"}
        <span class="account__text" data-language="index_account">{$lang->index_account} </span>
		<span class="account__login" data-language="index_login">{$lang->index_login}</span>
	</a>
{/if}