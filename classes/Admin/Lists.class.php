<?php
namespace ORFW\Admin;

class Lists
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
        add_filter( 'manage_orfw_review_posts_columns',        array( $this, 'addReviewColumns'));
        add_action( 'manage_orfw_review_posts_custom_column',  array( $this, 'customFieldColumnContent'), 10, 2);
        add_action( 'post_row_actions',                        array( $this, 'removeAllRowActions'), 10, 2);
    }

    public function addReviewColumns( $columns )  
    {
        
        $columns = array(
            'cb'            => '<input type="checkbox" />', // Render a checkbox instead of text
            'order_id'      => __( 'Order Id',      'order-reviews-for-woocommerce' ),
            'review_title'  => __( 'Review Title',  'order-reviews-for-woocommerce' ),
            'review_rating' => __( 'Review Rating', 'order-reviews-for-woocommerce' ),
            'review_user'   => __( 'Review User',   'order-reviews-for-woocommerce' ),
            'review_time'   => __( 'Review Time',   'order-reviews-for-woocommerce' )
        );

        return $columns;

    }

    public function customFieldColumnContent( $column, $post_id )
    {

        echo '</select>';
        switch ($column) {
            case 'order_id' :
                $post_meta = get_post_meta( $post_id );
                echo __('Order Id');
                break;
            case 'review_title' :
                echo __('Review Title');
                break;
            case 'review_rating' :
                echo __('Review Rating');
                break;
            case 'review_user' :
                echo __('Review User');
                break;
                break;
            case 'review_time' :
                echo __('Review Time');
                break;
        }

    }

    public function removeAllRowActions( $actions, $post )
    {

        global $current_screen;

        if ($current_screen->post_type != 'orfw_review') return $actions;

        unset($actions['view']);
        unset($actions['inline hide-if-no-js']);
        unset($actions['edit']);
        unset($actions['trash']);

        return $actions;

    }

}
