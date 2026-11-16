<?php
defined( 'ABSPATH' ) || exit;
get_header();
?>
<section class="page-hero">
	<div class="wrap page-hero__inner">
		<p class="eyebrow">Error 404</p>
		<h1 class="page-hero__title">This page could not be found</h1>
		<p class="page-hero__lead">The link may be out of date, or the page may have moved.</p>
		<div class="hero__actions">
			<a class="btn btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>">Go to the homepage</a>
			<a class="btn btn--ghost" href="<?php echo esc_url( jn_page_url( 'contact' ) ); ?>">Contact me</a>
		</div>
		<form class="search-form search-form--404" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label class="screen-reader-text" for="s404">Search</label>
			<?php echo jn_icon( 'search' ); // phpcs:ignore ?>
			<input id="s404" type="search" name="s" placeholder="Search the site">
			<button class="btn btn--primary" type="submit">Search</button>
		</form>
	</div>
</section>
<?php
get_footer();
