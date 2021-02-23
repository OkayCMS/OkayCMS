{* Product page *}

<div class="fn_product block" itemscope itemtype="http://schema.org/Product">
    {* The product name *}
    <div class="block__header block__header--boxed block__header--border  {if $product->variant->sku}block__header--promo{/if}">
        <h1 class="block__heading">
            <span data-product="{$product->id}" itemprop="name">{$h1|escape}</span>
        </h1>
        <div class="block__header_promo product-page__sku{if !$product->variant->sku} hidden{/if}">
            <span data-language="product_sku">{$lang->product_sku}:</span>
            <span class="fn_sku sku_nubmer" {if $product->variant->sku}itemprop = "sku"{/if}>{$product->variant->sku|escape}</span>
        </div>
    </div>

    <div class="fn_transfer f_row flex-column flex-lg-row align-items-lg-stretch">
        <div class="block product-page__gallery f_col f_col-lg-7 f_col-xl-7">
            <div class="block--boxed block--border boxed--stretch d-md-flex justify-content-between">
                {if $product->images}
                    {* Main product image *}
                    <div class="gallery_image product-page__image {if $product->images|count == 1} product-page__image--full {/if} f_row justify-content-center">
                        <div class="product-page__img swiper-container gallery-top">
                            <div class="swiper-wrapper">
                                {foreach $product->images as $i=>$image}
                                    <a href="{$image->filename|resize:1800:1800:w}" data-fancybox="we2" class="swiper-slide">
                                        <picture>
                                            {if $settings->support_webp}
                                                <source type="image/webp" srcset="{$image->filename|resize:600:800}.webp">
                                            {/if}
                                                <source srcset="{$image->filename|resize:600:800}">
                                                <img {if $image@first} itemprop="image" {/if} src="{$image->filename|resize:600:800}" alt="{$product->name|escape}" title="{$product->name|escape}"/>
                                        </picture>
                                    </a>
                                {/foreach}
                            </div>
                            {if $product->images|count > 1}
                                <div class="swiper-button-next"></div>
                                <div class="swiper-button-prev"></div>
                            {/if}
                        </div>
                        {if $product->featured || $product->special || $product->variant->compare_price}
                            <div class="stickers stickers_product-page">
                                {if $product->featured}
                                <span class="sticker sticker--hit" data-language="product_sticker_hit">{$lang->product_sticker_hit}</span>
                                {/if}
                                {if $product->variant->compare_price}
                                <span class="sticker sticker--discount" data-language="product_sticker_discount">{$lang->product_sticker_discount}</span>
                                {/if}
                                {if $product->special}
                                    <span class="sticker sticker--special">
                                        <img class="sticker__image" src='files/special/{$product->special}' alt='{$product->special|escape}' title="{$product->special|escape}"/>
                                    </span>
                                {/if}
                            </div>
                        {/if}
                    </div>
                    {* Additional product images *}
                    {if $product->images|count > 1}
                    <div class="product-page__images swiper-container gallery-thumbs d-md-flex justify-content-center justify-content-md-start flex-md-column hidden-sm-down">
                        <div class="swiper-wrapper">
                            {* cut removes the first image, if you need start from the second - write cut:2 *}
                            {foreach $product->images as $i=>$image}
                            <div class="swiper-slide product-page__images-item">
                                <picture>
                                    {if $settings->support_webp}
                                        <source type="image/webp" data-srcset="{$image->filename|resize:60:60}.webp">
                                    {/if}
                                        <source data-srcset="{$image->filename|resize:60:60}">
                                        <img class="lazy" data-src="{$image->filename|resize:60:60}" src="{$rootUrl}/design/{get_theme}/images/xloading.gif" alt="{$product->name|escape}" title="{$product->name|escape}"/>
                                </picture>
                            </div>
                            {/foreach}
                        </div>
                        {if $product->images|count > 4}
                            <div class="swiper-scrollbar"></div>
                        {/if}
                    </div>
                    {/if}
                {else}
                    <div class="product-page__no_image d-flex align-items-center justify-content-center" title="{$product->name|escape}">
                        {include file="svg.tpl" svgId="no_image"}
                    </div>
                {/if}
            </div>
        </div>

        <div class="block product-page__details f_col f_col-lg-5 f_col-xl-5">
            <div class="block--border boxed--stretch details_boxed">
                <div class="details_boxed__item details_boxed__item--one">
                    {* Product Rating *}
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="details_boxed__rating">
                            {*<div class="details_boxed__title" data-language="product_rating">{$lang->product_rating}:</div>*}
                            <div id="product_{$product->id}" class="product__rating fn_rating" data-rating_post_url="{url_generator route='ajax_product_rating'}" {if $product->rating > 0} itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating"{/if}>
                                <span class="rating_starOff">
                                    <span class="rating_starOn" style="width:{$product->rating*90/5|string_format:'%.0f'}px;"></span>
                                </span>
                                {*Вывод количества голосов данного товара, скрыт ради микроразметки*}
                                {if $product->rating > 0}
                                <span class="rating_text">( <span itemprop="reviewCount">{$product->votes|string_format:"%.0f"}</span> )</span>
                                <span class="rating_text hidden">( <span itemprop="ratingValue">{$product->rating|string_format:"%.1f"}</span> )</span>
                                {*Вывод лучшей оценки товара для микроразметки*}
                                <span class="rating_text hidden" itemprop="bestRating" style="display:none;">5</span>
                                {else}
                                <span class="rating_text hidden">({$product->rating|string_format:"%.1f"})</span>
                                {/if}
                            </div>
                        </div>
                        {* Product brand *}
                        {if !empty($brand)}
                            {if !empty($brand->image)}
                                <div class="details_boxed__brand clearfix">
                                    <a href="{url_generator route="brand" url=$brand->url}">
                                        <img class="brand_img" src="{$brand->image|resize:120:65:false:$config->resized_brands_dir}" alt="{$brand->name|escape}" title="{$brand->name|escape}">
                                        <span class="hidden" itemprop="brand">{$brand->name|escape}</span>
                                    </a>
                                </div>
                            {else}
                                <div class="details_boxed__no_brand clearfix">
                                    <span class="details_boxed__title" data-language="product_brand_name">{$lang->product_brand_name}</span>
                                    <a class="theme_link--color" href="{url_generator route="brand" url=$brand->url}">
                                        <span itemprop="brand">{$brand->name|escape}</span>
                                    </a>
                                </div>
                            {/if}
                        {/if}
                    </div>

                    {* Anchor form comments *}
                    <div class="details_boxed__anchor_comments">
                        <a href="#comments" class="fn_anchor_comments d-inline-flex align-items-center anchor_comments__link">
                            {if $comments|count}
                                {$comments|count}
                                {$comments|count|plural:$lang->product_anchor_comment_plural1:$lang->product_anchor_comment_plural2:$lang->product_anchor_comment_plural3}
                            {else}
                                <span data-language="product_anchor_comment">{$lang->product_anchor_comment}</span>
                            {/if}
                        </a>
                    </div>

                    {* Product available *}
                    <div class="details_boxed__available">
                        <div class="available__no_stock d-flex align-items-center icon icon-highlight-off fn_not_stock{if $product->variant->stock > 0} hidden-xs-up{/if}" data-language="product_out_of_stock">{$lang->product_out_of_stock}</div>
                        <div class="available__in_stock d-flex align-items-center icon icon-check-circle-outline fn_in_stock{if $product->variant->stock < 1} hidden-xs-up{/if}" data-language="product_in_stock">{$lang->product_in_stock}</div>
                    </div>
                </div>

                <div class="details_boxed__item">
                    <form class="fn_variants" action="{url_generator route="cart"}">

                        {* Product variants *}
                        <div class="details_boxed__select">
                            <div class="details_boxed__title {if $product->variants|count < 2} hidden{/if}" data-language="product_variant">{$lang->product_variant}:</div>
                            <select name="variant" class="fn_variant variant_select {if $product->variants|count < 2} hidden {else}fn_select2{/if}">
                                {foreach $product->variants as $v}
                                    <option{if $product->variant->id == $v->id} selected{/if} value="{$v->id}" data-price="{$v->price|convert}" data-stock="{$v->stock}"{if $v->compare_price > 0} data-cprice="{$v->compare_price|convert}"{if $v->compare_price>$v->price && $v->price>0} data-discount="{round((($v->price-$v->compare_price)/$v->compare_price)*100, 2)}&nbsp;%"{/if}{/if}{if $v->sku} data-sku="{$v->sku|escape}"{/if} {if $v->units}data-units="{$v->units}"{/if}>{if $v->name}{$v->name|escape}{else}{$product->name|escape}{/if}</option>
                                {/foreach}
                            </select>
                            <div class="dropDownSelect2"></div>
                        </div>

                        <div class="details_boxed__offer" itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
                            {* Schema.org *}
                            <span class="hidden">
                                <link itemprop="url" href="{url_generator route="product" url=$product->url absolute=1}" />
                                <time itemprop="priceValidUntil" datetime="{$product->created|date:'Ymd'}"></time>
                                {if $product->variant->stock > 0}
                                <link itemprop="availability" href="https://schema.org/InStock" />
                                {else}
                                <link itemprop="availability" href="http://schema.org/OutOfStock" />
                                {/if}
                                <link itemprop="itemCondition" href="https://schema.org/NewCondition" />
                                <span itemprop="seller" itemscope itemtype="http://schema.org/Organization">
                                <span itemprop="name">{$settings->site_name}</span></span>
                            </span>

                            <div class="d-flex flex-wrap align-items-center details_boxed__price_amount">
                                <div class="d-flex align-items-center details_boxed__prices">
                                    {* Old price *}
                                    <div class="d-flex align-items-center details_boxed__old_price {if !$product->variant->compare_price} hidden-xs-up{/if}">
                                        <span class="fn_old_price">{$product->variant->compare_price|convert}</span>
                                        <span class="currency">{$currency->sign|escape}</span>
                                    </div>
                                    {* Price *}
                                    <div class="d-flex align-items-center details_boxed__price {if $product->variant->compare_price} price--red{/if}">
                                        <span class="fn_price" itemprop="price" content="{$product->variant->price|convert:null:false}">{$product->variant->price|convert}</span>
                                        <span class="currency" itemprop="priceCurrency" content="{$currency->code|escape}">{$currency->sign|escape}</span>
                                    </div>

                                    <div class="fn_discount_label details_boxed_pct{if $product->variant->price>0 && $product->variant->compare_price>0 && $product->variant->compare_price>$product->variant->price}{else} hidden-xs-up{/if}">
                                        {if $product->variant->price>0 && $product->variant->compare_price>0 && $product->variant->compare_price>$product->variant->price}
                                        {round((($product->variant->price-$product->variant->compare_price)/$product->variant->compare_price)*100, 2)}&nbsp;%
                                        {/if}
                                    </div>
                                </div>

                                {* Quantity *}
                                <div class="details_boxed__amount">
                                    <div class="fn_is_stock{if $product->variant->stock < 1} hidden{/if}">
                                        {*<div class="details_boxed__title" data-language="product_quantity">
                                        {$lang->product_quantity}<span class="fn_units">{if $product->variant->units}, {$product->variant->units|escape}{/if}</span>:
                                    </div>*}
                                        <div class="fn_product_amount  amount">
                                            <span class="fn_minus amount__minus">&minus;</span>
                                            <input class="amount__input" type="text" name="amount" value="1" data-max="{$product->variant->stock}">
                                            <span class="fn_plus amount__plus">&plus;</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center details_boxed__buttons">
                                {if !$settings->is_preorder}
                                {* No stock *}
                                <p class="fn_not_preorder {if $product->variant->stock > 0} hidden-xs-up{/if}">
                                    <span class="product-page__button product-page__out_stock" data-language="product_out_of_stock">{$lang->product_out_of_stock}</span>
                                </p>
                                {else}
                                {* Preorder *}
                                <div class="fn_is_preorder {if $product->variant->stock > 0} hidden-xs-up{/if}">
                                    <button class="product-page__button product-page__button--preloader" type="submit" data-language="product_pre_order">{$lang->product_pre_order}</button>
                                </div>
                                {/if}

                                {* Submit button *}
                                <div class="fn_is_stock {if $product->variant->stock < 1} hidden-xs-up{/if}">
                                    <button class=" product-page__button button--blick" type="submit" data-language="product_add_cart">{$lang->product_add_cart}</button>
                                </div>

                                 <div class="d-flex align-items-center details_boxed__other">

                                     {fast_order_btn product=$product}

                                    {* Wishlist *}
                                    {if is_array($wishlist->ids) && in_array($product->id, $wishlist->ids)}
                                        <a href="#" data-id="{$product->id}" class="fn_wishlist product-page__wishlist selected" title="{$lang->product_remove_favorite}" data-result-text="{$lang->product_add_favorite}" data-language="product_remove_favorite">
                                            <i class="fa fa-heart"></i>
                                        </a>
                                    {else}
                                        <a href="#" data-id="{$product->id}" class="fn_wishlist product-page__wishlist" title="{$lang->product_add_favorite}" data-result-text="{$lang->product_remove_favorite}" data-language="product_add_favorite">
                                            <i class="fa fa-heart-o"></i>
                                        </a>
                                    {/if}

                                    {* Comparison *}
                                    {if is_array($comparison->ids) && in_array($product->id, $comparison->ids)}
                                        <a class="fn_comparison product-page__compare selected" href="#" data-id="{$product->id}" title="{$lang->product_remove_comparison}" data-result-text="{$lang->product_add_comparison}" data-language="product_remove_comparison">
                                            <i class="fa fa-balance-scale"></i>
                                        </a>
                                    {else}
                                        <a class="fn_comparison product-page__compare" href="#" data-id="{$product->id}" title="{$lang->product_add_comparison}" data-result-text="{$lang->product_remove_comparison}" data-language="product_add_comparison">
                                            <i class="fa fa-balance-scale"></i>
                                        </a>
                                    {/if}
                                </div>
                            </div>
                        </div>
                    </form>
                </div>


                <div class="fn_accordion accordion details_boxed__item details_boxed__item--inner">
                    <div class="details_boxed__item">
                        {* Delivery *}
                        <div class="accordion__item visible">
                            <div class="accordion__title active">
                                <div class="accordion__header d-flex justify-content-between align-items-center">
                                    <span data-language="product_delivery">{$lang->product_delivery}</span>
                                    <span class="accordion__arrow fa fa-chevron-down"></span>
                                </div>
                            </div>
                            <div class="accordion__content" style="display: block;">
                                <div class="">
                                    {$settings->product_deliveries}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="details_boxed__item">
                        {* Payments *}
                        <div class="accordion__item">
                            <div class="accordion__title">
                                <div class="accordion__header d-flex justify-content-between align-items-center">
                                    <span data-language="product_payment">{$lang->product_payment}</span>
                                    <span class="accordion__arrow fa fa-chevron-down"></span>
                                </div>
                            </div>
                            <div class="accordion__content">
                                <div class="">
                                    {$settings->product_payments}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {* Share buttons *}
                <div class="details_boxed__item details_boxed__share">
                    <div class="share">
                        <div class="share__text">
                            <span data-language="product_share">{$lang->product_share}:</span>
                        </div>
                        <div class="fn_share jssocials share__icons"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div id="fn_products_tab" class="product-page__tabs">
        <div class="block--border tabs">
            <div class="tabs__navigation hidden-sm-down">
                {if $description}
                <a class="tabs__link" href="#description">
                    <span data-language="product_description">{$lang->product_description}</span>
                </a>
                {/if}

                {if $product->features}
                <a class="tabs__link" href="#features">
                    <span data-language="product_features">{$lang->product_features}</span>
                </a>
                {/if}

                <a id="fn_tab_comments" class="tabs__link" href="#comments" >
                    <span data-language="product_comments">{$lang->product_comments}</span>
                </a>
            </div>

            <div class="tabs__content">
                {if $description}
                    <div id="description" class="tab product_description" itemprop="description">
                        <div class="fn_switch mobile_tab_navigation active hidden-md-up">
                            <div class="mobile_tab_title">
                                {include file="svg.tpl" svgId="description_icon"}
                                <span data-language="product_description">{$lang->product_description}</span>
                            </div>
                        </div>
                        <div class="mobile_tab__content">
                            <div class="block__description block__description--style">
                                {$description}
                            </div>
                         </div>
                    </div>
                {/if}

                {if $product->features}
                    <div id="features" class="tab">
                        <div class="fn_switch mobile_tab_navigation active hidden-md-up">
                            <div class="mobile_tab_title">
                                {include file="svg.tpl" svgId="features_icon"}
                                <span data-language="product_features">{$lang->product_features}</span>
                            </div>
                        </div>
                        <ul class="d-sm-flex flex-sm-wrap features mobile_tab__content">
                            {foreach $product->features as $f}
                            <li class="f_col-md-6 features__item">
                                <div class="d-flex justify-content-start features__wrap">
                                    <div class="features__name"><span>{$f->name|escape}:</span>{if $f->description}<span title="{$f->description}" style="margin-left: 10px; cursor: pointer; background: lightgreen; padding: 5px; border-radius: 5px;">i</span>{/if}</div>
                                    <div class="features__value">
                                        {foreach $f->values as $value}
                                        {if $category && $f->url_in_product && $f->in_filter && $value->to_index}
                                        <a href="{url_generator route="category" url=$category->url}{if !$settings->category_routes_template_slash_end}/{/if}{$f->url}-{$value->translit}">{$value->value|escape}</a>{if !$value@last},{/if}{*todo генерация урла*}
                                        {else}
                                        {$value->value|escape}{if !$value@last},{/if}
                                        {/if}
                                        {/foreach}
                                    </div>
                                </div>
                            </li>
                            {/foreach}
                        </ul>
                    </div>
                {/if}

                {* Comments *}
                <div id="comments" class="tab">
                    <div class="fn_switch mobile_tab_navigation active hidden-md-up">
                        <div class="mobile_tab_title">
                            {include file="svg.tpl" svgId="comment_icon"}
                            <span data-language="product_comments">{$lang->product_comments}</span>
                        </div>
                    </div>
                    <div class="mobile_tab__content comment-wrap f_row flex-lg-row align-items-md-start">
                        <div class="comment f_col-lg-7">
                            {if $comments}
                                {function name=comments_tree level=0}
                                    {foreach $comments as $comment}
                                    <div class="comment__item {if $level > 0} admin_note{/if}">
                                    {* Comment anchor *}
                                    <a name="comment_{$comment->id}"></a>
                                    {* Comment list *}
                                    <div class="comment__inner"> 
                                        <div class="comment__icon">
                                            {if $level > 0}
                                                {include file="svg.tpl" svgId="comment-admin_icon"} 
                                            {else}
                                                {include file="svg.tpl" svgId="comment-user_icon"}
                                            {/if}
                                        </div>
                                        <div class="comment__boxed">
                                            <div class="d-flex flex-wrap align-items-center justify-content-between comment__header">
                                                {* Comment name *}
                                                <div class="d-flex flex-wrap align-items-center comment__author">
                                                    <span class="comment__name">{$comment->name|escape}</span>
                                                    {* Comment status *}
                                                    {if !$comment->approved}
                                                        <span class="comment__status" data-language="post_comment_status">({$lang->post_comment_status})</span>
                                                    {/if}
                                                </div>
                                                {* Comment date *}
                                                <div class="comment__date">
                                                    <span>{$comment->date|date}, {$comment->date|time}</span>
                                                </div>
                                            </div>
    
                                            {* Comment content *}
                                            <div class="comment__body">
                                                {$comment->text|escape|nl2br}
                                            </div>
                                        </div>
                                    </div>
                                    {if !empty($comment->children)}
                                        {comments_tree comments=$comment->children level=$level+1}
                                    {/if}
                                    </div>
                                    {/foreach}
                                {/function}
                                {comments_tree comments=$comments}
                            {else}
                                <div class="boxed boxed--middle boxed--notify">
                                    <span data-language="product_no_comments">{$lang->product_no_comments}</span>
                                </div>
                            {/if}
                        </div>

                        <div class="form_wrap f_col-lg-5">
                            {* Comment form *}
                            <form id="captcha_id" class="form form--boxed fn_validate_product" method="post">

                                {if $settings->captcha_type == "v3"}
                                    <input type="hidden" class="fn_recaptcha_token fn_recaptchav3" name="recaptcha_token" />
                                {/if}

                                <div class="form__header">
                                    <div class="form__title">
                                        {include file="svg.tpl" svgId="comment_icon"}
                                        <span data-language="product_write_comment">{$lang->product_write_comment}</span>
                                    </div>

                                    {* Form error messages *}
                                    {if $error}
                                    <div class="message_error">
                                        {if $error=='captcha'}
                                        <span data-language="form_error_captcha">{$lang->form_error_captcha}</span>
                                        {elseif $error=='empty_name'}
                                        <span data-language="form_enter_name">{$lang->form_enter_name}</span>
                                        {elseif $error=='empty_comment'}
                                        <span data-language="form_enter_comment">{$lang->form_enter_comment}</span>
                                        {elseif $error=='empty_email'}
                                        <span data-language="form_enter_email">{$lang->form_enter_email}</span>
                                        {/if}
                                    </div>
                                    {/if}
                                </div>
                                <div class="form__body">
                                    {* User's name *}
                                    <div class="form__group">
                                        <input class="form__input form__placeholder--focus" type="text" name="name" value="{if $request_data.name}{$request_data.name|escape}{elseif $user->name}{$user->name|escape}{/if}" />
                                        <span class="form__placeholder">{$lang->form_name}*</span>
                                    </div>

                                    {* User's email *}
                                    <div class="form__group">
                                        <input class="form__input form__placeholder--focus" type="text" name="email" value="{if $request_data.email}{$request_data.email|escape}{elseif $user->email}{$user->email|escape}{/if}" data-language="form_email" />
                                        <span class="form__placeholder">{$lang->form_email}</span>
                                    </div>
                                    
                                    {* User's comment *}
                                    <div class="form__group">
                                        <textarea class="form__textarea form__placeholder--focus" rows="3" name="text" >{$request_data.text}</textarea>
                                        <span class="form__placeholder">{$lang->form_enter_comment}*</span>
                                    </div>
                                </div>
                                <div class="form__footer">
                                    {* Captcha *}
                                    {if $settings->captcha_comment}
                                        {if $settings->captcha_type == "v2"}
                                            <div class="captcha">
                                                <div id="recaptcha1"></div>
                                            </div>
                                        {elseif $settings->captcha_type == "default"}
                                        {get_captcha var="captcha_comment"}
                                            <div class="captcha">
                                                <div class="secret_number">{$captcha_comment[0]|escape} + ? =  {$captcha_comment[1]|escape}</div>
                                                <div class="form__captcha">
                                                    <input class="form__input form__input_captcha form__placeholder--focus" type="text" name="captcha_code" value="" />
                                                    <span class="form__placeholder">{$lang->form_enter_captcha}*</span>
                                                </div>
                                            </div>
                                        {/if}
                                    {/if}
                                    
                                    <input type="hidden" name="comment" value="1">
                                    {* Submit button *}
                                    <input class="form__button g-recaptcha" type="submit" name="comment" data-language="form_send" {if $settings->captcha_type == "invisible"}data-sitekey="{$settings->public_recaptcha_invisible}" data-badge='bottomleft' data-callback="onSubmit"{/if} value="{$lang->form_send}"/>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {* Previous/Next product *}
    {if $prev_product || $next_product}
        <nav>
            <ol class="pager row">
                <li class="col-xs-12{if $next_product} col-sm-6{else} col-sm-12{/if}">
                    {if $prev_product}
                    <a class="d-flex align-items-center justify-content-center" href="{url_generator route="product" url=$prev_product->url}">
                        {include file="svg.tpl" svgId="arrow_up_icon"}
                        <span>{$prev_product->name|escape}</span></a>
                    {/if}
                </li>
                <li class="col-xs-12 col-sm-6">
                    {if $next_product}
                    <a class="d-flex align-items-center justify-content-center" href="{url_generator route="product" url=$next_product->url}">
                        <span>{$next_product->name|escape}</span>
                        {include file="svg.tpl" svgId="arrow_up_icon"}
                    </a>
                    {/if}
                </li>
            </ol>
        </nav>
    {/if}
</div>

{* Related products *}
{if $related_products}
    <div class="block block--boxed block--border">
        <div class="block__header">
            <div class="block__title">
                <span data-language="product_recommended_products">{$lang->product_recommended_products}</span>
            </div>
        </div>

        <div class="block__body">
            <div class="products_list row">
                {foreach $related_products as $p}
                <div class="product_item col-xs-6 col-sm-4 col-md-4 col-xl-25">
                    {include "product_list.tpl" product = $p}
                </div>
                {/foreach}
            </div>
        </div>
    </div>
{/if}

{if $related_posts}
    <div class="block block--boxed block--border">
        <div class="block__header">
            <div class="block__title">
                <span data-language="product_related_post">{$lang->product_related_post}</span>
            </div>
        </div>
        <div class="block__body article">
            <div class="fn_articles_slide article_list row no_gutters">
                {foreach $related_posts as $r_p}
                    <div class="article_item no_hover col-sm-6 col-md-6 col-lg-3 col-xl-3">
                        {include 'post_list.tpl' post = $r_p}
                    </div>
                {/foreach}
            </div>
        </div>
    </div>
{/if}

{*микроразметка по схеме JSON-LD*}
{*
Микроразметка Json-LD отключена в связи с тем, что Яндекс не воспринимает Json-LD,
а Google расценивает двойную разметку (Microdata и Json-LD) как спам.
Если нужно разметить для Яндекс, то включаем Json-LD, а Microdata отключаем.
*}
{*
{literal}
<script type="application/ld+json">
{
"@context": "http://schema.org/",
"@type": "Product",
"name": "{/literal}{$product->name|escape}{literal}",
"image": "{/literal}{$product->image->filename|resize:330:300}{literal}",
"description": "{/literal}{str_replace(array("\r", "\n"), "", $product->annotation|strip_tags|escape)}{literal}",
"mpn": "{/literal}{if $product->variant->sku}{$product->variant->sku|escape}{else}Не указано{/if}{literal}",
{/literal}
{if $brand->name}
{literal}
"brand": {
"@type": "Brand",
"name": "{/literal}{$brand->name|escape}{literal}"
},
{/literal}
{/if}
{if $product->rating > 0}
{literal}
"aggregateRating": {
"@type": "AggregateRating",
"ratingValue": "{/literal}{$product->rating|string_format:'%.1f'}{literal}",
"ratingCount": "{/literal}{$product->votes|string_format:'%.0f'}{literal}"
},
{/literal}
{/if}
{literal}
"offers": {
"@type": "Offer",
"priceCurrency": "{/literal}{$currency->code|escape}{literal}",
"price": "{/literal}{$product->variant->price|convert:null:false}{literal}",
"priceValidUntil": "{/literal}{$smarty.now|date_format:'%Y-%m-%d'}{literal}",
"itemCondition": "http://schema.org/NewCondition",
{/literal}
{if $product->variant->stock > 0}
{literal}
"availability": "http://schema.org/InStock",
{/literal}
{else}
{literal}
"availability": "http://schema.org/OutOfStock",
{/literal}
{/if}
{literal}
"seller": {
"@type": "Organization",
"name": "{/literal}{$settings->site_name|escape}{literal}"
}
}
}
</script>
{/literal}
*}
