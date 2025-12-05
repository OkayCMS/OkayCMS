<?php
/* Smarty version 3.1.40, created on 2025-11-26 11:33:11
  from '/var/www/html/backend/design/html/learning_hints.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.40',
  'unifunc' => 'content_6926c95710ea42_95542969',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2a5f03746f3247526a9583d40048840cd3210e41' => 
    array (
      0 => '/var/www/html/backend/design/html/learning_hints.tpl',
      1 => 1764098168,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6926c95710ea42_95542969 (Smarty_Internal_Template $_smarty_tpl) {
echo '<script'; ?>
>
    <?php if ($_smarty_tpl->tpl_vars['hintId']->value == "hint_categories") {?>
        function learningCategories(){
            var intro = introJs();
            intro.setOptions({
                exitOnOverlayClick: false,
                showStepNumbers: false,
                showBullets: false,
                showProgress: true,
                prevLabel: '« <?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_prev;?>
',
                nextLabel: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_next;?>
 »',
                skipLabel: '×',
                doneLabel: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_next_done;?>
',
                steps: [
                    {
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_categories_intro;?>
',
                    },
                    {
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_categories_intro2;?>
',
                        position: 'left',
                    },
                    {
                        element: '.fn_step-1:first-child',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_categories_item;?>
',
                        position: 'top',
                    },
                    {
                        element: '.fn_step-1:first-child .okay_list_subicon',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_categories_subicon;?>
',
                        position: 'top',
                    },
                    {
                        element: '.fn_step-1:first-child .move_zone',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_categories_move_zone;?>
',
                        position: 'top',
                    },
                    {
                        element: '.fn_step-1:first-child .okay_list_check',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_categories_check;?>
',
                        position: 'top',
                    },
                    {
                        element: '.fn_step-1:first-child .okay_list_photo',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_categories_photo;?>
',
                        position: 'top',
                    },
                    {
                        element: '.fn_step-1:first-child .okay_list_categories_name',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_categories_name;?>
',
                        position: 'top',
                    },
                    {
                        element: '.fn_step-1:first-child .okay_list_status',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_categories_status;?>
',
                        position: 'left',
                    },
                    {
                        element: '.fn_step-1:first-child .okay_list_setting a',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_categories_setting;?>
',
                        position: 'left',
                    },
                    {
                        element: '.fn_step-1:first-child .okay_list_setting button',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_categories_setting_duplicate;?>
',
                        position: 'left',
                    },
                    {
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_categories_add;?>
',

                    }

                ]
            });
            intro.start();
            intro.oncomplete(function() {
                window.location.href = 'index.php?controller=CategoryAdmin&multipage=&id=1';
            });
        }
        if (RegExp('multipage', 'gi').test(window.location.search)) {
            learningCategories();
        }
    <?php }?>

    <?php if ($_smarty_tpl->tpl_vars['hintId']->value == "hint_category") {?>
        function learningCategory(){
            var intro = introJs();
            intro.setOptions({
            exitOnOverlayClick: false,
            showStepNumbers: false,
            showBullets: false,
            showProgress: true,
            prevLabel: '« <?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_prev;?>
',
            nextLabel: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_next;?>
 »',
            skipLabel: '×',
            doneLabel: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_done;?>
',
            steps: [
            {
            intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_category_intro;?>
',
            },
            {
            element: '.fn_step-1',
            intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_category_name;?>
',
            position: 'bottom',
            },
            {
            element: '.fn_step-2',
            intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_category_url;?>
',
            position: 'bottom',
            },
            {
            element: '.fn_step-3',
            intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_category_activity;?>
',
            position: 'left',
            },
            {
            element: '.fn_step-4',
            intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_category_images;?>
',
            },
            {
            element: '.fn_step-5',
            intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_category_h1;?>
',
            },
            {
            element: '.fn_step-6',
            intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_category_tree;?>
',
            position: 'left',
            },
            {
            element: '.fn_step-7',
            intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_meta_data;?>
',
            },
            {
            element: '.fn_step-8',
            intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_category_descriptions;?>
',
            },
            {
            intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_category_finish;?>
',
            position: 'bottom',
            }
            ]
            });
            intro.start();
            intro.oncomplete(function() {
                $.ajax({
                    url: 'ajax/lesson_done.php',
                    data: {
                        lesson: 1
                    }
                })
                .done(function() {
                    window.location.href = 'index.php?controller=LearningAdmin';
                });
            });
        }
        if (RegExp('multipage', 'gi').test(window.location.search)) {
            learningCategory();
        }
    <?php }?>

    <?php if ($_smarty_tpl->tpl_vars['hintId']->value == "hint_brands") {?>
        function learningBrands(){
            var intro = introJs();
            intro.setOptions({
                exitOnOverlayClick: false,
                showStepNumbers: false,
                showBullets: false,
                showProgress: true,
                prevLabel: '« <?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_prev;?>
',
                nextLabel: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_next;?>
 »',
                skipLabel: '×',
                doneLabel: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_next_done;?>
',
                steps: [
                    {
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_brands_intro;?>
',
                    },
                    {
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_brands_intro2;?>
',
                        position: 'left',
                    },
                    {
                        element: '.fn_step-1:first-child',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_brands_item;?>
',
                        position: 'top',
                    },
                    {
                        element: '.fn_step-1:first-child .move_zone',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_brands_move_zone;?>
',
                        position: 'top',
                    },
                    {
                        element: '.fn_step-1:first-child .okay_list_check',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_brands_check;?>
',
                        position: 'top',
                    },
                    {
                        element: '.fn_step-1:first-child .okay_list_photo',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_brands_photo;?>
',
                        position: 'top',
                    },
                    {
                        element: '.fn_step-1:first-child .okay_list_brands_name',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_brands_name;?>
',
                        position: 'top',
                    },
                    {
                        element: '.fn_step-1:first-child .okay_list_status',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_brands_status;?>
',
                        position: 'left',
                    },
                    {
                        element: '.fn_step-1:first-child .okay_list_setting',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_brands_setting;?>
',
                        position: 'left',
                    },
                    {
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_brands_add;?>
',

                    }

                ]
            });
            intro.start();
            intro.oncomplete(function() {
                window.location.href = 'index.php?controller=BrandAdmin&multipage=&id=1';
            });
        }
        if (RegExp('multipage', 'gi').test(window.location.search)) {
            learningBrands();
        }
    <?php }?>

    <?php if ($_smarty_tpl->tpl_vars['hintId']->value == "hint_brand") {?>
        function learningBrand(){
            var intro = introJs();
            intro.setOptions({
                exitOnOverlayClick: false,
                showStepNumbers: false,
                showBullets: false,
                showProgress: true,
                prevLabel: '« <?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_prev;?>
',
                nextLabel: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_next;?>
 »',
                skipLabel: '×',
                doneLabel: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_done;?>
',
                steps: [
                    {
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_brand_intro;?>
',
                    },
                    {
                        element: '.fn_step-1',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_brand_name;?>
',
                        position: 'bottom',
                    },
                    {
                        element: '.fn_step-2',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_brand_url;?>
',
                        position: 'bottom',
                    },
                    {
                        element: '.fn_step-3',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_brand_activity;?>
',
                        position: 'left',
                    },
                    {
                        element: '.fn_step-4',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_brand_images;?>
',
                    },
                    {
                        element: '.fn_step-5',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_meta_data;?>
',
                    },
                    {
                        element: '.fn_step-6',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_brand_descriptions;?>
',
                    },
                    {
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_brand_finish;?>
',
                        position: 'bottom',
                    }
                ]
            });

            intro.start();
            intro.oncomplete(function() {
                $.ajax({
                    url: 'ajax/lesson_done.php',
                    data: {
                        lesson: 2
                    }
                })
                    .done(function() {
                        window.location.href = 'index.php?controller=LearningAdmin';
                    });
            });
        }
        if (RegExp('multipage', 'gi').test(window.location.search)) {
            learningBrand();
        }
    <?php }?>

    <?php if ($_smarty_tpl->tpl_vars['hintId']->value == "hint_features") {?>
        function learningFeatures(){
            var intro = introJs();
            intro.setOptions({
                exitOnOverlayClick: false,
                showStepNumbers: false,
                showBullets: false,
                showProgress: true,
                prevLabel: '« <?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_prev;?>
',
                nextLabel: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_next;?>
 »',
                skipLabel: '×',
                doneLabel: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_next_done;?>
',
                steps: [
                    {
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_features_intro;?>
',
                    },
                    {
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_features_intro2;?>
',
                        position: 'left',
                    },
                    {
                        element: '.fn_step-1',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_features_sorting;?>
',
                        position: 'top',
                    },
                    {
                        element: '.fn_step-2:first-child',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_features_item;?>
',
                        position: 'top',
                    },
                    {
                        element: '.fn_step-2:first-child .move_zone',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_features_move_zone;?>
',
                        position: 'top',
                    },
                    {
                        element: '.fn_step-2:first-child .okay_list_check',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_features_check;?>
',
                        position: 'top',
                    },
                    {
                        element: '.fn_step-2:first-child .okay_list_features_name',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_features_name;?>
',
                        position: 'top',
                    },
                    {
                        element: '.fn_step-2:first-child .okay_list_features_tag',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_features_category;?>
',
                        position: 'top',
                    },
                    {
                        element: '.fn_step-2:first-child .okay_list_status',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_features_setting;?>
',
                        position: 'left',
                    },
                    {
                        element: '.fn_step-2:first-child .okay_list_url_status',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_features_status;?>
',
                        position: 'left',
                    },
                    {
                        element: '.fn_step-2:first-child .okay_list_boding:nth-last-child(2)',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_features_status_2;?>
',
                        position: 'left',
                    },
                    {
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_features_add;?>
',

                    }

                ]
            });
            intro.start();
            intro.oncomplete(function() {
                window.location.href = 'index.php?controller=FeatureAdmin&multipage=&id=1';
            });
        }
        if (RegExp('multipage', 'gi').test(window.location.search)) {
            learningFeatures();
        }
    <?php }?>

    <?php if ($_smarty_tpl->tpl_vars['hintId']->value == "hint_feature") {?>
    function learningFeature(){
        var intro = introJs();
        intro.setOptions({
            exitOnOverlayClick: false,
            showStepNumbers: false,
            showBullets: false,
            showProgress: true,
            prevLabel: '« <?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_prev;?>
',
            nextLabel: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_next;?>
 »',
            skipLabel: '×',
            doneLabel: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_done;?>
',
            steps: [
                {
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_feature_intro;?>
',
                },
                {
                    element: '.fn_step-1',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_feature_name;?>
',
                    position: 'bottom',
                },
                {
                    element: '.fn_step-2',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_feature_url;?>
',
                    position: 'bottom',
                },
                {
                    element: '.fn_step-3',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_feature_link;?>
',
                },
                {
                    element: '.fn_step-4',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_feature_activity;?>
',
                    position: 'left',
                },
                {
                    element: '.fn_step-5',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_feature_filter;?>
',
                    position: 'right',
                },
                {
                    element: '.fn_step-6',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_features_meta_data;?>
',
                    position: 'left',
                },
                {
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_feature_list;?>
',
                },
                {
                    element: '.fn_step-7',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_feature_list_add;?>
',
                },
                {
                    element: '.fn_step-8',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_feature_list_sorting;?>
',
                },
                {
                    element: '.fn_step-9:first-child',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_feature_item;?>
',
                },
                {
                    element: '.fn_step-9:first-child .move_zone',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_feature_move_zone;?>
',
                    position: 'top',
                },
                {
                    element: '.fn_step-9:first-child .feature_value_name',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_feature_list_name;?>
',
                    position: 'top',
                },
                {
                    element: '.fn_step-9:first-child .feature_value_translit',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_feature_value_translit;?>
',
                    position: 'top',
                },
                {
                    element: '.fn_step-9:first-child .feature_value_products_num',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_feature_value_products_num;?>
',
                    position: 'top',
                },
                {
                    element: '.fn_step-9:first-child .feature_value_index',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_feature_value_index;?>
',
                    position: 'top',
                },
                {
                    element: '.fn_step-10',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_feature_union;?>
',
                },
                {
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_feature_finish;?>
',
                    position: 'bottom',
                }
            ]
        });

        intro.start();
        intro.oncomplete(function() {
            $.ajax({
                url: 'ajax/lesson_done.php',
                data: {
                    lesson: 3
                }
            })
                .done(function() {
                    window.location.href = 'index.php?controller=LearningAdmin';
                });
        });
    }
    if (RegExp('multipage', 'gi').test(window.location.search)) {
        learningFeature();
    }
    <?php }?>

    <?php if ($_smarty_tpl->tpl_vars['hintId']->value == "hint_products") {?>
        function learningProducts(){
            var intro = introJs();
            intro.setOptions({
                exitOnOverlayClick: false,
                showStepNumbers: false,
                showBullets: false,
                showProgress: true,
                prevLabel: '« <?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_prev;?>
',
                nextLabel: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_next;?>
 »',
                skipLabel: '×',
                doneLabel: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_next_done;?>
',
                steps: [
                    {
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_products_intro;?>
',
                    },
                    {
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_products_intro2;?>
',
                        position: 'left',
                    },
                    {
                        element: '.fn_step-0',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_products_sorting;?>
',
                        position: 'top',
                    },
                    {
                        element: '.fn_step_sorting',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_products_sorting2;?>
',
                        position: 'bottom',
                    },
                    {
                        element: '.fn_step-1:first-child',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_products_item;?>
',
                        position: 'top',
                    },
                    {
                        element: '.fn_step-1:first-child .move_zone',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_products_move_zone;?>
',
                        position: 'top',
                    },
                    {
                        element: '.fn_step-1:first-child .okay_list_check',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_products_check;?>
',
                        position: 'top',
                    },
                    {
                        element: '.fn_step-1:first-child .okay_list_photo',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_products_photo;?>
',
                        position: 'top',
                    },
                    {
                        element: '.fn_step-1:first-child .okay_list_name',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_products_name;?>
',
                        position: 'top',
                    },
                    {
                        element: '.fn_step-1:first-child .okay_list_price',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_products_list_price;?>
',
                        position: 'top',
                    },
                    {
                        element: '.fn_step-1:first-child .okay_list_count',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_products_list_count;?>
',
                        position: 'top',
                    },
                    {
                        element: '.fn_step-1:first-child .okay_list_status',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_products_status;?>
',
                        position: 'left',
                    },
                    {
                        element: '.fn_step-1:first-child .okay_list_products_setting',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_products_setting;?>
',
                        position: 'left',
                    },
                    {
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_products_add;?>
',

                    }

                ]
            });
            intro.start();
            intro.oncomplete(function() {
                window.location.href = 'index.php?controller=ProductAdmin&multipage=&id=1';
            });
        }
        if (RegExp('multipage', 'gi').test(window.location.search)) {
            learningProducts();
        }
    <?php }?>

<?php if ($_smarty_tpl->tpl_vars['hintId']->value == "hint_product") {?>
    function learningProduct(){
        var intro = introJs();
        intro.setOptions({
        exitOnOverlayClick: false,
        showStepNumbers: false,
        showBullets: false,
        showProgress: true,
        prevLabel: '« <?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_prev;?>
',
        nextLabel: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_next;?>
 »',
        skipLabel: '×',
        doneLabel: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_done;?>
',
        steps: [
            {
                intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_product_intro;?>
',
            },
            {
                element: '.fn_step-1',
                intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_product_name;?>
',
                position: 'bottom',
            },
            {
                element: '.fn_step-2',
                intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_product_url;?>
',
                position: 'bottom',
            },
            {
                element: '.fn_step-3',
                intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_product_activity;?>
',
                position: 'left',
            },
            {
                element: '.fn_step-4',
                intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_product_hits;?>
',
                position: 'left',
            },
            {
                element: '.fn_step-5',
                intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_product_images;?>
',
            },
            {
                element: '.fn_step-6',
                intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_product_brand;?>
',
                position: 'left',
            },
            {
                element: '.fn_step-7',
                intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_product_category;?>
',
                position: 'left',
            },
            {
                element: '.fn_step-8',
                intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_product_variant;?>
',
                position: 'bottom',
            },
            {
                element: '.fn_step-9',
                intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_product_promo;?>
',
                position: 'top',
            },
            {
                element: '.fn_step-10',
                intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_product_rating;?>
',
                position: 'left',
            },
            {
                element: '.fn_step-11',
                intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_product_features;?>
',
                position: 'right',
            },
            {
                element: '.fn_step-12',
                intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_product_recommended;?>
',
                position: 'left',
            },
            {
                element: '.fn_step-13',
                intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_meta_data;?>
',
            },
            {
                element: '.fn_step-14',
                intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_product_descriptions;?>
',
            },
            {
                intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_product_finish;?>
',
                position: 'bottom',
            }
        ]
        });

        intro.start();
        intro.oncomplete(function() {
            $.ajax({
                url: 'ajax/lesson_done.php',
                data: {
                    lesson: 4
                }
            })
                .done(function() {
                    window.location.href = 'index.php?controller=LearningAdmin';
                });
        });
    }
    if (RegExp('multipage', 'gi').test(window.location.search)) {
        learningProduct();
    }
<?php }?>

<?php if ($_smarty_tpl->tpl_vars['hintId']->value == "hint_currency") {?>
    function learningCurrency(){
        var intro = introJs();
        intro.setOptions({
            exitOnOverlayClick: false,
            showStepNumbers: false,
            showBullets: false,
            showProgress: true,
            prevLabel: '« <?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_prev;?>
',
            nextLabel: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_next;?>
 »',
            skipLabel: '×',
            doneLabel: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_done;?>
',
            steps: [
                {
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_currency_intro;?>
',
                },
                {
                    element: '.fn_step-1',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_currency_start;?>
',
                    position: 'top',
                },
                {
                    element: '.fn_step-2:first-child',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_currency_intro2;?>
',
                },
                {
                    element: '.fn_step-2:first-child .okay_list_currency_name',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_currency_name;?>
',
                },
                {
                    element: '.fn_step-2:first-child .okay_list_currency_iso',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_currency_sign;?>
',
                    position: 'bottom',
                },
                {
                    element: '.fn_step-2:first-child .okay_list_currency_sign',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_currency_iso;?>
',
                    position: 'bottom',
                },
                {
                    element: '.fn_step-2:first-child .okay_list_currency_exchange .okay_list_currency_exchange_item',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_currency_course;?>
',
                },
                {
                    element: '.fn_step-2:nth-child(2) .okay_list_currency_exchange .okay_list_currency_exchange_item',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_currency_course2;?>
',
                },
                {
                    element: '.fn_step-2:nth-child(1) .okay_list_status',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_currency_activity;?>
',
                },
                {
                    element: '.fn_step-2:nth-child(1) .cur_settings',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_currency_cent;?>
',
                    position: 'left',
                },
                {
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_currency_finish;?>
',
                    position: 'center',
                }
            ]
        });

        intro.start();
        intro.oncomplete(function() {
            $.ajax({
                url: 'ajax/lesson_done.php',
                data: {
                    lesson: 9
                }
            })
                .done(function() {
                    window.location.href = 'index.php?controller=LearningAdmin';
                });
        });
    }
    if (RegExp('multipage', 'gi').test(window.location.search)) {
        learningCurrency();
    }
<?php }?>

    <?php if ($_smarty_tpl->tpl_vars['hintId']->value == "hint_deliveries") {?>
    function learningDeliveries(){
        var intro = introJs();
        intro.setOptions({
            exitOnOverlayClick: false,
            showStepNumbers: false,
            showBullets: false,
            showProgress: true,
            prevLabel: '« <?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_prev;?>
',
            nextLabel: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_next;?>
 »',
            skipLabel: '×',
            doneLabel: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_next_done;?>
',
            steps: [
                {
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_deliveries_intro;?>
',
                },
                {
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_deliveries_intro2;?>
',
                    position: 'left',
                },
                {
                    element: '.fn_step-1:first-child',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_deliveries_item;?>
',
                    position: 'top',
                },
                {
                    element: '.fn_step-1:first-child .move_zone',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_deliveries_move_zone;?>
',
                    position: 'top',
                },
                {
                    element: '.fn_step-1:first-child .okay_list_check',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_deliveries_check;?>
',
                    position: 'top',
                },
                {
                    element: '.fn_step-1:first-child .okay_list_delivery_photo',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_deliveries_photo;?>
',
                    position: 'top',
                },
                {
                    element: '.fn_step-1:first-child .okay_list_delivery_name',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_deliveries_name;?>
',
                    position: 'top',
                },
                {
                    element: '.fn_step-1:first-child .okay_list_delivery_condit',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_deliveries_terms;?>
',
                    position: 'left',
                },
                {
                    element: '.fn_step-1:first-child .okay_list_status',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_deliveries_status;?>
',
                    position: 'left',
                },
                {
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_deliveries_add;?>
',

                }

            ]
        });
        intro.start();
        intro.oncomplete(function() {
            window.location.href = 'index.php?controller=DeliveryAdmin&multipage=&id=1';
        });
    }
    if (RegExp('multipage', 'gi').test(window.location.search)) {
        learningDeliveries();
    }
    <?php }?>

<?php if ($_smarty_tpl->tpl_vars['hintId']->value == "hint_delivery") {?>
    function learningDelivery(){
        var intro = introJs();

        intro.setOptions({
            exitOnOverlayClick: false,
            showStepNumbers: false,
            showBullets: false,
            showProgress: true,
            prevLabel: '« <?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_prev;?>
',
            nextLabel: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_next;?>
 »',
            skipLabel: '×',
            doneLabel: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_done;?>
',
            steps: [
                {
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_delivery_intro;?>
',
                },
                {
                    element: '.fn_step-1',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_delivery_name;?>
',
                    position: 'bottom',
                },
                {
                    element: '.fn_step-2',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_delivery_activity;?>
',
                    position: 'left',
                },
                {
                    element: '.fn_step-3',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_delivery_images;?>
',
                },
                {
                    element: '.fn_step-4',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_delivery_type;?>
',
                },
                {
                    element: '.fn_step-5',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_delivery_module;?>
',
                },
                {
                    element: '.fn_step-6',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_delivery_payments;?>
',
                },
                {
                    element: '.fn_step-7',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_delivery_descriptions;?>
',
                },
                {
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_delivery_finish;?>
',
                    position: 'bottom',
                }
            ]
        });

        intro.start();
        intro.oncomplete(function() {
            $.ajax({
                url: 'ajax/lesson_done.php',
                data: {
                    lesson: 10
                }
            })
                .done(function() {
                    window.location.href = 'index.php?controller=LearningAdmin';
                });
        });
    }
    if (RegExp('multipage', 'gi').test(window.location.search)) {
        learningDelivery();
    }
<?php }?>

    <?php if ($_smarty_tpl->tpl_vars['hintId']->value == "hint_payments") {?>
    function learningPayments(){
        var intro = introJs();
        intro.setOptions({
            exitOnOverlayClick: false,
            showStepNumbers: false,
            showBullets: false,
            showProgress: true,
            prevLabel: '« <?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_prev;?>
',
            nextLabel: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_next;?>
 »',
            skipLabel: '×',
            doneLabel: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_next_done;?>
',
            steps: [
                {
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_payments_intro;?>
',
                },
                {
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_payments_intro2;?>
',
                    position: 'left',
                },
                {
                    element: '.fn_step-1:first-child',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_payments_item;?>
',
                    position: 'top',
                },
                {
                    element: '.fn_step-1:first-child .move_zone',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_payments_move_zone;?>
',
                    position: 'top',
                },
                {
                    element: '.fn_step-1:first-child .okay_list_check',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_payments_check;?>
',
                    position: 'top',
                },
                {
                    element: '.fn_step-1:first-child .okay_list_delivery_photo',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_payments_photo;?>
',
                    position: 'top',
                },
                {
                    element: '.fn_step-1:first-child .okay_list_delivery_name',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_payments_name;?>
',
                    position: 'top',
                },
                {
                    element: '.fn_step-1:first-child .okay_list_status',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_payments_status;?>
',
                    position: 'left',
                },
                {
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_payments_add;?>
',

                }

            ]
        });
        intro.start();
        intro.oncomplete(function() {
            window.location.href = 'index.php?controller=PaymentMethodAdmin&multipage=&id=1';
        });
    }
    if (RegExp('multipage', 'gi').test(window.location.search)) {
        learningPayments();
    }
    <?php }?>

<?php if ($_smarty_tpl->tpl_vars['hintId']->value == "hint_payment") {?>
    function learningPayment(){
        var intro = introJs();

        intro.setOptions({
            exitOnOverlayClick: false,
            showStepNumbers: false,
            showBullets: false,
            showProgress: true,
            prevLabel: '« <?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_prev;?>
',
            nextLabel: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_next;?>
 »',
            skipLabel: '×',
            doneLabel: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_done;?>
',
            steps: [
                {
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_payment_intro;?>
',
                },
                {
                    element: '.fn_step-1',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_payment_name;?>
',
                    position: 'bottom',
                },
                {
                    element: '.fn_step-2',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_payment_activity;?>
',
                    position: 'left',
                },
                {
                    element: '.fn_step-3',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_payment_images;?>
',
                },
                {
                    element: '.fn_step-4',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_payment_type;?>
',
                    position: 'left',
                },
                {
                    element: '.fn_step-6',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_payment_delivery;?>
',
                },
                {
                    element: '.fn_step-7',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_payment_descriptions;?>
',
                },
                {
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_payment_finish;?>
',
                    position: 'bottom',
                }
            ]
        });

        intro.start();
        intro.oncomplete(function() {
            $.ajax({
                url: 'ajax/lesson_done.php',
                data: {
                    lesson: 11
                }
            })
                .done(function() {
                    window.location.href = 'index.php?controller=LearningAdmin';
                });
        });
    }
    if (RegExp('multipage', 'gi').test(window.location.search)) {
        learningPayment();
    }
<?php }?>

<?php if ($_smarty_tpl->tpl_vars['hintId']->value == "hint_settings_catalog") {?>
    function learningSettingsCatalog(){
        var intro = introJs();

        intro.setOptions({
            exitOnOverlayClick: false,
            showStepNumbers: false,
            showBullets: false,
            showProgress: true,
            prevLabel: '« <?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_prev;?>
',
            nextLabel: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_next;?>
 »',
            skipLabel: '×',
            doneLabel: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_done;?>
',
            steps: [
                {
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_settings_catalog_intro;?>
',
                },
                {
                    element: '.fn_step-1',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_settings_catalog__products_on_page;?>
',
                    position: 'bottom',
                },
                {
                    element: '.fn_step-2',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_settings_catalog_products_max;?>
',
                    position: 'left',
                },
                {
                    element: '.fn_step-3',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_settings_catalog_posts_on_page;?>
',
                },
                {
                    element: '.fn_step-4',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_settings_catalog_products_comparcion;?>
',
                },
                {
                    element: '.fn_step-5',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_settings_catalog_units;?>
',
                },
                {
                    element: '.fn_step-6',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_settings_catalog_cents;?>
',
                    position: 'left',
                },
                {
                    element: '.fn_step-7',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_settings_catalog_thousands;?>
',
                    position: 'right',
                },
                {
                    element: '.fn_step-8',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_settings_catalog_not_in_stock;?>
',
                    position: 'left',
                },
                {
                    element: '.fn_step-9',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_settings_catalog_preorder_not_in_stock;?>
',
                },
                {
                    element: '.fn_step-10',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_settings_catalog_truncate_table;?>
',
                },
                {
                    element: '.fn_step-11',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_settings_catalog_watermark;?>
',
                },
                {
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_settings_catalog_finish;?>
',
                    position: 'bottom',
                }
            ]
        });

        intro.start();
        intro.oncomplete(function() {
            $.ajax({
                url: 'ajax/lesson_done.php',
                data: {
                    lesson: 8
                }
            })
                .done(function() {
                    window.location.href = 'index.php?controller=LearningAdmin';
                });
        });
    }
    if (RegExp('multipage', 'gi').test(window.location.search)) {
        learningSettingsCatalog();
    }
<?php }?>

<?php if ($_smarty_tpl->tpl_vars['hintId']->value == "hint_settings_notify") {?>
    function learningSettingsNotify(){
        var intro = introJs();

        intro.setOptions({
            exitOnOverlayClick: false,
            showStepNumbers: false,
            showBullets: false,
            showProgress: true,
            prevLabel: '« <?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_prev;?>
',
            nextLabel: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_next;?>
 »',
            skipLabel: '×',
            doneLabel: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_done;?>
',
            steps: [
                {
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_settings_notify_intro;?>
',
                },
                {
                    element: '.fn_step-1',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_settings_notify_emails;?>
',
                    position: 'right',
                },
                {
                    element: '.fn_step-2',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_settings_notify_reverce;?>
',
                    position: 'left',
                },
                {
                    element: '.fn_step-3',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_settings_notify_comments;?>
',
                    position: 'right',
                },
                {
                    element: '.fn_step-4',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_settings_notify_sender_name;?>
',
                    position: 'left',
                },
                {
                    element: '.fn_step-5',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_settings_notify_email_lang;?>
',
                    position: 'right',
                },
                {
                    element: '.fn_step-6',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_settings_notify_auto_approved;?>
',
                    position: 'left',
                },
                {
                    element: '.fn_step-7',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_settings_notify_smtp;?>
',
                    position: 'top',
                },
                {
                    element: '.fn_step-8',
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_settings_notify_smtp2;?>
',
                    position: 'top',
                },
                {
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_settings_notify_finish;?>
',
                    position: 'center',
                }
            ]
        });

        intro.start();
        intro.oncomplete(function() {
            $.ajax({
                url: 'ajax/lesson_done.php',
                data: {
                    lesson: 7
                }
            })
                .done(function() {
                    window.location.href = 'index.php?controller=LearningAdmin';
                });
        });
    }
    if (RegExp('multipage', 'gi').test(window.location.search)) {
        learningSettingsNotify();
    }
<?php }?>


    <?php if ($_smarty_tpl->tpl_vars['hintId']->value == "hint_seo_patterns") {?>
    function learningSeoPatterns(){
        var intro = introJs();

        intro.setOptions({
            exitOnOverlayClick: false,
            showStepNumbers: false,
            showBullets: false,
            showProgress: true,
            prevLabel: '« <?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_prev;?>
',
            nextLabel: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_next;?>
 »',
            skipLabel: '×',
            doneLabel: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_done;?>
',
            steps: [
                {
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_seo_patterns_intro;?>
',
                },
                {
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_seo_patterns_intro2;?>
',
                },
                {
                    element: document.querySelector('.fn_step-1'),
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_seo_patterns_list;?>
',
                    position: 'right',
                },
                {
                    element: document.querySelector('.fn_step-2'),
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_seo_patterns_right;?>
',
                    position: 'left',
                },
                {
                    element: document.querySelector('.fn_step-2'),
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_seo_patterns_right2;?>
',
                    position: 'left',
                },
                {
                    intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_seo_patterns_finish;?>
',
                    position: 'center',
                }
            ]
        });

        intro.start();
        intro.oncomplete(function() {
            $.ajax({
                url: 'ajax/lesson_done.php',
                data: {
                    lesson: 12
                }
            })
                .done(function() {
                    window.location.href = 'index.php?controller=LearningAdmin';
                });
        });
    }
    if (RegExp('multipage', 'gi').test(window.location.search)) {
        learningSeoPatterns();
    }
    <?php }?>


    <?php if ($_smarty_tpl->tpl_vars['hintId']->value == "hint_orders") {?>
        function learningOrders(){
            var intro = introJs();

            intro.setOptions({
                exitOnOverlayClick: false,
                showStepNumbers: false,
                showBullets: false,
                showProgress: true,
                prevLabel: '« <?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_prev;?>
',
                nextLabel: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_next;?>
 »',
                skipLabel: '×',
                doneLabel: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_next_done;?>
',
                steps: [
                    {
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_orders_intro;?>
',
                    },
                    {
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_orders_intro2;?>
',
                        position: 'left',
                    },
                    {
                        element: '.fn_step-0',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_orders_sorting;?>
',
                        position: 'top',
                    },
                    {
                        element: '.fn_step-1:first-child',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_orders_item;?>
',
                        position: 'top',
                    },
                    {
                        element: '.fn_step-1:first-child .okay_list_check',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_orders_check;?>
',
                        position: 'top',
                    },
                    {
                        element: '.fn_step-1:first-child .okay_list_order_number',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_orders_id;?>
',
                        position: 'top',
                    },
                    {
                        element: '.fn_step-1:first-child .okay_list_orders_name',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_orders_name;?>
',
                        position: 'top',
                    },
                    {
                        element: '.fn_step-1:first-child .okay_list_order_status',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_orders_status;?>
',
                        position: 'top',
                    },
                    {
                        element: '.fn_step-1:first-child .okay_list_order_product_count',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_orders_count;?>
',
                        position: 'top',
                    },
                    {
                        element: '.fn_step-1:first-child .okay_list_orders_price',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_orders_total;?>
',
                        position: 'left',
                    },
                    {
                        element: '.fn_step-1:first-child .okay_list_order_marker',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_orders_marker;?>
',
                        position: 'left',
                    },
                    {
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_orders_add;?>
',
                    }

                ]
            });

            intro.start();
            intro.oncomplete(function() {
                window.location.href = 'index.php?controller=OrderAdmin&multipage=&id=1';
            });
        }
        if (RegExp('multipage', 'gi').test(window.location.search)) {
            learningOrders();
        }
    <?php }?>

    <?php if ($_smarty_tpl->tpl_vars['hintId']->value == "hint_order") {?>
        function learningOrder(){
            var intro = introJs();

            intro.setOptions({
                exitOnOverlayClick: false,
                showStepNumbers: false,
                showBullets: false,
                showProgress: true,
                prevLabel: '« <?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_prev;?>
',
                nextLabel: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_next;?>
 »',
                skipLabel: '×',
                doneLabel: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_done;?>
',
                steps: [
                    {
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_order_intro;?>
',
                    },
                    {
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_order_intro2;?>
',
                    },
                    {
                        element: '.fn_step-1',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_order_toolbar;?>
',
                        position: 'bottom',
                    },
                    {
                        element: '.fn_step-1 .order_toolbar__status',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_order_toolbar__status;?>
',
                        position: 'right',
                    },
                    {
                        element: '.fn_step-1 .order_toolbar__eye',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_order_toolbar__button;?>
',
                        position: 'bottom',
                    },
                    {
                        element: '.fn_step-1 .order_toolbar__print',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_order_toolbar__print;?>
',
                        position: 'right',
                    },
                    {
                        element: '.fn_step-1 .order_toolbar__markers',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_order_toolbar__markers;?>
',
                        position: 'left',
                    },
                    {
                        element: '.fn_step-2',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_order_left_content;?>
',
                        position: 'right',
                    },
                    {
                        element: '.fn_step-3',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_order_parameters;?>
',
                        position: 'right',
                    },
                    {
                        element: '.fn_step-4',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_order_right_content;?>
',
                        position: 'left',
                    },
                    {
                        element: '.fn_step-5',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_order_to_email;?>
',
                        position: 'left',
                    },
                    {
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_order_finish;?>
',
                        position: 'center',
                    }
                ]
            });

            intro.start();
            intro.oncomplete(function() {
                $.ajax({
                    url: 'ajax/lesson_done.php',
                    data: {
                        lesson: 5
                    }
                })
                    .done(function() {
                        window.location.href = 'index.php?controller=LearningAdmin';
                    });
            });
        }
        if (RegExp('multipage', 'gi').test(window.location.search)) {
            learningOrder();
        }
    <?php }?>

    <?php if ($_smarty_tpl->tpl_vars['hintId']->value == "hint_blog") {?>
        function learningBlog(){
            var intro = introJs();
            intro.setOptions({
                exitOnOverlayClick: false,
                showStepNumbers: false,
                showBullets: false,
                showProgress: true,
                prevLabel: '« <?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_prev;?>
',
                nextLabel: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_next;?>
 »',
                skipLabel: '×',
                doneLabel: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_next_done;?>
',
                steps: [
                    {
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_blog_intro;?>
',
                    },
                    {
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_blog_intro2;?>
',
                        position: 'left',
                    },
                    {
                        element: '.fn_step-0',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_blog_sorting;?>
',
                        position: 'right',
                    },
                    {
                        element: '.fn_step-1:first-child',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_blog_item;?>
',
                        position: 'top',
                    },
                    {
                        element: '.fn_step-1:first-child .okay_list_check',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_blog_check;?>
',
                        position: 'top',
                    },
                    {
                        element: '.fn_step-1:first-child .okay_list_photo',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_blog_photo;?>
',
                        position: 'top',
                    },
                    {
                        element: '.fn_step-1:first-child .okay_list_blog_name',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_blog_name;?>
',
                        position: 'top',
                    },
                    {
                        element: '.fn_step-1:first-child .okay_list_status',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_blog_status;?>
',
                        position: 'left',
                    },
                    {
                        element: '.fn_step-1:first-child .okay_list_blog_setting',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_blog_setting;?>
',
                        position: 'left',
                    },
                    {
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_blog_add;?>
',
                    }

                ]
            });
            intro.start();
            intro.oncomplete(function() {
                window.location.href = 'index.php?controller=PostAdmin&multipage=&id=1';
            });
        }
        if (RegExp('multipage', 'gi').test(window.location.search)) {
            learningBlog();
        }
    <?php }?>

    <?php if ($_smarty_tpl->tpl_vars['hintId']->value == "hint_post") {?>
        function learningPost(){
            var intro = introJs();
            intro.setOptions({
                exitOnOverlayClick: false,
                showStepNumbers: false,
                showBullets: false,
                showProgress: true,
                prevLabel: '« <?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_prev;?>
',
                nextLabel: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_next;?>
 »',
                skipLabel: '×',
                doneLabel: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_done;?>
',
                steps: [
                    {
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_post_intro;?>
',
                    },
                    {
                        element: '.fn_step-1',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_post_name;?>
',
                        position: 'bottom',
                    },
                    {
                        element: '.fn_step-2',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_post_url;?>
',
                        position: 'bottom',
                    },
                    {
                        element: '.fn_step-3',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_post_activity;?>
',
                        position: 'left',
                    },
                    {
                        element: '.fn_step-4',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_post_images;?>
',
                        position: 'right',
                    },
                    {
                        element: '.fn_step-5',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_post_parameters;?>
',
                        position: 'left',
                    },
                    {
                        element: '.fn_step-6',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_post_product;?>
',
                        position: 'left',
                    },
                    {
                        element: '.fn_step-7',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_meta_data;?>
',
                        position: 'top',
                    },
                    {
                        element: '.fn_step-8',
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_post_descriptions;?>
',
                        position: 'top',
                    },
                    {
                        intro: '<?php echo $_smarty_tpl->tpl_vars['btr']->value->learning_hint_post_finish;?>
',
                        position: 'center',
                    }
                ]
            });

            intro.start();
            intro.oncomplete(function() {
                $.ajax({
                    url: 'ajax/lesson_done.php',
                    data: {
                        lesson: 6
                    }
                })
                    .done(function() {
                        window.location.href = 'index.php?controller=LearningAdmin';
                    });
            });
        }
        if (RegExp('multipage', 'gi').test(window.location.search)) {
            learningPost();
        }
    <?php }?>

<?php echo '</script'; ?>
>

<?php }
}
