<?php
/**
* Custom post types for this theme
*
*
* @package WordPress
* @subpackage Twenty_Seventeen
* @since 1.0
*/

function book_init() {
	// set up books labels
	$labels = array(
		'name'               => __( 'Books', 'twentyseventeen' ),
		'singular_name'      => __( 'Book', 'twentyseventeen' ),
		'add_new'            => __( 'Add New Book', 'twentyseventeen' ),
		'add_new_item'       => __( 'Add New Book', 'twentyseventeen' ),
		'edit_item'          => __( 'Edit Book', 'twentyseventeen' ),
		'new_item'           => __( 'New Book', 'twentyseventeen' ),
		'all_items'          => __( 'All Books', 'twentyseventeen' ),
		'view_item'          => __( 'View Book', 'twentyseventeen' ),
		'search_items'       => __( 'Search Books', 'twentyseventeen' ),
		'not_found'          => __( 'No Books Found', 'twentyseventeen' ),
		'not_found_in_trash' => __( 'No Books found in Trash', 'twentyseventeen' ),
		'parent_item_colon'  => '',
		'menu_name'          => __( 'Books', 'twentyseventeen' ),
	);

	// register post type
	$args = array(
		'labels'          => $labels,
		'public'          => true,
		'has_archive'     => true,
		'show_ui'         => true,
		'capability_type' => 'post',
		'hierarchical'    => false,
		'rewrite'         => array( 'slug' => 'genre' ),
		'query_var'       => true,
		'menu_icon'       => 'dashicons-randomize',
		'supports'        => array(
			'title',
			'editor',
			'excerpt',
			'trackbacks',
			'custom-fields',
			'comments',
			'revisions',
			'thumbnail',
			'author',
			'page-attributes'
		)
	);
	register_post_type( 'books', $args );

	// register taxonomy
	register_taxonomy( __( 'Genre', 'twentyseventeen' ), 'books', array(
		'hierarchical' => true,
		'label'        => __( 'Category', 'twentyseventeen' ),
		'query_var'    => true,
		'rewrite'      => array( 'slug' => 'genre' )
	) );
}

add_action( 'init', 'book_init' );