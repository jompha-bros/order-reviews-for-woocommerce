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


    public function enqueue($currentScreen)
    {
        $this->styles($currentScreen);
        $this->scripts($currentScreen);
    }

    private function styles($currentScreen)
    {
        wp_enqueue_style( 'orfw' );

        if( 'woocommerce_page_orfw-options' === $currentScreen )
        {
            wp_enqueue_style( 'jompha-admin-core' );
            wp_enqueue_style( 'jompha-icons' );
            wp_enqueue_style( 'orfw-admin' );
        }
    }

    private function scripts($currentScreen)
    {   
        if( 'woocommerce_page_orfw-options' === $currentScreen )
        {
            wp_enqueue_script( 'orfw-admin' );
        }
    }
}
