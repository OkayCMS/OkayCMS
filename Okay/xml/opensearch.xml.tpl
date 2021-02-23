<?xml version="1.0" encoding="UTF-8"?>
<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/" xmlns:moz="http://www.mozilla.org/2006/browser/search/">
    <ShortName>{$settings->site_name}</ShortName>
    <Developer>OkayCMS {$config->version} {$config->version_type}</Developer>
    <Description>{$settings->site_name}</Description>
    <InputEncoding>UTF-8</InputEncoding>
    {if (!empty($favicon_mime))}
        <Image type="{$favicon_mime}" width="16" height="16">{$rootUrl}/files/images/{$settings->site_favicon}</Image>
    {/if}

    <Url type="application/opensearchdescription+xml" rel="self" template="{url_generator route="opensearch" absolute=1}" />
    <Url type="application/x-suggestions+json" template="{url_generator route="opensearch_ajax" absolute=1}?query={literal}{searchTerms}{/literal}" />
    <Url type="text/html" template="{url_generator route="search" absolute=1}?keyword={literal}{searchTerms}{/literal}" />
    <moz:SearchForm>{url_generator route="search" absolute=1}</moz:SearchForm>
    <OutputEncoding>UTF-8</OutputEncoding>
    <InputEncoding>UTF-8</InputEncoding>
</OpenSearchDescription>