<?php
namespace ORFW\Front;

class Popup
{
    public static $instance;
    public $orderData;

    /**
    * Singleton Pattern
    *
    * @return object
    */
    public static function getInstance()
    {
        if ( !self::$instance instanceof self )
            self::$instance = new self();

        return self::$instance;
    }

    /**
    * Constructor
    *
    * @return void
    */
    private function __construct()
    {
        add_action( 'wp_footer',                      array( $this, 'view') );
        add_action( 'wp_ajax_orfwPopupSubmit',        array( $this, 'orfwPopupSubmit' ));
        add_action( 'wp_ajax_nopriv_orfwPopupSubmit', array( $this, 'orfwPopupSubmit' ));
    }

    public function orfwPopupSubmit()
    {
        $orderId    = intval( $_POST['order_id'] );
        $productIds = $_POST['product_ids']; // Need to be sanitized the ids.

        if( empty( $orderId ) && empty( $productIds ) ) return;

        $postID = wp_insert_post(
            array(
                'post_name'      => $orderId,
                'post_status'    => 'publish',
                'post_type'      => 'orfw_post_type',
            )
        );

       if( !$postID ) return;

       update_post_meta( $postID, 'orfw_order_id', $orderId );
       update_post_meta( $postID, 'orfw_products', $productIds );

        // echo wp_json_encode( array( 
        //     'orderId'    => $_POST['order_id'],
        //     'productId'  => $_POST['product_ids'],
        //     'cptID'      => $postID
        // ));

        wp_die();
    }

    public function hasOrdered()
    {
        //https://stackoverflow.com/questions/53050736/check-if-customer-wrote-a-review-for-a-product-in-woocommerce
        //https://stackoverflow.com/questions/38874189/checking-if-customer-has-already-bought-something-in-woocommerce
        //https://developer.wordpress.org/reference/functions/get_posts/
        $previousOrder = get_posts( array(
            'numberposts' => 1,
            'meta_key'    => '_customer_user',
            'meta_value'  => get_current_user_id(),
            'post_type'   => 'shop_order',
            'post_status' => 'wc-completed',
            'fields'      => array('ids'), // 'all', 
            'meta_query'  => array(
                array(
                    'key'   => 'orfw_order',
                    'value' => '1',
                )
            ),
        ) );

        if ( count( $previousOrder ) > 0 )
        {   
            //check order with orfw_order meta
            return $previousOrder[0];
        }
        else
        {
            //check for last 72hrs orders
            $previousOrder = get_posts( array(
                'numberposts' => 1,
                'meta_key'    => '_customer_user',
                'meta_value'  => get_current_user_id(),
                'post_type'   => 'shop_order',
                'post_status' => 'wc-completed',
                'fields'      => array('ids'), // 'all', 
                'date_query' => [
                    [
                        'after'     => '72 hour ago',  
                        'inclusive' => true,
                    ],
                ],
            ) );
            
            return $previousOrder[0];

        }
    
       //return $previousOrder;
    }

    public function checkOrder ()
    {
        $orders = $this->hasOrdered();
        $getIds = array();

        foreach($orders as $order)
        {
            array_push($getIds, $order->ID);
        }

        return $orders;
        //return $getIds;
    }

    public function view()
    {   
        $this->orderData = $this->checkOrder();
        //include_once ORFW_RENDER_FRONT . '/markup/popup-design-1.php';
    }
}
