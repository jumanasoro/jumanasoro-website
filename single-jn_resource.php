<?php
/** One downloadable resource. */
defined( 'ABSPATH' ) || exit;
get_header();
the_post();
$rid    = get_the_ID();
$access = jn_meta( 'access' ) ? jn_meta( 'access' ) : 'free';
$url    = jn_resource_file_url( $rid );
$cats   = get_the_category();
// After an email-gated form without JavaScript, a signed token reveals the link.
$unlocked = false;
if ( isset( $_GET['jn_dl'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
	$parts    = explode( '.', sanitize_text_field( wp_unslash( $_GET['jn_dl'] ) ) ); // phpcs:ignore
	$unlocked = 2 === count( $parts ) && (int) $parts[0] === $rid && hash_equals( jn_download_sig( $rid ), $parts[1] );
}
jn_part( 'page-hero', array(
	'eyebrow' => 'Resource Library' . ( $cats ? ' · ' . esc_html( $cats[0]->name ) : '' ),
	'title'   => esc_html( get_the_title() ),
	'lead'    => esc_html( get_the_excerpt() ),
) );
?>
<section class="section">
	<div class="wrap project__layout">
		<div class="project__main prose"><?php the_content(); ?></div>
		<aside class="project__aside">
			<div class="download-box" id="form">
				<p class="eyebrow"><?php echo esc_html( jn_meta( 'format' ) ? jn_meta( 'format' ) : 'Download' ); ?></p>
				<?php if ( 'coming-soon' === $access || ! $url ) : ?>
					<p>This resource is being prepared and will be available soon.</p>
					<?php jn_part( 'newsletter', array( 'compact' => true ) ); ?>
				<?php elseif ( 'registration' === $access && ! $unlocked ) : ?>
					<p>Enter your email address and the download link will appear straight away.</p>
					<form class="form js-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-kind="resource">
						<?php jn_form_hidden( 'resource', array( 'resource_id' => $rid ) ); ?>
						<div class="form__row"><label for="dl-name">Name</label><input id="dl-name" name="name" type="text" autocomplete="name"></div>
						<div class="form__row"><label for="dl-email">Email <span aria-hidden="true">*</span></label><input id="dl-email" name="email" type="email" required autocomplete="email"></div>
						<label class="check"><input type="checkbox" name="newsletter" value="1"> Also send me the Learning Centre newsletter</label>
						<button class="btn btn--primary" type="submit"><?php echo jn_icon( 'download' ); // phpcs:ignore ?> Get the download</button>
						<div class="form__status" role="status" aria-live="polite"></div>
						<?php jn_form_feedback( 'resource' ); ?>
					</form>
				<?php else : ?>
					<?php jn_form_feedback( 'resource' ); ?>
					<a class="btn btn--primary btn--lg" href="<?php echo esc_url( $url ); ?>" download><?php echo jn_icon( 'download' ); // phpcs:ignore ?> Download</a>
				<?php endif; ?>
			</div>
			<?php echo jn_share_links(); // phpcs:ignore ?>
		</aside>
	</div>
</section>
<?php
get_footer();
