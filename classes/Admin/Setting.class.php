<?php
namespace ORFW\Admin;

class Setting
{
    public static $instance;
	public static $pageSlug  = 'orfw-settings';
	public static $optPrefix = 'orfw_';
	public $tabs;
	public $tab;

    public static function getInstance()
    {
        if ( !self::$instance instanceof self )
            self::$instance = new self();

        return self::$instance;
    }

    private function __construct()
    {
		$this->tabs = array(
			'general' => array(
				'name' 		  => esc_html__( 'General', 'order-reviews-for-woocommerce' ),
				'title' 	  => esc_html__( 'General Settings', 'order-reviews-for-woocommerce' ),
				'description' => esc_html__( 'General settings description', 'order-reviews-for-woocommerce' ),
			),
			'styles'  => array(
				'name' 		  => esc_html__( 'Styles', 'order-reviews-for-woocommerce' ),
				'title' 	  => esc_html__( 'Style Settings', 'order-reviews-for-woocommerce' ),
				'description' => esc_html__( 'Style settings description', 'order-reviews-for-woocommerce' ),
			),
			'content'  => array(
				'name' 		  => esc_html__( 'Content', 'order-reviews-for-woocommerce' ),
				'title' 	  => esc_html__( 'Content Settings', 'order-reviews-for-woocommerce' ),
				'description' => esc_html__( 'Change the default texts.', 'order-reviews-for-woocommerce' ),
			),
		);
		$this->tab  = (isset($_GET['tab'])) ? $_GET['tab'] : 'general';

		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_init', array( $this, 'addSections' ) );
		add_action( 'admin_init', array( $this, 'addFields' ) );
    }

	public function menu() 
	{
		add_submenu_page(
			'woocommerce',
			esc_html__( 'ORFW Starter Plugin Settings', 'order-reviews-for-woocommerce' ),
			esc_html__( 'ORFW PLUGIN', 'order-reviews-for-woocommerce' ),
			'manage_woocommerce',
			self::$pageSlug,
			array( $this, 'renderPage' ),
			0
		);
	}

	public static function fields()
	{
		return array(

			array(
				'id'          => 'template_interval_delay',
				'type'        => 'number',
				'section'     => 'general',
				'label'       => esc_html__( 'Interval Delay', 'order-reviews-for-woocommerce' ),
				'placeholder' => '',
				'description' => esc_html__( 'After the popup is closed, show again after X hours.', 'order-reviews-for-woocommerce' ),
				'show_in_js'  => true,
			),

			array(
				'id'          => 'template_force_feedback',
				'type'        => 'checkbox',
				'section'     => 'general',
				'label'       => esc_html__( 'Force Write Feedback', 'order-reviews-for-woocommerce' ),
				'description' => esc_html__( 'If checked, users will have to write a feedback.', 'order-reviews-for-woocommerce' ),
				'options' => array(
					'yes' => esc_html__( 'Yes', 'order-reviews-for-woocommerce' ),
				),
				'show_in_js'  => true,
			),
			
			array(
				'id'          => 'template_force_bad_feedback',
				'type'        => 'checkbox',
				'section'     => 'general',
				'label'       => esc_html__( 'Force Feedback for Bad Rating', 'order-reviews-for-woocommerce' ),
				'description' => esc_html__( 'If checked, users will have to write a feedback if the rating is equal to or less than 3.', 'order-reviews-for-woocommerce' ),
				'options' => array(
					'yes' => esc_html__( 'Yes', 'order-reviews-for-woocommerce' ),
				),
				'show_in_js'  => true,
			),
			
			array(
				'id'          => 'template_show_count',
				'type'        => 'number',
				'section'     => 'general',
				'label'       => esc_html__( 'View count', 'order-reviews-for-woocommerce' ),
				'placeholder' => esc_html__( '2', 'order-reviews-for-woocommerce' ),
				'description' => esc_html__( 'How many times the template will show to customer?', 'order-reviews-for-woocommerce' ),
				'show_in_js'  => true,
			),

			array(
				'id'          => 'template_show_after_hours',
				'type'        => 'number',
				'section'     => 'general',
				'label'       => esc_html__( 'Show after hours', 'order-reviews-for-woocommerce' ),
				'placeholder' => esc_html__( '24', 'order-reviews-for-woocommerce' ),
				'description' => esc_html__( 'After how many hours the template will show after mark the order as completed?', 'order-reviews-for-woocommerce' ),
				'show_in_js'  => true,
			),

			array(
				'id'          => 'template_style_background_header',
				'type'        => 'color',
				'section'     => 'styles',
				'value'		  => '#f4b248',
				'label'       => esc_html__( 'Header Background', 'order-reviews-for-woocommerce' ),
				'description' => esc_html__( 'Set background for the template header', 'order-reviews-for-woocommerce' ),
				'show_in_js'  => false,
			),

			array(
				'id'          => 'template_style_background_body',
				'type'        => 'color',
				'section'     => 'styles',
				'value'		  => '#ecf0f1',
				'label'       => esc_html__( 'Body Background', 'order-reviews-for-woocommerce' ),
				'description' => esc_html__( 'Set background for the template body', 'order-reviews-for-woocommerce' ),
				'show_in_js'  => false,
			),

			array(
				'id'          => 'template_style_color_header',
				'type'        => 'color',
				'section'     => 'styles',
				'value'		  => '#442a00',
				'label'       => esc_html__( 'Header text color', 'order-reviews-for-woocommerce' ),
				'description' => esc_html__( 'Set header text color', 'order-reviews-for-woocommerce' ),
				'show_in_js'  => false,
			),

			array(
				'id'          => 'template_style_color_body',
				'type'        => 'color',
				'section'     => 'styles',
				'value'		  => '#442a00',
				'label'       => esc_html__( 'Body text color', 'order-reviews-for-woocommerce' ),
				'description' => esc_html__( 'Set body text color', 'order-reviews-for-woocommerce' ),
				'show_in_js'  => false,
			),

			array(
				'id'          => 'template_style_background_button',
				'type'        => 'color',
				'section'     => 'styles',
				'value'		  => '#f4b248',
				'label'       => esc_html__( 'Button Background', 'order-reviews-for-woocommerce' ),
				'description' => esc_html__( 'Set button background color', 'order-reviews-for-woocommerce' ),
				'show_in_js'  => false,
			),

			array(
				'id'          => 'template_style_color_button',
				'type'        => 'color',
				'section'     => 'styles',
				'value'		  => '#ecf0f1',
				'label'       => esc_html__( 'Button text color', 'order-reviews-for-woocommerce' ),
				'description' => esc_html__( 'Set button background color', 'order-reviews-for-woocommerce' ),
				'show_in_js'  => false,
			),

			array(
				'id'          => 'template_style_color_skip',
				'type'        => 'color',
				'section'     => 'styles',
				'value'		  => '#f4b248',
				'label'       => esc_html__( 'Skip/Close color', 'order-reviews-for-woocommerce' ),
				'description' => esc_html__( 'Set skip/close color', 'order-reviews-for-woocommerce' ),
				'show_in_js'  => false,
			),

			array(
				'id'          => 'text_rate_order_heading',
				'type'        => 'text',
				'section'     => 'content',
				'label'       => esc_html__( 'Rate the order', 'order-reviews-for-woocommerce' ),
				'value' 	  => esc_attr( 'Rate the order' ),
				'show_in_js'  => false,
			),

			array(
				'id'          => 'text_write_feedback',
				'type'        => 'text',
				'section'     => 'content',
				'label'       => esc_html__( 'Write feedback', 'order-reviews-for-woocommerce' ),
				'value' 	  => esc_attr( 'Write feedback' ),
				'show_in_js'  => false,
			),

			array(
				'id'          => 'text_footer',
				'type'        => 'textarea',
				'section'     => 'content',
				'label'       => esc_html__( 'Footer Text', 'order-reviews-for-woocommerce' ),
				'value' 	  => esc_attr( 'Please provide your honest feedback!' ),
				'show_in_js'  => false,
			),

			array(
				'id'          => 'template_style_color_star',
				'type'        => 'color',
				'section'     => 'styles',
				'value'		  => '#f4b248',
				'label'       => esc_html__( 'Stars color', 'order-reviews-for-woocommerce' ),
				'description' => esc_html__( 'Set stars color', 'order-reviews-for-woocommerce' ),
				'show_in_js'  => false,
			),

			array(
				'id'      	  => 'force_order',
				'type'    	  => 'toggle',
				'section' 	  => 'general',
				'label'   	  => esc_html__( 'Force Order', 'order-reviews-for-woocommerce' ),
				'description' => esc_html__( 'Force customer to give a review and hide the skip button', 'order-reviews-for-woocommerce' ),
				'show_in_js'  => false,
			),

			array(
				'id'      	  => 'spd_card',
				'type'    	  => 'color',
				'section' 	  => 'styles',
				'label'   	  => esc_html__( 'Review Box', 'order-reviews-for-woocommerce' ),
				'description' => esc_html__( 'Background color of the card', 'order-reviews-for-woocommerce' ),
				'show_in_js'  => false,
			),

			array(
				'id'      	  => 'spd_card_text',
				'type'    	  => 'color',
				'section' 	  => 'styles',
				'label'   	  => esc_html__( 'Review Color', 'order-reviews-for-woocommerce' ),
				'description' => esc_html__( 'Review color of the text ', 'order-reviews-for-woocommerce' ),
				'show_in_js'  => false,
			),

			array(
				'id'      	  => 'spd_card_link',
				'type'    	  => 'color',
				'section' 	  => 'styles',
				'label'   	  => esc_html__( 'Review Link Color', 'order-reviews-for-woocommerce' ),
				'description' => esc_html__( 'Link color (products) of the text', 'order-reviews-for-woocommerce' ),
				'show_in_js'  => false,
			),

			array(
				'id'      => 'spd_card_font_size',
				'type'    => 'select',
				'section' => 'styles',
				'label'   => esc_html__( 'Review Font Size', 'order-reviews-for-woocommerce' ),
				'options' => array(
					'large'  => esc_html__( 'Large',  'order-reviews-for-woocommerce' ),
					'medium' => esc_html__( 'Medium', 'order-reviews-for-woocommerce' ),
					'small'  => esc_html__( 'Small',  'order-reviews-for-woocommerce' ),
				),
				'show_in_js'  => false,
			),

			array(
				'id'          => 'spd_card_font_style',
				'type'        => 'toggle',
				'section'     => 'styles',
				'label'   	  => esc_html__( 'Review Font Style', 'order-reviews-for-woocommerce' ),
				'description' => esc_html__( 'Mark if you want an italic style', 'order-reviews-for-woocommerce' ),
				'show_in_js'  => false,
			),

		);
	}

	public function renderPage()
	{
	?>
        <div class="wrap jmph-settings-container">
            <div class="jmph-sets">
				<h1><?php echo esc_html__( 'ORFW Starter Plugin', 'order-reviews-for-woocommerce' ); ?></h1>
				<p><?php echo esc_html__( 'Subtitle here', 'order-reviews-for-woocommerce' ); ?></p>

				<?php settings_errors(); ?>

				<nav class="nav-tab-wrapper">
					<?php foreach ( $this->tabs as $tabID => $tab ) { ?>
					<a href="?page=<?php echo self::$pageSlug; ?>&tab=<?php echo $tabID; ?>" class="nav-tab <?php if ($tabID == $this->tab) echo 'nav-tab-active'; ?>"><?php echo $tab['name']; ?></a>
					<?php } ?>
				</nav>

				<form method="POST" action="options.php">
					<?php 
					wp_nonce_field('update-options');
					
					settings_fields( self::$optPrefix . $this->tab );
					do_settings_sections( self::$pageSlug );
					submit_button();
					?>
				</form>
			</div>
			<?php /* <div class="jmph-endorse">
				<h2>Recommended Plugins</h2>
				<a href="https://wordpress.org/plugins/ultimate-coupon-for-woocommerce" target="_blank">
					<img src="<?php echo JSP_RESOURCES; ?>/images/jsp-banner.png" alt="">
				</a>
			</div> */ ?>
        </div>
	<?php
	}

	public function addSections()
	{
		if (! isset($this->tabs[ $this->tab ]))
			return;
		
		add_settings_section( self::$optPrefix . $this->tab, $this->tabs[ $this->tab ]['title'], function() { echo $this->tabs[ $this->tab ]['description']; }, self::$pageSlug );
	}

	public function addFields()
	{
		foreach ( self::fields() as $field )
		{
			$uniqueID = self::$optPrefix . $field['id'];

			add_settings_field(
				$uniqueID,
				$field['label'],
				array( $this, 'createField' ),
				self::$pageSlug,
				self::$optPrefix . $field['section'],
				array_merge( $field, array( 'unique_id' => $uniqueID ) )
			);

			switch ( $field['type'] )
			{
				case 'toggle':
				case 'checkbox':
				case 'radio':
					register_setting( self::$optPrefix . $field['section'], $uniqueID);
					break;
				
				default:
					register_setting( self::$optPrefix . $field['section'], $uniqueID, array( 'sanitize_callback' => 'esc_attr' ) );
			}
		}

	}

	public function createField( $field )
	{
		$value = get_option( $field['unique_id'] );

		switch ( $field['type'] )
		{
			case 'textarea':
				echo sprintf( '<textarea name="%1$s" id="%1$s" placeholder="%2$s" rows="5" cols="50">%3$s</textarea>',
					esc_attr( $field['unique_id'] ),
					isset( $field['placeholder'] ) ? esc_attr( $field['placeholder'] ) : '',
					( $value == false ) ? esc_attr( $field['value'] ) : esc_attr( $value )
				);
				break;

			case 'select':
				echo '<select name="' . esc_attr( $field['unique_id'] ) . '">';
				foreach( $field['options'] as $optValue => $optText )
				{
					$selected = ($value === $optValue) ? 'selected' : '';
					echo sprintf( '<option value="%1$s" %3$s>%2$s</option>', esc_attr( $optValue ), esc_html( $optText ), $selected );
				}
				echo '</select>';

				break;

			case 'toggle':
				$checked = ( 'toggled' == $value ) ? 'checked' : '';
				echo sprintf( '<div class="jmph-toggle">
					<input type="checkbox" id="%1$s" name="%1$s" value="toggled" %2$s>
					<label for="%1$s"></label>
				</div>', esc_attr( $field['unique_id'] ), $checked );
				break;

			case 'checkbox':
				$options = $field['options'];

				foreach( $options as $optValue => $optText )
				{
					echo sprintf( '<input type="checkbox" id="%5$s" name="%1$s[]" value="%2$s" %4$s> <label for="%5$s">%3$s</label> <br>',
						esc_attr( $field['unique_id'] ),
						esc_attr( $optValue ),
						esc_html( $optText ),
						( is_array($value) && in_array($optValue, $value) ) ? 'checked' : '',
						$field['unique_id'] . '_' . strtolower(preg_replace('/[^A-Za-z0-9_-]/i', '', $optValue))
					);
				}
				break;
			
			case 'radio':
				$options = $field['options'];

				foreach( $options as $optValue => $optText )
				{
					echo sprintf( '<input type="radio" id="%5$s" name="%1$s[]" value="%2$s" %4$s> <label for="%5$s">%3$s</label><br>',
						esc_attr( $field['unique_id'] ),
						esc_attr( $optValue ),
						esc_html( $optText ),
						( is_array($value) && in_array($optValue, $value) ) ? 'checked' : '',
						$field['unique_id'] . '_' . strtolower(preg_replace('/[^A-Za-z0-9_-]/i', '', $optValue))
					);
				}
				break;

			case 'icons':
				echo sprintf( '<div id="%1$s" class="jmph-icons">
					<ul class="jmph-icons-selector">
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
					<input type="hidden" name="%1$s" value="%2$s">
				</div>', esc_attr( $field['unique_id'] ), esc_attr( $value ) );
				break;

			default:
				echo sprintf( '<input id="%1$s" name="%1$s" type="%2$s" placeholder="%3$s" value="%4$s">',
					esc_attr( $field['unique_id'] ),
					esc_attr( $field['type'] ),
					isset( $field['placeholder'] ) ? esc_attr( $field['placeholder'] ) : '',
					( $value == false ) ? esc_attr( $field['value'] ) : esc_attr( $value )
				);
		}

		if ( isset( $field['description'] ) )
			echo sprintf( '<p class="description">%s</p>', esc_html( $field['description'] ) );
	}
}
