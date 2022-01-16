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
        add_action( 'woocommerce_review_after_comment_text', array( $this, 'afterBal' ) );
    }

    public function afterBal( $comment )
    {
        $commentID = intval($comment->comment_ID);
        $productID = intval($comment->comment_post_ID);
        $oReviewID = get_comment_meta( $commentID, 'orfw_review_id', true );
        $oProducts = get_post_meta( $oReviewID, 'orfw_products', true );

        $productKey = array_search( $productID, $oProducts );

        if ( $productKey )
            unset( $oProducts[ $productKey ] );

        $i  = 0;
        $t  = count($oProducts);
        $xs = array();
        
        if (! $oProducts || $t == 0)
            return;

        foreach ($oProducts as $oProductID)
        {
            $i++;

            if ($i > 3)
                break;
            
            $oProduct = \wc_get_product($oProductID);
            $xs[$i]  = array(
                'id'   => $oProduct->get_id(),
                'name' => $oProduct->get_name(),
                'url'  => $oProduct->get_permalink(),
            );

            shuffle($xs);

            // 'This user bought %s, %s, %s and %s other items.'
        }
    ?>
    <div class="orfw-review-des">
    <?php
    switch ($t)
    {
        case 1:
            echo sprintf(
                __('This user also bought %s.', 'order-review-for-woocommerce'),
                '<a href="' . $xs[1]['url'] . '" target="_blank">' . $xs[1]['name'] . '</a>'
            );
            break;
        
        case 2:
            echo sprintf(
                __('This user also bought %s and %s.', 'order-review-for-woocommerce'),
                '<a href="' . $xs[1]['url'] . '" target="_blank">' . $xs[1]['name'] . '</a>',
                '<a href="' . $xs[2]['url'] . '" target="_blank">' . $xs[2]['name'] . '</a>'
            );
            break;
        
        case 3:
            echo sprintf(
                __('This user also bought %s, %s, and %s.', 'order-review-for-woocommerce'),
                '<a href="' . $xs[1]['url'] . '" target="_blank">' . $xs[1]['name'] . '</a>',
                '<a href="' . $xs[2]['url'] . '" target="_blank">' . $xs[2]['name'] . '</a>',
                '<a href="' . $xs[3]['url'] . '" target="_blank">' . $xs[3]['name'] . '</a>'
            );
            break;
        
        default:
            echo sprintf(
                __('This user also bought %s, %s, %s and %s other items.', 'order-review-for-woocommerce'),
                '<a href="' . $xs[1]['url'] . '" target="_blank">' . $xs[1]['name'] . '</a>',
                '<a href="' . $xs[2]['url'] . '" target="_blank">' . $xs[2]['name'] . '</a>',
                '<a href="' . $xs[3]['url'] . '" target="_blank">' . $xs[3]['name'] . '</a>',
                ($t - 3)
            );
            break;
    }
    ?>
    </div>
    <?php
    }
}
