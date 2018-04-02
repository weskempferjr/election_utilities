<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://tnotw.com
 * @since      1.0.0
 *
 * @package    Election_Utilities
 * @subpackage Election_Utilities/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Election_Utilities
 * @subpackage Election_Utilities/public
 * @author     Wes Kempfer <wkempferjr@tnotw.com>
 */
class Election_Utilities_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Election_Utilities_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Election_Utilities_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */


		$shortcodes = new Election_Utilities_Shortcodes();
		if ( ! $shortcodes->has_shortcodes() ) {
			return;
		}


		wp_enqueue_style( $this->plugin_name . '-bootstrap',
			plugin_dir_url( __FILE__ ) . 'css/bootstrap.css',
			wp_get_theme()->get('Version'),
			false
		);

		wp_enqueue_style( $this->plugin_name . '-bootstrap-theme',
			plugin_dir_url( __FILE__ ) . 'css/bootstrap-theme.css',
			array($this->plugin_name . '-bootstrap'),
			wp_get_theme()->get('Version'),
			false
		);

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/election-utilities-public.css',
			array(),
			$this->version,
			'all'
		);




	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Election_Utilities_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Election_Utilities_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		// Enqueue only if necessary.

		$shortcodes = new Election_Utilities_Shortcodes();
		if ( ! $shortcodes->has_shortcodes() ) {
			return;
		}

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/election-utilities-public.js',
			array( 'jquery' ),
			$this->version,
			false
		);

		/*
	    * Bootstrap
	    */
		wp_enqueue_script( $this->plugin_name . '-bootstrap',
			plugin_dir_url( __FILE__ ) . '/js/bootstrap.js',
			array('jquery'),
			$this->version,
			true
		);


		// TODO: configure support for minified files in production env
		$child_ng = $this->plugin_name . '-ng';

		wp_enqueue_script( $child_ng,
			plugin_dir_url( __FILE__ ) . '/js/angular.js',
			array('jquery'),
			$this->version,
			true
		);



		wp_enqueue_script( $this->plugin_name . '-ng-route',
			plugin_dir_url( __FILE__ ) . '/js/angular-route.js',
			array('jquery', $child_ng),
			$this->version,
			true
		);

		wp_enqueue_script( $this->plugin_name . '-ng-animate',
			plugin_dir_url( __FILE__ ) . '/js/angular-animate.js',
			array('jquery', $child_ng),
			$this->version,
			true
		);



		wp_enqueue_script( $this->plugin_name . '-ng-sanitize',
			plugin_dir_url( __FILE__ ) . '/js/angular-sanitize.js',
			array('jquery', $child_ng),
			$this->version,
			true
		);

		wp_enqueue_script( $this->plugin_name . '-ng-resource',
			plugin_dir_url( __FILE__ ) . '/js/angular-resource.js',
			array('jquery', $child_ng),
			$this->version,
			true
		);

		wp_enqueue_script( $this->plugin_name . '-ng-ui-router',
			plugin_dir_url( __FILE__ ) . '/js/angular-ui-router.js',
			array('jquery', $child_ng),
			$this->version,
			true
		);

		wp_enqueue_script( $this->plugin_name . '-ng-infinite-scroll',
			plugin_dir_url( __FILE__ ) . '/js/ng-infinite-scroll.min.js',
			array('jquery', $child_ng),
			$this->version,
			true
		);

		//add for spinner
		wp_enqueue_script( $this->plugin_name . '-ng-spinner',
			plugin_dir_url( __FILE__ ) . '/js/angular-spinner.js',
			array('jquery', $child_ng),
			$this->version,
			true
		);

		wp_enqueue_script( $this->plugin_name . '-ng-ui-bootstrap',
			plugin_dir_url( __FILE__ ) . '/js/ui-bootstrap.js',
			array('jquery', $child_ng),
			$this->version,
			true
		);

		wp_enqueue_script( $this->plugin_name . '-ng-ui-bootstrap-tpls',
			plugin_dir_url( __FILE__ ) . '/js/ui-bootstrap-tpls.js',
			array('jquery', $child_ng),
			$this->version,
			true
		);

		$public_notices_js_handle = $this->plugin_name . '-ng-app';

		wp_enqueue_script( $public_notices_js_handle,
			plugin_dir_url( __FILE__ ) . '/js/election-utilities-ng.js',
			array('jquery',$child_ng),
			null,
			true
		);

		$client_config = new Election_Utilities_Client_Configurator();
		$client_config->configure_js_globals( $public_notices_js_handle );

	}

}
