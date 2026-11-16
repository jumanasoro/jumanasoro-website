<?php
defined( 'ABSPATH' ) || exit;
$title  = $args['title'] ?? 'Have an Idea, Challenge or Opportunity Worth Exploring?';
$text   = $args['text'] ?? 'I work with organisations, businesses, researchers, funders, development partners and entrepreneurs. If you are building something that matters, let\'s talk about how we might build it together.';
$button = $args['button'] ?? 'Let\'s Work Together';
$url    = $args['url'] ?? jn_page_url( 'contact' ) . '#form';
?>
<section class="section cta-band">
	<div class="wrap cta-band__inner reveal">
		<div>
			<p class="eyebrow eyebrow--gold">Collaboration</p>
			<h2 class="cta-band__title"><?php echo esc_html( $title ); ?></h2>
			<p class="cta-band__text"><?php echo esc_html( $text ); ?></p>
		</div>
		<div class="cta-band__actions">
			<a class="btn btn--gold btn--lg" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $button ); ?> <?php echo jn_icon( 'arrow' ); // phpcs:ignore ?></a>
			<?php if ( $w = jn_whatsapp_url( 'Hello Juma, I would like to talk about…' ) ) : ?>
				<a class="btn btn--ghost-light" href="<?php echo esc_url( $w ); ?>" target="_blank" rel="noopener"><?php echo jn_icon( 'whatsapp' ); // phpcs:ignore ?> WhatsApp</a>
			<?php endif; ?>
			<?php if ( $b = jn_opt( 'booking_url' ) ) : ?>
				<a class="btn btn--ghost-light" href="<?php echo esc_url( $b ); ?>" target="_blank" rel="noopener"><?php echo jn_icon( 'clock' ); // phpcs:ignore ?> Book a call</a>
			<?php endif; ?>
		</div>
	</div>
</section>
