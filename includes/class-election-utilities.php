<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://tnotw.com
 * @since      1.0.0
 *
 * @package    Election_Utilities
 * @subpackage Election_Utilities/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Election_Utilities
 * @subpackage Election_Utilities/includes
 * @author     Wes Kempfer <wkempferjr@tnotw.com>
 */
class Election_Utilities {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Election_Utilities_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'PLUGIN_NAME_VERSION' ) ) {
			$this->version = PLUGIN_NAME_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'election-utilities';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

		$this->register_shortcodes();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Election_Utilities_Loader. Orchestrates the hooks of the plugin.
	 * - Election_Utilities_i18n. Defines internationalization functionality.
	 * - Election_Utilities_Admin. Defines all hooks for the admin area.
	 * - Election_Utilities_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-election-utilities-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-election-utilities-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-election-utilities-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-election-utilities-public.php';


		/*
	      * Defines plugin settings UI.
	    */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-election-utilities-settings.php';


		/*
		 * The plugin AJAX request dispatcher.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-election-utilities-ajax.php';

		/*
		 *  Configures client-side javascript globals
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-election-utilities-client-configurator.php';


		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-file-uploader.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-category-dropdown-generator.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-election-utilities-election-category-uploader.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-election-utilities-response-uploader.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-personal-info-manager.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-election-utilities-shortcodes.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-election-utilities-election-overview.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-election-utilities-page-manager.php';



		$this->loader = new Election_Utilities_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Election_Utilities_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Election_Utilities_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Election_Utilities_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );


		$settings = new Election_Utilities_Settings();
		$this->loader->add_action('admin_menu',  $settings, 'add_options_page');
		$this->loader->add_action('admin_init',  $settings, 'settings_init');


		$page_manager = new Election_Utilities_Page_Manager();

		$this->loader->add_action('init', $page_manager, 'create_contest_page');
		$this->loader->add_action('init', $page_manager, 'create_compare_page');
		$this->loader->add_action('init', $page_manager, 'add_rewrite_tags');
		$this->loader->add_action('init', $page_manager, 'add_rewrite_rules');
		$this->loader->add_filter('the_content', $page_manager, 'display_contest_page');
		$this->loader->add_filter('the_content', $page_manager, 'display_compare_page');
		$this->loader->add_filter('query_vars', $page_manager, 'add_query_vars');

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Election_Utilities_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		/*
	     * AJAX configuration.
	    */
		$ajax_controller = new Election_Utilities_Ajax_Controller();
		$this->loader->add_action('wp_ajax_nopriv_election_utilities_ajax',  $ajax_controller, 'election_utilities_ajax');
		$this->loader->add_action('wp_ajax_election_utilities_ajax',$ajax_controller, 'election_utilities_ajax');


	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Election_Utilities_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}



	private function register_shortcodes() {

		$pad_shortcodes = new Election_Utilities_Shortcodes();
		$pad_shortcodes->register();

	}

}
