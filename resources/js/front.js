;jQuery(document).ready(function () {
    "use strict";

    const orfwGlide = new Glide('.orfw_popup_order_products', {
        type: 'carousel',
        autoplay: 1,
        animationDuration: 3000,
        animationTimingFunc: 'linear',
        perView: 4.5,
        focusAt: 0
    });

    orfwGlide.mount();

    var ratingStars = jQuery('#orfw_popup .feedback input'),
        ratingComment = jQuery('#orfw_popup #orfw_popup_comment');
    
    ratingStars.on('click', function()
    {
        ratingComment.fadeIn();
    });
    
});
