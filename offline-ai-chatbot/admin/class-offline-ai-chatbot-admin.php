<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://jules.agent
 * @since      0.1.0
 *
 * @package    Offline_AI_Chatbot
 * @subpackage Offline_AI_Chatbot/admin
 */
class Offline_AI_Chatbot_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    0.1.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    0.1.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    0.1.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the administration menu for this plugin into the WordPress Dashboard menu.
     *
     * @since    0.1.0
     */
    public function add_admin_menu() {
        add_menu_page(
            __( 'Offline AI Chatbot', 'offline-ai-chatbot' ),
            __( 'AI Chatbot', 'offline-ai-chatbot' ),
            'manage_options',
            $this->plugin_name,
            array( $this, 'display_admin_page' ),
            'dashicons-format-chat',
            80
        );
    }

    /**
     * Render the settings page for this plugin.
     *
     * @since    0.1.0
     */
    public function display_admin_page() {
        require_once plugin_dir_path( __FILE__ ) . 'partials/offline-ai-chatbot-admin-display.php';
    }

    /**
     * Handle the content scraping request.
     *
     * @since    0.1.0
     */
    public function handle_scrape_request() {
        if ( ! isset( $_POST['oac_nonce_field'] ) || ! wp_verify_nonce( $_POST['oac_nonce_field'], 'oac_scrape_content_nonce' ) ) {
            wp_die( __( 'Security check failed.', 'offline-ai-chatbot' ) );
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'You do not have sufficient permissions to perform this action.', 'offline-ai-chatbot' ) );
        }

        Offline_AI_Chatbot_Scraper::scrape();

        wp_redirect( add_query_arg( 'scraped', 'true', wp_get_referer() ) );
        exit;
    }

    /**
     * Handle the model settings save request.
     *
     * @since    0.1.0
     */
    public function handle_save_model_settings_request() {
        if ( ! isset( $_POST['oac_nonce_field_settings'] ) || ! wp_verify_nonce( $_POST['oac_nonce_field_settings'], 'oac_save_model_settings_nonce' ) ) {
            wp_die( __( 'Security check failed.', 'offline-ai-chatbot' ) );
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'You do not have sufficient permissions to perform this action.', 'offline-ai-chatbot' ) );
        }

        if ( isset( $_POST['oac_llama_cpp_path'] ) ) {
            $llama_path = sanitize_text_field( $_POST['oac_llama_cpp_path'] );
            update_option( 'oac_llama_cpp_path', $llama_path );
        }

        if ( isset( $_POST['oac_gguf_model_path'] ) ) {
            $model_path = sanitize_text_field( $_POST['oac_gguf_model_path'] );
            update_option( 'oac_gguf_model_path', $model_path );
        }

        wp_redirect( add_query_arg( 'settings_saved', 'true', wp_get_referer() ) );
        exit;
    }
}
