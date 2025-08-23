<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://jules.agent
 * @since      0.1.0
 *
 * @package    Offline_AI_Chatbot
 * @subpackage Offline_AI_Chatbot/admin/partials
 */
?>

<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    <p><?php esc_html_e( 'Welcome to the settings page for the Offline AI Chatbot.', 'offline-ai-chatbot' ); ?></p>

    <hr>

    <h2><?php esc_html_e( 'Knowledge Base Management', 'offline-ai-chatbot' ); ?></h2>
    <p><?php esc_html_e( 'Click the button below to scrape the content of your website. This will build the knowledge base for the AI to answer questions.', 'offline-ai-chatbot' ); ?></p>

    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <input type="hidden" name="action" value="oac_scrape_content">
        <?php wp_nonce_field( 'oac_scrape_content_nonce', 'oac_nonce_field' ); ?>
        <?php submit_button( __( 'Scrape Website Content', 'offline-ai-chatbot' ) ); ?>
    </form>

    <?php
    if ( isset( $_GET['scraped'] ) && $_GET['scraped'] === 'true' ) {
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Content scraped successfully!', 'offline-ai-chatbot' ) . '</p></div>';
    }
    if ( isset( $_GET['settings_saved'] ) && $_GET['settings_saved'] === 'true' ) {
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Model settings saved successfully!', 'offline-ai-chatbot' ) . '</p></div>';
    }
    ?>

    <hr>

    <h2><?php esc_html_e( 'Model Settings', 'offline-ai-chatbot' ); ?></h2>
    <p><?php esc_html_e( 'Please provide the full server paths to your LLaMA C++ executable and the GGUF model file.', 'offline-ai-chatbot' ); ?></p>
    <p><em><?php esc_html_e( 'Note: You must compile/download the llama.cpp executable and a compatible GGUF model onto your server manually. This plugin does not handle the installation of these components.', 'offline-ai-chatbot' ); ?></em></p>

    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <input type="hidden" name="action" value="oac_save_model_settings">
        <?php wp_nonce_field( 'oac_save_model_settings_nonce', 'oac_nonce_field_settings' ); ?>

        <table class="form-table">
            <tr valign="top">
                <th scope="row"><label for="oac_llama_cpp_path"><?php esc_html_e( 'Path to llama.cpp executable', 'offline-ai-chatbot' ); ?></label></th>
                <td><input type="text" id="oac_llama_cpp_path" name="oac_llama_cpp_path" value="<?php echo esc_attr( get_option( 'oac_llama_cpp_path' ) ); ?>" size="80" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="oac_gguf_model_path"><?php esc_html_e( 'Path to GGUF model file', 'offline-ai-chatbot' ); ?></label></th>
                <td><input type="text" id="oac_gguf_model_path" name="oac_gguf_model_path" value="<?php echo esc_attr( get_option( 'oac_gguf_model_path' ) ); ?>" size="80" /></td>
            </tr>
        </table>

        <?php submit_button( __( 'Save Model Settings', 'offline-ai-chatbot' ) ); ?>
    </form>

    <hr>

    <h2><?php esc_html_e( 'Test Model', 'offline-ai-chatbot' ); ?></h2>
    <p><?php esc_html_e( 'Enter a prompt to test the model response. This will confirm if your paths are correct and the model is working.', 'offline-ai-chatbot' ); ?></p>
    <form method="post" action="">
        <input type="hidden" name="action" value="oac_test_model">
        <?php wp_nonce_field( 'oac_test_model_nonce', 'oac_nonce_field_test' ); ?>

        <table class="form-table">
            <tr valign="top">
                <th scope="row"><label for="oac_test_prompt"><?php esc_html_e( 'Prompt', 'offline-ai-chatbot' ); ?></label></th>
                <td><textarea id="oac_test_prompt" name="oac_test_prompt" rows="3" class="large-text"><?php echo isset( $_POST['oac_test_prompt'] ) ? esc_textarea( $_POST['oac_test_prompt'] ) : 'What is WordPress?'; ?></textarea></td>
            </tr>
        </table>

        <?php submit_button( __( 'Get Model Response', 'offline-ai-chatbot' ) ); ?>
    </form>

    <?php
    if ( isset( $_POST['action'] ) && $_POST['action'] === 'oac_test_model' ) {
        if ( ! isset( $_POST['oac_nonce_field_test'] ) || ! wp_verify_nonce( $_POST['oac_nonce_field_test'], 'oac_test_model_nonce' ) ) {
            wp_die( __( 'Security check failed.', 'offline-ai-chatbot' ) );
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'You do not have sufficient permissions to perform this action.', 'offline-ai-chatbot' ) );
        }

        if ( isset( $_POST['oac_test_prompt'] ) ) {
            $prompt = sanitize_textarea_field( $_POST['oac_test_prompt'] );
            echo '<h3>' . esc_html__( 'Model Response:', 'offline-ai-chatbot' ) . '</h3>';
            echo '<div style="background-color: #f7f7f7; border: 1px solid #ccc; padding: 10px; border-radius: 4px; white-space: pre-wrap; word-wrap: break-word;">';
            // Using nl2br to make newlines appear correctly in HTML output
            echo nl2br( esc_html( Offline_AI_Chatbot_Model::generate_response( $prompt ) ) );
            echo '</div>';
        }
    }
    ?>
</div>
