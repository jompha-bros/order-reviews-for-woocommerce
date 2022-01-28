jQuery(document).ready(function() 
{
    'use strict';

    if ( !jQuery('#orfw_popup').length )
        return;

    const orfwGlide = new Glide('.orfw_popup_order_products', {
        type: 'carousel',
        autoplay: 1,
        animationDuration: 3000,
        animationTimingFunc: 'linear',
        perView: 3.5,
        focusAt: 0,
    });

    orfwGlide.mount();



    var orfwPopup = jQuery('#orfw_popup'),
        ratingStars = jQuery('#orfw_popup .feedback input'),
        feedback = jQuery('#orfw_popup #orfw_popup_comment'),
        submitButton = jQuery('#orfw-template-submit-button'),
        orfwFrequencyCount = ( getCookie('orfw-template-view-frequency') == null ) ? 0 : parseInt( getCookie('orfw-template-view-frequency') );

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
        if ( !jQuery('#orfw_popup .feedback input:checked').length )
        {
            jQuery('.orfw-popup-error-wrapper')
                .find('.orfw-popup-error-text')
                    .text(orfw_data.text_rate_order)
            .end()
            .show();
            return;
        }
        
        var orfwOrderId          = jQuery('#order-id').data('order-id'),
            orfwProductIds       = [24, 33, 16],
            orfwFeedback         = feedback.val(),
            orfwRating           = parseInt(jQuery('#orfw_popup .feedback input:checked').val()),
            orfwForceFeedback    = (orfw_data.template_force_feedback == 'yes') ? true : false,
            orfwForceBadFeedback = (orfw_data.template_force_bad_feedback == 'yes') ? true : false;

        if (orfwForceFeedback && !orfwFeedback.length)
        {
            jQuery('.orfw-popup-error-wrapper')
                .find('.orfw-popup-error-text')
                    .text(orfw_data.text_write_feedback)
            .end()
            .show();
            return;
        }

        if (orfwForceBadFeedback && jQuery('#orfw_popup .feedback input:checked').val() < 4 && !orfwFeedback.length)
        {
            jQuery('.orfw-popup-error-wrapper')
                .find('.orfw-popup-error-text')
                    .text(orfw_data.text_write_feedback)
            .end()
            .show();
            return;
        }
        
        jQuery('.orfw_popup_order_products').find('ul').find('li').each(function()
        {
            orfwProductIds.push(jQuery(this).data('product-id'));
        });

        jQuery.ajax({
            type: 'post',
            url: orfw_data.ajaxurl,
            data: {
                action: 'orfwPopupSubmit',
                order_id: orfwOrderId,
                product_ids: orfwProductIds,
                review: orfwFeedback,
                rating: orfwRating,
            },
            beforeSend()
            {
                jQuery('#orfw-popup-submit-save-icon').addClass('dashicons dashicons-arrow-right-alt2 orfw-horizontal-bounce').attr('disabled', true);
            },
            dataType: 'json',
            success: function(response)
            {
                console.log(response);
            },
            complete: function()
            {
                jQuery('#orfw-popup-submit-save-icon').removeClass('dashicons dashicons-arrow-right-alt2 orfw-horizontal-bounce');
            }
        });
    });

    if ( orfwFrequencyCount <= ( orfw_data.template_view_frequency == '' ? 2 : parseInt( orfw_data.template_view_frequency ) ) )
    {
        orfwPopup.removeClass('hide');
    }

    jQuery(document).on('click', '#orfw_popup_skip', function (e)
    {   
        e.preventDefault();

        var orfwPopupContainer = jQuery('#orfw_popup');
        orfwPopupContainer.fadeOut();

        var orfwAgainPeriod = parseInt(orfw_data.template_again_period);
        setCookie( 'orfw-template-again-period', 'yes', (orfwAgainPeriod * 60 * 60) );

        setCookie( 'orfw-template-view-frequency', (orfwFrequencyCount + 1), ( 30 * 24 * 60 * 60 ) );
    });
});
