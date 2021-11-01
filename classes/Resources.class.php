<?php
namespace ORFW;

class Resources
{
    public static $instance;

    public static function getInstance()
    {
        if ( !self::$instance instanceof self )
            self::$instance = new self();

        return self::$instance;
    }

    /**
     * Singleton Pattern
     *
     * @return object
     */
    private function __construct()
    {
        if ( is_admin() )
            add_action( 'admin_enqueue_scripts', array( $this, 'register' ), 5 );
        else
            add_action( 'wp_enqueue_scripts', array( $this, 'register' ), 5 );
    }

    /**
     * Register our app scripts and styles
     *
     * @return void
     */
    public function register()
    {
        $this->registerScripts( $this->scripts() );
        $this->registerStyles( $this->styles() );
    }

    /**
     * Register scripts
     *
     * @param  array $scripts
     *
     * @return void
     */
    private function registerScripts( $scripts )
    {
        foreach ( $scripts as $handle => $script )
        {
            $deps      = isset( $script['deps'] ) ? $script['deps'] : false;
            $in_footer = isset( $script['in_footer'] ) ? $script['in_footer'] : false;
            $version   = isset( $script['version'] ) ? $script['version'] : ORFW_VERSION;

            wp_register_script( $handle, $script['src'], $deps, $version, $in_footer );
        }
    }

    /**
     * Register styles
     *
     * @param  array $styles
     *
     * @return void
     */
    public function registerStyles( $styles )
    {
        foreach ( $styles as $handle => $style )
        {
            $deps = isset( $style['deps'] ) ? $style['deps'] : false;
            wp_register_style( $handle, $style['src'], $deps, $style['version'] );
        }
    }

    /**
     * Get all registered scripts
     *
     * @return array
     */
    
    public function scripts()
    {
        $scripts = array(
            'orfw-admin' => array(
                'src'       => ORFW_RESOURCES . '/js/admin.js',
                'deps'      => array( 'jquery' ),
                'version'   => filemtime( ORFW_PATH . '/resources/js/admin.js' ),
                'in_footer' => true
            ),
            'glide' => array(
                'src'       => ORFW_RESOURCES . '/js/glide.js',
                'deps'      => array(  ),
                'version'   => filemtime( ORFW_PATH . '/resources/js/glide.js' ),
                'in_footer' => true
            ),
            'orfw-front' => array(
                'src'       => ORFW_RESOURCES . '/js/front.js',
                'deps'      => array( 'jquery' ),
                'version'   => filemtime( ORFW_PATH . '/resources/js/front.js' ),
                'in_footer' => true
            )
        );

        return $scripts;
    }

    /**
     * Get registered styles
     *
     * @return array
     */
    public function styles()
    {
        $styles = array(
            'orfw-admin' => array(
                'src'     => ORFW_RESOURCES . '/css/admin.css',
                'version' => filemtime( ORFW_PATH . '/resources/css/admin.css' ),
            ),
            'orfw-front' => array(
                'src'     => ORFW_RESOURCES . '/css/front.css',
                'version' => filemtime( ORFW_PATH . '/resources/css/front.css' ),
            ),
            'orfw-icons' => array(
                'src'     => ORFW_RESOURCES . '/css/icons.css',
                'version' => filemtime( ORFW_PATH . '/resources/css/icons.css' ),
            ),
        );

        return $styles;
    }
}
