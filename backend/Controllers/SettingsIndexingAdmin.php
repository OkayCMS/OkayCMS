<?php


namespace Okay\Admin\Controllers;


use Giggsey\Locale\Locale;
use libphonenumber\PhoneNumberUtil;
use Okay\Admin\Helpers\BackendSettingsHelper;
use Okay\Core\BackendTranslations;
use Okay\Core\Phone;

class SettingsIndexingAdmin extends IndexAdmin
{
    public function fetch()
    {
        if ($this->request->method('post')) {
            
            $this->settings->set('canonical_catalog_pagination', 
                $this->request->post('canonical_catalog_pagination', null, CANONICAL_FIRST_PAGE)
            );
            $this->settings->set('canonical_catalog_page_all', 
                $this->request->post('canonical_catalog_page_all', null, CANONICAL_FIRST_PAGE)
            );
            $this->settings->set('canonical_category_brand', 
                $this->request->post('canonical_category_brand', null, CANONICAL_WITHOUT_FILTER)
            );
            $this->settings->set('canonical_category_features', 
                $this->request->post('canonical_category_features', null, CANONICAL_WITHOUT_FILTER)
            );
            $this->settings->set('canonical_catalog_other_filter', 
                $this->request->post('canonical_catalog_other_filter', null, CANONICAL_WITHOUT_FILTER)
            );
            $this->settings->set('canonical_catalog_filter_pagination', 
                $this->request->post('canonical_catalog_filter_pagination', null, CANONICAL_WITHOUT_FILTER_FIRST_PAGE)
            );
            
            $this->settings->set('robots_catalog_pagination', 
                $this->request->post('robots_catalog_pagination', null, ROBOTS_INDEX_FOLLOW)
            );
            $this->settings->set('robots_catalog_page_all', 
                $this->request->post('robots_catalog_page_all', null, ROBOTS_INDEX_FOLLOW)
            );
            $this->settings->set('robots_category_brand', 
                $this->request->post('robots_category_brand', null, ROBOTS_INDEX_FOLLOW)
            );
            $this->settings->set('robots_category_features', 
                $this->request->post('robots_category_features', null, ROBOTS_INDEX_FOLLOW)
            );
            $this->settings->set('robots_catalog_other_filter', 
                $this->request->post('robots_catalog_other_filter', null, ROBOTS_INDEX_FOLLOW)
            );
            $this->settings->set('robots_catalog_filter_pagination', 
                $this->request->post('robots_catalog_filter_pagination', null, ROBOTS_INDEX_FOLLOW)
            );

            $this->settings->set('max_brands_filter_depth', $this->request->post('max_brands_filter_depth', 'integer', 0));
            $this->settings->set('max_other_filter_depth', $this->request->post('max_other_filter_depth', 'integer', 0));
            $this->settings->set('max_features_filter_depth', $this->request->post('max_features_filter_depth', 'integer', 0));
            $this->settings->set('max_features_values_filter_depth', $this->request->post('max_features_values_filter_depth', 'integer', 0));
            $this->settings->set('max_filter_depth', $this->request->post('max_filter_depth', 'integer', 0));
            
            $this->design->assign('message_success', 'saved');
        }
        
        $this->response->setContent('settings_indexing.tpl');
    }
}