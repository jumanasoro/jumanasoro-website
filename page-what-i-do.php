<?php
defined( 'ABSPATH' ) || exit;
get_header();
the_post();
$services = get_posts( array( 'post_type' => 'jn_service', 'posts_per_page' => 20, 'orderby' => 'menu_order', 'order' => 'ASC' ) );

jn_part( 'page-hero', array(
	'eyebrow' => 'What I do · Consultancy and professional services',
	'title'   => 'Practical solutions for organisations, businesses and development initiatives',
	'lead'    => 'Six connected service areas, delivered personally or with trusted specialists through Juma Nasoro &amp; Co.',
) );
?>
<section class="section section--tight">
	<div class="wrap services-intro">
		<div class="prose"><?php the_content(); ?></div>
		<nav class="service-index" aria-label="Services on this page">
			<?php foreach ( $services as $s ) : ?>
				<?php $anchor = jn_meta( 'anchor', $s->ID ) ? jn_meta( 'anchor', $s->ID ) : $s->post_name; ?>
				<a href="#<?php echo esc_attr( $anchor ); ?>"><span><?php echo esc_html( jn_meta( 'number', $s->ID ) ); ?></span> <?php echo esc_html( $s->post_title ); ?></a>
			<?php endforeach; ?>
		</nav>
	</div>
</section>

<section class="section section--services">
	<div class="wrap">
		<?php foreach ( $services as $s ) : ?>
			<?php jn_part( 'service-block', array( 'service' => $s, 'compact' => true ) ); ?>
		<?php endforeach; ?>
	</div>
</section>

<?php
jn_part( 'cta-band', array(
	'title'  => 'Not sure which service fits?',
	'text'   => 'Most challenges cross more than one area. Describe yours and I will suggest where to start.',
	'button' => 'Book a Consultation',
) );
get_footer();
