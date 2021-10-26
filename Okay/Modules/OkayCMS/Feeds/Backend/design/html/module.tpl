{$meta_title = $btr->okay_cms__feeds__module__title|escape scope=global}

{*Название страницы*}
<div class="row">
    <div class="col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {$btr->okay_cms__feeds__module__title|escape}
            </div>
        </div>
    </div>
    <div class="col-md-12 float-xs-right"></div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="alert alert--icon">
            <div class="alert__content">
                <div class="alert__title">{$btr->okay_cms__feeds__module__description_title}</div>
                <p>{$btr->okay_cms__feeds__module__description_content|escape|nl2br}</p>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="alert alert--icon alert--info">
            <div class="alert__content">
                <div class="alert__title">{$btr->okay_cms__feeds__module__instruction_title}</div>
                <p>{$btr->okay_cms__feeds__module__instruction_content|escape|nl2br}</p>
            </div>
        </div>
    </div>
</div>
