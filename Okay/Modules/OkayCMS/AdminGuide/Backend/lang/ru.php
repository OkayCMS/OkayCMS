<?php
$lang['description__title'] = 'Документация админ панели';
$lang['description__description'] = 'В этом разделе приведены примеры готовых решений по оформлению админки. ';
$lang['description_context'] = 'Содержание';
$lang['description_title_grid'] = 'Структура сетки (grid)';
$lang['description_title_typography'] = 'Типографика';
$lang['description_title_alerts'] = 'Уведомления (alerts)';
$lang['description_title_buttons'] = 'Кнопки (buttons)';
$lang['description_title_tooltips'] = 'Подсказки (tooltips)';
$lang['description_title_forms'] = 'Элементы формы';
$lang['description_title_switcher'] = 'Переключатель (switcher)';
$lang['description_title_tabs'] = 'Вкладки (tabs)';
$lang['description_title_add_images'] = 'Загрузка файлов и изображений';
$lang['description_title_clipboard'] = 'Запись данных в буфер обмена';
$lang['description_title_icons'] = 'Иконки';
$lang['description_info_grid'] = '<p>Структура админки построена на bootstrap grid, которая соответствующим образом масштабируется до 12 столбцов при увеличении размера устройства или экрана просмотра. Она включает в себя предопределенные классы для упрощения компоновки разметки.</p>
                                  <p>Эта система сеток используется для создания блоков на странице через группы строк ( <strong>.row</strong> ) и столбцов ( <strong>.col-*</strong> ) в которых размещается контент.</p>
                                  <div class="alert alert--info">
                                  <div class="alert__content">
                                  <div class="alert__title">Важные моменты для правильного использования:</div>
                                  <ul class="list_styling">
                                      <li>Используйте строки ( <strong>.row</strong> ) для создания горизонтальных групп столбцов ( <strong>.col-*</strong> ).</li>
                                      <li>Контент должен быть помещен в столбцы ( <strong>.col-*</strong> ) и только столбцы могут быть непосредственными дочерними элементами строк ( <strong>.row</strong> )</li>
                                      <li>У столбцов ( <strong>.col-*</strong> ) заданы внутренние отступы ( <strong>padding</strong> ) между собой, соответственно у  строк ( <strong>.row</strong> ) заданы отрицательные внешние отступы ( <strong>margin</strong> ), чтобы правильно выровнять контент в сетке</li>
                                      <li>Столбцы ( <strong>.col-*</strong> ) создаются путем указания количества доступных 12-ти столбцов, которые нужно развернуть. Например три равных столбца будут использовать ( <strong>.col-*-4</strong> )</li>
                                      <li>Если более чем 12 столбцов ( <strong>.col-*</strong> ) помещены в одну строку ( <strong>.row</strong> ), то каждая группа дополнительных столбцов ( <strong>.col-*</strong> ) будут объединятся в блок и переноситься на новую строку.</li>
                                      <li>Сетка классов применяется к устройствам с шириной экрана, большими или равными размерам контрольным точкам и переопределяют классы сетки, ориентированные на более мелкие устройства. Поэтому, например, применение какого-либо класса ( <strong>.col-md-*</strong> ) к элементу не только повлияет на его стиль на средних устройствах, но и на большие устройства, если класс ( <strong>.col-lg-*</strong> ) отсутствует.</li>
                                  </ul></div></div>';
$lang['description_title2_grid'] = 'Основные параметры сетки:';
$lang['description_info2_grid'] = '<p>Адаптивные блоки - это элементы сетки, которым установлен один или несколько классов ( <strong>.col-*-*</strong> ). Данные блоки являются основными «строительными» кирпичиками, именно они и формируют необходимую структуру.</p>
                                    <p>Ширина адаптивному блоку задаётся в связке с типом устройства. Это означает, что адаптивный блок на разных устройствах может иметь разную ширину. Именно из-за этого данный блок и называется адаптивным.</p>
                                    <p>Установка ширины адаптивному блоку, которую он должен иметь на определённом устройстве, задаётся по умолчанию числом от 1 до 12. Данное число указывается вместо второго знака <strong>*</strong> в классе ( <strong>.col-*-*</strong> ).</p>
                                    <p>Данное число (по умолчанию от 1 до 12) определеяет какой процент от ширины родительского элемента должен иметь адаптивный блок.</p>
                                    <p>Например, число 1 - устанавливает адаптивному блоку ширину, равную 8,3% (1/12) от ширины родительского блока. Число 12 - ширину, равную 100% (12/12) от ширины родительского блока.</p>
                                    <p>Кроме указания ширины адаптивному блоку необходимо ещё указать и тип устройства (вместо первого <strong>*</strong>). Класс устройства будет определять то, на каком viewport будет действовать эта ширина.</p>
                                    <p> В нашем случае различают 5 основных классов:</p>
                                    <ul class="list_styling">
                                        <li><strong>col-xs-*</strong> (ширина viewport >0)</li>
                                        <li><strong>col-sm-*</strong> (ширина viewport >= 576px)</li>
                                        <li><strong>col-md-*</strong> (ширина viewport >= 768px)</li>
                                        <li><strong>col-lg-*</strong> (ширина viewport >= 992px)</li>
                                        <li><strong>col-xl-*</strong> (ширина области просмотра браузера >=1200px)</li>
                                    </ul>';
$lang['description_title3_grid'] = 'Пример работы сетки:';
$lang['description_info_typography'] = '<p>Документация и примеры типографии, которая используется в системе</p>';
$lang['description_title2_typography'] = 'Заголовки:';
$lang['description_title3_typography'] = 'Размер шрифта:';
$lang['description_title4_typography'] = 'Цвет шрифта:';
$lang['description_typography_class'] = 'Класс:';
$lang['description_typography_example'] = 'Пример:';
$lang['description_typography_h_page'] = 'Заголовки для страниц';
$lang['description_typography_h_block'] = 'Заголовки для разделов';
$lang['description_typography_h_label'] = 'Заголовки к полям (Label)';
$lang['description_promo_alerts'] = '<p>Уведомления доступны для любой длины текста. Модификаторы типов <strong>.alert--error</strong>, <strong>.alert--success</strong>, <strong>.alert--info</strong>, <strong>.alert--warning</strong> задают определенный стиль, а модификатор <strong>.alert--icon</strong> добавляет иконку к соответствующему типу.</p>
                                     <p>Для правильной стилизации используйте один из опциональных классов.</p>';
$lang['description_alerts1'] = '<p>Это основное уведомление, которое используется для описания страниц, модулей и т.д</p>';
$lang['description_alerts2'] = '<p>Это уведомление вывода ошибки, предупреждения об опасности или очень важной информации</p>';
$lang['description_alerts3'] = '<p>Это уведомление об успехе. Используется при сохранения каких либо успешных действий</p>';
$lang['description_alerts4'] = '<p>Это инфо-уведомление, его лучше использовать для написания инструкции каких либо действий</p>';
$lang['description_alerts5'] = '<p>Это уведомление может использоваться как для вывода совета, так и о предупреждении</p>';
$lang['description_promo_buttons'] = '<p>В системе есть предопределенные стили кнопок, каждые из которых имеет свою семантическую цель, и имеет дополнительные параметры для большего контроля и гибкости.</p>
                                      <p>Класс <strong>.btn</strong> можно использовать как для <strong>button</strong>, так и для <strong>input</strong> и ссыллок. Классы <strong>.btn_blue</strong>, <strong>.btn-outline-info</strong> и т.д задают определенный стиль кнопкам, а классы <strong>.btn_big</strong>, <strong>.btn_small</strong> и <strong>.btn_mini</strong> изменяют размер кнопок </p>';
$lang['description_title2_buttons'] = 'Пример кнопок c фоном:';
$lang['description_title3_buttons'] = 'Пример кнопок без фона:';
$lang['description_title4_buttons'] = 'Пример больших кнопок:';
$lang['description_title5_buttons'] = 'Пример средних кнопок:';
$lang['description_title6_buttons'] = 'Пример маленьких кнопок:';
$lang['description_btn_buttons'] = 'Кнопка';
$lang['description_info_tooltips'] = '<p>В системе реализовано два способа вывода подсказок. Первый способ позволяет вывести большой текст для подробного описания, второй же лучше использовать для вывода названия кнопки или иконки.</p>
                                       <p>Для примера рассмотрим два способа использования подсказок.</p>';
$lang['description_title2_tooltips'] = 'Подсказки для подробного описания:';
$lang['description_title3_tooltips'] = 'Подсказки для вывода названия:';
$lang['description_info_switcher'] = '<p>В этом разделе приведены примеры и рекомендации по использованию стилей управления формой и пользовательских компонентов для широкого использования</p>';
$lang['description_title2_switcher'] = 'Переключатель (switcher)';
$lang['description_title2_switcher_label'] = 'Активность';
$lang['description_info_add_images'] = '<p>Для загрузки файлов или изображений предусмотрены два варианта. Первый - это загрузка текстового файла, а второй вариант отлично подойдет для загрузки изображений и управлениями ими. </p>';
$lang['description_title2_add_images'] = 'Загрузка файла:';
$lang['description_title3_add_images'] = 'Загрузка изображений:';
$lang['description_info3_add_images'] = '<p>Этот способ используется для загрузки фото к баннерам, товарам, категориям и т.д. Можно загружать несколько фото одновременно, удалять их и перемещать по приоритетности. Для его настройки необходимо подставить свои переменные в <strong>&#123;foreach&#125;</strong> и в <strong>&#123;if&#125;</strong></p>';
$lang['description_text_clip_clipboard'] = 'Нажмите, чтобы скопировать в буфер обмена.';
$lang['description_info_icons'] = '<p>В системе используется иконки <a href="https://fontawesome.com/v4.7.0/icons/" target="_blank">fontawesome</a> и дефолтный набор svg конок, который находится в <strong>svg_icon.tpl</strong>. Для того чтобы вывести определенную иконку, нужно подключить <strong>svg_icon.tpl</strong> с параметром <strong>svgId=""</strong>, в котором указываем id нужной иконки.</p>
                                   <p>Для примера, чтобы вывести иконку корзины, вставляем <strong>&#123;include file="svg_icon.tpl" svgId="trash"&#125;</strong> и в <strong>svgId</strong> указываем id иконки корзины.</p> Наша иконка:  {include file="svg_icon.tpl" svgId="trash"}';
$lang['description_title2_icons'] = 'Список дефолтных иконок:';