<?php
defined( 'ABSPATH' ) || exit;
get_header();
global $wp_query;
jn_part( 'page-hero', array(
	'eyebrow' => 'Search',
	'title'   => get_search_query() ? 'Results for “' . esc_html( get_search_query() ) . '”' : 'Search',
	'lead'    => sprintf( '%d result%s', (int) $wp_query->found_posts, 1 === (int) $wp_query->found_posts ? '' : 's' ),
) );
?>
<section class="section section--tight">
	<div class="wrap"><?php jn_part( 'lc-nav' ); ?></div>
</section>
<section class="section">
	<div class="wrap">
		<?php if ( have_posts() ) : ?>
			<div class="cards cards--3">
				<?php
				while ( have_posts() ) :
					the_post();
					'jn_project' === get_post_type() ? jn_part( 'card-project' ) : jn_part( 'card-article' );
				endwhile;
				?>
			</div>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<div class="empty-state">
				<?php echo jn_icon( 'search' ); // phpcs:ignore ?>
				<h2>Nothing found</h2>
				<p>Try a different word, or browse the Learning Centre topics.</p>
			</div>
			<?php jn_part( 'planned-topics' ); ?>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
