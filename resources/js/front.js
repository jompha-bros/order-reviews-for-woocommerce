jQuery(document).ready(function() 
{
    'use strict';

    const orfwGlide = new Glide('.orfw_popup_order_products', {
        type: 'carousel',
        autoplay: 1,
        animationDuration: 3000,
        animationTimingFunc: "linear",
        perView: 3.5,
        focusAt: 0,
    });

    orfwGlide.mount();

    var ratingStars = jQuery('#orfw_popup .feedback input'),
        ratingComment = jQuery('#orfw_popup #orfw_popup_comment');

    ratingStars.on('click', function() 
    {
        ratingComment.fadeIn();
    });

    jQuery('#onPopupSubmit').on('click', function() 
    {
        var order_id      = jQuery('#order-id').data('order-id'),
            product_ids   = [];
            reviewComment = 'This is test review',
            ratingStars   = 4;

        jQuery('.orfw_popup_order_products').find('ul').find('li').each(function()
        {
            product_ids.push(jQuery(this).data('product-id'));
        });

        jQuery.ajax({
            type: 'post',
            url: orfw_front_data.ajaxurl,
            data: {
                action: 'orfwPopupSubmit',
                order_id: order_id,
                product_ids: product_ids,
                review: reviewComment,
                rating: ratingStars,
            },
            beforeSend()
            {
                console.log('Submited review for: ' + order_id);
                jQuery('#onPopupSubmit').text('Submitting..')
            },
            dataType: 'json',
            success: function (response)
            {
                console.log(response);
                //jQuery("#orfw_popup").remove();
            },
        });
    });
});
