<?php
defined( 'ABSPATH' ) || exit;
get_header();
the_post();
$cv = (int) jn_opt( 'cv_id', 0 );
?>
<section class="page-hero page-hero--about">
	<div class="wrap about-hero">
		<div>
			<p class="eyebrow">About</p>
			<h1 class="page-hero__title"><?php echo esc_html( jn_opt( 'name' ) ); ?></h1>
			<p class="page-hero__lead"><?php echo esc_html( jn_opt( 'positioning' ) ); ?></p>
			<p class="hero__loc"><?php echo jn_icon( 'pin' ); // phpcs:ignore ?> <?php echo esc_html( jn_opt( 'location' ) ); ?></p>
		</div>
		<?php echo jn_portrait( 'portrait about-hero__portrait' ); // phpcs:ignore ?>
	</div>
</section>

<section class="section">
	<div class="wrap wrap--narrow prose prose--about">
		<?php the_content(); ?>
	</div>
</section>

<?php $milestones = get_posts( array( 'post_type' => 'jn_milestone', 'posts_per_page' => 50, 'orderby' => 'menu_order', 'order' => 'ASC' ) ); ?>
<?php if ( $milestones ) : ?>
<section class="section section--timeline" id="timeline">
	<div class="wrap">
		<?php jn_section_head( 'Professional timeline', 'Milestones', 'Education, certifications, roles, projects and ventures.' ); ?>
		<ol class="timeline">
			<?php foreach ( $milestones as $m ) : ?>
				<li class="timeline__item reveal">
					<div class="timeline__when"><?php echo esc_html( jn_meta( 'when', $m->ID ) ); ?></div>
					<div class="timeline__dot" aria-hidden="true"></div>
					<div class="timeline__body">
						<span class="milestone__kind"><?php echo esc_html( ucfirst( jn_meta( 'kind', $m->ID ) ) ); ?></span>
						<h3><?php echo esc_html( $m->post_title ); ?></h3>
						<?php if ( jn_meta( 'organisation', $m->ID ) ) : ?><p class="timeline__org"><?php echo esc_html( jn_meta( 'organisation', $m->ID ) ); ?></p><?php endif; ?>
						<?php if ( $m->post_excerpt ) : ?><p><?php echo esc_html( $m->post_excerpt ); ?></p><?php endif; ?>
					</div>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>
<?php endif; ?>

<section class="section section--cv">
	<div class="wrap cv-box reveal">
		<div>
			<p class="eyebrow">Curriculum vitae</p>
			<h2 class="section-title">Download my CV</h2>
			<p><?php echo $cv ? 'A full record of my education, roles and experience.' : 'My full CV will be available to download here shortly. In the meantime, I am happy to send it on request.'; ?></p>
		</div>
		<div>
			<?php if ( $cv && wp_get_attachment_url( $cv ) ) : ?>
				<a class="btn btn--primary btn--lg" href="<?php echo esc_url( wp_get_attachment_url( $cv ) ); ?>" download><?php echo jn_icon( 'download' ); // phpcs:ignore ?> Download CV</a>
			<?php else : ?>
				<a class="btn btn--ghost btn--lg" href="<?php echo esc_url( jn_page_url( 'contact' ) ); ?>?type=other#form">Request my CV</a>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php
jn_part( 'cta-band' );
get_footer();
