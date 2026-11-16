<?php defined( 'ABSPATH' ) || exit; ?><!doctype html>
<html class="no-js" <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="<?php echo esc_url( JN_URI . '/assets/img/favicon.svg' ); ?>" type="image/svg+xml">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main">Skip to content</a>
<header class="site-header" id="top">
	<div class="wrap site-header__inner">
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( jn_opt( 'name' ) ); ?>, home">
			<span class="brand__mark" aria-hidden="true">JN</span>
			<span class="brand__name"><?php echo esc_html( jn_opt( 'name' ) ); ?></span>
		</a>
		<nav class="nav" id="site-nav" aria-label="Main">
			<?php jn_primary_menu(); ?>
			<a class="btn btn--gold nav__cta" href="<?php echo esc_url( jn_page_url( 'contact' ) ); ?>#form">Work With Me</a>
		</nav>
		<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav">
			<span class="nav-toggle__open"><?php echo jn_icon( 'menu' ); // phpcs:ignore ?></span>
			<span class="nav-toggle__close"><?php echo jn_icon( 'close' ); // phpcs:ignore ?></span>
			<span class="screen-reader-text">Menu</span>
		</button>
	</div>
</header>
<main id="main" class="site-main">
