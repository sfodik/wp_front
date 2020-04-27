<?php
/**
 * Twenty Seventeen Child functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 */


function child_theme_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}

add_action( 'wp_enqueue_scripts', 'child_theme_styles' );

function rtl_style() {

	wp_register_style( 'style-rtl', get_theme_file_uri( '/css/rtl.css', _FILE_ ) );

	if ( is_rtl() ) {
		wp_enqueue_style( 'style-rtl' );
	}

}

add_action( 'wp_enqueue_scripts', 'rtl_style' );

/**
* Woocommerce functions
*/

require_once( get_theme_file_path( '/inc/woocommerce-function.php' ) );


/**
* Custom post types
*/

require_once( get_theme_file_path( '/inc/custom-post-types.php' ) );


