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
            'cb'            => '<input type="checkbox">',
            'order_id'      => esc_html__( 'Order ID',      'order-reviews-for-woocommerce' ),
            'review_title'  => esc_html__( 'Feedback',  'order-reviews-for-woocommerce' ),
            'review_rating' => esc_html__( 'Rating', 'order-reviews-for-woocommerce' ),
            'review_user'   => esc_html__( 'Customer',   'order-reviews-for-woocommerce' ),
            'review_time'   => esc_html__( 'Time',   'order-reviews-for-woocommerce' )
        );

        return $columns;
    }

    public function customFieldColumnContent( $column, $reviewID )
    {
        // echo '</select>'; ?????

        $orderID = get_post_meta( $reviewID, 'orfw_order_id', true );
        $review  = get_post( $reviewID );
        $order   = wc_get_order( $orderID );

        switch ( $column )
        {
            case 'order_id':
                echo sprintf( '<a href="%1$s"><strong>%2$s</strong></a>',
                    esc_url( get_edit_post_link( $reviewID ) ),
                    esc_html__( "#{$orderID} {$order->get_customer_first_name()} {$order->get_customer_last_name()}", 'order-reviews-for-woocommerce' )
                );
                break;

            case 'review_title':
                echo esc_html( $review->post_content );
                break;

            case 'review_rating':
                $stars = intval(get_post_meta( $reviewID, 'orfw_rating', true ));
                for ( $i = 1; $i <= 5; $i++ )
                { 
                    echo ($i > $stars) ? '<span class="orfw-star-empty"></span>' : '<span class="orfw-star"></span>';
                }
                break;

            case 'review_user':
                echo sprintf( '<a href="%1$s">%2$s</a>',
                    get_edit_user_link( $order->get_customer_id() ),
                    esc_html( "{$order->get_customer_first_name()} {$order->get_customer_last_name()}", 'order-reviews-for-woocommerce' )
                );
                break;
                
            case 'review_time':
                echo esc_html__( date('M j Y, h:i A', strtotime($review->post_date)), 'order-reviews-for-woocommerce' );
                break;
        }
    }

    public function removeAllRowActions( $actions, $post )
    {
        global $current_screen;

        if ( 'orfw_review' != $current_screen->post_type )
            return $actions;

        unset( $actions['view'] );
        unset( $actions['inline hide-if-no-js'] );
        unset( $actions['edit'] );
        unset( $actions['trash'] );

        return $actions;
    }
}
