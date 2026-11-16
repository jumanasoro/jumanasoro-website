<?php
defined( 'ABSPATH' ) || exit;
get_header();
the_post();
$services = get_posts( array( 'post_type' => 'jn_service', 'posts_per_page' => 20, 'orderby' => 'menu_order', 'order' => 'ASC' ) );

jn_part( 'page-hero', array(
	'eyebrow' => 'Independent consultancy practice',
	'title'   => 'Juma Nasoro &amp; Co',
	'lead'    => 'Programme, fundraising, enterprise and digital transformation support for organisations that want to build something that lasts.',
	'actions' => '<a class="btn btn--primary btn--lg" href="#consult">Book a Consultation ' . jn_icon( 'arrow' ) . '</a>',
) );
?>
<section class="section section--tight">
	<div class="wrap wrap--narrow prose prose--lead"><?php the_content(); ?></div>
</section>

<section class="section section--clients">
	<div class="wrap split">
		<div>
			<?php jn_section_head( 'Who we work with', 'Clients the practice is built for', '' ); ?>
			<ul class="tag-cloud">
				<?php foreach ( array( 'NGOs', 'CBOs', 'SMEs', 'Startups', 'Social enterprises', 'Research organisations', 'Development institutions', 'Foundations', 'Entrepreneurs', 'Businesses seeking digital transformation' ) as $c ) : ?>
					<li><?php echo esc_html( $c ); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<div>
			<?php jn_section_head( 'How we help', 'What clients come to us for', '' ); ?>
			<ul class="tick-list tick-list--cols">
				<?php foreach ( array( 'Develop projects and programmes', 'Mobilise resources', 'Strengthen organisations', 'Build enterprises', 'Improve operational systems', 'Automate workflows', 'Adopt AI', 'Develop digital infrastructure', 'Build strategic partnerships' ) as $h ) : ?>
					<li><?php echo jn_icon( 'check' ); // phpcs:ignore ?> <?php echo esc_html( $h ); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</section>

<section class="section section--svc-list">
	<div class="wrap">
		<?php jn_section_head( 'Services', 'Six practice areas', '' ); ?>
		<div class="svc-cards">
			<?php foreach ( $services as $s ) : ?>
				<?php
				$anchor = jn_meta( 'anchor', $s->ID ) ? jn_meta( 'anchor', $s->ID ) : $s->post_name;
				$url    = 'ai' === $anchor ? jn_page_url( 'ai-automation' ) : jn_page_url( 'what-i-do' ) . '#' . $anchor;
				?>
				<a class="svc-card reveal" href="<?php echo esc_url( $url ); ?>">
					<span class="svc-card__num"><?php echo esc_html( jn_meta( 'number', $s->ID ) ); ?></span>
					<?php echo jn_icon( jn_meta( 'icon', $s->ID ) ? jn_meta( 'icon', $s->ID ) : 'spark' ); // phpcs:ignore ?>
					<h3><?php echo esc_html( $s->post_title ); ?></h3>
					<p><?php echo esc_html( $s->post_excerpt ); ?></p>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="section section--packages">
	<div class="wrap packages reveal">
		<div>
			<p class="eyebrow">Coming soon</p>
			<h2 class="section-title">Service packages and digital products</h2>
			<p>Structured packages, such as fundraising readiness reviews, automation starter kits and toolkits for small organisations, are in development and will be listed here. If you need something now, a consultation is the best place to start.</p>
		</div>
		<a class="btn btn--ghost" href="<?php echo esc_url( get_post_type_archive_link( 'jn_project' ) ); ?>?section=ventures">See ventures in progress</a>
	</div>
</section>

<section class="section section--form" id="consult">
	<div class="wrap form-layout">
		<div class="form-layout__aside">
			<p class="eyebrow">Consultation request</p>
			<h2 class="section-title">Book a Consultation</h2>
			<p>Describe the assignment, challenge or opportunity. I will reply to arrange a first conversation.</p>
		</div>
		<?php jn_part( 'form-enquiry', array( 'type' => 'consultancy', 'button' => 'Request a consultation' ) ); ?>
	</div>
</section>
<?php
get_footer();
