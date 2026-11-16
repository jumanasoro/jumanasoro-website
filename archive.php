<?php
/** Category / generic archive (Learning Centre topics). */
defined( 'ABSPATH' ) || exit;
get_header();
$term = get_queried_object();
jn_part( 'page-hero', array(
	'eyebrow' => 'The Learning Centre',
	'title'   => esc_html( single_term_title( '', false ) ? single_term_title( '', false ) : get_the_archive_title() ),
	'lead'    => $term && ! empty( $term->description ) ? esc_html( $term->description ) : '',
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
					jn_part( 'card-article' );
				endwhile;
				?>
			</div>
			<?php the_posts_pagination( array( 'mid_size' => 1 ) ); ?>
		<?php else : ?>
			<div class="empty-state">
				<?php echo jn_icon( 'idea' ); // phpcs:ignore ?>
				<h2>Nothing published in this topic yet</h2>
				<p>New articles and resources are being prepared. Subscribe to be told when they are published.</p>
				<?php jn_part( 'newsletter', array( 'compact' => true ) ); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
