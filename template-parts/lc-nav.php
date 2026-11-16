<?php
/** Learning Centre search + category navigation. */
defined( 'ABSPATH' ) || exit;
$current = is_category() ? get_queried_object_id() : 0;
$cats    = get_categories( array( 'hide_empty' => false, 'exclude' => array( (int) get_option( 'default_category' ) ) ) );
?>
<div class="lc-nav">
	<form class="search-form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<label class="screen-reader-text" for="lc-search">Search the Learning Centre</label>
		<?php echo jn_icon( 'search' ); // phpcs:ignore ?>
		<input id="lc-search" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="Search articles, guides and resources">
		<input type="hidden" name="scope" value="learning">
		<button class="btn btn--primary" type="submit">Search</button>
	</form>
	<nav class="chips chips--nav" aria-label="Learning Centre categories">
		<a class="chip<?php echo ( is_home() ) ? ' is-active' : ''; ?>" href="<?php echo esc_url( jn_page_url( 'learning-centre' ) ); ?>">All</a>
		<?php foreach ( $cats as $c ) : ?>
			<a class="chip<?php echo $current === $c->term_id ? ' is-active' : ''; ?>" href="<?php echo esc_url( get_category_link( $c ) ); ?>"><?php echo esc_html( $c->name ); ?></a>
		<?php endforeach; ?>
		<a class="chip chip--alt<?php echo is_post_type_archive( 'jn_resource' ) ? ' is-active' : ''; ?>" href="<?php echo esc_url( get_post_type_archive_link( 'jn_resource' ) ); ?>"><?php echo jn_icon( 'download' ); // phpcs:ignore ?> Resource library</a>
	</nav>
</div>
