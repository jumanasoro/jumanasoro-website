<?php
/**
 * Theme supports, menus, assets.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/editor.css' );

	add_image_size( 'jn-card', 800, 480, true );
	add_image_size( 'jn-wide', 1600, 800, true );
	add_image_size( 'jn-portrait', 800, 1000, true );

	register_nav_menus( array(
		'primary' => 'Main navigation',
		'footer'  => 'Footer links',
	) );
} );

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'jn-fonts', 'https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..700;1,9..144,300..600&family=Inter:wght@400;500;600&display=swap', array(), null );
	wp_enqueue_style( 'jn-main', JN_URI . '/assets/css/main.css', array(), JN_VERSION );
	wp_enqueue_script( 'jn-main', JN_URI . '/assets/js/main.js', array(), JN_VERSION, array( 'in_footer' => true, 'strategy' => 'defer' ) );

	// Drop block library CSS on pages that do not use blocks heavily? Keep: content uses core blocks.
	wp_dequeue_style( 'global-styles' );
	wp_dequeue_style( 'classic-theme-styles' );
} );

add_filter( 'wp_resource_hints', function ( $urls, $relation ) {
	if ( 'preconnect' === $relation ) {
		$urls[] = 'https://fonts.googleapis.com';
		$urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' );
	}
	return $urls;
}, 10, 2 );

// Remove emoji scripts and other head clutter for speed.
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );

add_filter( 'excerpt_length', function () {
	return 28;
} );
add_filter( 'excerpt_more', function () {
	return '…';
} );

// Comments are not part of this site.
add_action( 'init', function () {
	remove_post_type_support( 'post', 'comments' );
	remove_post_type_support( 'page', 'comments' );
} );
add_filter( 'comments_open', '__return_false', 20 );
add_filter( 'pings_open', '__return_false', 20 );
add_action( 'admin_menu', function () {
	remove_menu_page( 'edit-comments.php' );
} );

// Rename "Posts" to "Articles" so the dashboard reads like the site.
add_action( 'init', function () {
	global $wp_post_types;
	if ( isset( $wp_post_types['post'] ) ) {
		$l                  = $wp_post_types['post']->labels;
		$l->name            = 'Articles';
		$l->singular_name   = 'Article';
		$l->add_new_item    = 'Add new article';
		$l->edit_item       = 'Edit article';
		$l->all_items       = 'All articles';
		$l->menu_name       = 'Learning Centre';
		$l->name_admin_bar  = 'Article';
		$wp_post_types['post']->menu_icon = 'dashicons-welcome-learn-more';
	}
	global $wp_taxonomies;
	if ( isset( $wp_taxonomies['category'] ) ) {
		$wp_taxonomies['category']->labels->name      = 'Learning Centre categories';
		$wp_taxonomies['category']->labels->menu_name = 'Categories';
	}
}, 20 );

/** Fallback menu used until a menu is assigned in Appearance > Menus. */
function jn_default_menu_items() {
	return array(
		array( 'Home', home_url( '/' ) ),
		array( 'About', jn_page_url( 'about' ) ),
		array( 'What I Do', jn_page_url( 'what-i-do' ) ),
		array( 'Projects & Ventures', get_post_type_archive_link( 'jn_project' ) ),
		array( 'Learning Centre', jn_page_url( 'learning-centre' ) ),
		array( 'Contact', jn_page_url( 'contact' ) ),
	);
}

function jn_primary_menu() {
	if ( has_nav_menu( 'primary' ) ) {
		wp_nav_menu( array(
			'theme_location' => 'primary',
			'container'      => false,
			'menu_class'     => 'nav__list',
			'depth'          => 1,
		) );
		return;
	}
	$current = trailingslashit( home_url( add_query_arg( array() ) ) );
	echo '<ul class="nav__list">';
	foreach ( jn_default_menu_items() as $item ) {
		$active = untrailingslashit( $item[1] ) === untrailingslashit( strtok( $current, '?' ) );
		if ( ! $active && 'Home' !== $item[0] && 0 === strpos( strtok( $current, '?' ), trailingslashit( $item[1] ) ) ) {
			$active = true;
		}
		printf( '<li%3$s><a href="%1$s"%4$s>%2$s</a></li>', esc_url( $item[1] ), esc_html( $item[0] ), $active ? ' class="current-menu-item"' : '', $active ? ' aria-current="page"' : '' );
	}
	echo '</ul>';
}

add_filter( 'body_class', function ( $classes ) {
	if ( is_front_page() ) {
		$classes[] = 'is-home';
	}
	return $classes;
} );
