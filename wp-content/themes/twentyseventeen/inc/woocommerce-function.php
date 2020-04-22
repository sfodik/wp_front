<?php
/**
 * Custom woocommerce function for this theme
 *
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 */

if ( class_exists( 'WooCommerce' ) ) {
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
	add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 11 );

	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
	add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 30 );

	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
}
