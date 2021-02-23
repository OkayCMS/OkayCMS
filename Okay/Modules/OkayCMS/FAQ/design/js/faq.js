$(document).on('click', '.faq__question', function() {
    var outerBox = $(this).parents('.fn_faq');
    var target = $(this).parents('.faq__item');

    if($(this).hasClass('active')!==true){
        $(outerBox).find('.faq__item .faq__question').removeClass('active');
    }

    if ($(this).next('.faq__content').is(':visible')){
        return false;
    }else{
        $(this).addClass('active');
        $(outerBox).children('.faq__item').removeClass('visible');
        $(outerBox).find('.faq__item').children('.faq__content').slideUp(300);
        target.addClass('visible');
        $(this).next('.faq__content').slideDown(300);
    }
});