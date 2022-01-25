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
				'description' => esc_html__( 'Content settings description', 'order-reviews-for-woocommerce' ),
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

	private function fields()
	{
		return array(
			array(
				'id'          => 'name',
				'type'        => 'text',
				'section'     => 'general',
				'label'       => esc_html__( 'Name', 'order-reviews-for-woocommerce' ),
				'placeholder' => esc_html__( 'Name', 'order-reviews-for-woocommerce' ),
			),
			
			array(
				'id'      => 'gender',
				'type'    => 'radio',
				'section' => 'general',
				'label'   => esc_html__( 'Gender', 'order-reviews-for-woocommerce' ),
				'options' => array(
					'male'   => esc_html__( 'Male', 'order-reviews-for-woocommerce' ),
					'female' => esc_html__( 'Female', 'order-reviews-for-woocommerce' ),
					'other'  => esc_html__( 'Other', 'order-reviews-for-woocommerce' ),
				),
			),

			array(
				'id'      => 'city',
				'type'    => 'select',
				'section' => 'general',
				'label'   => esc_html__( 'Select City', 'order-reviews-for-woocommerce' ),
				'options' => array(
					'' 		 	 => esc_html__( 'Select', 'order-reviews-for-woocommerce' ),
					'dhaka'      => esc_html__( 'Dhaka', 'order-reviews-for-woocommerce' ),
					'chittagong' => esc_html__( 'Chittagong', 'order-reviews-for-woocommerce' ),
					'mymensingh' => esc_html__( 'Mymensingh', 'order-reviews-for-woocommerce' ),
					'barisal'    => esc_html__( 'Barisal', 'order-reviews-for-woocommerce' ),
				),
			),

			array(
				'id'      	  => 'about_me',
				'type'    	  => 'textarea',
				'section' 	  => 'general',
				'label'   	  => esc_html__( 'About Me', 'order-reviews-for-woocommerce' ),
				'description' => esc_html__( 'Write about me.', 'order-reviews-for-woocommerce' ),
			),

			array(
				'id'      	  => 'is_admin',
				'type'    	  => 'toggle',
				'section' 	  => 'general',
				'label'   	  => esc_html__( 'Is Admin?', 'order-reviews-for-woocommerce' ),
				'description' => esc_html__( 'Is admin?', 'order-reviews-for-woocommerce' ),
			),

			array(
				'id'      => 'has_access',
				'type'    => 'checkbox',
				'section' => 'general',
				'label'   => esc_html__( 'Has access?', 'order-reviews-for-woocommerce' ),
				'options' => array(
					'front' => esc_html__( 'Front-end', 'order-reviews-for-woocommerce' ),
					'admin' => esc_html__( 'Admin', 'order-reviews-for-woocommerce' ),
					'vip'   => esc_html__( 'VIP', 'order-reviews-for-woocommerce' ),
				),
			),

			array(
				'id'      	  => 'badge',
				'type'    	  => 'icons',
				'section' 	  => 'general',
				'label'   	  => esc_html__( 'Badge', 'order-reviews-for-woocommerce' ),
				'description' => esc_html__( 'Select icon.', 'order-reviews-for-woocommerce' ),
			),

			array(
				'id'      	  => 'badge',
				'type'    	  => 'color',
				'section' 	  => 'general',
				'label'   	  => esc_html__( 'Color', 'order-reviews-for-woocommerce' ),
				'description' => esc_html__( 'Select color.', 'order-reviews-for-woocommerce' ),
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
		foreach ( $this->fields() as $field )
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
					esc_html( $value )
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
					esc_attr( $value )
				);
		}

		if ( isset( $field['description'] ) )
			echo sprintf( '<p class="description">%s</p>', esc_html( $field['description'] ) );
	}
}
