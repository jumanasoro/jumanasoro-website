<?php
defined( 'ABSPATH' ) || exit;
$eyebrow = $args['eyebrow'] ?? '';
$title   = $args['title'] ?? get_the_title();
$lead    = $args['lead'] ?? '';
$dark    = ! empty( $args['dark'] );
?>
<section class="page-hero<?php echo $dark ? ' page-hero--dark' : ''; ?>">
	<div class="wrap page-hero__inner">
		<?php if ( $eyebrow ) : ?><p class="eyebrow<?php echo $dark ? ' eyebrow--gold' : ''; ?>"><?php echo esc_html( $eyebrow ); ?></p><?php endif; ?>
		<h1 class="page-hero__title"><?php echo wp_kses_post( $title ); ?></h1>
		<?php if ( $lead ) : ?><p class="page-hero__lead"><?php echo wp_kses_post( $lead ); ?></p><?php endif; ?>
		<?php if ( ! empty( $args['actions'] ) ) : ?>
			<div class="hero__actions"><?php echo $args['actions']; // phpcs:ignore ?></div>
		<?php endif; ?>
	</div>
</section>
