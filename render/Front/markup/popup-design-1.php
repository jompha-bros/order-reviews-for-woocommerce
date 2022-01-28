<div id="orfw_popup" class="popup-one">
    <div class="orfw_popup_inner">
        <div class="orfw_popup_header">
            
            <div class="orfw_popup_order_info">
                <h3><?php echo esc_html( 'Your Last Order', 'order-reviews-for-woocommerce' ); ?></h3>
                <h1 data-order-id="<?php echo esc_html( $this->orderData->ID ); ?>" id="order-id"><?php echo esc_html( $this->orderData->ID ); ?></h1>
                <p><?php echo esc_html( $this->orderData->post_date ); ?></p>
            </div>
            
            <div class="orfw_popup_order_products glide">
                <div data-glide-el="track" class="glide__track">
                    <ul class="glide__slides">
                        <?php
                        $order = wc_get_order( $this->orderData->ID );

                        foreach ( $order->get_items() as $item )
                        {
                            $product = $item->get_product();
                        ?>

                        <li class="glide__slide product" data-product-id="<?php echo esc_attr( $product->get_id() ); ?>">
                            <?php 
                            echo wp_kses( 
                                $product->get_image('thumbnail'), 
                                array(
                                    'img' => array(
                                        'src' => array()
                                    )
                                ) 
                            );
                            ?>
                        </li>

                        <?php 
                        } 
                        ?>
                    </ul>
                </div>
            </div>

        </div>

        <div class="orfw_popup_footer">
            <h2><?php echo esc_html( 'Rate the order', 'order-reviews-for-woocommerce' ); ?></h2>

            <div class="orfw_popup_rating">
                
                <div class="feedback">
                    <div class="rating">
                        <input type="radio" name="rating" id="rating-5" value="5">
                        <label for="rating-5"></label>
                        <input type="radio" name="rating" id="rating-4" value="4">
                        <label for="rating-4"></label>
                        <input type="radio" name="rating" id="rating-3" value="3">
                        <label for="rating-3"></label>
                        <input type="radio" name="rating" id="rating-2" value="2">
                        <label for="rating-2"></label>
                        <input type="radio" name="rating" id="rating-1" value="1">
                        <label for="rating-1"></label>
                        <div class="emoji-wrapper">
                            <div class="emoji">
                                <img class="rating-0" src="<?php echo esc_url(ORFW_RESOURCES . '/images/rating-0.png') ?>">
                                <img class="rating-1" src="<?php echo esc_url(ORFW_RESOURCES . '/images/rating-1.png') ?>">
                                <img class="rating-2" src="<?php echo esc_url(ORFW_RESOURCES . '/images/rating-2.png') ?>">
                                <img class="rating-3" src="<?php echo esc_url(ORFW_RESOURCES . '/images/rating-3.png') ?>">
                                <img class="rating-4" src="<?php echo esc_url(ORFW_RESOURCES . '/images/rating-4.png') ?>">
                                <img class="rating-5" src="<?php echo esc_url(ORFW_RESOURCES . '/images/rating-5.png') ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <textarea name="rating_comment " id="orfw_popup_comment" cols="20" rows="5" placeholder="Write Feedback"></textarea>

            </div>

            <button type="submit" id="onPopupSubmit"><?php echo esc_html( 'Submit', 'order-reviews-for-woocommerce' ); ?></button>
            
            <h4 class="disclaimer"><?php echo esc_html( 'Please provide your honest feedback!', 'order-reviews-for-woocommerce' ); ?></h4>

            <a id="orfw_popup_skip" href=""><?php echo esc_html( 'Skip', 'order-reviews-for-woocommerce' ); ?></a>
        </div>

    </div>
</div>