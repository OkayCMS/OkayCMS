{if (!$translation || !$translation->id) && $settings->deploy_build_channel && $settings->deploy_build_channel != 'local'}
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="alert alert--center alert--icon alert--error">
                <div class="alert__content">
                    <div class="alert__title">
                        {$btr->auto_deploy_disable_add|escape}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function () {
            $('.min_content_fix input, .min_content_fix textarea, .min_content_fix button').prop('disabled', true);
        });
    </script>
{/if}