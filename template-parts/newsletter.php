<?php
defined( 'ABSPATH' ) || exit;
$compact = ! empty( $args['compact'] );
?>
<form class="form form--inline js-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-kind="subscribe">
	<?php if ( ! $compact ) : ?>
		<p class="form__intro"><?php echo esc_html( jn_opt( 'newsletter_intro' ) ); ?></p>
	<?php else : ?>
		<p class="form__intro small"><?php echo esc_html( jn_opt( 'newsletter_intro' ) ); ?></p>
	<?php endif; ?>
	<?php jn_form_hidden( 'subscribe' ); ?>
	<div class="form__row form__row--inline">
		<label class="screen-reader-text" for="nl-email-<?php echo $compact ? 'f' : 'm'; ?>">Email address</label>
		<input id="nl-email-<?php echo $compact ? 'f' : 'm'; ?>" type="email" name="email" required placeholder="Your email address" autocomplete="email">
		<button class="btn btn--gold" type="submit">Subscribe</button>
	</div>
	<div class="form__status" role="status" aria-live="polite"></div>
	<?php jn_form_feedback( 'subscribe' ); ?>
</form>
