<?php


namespace Okay\Admin\Helpers;


use Okay\Core\Config;
use Okay\Core\DataCleaner;
use Okay\Core\EntityFactory;
use Okay\Core\Image;
use Okay\Core\JsSocial;
use Okay\Core\Languages;
use Okay\Core\Managers;
use Okay\Core\QueryFactory;
use Okay\Core\Request;
use Okay\Core\Settings;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\TemplateConfig\FrontTemplateConfig;
use Okay\Entities\LanguagesEntity;
use Okay\Entities\ManagersEntity;
use Okay\Entities\AdvantagesEntity;

class BackendSettingsHelper
{
    /**
     * @var Settings
     */
    private $settings;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var ManagersEntity
     */
    private $managersEntity;

    /**
     * @var LanguagesEntity
     */
    private $languagesEntity;

    /**
     * @var AdvantagesEntity
     */
    private $advantagesEntity;

    /**
     * @var DataCleaner
     */
    private $dataCleaner;

    /**
     * @var Managers
     */
    private $managers;

    /**
     * @var FrontTemplateConfig
     */
    private $frontTemplateConfig;

    /**
     * @var QueryFactory
     */
    private $queryFactory;

    /**
     * @var Languages
     */
    private $languages;

    /**
     * @var JsSocial
     */
    private $jsSocial;

    /**
     * @var Image
     */
    private $imageCore;

    private $allowedImageExtensions = ['png', 'gif', 'jpg', 'jpeg', 'ico', 'svg'];

    public function __construct(
        Settings $settings,
        Request $request,
        Config $config,
        EntityFactory $entityFactory,
        DataCleaner $dataCleaner,
        Managers $managers,
        FrontTemplateConfig $frontTemplateConfig,
        QueryFactory $queryFactory,
        Languages $languages,
        JsSocial $jsSocial,
        Image $imageCore
    )
    {
        $this->managersEntity = $entityFactory->get(ManagersEntity::class);
        $this->languagesEntity = $entityFactory->get(LanguagesEntity::class);
        $this->advantagesEntity = $entityFactory->get(AdvantagesEntity::class);
        $this->settings = $settings;
        $this->request = $request;
        $this->config = $config;
        $this->managers = $managers;
        $this->frontTemplateConfig = $frontTemplateConfig;
        $this->queryFactory = $queryFactory;
        $this->languages = $languages;
        $this->jsSocial = $jsSocial;
        $this->dataCleaner = $dataCleaner;
        $this->imageCore = $imageCore;
    }

    public function updateSettings()
    {
        $this->settings->set('decimals_point', $this->request->post('decimals_point', null, ','));
        $this->settings->set('thousands_separator', $this->request->post('thousands_separator'));
        $this->settings->set('products_num', $this->request->post('products_num', 'int', 24));
        $this->settings->set('max_order_amount', $this->request->post('max_order_amount', 'int', 50));
        $this->settings->set('comparison_count', $this->request->post('comparison_count', 'int', 5));
        $this->settings->set('posts_num', $this->request->post('posts_num', 'int', 8));
        $this->settings->set('missing_products', $this->request->post('missing_products', null, 'default'));
        $this->settings->set('hide_single_filters', $this->request->post('hide_single_filters', 'int'));
        $this->settings->set('support_webp', $this->request->post('support_webp', 'int'));
        $this->settings->set('features_cache_ttl', $this->request->post('features_cache_ttl', 'int'));
        $this->settings->set('deferred_load_features', $this->request->post('deferred_load_features', 'int'));
        $this->settings->update('units', $this->request->post('units'));

        if ($this->request->post('is_preorder', 'integer')) {
            $this->settings->set('is_preorder', $this->request->post('is_preorder', 'integer'));
        } else {
            $this->settings->set('is_preorder', 0);
        }

        if ($this->request->post('show_empty_categories', 'integer')) {
            $this->settings->set('show_empty_categories', $this->request->post('show_empty_categories', 'integer'));
        } else {
            $this->settings->set('show_empty_categories', 0);
        }

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function deleteWatermark()
    {
        unlink($this->config->root_dir . $this->config->watermark_file);
        $this->config->watermark_file = '';
        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function clearCatalog()
    {
        $this->dataCleaner->clearAllCatalogImages();
        $this->dataCleaner->clearCatalogData();
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function updateWatermark()
    {
        $error = '';

        // Водяной знак
        $clearImageCache = false;
        if ($this->request->post('delete_watermark')) {
            $clearImageCache = true;
            @unlink($this->config->root_dir . $this->config->watermark_file);
            $this->config->watermark_file = '';
        }

        $watermark = $this->request->files('watermark_file', 'tmp_name');
        if (!empty($watermark) && in_array(pathinfo($this->request->files('watermark_file', 'name'), PATHINFO_EXTENSION), $this->allowedImageExtensions)) {
            $this->config->watermark_file = 'backend/files/watermark/watermark.png';
            if (@move_uploaded_file($watermark, $this->config->root_dir . $this->config->watermark_file)) {
                $clearImageCache = true;
            } else {
                $error = 'watermark_is_not_writable';
            }
        }

        if ($this->settings->watermark_offset_x != $this->request->post('watermark_offset_x')) {
            $this->settings->watermark_offset_x = $this->request->post('watermark_offset_x');
            $clearImageCache = true;
        }

        if ($this->settings->watermark_offset_y != $this->request->post('watermark_offset_y')) {
            $this->settings->watermark_offset_y = $this->request->post('watermark_offset_y');
            $clearImageCache = true;
        }

        // Удаление заресайзеных изображений
        if ($clearImageCache === true) {
            $this->dataCleaner->clearResizeImages();
        }

        return ExtenderFacade::execute(__METHOD__, $error, func_get_args());
    }

    public function updateCounters($counters)
    {
        $this->settings->set('counters', $counters);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function findCounters()
    {
        return ExtenderFacade::execute(__METHOD__, $this->settings->get('counters'), func_get_args());
    }

    public function updateGeneralSettings()
    {
        $this->settings->set('phone_default_region', $this->request->post('phone_default_region'));
        $this->settings->set('phone_default_format', $this->request->post('phone_default_format'));
        $this->settings->set('date_format', $this->request->post('date_format'));
        $this->settings->set('site_work', $this->request->post('site_work'));
        $this->settings->set('captcha_comment', $this->request->post('captcha_comment', 'boolean'));
        $this->settings->set('captcha_cart', $this->request->post('captcha_cart', 'boolean'));
        $this->settings->set('captcha_register', $this->request->post('captcha_register', 'boolean'));
        $this->settings->set('captcha_feedback', $this->request->post('captcha_feedback', 'boolean'));
        $this->settings->set('captcha_callback', $this->request->post('captcha_callback', 'boolean'));
        $this->settings->set('public_recaptcha', $this->request->post('public_recaptcha'));
        $this->settings->set('secret_recaptcha', $this->request->post('secret_recaptcha'));
        $this->settings->set('public_recaptcha_invisible', $this->request->post('public_recaptcha_invisible'));
        $this->settings->set('secret_recaptcha_invisible', $this->request->post('secret_recaptcha_invisible'));
        $this->settings->set('captcha_type', $this->request->post('captcha_type'));
        $this->settings->set('gather_enabled', $this->request->post('gather_enabled', 'boolean'));
        $this->settings->set('public_recaptcha_v3', $this->request->post('public_recaptcha_v3'));
        $this->settings->set('secret_recaptcha_v3', $this->request->post('secret_recaptcha_v3'));
        $this->settings->update('site_name', $this->request->post('site_name'));
        $this->settings->update('site_annotation', $this->request->post('site_annotation'));

        if ($recaptcha_scores = $this->request->post('recaptcha_scores')) {
            foreach ($recaptcha_scores as $k => $score) {
                $score = (float)str_replace(',', '.', $score);
                $recaptcha_scores[$k] = round($score, 1);
            }
        }
        $this->settings->set('recaptcha_scores', $recaptcha_scores);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function updateNotifySettings()
    {
        $this->settings->set('order_email', $this->request->post('order_email'));
        $this->settings->set('comment_email', $this->request->post('comment_email'));
        $this->settings->set('notify_from_email', $this->request->post('notify_from_email'));
        $this->settings->set('email_lang', $this->request->post('email_lang'));
        $this->settings->set('auto_approved', $this->request->post('auto_approved'));
        $this->settings->set('use_smtp', $this->request->post('use_smtp'));
        $this->settings->set('smtp_server', $this->request->post('smtp_server'));
        $this->settings->set('smtp_port', $this->request->post('smtp_port'));
        $this->settings->set('smtp_user', $this->request->post('smtp_user'));
        $this->settings->set('smtp_pass', $this->request->post('smtp_pass'));
        $this->settings->set('disable_validate_smtp_certificate', $this->request->post('disable_validate_smtp_certificate', 'int'));
        $this->settings->update('notify_from_name', $this->request->post('notify_from_name'));
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function updateRouterSettings()
    {
        $this->settings->set('category_routes_template', $this->request->post('category_routes_template'));
        $this->settings->set('category_routes_template__default', trim($this->request->post('category_routes_template__default')));
        $this->settings->set('category_routes_template__prefix_and_path', trim($this->request->post('category_routes_template__prefix_and_path')));
        $this->settings->set('category_routes_template_slash_end', $this->request->post('category_routes_template_slash_end'));

        $this->settings->set('product_routes_template', $this->request->post('product_routes_template'));
        $this->settings->set('product_routes_template__prefix_and_path', trim($this->request->post('product_routes_template__prefix_and_path')));
        $this->settings->set('product_routes_template__default', trim($this->request->post('product_routes_template__default')));
        $this->settings->set('product_routes_template_slash_end', $this->request->post('product_routes_template_slash_end'));

        $this->settings->set('brand_routes_template', $this->request->post('brand_routes_template'));
        $this->settings->set('brand_routes_template__default', trim($this->request->post('brand_routes_template__default')));
        $this->settings->set('brand_routes_template_slash_end', $this->request->post('brand_routes_template_slash_end'));

        $this->settings->set('blog_category_routes_template', $this->request->post('blog_category_routes_template'));
        $this->settings->set('blog_category_routes_template__default', trim($this->request->post('blog_category_routes_template__default')));
        $this->settings->set('blog_category_routes_template__prefix_and_path', trim($this->request->post('blog_category_routes_template__prefix_and_path')));
        $this->settings->set('blog_category_routes_template_slash_end', $this->request->post('blog_category_routes_template_slash_end'));

        $this->settings->set('post_routes_template', $this->request->post('post_routes_template'));
        $this->settings->set('post_routes_template__prefix_and_path', trim($this->request->post('post_routes_template__prefix_and_path')));
        $this->settings->set('post_routes_template__default', trim($this->request->post('post_routes_template__default')));
        $this->settings->set('post_routes_template_slash_end', $this->request->post('post_routes_template_slash_end'));

        $this->settings->set('all_brands_routes_template__default', trim($this->request->post('all_brands_routes_template__default')));
        $this->settings->set('all_blog_routes_template__default', trim($this->request->post('all_blog_routes_template__default')));

        $this->settings->set('all_brands_routes_template_slash_end', $this->request->post('all_brands_routes_template_slash_end'));
        $this->settings->set('all_blog_routes_template_slash_end', $this->request->post('all_blog_routes_template_slash_end'));
        $this->settings->set('all_news_routes_template_slash_end', $this->request->post('all_news_routes_template_slash_end'));

        $this->settings->set('global_unique_url', $this->request->post('global_unique_url'));
        $this->settings->set('page_routes_template_slash_end', $this->request->post('page_routes_template_slash_end'));

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function updateThemeSettings()
    {
        if ($cssColors = $this->request->post('css_colors')) {
            $this->frontTemplateConfig->updateCssVariables($cssColors);
        }

        if ($this->settings->get('social_share_theme') != $this->request->post('social_share_theme')) {
            $this->frontTemplateConfig->clearCompiled();
        }

        $this->settings->set('social_share_theme', $this->request->post('social_share_theme'));
        $this->settings->set('sj_shares', $this->request->post('sj_shares'));
        $this->settings->set('site_email', $this->request->post('site_email'));

        $siteSocialLinks = $this->request->post('site_social_links');
        if (!empty($siteSocialLinks)) {
            $linksArray = explode(PHP_EOL, $siteSocialLinks);
            foreach ($linksArray as $key => $link) {
                if (trim($link) == '') {
                    unset($linksArray[$key]);
                }
            }

            $this->settings->set('site_social_links', $linksArray);
        } else {
            $this->settings->set('site_social_links', '');
        }

        $this->settings->update('site_working_hours', $this->request->post('site_working_hours'));
        $this->settings->update('product_deliveries', $this->request->post('product_deliveries'));
        $this->settings->update('product_payments', $this->request->post('product_payments'));

        $phones = [];
        if ($sitePhones = $this->request->post('site_phones')) {
            foreach (explode(',', $sitePhones) as $k => $phone) {
                $phones[$k] = trim($phone);
            }
        }
        $this->settings->set('site_phones', $phones);

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function getDesignImagesDir()
    {
        $designImagesDir = $this->config->root_dir . '/' . $this->config->design_images;
        return ExtenderFacade::execute(__METHOD__, $designImagesDir, func_get_args());
    }

    public function uploadFavicon()
    {
        if (empty($_FILES['site_favicon']['name'])) {
            return ExtenderFacade::execute(__METHOD__, null, func_get_args());
        }

        $designImagesDir = $this->config->get('root_dir') . '/' . $this->config->get('design_images');
        $tmpName = $_FILES['site_favicon']['tmp_name'];
        $ext = pathinfo($_FILES['site_favicon']['name'], PATHINFO_EXTENSION);
        $siteFaviconName = 'favicon.' . $ext;

        @unlink($designImagesDir . $this->settings->get('site_favicon'));
        if (move_uploaded_file($tmpName, $designImagesDir . $siteFaviconName)) {
            $this->settings->set('site_favicon', $siteFaviconName);
            $siteFaviconVersion = ltrim($this->settings->get('site_favicon_version'), '0');

            if (!$siteFaviconVersion) {
                $siteFaviconVersion = 0;
            }

            $this->settings->set('site_favicon_version', str_pad(++$siteFaviconVersion, 3, 0, STR_PAD_LEFT));
        }

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function deleteFavicon()
    {
        $designImagesDir = $this->config->get('root_dir') . '/' . $this->config->get('design_images');
        $filename = $designImagesDir . $this->settings->get('site_favicon');

        if (is_file($filename)) {
            @unlink($filename);
        }

        $this->settings->set('site_favicon', '');
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function getSitePhones()
    {
        $sitePhones = $this->settings->get('site_phones');
        $stringifySitePhones = !empty($sitePhones) ? implode(', ', $sitePhones) : "";
        return ExtenderFacade::execute(__METHOD__, $stringifySitePhones, func_get_args());
    }

    public function getAllowImageExtensions()
    {
        return ExtenderFacade::execute(__METHOD__, $this->allowedImageExtensions, func_get_args());
    }

    public function getJsSocials()
    {
        return ExtenderFacade::execute(__METHOD__, $this->jsSocial->getSocials(), func_get_args());
    }

    public function getJsCustomSocials()
    {
        return ExtenderFacade::execute(__METHOD__, $this->jsSocial->getCustomSocials(), func_get_args());
    }

    public function getSiteSocialLinks()
    {
        if (!empty($this->settings->get('site_social_links'))) {
            $siteSocialLinks = implode(PHP_EOL, $this->settings->get('site_social_links'));
        } else {
            $siteSocialLinks = '';
        }

        return ExtenderFacade::execute(__METHOD__, $siteSocialLinks, func_get_args());
    }

    public function getCssVariables()
    {
        return ExtenderFacade::execute(__METHOD__, $this->frontTemplateConfig->getCssVariables(), func_get_args());
    }

    public function uploadSiteLogo()
    {
        $error = '';
        $siteLogoName = $this->settings->get('site_logo');
        $logoLang = '';

        $this->settings->set('iframe_map_code', $this->request->post('iframe_map_code'));
        $multiLangLogo = $this->request->post('multilang_logo', 'integer');
        $designImagesDir = $this->config->get('root_dir') . '/' . $this->config->get('design_images');

        // если лого мультиязычное, добавим префикс в виде лейбла языка
        if ($multiLangLogo == 1) {
            $currentLang = $this->languagesEntity->get($this->languages->getLangId());
            $logoLang = '_' . $currentLang->label;
        }

        if ($_FILES['site_logo']['error'] == UPLOAD_ERR_OK) {
            $tmpName = $_FILES['site_logo']['tmp_name'];
            $ext = pathinfo($_FILES['site_logo']['name'], PATHINFO_EXTENSION);
            $siteLogoName = 'logo' . $logoLang . '.' . $ext;

            if (in_array($ext, $this->allowedImageExtensions)) {
                // Удаляем старое лого
                @unlink($designImagesDir . $this->settings->get('site_logo'));

                // Загружаем новое лого
                if (move_uploaded_file($tmpName, $designImagesDir . $siteLogoName)) {
                    $siteLogoVersion = ltrim($this->settings->get('site_logo_version'), '0');
                    if (!$siteLogoVersion) {
                        $siteLogoVersion = 0;
                    }

                    if ($multiLangLogo == 1) {
                        $this->settings->update('site_logo', $siteLogoName);
                    } else {
                        $this->settings->set('site_logo', $siteLogoName);
                    }

                    $this->settings->set('site_logo_version', str_pad(++$siteLogoVersion, 3, 0, STR_PAD_LEFT));
                }
            } else {
                $siteLogoName = '';
                $error = 'wrong_logo_ext';
            }
        }

        // Если раньше лого было не мультиязычным, а теперь будет, нужно его продублировать на все языки
        if ($this->settings->get('multilang_logo') == 0 && $multiLangLogo == 1) {
            $currentLang = $this->languagesEntity->get($this->languages->getLangId());
            $ext = pathinfo($siteLogoName, PATHINFO_EXTENSION);

            foreach ($this->languagesEntity->find() as $language) {
                $this->languages->setLangId($language->id);

                $langLogoName = 'logo_' . $language->label . '.' . $ext;

                // Дублируем лого на все языки
                if (file_exists($designImagesDir . $siteLogoName) && $siteLogoName != $langLogoName) {
                    copy($designImagesDir . $siteLogoName, $designImagesDir . $langLogoName);
                }

                $this->settings->update('site_logo', $langLogoName);
            }

            // Удалим старое, не мультиязычное лого
            if (file_exists($designImagesDir . $siteLogoName) && $siteLogoName != 'logo_' . $currentLang->label . '.' . $ext) {
                unlink($designImagesDir . $siteLogoName);
            }

            $this->languages->setLangId($currentLang->id);
        } // Если раньше лого было мультиязычным, а теперь будет не мультиязычным, нужно сохранить его из основного языка
        elseif ($this->settings->get('multilang_logo') == 1 && $multiLangLogo == 0) {
            $currentLangId = $this->languages->getLangId();
            $mainLang = $this->languagesEntity->getMainLanguage();
            $ext = pathinfo($siteLogoName, PATHINFO_EXTENSION);
            $langLogoName = 'logo_' . $mainLang->label . '.' . $ext;
            $siteLogoName = 'logo.' . $ext;

            // Дублируем лого из основного языка
            if (file_exists($designImagesDir . $langLogoName)) {
                copy($designImagesDir . $langLogoName, $designImagesDir . $siteLogoName);
            }

            foreach ($this->languagesEntity->find() as $language) {
                $this->languages->setLangId($language->id);
                $this->settings->initSettings();

                // Удалим все мультиязычные лого
                @unlink($designImagesDir . $this->settings->get('site_logo'));
            }

            // Удалим упоминание о лого в мультиленгах
            $delete = $this->queryFactory->newDelete();
            $delete->from('__settings_lang')
                ->where("param ='site_logo'")
                ->execute();

            $this->settings->set('site_logo', $siteLogoName);

            // Вернем lang_id и мультиязычные настройки
            $this->languages->setLangId($currentLangId);
        }

        $this->settings->set('multilang_logo', $multiLangLogo);

        return ExtenderFacade::execute(__METHOD__, $error, func_get_args());
    }

    public function deleteSiteLogo()
    {
        $designImagesDir = $this->config->get('root_dir') . '/' . $this->config->get('design_images');
        $multiLangLogo = $this->request->post('multilang_logo', 'integer');

        unlink($designImagesDir . $this->settings->get('site_logo'));
        if ($multiLangLogo == 1) {
            $this->settings->update('site_logo', '');
        } else {
            $this->settings->set('site_logo', '');
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function initSettings()
    {
        $this->settings->initSettings();
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function findAdvantages($filter = [])
    {
        $advantages = $this->advantagesEntity->find($filter);
        return ExtenderFacade::execute(__METHOD__, $advantages, func_get_args());
    }

    public function updateAdvantage(
        $advantageId,
        $updates,
        $advantageImagesToUpload,
        $advantageImagesToDelete
    ){
        if (in_array($advantageId, $advantageImagesToDelete)) {
            $this->deleteAdvantageImage($advantageId);
        }

        if (in_array($advantageId, array_keys($advantageImagesToUpload))) {
            $this->uploadAdvantageImage($advantageId, $advantageImagesToUpload[$advantageId]);
        }

        $this->advantagesEntity->update($advantageId, $updates);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function deleteAdvantage($ids)
    {
        $result = $this->advantagesEntity->delete($ids);
        return ExtenderFacade::execute(__METHOD__, $result, func_get_args());
    }

    public function sortPositionAdvantage($positions)
    {
        $positions = (array) $positions;
        $ids       = array_keys($positions);
        sort($positions);
        return ExtenderFacade::execute(__METHOD__, [$ids, $positions], func_get_args());
    }

    public function updatePositionAdvantage($ids, $positions)
    {
        foreach ($positions as $i => $position) {
            $this->advantagesEntity->update($ids[$i], ['position' => (int)$position]);
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function uploadAdvantageImage($advantageId, $fileImage)
    {
        if (!empty($fileImage['name']) &&
            ($filename = $this->imageCore->uploadImage(
                $fileImage['tmp_name'],
                $fileImage['name'],
                $this->config->original_advantages_dir))
        ) {
            $this->imageCore->deleteImage(
                $advantageId,
                'filename',
                AdvantagesEntity::class,
                $this->config->original_advantages_dir,
                $this->config->resized_advantages_dir
            );

            $this->advantagesEntity->update($advantageId, ['filename' => $filename]);
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    private function deleteAdvantageImage($advantageId)
    {
        $this->imageCore->deleteImage(
            $advantageId,
            'filename',
            AdvantagesEntity::class,
            $this->config->original_advantages_dir,
            $this->config->resized_advantages_dir
        );

        $this->advantagesEntity->update($advantageId, ['filename' => '']);
    }
}