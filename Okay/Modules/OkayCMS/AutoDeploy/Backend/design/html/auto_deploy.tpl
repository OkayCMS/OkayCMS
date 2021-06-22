{$meta_title = $btr->auto_deploy_title scope=global}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {$btr->auto_deploy_title|escape}
            </div>
            {if $faq->id}
                <div class="box_btn_heading">
                    <a class="btn btn_small btn-info add" target="_blank" href="{url_generator route="OkayCMS_FAQ_main" absolute=1}">
                        {include file='svg_icon.tpl' svgId='icon_desktop'}
                        <span>{$btr->general_open|escape}</span>
                    </a>
                </div>
            {/if}
        </div>
    </div>
    <div class="col-md-12 col-lg-12 col-sm-12 float-xs-right"></div>
</div>

{*Главная форма страницы*}
<div class="row">
    <div class="col-xs-12 ">
        <div class="match_matchHeight_true">
            {*Название элемента сайта*}
            <div class="row d_flex">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="boxed" style="max-width: 800px;margin: 0 auto;">
                        
                        {if $settings->deploy_build_channel && $settings->deploy_build_channel != 'local'}
                            <form method="post" enctype="multipart/form-data" action="{url controller='OkayCMS.AutoDeploy.AutoDeployAdmin@updateProject'}">
                                <input type=hidden name="session_id" value="{$smarty.session.id}">
                                <input type="hidden" name="lang_id" value="{$lang_id}" />
                                <div class="form-group">
                                    <button name="update" type="submit" class="btn btn_mini btn-secondary" value="1">Обновить проект с битбакета</button>
                                </div>
                            </form>
                        {/if}
                        
                        {if $settings->deploy_last_status_text}
                            <a href="#last_deploy_log" class="fn_fancybox_button">Лог последней сборки проекта</a>
                            <div style="display:none;">
                                <div id="last_deploy_log" style="min-width: 500px;min-height: 300px;">{$settings->deploy_last_status_text|nl2br}</div>
                            </div>
                        {/if}
                        
                        <h5 style="text-align: center;"><b>1</b> Основные правила</h5>
                        <p>
                            <b>1.1</b> Инструкция предполагает разворачивание нового проекта. Чтобы развернуть уже работающий проект
                            нужно будет его выкачать на локалку и удалить с сервера, а далее все инструкции совпадают 
                            как для нового проекта.
                        </p>
                        <p>
                            <b>1.2</b> Все описанные здесь инструкции являются общими, и не описывают нюансов, которые
                            могут быть на конкретном проекте.
                            <br>
                            Данный модуль предназначен только для разработчиков, которые умеют работать с гитом.
                            За все действия ответственность несет исполнитель, который делает эти действия.
                        </p>
                        <p>
                            <b>1.3</b> Данные, а именно изображения товаров, категорий etc и данные с БД на гите не ведутся. 
                            Данные с БД могут быть включены в миграцию только если эти данные нужны для работы именно кода 
                            (в основном это записи таблицы settings, языки etc).
                        </p>
                        
                        <h5 style="text-align: center;"><b>2</b> Инициализация проекта</h5>
                        <h6 style="text-align: center;"><b>2.1</b> Инициализация нового проекта</h6>
                        <p>
                            <b>2.1.1</b> Инициализация репозитория на bitbucket.<br>
                            В аккаунте на bitbucket для клиентских проектов создается приватный репозиторий.
                        </p>
                        <p>
                            <b>2.1.2</b> На локалке нужно инициализировать проект и сделать первый коммит.<br>
                            Настроить <b>.gitignore</b> в соответствии с 
                            <a href="#gitignore_example" class="fn_fancybox_button"><b>примером</b></a>.
                            Данный пример является общим для системы OkayCMS и может отличаться для конкретного проекта.
                            <br>
                            Добавить удаленный репозиторий с алиасом origin (напр. git remote add origin 
                            https://&lt;login&gt;@bitbucket.org/&lt;user&gt;/&lt;repoSlug&gt;.git) В примере login - это имя пользователя 
                            под которым мы будем пушить изменения, user - это имя пользователя владельца 
                            репозитория, а repoSlug - это название репозитория.<br>
                            Создать соответствующую ветку (dev или production) и настроить окружение.
                        </p>
                        <p>
                            <b>2.1.3</b> Файлы которые в игноре, нужно переносить руками по FTP.
                            Конфиг и подключение к БД настраивается в файле config/config.local.php. 
                            Этот файл содержит структуру идентичную config/config.php но он по гиту не ведется.
                        </p>
                        <p>
                            <b>2.1.4</b> Настройка SSH доступа на сервере для bitbucket.<br>
                            Чтобы команда git pull не требовала ввода пароля, нужно настроить SSH доступ для bitbucket.
                            Для этого нужно создать SSH public и private key, и добавить public key в настройки репозитория на bitbucket.
                            Подключиться по SSH к серверу, перейти в папку ssh (если ее нет, создаем mkdir ~/.ssh) выполнить команду 
                            <b>ssh-keygen -t rsa</b> и указать название файла для хранения ключей (рекомендую bitbucket_rsa).
                            На запрос пароля оставляем его пустым.<br><br>
                            После этого в папке ~/.ssh будут созданы два файла bitbucket_rsa и bitbucket_rsa.pub, нам 
                            нужно скопировать содержимое bitbucket_rsa.pub, оно понадобиться позже 
                            (как вариант открыть файл командой vi ~/.ssh/bitbucket_rsa.pub).
                            Затем открыть на редактирование файл ~/.ssh/config той же командой 
                            vi ~/.ssh/config (если файла нет, он создастся) и вставить в него содержимое:
<pre>
Host bitbucket.org
IdentityFile ~/.ssh/bitbucket_rsa
</pre>
                            <b>NOTICE:</b> права на файл конфига должны быть 600, если это не так, изменить можно командой chmod 600 ~/.ssh/config<br>
                            Заходим на bitbucket в раздел Settings -> Access keys -> Add key. Заполняем поля label название домена, key содержимое файла bitbucket_rsa.pub
                        </pre>
                        <p style="text-align: center;">
                            <img src="../Okay/Modules/OkayCMS/AutoDeploy/Backend/design/images/example_add_key.png">
                        </p>
                        
                        <p>
                            <b>2.1.5</b> Пуш локальных веток.<br>
                            После настройки окружения нужно запушить ветки master и новую (dev или production) на bitbucket.
                        </p>
                        
                        <p>
                            <b>2.1.6</b> Создание тестового сервера.<br>
                            Подключаемся к тестовому серверу по SSH, переходим в папку самого домена и клонируем нужную нам ветку с bitbucket по SSH
                            (<b>git clone -b dev git@bitbucket.org:&lt;user&gt;/&lt;repoSlug&gt;.git .</b> (в конце точка, это путь к папке в которую клонировать)), 
                            если SSH доступ ранее был настроен верно, клон пройдет без запроса пароля (!).<br>
                            <b>NOTICE:</b> Если в папке домена есть какие-то файлы (иногда хостинги кладут index.html как заглушку), 
                            их нужно удалить (<b>но думайте головою что удаляете!!!</b>).<br>
                            <b>NOTICE:</b> После этого сайт будет уже на сервере, но БД еще не будет развернута, для этого 
                            нужно выполнить первую миграцию вручную (см. пункт 3.2).
                            <br>
                            Также нужно все файлы которые в игноре перенести по ftp.
                        </p>
                        
                        <p>
                            <b>2.1.7</b> Настройка bitbucket webhooks.<br>
                            Заходим в настройки репозитория на вкладку Webhooks, нажимаем add webhook.
                            <div style="text-align: center;">
                                <img src="../Okay/Modules/OkayCMS/AutoDeploy/Backend/design/images/example_add_hook.png">
                            </div>
                            Поле title называем dev или production в зависимости для какого сервера мы настраиваем.
                            Поле URL заполняем следующим образом (<b>выберите нужный канал обновлений</b>):
                            <br>
                            <br>
                            <div class="form-group">
                                <input id="local_channel" type="radio" name="deploy_build_channel" value="local"{if !$settings->deploy_build_channel || $settings->deploy_build_channel == 'local'} checked{/if}>
                                <label for="local_channel"><b>local</b> Разработка на локальной машине</label>
                            </div>
                            <div class="form-group">
                                <input id="dev_channel" type="radio" name="deploy_build_channel" value="dev"{if $settings->deploy_build_channel == 'dev'} checked{/if}>
                                <label for="dev_channel"><b>dev</b> {url_generator route='OkayCMS_AutoDeploy_build' channel=dev buildKey=$settings->deploy_build_key absolute=1}</label>
                            </div>
                            <div class="form-group"><div>
                                <input id="production_channel" type="radio" name="deploy_build_channel" value="production"{if $settings->deploy_build_channel == 'production'} checked{/if}>
                                <label for="production_channel"><b>production</b> {url_generator route='OkayCMS_AutoDeploy_build' channel=production buildKey=$settings->deploy_build_key absolute=1}</label>
                            </div>
                        
                            Поле Triggers оставляем выбранным Repository push. Теперь после пуша (git push) в ветку указанную в channel,
                            проект будет автоматически "собираться" на сервере.
                        </p>
                        <p>
                            При работе на dev или production сервере, все переводы будут сохраняться в файлах: 
                            <b>design/&lt;themeName&gt;/lang/local.*.php</b>. Данные файлы должны быть добавлены в .gitignore
                        </p>
                        
                        <h6 style="text-align: center;"><b>2.2</b> Клонирование проекта</h6>
                        <p>
                            <b>2.2.1</b> Существующий проект клонируется из репозитория bitbucket посредством команды git clone.
                        </p>
                        
                        
                        <h5 style="text-align: center;"><b>3</b> Работа на локалке</h5>
                        <h6 style="text-align: center;"><b>3.1</b> Ветвление</h6>
                        <p>
                            <b>3.1.1</b> Есть три основные ветки "master", "dev" и "production".
                            С ветки dev проект автоматически собирается на тестовом сервере (напр. test.domain.com), 
                            с ветки production проект собирается на production сервере (production - это реальный сайт клиента).
                            Ветка master используется для ведения проекта без заливки его на тестовый или production сервер.
                            Все работы нужно вести в своей ветке, созданной от ветки master (напр. my_branch). 
                            После внесения изменений нужно сделать коммит в ветке my_branch, "втянуть" все изменения 
                            из ветки master удаленного репозитория командой git pull и примержить ветку my_branch в master.
                        </p>

                        <h6 style="text-align: center;"><b>3.2</b> Миграции БД</h6>
                        <p>
                            <b>3.2.1</b> Миграции нужны для ведения на гите изменения базы данных.
                            Миграции предполагаются только "вперёд", rollback-миграций в данной системе нет.
                        </p>
                        <p>
                            <b>3.2.2</b> Нулевой миграцией (которая создаёт структуру базы данных) считается файл
                            1DB_changes/okay_clean.sql, или структура базы, полученная при установке окая через установщик
                        </p>
                        <p>
                            <b>3.2.3</b> Миграции на локалке выполняются только в ручном режиме.
                            На production или dev сервере миграции выполняются автоматически со сборкой проекта.
                            {if $new_migrations}
                                <div class="fn_new_migrations">
                                    <b>У вас есть невыполненные миграции:</b>
                                    {foreach $new_migrations as $migration}
                                        <div><b>{$migration['name']}</b></div>
                                    {/foreach}
                                    <button type="button" class="btn btn_mini btn-secondary fn_execute_migrations">Выполнить все миграции</button>
                                </div>
                            {/if}
                            <b{if $new_migrations} style="display: none"{/if} class="fn_all_migrations_executed">У вас все миграции выполнены.</b>
                        </p>
                        <p>
                            <b>3.2.4</b> Создание новой миграции<br>
                            Миграции создаются только на локалке. Миграции хранятся в директории 
                            <b>Okay/Modules/OkayCMS/AutoDeploy/migrations</b>
                            {if !$settings->deploy_build_channel || $settings->deploy_build_channel == 'local'}
                                <div class="form-group">
                                    <input class="fn_new_migration_name form-control" type="text" style="width: calc(100% - 150px);display: inline-block;">
                                    <button type="button" class="btn btn_mini btn-secondary fn_create_migration">Создать миграцию</button>
                                </div>
                            {else}
                                <p><b>На сервере не создаются миграции</b></p>
                            {/if}
                            После создания миграции нужно идти в директорию миграций и в созданный файл внести
                            SQL запрос самой миграции.
                        </p>

                        <h5 style="text-align: center;"><b>4</b> Заливка (push) изменений</h5>
                        <h6 style="text-align: center;"><b>4.1</b> Обмен изменениями с командой разработчиков.</h6>
                        <p>
                            Для обычной работы достаточно пушить только ветку master (для обмена изменениями с остальными разработчиками).
                            Но предварительно не забываем делать git pull
                        </p>
                        <h6 style="text-align: center;"><b>4.2</b> Заливка изменений на dev сервер.</h6>
                        <p>
                            Чтобы залить изменения на тестовый сервер, нужно примержить изменения из ветки master в ветку dev и запушить ветку dev.<br>
                            <b>NOTICE:</b> Важно проверить чтобы доступы к БД в config.php не были перезаписаны
                        </p>
                        <h6 style="text-align: center;"><b>4.3</b> Заливка изменений на production сервер.</h6>
                        <p>
                            После того, как все протестировано на тестовом сервере (соответственно в ветке dev), нужно примержить ветку dev в ветку production и запушить ветку production.
                        </p>
                        <h5 style="text-align: center;"><b>5</b> Не забывать выполнять <b>composer install</b></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="gitignore_example" style="display:none;min-width: 500px;min-height: 300px;">
<pre>
compiled/*/*.php
Okay/xml/compiled/*.php
/vendor/
backend/design/compiled/*.php
backend/files/*/*.*
!backend/files/*/.keep_folder

files/**/*.*
!files/**/*.phtml
!files/**/*.php*
!files/**/*.cgi
!files/**/*.exe
!files/**/*.pl
!files/**/*.asp
!files/**/*.aspx
!files/**/*.shtml
!files/**/*.shtm
!files/**/*.fcgi
!files/**/*.fpl
!files/**/*.jsp
!files/**/*.htm
!files/**/*.html
!files/**/*.wml
!files/**/.gitignore
!files/lang/*.*
files/thumbs/*
cache/

/Okay/log/*.log
config/config.local.php
design/*/lang/local.*.php

!*.keep_folder
!*.htaccess
robots.txt
</pre>
</div>
<script>

    $('.fn_fancybox_button').on('click', function (e) {
        e.preventDefault();
        $.fancybox.open({
            src: $(this).attr('href'),
            type : 'inline',
            touch:  false,
        });
    });
    
    $('.fn_create_migration').on('click', function () {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: "{url controller='OkayCMS.AutoDeploy.AutoDeployAdmin@createMigration'}",
            data: {
                migration_name: $('.fn_new_migration_name').val(),
                session_id: '{$smarty.session.id}',
            },
            success: function(data) {
                if (data === false) {
                    alert('Укажите название миграции');
                } else {
                    alert('Создана миграция ' + data);
                }
            }
        })
    });
    
    $('input[name="deploy_build_channel"]').on('change', function () {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: "{url controller='OkayCMS.AutoDeploy.AutoDeployAdmin@saveChannel'}",
            data: {
                channel: $(this).val(),
                session_id: '{$smarty.session.id}',
            },
        })
    });
    
    $('.fn_execute_migrations').on('click', function () {
        $.ajax({
            dataType: 'json',
            url: "{url controller='OkayCMS.AutoDeploy.AutoDeployAdmin@executeMigrations'}",
            success: function (data) {
                $('.fn_new_migrations').hide();
                $('.fn_all_migrations_executed').show();
            }
        })
    });
</script>