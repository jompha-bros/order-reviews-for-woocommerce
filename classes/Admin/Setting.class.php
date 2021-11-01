<?php
namespace ORFW\Admin;

class Setting
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
		add_action( 'admin_menu', array( $this, 'orfwOptionsMenu' ) );
		add_action( 'admin_init', array( $this, 'orfwOptionsSections' ) );
		add_action( 'admin_init', array( $this, 'orfwOptionFields' ) );
    }

	public function orfwOptionsMenu() 
	{
		$parent_slug = 'woocommerce';
		$page_title = __( 'Jompha Starter Plugin Settings','jompha-starter-plugin' );
		$menu_title = __( 'ORFW PLUGIN','jompha-starter-plugin' );
		$capability = 'manage_woocommerce';
		$slug       = 'orfw-options';
		$callback   = array( $this, 'orfwOptionsRender' );
		//add_submenu_page( $page_title, $menu_title, $capability, $slug, $callback );

		add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $slug, $callback, 0 );
	}

	public function orfwOptionsRender() { ?>
        <div class="wrap joms-settings">
            <div class="joms-fields">
				<h1><?php echo __( 'Jompha Starter Plugin','jompha-starter-plugin' ); ?></h1>
				<p><?php echo  __( 'Subtitle here','jompha-starter-plugin' ); ?></p>
				<form method="POST" action="options.php">
					<?php 
					wp_nonce_field('update-options');
					
					settings_fields( 'orfw-options' ); //option group , should match with register_setting('orfw-options') 
					do_settings_sections( 'orfw-options' ); // setting page slug 'orfw-options'
					submit_button();
					?>
				</form>
			</div>
			<div class="joms-recommendations">
				<h2>Recommended Plugins</h2>
				<a href="https://wordpress.org/plugins/ultimate-coupon-for-woocommerce" target="_blank">
					<img src="<?php echo ORFW_RESOURCES; ?>/images/orfw-banner.png" alt="">
				</a>
			</div>
        </div> <?php
	}

	public function orfwOptionsSections() {
		add_settings_section( 'orfwOptions_section', 'Section 1', array(), 'orfw-options' ); //'orfw-options' is page slug
		add_settings_section( 'orfwOptions_section2', 'Section 2', array(), 'orfw-options' ); //'orfw-options' is page slug

	}

	public function orfwOptionFields() {
		$fields = array(
			array(
				'label'       => __( 'Name','jompha-starter-plugin' ),
				'id'          => 'orfwOptions_name',
				'type'        => 'text',
				'section'     => 'orfwOptions_section',
				'placeholder' => __( 'Name','jompha-starter-plugin' ),
			),
			
			array(
				'label'   => __( 'Color','jompha-starter-plugin' ),
				'id'      => 'orfwOptions_expirydate',
				'type'    => 'Color',
				'section' => 'orfwOptions_section',
				'description' => 'Select your color for the timeline.',
			),

			array(
				'label'   => __( 'Order Details','jompha-starter-plugin' ),
				'id'      => 'orfwOptions_toggle',
				'type'    => 'toggle',
				'section' => 'orfwOptions_section',
				'description' => 'Enable/Disable Order Details.',
			),

			array(
				'label'   => __( 'Select Options','jompha-starter-plugin' ),
				'id'      => 'orfwOptions_selects',
				'type'    => 'select',
				'section' => 'orfwOptions_section',
				'options' => array(
					__( 'Select','jompha-starter-plugin' ),
					__( 'On','jompha-starter-plugin' ),
					__( 'Off','jompha-starter-plugin' )
				),
			),

			array(
				'label'   => __( 'Checkbox','jompha-starter-plugin' ),
				'id'      => 'orfwOptions_checkbox',
				'type'    => 'checkbox',
				'section' => 'orfwOptions_section',
				'options' => array(
					__( 'Bangladesh','jompha-starter-plugin' ),
					__( 'USA','jompha-starter-plugin' ),
					__( 'Canada','jompha-starter-plugin' ),
				),
			),

			array(
				'label'   => __( 'Radio','jompha-starter-plugin' ),
				'id'      => 'orfwOptions_radio',
				'type'    => 'radio',
				'section' => 'orfwOptions_section',
				'options' => array(
					__( 'On','jompha-starter-plugin' ),
					__( 'Off','jompha-starter-plugin' ),
				),
			),

			array(
				'label'   => esc_html__( 'Icons 1', 'jompha-starter-plugin' ),
				'id'      => 'orfwOptions_icon_1',
				'type'    => 'icons',
				'section' => 'orfwOptions_section',
				'description' => esc_html__('Select icon.', 'jompha-starter-plugin' ),
			),

			array(
				'label'   => __( 'Order Details','jompha-starter-plugin' ),
				'id'      => 'orfwOptions_textarea',
				'type'    => 'textarea',
				'section' => 'orfwOptions_section2',
				'description' => 'Enter Order Details.',
			),

			array(
				'label'   => esc_html__( 'Icon', 'jompha-starter-plugin' ),
				'id'      => 'orfwOptions_icon',
				'type'    => 'icons',
				'section' => 'orfwOptions_section2',
				'description' => esc_html__('Select icon.', 'jompha-starter-plugin' ),
			)
		);

		foreach ( $fields as $field ) {
			add_settings_field(
				$field['id'], 
				$field['label'], 
				array( $this, 'orfwOptionFieldsGenerator' ), 
				'orfw-options', // page slug 
				$field['section'], 
				$field 
			);

			switch ( $field['type'] ) {
				case 'toggle':
				case 'checkbox':
				case 'radio':
					register_setting( 'orfw-options', $field['id']);
					break;
				default:
					register_setting( 'orfw-options', $field['id'], array( 'sanitize_callback' => 'esc_attr' ) );
			}
		}

	}

	public function orfwOptionFieldsGenerator( $field ) {
		$value = get_option( $field['id'] );

		switch ( $field['type'] ) {
			case 'textarea':
				printf( '<textarea name="%1$s" id="%1$s" placeholder="%2$s" rows="5" cols="50">%3$s</textarea>',
					$field['id'],
					isset( $field['placeholder'] ) ? $field['placeholder'] : '',
					$value
				);
				break;

			case 'select':
				$options = $field['options'];

				echo '<select id="'.$field['id'].'" name="'.$field['id'].'">';
				foreach( $options as $option )
				{
					$selected = ($value === $option) ? 'selected' : '';
					printf('<option value="%s" %s>%s</option>', $option, $selected, $option );
				}
				echo "</select>";

				break;

			case 'toggle':
					if( is_array($value) && in_array('toggled', $value) )
					{
						$checked = 'checked';
					}
					printf ('<div class="orfw_switch">
						<input type="checkbox" name="%s[]" id="%s" value="toggled" %s>
						<label for="%s"></label>
					</div>', $field['id'], $field['id'], $checked, $field['id']);
					break;

			case 'checkbox':
				$options = $field['options'];

				foreach( $options as $option )
				{	
					$checked = '';
					if( is_array($value) && in_array($option, $value) )
					{
						$checked = 'checked';
					}

					printf('<input type="checkbox" name="%s[]" value="%s" %s> %s <br>', $field['id'], $option, $checked, $option );
				}
				break;
			
			case 'radio':
				$options = $field['options'];

				foreach( $options as $option )
				{	
					$checked = '';
					if( is_array($value) && in_array($option, $value) )
					{
						$checked = 'checked';
					}

					printf('<input type="radio" name="%s[]" value="%s" %s> %s <br>', $field['id'], $option, $checked, $option );
				}
				break;

			case 'icons':
				printf('<div id="%s" class="jomps-icons">
					<ul class="jomps-icons-selector">
						<li><span class="icon-checkmark"></span></li>
						<li><span class="icon-loop"></span></li>
						<li><span class="icon-stop"></span></li>
						<li><span class="icon-pause"></span></li>
						<li><span class="icon-cross"></span></li>
						<li><span class="icon-warning"></span></li>
						<li><span class="icon-star-full"></span></li>
						<li><span class="icon-clipboard"></span></li>
						<li><span class="icon-power-cord"></span></li>
						<li><span class="icon-ticket"></span></li>
						<li><span class="icon-cart"></span></li>
						<li><span class="icon-coin-dollar"></span></li>
						<li><span class="icon-compass"></span></li>
						<li><span class="icon-clock"></span></li>
						<li><span class="icon-hour-glass"></span></li>
						<li><span class="icon-spinner"></span></li>
						<li><span class="icon-lock"></span></li>
						<li><span class="icon-gift"></span></li>
						<li><span class="icon-fire"></span></li>
						<li><span class="icon-briefcase"></span></li>
						<li><span class="icon-airplane"></span></li>
						<li><span class="icon-shield"></span></li>
						<li><span class="icon-power"></span></li>
						<li><span class="icon-cloud-fill"></span></li>
						<li><span class="icon-cloud-download"></span></li>
						<li><span class="icon-cloud-upload"></span></li>
						<li><span class="icon-cloud-check"></span></li>
						<li><span class="icon-bookmarks"></span></li>
					</ul>
					<input type="hidden" name="%s" value="%s">
				</div>', esc_attr( $field['id'] ), esc_attr( $field['id'] ), esc_attr( $value ));
				break;

			default:
				printf( '<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" />',
					$field['id'],
					$field['type'],
					isset( $field['placeholder'] ) ? $field['placeholder'] : '',
					$value
				);
		}

		if ( isset( $field['description'] ) ) {
			if ( $desc = $field['description'] ) {
				printf( '<p class="description">%s </p>', $desc );
			}
		}
	}


    /**
     * Process and save settings
     *
     * @return mixed array|boolean
     */
    public static function processSettings( $settings ){}
        
}
