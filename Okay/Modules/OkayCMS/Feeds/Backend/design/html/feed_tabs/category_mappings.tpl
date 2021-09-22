<div class="row">
    <div class="col-md-12">
        <div class="boxed">
            {function name=category_mappings level=0}
                {foreach $categories as $category}
                    <div class="row category_mapping pt-1">
                        <div class="col-md-6">
                            <div class="form-group">
                                <input class="form-control" type="text" value="{section name=sp loop=$level}&nbsp;&nbsp;&nbsp;&nbsp;{/section}{$category->name}" disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <input
                                        {if !empty($category_mappings[$category->id])}
                                            name="mappings[{$category_mappings[$category->id]->id}][value]"
                                        {else}
                                            name="new_mappings[category][{$category->id}][value]"
                                        {/if}
                                        class="form-control fn_category_mapping"
                                        type="text"
                                        placeholder="{$category->name}"
                                        value="{$category_mappings[$category->id]->value}"
                                        {if !empty($category_mappings[$category->id]->value)} readonly="readonly" {/if}
                                    >
                                    <span class="input-group-addon disable_category_mapping fn_disable_category_mapping"><i class="fa fa-lock {if empty($category_mappings[$category->id]->value)} fa-unlock {/if}"></i></span>
                                </div>
                            </div>
                        </div>
                        <input
                            class="switch-input"
                            {if !empty($category_mappings[$category->id])}
                                name="mappings[{$category_mappings[$category->id]->id}][to_feed]"
                            {else}
                                name="new_mappings[category][{$category->id}][to_feed]"
                            {/if}
                            value="1"
                            type="hidden"
                        >
                    </div>

                    {category_mappings categories=$category->subcategories level=$level+1}
                {/foreach}
            {/function}

            {category_mappings categories=$categories}
        </div>
    </div>
</div>

{literal}
    <script>
        $(document).on("click", ".fn_disable_category_mapping", function () {
            let input = $(this).closest('.input-group').find('.fn_category_mapping');
            if(input.attr("readonly")){
                input.removeAttr("readonly");
            } else {
                input.attr("readonly",true);
            }
            $(this).find('i').toggleClass("fa-unlock");
        });
    </script>

    <style>
        .disable_category_mapping {
            cursor: pointer;
        }

        .category_mapping {
            border-top: 1px solid rgba(0, 0, 0, 0.2);
        }
    </style>
{/literal}