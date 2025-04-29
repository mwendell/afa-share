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
 * Add Open Graph and other meta tags to the header.
 */
function afa_share_header_tags() {

	if ( is_plugin_active( 'wordpress-seo/wp-seo.php' ) ) { return false; }

	echo "<!-- AFA-SHARE META-TAGS -->\r\n";
	$title = get_the_title();
	$url = get_permalink();
	$excerpt = get_the_excerpt();
	$source = get_bloginfo( 'name' );
	$type = ( is_single() ) ? 'article' : 'website';
	if ( get_post_type() == 'event' ) {
		$type = 'event';
	}

	echo "<meta property='og:title' content='{$title}' />\r\n";
	echo "<meta property='og:type' content='{$type}' />\r\n";
	echo "<meta property='og:url' content='{$url}' />\r\n";
	echo "<meta property='og:description' content='{$excerpt}' />\r\n";
	echo "<meta property='og:site_name' content='{$source}'>\r\n";

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
add_action( 'wp_head', 'afa_share_header_tags', 10 );

/**
 * Create the share buttons
 */
function afa_share_buttons( $args = array() ) {

	// SETTINGS AND BASE DATA

	$title = ( isset( $args['title'] ) ) ? $args['title'] : false;

	$link = ( isset( $args['link'] ) ) ? $args['link'] : false;

	$svg_shape = ( isset( $args['shape'] ) ) ? $args['shape'] : 'circle';

	$svg_bg = array(
		'circle'  => '<circle cx="12" cy="12" fill="[COLOR]" r="12"></circle>',
		'square'  => '<rect x="0" y="0" width="24" height="24" fill="[COLOR]"></rect>',
		'rounded' => '<rect x="2" y="2" width="20" height="20" fill="[COLOR]" stroke="[COLOR]" stroke-width="4px" rx="4px" ry="4px" stroke-linejoin="round"></rect>',
	);

	$svg_base = str_replace( '[BACKGROUND]', $svg_bg[$svg_shape], '<svg class="icon-svg" height="24" width="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">[BACKGROUND][LOGO]</svg>' );

	$style = 'display: inline-block; margin-left: 4px;';

	if ( isset( $args['direction'] ) && $args['direction'] != 'horizontal' ) {
		$style = 'margin-bottom: 4px;';
	}

	$output = '';

	// SHARE DATA FOR THIS POST
	$title = ( $title ) ? urlencode( $title ) : urlencode( get_the_title() );
	$link = ( $link ) ? urlencode( $link ) : urlencode( get_permalink() );
	$excerpt = urlencode( get_the_excerpt() );
	$source = urlencode( get_bloginfo( 'name' ) );
	$image = '';
	if ( has_post_thumbnail() ) {
		$image_id = get_post_thumbnail_id();
		$image_array = wp_get_attachment_image_src( $image_id, 'full' );
		if ( $image_array && is_array( $image_array ) && isset( $image_array[0] ) ) {
			$image = urlencode( $image_array[0] );
		}
	}

	$socials = array(
		'facebook' => array(
			'yes' => true,
			'url' => "https://www.facebook.com/sharer/sharer.php?u={$link}",
			'clr' => '#4267B2',
			'svg' => '<path fill="#fff" d="m13.079 19h-2.9v-7h-1.441v-2.408h1.442v-1.456c0-1.96.813-3.136 3.136-3.136h1.933v2.408h-1.2c-.91 0-.965.35-.965.966v1.218h2.183l-.257 2.408h-1.931z"></path>',
		),
		'xtwitter' => array(
			'yes' => true,
			'url' => "https://twitter.com/intent/tweet?url={$link}&text={$title}",
			'clr' => '#000000',
			'svg' => '<path fill="#fff" d="M15.073 6.873h2.054l-4.1 4.615 4.615 5.638h-3.592l-2.562-3.592-3.077 3.592H6.865l3.592-4.615L6.358 6.873h3.592l2.562 3.077zm-.515 9.231h1.023L9.435 7.904H8.404z"></path>',
		),
		'linkedin' => array(
			'yes' => true,
			'url' => "https://www.linkedin.com/shareArticle?url={$link}&title={$title}&summary={$excerpt}&source={$source}&mini=true",
			'clr' => '#0077b5',
			'svg' => '<path fill="#fff" d="M17.54 17.28H15.205V13.63C15.205 12.76 15.19 11.64 13.995 11.64 12.78 11.64 12.595 12.59 12.595 13.57V17.28H10.265V9.775H12.5V10.8H12.535C12.99 10.02 13.835 9.555 14.74 9.585 17.105 9.585 17.54 11.14 17.54 13.165L17.54 17.28ZM7.63 8.745C6.59 8.75 5.94 7.62 6.46 6.72 6.98 5.815 8.285 5.815 8.805 6.72 8.925 6.925 8.985 7.155 8.985 7.395 8.985 8.14 8.38 8.745 7.63 8.745M8.8 17.28H6.465V9.775H8.8V17.28Z"></path>',
		),
		'pinterest' => array(
			'yes' => false,
			'url' => "https://pinterest.com/pin/create/button/?url={$link}&amp;media={$image}",
			'clr' => '#ff0000',
			'svg' => '<path fill="#fff" d="m12.336 5c-3.822 0-5.754 2.744-5.754 5.025a3.065 3.065 0 0 0 1.652 3.066.279.279 0 0 0 .407-.2l.154-.644a.373.373 0 0 0 -.113-.448 2.341 2.341 0 0 1 -.532-1.582 3.812 3.812 0 0 1 3.961-3.849 3.009 3.009 0 0 1 3.346 3.08c0 2.323-1.022 4.283-2.547 4.283a1.253 1.253 0 0 1 -1.273-1.554 17.616 17.616 0 0 0 .713-2.856 1.081 1.081 0 0 0 -1.092-1.2c-.854 0-1.553.881-1.553 2.071a2.954 2.954 0 0 0 .266 1.274l-1.038 4.383a9.389 9.389 0 0 0 -.027 3.065.109.109 0 0 0 .2.042 8.737 8.737 0 0 0 1.457-2.631l.561-2.212a2.3 2.3 0 0 0 1.959 1.008c2.59 0 4.34-2.366 4.34-5.516a4.8 4.8 0 0 0 -5.087-4.605z"></path>',
		),
		'reddit' => array(
			'yes' => true,
			'url' => "https://www.reddit.com/submit?url={$link}&amp;title={$title}",
			'clr' => '#ff4500',
			'svg' => '<path fill="#fff" d="M18.828 12.108c0-.828-.672-1.5-1.5-1.5-.408 0-.768.156-1.032.42-1.02-.732-2.436-1.212-3.996-1.272l.684-3.204 2.22.468c.024.564 .492 1.02 1.068 1.02.588 0 1.068-.48 1.068-1.068s-.48-1.068-1.068-1.068c-.42 0-.78.24-.948.6l-2.484-.528c-.072-.012-.144 0-.204.036s-.096.096-.12.168l-.756 3.576c-1.596.048-3.024.516-4.056 1.272-.264-.252-.636-.42-1.032-.42-.828 0-1.5.672-1.5 1.5 0 .612.36 1.128.888 1.368-.024.144-.036.3-.036.456 0 2.304 2.676 4.164 5.988 4.164 3.312 0 5.988-1.86 5.988-4.164 0-.156-.012-.3-.036-.444.492-.24.864-.768.864-1.38zm-10.26 1.068c0-.588.48-1.068 1.068-1.068s1.068.48 1.068 1.068-.48 1.068-1.068 1.068-1.068-.48-1.068-1.068zm5.964 2.82c-.732.732-2.124.78-2.532.78-.408 0-1.812-.06-2.532-.78-.108-.108-.108-.288 0-.396.108-.108.288-.108.396 0 .456.456 1.44.624 2.148.624 .708 0 1.68-.168 2.148-.624.108-.108.288-.108.396 0 .084.12 .084.288-.024.396zm-.192-1.752c-.588 0-1.068-.48-1.068-1.068s.48-1.068 1.068-1.068 1.068.48 1.068 1.068-.48 1.068-1.068 1.068z"></path>',
		),
		'bluesky' => array(
			'yes' => true,
			'url' => "https://bsky.app/intent/compose?text={$title}%20{$link}",
			'mbl' => "bluesky://intent/compose?text={$title}",
			'clr' => '#097AFE',
			'svg' => '<path fill="#fff" d="M7.924 6.684C9.574 7.923 11.348 10.434 12 11.782 12.652 10.434 14.426 7.923 16.076 6.684 17.266 5.791 19.195 5.099 19.195 7.3 19.195 7.739 18.943 10.991 18.795 11.519 18.281 13.354 16.41 13.823 14.745 13.539 17.655 14.034 18.395 15.675 16.796 17.316 13.76 20.431 12.432 16.534 12.092 15.535 12.03 15.352 12 15.266 12 15.339 12 15.266 11.97 15.352 11.908 15.535 11.568 16.534 10.24 20.431 7.204 17.316 5.605 15.675 6.345 14.034 9.255 13.539 7.59 13.823 5.719 13.354 5.205 11.519 5.057 10.991 4.805 7.739 4.805 7.3 4.805 5.099 6.734 5.791 7.924 6.684Z"></path>',
		),
		'threads' => array(
			'yes' => false,
			'url' => "https://threads.net/intent/post?text={$link}",
			'clr' => '#333333',
			'svg' => '<path fill="#fff" d="M18 2h4l-8 9 9 11h-7l-5-7-6 7H2l7-9L1 2h7l5 6zm-1 18h2L7 4H5z"></path>',
		),
		'whatsapp' => array(
			'yes' => false,
			'url' => '',
			'mbl' => "whatsapp://send?text={$title}%20;{$link}?fwa",
			'clr' => '#25d366',
			'svg' => '<path d="M509 0A489 489 0 0 0 18 487a482 482 0 0 0 71 252L0 1000l272-86a493 493 0 0 0 237 60c271 0 491-218 491-487S780 0 509 0zm0 893a409 409 0 0 1-225-67l-157 49 51-150a401 401 0 0 1-78-238 408 408 0 0 1 818 0c0 224-184 406-409 406zm230-295a2222 2222 0 0 0-84-44c-11-4-20-7-28 5a748 748 0 0 1-42 48c-7 8-15 8-27 2-12-7-52-22-99-66a372 372 0 0 1-66-88c-7-13 0-20 7-26l19-21c7-7 9-12 13-20 5-8 3-15 0-22l-35-94c-9-25-20-21-27-21l-24-2c-8 0-22 2-34 14s-46 41-48 102 40 122 46 131c6 8 82 141 207 195 126 55 126 38 149 37 24-1 76-27 87-56 12-29 13-54 10-59s-11-9-24-15z"></path>',
		),
		'mailto' => array(
			'yes' => false,
			'url' => "mailto:?subject=I%20found%20this%20webpage&amp;body=Hi,%20I%20found%20this%20webpage%20and%20thought%20you%20might%20like%20it%20{$link}",
			'clr' => '',
			'svg' => '',
		),

	);

	// RENDER THE SHARE LINKS
	$output .= "<ul class='afa-share-widget-ul'>";
	foreach ( $socials as $key => $arr ) {
		if ( ! $arr['yes'] ) { continue; }
		$output .= "<li class='afa-share-widget-item' style='{$style}'>";
		$output .= "<a href='{$arr['url']}' data-platform='{$key}' target='_blank' class='afa-share afa-share-{$key}' data-action='share' aria-label='share page via {$key}'>";
		if ( $arr['svg'] && $arr['clr'] ) {
			$svg = str_replace( '[LOGO]', $arr['svg'], $svg_base );
			$svg = str_replace( '[COLOR]', $arr['clr'], $svg );
			$output .= $svg;
		} else {
			$output .= "<i class='afa-share-icon afa-share-icon-{$key}'></i>";
		}
		$output .= "</a></li>";
	}
	$output .= "</ul>";

	// ECHO OR RETURN FORMATTED SHARE LINKS
	if ( isset( $args['shortcode'] ) && $args['shortcode'] ) {
		return $output;
	} else {
		echo $output;
	}

}

function afa_share_buttons_shortcode( $atts = array() ) {
	$atts['shortcode'] = true;
	return afa_share_buttons( $atts );
}

add_shortcode( 'afa_share_buttons', 'afa_share_buttons_shortcode' );
