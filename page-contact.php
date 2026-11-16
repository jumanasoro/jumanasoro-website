<?php
defined( 'ABSPATH' ) || exit;
get_header();
the_post();
$type = isset( $_GET['type'] ) ? sanitize_key( wp_unslash( $_GET['type'] ) ) : 'consultancy'; // phpcs:ignore WordPress.Security.NonceVerification
if ( ! isset( jn_enquiry_types()[ $type ] ) ) {
	$type = 'consultancy';
}
jn_part( 'page-hero', array(
	'eyebrow' => 'Contact &amp; collaboration',
	'title'   => 'Let\'s talk about what you are building',
	'lead'    => wp_strip_all_tags( get_the_content() ),
) );
?>
<section class="section section--form section--contact">
	<div class="wrap form-layout">
		<aside class="form-layout__aside contact-aside">
			<h2 class="h3">Get in touch directly</h2>
			<ul class="contact-list">
				<?php if ( $e = jn_opt( 'email' ) ) : ?>
					<li><?php echo jn_icon( 'mail' ); // phpcs:ignore ?><a href="mailto:<?php echo esc_attr( antispambot( $e ) ); ?>"><?php echo esc_html( antispambot( $e ) ); ?></a></li>
				<?php endif; ?>
				<?php if ( $t = jn_opt( 'phone' ) ) : ?>
					<li><?php echo jn_icon( 'phone' ); // phpcs:ignore ?><a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $t ) ); ?>"><?php echo esc_html( $t ); ?></a></li>
				<?php endif; ?>
				<?php if ( $w = jn_whatsapp_url( 'Hello Juma, I would like to talk about…' ) ) : ?>
					<li><?php echo jn_icon( 'whatsapp' ); // phpcs:ignore ?><a href="<?php echo esc_url( $w ); ?>" target="_blank" rel="noopener">Chat on WhatsApp</a></li>
				<?php endif; ?>
				<?php foreach ( jn_social_links() as $key => $s ) : ?>
					<li><?php echo jn_icon( 'linkedin' === $key ? 'linkedin' : 'link' ); // phpcs:ignore ?><a href="<?php echo esc_url( $s['url'] ); ?>" target="_blank" rel="noopener me"><?php echo esc_html( $s['label'] ); ?></a></li>
				<?php endforeach; ?>
				<?php if ( $b = jn_opt( 'booking_url' ) ) : ?>
					<li><?php echo jn_icon( 'clock' ); // phpcs:ignore ?><a href="<?php echo esc_url( $b ); ?>" target="_blank" rel="noopener">Book a call</a></li>
				<?php endif; ?>
				<li><?php echo jn_icon( 'pin' ); // phpcs:ignore ?><span><?php echo esc_html( jn_opt( 'location' ) ); ?></span></li>
			</ul>
			<?php if ( ! jn_opt( 'email' ) && ! jn_opt( 'phone' ) && ! jn_whatsapp_url() ) : ?>
				<p class="small muted">Direct email, phone and WhatsApp details will be listed here shortly. The form is the quickest way to reach me in the meantime.</p>
			<?php endif; ?>
			<h2 class="h3 contact-aside__h">I am glad to hear about</h2>
			<ul class="tag-cloud tag-cloud--small">
				<?php foreach ( jn_enquiry_types() as $k => $label ) : ?>
					<?php if ( 'other' !== $k && 'assessment' !== $k ) : ?><li><?php echo esc_html( $label ); ?></li><?php endif; ?>
				<?php endforeach; ?>
			</ul>
		</aside>
		<?php jn_part( 'form-enquiry', array( 'type' => $type, 'title' => 'Send a message' ) ); ?>
	</div>
</section>
<?php
get_footer();
