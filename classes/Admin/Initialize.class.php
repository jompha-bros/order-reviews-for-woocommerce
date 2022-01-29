<?php
namespace ORFW\Admin;

class Initialize
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
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ), 10, 1 );
    }


    public function enqueue( $screen )
    {
        $this->styles($screen);
        $this->scripts($screen);
    }

    private function styles( $screen )
    {
        wp_enqueue_style( 'orfw' );

        if ( 'woocommerce_page_orfw-settings' === $screen )
        {
            wp_enqueue_style( 'jompha-admin-core' );
            wp_enqueue_style( 'jompha-icons' );
            wp_enqueue_style( 'orfw-admin' );
        }
    }

    private function scripts( $screen )
    {   
        if ( 'woocommerce_page_orfw-settings' === $screen )
        {
            wp_enqueue_script( 'orfw-admin' );
        }
    }
}
