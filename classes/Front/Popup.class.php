<?php
namespace ORFW\Front;

class Popup
{
    public static $instance;

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
      add_action( 'wp_footer', array($this, 'view') );
    }

    public function hasOrdered()
    {   
        //https://stackoverflow.com/questions/53050736/check-if-customer-wrote-a-review-for-a-product-in-woocommerce
        //https://stackoverflow.com/questions/38874189/checking-if-customer-has-already-bought-something-in-woocommerce
        //https://developer.wordpress.org/reference/functions/get_posts/
        $previousOrder = get_posts( array(
            'numberposts' => -1,
            'meta_key'    => '_customer_user',
            'meta_value'  => get_current_user_id(),
            'post_type'   => 'shop_order',
            'post_status' => 'wc-completed',
            'fields'      => array('all', 'ids'),
            'meta_query' => array(
                array(
                    'key'   => 'orfw_order',
                    'value' => '1',
                )
            ),
        ) );

        if ( count( $previousOrder ) > 0 )
        {   
            //check order with orfw_order meta
            return $previousOrder;
        }
        else
        {
            //check for last 72hrs orders
            $previousOrder = get_posts( array(
                'numberposts' => -1,
                'meta_key'    => '_customer_user',
                'meta_value'  => get_current_user_id(),
                'post_type'   => 'shop_order',
                'post_status' => 'wc-completed',
                'fields'      => array('all', 'ids'),
                'date_query' => [
                    [
                        'after'     => '72 hour ago',  
                        'inclusive' => true,
                    ],
                ],
            ) );
            
            return $previousOrder;

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

        return $getIds;
    }



    public function view()
    {   
        var_dump($this->checkOrder());
        //include_once ORFW_RENDER_FRONT . '/markup/popup-design-1.php';
    }


}
