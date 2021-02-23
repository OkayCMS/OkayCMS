<?xml version='1.0' encoding='UTF-8'?>
<price>

    {if $settings->okaycms__hotline__company}
        <firmName>{$settings->okaycms__hotline__company|escape}</firmName>
    {else}
        <firmName>{$settings->site_name}</firmName>
    {/if}

    <categories>
        {function name=categories_tree}
            {if $categories}
                {foreach $categories as $c}
                    <category>
                        <id>{$c->id}</id>
                        {if $c->parent_id}
                            <parentId>{$c->parent_id}</parentId>
                        {/if}
                        <name>{$c->name|escape}</name>
                    </category>
                    {if $c->subcategories && $c->count_children_visible && $level < 3}
                        {categories_tree categories=$c->subcategories}
                    {/if}
                {/foreach}
            {/if}
        {/function}
        {categories_tree categories=$categories}
    </categories>
    {get_design_block block=OkayCMS_Hotline_head}
    <items>