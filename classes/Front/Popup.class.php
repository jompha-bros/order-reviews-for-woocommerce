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
        add_action( 'wp_head',                        array( $this, 'colorVariables' ), 0 );

        add_action( 'wp_footer',                      array( $this, 'view') );
        add_action( 'wp_ajax_orfwPopupSubmit',        array( $this, 'orfwPopupSubmit' ));
        add_action( 'wp_ajax_nopriv_orfwPopupSubmit', array( $this, 'orfwPopupSubmit' ));
    }

    /**
    * Handle output of banner
    *
    * @return void
    */
    public function colorVariables()
    {   
        // if ( 'yes' !== $this->enabled ) 
        //     return;
    ?>
    <style>
    :root {
        --orfw-template-header-background-color: <?php echo wp_kses( get_option( 'orfw_template_header_background_color', '#f4b248' ), array() ); ?>;
        --orfw-template-body-background-color: <?php echo wp_kses( get_option( 'orfw_template_body_background_color', '#ffffff' ), array() ); ?>;
        --orfw-template-header-text-color: <?php echo wp_kses( get_option( 'orfw_template_header_text_color', '#442a00' ), array() ); ?>;
        --orfw-template-header-highlighted-text-color: <?php echo wp_kses( get_option( 'orfw_template_header_highlight_text_color', '#ffffff' ), array() ); ?>;
        --orfw-template-body-text-color: <?php echo wp_kses( get_option( 'orfw_template_body_text_color', '#442a00' ), array() ); ?>;
        --orfw-template-submit-background-color: <?php echo wp_kses( get_option( 'orfw_template_submit_background_color', '#f4b248' ), array() ); ?>;
        --orfw-template-submit-text-color: <?php echo wp_kses( get_option( 'orfw_template_submit_text_color', '#ffffff' ), array() ); ?>;
        --orfw-template-small-text-color: <?php echo wp_kses( get_option( 'orfw_template_small_text_color', '#888888' ), array() ); ?>;
        --orfw-template-skip-text-color: <?php echo wp_kses( get_option( 'orfw_template_skip_text_color', '#f4b248' ), array() ); ?>;
        /* --orfw-sri-background-color: <?php echo wp_kses( get_option( 'orfw_sri_background_color', '#bcbad0' ), array() ); ?>;
        --orfw-sri-text-color: <?php echo wp_kses( get_option( 'orfw_sri_text_color', '#bcbad0' ), array() ); ?>;
        --orfw-sri-link-color: <?php echo wp_kses( get_option( 'orfw_sri_link_color', '#bcbad0' ), array() ); ?>;
        --orfw-sri-font-size: <?php echo wp_kses( get_option( 'orfw_sri_font_size', '#bcbad0' ), array() ); ?>;
        --orfw-sri-font-style: <?php echo wp_kses( get_option( 'orfw_sri_font_style', '#bcbad0' ), array() ); ?>; */
    }
    </style>
    <?php
    }

    public function orfwPopupSubmit()
    {
        $orderID    = intval( $_POST['order_id'] );
        $productIDs = $_POST['product_ids']; // Need to be sanitized the ids.

        if ( empty( $orderID ) || empty( $productIDs ) )
            return;

        update_post_meta( $orderID, 'orfw_products', $productIDs );

        $review    = $_POST['review'];
        $rating    = $_POST['rating'];
        $customer  = wp_get_current_user();
        $reviewIds = array();

        foreach ( $productIDs as $productId )
        {
            if ( comments_open( $productId ) ) 
            {
                $reviewData = array(
                    'comment_post_ID'      => $productId,
                    'comment_type'         => 'review',
                    'comment_content'      => $review,
                    'comment_parent'       => 0,
                    'comment_date'         => date('Y-m-d H:i:s'),
                    'user_id'              => $customer->ID,
                    'comment_author'       => $customer->user_login,
                    'comment_author_email' => $customer->user_email,
                    'comment_author_url'   => $customer->user_url,
                    'comment_meta'         => array(
                        'rating'            => $rating,
                        'orfw_order_id'     => $orderID,
                    ),
                    'comment_approved'     => 1,
                );
                
                $review_id = wp_insert_comment( $reviewData );

                if ( ! is_wp_error( $review_id ) ) 
                {   
                    $reviewIds[] = $review_id;
                }

            }
        }

        echo wp_json_encode( $reviewIds );
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
    
        return $previousOrder[0];
    }

    public function checkOrder ()
    {
        $orders = $this->hasOrdered();
        return $orders;
        //return $getIds;
    }

    public function view()
    {   
        if ( isset($_COOKIE['orfw-template-interval-delay']) )
            return;
        
        $this->orderData = wc_get_order( $this->checkOrder()->ID );

        //var_dump($this->orderData);

        $completedDate = strtotime($this->orderData->date_completed);
        $showAfterHrs = get_option( 'template_show_after_hours', 5 );

        $timeToShow = strtotime('+'.$showAfterHrs.' hours', $completedDate);

        if( $timeToShow > current_time( 'timestamp' ) )
            return;
      
        include_once ORFW_RENDER_FRONT . '/markup/popup-design-1.php';
    }
}
