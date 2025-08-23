<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://jules.agent
 * @since      0.1.0
 *
 * @package    Offline_AI_Chatbot
 * @subpackage Offline_AI_Chatbot/includes
 */
class Offline_AI_Chatbot {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    0.1.0
     * @access   protected
     * @var      Offline_AI_Chatbot_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    0.1.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    0.1.0
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
     * @since    0.1.0
     */
    public function __construct() {
        if ( defined( 'OAC_VERSION' ) ) {
            $this->version = OAC_VERSION;
        } else {
            $this->version = '0.1.0';
        }
        $this->plugin_name = 'offline-ai-chatbot';

        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();

    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Offline_AI_Chatbot_Loader. Orchestrates the hooks of the plugin.
     * - Offline_AI_Chatbot_Admin. Defines all hooks for the admin area.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    0.1.0
     * @access   private
     */
    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-offline-ai-chatbot-loader.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-offline-ai-chatbot-admin.php';

        /**
         * The class responsible for scraping the website content.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-offline-ai-chatbot-scraper.php';

        /**
         * The class responsible for interacting with the AI model.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-offline-ai-chatbot-model.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-offline-ai-chatbot-public.php';

        $this->loader = new Offline_AI_Chatbot_Loader();

    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    0.1.0
     * @access   private
     */
    private function define_admin_hooks() {

        $plugin_admin = new Offline_AI_Chatbot_Admin( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_admin_menu' );
        $this->loader->add_action( 'admin_post_oac_scrape_content', $plugin_admin, 'handle_scrape_request' );
        $this->loader->add_action( 'admin_post_oac_save_model_settings', $plugin_admin, 'handle_save_model_settings_request' );

    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    0.1.0
     * @access   private
     */
    private function define_public_hooks() {

        $plugin_public = new Offline_AI_Chatbot_Public( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
        $this->loader->add_action( 'wp_footer', $plugin_public, 'display_chat_widget' );
        $this->loader->add_action( 'wp_ajax_oac_get_bot_response', $plugin_public, 'get_bot_response' );
        $this->loader->add_action( 'wp_ajax_nopriv_oac_get_bot_response', $plugin_public, 'get_bot_response' );

    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    0.1.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     0.1.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     0.1.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }
}
