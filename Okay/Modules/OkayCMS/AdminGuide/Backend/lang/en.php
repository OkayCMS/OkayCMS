<?php
$lang['description__title'] = 'Admin Panel Documentation';
$lang['description__description'] = 'This section provides examples of ready-made solutions for the design of the admin panel.';
$lang['description_context'] = 'Content';
$lang['description_title_grid'] = 'Grid structure';
$lang['description_title_typography'] = 'Typography';
$lang['description_title_alerts'] = 'Notifications (alerts)';
$lang['description_title_buttons'] = 'Buttons';
$lang['description_title_tooltips'] = 'Tooltips';
$lang['description_title_forms'] = 'Form elements';
$lang['description_title_switcher'] = 'Switch ';
$lang['description_title_tabs'] = 'Tabs';
$lang['description_title_add_images'] = 'Upload files and images';
$lang['description_title_clipboard'] = 'Writing data to the clipboard';
$lang['description_title_icons'] = 'Icons';
$lang['description_info_grid'] = '<p>The admin structure is built on bootstrap grid, which scales accordingly to 12 columns as the size of the device or viewing screen increases. It includes predefined classes to simplify the layout of markup.</p>
                                  <p>This grid system is used to create blocks on a page through groups of rows (<strong> .row </strong>) and columns (<strong> .col- * </strong>) that host the content.</p>
                                  <div class="alert alert--info">
                                  <div class="alert__content">
                                  <div class="alert__title">Important points for proper use:</div>
                                  <ul class="list_styling">
                                      <li>Use the rows (<strong> .row </strong>) to create horizontal column groups (<strong> .col- * </strong>).</li>
                                      <li>Content should be placed in columns (<strong> .col- * </strong>) and only columns can be immediate children of rows (<strong> .row </strong>)</li>
                                      <li>Columns (<strong> .col- * </strong>) are set with indentation (<strong> padding </strong>) between themselves, respectively, rows (<strong> .row </strong>) are set with negative indentation ( <strong> margin </strong>) to properly align content in the grid</li>
                                      <li>Columns (<strong> .col- * </strong>) are created by specifying the number of available 12 columns to expand. For example, three equal columns will use (<strong> .col - * - 4 </strong>)</li>
                                      <li>If more than 12 columns (<strong> .col- * </strong>) are placed on one line (<strong> .row </strong>), then each group of additional columns (<strong> .col - * </ strong >) will merge into a block and wrap to a new line.</li>
                                      <li>The class grid is applied to devices with screen widths greater than or equal to the size of the control points and redefine the grid classes that are targeted at smaller devices. Therefore, for example, applying a class (<strong> .col-md- * </strong>) to an element will not only affect its style on medium devices, but also on large devices if the class (<strong> .col -lg- * </strong>) is missing.</li>
                                  </ul></div></div>';
$lang['description_title2_grid'] = 'The main parameters of the grid:';
$lang['description_info2_grid'] = '<p>Adaptive blocks are grid elements that have one or more classes installed (<strong> .col - * - * </strong>). These blocks are the main "building" bricks, they form the necessary structure.</p>
                                    <p>The width of the adaptive block is set in conjunction with the type of device. This means that the adaptive block on different devices can have different widths. It is because of this that this block is called adaptive.</p>
                                    <p>The width of the adaptive block that it should have on a specific device is set by default from 1 to 12. This number is indicated instead of the second character <strong> * </strong> in the class (<strong> .col - * - * </ strong>).</p>
                                    <p>This number (by default, from 1 to 12) determines what percentage of the width of the parent element the adaptive block should have.</p>
                                    <p>For example, the number 1 - sets the adaptive block width equal to 8.3% (1/12) of the width of the parent block. The number 12 is the width equal to 100% (12/12) of the width of the parent block.</p>
                                    <p>In addition to specifying the width of the adaptive unit, you must also specify the type of device (instead of the first <strong> * </strong>). The device class will determine which viewport this width will act on.</p>
                                    <p>In our case, there are 5 main classes:</p>
                                    <ul class="list_styling">
                                        <li><strong>col-xs-*</strong> (width viewport >0)</li>
                                        <li><strong>col-sm-*</strong> (width viewport >= 576px)</li>
                                        <li><strong>col-md-*</strong> (width viewport >= 768px)</li>
                                        <li><strong>col-lg-*</strong> (width viewport >= 992px)</li>
                                        <li><strong>col-xl-*</strong> (browser viewport width >=1200px)</li>
                                    </ul>';
$lang['description_title3_grid'] = 'An example of a grid:';
$lang['description_info_typography'] = '<p>Documentation and examples of typography used in the system></p>';
$lang['description_title2_typography'] = 'Heading';
$lang['description_title3_typography'] = 'Font size:';
$lang['description_title4_typography'] = 'Font color:';
$lang['description_typography_class'] = 'Class:';
$lang['description_typography_example'] = 'Example:';
$lang['description_typography_h_page'] = 'Page heading';
$lang['description_typography_h_block'] = 'Section heading';
$lang['description_typography_h_label'] = 'Field heading (Label)';
$lang['description_promo_alerts'] = '<p>Notifications are available for any length of text. Type modifiers <strong> .alert - error </strong>, <strong> .alert - success </strong>, <strong> .alert - info </strong>, <strong> .alert - warning < / strong> set a specific style, and the <strong> .alert - icon </strong> modifier adds an icon to the corresponding type.</p>
                                     <p>For proper styling, use one of the optional classes.</p>';
$lang['description_alerts1'] = '<p>This is the main notice used to describe pages, modules, etc.</p>';
$lang['description_alerts2'] = '<p>This is a notification of error output, hazard warning or very important information.</p>';
$lang['description_alerts3'] = '<p>This is a success notice. Used when saving any successful actions.</p>';
$lang['description_alerts4'] = '<p>This is an info notification, it is better to use it to write instructions for any actions</p>';
$lang['description_alerts5'] = '<p>This notice can be used both for withdrawal of advice and for warning.</p>';
$lang['description_promo_buttons'] = '<p>The system has predefined button styles, each of which has its own semantic purpose, and has additional parameters for greater control and flexibility.</p>
                                      <p>The <strong> .btn </strong> class can be used for both the <strong> button </strong> and the <strong> input </strong> and links. The classes <strong> .btn_blue </strong>, <strong> .btn-outline-info </strong>, etc., define a specific style for the buttons, and the classes <strong> .btn_big </strong>, <strong> .btn_small </strong> and <strong> .btn_mini </strong> resize buttons</p>';
$lang['description_title2_buttons'] = 'Example of buttons with a background:';
$lang['description_title3_buttons'] = 'Example of buttons without a background:';
$lang['description_title4_buttons'] = 'Example of large buttons:';
$lang['description_title5_buttons'] = 'Example of middle buttons:';
$lang['description_title6_buttons'] = 'Example of small buttons:';
$lang['description_btn_buttons'] = 'Button';
$lang['description_info_tooltips'] = '<p>The system implements two ways to display prompts. The first method allows you to display large text for a detailed description, while the second is best used to display the name of a button or icon.</p>
                                       <p>For example, consider two ways to use hints.</p>';
$lang['description_title2_tooltips'] = 'Tips for a detailed description:';
$lang['description_title3_tooltips'] = 'Tips for displaying the name:';
$lang['description_info_switcher'] = '<p>This section provides examples and recommendations for using form-control styles and custom components for widespread use.</p>';
$lang['description_title2_switcher'] = 'Switch';
$lang['description_title2_switcher_label'] = 'Activity';
$lang['description_info_add_images'] = '<p>There are two options for downloading files or images. The first is downloading a text file, and the second option is perfect for downloading images and managing them.</p>';
$lang['description_title2_add_images'] = 'File Download:';
$lang['description_title3_add_images'] = 'Image upload:';
$lang['description_info3_add_images'] = '<p>This method is used to upload photos to banners, products, categories, etc. You can upload multiple photos at once, delete them and move them in priority. To configure it, you must substitute your variables in <strong>&#123;foreach&#125;</strong> and in <strong>&#123;if&#125;</strong></p>';
$lang['description_text_clip_clipboard'] = 'Click to copy to clipboard.';
$lang['description_info_icons'] = '<p>The system uses icons <a href="https://fontawesome.com/v4.7.0/icons/" target="_blank">fontawesome</a> and the default svg konok set, which is located in <strong> svg_icon.tpl </strong>. In order to display a specific icon, you need to connect <strong> svg_icon.tpl </strong> with the parameter <strong> svgId = "" </strong>, in which we indicate the id of the desired icon.</p>
                                   <p>For example, to display the basket icon, insert <strong>&#123;include file="svg_icon.tpl" svgId="trash"&#125;</strong> and in <strong>svgId</strong> specify the id of the basket icon..</p> Our icon:  {include file="svg_icon.tpl" svgId="trash"}';
$lang['description_title2_icons'] = 'List of default icons:';