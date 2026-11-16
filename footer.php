<?php defined( 'ABSPATH' ) || exit; ?>
</main>
<footer class="site-footer">
	<div class="wrap">
		<div class="footer-grid">
			<div class="footer-brand">
				<p class="footer-name"><?php echo esc_html( jn_opt( 'name' ) ); ?></p>
				<p class="footer-tag"><?php echo esc_html( jn_opt( 'hero_statement' ) ); ?></p>
				<p class="footer-loc"><?php echo jn_icon( 'pin' ); // phpcs:ignore ?> <?php echo esc_html( jn_opt( 'location' ) ); ?></p>
			</div>
			<div>
				<p class="eyebrow">Explore</p>
				<ul class="footer-links">
					<li><a href="<?php echo esc_url( jn_page_url( 'about' ) ); ?>">About</a></li>
					<li><a href="<?php echo esc_url( jn_page_url( 'what-i-do' ) ); ?>">What I Do</a></li>
					<li><a href="<?php echo esc_url( jn_page_url( 'ai-automation' ) ); ?>">AI &amp; Automation</a></li>
					<li><a href="<?php echo esc_url( get_post_type_archive_link( 'jn_project' ) ); ?>">Projects &amp; Ventures</a></li>
					<li><a href="<?php echo esc_url( jn_page_url( 'learning-centre' ) ); ?>">Learning Centre</a></li>
					<li><a href="<?php echo esc_url( get_post_type_archive_link( 'jn_resource' ) ); ?>">Resource Library</a></li>
					<li><a href="<?php echo esc_url( jn_page_url( 'consultancy' ) ); ?>">Juma Nasoro &amp; Co</a></li>
				</ul>
			</div>
			<div>
				<p class="eyebrow">Connect</p>
				<ul class="footer-links">
					<?php if ( $e = jn_opt( 'email' ) ) : ?>
						<li><a href="mailto:<?php echo esc_attr( antispambot( $e ) ); ?>"><?php echo esc_html( antispambot( $e ) ); ?></a></li>
					<?php endif; ?>
					<?php if ( $w = jn_whatsapp_url() ) : ?>
						<li><a href="<?php echo esc_url( $w ); ?>" target="_blank" rel="noopener">WhatsApp</a></li>
					<?php endif; ?>
					<?php foreach ( jn_social_links() as $s ) : ?>
						<li><a href="<?php echo esc_url( $s['url'] ); ?>" target="_blank" rel="noopener me"><?php echo esc_html( $s['label'] ); ?></a></li>
					<?php endforeach; ?>
					<li><a href="<?php echo esc_url( jn_page_url( 'contact' ) ); ?>">Contact form</a></li>
				</ul>
			</div>
			<div class="footer-news">
				<p class="eyebrow">The Learning Centre newsletter</p>
				<?php jn_part( 'newsletter', array( 'compact' => true ) ); ?>
			</div>
		</div>
		<div class="footer-base">
			<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( jn_opt( 'name' ) ); ?>. Independent professional practice.</p>
			<p><a href="<?php echo esc_url( jn_page_url( 'privacy-policy' ) ); ?>">Privacy</a> · <a href="#top">Back to top</a></p>
		</div>
	</div>
</footer>
<?php if ( $w = jn_whatsapp_url( 'Hello Juma, I found your website and would like to talk about…' ) ) : ?>
	<a class="wa-float" href="<?php echo esc_url( $w ); ?>" target="_blank" rel="noopener" aria-label="Chat on WhatsApp"><?php echo jn_icon( 'whatsapp' ); // phpcs:ignore ?></a>
<?php endif; ?>
<?php wp_footer(); ?>
</body>
</html>
