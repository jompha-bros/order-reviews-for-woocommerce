<?php
namespace ORFW\Front;

class ReviewUI
{
    public static $instance;
    private $enabled;
    private $fontSize;
    private $fontStyle;

    public static function getInstance()
    {
        if ( !self::$instance instanceof self )
            self::$instance = new self();
        
        return self::$instance;
    }

    private function __construct()
    {
        add_action( 'woocommerce_review_after_comment_text', array( $this, 'afterReviewText' ) );
        add_action( 'wp_head', array( $this, 'productReviewDisplayCss' ) );

        $this->fontSize  = get_option( 'orfw_sri_font_size', 'medium' );
        $this->fontStyle = get_option( 'orfw_sri_font_style', 'normal' );

        $this->enabled = true;
    }

    public function productReviewDisplayCss()
    {
        if ( !$this->enabled )
            return;

        switch( $this->fontSize )
        {
            case 'large': '2rem';
            case 'small': '1rem';
            default: '1.25rem';
        }

        switch( $this->fontStyle )
        {
            case 'italic': 'italic';
            default: 'normal';
        }

        ?>
            <style>
                :root{
                    --orfw-review-font-style: <?php echo esc_html( $this->fontStyle ); ?>;
                    --orfw-review-font-size: <?php echo esc_html( $this->fontSize ); ?>;
                    --orfw-review-font-color: <?php echo wp_kses( get_option( 'orfw_sri_font_color', '#28303d' ), array() ); ?>;
                    --orfw-review-font-link-color: <?php echo wp_kses( get_option( 'orfw_sri_link_color', '#28303d' ), array() ); ?>;
                    --orfw-review-background: <?php echo wp_kses( get_option( 'orfw_sri_card_bg', 'transparent' ), array() ); ?>;
                }
            </style>

        <?php
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
