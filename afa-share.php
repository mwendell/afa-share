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

namespace AFAshare;

defined('WPINC') || die;
require_once __DIR__ . '/vendor/autoload.php';

/**
 * Add Open Graph meta tags to the header.
 */
function afa_share_tags() {
	$title = get_the_title();
	$url = get_permalink();
	$excerpt = get_the_excerpt();
	$name = get_bloginfo( 'name' );
	echo "<meta property='og:title' content='{$title}' />";
	echo "<meta property='og:type' content='article' />";
	echo "<meta property='og:url' content='{$url}' />";
	echo "<meta property='og:description' content='{$excerpt}' />";
	echo "<meta property='og:site_name' content='{$name}'>";

	if ( has_post_thumbnail() ) {
		$image_id = get_post_thumbnail_id();
		$image_array = wp_get_attachment_image_src( $image_id, 'full' );
		if ( $image && is_array( $image ) ) {
			$image = esc_url( $image[0] );
			echo "<meta property='og:image' content='{$image}' />";
			echo "<meta name='twitter:card' content='summary_large_image'>";
			$alt_text = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
			if ( $alt_text ) {
				echo "<meta name='twitter:image:alt' content='{$alt_text}'>";
			}
		}
	}
}
add_action( 'wp_head', 'afa_share_tags', 10 );