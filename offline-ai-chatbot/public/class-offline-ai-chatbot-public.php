<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://jules.agent
 * @since      0.1.0
 *
 * @package    Offline_AI_Chatbot
 * @subpackage Offline_AI_Chatbot/public
 */
class Offline_AI_Chatbot_Public {

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
     * @since    0.1.0
     */
    public function enqueue_styles() {
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/offline-ai-chatbot-public.css', array(), $this->version, 'all' );
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    0.1.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/offline-ai-chatbot-public.js', array( 'jquery' ), $this->version, true );

        // Pass data to JS, including the AJAX URL and a nonce for security
        wp_localize_script( $this->plugin_name, 'oac_ajax_object',
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'oac_chat_nonce' )
            )
        );
    }

    /**
     * Add the chat widget HTML to the site footer.
     *
     * @since    0.1.0
     */
    public function display_chat_widget() {
        require_once plugin_dir_path( __FILE__ ) . 'partials/offline-ai-chatbot-public-display.php';
    }

    /**
     * Handle the AJAX request for getting a bot response.
     *
     * @since    0.1.0
     */
    public function get_bot_response() {
        check_ajax_referer( 'oac_chat_nonce', 'nonce' );

        if ( ! isset( $_POST['message'] ) ) {
            wp_send_json_error( array( 'message' => 'No message provided.' ) );
        }

        $user_message = sanitize_textarea_field( $_POST['message'] );

        $bot_response = Offline_AI_Chatbot_Model::generate_response( $user_message );

        // The response from the model might be null or empty if there was an error.
        if ( empty( $bot_response ) ) {
             wp_send_json_error( array( 'message' => 'The model did not return a response.' ) );
        } else {
             wp_send_json_success( array( 'message' => $bot_response ) );
        }

        wp_die();
    }
}
