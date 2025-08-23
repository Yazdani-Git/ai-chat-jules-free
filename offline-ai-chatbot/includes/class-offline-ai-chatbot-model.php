<?php
/**
 * Interacts with the local AI model.
 *
 * @link       https://jules.agent
 * @since      0.1.0
 *
 * @package    Offline_AI_Chatbot
 * @subpackage Offline_AI_Chatbot/includes
 */
class Offline_AI_Chatbot_Model {

    /**
     * Generate a response from the model.
     *
     * @since    0.1.0
     * @param    string  $prompt The prompt to send to the model.
     * @return   string  The response from the model.
     */
    public static function generate_response( $prompt ) {
        $llama_path = get_option( 'oac_llama_cpp_path' );
        $model_path = get_option( 'oac_gguf_model_path' );
        $knowledge_base_path = wp_upload_dir()['basedir'] . '/offline-ai-chatbot-knowledge-base.txt';

        if ( empty( $llama_path ) || empty( $model_path ) ) {
            return __( 'Model paths are not configured. Please set them in the AI Chatbot settings.', 'offline-ai-chatbot' );
        }

        if ( ! is_executable( $llama_path ) ) {
            return sprintf(
                __( 'Error: The llama.cpp path (%s) is not executable. Please check the file permissions.', 'offline-ai-chatbot' ),
                '<code>' . esc_html($llama_path) . '</code>'
            );
        }

        if ( ! is_readable( $model_path ) ) {
            return sprintf(
                __( 'Error: The model file (%s) is not readable. Please check the file permissions.', 'offline-ai-chatbot' ),
                '<code>' . esc_html($model_path) . '</code>'
            );
        }

        // Basic prompt formatting for RAG
        $full_prompt = "You are a helpful AI assistant for a website. Use the following context to answer the user's question. If the answer is not in the context, say you don't know.\n\n";

        if ( is_readable( $knowledge_base_path ) ) {
            $knowledge = file_get_contents( $knowledge_base_path );
            // This is a very basic RAG implementation. A real one would use vector search.
            $full_prompt .= "### Context:\n" . substr($knowledge, 0, 3000) . "\n\n";
        }

        $full_prompt .= "### User Question:\n" . $prompt . "\n\n### AI Assistant Answer:";

        // Example command. Parameters might need adjustment. -c is context size.
        $command = sprintf(
            '%s --model %s --prompt %s --n-predict 128 --temp 0.7 --top-k 40 --top-p 0.9 --ctx-size 4096 2>/dev/null',
            escapeshellcmd( $llama_path ),
            escapeshellcmd( $model_path ),
            escapeshellarg( $full_prompt )
        );

        // Execute the command.
        // Using proc_open for better error handling and to avoid timeouts on long-running processes.
        $descriptorspec = array(
           0 => array("pipe", "r"),  // stdin
           1 => array("pipe", "w"),  // stdout
           2 => array("pipe", "w")   // stderr
        );

        $process = proc_open($command, $descriptorspec, $pipes);

        if (is_resource($process)) {
            fclose($pipes[0]); // We don't write to stdin

            $output = stream_get_contents($pipes[1]);
            fclose($pipes[1]);

            $error = stream_get_contents($pipes[2]);
            fclose($pipes[2]);

            proc_close($process);

            if (!empty($error)) {
                 return __( 'An error occurred while running the model:', 'offline-ai-chatbot' ) . "\n<pre>" . esc_html($error) . "</pre>";
            }

            // The actual response is often appended after the prompt, so we need to clean it up.
            $response_start = strpos($output, $full_prompt);
            if ($response_start !== false) {
                return substr($output, $response_start + strlen($full_prompt));
            }

            return $output;
        }

        return __( 'Failed to execute the model command.', 'offline-ai-chatbot' );
    }
}
