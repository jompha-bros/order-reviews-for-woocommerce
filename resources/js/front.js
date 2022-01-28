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
        feedback = jQuery('#orfw_popup #orfw_popup_comment'),
        submitButton = jQuery('#onPopupSubmit'),
        skipButton = jQuery('#orfw_popup_skip'); 

    ratingStars.on('click', function() 
    {
        feedback.fadeIn();
    });


    //rating
    ratingStars.on('change', function ()
    {
        var orfwRating = jQuery(this).val();
        var orfwFeedback = feedback.val();
        
    });

    //feedback box
    feedback.on('keyup', function ()
    {
        var orfwFeedback = jQuery(this).val();
    });

    //submit button
    submitButton.on('click', function() 
    {
        var orfwOrderId      = jQuery('#order-id').data('order-id'),
            orfwProductIds   = [24, 33, 16],
            orfwFeedback = feedback.val(),
            orfwRating = ratingStars.val();
        
        
        jQuery('.orfw_popup_order_products').find('ul').find('li').each(function()
        {
            orfwProductIds.push(jQuery(this).data('product-id'));
        });

        jQuery.ajax({
            type: 'post',
            url: orfw_front_data.ajaxurl,
            data: {
                action: orfwPopupSubmit,
                order_id: orfwOrderId,
                product_ids: orfwProductIds,
                review: orfwFeedback,
                rating: orfwRating,
            },
            beforeSend()
            {
                console.log('Submited review for: ' + orfwOrderId);
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

    

    //skip button
    jQuery(document).on('click', '#orfw_popup_skip', function ()
    {
        var orfwPopupContainer = jQuery('#orfw_popup');
        

    });
});
