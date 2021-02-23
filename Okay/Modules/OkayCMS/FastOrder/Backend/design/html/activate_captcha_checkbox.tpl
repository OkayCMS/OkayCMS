<div class="col-xl-3 col-lg-4 col-md-6">
    <div class="permission_box">
        <span>{$btr->okay_cms__fast_order__captcha|escape}</span>
        <label class="switch switch-default">
            <input class="switch-input" name="captcha_fast_order" value='1' type="checkbox" {if $settings->captcha_fast_order}checked=""{/if}/>
            <span class="switch-label"></span>
            <span class="switch-handle"></span>
        </label>
    </div>
</div>