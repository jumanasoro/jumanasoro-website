<?php
defined( 'ABSPATH' ) || exit;
$pid     = get_the_ID();
$section = jn_project_section( $pid );
$status  = jn_meta( 'status', $pid );
$contrib = jn_contribution_labels()[ jn_meta( 'contribution', $pid ) ] ?? '';
$search  = strtolower( wp_strip_all_tags( get_the_title() . ' ' . get_the_excerpt() . ' ' . jn_meta( 'highlights', $pid ) . ' ' . jn_meta( 'organisation', $pid ) ) );
?>
<article class="card card--project reveal" data-section="<?php echo esc_attr( $section ? $section->slug : '' ); ?>" data-status="<?php echo esc_attr( $status ); ?>" data-search="<?php echo esc_attr( $search ); ?>">
	<a class="card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php echo jn_card_media( $pid ); // phpcs:ignore ?>
		<?php if ( jn_is_proposed_status( $status ) ) : ?>
			<span class="card__flag">Proposed</span>
		<?php endif; ?>
	</a>
	<div class="card__body">
		<div class="card__meta">
			<?php echo jn_status_badge( $pid ); // phpcs:ignore ?>
			<?php if ( $section ) : ?><span class="card__section"><?php echo esc_html( $section->name ); ?></span><?php endif; ?>
		</div>
		<h3 class="card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<p class="card__text"><?php echo esc_html( get_the_excerpt() ); ?></p>
		<?php if ( $contrib || jn_meta( 'role', $pid ) ) : ?>
			<p class="card__role">
				<?php echo esc_html( jn_meta( 'role', $pid ) ? 'Role: ' . jn_meta( 'role', $pid ) : $contrib ); ?>
			</p>
		<?php endif; ?>
	</div>
</article>
