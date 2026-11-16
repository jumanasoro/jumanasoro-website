<?php
/**
 * Custom content types: projects, services, automation examples, timeline,
 * resources, enquiries and subscribers.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', function () {

	register_post_type( 'jn_project', array(
		'labels'       => array(
			'name'          => 'Projects & Ventures',
			'singular_name' => 'Portfolio item',
			'add_new'       => 'Add new',
			'add_new_item'  => 'Add portfolio item',
			'edit_item'     => 'Edit portfolio item',
			'all_items'     => 'All portfolio items',
			'menu_name'     => 'Projects & Ventures',
		),
		'public'       => true,
		'has_archive'  => 'projects',
		'rewrite'      => array( 'slug' => 'projects', 'with_front' => false ),
		'menu_icon'    => 'dashicons-portfolio',
		'menu_position' => 5,
		'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'revisions' ),
		'show_in_rest' => true,
	) );

	register_taxonomy( 'jn_section', 'jn_project', array(
		'labels'            => array(
			'name'          => 'Portfolio sections',
			'singular_name' => 'Portfolio section',
			'add_new'       => 'Add new',
		),
		'hierarchical'      => true,
		'public'            => true,
		'show_admin_column' => true,
		'rewrite'           => array( 'slug' => 'portfolio', 'with_front' => false ),
		'show_in_rest'      => true,
	) );

	register_post_type( 'jn_service', array(
		'labels'             => array(
			'name'          => 'Services',
			'singular_name' => 'Service',
			'add_new'       => 'Add new',
			'add_new_item'  => 'Add service',
			'edit_item'     => 'Edit service',
		),
		'public'             => false,
		'show_ui'            => true,
		'show_in_rest'       => true,
		'menu_icon'          => 'dashicons-hammer',
		'menu_position'      => 6,
		'supports'           => array( 'title', 'excerpt', 'page-attributes' ),
	) );

	register_post_type( 'jn_automation', array(
		'labels'        => array(
			'name'          => 'Automation examples',
			'singular_name' => 'Automation example',
			'add_new'       => 'Add new',
			'add_new_item'  => 'Add automation example',
			'edit_item'     => 'Edit automation example',
		),
		'public'        => false,
		'show_ui'       => true,
		'show_in_menu'  => 'edit.php?post_type=jn_service',
		'show_in_rest'  => true,
		'supports'      => array( 'title', 'page-attributes' ),
	) );

	register_post_type( 'jn_milestone', array(
		'labels'        => array(
			'name'          => 'Timeline',
			'singular_name' => 'Milestone',
			'add_new'       => 'Add new',
			'add_new_item'  => 'Add milestone',
			'edit_item'     => 'Edit milestone',
		),
		'public'        => false,
		'show_ui'       => true,
		'show_in_rest'  => true,
		'menu_icon'     => 'dashicons-backup',
		'menu_position' => 7,
		'supports'      => array( 'title', 'excerpt', 'page-attributes' ),
	) );

	register_post_type( 'jn_resource', array(
		'labels'       => array(
			'name'          => 'Resources',
			'singular_name' => 'Resource',
			'add_new'       => 'Add new',
			'add_new_item'  => 'Add resource',
			'edit_item'     => 'Edit resource',
			'menu_name'     => 'Resource library',
		),
		'public'       => true,
		'has_archive'  => 'learning-centre/resources',
		'rewrite'      => array( 'slug' => 'learning-centre/resources', 'with_front' => false ),
		'menu_icon'    => 'dashicons-download',
		'menu_position' => 6,
		'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ),
		'taxonomies'   => array( 'category' ),
		'show_in_rest' => true,
	) );

	// Private records created by the site's forms.
	foreach ( array(
		'jn_enquiry'    => array( 'Enquiries', 'Enquiry', 'dashicons-email-alt' ),
		'jn_subscriber' => array( 'Subscribers', 'Subscriber', 'dashicons-groups' ),
	) as $type => $l ) {
		register_post_type( $type, array(
			'labels'          => array(
				'name'          => $l[0],
				'singular_name' => $l[1],
				'add_new'       => 'Add new',
				'edit_item'     => 'View ' . strtolower( $l[1] ),
			),
			'public'          => false,
			'show_ui'         => true,
			'menu_icon'       => $l[2],
			'menu_position'   => 3,
			'supports'        => array( 'title' ),
			'capability_type' => 'post',
			'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap'    => true,
		) );
	}
} );

// Show planned (draft) articles alongside the categories used by resources.
add_action( 'pre_get_posts', function ( $q ) {
	if ( is_admin() || ! $q->is_main_query() ) {
		return;
	}
	if ( $q->is_post_type_archive( 'jn_project' ) || $q->is_tax( 'jn_section' ) ) {
		$q->set( 'posts_per_page', 60 );
		$q->set( 'orderby', array( 'menu_order' => 'ASC', 'date' => 'DESC' ) );
	}
	if ( $q->is_post_type_archive( 'jn_resource' ) ) {
		$q->set( 'posts_per_page', 30 );
	}
	if ( $q->is_search() ) {
		$scope = isset( $_GET['scope'] ) ? sanitize_key( wp_unslash( $_GET['scope'] ) ) : ''; // phpcs:ignore
		$q->set( 'post_type', 'learning' === $scope ? array( 'post', 'jn_resource' ) : array( 'post', 'jn_resource', 'jn_project', 'page' ) );
	}
	if ( $q->is_category() ) {
		$q->set( 'post_type', array( 'post', 'jn_resource' ) );
	}
} );
