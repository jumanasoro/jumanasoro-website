<?php
/** Resource library. */
defined( 'ABSPATH' ) || exit;
get_header();
jn_part( 'page-hero', array(
	'eyebrow' => 'The Learning Centre',
	'title'   => 'Resource Library',
	'lead'    => 'Templates, frameworks, toolkits and guides you can download and use: for grant proposals, fundraising, project management, business planning, MEAL, AI prompts and automation workflows.',
) );
?>
<section class="section section--tight">
	<div class="wrap"><?php jn_part( 'lc-nav' ); ?></div>
</section>
<section class="section">
	<div class="wrap">
		<?php if ( have_posts() ) : ?>
			<div class="resource-list">
				<?php
				while ( have_posts() ) :
					the_post();
					jn_part( 'card-resource' );
				endwhile;
				?>
			</div>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<div class="empty-state reveal">
				<?php echo jn_icon( 'download' ); // phpcs:ignore ?>
				<h2>The library is being stocked</h2>
				<p>Practical templates and guides will be published here, including grant proposal templates, fundraising frameworks, project management and MEAL tools, business planning templates, AI prompts and automation workflow guides. Subscribe to be told when the first ones are ready.</p>
				<?php jn_part( 'newsletter', array( 'compact' => true ) ); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
