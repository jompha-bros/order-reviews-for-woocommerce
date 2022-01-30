<?php
/**
 * ORFW class
 *
 * @class ORFW The class that holds the entire plugin
 */
final class ORFW
{
    public static $instance;
    public $version = '1.0.0';
    public $isAnumati;
    public $anumatiObj;
    public $anumatiMessage;
    public $anumatiShowMessage = false;

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

        $anumatiKey   = get_option( 'order-reviews-for-woocommerce_lic_Key',   '' );
        $anumatiEmail = get_option( 'order-reviews-for-woocommerce_lic_email', '' );

        \ORFW\Anumati::addOnDelete(function(){
            delete_option( 'order-reviews-for-woocommerce_lic_Key' );
        });

        if ( \ORFW\Anumati::CheckWPPlugin( $anumatiKey, $anumatiEmail, $this->anumatiMessage, $this->anumatiObj, ORFW_FILE ) )
        {
            $this->isAnumati = true;

            add_action( 'admin_post_' . 'order-reviews-for-woocommerce_el_deactivate_license', array( $this, 'deactivateLicense' ) );

            add_action( 'plugins_loaded', array( $this, 'bootSystem' ) );
            add_action( 'plugins_loaded', array( $this, 'run' ) );

            add_action( 'admin_menu', array( $this, 'mainMenu' ) );
            add_action( 'admin_menu', array( $this, 'activeAdminMenu' ), 99999 );
        }
        else
        {
            $this->isAnumati = false;

            if ( !empty($anumatiKey) && !empty($this->anumatiMessage) )
               $this->anumatiShowMessage = true;
            
            update_option( 'order-reviews-for-woocommerce_lic_Key', '') || add_option( 'order-reviews-for-woocommerce_lic_Key', '' );
            add_action( 'admin_post_' . 'order-reviews-for-woocommerce_el_activate_license', array( $this, 'activateLicense' ) );

            add_action( 'admin_menu', array( $this, 'inactiveMenu' ));
        }

        add_action( 'activated_plugin',      array( $this, 'activationRedirect' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'anumatiScripts' ) );

        add_filter( 'plugin_row_meta', array( $this, 'helpLinks' ), 10, 2 );
        add_filter( 'plugin_action_links_' . plugin_basename(__DIR__) . '/order-reviews-for-woocommerce.php', array( $this, 'settingLink' ) );
    }

    public function mainMenu()
    {
        add_menu_page(
			esc_html( 'Order Reviews for WooCommerce', 'order-reviews-for-woocommerce' ), 
			esc_html( 'ORFW', 'order-reviews-for-woocommerce' ), 
			'manage_woocommerce', 
			'orfw-settings', 
			$this->isAnumati ? array( \ORFW\Admin\Settings::getInstance(), 'renderPage' ) : array( $this, 'LicenseForm' ), 
			'dashicons-feedback', 
			56
		);
    }

    public static function renderPage()
    {

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
        define( 'ORFW_RENDER_FRONT',  ORFW_RENDER . '/Front' );
    }

    /**
     * Boots System
     */
    public function bootSystem()
    {
        if ( !class_exists('woocommerce') )
        {
            add_action( 'admin_notices',         array( $this, 'requiredWoocommerce' ) );
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
        add_action( 'init', array( $this, 'register_new_post_types' ) );

    }

    /**
     * Include the required files
     *
     * @return void
     */
    public function includes()
    {
        include_once ORFW_ADMIN_CLASSES . '/Initialize.class.php';
        include_once ORFW_ADMIN_CLASSES . '/Settings.class.php';
        include_once ORFW_ADMIN_CLASSES . '/Lists.class.php';

        include_once ORFW_FRONT_CLASSES . '/Initialize.class.php';
        include_once ORFW_FRONT_CLASSES . '/Popup.class.php';
        include_once ORFW_FRONT_CLASSES . '/ReviewInfo.class.php';
        include_once ORFW_FRONT_CLASSES . '/Order.class.php';

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
            \ORFW\Admin\Settings::getInstance();
            \ORFW\Admin\Lists::getInstance();
        }

        if ( $this->is_request( 'front' ) )
        {
            \ORFW\Front\Initialize::getInstance();
            \ORFW\Front\Popup::getInstance();
            \ORFW\Front\ReviewInfo::getInstance();
            \ORFW\Front\Order::getInstance();
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
        load_plugin_textdomain('order-reviews-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

    /**
     * Create a non-editable custom post type after plugin activated.
     *
     * @uses load_plugin_textdomain()
     */
    public function register_new_post_types()
    {
        register_post_type( 'orfw_review', array(
            'label'               => esc_html__( 'Order Reviews', 'order-reviews-for-woocommerce' ),
            'description'         => esc_html__( 'Reviews of orders posted by customers.', 'order-reviews-for-woocommerce' ),
            'labels'              => array(
                'name'               => esc_html__( 'Order Reviews', 'order-reviews-for-woocommerce' ),
                'singular_name'      => esc_html__( 'Order Review', 'order-reviews-for-woocommerce' ),
                'menu_name'          => esc_html__( 'Order Reviews', 'order-reviews-for-woocommerce' ),
                'name_admin_bar'     => esc_html__( 'Order Review', 'order-reviews-for-woocommerce' ),
                'parent_item_colon'  => esc_html__( 'Parent Order Review:', 'order-reviews-for-woocommerce' ),
                'all_items'          => esc_html__( 'Reviews', 'order-reviews-for-woocommerce' ),
                'add_new_item'       => esc_html__( 'Add New review', 'order-reviews-for-woocommerce' ),
                'add_new'            => esc_html__( 'Add New review', 'order-reviews-for-woocommerce' ),
                'new_item'           => esc_html__( 'New review', 'order-reviews-for-woocommerce' ),
                'edit_item'          => esc_html__( 'Edit review', 'order-reviews-for-woocommerce' ),
                'update_item'        => esc_html__( 'Update review', 'order-reviews-for-woocommerce' ),
                'view_item'          => esc_html__( 'View review', 'order-reviews-for-woocommerce' ),
                'search_items'       => esc_html__( 'Search review', 'order-reviews-for-woocommerce' ),
                'not_found'          => esc_html__( 'No Reviews found', 'order-reviews-for-woocommerce' ),
                'not_found_in_trash' => esc_html__( 'Not Reviews found in Trash', 'order-reviews-for-woocommerce' ),
            ),
            'supports'            => array( 'title', 'editor', 'author' ),
            'show_in_rest'        => true,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_position'       => 5,
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'capability_type'     => 'post',
            'map_meta_cap'        => true,
            'show_in_menu' => 'orfw-settings'
        ) );
    }

    /**
     * Placeholder for activation function
     *
     * Nothing being called here yet.
     */
    public function activate()
    {
        $installed = get_option( 'orfw_installed' );

        if ( ! $installed )
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
        if ( plugin_basename(__DIR__) . '/order-reviews-for-woocommerce.php' == $plugin && class_exists('woocommerce') )
            exit( wp_redirect( admin_url( '/admin.php?page=orfw-settings' ) ) );
    }

    /**
     * Setting page link in plugin list
     *
     */
    public function settingLink( $links ) 
    {
	    $links[] = sprintf( '<a href="%s">%s</a>', esc_url( admin_url( '/admin.php?page=orfw-settings' ) ), esc_html__( 'Settings','order-reviews-for-woocommerce' ) );

	    return $links;
	}

    /**
     * Plugin row links
     *
     */
    public function helpLinks( $links, $plugin )
    {
        if ( plugin_basename(__DIR__) . '/order-reviews-for-woocommerce.php' != $plugin )
            return $links;
        
        $docsLink    = sprintf( "<a href='%s'>%s</a>", esc_url( '//docs.jompha.com/order-reviews-for-woocommerce' ), esc_html__( 'Docs','order-reviews-for-woocommerce' ) );
        $supportLink = sprintf( "<a href='%s'>%s</a>", esc_url( '//forum.jompha.com' ), esc_html__( 'Community support','order-reviews-for-woocommerce' ) );
        
        $links[] = $docsLink;
        $links[] = $supportLink;
    
        return $links;
    }

    /**
     * Enqueue scripts for license
     */
    public function anumatiScripts()
    {
        wp_enqueue_style( 'orfw-anumati', plugins_url( '/resources/css/anumati.css', ORFW_FILE ) , false, filemtime( ORFW_PATH . '/resources/css/anumati.css' ) );
    }

    /**
     * License menu after activation
     */
    function activeAdminMenu()
    {
		add_submenu_page( 
            'orfw-settings',
            esc_html__( 'License', 'order-reviews-for-woocommerce' ),
            esc_html__( 'License', 'order-reviews-for-woocommerce' ),
            'activate_plugins',
            'order-reviews-for-woocommerce',
            array( $this, 'Activated' )
        );
    }

    /**
     * License menu before activation
     */
    function inactiveMenu()
    {
        add_menu_page(
			esc_html( 'Order Reviews for WooCommerce', 'order-reviews-for-woocommerce' ), 
			esc_html( 'ORFW', 'order-reviews-for-woocommerce' ), 
			'manage_woocommerce', 
			'orfw-settings', 
			$this->isAnumati ? array( \ORFW\Admin\Settings::getInstance(), 'renderPage' ) : array( $this, 'LicenseForm' ), 
			'dashicons-feedback', 
			56
		);

        add_submenu_page( 
            'orfw-settings',
            esc_html__( 'License', 'order-reviews-for-woocommerce' ),
            esc_html__( 'License', 'order-reviews-for-woocommerce' ),
            'activate_plugins',
            'orfw-settings',
            array( $this, 'LicenseForm' ),
            0
        );
    }

    function activateLicense()
    {
        check_admin_referer( 'el-license' );

        $licenseKey   = !empty($_POST['el_license_key']) ? $_POST['el_license_key'] : '';
        $licenseEmail = !empty($_POST['el_license_email']) ? $_POST['el_license_email'] : '';

        update_option( 'order-reviews-for-woocommerce_lic_Key', $licenseKey ) || add_option( 'order-reviews-for-woocommerce_lic_Key', $licenseKey );
        update_option( 'order-reviews-for-woocommerce_lic_email', $licenseEmail ) || add_option( 'order-reviews-for-woocommerce_lic_email', $licenseEmail );

        update_option( '_site_transient_update_plugins', '' );
        wp_safe_redirect( admin_url( 'admin.php?page=' . 'order-reviews-for-woocommerce' ) );
    }

    function deactivateLicense()
    {
        check_admin_referer( 'el-license' );
        $message = '';

        if (\ORFW\Anumati::RemoveLicenseKey( ORFW_FILE, $message ))
        {
            update_option( 'order-reviews-for-woocommerce_lic_Key', '' ) || add_option( 'order-reviews-for-woocommerce_lic_Key', '' );
            update_option( '_site_transient_update_plugins', '' );
        }

        wp_safe_redirect( admin_url( 'admin.php?page=' . 'order-reviews-for-woocommerce' ) );
    }

    function Activated()
    {
    ?>
        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
            <input type="hidden" name="action" value="<?php echo esc_attr( $this->slug ); ?>_el_deactivate_license"/>
            <div class="el-license-container">
                <h3 class="el-license-title"><i class="dashicons-before dashicons-star-filled"></i> <?php echo esc_html__( 'ORFW License Info', 'order-reviews-for-woocommerce' );?> </h3>
                <hr>
                <ul class="el-license-info">
                    <li>
                        <div>
                            <span class="el-license-info-title"><?php echo esc_html__( 'Status', 'order-reviews-for-woocommerce' );?></span>

                            <?php if ( $this->anumatiObj->is_valid ) : ?>
                                <span class="el-license-valid"><?php echo esc_html__( 'Valid', 'order-reviews-for-woocommerce' );?></span>
                            <?php else : ?>
                                <span class="el-license-valid"><?php echo esc_html__( 'Invalid', 'order-reviews-for-woocommerce' );?></span>
                            <?php endif; ?>
                        </div>
                    </li>

                    <li>
                        <div>
                            <span class="el-license-info-title"><?php echo esc_html__( 'License Type', 'order-reviews-for-woocommerce' );?></span>
                            <?php echo esc_html( $this->anumatiObj->license_title ); ?>
                        </div>
                    </li>

                    <li>
                        <div>
                            <span class="el-license-info-title"><?php echo esc_html__( 'License Expired on', 'order-reviews-for-woocommerce' );?></span>
                            <?php echo esc_html( $this->anumatiObj->expire_date );

                            if ( !empty($this->anumatiObj->expire_renew_link) )
                            {
                                ?>
                                <a target="_blank" class="el-blue-btn" href="<?php echo esc_url( $this->anumatiObj->expire_renew_link ); ?>"><?php echo esc_html__('Renew', 'order-reviews-for-woocommerce'); ?></a>
                                <?php
                            }
                            ?>
                        </div>
                    </li>

                    <li>
                        <div>
                            <span class="el-license-info-title"><?php echo esc_html__( 'Support Expired on', 'order-reviews-for-woocommerce' );?></span>
                            <?php
                                echo esc_html( $this->anumatiObj->support_end );
                                if (!empty($this->anumatiObj->support_renew_link))
                                {
                                    ?>
                                    <a target="_blank" class="el-blue-btn" href="<?php echo esc_url( $this->anumatiObj->support_renew_link ); ?>"><?php echo esc_html__('Renew', 'order-reviews-for-woocommerce'); ?></a>
                                    <?php
                                }
                            ?>
                        </div>
                    </li>
                    <li>
                        <div>
                            <span class="el-license-info-title"><?php echo esc_html__( 'Your License Key', 'order-reviews-for-woocommerce' );?></span>
                            <span class="el-license-key"><?php echo esc_html( substr( $this->anumatiObj->license_key, 0, 9 ) . 'XXXXXXXX-XXXXXXXX' . substr($this->anumatiObj->license_key, -9) ); ?></span>
                        </div>
                    </li>
                </ul>
                <div class="el-license-active-btn">
                    <?php wp_nonce_field( 'el-license' ); ?>
                    <?php submit_button( 'Deactivate' ); ?>
                </div>
            </div>
        </form>
    <?php
    }

    function LicenseForm()
    {
    ?>
    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <input type="hidden" name="action" value="<?php echo esc_attr( 'order-reviews-for-woocommerce' ); ?>_el_activate_license">
        <div class="el-license-container">
            <h3 class="el-license-title"><i class="dashicons-before dashicons-star-filled"></i> <?php echo esc_html__( 'ORFW Licensing', 'order-reviews-for-woocommerce' ); ?></h3>
            <hr>
            <?php
            if ( !empty($this->anumatiShowMessage) && !empty($this->anumatiMessage) )
            {
                ?>
                <div class="notice notice-error is-dismissible">
                    <p><?php echo esc_html( $this->anumatiMessage ); ?></p>
                </div>
                <?php
            }
            ?>
            <p><?php echo esc_html__( 'Enter your license key here, to activate the product, and get full feature updates and premium support.', 'order-reviews-for-woocommerce' ); ?></p>
            <ol>
                <li><?php echo esc_html__( 'Write your license key details', 'order-reviews-for-woocommerce' ); ?></li>
                <li><?php echo esc_html__( 'How buyer will get this license key?', 'order-reviews-for-woocommerce' ); ?></li>
                <li><?php echo esc_html__( 'Describe other info about licensing if required', 'order-reviews-for-woocommerce' ); ?></li>
            </ol>
            <div class="el-license-field">
                <label for="el_license_key"><?php echo esc_html__( 'License code', 'order-reviews-for-woocommerce' ); ?></label>
                <input type="text" class="regular-text code" name="el_license_key" size="50" placeholder="<?php echo esc_attr__( 'xxxxxxxx-xxxxxxxx-xxxxxxxx-xxxxxxxx', 'order-reviews-for-woocommerce' ) ?>" required="required">
            </div>
            <div class="el-license-field">
                <label for="el_license_key"><?php echo esc_html__( 'Email Address', 'order-reviews-for-woocommerce' ); ?></label>
                <?php
                    $purchaseEmail = get_option( 'order-reviews-for-woocommerce_lic_email', get_bloginfo( 'admin_email' ));
                ?>
                <input type="text" class="regular-text code" name="el_license_email" size="50" value="<?php echo esc_attr( $purchaseEmail ); ?>"  required="required">
                <div><small><?php echo esc_html__( 'We will send update news of this product on this e-mail address, do not worry, we hate spam too', 'order-reviews-for-woocommerce' ); ?></small></div>
            </div>
            <div class="el-license-active-btn">
                <?php wp_nonce_field( 'el-license' ); ?>
                <?php submit_button( esc_html__('Activate', 'order-reviews-for-woocommerce' ) ); ?>
            </div>
        </div>
    </form>
    <?php
    }
}
