<?php
/**
 * Juma Nasoro theme bootstrap.
 *
 * Content lives in WordPress (pages, posts, custom post types and the
 * "Site Profile" settings screen). The theme only handles layout and features.
 */

defined( 'ABSPATH' ) || exit;

define( 'JN_VERSION', '1.0.0' );
define( 'JN_DIR', get_template_directory() );
define( 'JN_URI', get_template_directory_uri() );

require JN_DIR . '/inc/helpers.php';
require JN_DIR . '/inc/setup.php';
require JN_DIR . '/inc/content-types.php';
require JN_DIR . '/inc/meta-boxes.php';
require JN_DIR . '/inc/options.php';
require JN_DIR . '/inc/forms.php';
require JN_DIR . '/inc/seo.php';
require JN_DIR . '/inc/seed.php';
