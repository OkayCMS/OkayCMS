{function name='render_condition_product'}
    <div class="fn_condition_product row">
        <div class="col-md-2">
            <div class="activity_of_switch activity_of_switch--left">
                <div class="activity_of_switch_item">
                    <div class="okay_switch clearfix">
                        <label class="switch_label">Все</label>
                        <label class="switch switch-default">
                            <input
                                class="switch-input"
                                {if $condition}
                                    name="conditions[{$condition->id}][all_entities]"
                                {else}
                                    name="new_conditions[condition_type][id][all_entities]"
                                {/if}
                                value="1"
                                type="checkbox"
                                id="example_checkbox"
                                {if $condition->all_entities}
                                    checked
                                {/if}
                            >
                            <span class="switch-label"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-10">
            <div class="toggle_body_wrap on fn_card fn_sort_list">
                <div class="okay_list ok_related_list">
                    <div class="okay_list_body compact_product_list sortable">
                        {if $condition}
                            {foreach $condition->entities as $product}
                                <div class="fn_row okay okay_list_body_item fn_sort_item">
                                    <div class="okay_list_row">
                                        <div class="okay_list_boding okay_list_drag move_zone">
                                            {include file='svg_icon.tpl' svgId='drag_vertical'}
                                        </div>
                                        <div class="okay_list_boding okay_list_related_photo">
                                            <input type="hidden" name=conditions[{$condition->id}][entities][] value='{$product->id}'>
                                            <a href="{url controller=ProductAdmin id=$product->id}">
                                                {if $product->image}
                                                    <img class="product_icon" src='{$product->image->filename|resize:40:40}'>
                                                {elseif $product->images[0]}
                                                    <img class="product_icon" src='{$product->images[0]->filename|resize:40:40}'>
                                                {else}
                                                    <img class="product_icon" src="design/images/no_image.png" width="40">
                                                {/if}
                                            </a>
                                        </div>
                                        <div class="okay_list_boding okay_list_related_name">
                                            <a class="link" href="{url controller=ProductAdmin id=$product->id}">{$product->name|escape}</a>
                                        </div>
                                        <div class="okay_list_boding okay_list_close">
                                            <button data-hint="{$btr->general_delete_product|escape}" type="button" class="btn_close fn_remove_item hint-bottom-right-t-info-s-small-mobile  hint-anim">
                                                {include file='svg_icon.tpl' svgId='delete'}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            {/foreach}
                        {/if}

                        <div class="fn_row okay okay_list_body_item fn_sort_item fn_compact_product_list_item_add" style='display:none;'>
                            <div class="okay_list_row">
                                <div class="okay_list_boding okay_list_drag move_zone">
                                    {include file='svg_icon.tpl' svgId='drag_vertical'}
                                </div>
                                <div class="okay_list_boding okay_list_related_photo">
                                    <input type="hidden" name="{if $condition}conditions[{$condition->id}][entities][]{else}new_conditions[condition_type][id][entities][]{/if}" value="">
                                    <img class=product_icon src="">
                                </div>
                                <div class="okay_list_boding okay_list_related_name">
                                    <a class="link compact_list_product_name" href=""></a>
                                </div>
                                <div class="okay_list_boding okay_list_close">
                                    <button data-hint="{$btr->general_delete_product|escape}" type="button" class="btn_close fn_remove_item hint-bottom-right-t-info-s-small-mobile  hint-anim">
                                        {include file='svg_icon.tpl' svgId='delete'}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="heading_label heading_label--required">
                    <span>{$btr->okay_cms__feeds__feed__entities__select_products|escape}</span>
                </div>
                <div class="autocomplete_arrow">
                    <input type=text name=related class="form-control fn_compact_product_list" placeholder='{$btr->general_recommended_add|escape}'>
                </div>
            </div>
        </div>
    </div>
{/function}

{function name='render_condition_category'}
    <div class="fn_condition_category row">
        <div class="col-md-12 form_group">
            <div class="heading_label heading_label--required">
                <span>{$btr->okay_cms__feeds__feed__entities__select_categories|escape}</span>
            </div>
            <select class="selectpicker form-control mb-1" name="{if !$condition}new_conditions[condition_type][id][entities][]{else}conditions[{$condition->id}][entities][]{/if}" data-actions-box="true" multiple>
                {function name=category_select_2 selected_id=$product_category level=0}
                    {foreach $categories as $category}
                        <option value="{$category->id}" title="{$category->name}" {if $condition && in_array($category->id, $condition->entity_ids)} selected {/if}>{section name=sp loop=$level}&nbsp;&nbsp;&nbsp;&nbsp;{/section}{$category->name}</option>
                        {category_select_2 categories=$category->subcategories selected_id=$selected_id  level=$level+1}
                    {/foreach}
                {/function}
                {category_select_2 categories=$categories}
            </select>
        </div>
    </div>
{/function}

{function name='render_condition_feature_value'}
    <div class="fn_condition_feature_value row">
        <div class="fn_features col-md-6 form_group">
            <div class="heading_label heading_label--required">
                <span>{$btr->okay_cms__feeds__feed__entities__select_feature|escape}</span>
            </div>
            <select class="selectpicker form-control mb-1" {if $condition}disabled{/if}>
                {if !$condition}
                    <option value="0" selected disabled>{$feature->name}</option>
                {/if}
                {foreach $features as $feature}
                    <option value="{$feature->id}" {if $condition && $feature->id == $condition->all_entities[0]->feature_id} selected {/if}>{$feature->name}</option>
                {/foreach}
            </select>
        </div>
        <div class="fn_feature_values col-md-6 form_group">
            {if $condition}
                <div class="heading_label heading_label--required">
                    <span>{$btr->okay_cms__feeds__feed__entities__select_feature_values|escape}</span>
                </div>
                <select class="selectpicker form-control mb-1" name="conditions[{$condition->id}][entities][]" data-actions-box="true" multiple>
                    {foreach $condition->all_entities as $entity}
                        <option value="{$entity->id}" {if in_array($entity->id, $condition->entity_ids)} selected {/if}>{$entity->value}</option>
                    {/foreach}
                </select>
            {/if}
        </div>
    </div>
{/function}

{function name='render_condition_brand'}
    <div class="fn_condition_brand row">
        <div class="col-md-12 form_group">
            <div class="heading_label heading_label--required">
                <span>{$btr->okay_cms__feeds__feed__entities__select_brands|escape}</span>
            </div>
            <select class="selectpicker form-control mb-1" name="{if !$condition}new_conditions[condition_type][id][entities][]{else}conditions[{$condition->id}][entities][]{/if}" data-actions-box="true" multiple>
                {foreach $brands as $brand}
                    <option value="{$brand->id}" {if $condition && in_array($brand->id, $condition->entity_ids)} selected {/if}>{$brand->name}</option>
                {/foreach}
            </select>
        </div>
    </div>
{/function}

{function name='render_condition'}
    <div class="fn_condition row mt-1" {if $condition}data-id="{$condition->id}" data-type="$condition->type"{/if}>
        <div class="col-md-3 form_group">
            <div class="heading_label heading_label--required">
                <span>{$btr->okay_cms__feeds__feed__entities__select_entity|escape}</span>
            </div>
            <select class="fn_condition_type selectpicker form-control mb-1" name="{if $condition}conditions[{$condition->id}][entity]{else}new_conditions[condition_type][id][entity]{/if}" {if $condition}disabled{/if}>
                <option value="product" {if $condition && $condition->entity === 'product'} selected {/if}>{$btr->okay_cms__feeds__feed__entities__product}</option>
                <option value="category" {if $condition && $condition->entity === 'category'} selected {/if}>{$btr->okay_cms__feeds__feed__entities__category}</option>
                <option value="feature_value" {if $condition && $condition->entity === 'feature_value'} selected {/if}>{$btr->okay_cms__feeds__feed__entities__feature_value}</option>
                <option value="brand" {if $condition && $condition->entity === 'brand'} selected {/if}>{$btr->okay_cms__feeds__feed__entities__brand}</option>
            </select>
        </div>
        <div class="fn_condition_container col-md-8">
            {if $condition}
                {if $condition->entity === 'product'}
                    {render_condition_product condition=$condition}
                {elseif $condition->entity === 'category'}
                    {render_condition_category condition=$condition}
                {elseif $condition->entity === 'feature_value'}
                    {render_condition_feature_value condition=$condition}
                {elseif $condition->entity === 'brand'}
                    {render_condition_brand condition=$condition}
                {/if}
            {/if}
        </div>
        <div class="col-md-1">
            <div>
                <button data-hint="{$btr->okay_cms__feeds__feed__entities__delete|escape}" type="button" class="btn_close fn_delete_condition hint-bottom-right-t-info-s-small-mobile hint-anim">
                    {include file='svg_icon.tpl' svgId='trash'}
                </button>
            </div>
        </div>
    </div>
{/function}

<div class="fn_conditions_new">
    {render_condition}
    {render_condition_product}
    {render_condition_category}
    {render_condition_feature_value}
    {render_condition_brand}
</div>

<div class="row">
    <div class="col-md-12">
        <div class="boxed">
            <div class="heading_box">
                {$btr->okay_cms__feeds__feed__entities__inclusions|escape}
            </div>
            <div class="content row">
                <div class="col-md-12">
                    <div class="box_btn_heading">
                        <button class="fn_add_condition btn btn_small btn-info" data-type="inclusion" type="button">
                            {include file='svg_icon.tpl' svgId='plus'}
                            <span>{$btr->okay_cms__feeds__feed__entities__add|escape}</span>
                        </button>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="fn_conditions fn_inclusions">
                        {foreach $conditions['inclusions'] as $inclusion}
                            {render_condition condition=$inclusion}
                        {/foreach}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="boxed">
            <div class="heading_box">
                {$btr->okay_cms__feeds__feed__entities__exclusions|escape}
            </div>
            <div class="content row">
                <div class="col-md-12">
                    <div class="box_btn_heading">
                        <button class="fn_add_condition btn btn_small btn-info" data-type="exclusion" type="button">
                            {include file='svg_icon.tpl' svgId='plus'}
                            <span>{$btr->okay_cms__feeds__feed__entities__add|escape}</span>
                        </button>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="fn_conditions fn_exclusions">
                        {foreach $conditions['exclusions'] as $exclusion}
                            {render_condition condition=$exclusion}
                        {/foreach}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{literal}
<script src="design/js/autocomplete/jquery.autocomplete-min.js"></script>

<script>
    $(function() {
        let inclusionsContainer = $('.fn_conditions.fn_inclusions'),
            exclusionsContainer = $('.fn_conditions.fn_exclusions'),
            newCondition = $('.fn_conditions_new .fn_condition').clone(false),
            newConditionProduct = $('.fn_conditions_new .fn_condition_product').clone(false),
            newConditionCategory = $('.fn_conditions_new .fn_condition_category').clone(false),
            newConditionFeatureValue = $('.fn_conditions_new .fn_condition_feature_value').clone(false),
            newConditionBrand = $('.fn_conditions_new .fn_condition_brand').clone(false);

        $('.fn_conditions_new').html('');

        $('.fn_condition_product').not('.fn_conditions_new .fn_condition_product').each(function(i, el) {
            initProductList($(el).find('.fn_card.fn_sort_list'));
        })

        $(document).on('click', '.fn_add_condition', function() {
            addCondition($(this).data('type'));
        });

        function addCondition(type) {
            let condition = newCondition.clone(false),
                el = newConditionProduct.clone(false),
                id = Date.now();

            if (type === 'inclusion') {
                inclusionsContainer.append(condition);
            } else if (type === 'exclusion') {
                exclusionsContainer.append(condition);
            }

            condition.find('.fn_condition_container').html(el);

            condition.data('id', id);
            condition.data('type', type);

            initNames(condition, condition.data('id'), condition.data('type'));

            condition.find('.selectpicker').selectpicker();
            if (el.find('.fn_card.fn_sort_list').length > 0) {
                initProductList(el.find('.fn_card.fn_sort_list'));
            }
        }

        $(document).on('click', '.fn_delete_condition', function() {
            $(this).closest('.fn_condition').remove();
        })

        $(document).on('change', 'select.fn_condition_type', function() {
            let condition = $(this).closest('.fn_condition'),
                el;

            switch ($(this).val()) {
                case 'product':
                    el = newConditionProduct.clone(false);
                    break;
                case 'category':
                    el = newConditionCategory.clone(false);
                    break;
                case 'feature_value':
                    el = newConditionFeatureValue.clone(false);
                    break;
                case 'brand':
                    el = newConditionBrand.clone(false);
                    break;
            }

            condition.find('.fn_condition_container').html(el);

            initNames(el, condition.data('id'), condition.data('type'));

            el.find('.selectpicker').selectpicker();
            if (el.find('.fn_card.fn_sort_list').length > 0) {
                initProductList(el.find('.fn_card.fn_sort_list'));
            }
        });

        $(document).on('change', '.fn_features select', function() {
            let condition = $(this).closest('.fn_condition'),
                selectContainer = $(this).closest('.fn_condition_feature_value').find('.fn_feature_values');

            $.ajax({
                url: '{/literal}{url controller='OkayCMS.Feeds.FeedAdmin@getFeatureValues'}{literal}',
                data: {
                    feature_id: $(this).val()
                },
                success: function(featureValues) {
                    if (featureValues.length > 0) {
                        selectContainer.html(`
                            <div class="heading_label heading_label--required">
                                <span>{/literal}{$btr->okay_cms__feeds__feed__entities__select_feature_values|escape}{literal}</span>
                            </div>
                            <select class="form-control mb-1" name="new_conditions[condition_type][id][entities][]" data-actions-box="true" multiple></select>
                        `);

                        let select = selectContainer.find('select');

                        for (let featureValue of featureValues) {
                            select.append(`<option value="${featureValue.id}">${featureValue.value}</option>`);
                        }

                        initNames(selectContainer, condition.data('id'), condition.data('type'));

                        select.selectpicker();
                    } else {
                        selectContainer.html('Нет значений');
                    }
                }
            });
        });

        function initNames(el, id, type) {
            el.find('input, select').each(function(i, el) {
                if ($(el).attr('name')) {
                    $(el).attr('name', $(el).attr('name').replace('[id]', `[${id}]`));
                    $(el).attr('name', $(el).attr('name').replace('[condition_type]', `[${type}]`));
                }
            })
        }

        function initProductList(el) {
            let compact_product_list_item_add = el.find('.fn_compact_product_list_item_add').clone(true),
                compact_product_list_input = el.find("input.fn_compact_product_list"),
                compact_product_list = el.find(".compact_product_list");
            el.find('.fn_compact_product_list_item_add').remove();
            compact_product_list_input.removeAttr('id');
            compact_product_list_input.devbridgeAutocomplete({
                serviceUrl:'ajax/search_products.php',
                type: 'POST',
                minChars:0,
                orientation:'auto',
                noCache: false,
                onSelect:
                    function(suggestion){
                        compact_product_list_input.val('').focus().blur();
                        new_item = compact_product_list_item_add.clone().appendTo(compact_product_list);
                        new_item.find('a.compact_list_product_name').html(suggestion.data.name);
                        new_item.find('a.compact_list_product_name').attr('href', 'index.php?controller=ProductAdmin&id='+suggestion.data.id);
                        new_item.find('input').val(suggestion.data.id);
                        if(suggestion.data.image) {
                            new_item.find('img.product_icon').attr("src", suggestion.data.image);
                        }
                        else {
                            new_item.find('img.product_icon').remove();
                        }
                        new_item.show();
                    },
                formatResult:
                    function(suggestions, currentValue){
                        var reEscape = new RegExp('(\\' + ['/', '.', '*', '+', '?', '|', '(', ')', '[', ']', '{', '}', '\\'].join('|\\') + ')', 'g');
                        var pattern = '(' + currentValue.replace(reEscape, '\\$1') + ')';
                        return "<div>" + (suggestions.data.image?"<img align=absmiddle src='"+suggestions.data.image+"'> ":'') + "</div>" +  "<span>" + suggestions.value.replace(new RegExp(pattern, 'gi'), '<strong>$1<\/strong>') + "</span>";
                    }
            });

            $(document).on( "click", ".fn_remove_item", function() {
                $(this).closest(".fn_row").fadeOut(200, function() { $(this).remove(); });
                return false;
            });
        }
    });
</script>

<style>
    .ok_related_list {
        margin-bottom: 0;
    }

    .btn_close{
        color: #000000;
    }
</style>
{/literal}