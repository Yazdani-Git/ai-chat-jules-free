<?php
/**
 * Scrapes the content of the website.
 *
 * @link       https://jules.agent
 * @since      0.1.0
 *
 * @package    Offline_AI_Chatbot
 * @subpackage Offline_AI_Chatbot/includes
 */
class Offline_AI_Chatbot_Scraper {

    /**
     * Scrape the content and save it to a file.
     *
     * @since    0.1.0
     */
    public static function scrape() {
        $content = self::get_content();
        self::save_content( $content );
    }

    /**
     * Get content from all posts and pages.
     *
     * @since    0.1.0
     * @return   string  The combined content of all posts and pages.
     */
    private static function get_content() {
        $args = array(
            'post_type'      => array( 'post', 'page' ),
            'post_status'    => 'publish',
            'posts_per_page' => -1,
        );

        $query = new WP_Query( $args );
        $content = '';

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $post_content = get_the_content();
                // Basic cleaning
                $post_content = strip_tags( $post_content );
                $post_content = preg_replace( '/\s+/', ' ', $post_content ); // Replace multiple whitespace with single space
                $content .= get_the_title() . "\n" . trim( $post_content ) . "\n\n---\n\n";
            }
        }

        wp_reset_postdata();

        return $content;
    }

    /**
     * Save the scraped content to a file.
     *
     * @since    0.1.0
     * @param    string  $content The content to save.
     */
    private static function save_content( $content ) {
        $upload_dir = wp_upload_dir();
        $file_path = $upload_dir['basedir'] . '/offline-ai-chatbot-knowledge-base.txt';
        file_put_contents( $file_path, $content );
    }
}
