jQuery(document).ready(function() {
    "use strict";

    const orfwGlide = new Glide(".orfw_popup_order_products", {
        type: "carousel",
        autoplay: 1,
        animationDuration: 3000,
        animationTimingFunc: "linear",
        perView: 3.5,
        focusAt: 0,
    });

    orfwGlide.mount();

    var ratingStars = jQuery("#orfw_popup .feedback input"),
        ratingComment = jQuery("#orfw_popup #orfw_popup_comment");

    ratingStars.on("click", function() {
        ratingComment.fadeIn();
    });

    jQuery("#onPopupSubmit").on("click", function() {
        var order_id      = jQuery("#order-id").data("order-id"),
            product_ids = [24, 33, 16, 17],
            reviewComment    = 'This is test review',
            ratingStars      = 4;



        jQuery.ajax({
            type: "post",
            url: orfw_front_data.ajaxurl,
            data: {
                action: "orfwPopupSubmit",
                order_id: order_id,
                product_ids: product_ids,
                review: reviewComment,
                rating: ratingStars,
            },
            beforeSend() {
                console.log('Submited review for: ' + order_id);
            },
            dataType: "json",
            success: function (response) {
                console.log(response);
                //jQuery("#orfw_popup").remove();
            },
        });
    });
});
