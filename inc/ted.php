<?php
/*
 * TED Player embed code based on Jetpack 7.2.1 with minor modifications by me. All credit goes to Jetpack authors.
 * http://www.ted.com
 *
 * http://www.ted.com/talks/view/id/210
 * http://www.ted.com/talks/marc_goodman_a_vision_of_crimes_in_the_future.html
 * [ted id="210" lang="en"]
 * [ted id="http://www.ted.com/talks/view/id/210" lang="en"]
 * [ted id=1539 lang=fr width=560]
 */

wp_oembed_add_provider( '!https?://(www\.)?ted.com/talks/view/id/.+!i', 'http://www.ted.com/talks/oembed.json', true );
wp_oembed_add_provider( '!https?://(www\.)?ted.com/talks/[a-zA-Z\-\_]+\.html!i', 'http://www.ted.com/talks/oembed.json', true );

function jetpack_shortcode_get_ted_id( $atts ) {
	return ( ! empty( $atts['id'] ) ? $atts['id'] : 0 );
}

add_shortcode( 'ted', 'shortcode_ted' );
function shortcode_ted( $atts ) {
	global $wp_embed;

	$tte_options = get_option( 'tte_settings' ); // Gets TEDTalks Embedder settings if any
	
	$defaults = array(
		'id'     => '',
		'width'  => $tte_options['tte_width'],
		'height' => $tte_options['tte_height'],
		'lang'   => ( is_string( $tte_options['tte_lang'] ) ) ? $tte_options['tte_lang'] : 'en',
	);
	$atts     = shortcode_atts( $defaults, $atts, 'ted' );

	if ( empty( $atts['id'] ) ) {
		return '<!-- Missing TED ID -->';
	}

	$url = '';
	if ( preg_match( '#^[\d]+$#', $atts['id'], $matches ) ) {
		$url = 'http://ted.com/talks/view/id/' . $matches[0];
	} elseif ( preg_match( '#^https?://(www\.)?ted\.com/talks/view/id/[0-9]+$#', $atts['id'], $matches ) ) {
		$url = $matches[0];
	}

	unset( $atts['id'] );

	// Set width only if provided in shortcode or settings page. If no value provided WP will calculate the correct one.
	$args = array();
	if ( is_numeric( $atts['width'] ) ) {
		$args['width'] = $atts['width'];
	}

	// Set height only if provided in shortcode or settings page. If no value provided WP will calculate the correct one.
	if ( is_numeric( $atts['height'] ) ) {
		$args['height'] = $atts['height'];
	}

	if ( ! empty( $atts['lang'] ) ) {
		$args['lang'] = sanitize_key( $atts['lang'] );
		add_filter( 'oembed_fetch_url', 'ted_filter_oembed_fetch_url', 10, 3 );
	}

	$retval = $wp_embed->shortcode( $args, $url );
	remove_filter( 'oembed_fetch_url', 'ted_filter_oembed_fetch_url', 10 );

	return $retval;
}

/**
 * Filter the request URL to also include the $lang parameter
 */
function ted_filter_oembed_fetch_url( $provider, $url, $args ) {
	return add_query_arg( 'lang', $args['lang'], $provider );
}
