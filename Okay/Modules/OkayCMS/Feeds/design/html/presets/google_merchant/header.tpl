<?xml version='1.0' encoding='UTF-8'?>
<rss xmlns:g='http://base.google.com/ns/1.0' version='2.0'>
    <channel>

        {if $feed->settings['company']}
            <title>{$feed->settings['company']|escape}</title>
        {/if}

        <link>{$rootUrl}</link>