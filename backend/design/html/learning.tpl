{* Title *}
{$meta_title=$btr->learning_title scope=global}

<div class="boxed boxed_learning--bg">
    <div class="row">
        <div class="col-md-12">
            <div class="wrap_heading">
                <div class="heading_page">
                    {$btr->learning_welcome|escape}
                </div>
                <div class="heading_box">
                    {$btr->learning_annotation|escape}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="boxed boxed--progress-bar">
    <div class="row">
        <div class="col-lg-12 col-md-12 ">
            <div class="row">
                <div class="col-sm-12">
                    <div class="heading_box">{$btr->we_use_of_learning} {$progress}% {$btr->of_system}</div>
                    <div class="progress-bar__wrap">
                        <div class='progress-bar-holder'>
                            <div class='progress-bar'>
                                {$progress}%
                            </div>
                        </div>
                        <form method="POST">
                            <input type="hidden" name="session_id" value="{$smarty.session.id}" />
                            <button class="btn btn btn_small btn_blue add btn_skip_learning" name="skip_learning" value="1">{$btr->skip_learning}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="learning_card__list">
    {foreach $lessons as $lesson}
        <div class="learning_card__item">
            <div class="learning_card__inner">
                <div class="learning_card__header">
                    <div class="icon_learning hint-right-middle-t-white-s-small-mobile  hint-anim" data-hint="{$btr->lesson_type}">
                        <svg viewBox="0 0 181.207 181.207" width="20px" height="20px">
                            <path fill="currentColor" d="M176.805,62.738L92.701,34.067c-1.361-0.464-2.834-0.464-4.195,0L4.402,62.738C1.77,63.636-0.001,66.11,0,68.892
            c0.001,2.783,1.772,5.256,4.407,6.152l32.694,11.121v33.883c0,1.62,0.604,3.181,1.695,4.378
            c13.356,14.657,32.383,23.063,52.2,23.063c19.622,0,37.853-7.858,51.333-22.127c1.141-1.207,1.775-2.804,1.775-4.464V86.215
            l13.498-4.612v16.343c-1.52,1.19-2.5,3.037-2.5,5.116v15.855c0,3.59,2.91,6.5,6.5,6.5s6.5-2.91,6.5-6.5v-15.855
            c0-2.08-0.98-3.927-2.5-5.116V78.87l11.205-3.828c2.631-0.899,4.399-3.373,4.398-6.153
            C181.206,66.108,179.437,63.635,176.805,62.738z M131.105,118.235c-10.799,10.501-24.944,16.253-40.108,16.253
            c-15.348,0-30.114-6.18-40.896-17.036V90.587l38.597,13.128c0.679,0.231,1.386,0.346,2.093,0.346c0.71,0,1.421-0.116,2.102-0.349
            l38.213-13.056V118.235z M90.786,90.694L26.664,68.883l63.939-21.797l63.979,21.811L90.786,90.694z"/>
                        </svg>
                        <i class="learning_card__header-category">{$lesson->button}</i>
                    </div>
                    <div class="learning_card__bookmark hint-top-right-t-info-s-small-mobile  hint-anim {if $lesson->done}learning_card__bookmark--done{/if}" {if $lesson->done}data-hint="{$btr->lesson_status_done}"{else}data-hint="{$btr->lesson_status_not_done}"{/if}>
                        <svg viewBox="0 0 57 109">
                            <path fill="currentColor" d="M55.2581768,1.68472072 C55.4963653,1.99229417 55.9264683,2.49612939 56.0964433,3.13700841 C56.2097599,3.56426109 56.2519834,5.17933791 56.2231139,7.98223886 L56.2231139,107.862169 L56.2231139,108.198446 L54.3316672,108.198446 L31.7336208,93.0756154 L7.60113862,107.862169 L5.75307495,107.862169 L5.75307495,6.37648608 C5.75307495,6.30890507 5.75307495,6.13481848 5.75307495,5.01912143 C5.66354505,4.99969682 4.34603562,5.0760989 3.17031126,5.01912143 C2.37863609,4.98075559 1.63339871,4.78553375 1.43802847,4.59524702 C0.0310234451,3.22485204 2.05721932,0.297207262 2.83338791,0.0743018154 C3.30404348,-0.0608642931 18.0701757,-0.0116148851 47.1317847,0.222050039 C48.3458258,0.23181133 51.4117414,0.098494554 53.0022991,0.566857956 C53.9351979,0.841563897 55.0199882,1.37714727 55.2581768,1.68472072 Z"></path>
                            <path fill="currentColor" d="M5.75675723,4.70498479 C5.75675723,1.64840033 5.07444433,0.186950855 3.70981853,0.320636374 C3.6022952,0.308972102 3.51184528,0.308972102 3.43846878,0.320636374 C3.36509229,0.332300645 3.17764187,0.376476982 2.87611754,0.453165385 C2.70892271,0.520918007 2.58633026,0.592984069 2.50834017,0.669363572 C2.43035008,0.745743075 2.28191228,0.93911018 2.06302678,1.24946489 L1.47106804,2.55582953 L1.3205017,3.1102027 L1.38880379,3.88608585 L1.8679792,4.44088638 L2.75902248,4.60316775 L3.70981853,4.70498479 L4.7174867,4.70498479 L5.75675723,4.70498479 C5.75675723,7.76156925 5.75675723,7.76156925 5.75675723,4.70498479 Z"></path>
                        </svg>
                    </div>
                </div>
                <div class="learning_card__body">
                    <div class="learning_card__title" title="{$lesson->title|escape}">{$lesson->title|escape}</div>
                    <div class="learning_card__description">{$lesson->description|escape}</div>
                </div>
                <div class="learning_card__footer">
                    <a class="learning_card__button" href="{url controller=$lesson->target_module}&multipage" target="_blank">{$btr->lesson_explore}</a>
                    <div class="learning_card__informers">
                        {if $lesson->video}
                        <span class="fn_video_open learning_card__informer learning_card__video hint-top-right-t-info-s-small-mobile  hint-anim" data-hint="{$btr->lesson_video}">
                            <svg class="video_open" data-video="{$lesson->video}" width="40px" height="40px" viewBox="0 0 215.094 215.094" >
                                <path fill="currentColor" d="M28.302,32.547C12.673,32.547,0,45.22,0,60.849v93.396c0,15.629,12.673,28.302,28.302,28.302h158.491
                                c15.629,0,28.301-12.673,28.301-28.302V60.849c0-15.629-12.672-28.302-28.301-28.302H28.302z M90.547,145.583V69.511l50,38.036
                                L90.547,145.583z"/>
                            </svg>
                        </span>
                        {/if}
                        {*if !$lesson->done}
                             <form class="learning_card__informer hint-top-middle-t-info-s-small-mobile  hint-anim" data-hint="{$btr->lesson_skip}"  method="POST">
                                <input type="hidden" name="session_id" value="{$smarty.session.id}" />
                                <input type="hidden" name="lesson_id" value="{$lesson->id}" />
                                <button class="learning_card__done" name="action" title="{$btr->lesson_skip}" value="lesson_skip">
                                    <svg width="12px" height="12px" viewBox="0 0 163.861 163.861">
                                        <path fill="currentColor" d="M34.857,3.613C20.084-4.861,8.107,2.081,8.107,19.106v125.637c0,17.042,11.977,23.975,26.75,15.509L144.67,97.275
            c14.778-8.477,14.778-22.211,0-30.686L34.857,3.613z"/>
                                    </svg>
                                </button>
                            </form>
                        {/if*}
                    </div>
                </div>
            </div>
        </div>
    {/foreach}
</div>

<link href="design/js//loading_bar/loading-bar.css" rel="stylesheet" type="text/css" />
<script src="design/js/loading_bar/loading-bar.js"></script>

{literal}
<script>
    $(document).ready(function(){
        $(".progress-bar").each(function(){
            var percentage = parseInt($(this).html());
            if(percentage > 0){
                $(this).animate({'width':''+percentage+'%'}, 1800);
            }else{
                $(this).css({'color':'black', 'background':'none'}, 4000);
            }
        })
    })
</script>
{/literal}

{*youtube_video_popup*}
<div id="fn_video" style="display: none;">
    <div class="video_adaptive">
        <iframe class="youtube" width="560" height="315" src="" frameborder="0" allowfullscreen></iframe>
    </div>
</div>

{literal}
<script>
    $('.fn_video_open').on('click', function() {
        let video = $(this).find('.video_open').data('video');
        $('iframe.youtube').attr('src', video);

        $.fancybox.open({
            src: '#fn_video'
        })
    });
</script>
{/literal}
{*/youtube_video_popup*}