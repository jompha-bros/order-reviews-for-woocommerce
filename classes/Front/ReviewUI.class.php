<?php
namespace ORFW\Front;

class ReviewUI
{
    public static $instance;

    public static function getInstance()
    {
        if ( !self::$instance instanceof self )
            self::$instance = new self();
        
        return self::$instance;
    }

    private function __construct()
    {
        add_action( 'woocommerce_review_after_comment_text', array( $this, 'afterReviewText' ) );
    }

    public function afterReviewText( $comment )
    {
        $reviewID = intval($comment->comment_ID);
        $orderID  = get_comment_meta( $reviewID, 'orfw_order_id', true );
        $products = get_post_meta( $orderID, 'orfw_products', true );
        $key      = array_search( intval($comment->comment_post_ID), $products );

        if ( $key )
            unset( $products[ $key ] );

        $i  = 0;
        $t  = count($products);
        $px = array();
        
        if (! $products || $t == 0)
            return;

        foreach ($products as $productID)
        {
            $i++;

            if ($i > 3)
                break;
            
            $product = \wc_get_product($productID);
            $px[$i]  = array(
                'id'   => $product->get_id(),
                'name' => $product->get_name(),
                'url'  => $product->get_permalink(),
            );

            shuffle($px);
        }
    ?>
    <div class="orfw-review-info">
    <?php
        switch ($t)
        {
            case 1:
                echo sprintf(
                    esc_html__('This user also bought %s.', 'order-reviews-for-woocommerce'),
                    '<a href="' . esc_url($px[1]['url']) . '" target="_blank">' . esc_html($px[1]['name']) . '</a>'
                );
                break;
            
            case 2:
                echo sprintf(
                    esc_html__('This user also bought %s and %s.', 'order-reviews-for-woocommerce'),
                    '<a href="' . esc_url($px[1]['url']) . '" target="_blank">' . esc_html($px[1]['name']) . '</a>',
                    '<a href="' . esc_url($px[2]['url']) . '" target="_blank">' . esc_html($px[2]['name']) . '</a>'
                );
                break;
            
            case 3:
                echo sprintf(
                    esc_html__('This user also bought %s, %s, and %s.', 'order-reviews-for-woocommerce'),
                    '<a href="' . esc_url($px[1]['url']) . '" target="_blank">' . esc_html($px[1]['name']) . '</a>',
                    '<a href="' . esc_url($px[2]['url']) . '" target="_blank">' . esc_html($px[2]['name']) . '</a>',
                    '<a href="' . esc_url($px[3]['url']) . '" target="_blank">' . esc_html($px[3]['name']) . '</a>'
                );
                break;
            
            default:
                echo sprintf(
                    esc_html__('This user also bought %s, %s, %s and %s other items.', 'order-reviews-for-woocommerce'),
                    '<a href="' . esc_url($px[1]['url']) . '" target="_blank">' . esc_html($px[1]['name']) . '</a>',
                    '<a href="' . esc_url($px[2]['url']) . '" target="_blank">' . esc_html($px[2]['name']) . '</a>',
                    '<a href="' . esc_url($px[3]['url']) . '" target="_blank">' . esc_html($px[3]['name']) . '</a>',
                    ($t - 3)
                );
                break;
        }
    ?>
    </div>
    <?php
    }
}
