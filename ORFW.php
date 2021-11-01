<?php
/**
 * ORFW class
 *
 * @class ORFW The class that holds the entire plugin
 */
final class ORFW
{
    public static $instance;

    /**
     * Plugin version
     *
     * @var string
     */
    public $version = '1.0.0';

    /**
     * Holds various class instances
     *
     * @var array
     */
    private $container = array();

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
     * Constructor for the ORFW class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     */
    public function __construct()
    {
        $this->defineConstants();

        register_activation_hook( __FILE__,   array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

        add_action( 'plugins_loaded',   array( $this, 'bootSystem' ) );
        add_action( 'plugins_loaded',   array( $this, 'run' ) );
        add_action( 'activated_plugin', array($this, 'activationRedirect') );

        add_filter( 'plugin_action_links_' . plugin_basename(__DIR__) . '/jompha-starter-plugin.php', array( $this, 'settingLink' ) );
        add_filter( 'plugin_row_meta', array( $this, 'helpLinks' ), 10, 2);
    }

    /**
     * Define the constants
     * @return void
     */
    public function defineConstants()
    {
        define( 'ORFW_VERSION',       $this->version );
        define( 'ORFW_FILE',          __FILE__ );
        define( 'ORFW_PATH',          dirname( ORFW_FILE ) );
        define( 'ORFW_CLASSES',       ORFW_PATH . '/classes' );
        define( 'ORFW_ADMIN_CLASSES', ORFW_CLASSES . '/Admin' );
        define( 'ORFW_FRONT_CLASSES', ORFW_CLASSES . '/Front' );
        define( 'ORFW_URL',           plugins_url( '', ORFW_FILE ) );
        define( 'ORFW_RESOURCES',     ORFW_URL . '/resources' );
        define( 'ORFW_RENDER',        ORFW_PATH . '/render' );
        define( 'ORFW_RENDER_FRONT',        ORFW_RENDER . '/Front' );
    }

    /**
     * Boots System
     */
    public function bootSystem()
    {
        if ( !class_exists('woocommerce') )
        {
            add_action( 'admin_notices',         array( $this, 'requiredWoocommerce' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'noticeScripts' ) );
            return;
        }
    }

    /**
     * Load the plugin after all plugis are loaded
     *
     * @return void
     */
    public function run()
    {
        if ( !class_exists('woocommerce') )
            return;
            
        $this->includes();
        
        add_action( 'init', array( $this, 'init_classes' ) );
        add_action( 'init', array( $this, 'localization_setup' ) );
    }

    /**
     * Include the required files
     *
     * @return void
     */
    public function includes()
    {
        include_once ORFW_ADMIN_CLASSES . '/Initialize.class.php';
        include_once ORFW_ADMIN_CLASSES . '/Setting.class.php';

        include_once ORFW_FRONT_CLASSES . '/Initialize.class.php';
        include_once ORFW_FRONT_CLASSES . '/Popup.class.php';

        include_once ORFW_CLASSES       . '/Resources.class.php';
    }

    /**
     * Instantiate the required classes
     *
     * @return void
     */
    public function init_classes()
    {
        if ( $this->is_request( 'admin' ) )
        {
            \ORFW\Admin\Initialize::getInstance();
            \ORFW\Admin\Setting::getInstance();
        }

        if ( $this->is_request( 'front' ) )
        {
            \ORFW\Front\Initialize::getInstance();
            \ORFW\Front\Popup::getInstance();
        }

        \ORFW\Resources::getInstance();
    }

    /**
     * Initialize plugin for localization
     *
     * @uses load_plugin_textdomain()
     */
    public function localization_setup()
    {
        load_plugin_textdomain('jompha-starter-plugin', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

    /**
     * Placeholder for activation function
     *
     * Nothing being called here yet.
     */
    public function activate()
    {
        $installed = get_option( 'orfw_installed' );

        if ( !$installed )
            update_option( 'orfw_installed', time() );

        update_option( 'orfw_version', ORFW_VERSION );
    }

    /**
     * Placeholder for deactivation function
     *
     * Nothing being called here yet.
     */
    public function deactivate() {}

    /**
     * What type of request is this?
     *
     * @param  string $type admin, ajax, cron or front.
     *
     * @return bool
     */
    private function is_request( $type )
    {
        switch ( $type )
        {
            case 'admin' :
                return is_admin();

            case 'ajax' :
                return defined( 'DOING_AJAX' );

            case 'rest' :
                return defined( 'REST_REQUEST' );

            case 'cron' :
                return defined( 'DOING_CRON' );

            case 'front' :
                return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
        }
    }

    /**
     * Enqueue scripts for notice
     */
    public function noticeScripts()
    {   
        wp_enqueue_style( 'orfw-admin-notice', ORFW_RESOURCES . '/css/admin-notice.css', false, filemtime( ORFW_PATH . '/resources/css/admin-notice.css' ) );
    }

    function requiredWoocommerce()
    {
        if ( !class_exists('woocommerce') )
        {
            echo '<div class="orfw-plugin-required-notice notice notice-warning">
                <div class="orfw-admin-notice-content">
                <h2>ORFW Required dependency.</h2>
                <p>Please ensure you have the <strong>WooCommerce</strong> plugin installed and activated.</p>
                </div>
            </div>';
        }
    }

    /**
     * Redirect to plugin page on activation
     *
     */
    public function activationRedirect( $plugin ) 
    {
        if( plugin_basename(__DIR__) . '/jompha-starter-plugin.php' == $plugin && class_exists('woocommerce') )
            exit( wp_redirect( admin_url( '/admin.php?page=orfw-options' ) ) );
    }

    /**
     * Setting page link in plugin list
     *
     */
    public function settingLink( $links ) 
    {
	    $settingLink = sprintf( "<a href='%s'>%s</a>", esc_url( admin_url( '/admin.php?page=orfw-options' ) ), __( 'Settings','jompha-starter-plugin' ) );
	    $links[] = $settingLink;
	    $links[] = $premiumLink;

	    return $links;
	}

    /**
     * Plugin row links
     *
     */
    public function helpLinks( $links, $plugin )
    {
        if ( plugin_basename(__DIR__) . '/jompha-starter-plugin.php' != $plugin )
            return $links;
        
        $docsLink    = sprintf( "<a href='%s'>%s</a>", esc_url( '//docs.jompha.com/jompha-starter-plugin' ), __( 'Docs','jompha-starter-plugin' ) );
        $supportLink = sprintf( "<a href='%s'>%s</a>", esc_url( '//forum.jompha.com' ), __( 'Community support','jompha-starter-plugin' ) );
        
        $links[] = $docsLink;
        $links[] = $supportLink;
    
        return $links;
    }
}
