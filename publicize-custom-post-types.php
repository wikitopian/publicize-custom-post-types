<?php
/*
 * Plugin Name: Publicize Custom Post Types
 * Plugin URI: http://www.github.com/wikitopian/publicize-custom-post-types
 * Description: Publish custom post types to social media, too.
 * Version: 0.1.0
 * Author: @wikitopian
 * Author URI: http://www.github.com/wikitopian
 * License: GPLv2
 */

class Publicize_Custom_Post_Types {
	private $namespace;

	public function __construct() {
		$this->namespace = get_class( $this );

		add_action( 'admin_init', array( &$this, 'add_option' ) );
		add_action( 'admin_menu', array( &$this, 'add_menu' ) );

		add_action( 'init', array( &$this, 'set_post_types' ) );
	}
	public function add_option() {
		register_setting( $this->namespace, $this->namespace );

		add_settings_section(
			$this->namespace,
			'',
			array( &$this, 'add_menu_options' ),
			$this->namespace
		);  

		add_settings_field(
			$this->namespace,
			__( 'Available Post Types...' ),
			array( &$this, 'add_menu_options_fields' ),
			$this->namespace,
			$this->namespace
		);

	}
	public function add_menu() {
		add_options_page(
			__( 'Publicize Custom Post Types' ),
			__( 'Publicize Custom Post Types' ),
			'manage_options',
			$this->namespace,
			array( &$this, 'add_menu_page' )
		);
	}
	public function add_menu_page() {
		// Set class property
		$this->options = get_option( $this->namespace );
?>
<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php _e( 'Publicize Custom Post Types' ); ?></h2>
	<form method="post" action="options.php">
<?php
		settings_fields( $this->namespace );
		do_settings_sections( $this->namespace );
		submit_button(); 
?>
	</form>
</div>
<?php
	}
	public function add_menu_options() {}
	public function add_menu_options_fields() {

		$types_all = get_post_types( array(), 'objects' );
		unset( $types_all['post'] );

		$settings = get_option( $this->namespace, array() );

		$types = array();
		foreach ( $types_all as $type => $details ) {

			if( !empty( $settings[$type] ) ) {
				$value = true;
			} else {
				$value = false;
			}

			$types[$type] = array(
				'label' => $details->labels->name,
				'name'  => $type,
				'value' => $value,
			);
		}

		echo "<table class=\"form-table\">\n";

		foreach ( $types as $type ) {

			echo "\t<tr valign=\"top\">\n";
			echo "\t\t<th scope=\"row\">{$type['label']}</th>\n";
			echo "\t\t<td>\n";
			echo "\t\t\t<input type=\"checkbox\" ";
			echo "name=\"{$this->namespace}[{$type['name']}]\" ";
			echo checked( $type['value'] );
			echo " />\n";
			echo "\t\t</td>\n";
			echo "\t</tr>\n";

		}

		echo "</table>";

	}
	public function set_post_types() {
		$types = get_option( $this->namespace, array() );

		foreach ( $types as $type => $on ) {
			if( $on ) {
				add_post_type_support( $type, 'publicize' );
			}
		}
	}
}
$publicize_custom_post_types = new Publicize_Custom_Post_Types();

?>
