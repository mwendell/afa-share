<?php
/**
 * AFA Share
 *
 * PHP version 8.0.0
 *
 * @category WordPress_Plugin
 * @package  afa-share
 * @author   Michael Wendell <mwendell@kwyjibo.com>
 * @license  GPLv3 <https://www.gnu.org/licenses/gpl-3.0.en.html>
 * @link     https://github.com/mwendell/afa-share/
 * @since    2025-04-09
 *
 * @wordpress-plugin
 * Plugin Name: AFA Share
 * Plugin URI:  https://github.com/mwendell/afa-share/
 * Description: An extremely lightweight plugin designed to simply add sharing buttons and the associated meta tags needed for them.
 * Author:      Michael Wendell <mwendell@kwyjibo.com>
 * Version:     0.0.1
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Add Open Graph meta tags to the header.
 */
function afa_share_insert_tags() {
	echo "<!-- AFA-SHARE META-TAGS -->\r\n";
	$title = get_the_title();
	$url = get_permalink();
	$excerpt = get_the_excerpt();
	$name = get_bloginfo( 'name' );
	$type = ( is_single() ) ? 'article' : 'website';
	if ( get_post_type() == 'event' ) {
		$type = 'event';
	}
	echo "<meta property='og:title' content='{$title}' />\r\n";
	echo "<meta property='og:type' content='{$type}' />\r\n";
	echo "<meta property='og:url' content='{$url}' />\r\n";
	echo "<meta property='og:description' content='{$excerpt}' />\r\n";
	echo "<meta property='og:site_name' content='{$name}'>\r\n";

	if ( has_post_thumbnail() ) {
		$image_id = get_post_thumbnail_id();
		$image_array = wp_get_attachment_image_src( $image_id, 'full' );
		if ( $image_array && is_array( $image_array ) ) {
			$image = ( isset( $image_array[0] ) ) ? esc_url( $image_array[0] ) : false;
			$image_width = ( isset( $image_array[1] ) ) ? intval( $image_array[1] ) : false;
			$image_height = ( isset( $image_array[2] ) ) ? intval( $image_array[2] ) : false;
			if ( $image ) {
				echo "<meta property='og:image' content='{$image}' />\r\n";
				if ( is_numeric( $image_width ) ) {
					echo "<meta property='og:image:width' content='{$image_width}' />\r\n";
				}
				if ( is_numeric( $image_height ) ) {
					echo "<meta property='og:image:height' content='{$image_height}' />\r\n";
				}
				$alt_text = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
				if ( $alt_text ) {
					echo "<meta property='og:image:alt' content='{$alt_text}' />\r\n";
					echo "<meta name='twitter:image:alt' content='{$alt_text}'>\r\n";
				}
				echo "<meta name='twitter:card' content='summary_large_image'>\r\n";
			}
		}
	}

	/*
	if ( $type == 'event' ) {
		echo "<meta property='event:start_time' content='2025-04-10T19:00:00Z'>\r\n";
	}
	*/

}
add_action( 'wp_head', 'afa_share_insert_tags', 10 );