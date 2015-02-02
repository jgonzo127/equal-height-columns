<?php

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       http://wordpress.org/plugins/equal-height-columns
 * @since      1.0.0
 *
 * @package    Equal_Height_Columns
 * @subpackage Equal_Height_Columns/admin
 */

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    Equal_Height_Columns
 * @subpackage Equal_Height_Columns/admin
 * @author     MIGHTYminnow, Mickey Kay, Braad Martin mickeyskay@gmail.com
 */
class Equal_Height_Columns_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The display name of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin__displau_name    The public name of this plugin.
	 */
	private $plugin_display_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Options for this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $options    The options stored for this plugin.
	 */
	private $options;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $plugin_name            The name of this plugin.
	 * @var      string    $plugin_display_name    The public name of this plugin.
	 * @var      string    $version                The version of this plugin.
	 */
	public function __construct( $plugin_name, $plugin_display_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->plugin_display_name = $plugin_display_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the Dashboard.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/equal-height-columns-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the dashboard.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/equal-height-columns-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Add options page to admin menu.
	 *
	 * @since    1.0.0
	 */
	public function add_options_page() {

		// Get the options stored for this plugin.
		$this->options = get_option( $this->plugin_name );
				
		add_options_page(
            $this->plugin_display_name, // Page title
            $this->plugin_display_name, // Menu title
            'manage_options', // Capability
            $this->plugin_name, // Menu slug
            array( $this, 'output_options_page' )
        );

	}

	/**
	 * Output the contents of the options page.
	 *
	 * @since    1.0.0
	 */
	public function output_options_page() {
		?>
		<div class="wrap">
            <?php screen_icon(); ?>
            <h2><?php echo $this->plugin_display_name; ?></h2>           
            <form method="post" action="options.php">
            <?php
                settings_fields( $this->plugin_name );   
                do_settings_sections( $this->plugin_name );
                $this->output_add_button();
                submit_button();
            ?>
            </form>
        </div>
        <?php
	}

	function output_add_button() {
		printf(
			'<button type="button" class="button add-element">%s</button>',
			__( '+ Add More', 'equal-height-columns' )
		);
	}

	/**
	 * Register the plugin settings.
	 *
	 * @since    1.0.0
	 */
	function register_settings() {

		register_setting(
            $this->plugin_name, // Option group
            $this->plugin_name, // Option name
            array( $this, 'validate' ) // Validate
        );

        add_settings_section(
            'main-settings', // ID
            __( 'Elements', 'equal-height-columns' ), // Title
            array( $this, 'output_element_section_info' ), // Callback
            $this->plugin_name // Page
        );

        // Set number of fields to output (number of saved groups, or 1 if none are saved).  		
        $field_count = count( $this->options['element-groups'] ) ? count( $this->options['element-groups'] ) : 1;

        // Output correct number of fields.
        for ( $i = 1; $i <= $field_count; $i++ ) {
	        
	        add_settings_field(
	            'elements-group-' . $i, // ID
	            __( 'Element Group #', 'equal-height-columns' ) . '<span class="index-number">' . $i . '<span>', // Title 
	            array( $this, 'output_elements_fields' ), // Callback
	            $this->plugin_name, // Page
	            'main-settings', // Section
	            array( // Args
	                'id' => 'element-groups',
	                'index' => $i,
	            )         
	        );

	    }

	}

	function validate( $input ) {

		// Do validation.

		return $input;
	}

	function output_element_section_info() {
		_e( 'Section description goes here.', 'equal-height-columns' );
	}

	function output_elements_fields( $args ) {

		// Set spacer variable
		$spacer = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

		// Selector input
		printf(
            '<input type="text" id="%s" name="%s[%s][%s][%s]" data-index="%s" value="%s" />%s',
            $args['id'],
            $this->plugin_name,
            $args['id'],
            $args['index'],
            'selector',
            $args['index'],
            isset( $this->options[ $args['id'] ][ $args['index'] ]['selector'] ) ? esc_attr( $this->options[ $args['id'] ][ $args['index'] ]['selector'] ) : '',
            $spacer
        );

		// Breakpoint input
        printf(
            '<small>%s</small> <input type="number" id="%s" name="%s[%s][%s][%s]" data-index="%s" value="%s" /> px%s',
            __( 'Breakpoint:', 'equal-height-columns' ),
            'breakpoint',
            $this->plugin_name,
            $args['id'],
            $args['index'],
            'breakpoint',
            $args['index'],
            isset( $this->options[ $args['id'] ][ $args['index'] ]['breakpoint'] ) ? esc_attr( $this->options[ $args['id'] ][ $args['index'] ]['breakpoint'] ) : '768',
            $spacer
        );

        // Remove button
        printf(
            '<button type="button" class="button remove-element">%s</button>',
            __( 'Remove', 'equal-height-columns' )
        );

	}
}
