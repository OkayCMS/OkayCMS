{if $settings->deploy_build_channel && $settings->deploy_build_channel != 'local'}
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="alert alert--center alert--icon alert--error">
            <div class="alert__content">
                <div class="alert__title">
                    {$btr->auto_deploy_disable_add|escape}
                </div>
            </div>
        </div>
    </div>
    <style>
        .box_btn_heading,
        .okay_list_close,
        .okay_list_footer {
            display: none !important;
        }
    </style>
{/if}