<?php

use Okay\Core\Design;

require_once 'configure.php';

// Проверка сессии для защиты от xss
if (!$request->checkSession()) {
    trigger_error('Session expired', E_USER_WARNING);
    exit();
}

$result = '';
/*Принимаем данные от объекта, который нужно обновить*/
$id = intval($request->post('id'));
$object = $request->post('object');
$values = $request->post('values');
$entity = null;

/*В зависимости от сущности, обновляем её*/
switch ($object) {
    case 'product':
        if ($managers->access('products', $manager)) {
            $entity = $entityFactory->get(\Okay\Entities\ProductsEntity::class);
        }
        break;
    case 'variant':
        if ($managers->access('products', $manager)) {
            $entity = $entityFactory->get(\Okay\Entities\VariantsEntity::class);
        }
        break;
    case 'category':
        if ($managers->access('categories', $manager)) {
            $entity = $entityFactory->get(\Okay\Entities\CategoriesEntity::class);
        }
        break;
    case 'blog_category':
        if ($managers->access('blog_categories', $manager)) {
            $entity = $entityFactory->get(\Okay\Entities\BlogCategoriesEntity::class);
        }
        break;
    case 'authors':
        if ($managers->access('authors', $manager)) {
            $entity = $entityFactory->get(\Okay\Entities\AuthorsEntity::class);
        }
        break;
    case 'brands':
        if ($managers->access('brands', $manager)) {
            $entity = $entityFactory->get(\Okay\Entities\BrandsEntity::class);
        }
        break;
    case 'feature':
        if ($managers->access('features', $manager)) {
            $entity = $entityFactory->get(\Okay\Entities\FeaturesEntity::class);
        }
        break;
    case 'page':
        if ($managers->access('pages', $manager)) {
            $entity = $entityFactory->get(\Okay\Entities\PagesEntity::class);
        }
        break;
    case 'menu':
        if ($managers->access('pages', $manager)) {
            $entity = $entityFactory->get(\Okay\Entities\MenuEntity::class);
        }
        break;
    case 'menu_item':
        if ($managers->access('pages', $manager)) {
            $entity = $entityFactory->get(\Okay\Entities\MenuItemsEntity::class);
        }
        break;
    case 'blog':
        if ($managers->access('blog', $manager)) {
            $entity = $entityFactory->get(\Okay\Entities\BlogEntity::class);
        }
        break;
    case 'delivery':
        if ($managers->access('delivery', $manager)) {
            $entity = $entityFactory->get(\Okay\Entities\DeliveriesEntity::class);
        }
        break;
    case 'payment':
        if ($managers->access('payment', $manager)) {
            $entity = $entityFactory->get(\Okay\Entities\PaymentsEntity::class);
        }
        break;
    case 'currency':
        if ($managers->access('currency', $manager)) {
            $entity = $entityFactory->get(\Okay\Entities\CurrenciesEntity::class);
            if (!empty($values['cents'])) {
                $values['cents'] = 2;
            }
        }
        break;
    case 'comment':
        if ($managers->access('comments', $manager)) {
            $entity = $entityFactory->get(\Okay\Entities\CommentsEntity::class);
        }
        break;
    case 'user':
        if ($managers->access('users', $manager)) {
            $entity = $entityFactory->get(\Okay\Entities\UsersEntity::class);
        }
        break;
    case 'label':
        if ($managers->access('labels', $manager)) {
            $entity = $entityFactory->get(\Okay\Entities\OrderLabelsEntity::class);
        }
        break;
    case 'language':
        if ($managers->access('languages', $manager)) {
            $entity = $entityFactory->get(\Okay\Entities\LanguagesEntity::class);
        }
        break;
    case 'banner':
        if ($managers->access('banners', $manager)) {
            $entity = $entityFactory->get(\Okay\Entities\BannersEntity::class);
        }
        break;
    case 'banners_image':
        if ($managers->access('banners', $manager)) {
            $entity = $entityFactory->get(\Okay\Entities\BannersImagesEntity::class);
        }
        break;
    case 'callback':
        if ($managers->access('callbacks', $manager)) {
            $entity = $entityFactory->get(\Okay\Entities\CallbacksEntity::class);
        }
        break;
    case 'feedback':
        if ($managers->access('feedbacks', $manager)) {
            $entity = $entityFactory->get(\Okay\Entities\FeedbacksEntity::class);
        }
        break;
    case 'module':
        if ($managers->access('modules', $manager)) {
            /** @var Design $design */
            $design = $DI->get(Design::class);
            $design->clearCompiled();
            $entity = $entityFactory->get(\Okay\Entities\ModulesEntity::class);
        }
        break;
    case 'managers':
        if ($managerMenu = $request->post('manager_menu')) {
            $entity = $entityFactory->get(\Okay\Entities\ManagersEntity::class);
            $values = ['menu'=>$managerMenu];
        } elseif ($managers->access('managers', $manager)) {
            $entity = $entityFactory->get(\Okay\Entities\ManagersEntity::class);
            $result = $entity->update($id, $values);
        } elseif (isset($values['menu_status'])) {
            $entity = $entityFactory->get(\Okay\Entities\ManagersEntity::class);
            $result = $entity->update($id, ['menu_status'=>$values['menu_status']]);
        }
        break;
}

if (empty($entity)) {
    /** @var Okay\Core\Modules\UpdateObject $updateObject */
    $updateObject = $DI->get(\Okay\Core\Modules\UpdateObject::class);
    $moduleEntityObject = $updateObject->getByAlias($object);

    if (!empty($moduleEntityObject) && $managers->access($moduleEntityObject->permission, $manager)) {
        $entity = $entityFactory->get($moduleEntityObject->entityName);
    }
}

if (!empty($entity)) {
    $result = $entity->update($id, $values);
}

$response->setContent(json_encode($result), RESPONSE_JSON);
$response->sendContent();
