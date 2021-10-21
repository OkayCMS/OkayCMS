{function name='render_condition_product'}
    <div class="fn_condition_product row mb-1">
        <div class="col-md-12">
            <div class="toggle_body_wrap on fn_card fn_sort_list">
                <div class="d_flex">
                    <div class="f_col-lg">
                        <div class="heading_label heading_label--required">
                            <span>{$btr->okay_cms__feeds__feed__entities__select_products|escape}</span>
                        </div>
                        <div class="autocomplete_arrow">
                            <input type=text name=related class="form-control fn_compact_product_list" placeholder='{$btr->okay_cms__feeds__feed__entities__product_add|escape}'>
                        </div>
                    </div>
                    <div class="activity_of_switch activity_of_switch--left mt-2 ml-1">
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
                <div class="okay_list ok_feed_condition_list">
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
                                            <button data-hint="{$btr->general_delete_product|escape}" type="button" class="btn_close fn_remove_item hint-bottom-right-t-info-s-small-mobile hint-anim">
                                                {include file='svg_icon.tpl' svgId='trash'}
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
                                    <button data-hint="{$btr->general_delete_product|escape}" type="button" class="btn_close fn_remove_item hint-bottom-right-t-info-s-small-mobile hint-anim">
                                        {include file='svg_icon.tpl' svgId='trash'}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
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
    <div class="fn_condition feed_condition_item row" {if $condition}data-id="{$condition->id}" data-type="$condition->type"{/if}>
        <div class="col-md-12">
            <div class="d_flex">
                <div class="feed_select_type">
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
                <button type="button" data-hint="{$btr->okay_cms__feeds__feed__entities__delete|escape}" class="btn btn_close feed_condition_delete fn_delete_condition hint-bottom-right-t-info-s-small-mobile hint-anim">
                    {include file='svg_icon.tpl' svgId='delete'}
                </button>
            </div>
        </div>
        <div class="fn_condition_container col-md-12">
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
    <div class="col-md-12 col-lg-6 pr-0">
        <div class="boxed">
            <div class="heading_box mb-q">
                {$btr->okay_cms__feeds__feed__entities__inclusions|escape}
                <i class="fn_tooltips" title="{$btr->okay_cms__feeds__feed__entities__inclusions_faq|escape}">
                    <svg width="20px" height="20px" viewBox="0 0 438.533 438.533"><path fill="currentColor" d="M409.133,109.203c-19.608-33.592-46.205-60.189-79.798-79.796C295.736,9.801,259.058,0,219.273,0c-39.781,0-76.47,9.801-110.063,29.407c-33.595,19.604-60.192,46.201-79.8,79.796C9.801,142.8,0,179.489,0,219.267c0,39.78,9.804,76.463,29.407,110.062c19.607,33.592,46.204,60.189,79.799,79.798c33.597,19.605,70.283,29.407,110.063,29.407s76.47-9.802,110.065-29.407c33.593-19.602,60.189-46.206,79.795-79.798c19.603-33.596,29.403-70.284,29.403-110.062C438.533,179.485,428.732,142.795,409.133,109.203z M255.82,356.309c0,2.662-0.862,4.853-2.573,6.563c-1.704,1.711-3.895,2.567-6.557,2.567h-54.823c-2.664,0-4.854-0.856-6.567-2.567c-1.714-1.711-2.57-3.901-2.57-6.563v-54.823c0-2.662,0.855-4.853,2.57-6.563c1.713-1.708,3.903-2.563,6.567-2.563h54.823c2.662,0,4.853,0.855,6.557,2.563c1.711,1.711,2.573,3.901,2.573,6.563V356.309z M325.338,187.574c-2.382,7.043-5.044,12.804-7.994,17.275c-2.949,4.473-7.187,9.042-12.709,13.703c-5.51,4.663-9.891,7.996-13.135,9.998c-3.23,1.995-7.898,4.713-13.982,8.135c-6.283,3.613-11.465,8.326-15.555,14.134c-4.093,5.804-6.139,10.513-6.139,14.126c0,2.67-0.862,4.859-2.574,6.571c-1.707,1.711-3.897,2.566-6.56,2.566h-54.82c-2.664,0-4.854-0.855-6.567-2.566c-1.715-1.712-2.568-3.901-2.568-6.571v-10.279c0-12.752,4.993-24.701,14.987-35.832c9.994-11.136,20.986-19.368,32.979-24.698c9.13-4.186,15.604-8.47,19.41-12.847c3.812-4.377,5.715-10.188,5.715-17.417c0-6.283-3.572-11.897-10.711-16.849c-7.139-4.947-15.27-7.421-24.409-7.421c-9.9,0-18.082,2.285-24.555,6.855c-6.283,4.565-14.465,13.322-24.554,26.263c-1.713,2.286-4.093,3.431-7.139,3.431c-2.284,0-4.093-0.57-5.424-1.709L121.35,145.89c-4.377-3.427-5.138-7.422-2.286-11.991c24.366-40.542,59.672-60.813,105.922-60.813c16.563,0,32.744,3.903,48.541,11.708c15.796,7.801,28.979,18.842,39.546,33.119c10.554,14.272,15.845,29.787,15.845,46.537C328.904,172.824,327.71,180.529,325.338,187.574z"></path></svg>
                </i>
                <div class="box_btn_heading ml-1">
                    <button class="fn_add_condition btn btn_mini btn-secondary" data-type="inclusion" type="button">
                        {include file='svg_icon.tpl' svgId='plus'}
                        <span>{$btr->okay_cms__feeds__feed__entities__add|escape}</span>
                    </button>
                </div>
            </div>
            <div class="content row">
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
    <div class="col-md-12 col-lg-6">
        <div class="boxed">
            <div class="heading_box mb-q">
                {$btr->okay_cms__feeds__feed__entities__exclusions|escape}
                <i class="fn_tooltips" title="{$btr->okay_cms__feeds__feed__entities__exclusions_faq|escape}">
                    <svg width="20px" height="20px" viewBox="0 0 438.533 438.533"><path fill="currentColor" d="M409.133,109.203c-19.608-33.592-46.205-60.189-79.798-79.796C295.736,9.801,259.058,0,219.273,0c-39.781,0-76.47,9.801-110.063,29.407c-33.595,19.604-60.192,46.201-79.8,79.796C9.801,142.8,0,179.489,0,219.267c0,39.78,9.804,76.463,29.407,110.062c19.607,33.592,46.204,60.189,79.799,79.798c33.597,19.605,70.283,29.407,110.063,29.407s76.47-9.802,110.065-29.407c33.593-19.602,60.189-46.206,79.795-79.798c19.603-33.596,29.403-70.284,29.403-110.062C438.533,179.485,428.732,142.795,409.133,109.203z M255.82,356.309c0,2.662-0.862,4.853-2.573,6.563c-1.704,1.711-3.895,2.567-6.557,2.567h-54.823c-2.664,0-4.854-0.856-6.567-2.567c-1.714-1.711-2.57-3.901-2.57-6.563v-54.823c0-2.662,0.855-4.853,2.57-6.563c1.713-1.708,3.903-2.563,6.567-2.563h54.823c2.662,0,4.853,0.855,6.557,2.563c1.711,1.711,2.573,3.901,2.573,6.563V356.309z M325.338,187.574c-2.382,7.043-5.044,12.804-7.994,17.275c-2.949,4.473-7.187,9.042-12.709,13.703c-5.51,4.663-9.891,7.996-13.135,9.998c-3.23,1.995-7.898,4.713-13.982,8.135c-6.283,3.613-11.465,8.326-15.555,14.134c-4.093,5.804-6.139,10.513-6.139,14.126c0,2.67-0.862,4.859-2.574,6.571c-1.707,1.711-3.897,2.566-6.56,2.566h-54.82c-2.664,0-4.854-0.855-6.567-2.566c-1.715-1.712-2.568-3.901-2.568-6.571v-10.279c0-12.752,4.993-24.701,14.987-35.832c9.994-11.136,20.986-19.368,32.979-24.698c9.13-4.186,15.604-8.47,19.41-12.847c3.812-4.377,5.715-10.188,5.715-17.417c0-6.283-3.572-11.897-10.711-16.849c-7.139-4.947-15.27-7.421-24.409-7.421c-9.9,0-18.082,2.285-24.555,6.855c-6.283,4.565-14.465,13.322-24.554,26.263c-1.713,2.286-4.093,3.431-7.139,3.431c-2.284,0-4.093-0.57-5.424-1.709L121.35,145.89c-4.377-3.427-5.138-7.422-2.286-11.991c24.366-40.542,59.672-60.813,105.922-60.813c16.563,0,32.744,3.903,48.541,11.708c15.796,7.801,28.979,18.842,39.546,33.119c10.554,14.272,15.845,29.787,15.845,46.537C328.904,172.824,327.71,180.529,325.338,187.574z"></path></svg>
                </i>
                <div class="box_btn_heading ml-1">
                    <button class="fn_add_condition btn btn_mini btn-secondary" data-type="exclusion" type="button">
                        {include file='svg_icon.tpl' svgId='plus'}
                        <span>{$btr->okay_cms__feeds__feed__entities__add|escape}</span>
                    </button>
                </div>
            </div>
            <div class="content row">
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
{/literal}
