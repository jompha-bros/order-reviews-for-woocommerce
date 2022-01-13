jQuery(document).ready(function() {
    'use strict';

    const orfwGlide = new Glide('.orfw_popup_order_products', {
        type: 'carousel',
        autoplay: 1,
        animationDuration: 3000,
        animationTimingFunc: 'linear',
        perView: 4.5,
        focusAt: 0,
    });

    orfwGlide.mount();

    var ratingStars = jQuery('#orfw_popup .feedback input'),
        ratingComment = jQuery('#orfw_popup #orfw_popup_comment');

    ratingStars.on('click', function() {
        ratingComment.fadeIn();
    });

    jQuery('#onPopupSubmit').on('click', function() {
        var order_id = jQuery('#order-id').data('order-id');
        var product_ids = [];
        jQuery('.orfw_popup_order_products').find('ul').find('li').each(function(){
            product_ids.push(jQuery(this).data('product-id'));
        });

        jQuery.ajax({
            type: 'post',
            url: orfw_front_data.ajaxurl,
            data: {
                action: 'orfwPopupSubmit',
                order_id: order_id,
                product_ids: product_ids
            },
            beforeSend() {
                console.log(order_id);
                console.log(product_ids);
            },
            dataType: 'json',
            success: function(response) {
                jQuery('#orfw_popup').remove();
            },
        });
    });
});
