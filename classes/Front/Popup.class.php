<?php
namespace ORFW\Front;

class Popup
{
    public static $instance;
    public $orderID = null;
    public $order;

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

    private function __construct()
    {
        if ( !$this->hasOrdered() )
            return;

        add_action( 'wp_head',                           array( $this, 'colorVariables' ), 0 );
        add_action( 'wp_footer',                         array( $this, 'view') );
        add_action( 'wp_ajax_orfw_review_submit',        array( $this, 'submitReview' ));
        add_action( 'wp_ajax_nopriv_orfw_review_submit', array( $this, 'submitReview' ));
    }

    public function hasOrdered()
    {
        $lastOrder = get_posts( array(
            'numberposts' => 1,
            'meta_key'    => '_customer_user',
            'meta_value'  => get_current_user_id(),
            'order'       => 'DESC',
            'post_type'   => 'shop_order',
            'post_status' => 'wc-completed',
            'fields'      => 'ids',
            'meta_query'  => array(
                'relation' => 'AND',
                array(
                    'relation' => 'OR',
                    array(
                        'key'     => 'orfw_reviewed',
                        'value'   => true,
                        'compare' => '!=',
                    ),
                    array(
                        'key'     => 'orfw_reviewed',
                        'compare' => 'NOT EXISTS',
                    )
                ),
                array(
                    'key'     => 'orfw_is_order',
                    'value'   => true,
                )
            ),
        ) );

        if ( !count($lastOrder) )
        {
            $lastOrder = get_posts( array(
                'numberposts' => 1,
                'meta_key'    => '_customer_user',
                'meta_value'  => get_current_user_id(),
                'order'       => 'DESC',
                'post_type'   => 'shop_order',
                'post_status' => 'wc-completed',
                'fields'      => 'ids',
                'meta_query'  => array(
                    'relation' => 'OR',
                    array(
                        'key'     => 'orfw_reviewed',
                        'value'   => true,
                        'compare' => '!=',
                    ),
                    array(
                        'key'     => 'orfw_reviewed',
                        'compare' => 'NOT EXISTS',
                    ),
                ),
                'date_query' => array(
                    array(
                        'after'     => date('Y-m-d', strtotime('-3 days')),  
                        'inclusive' => true,
                    ),
                ),
            ) );
        }

        if ( !isset($lastOrder[0]) )
            return false;
    
        $this->orderID = $lastOrder[0];
        $this->order   = wc_get_order( $this->orderID );

        return true;
    }

    public function view()
    {   
        if ( $this->isWaitPeriod() || $this->isAgainPeriod() )
            return;
      
        include_once ORFW_RENDER_FRONT . '/markup/popup-design-1.php';
    }

    private function isWaitPeriod()
    {
        return ( strtotime( '+' . get_option( 'orfw_template_wait_period', 3 ) . ' hours', strtotime( $this->order->date_completed ) ) > current_time( 'timestamp' ) ) ? true : false;
    }

    private function isAgainPeriod()
    {
        return ( isset($_COOKIE['orfw-template-again-period']) ) ? true : false;
    }

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

    public function submitReview()
    {
        $orderID    = intval( $_POST['order_id'] );
        $productIDs = $_POST['product_ids']; // Need to be sanitized the ids.

        if ( empty( $orderID ) || empty( $productIDs ) )
            return;

        update_post_meta( $orderID, 'orfw_products', $productIDs );

        $review    = $_POST['review'];
        $rating    = $_POST['rating'];
        $customer  = wp_get_current_user();
        $reviewIDs = array();

        foreach ( $productIDs as $productID )
        {
            if ( comments_open( $productID ) )
            {
                $reviewID = wp_insert_comment( array(
                    'comment_post_ID'      => $productID,
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
                ) );

                if ( ! is_wp_error( $reviewID ) ) 
                    $reviewIDs[] = $reviewID;
            }
        }

        update_post_meta( $orderID, 'orfw_reviewed', true );

        echo wp_json_encode( $reviewIDs );
        wp_die();
    }
}
