<?php
/**
 * Plugin Name: Offline AI Chatbot
 * Plugin URI:  https://github.com/jules-agent/offline-ai-chatbot
 * Description: A free and open-source AI chatbot plugin for WordPress that runs entirely on your server without paid APIs.
 * Version:     0.1.0
 * Author:      Jules
 * Author URI:  https://jules.agent
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: offline-ai-chatbot
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Currently plugin version.
 */
define( 'OAC_VERSION', '0.1.0' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-offline-ai-chatbot.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.1.0
 */
function run_offline_ai_chatbot() {

    $plugin = new Offline_AI_Chatbot();
    $plugin->run();

}
run_offline_ai_chatbot();
