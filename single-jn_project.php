<?php
/** One portfolio item: project, idea or venture. */
defined( 'ABSPATH' ) || exit;
get_header();
the_post();
$pid      = get_the_ID();
$section  = jn_project_section( $pid );
$status   = jn_meta( 'status' );
$proposed = jn_is_proposed_status( $status ) || 'proposal' === jn_meta( 'contribution' );
$contrib  = jn_contribution_labels()[ jn_meta( 'contribution' ) ] ?? '';
$facts    = array_filter( array(
	'Status'        => jn_statuses()[ $status ] ?? '',
	'My role'       => jn_meta( 'role' ),
	'Contribution'  => $contrib,
	'Organisation'  => jn_meta( 'organisation' ),
	'Period'        => jn_meta( 'period' ),
	'Location'      => jn_meta( 'location' ),
) );
$initiative = array_filter( array(
	'The problem or opportunity'      => jn_meta( 'problem' ),
	'Proposed solution'               => jn_meta( 'solution' ),
	'Vision and intended impact'      => jn_meta( 'vision' ),
	'Potential implementation model'  => jn_meta( 'model' ),
	'Resource requirements'           => jn_meta( 'resources' ),
) );
?>
<article class="project">
	<header class="page-hero page-hero--project">
		<div class="wrap">
			<p class="eyebrow"><a href="<?php echo esc_url( get_post_type_archive_link( 'jn_project' ) ); ?>">Projects, Initiatives &amp; Ventures</a><?php echo $section ? ' · ' . esc_html( $section->name ) : ''; ?></p>
			<h1 class="page-hero__title"><?php the_title(); ?></h1>
			<?php if ( has_excerpt() ) : ?><p class="page-hero__lead"><?php echo esc_html( get_the_excerpt() ); ?></p><?php endif; ?>
			<div class="card__meta"><?php echo jn_status_badge(); // phpcs:ignore ?></div>
		</div>
	</header>

	<?php if ( $proposed ) : ?>
		<div class="wrap"><p class="proposal-note"><?php echo jn_icon( 'idea' ); // phpcs:ignore ?> <span>This is a proposed initiative. It has not yet been implemented, and it is shared to invite collaboration.</span></p></div>
	<?php endif; ?>

	<div class="wrap project__layout">
		<div class="project__main">
			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="project__image"><?php the_post_thumbnail( 'jn-wide' ); ?></figure>
			<?php endif; ?>
			<div class="prose"><?php the_content(); ?></div>

			<?php if ( $initiative ) : ?>
				<div class="initiative">
					<?php foreach ( $initiative as $label => $text ) : ?>
						<section class="initiative__item">
							<h2 class="service__label"><?php echo esc_html( $label ); ?></h2>
							<p><?php echo nl2br( esc_html( $text ) ); ?></p>
						</section>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php if ( jn_meta( 'outcomes' ) ) : ?>
				<section class="outcomes">
					<h2 class="h3">Verified results</h2>
					<ul class="tick-list">
						<?php foreach ( jn_lines( jn_meta( 'outcomes' ) ) as $o ) : ?>
							<li><?php echo jn_icon( 'check' ); // phpcs:ignore ?> <?php echo esc_html( $o ); ?></li>
						<?php endforeach; ?>
					</ul>
				</section>
			<?php endif; ?>
		</div>

		<aside class="project__aside">
			<?php if ( $facts ) : ?>
				<dl class="facts">
					<?php foreach ( $facts as $k => $v ) : ?>
						<div><dt><?php echo esc_html( $k ); ?></dt><dd><?php echo esc_html( $v ); ?></dd></div>
					<?php endforeach; ?>
				</dl>
			<?php endif; ?>
			<?php if ( jn_meta( 'highlights' ) ) : ?>
				<h2 class="service__label">Focus areas</h2>
				<ul class="tag-cloud tag-cloud--small">
					<?php foreach ( jn_lines( jn_meta( 'highlights' ) ) as $h ) : ?><li><?php echo esc_html( $h ); ?></li><?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<?php if ( jn_meta( 'partners' ) ) : ?>
				<h2 class="service__label">Potential partners</h2>
				<ul class="tick-list">
					<?php foreach ( jn_lines( jn_meta( 'partners' ) ) as $h ) : ?><li><?php echo jn_icon( 'link' ); // phpcs:ignore ?> <?php echo esc_html( $h ); ?></li><?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<?php echo jn_share_links(); // phpcs:ignore ?>
		</aside>
	</div>

	<section class="section section--form" id="interest">
		<div class="wrap form-layout">
			<div class="form-layout__aside">
				<p class="eyebrow"><?php echo $proposed ? 'Collaboration opportunity' : 'Get involved'; ?></p>
				<h2 class="section-title"><?php echo $proposed ? 'Help build this initiative' : 'Discuss this work'; ?></h2>
				<p><?php echo esc_html( jn_meta( 'collaboration' ) ? jn_meta( 'collaboration' ) : 'Interested in supporting, partnering on or learning more about this work? Send a short note.' ); ?></p>
			</div>
			<?php
			jn_part( 'form-enquiry', array(
				'type'       => $proposed ? 'venture' : 'partnership',
				'initiative' => get_the_title(),
				'button'     => $proposed ? 'Express interest' : 'Send message',
			) );
			?>
		</div>
	</section>
</article>
<?php
get_footer();
