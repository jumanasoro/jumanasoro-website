<?php
/**
 * "Site Profile" settings screen: biography basics, homepage text, contact
 * details, form destinations and integrations. Everything a non-developer
 * needs to change lives here or in the normal WordPress editors.
 */

defined( 'ABSPATH' ) || exit;

function jn_option_fields() {
	return array(
		'Identity'               => array(
			'name'        => array( 'Name', 'text' ),
			'positioning' => array( 'Professional positioning (shown under your name)', 'text' ),
			'location'    => array( 'Location', 'text' ),
			'portrait_id' => array( 'Portrait photograph (portrait orientation, at least 800×1000)', 'media' ),
			'cv_id'       => array( 'CV file (PDF). The "Download CV" button appears once a file is chosen.', 'media' ),
		),
		'Homepage'               => array(
			'hero_headline'  => array( 'Headline', 'text' ),
			'hero_text'      => array( 'Supporting text', 'textarea' ),
			'hero_statement' => array( 'Brand statement (shown in the "Who I am" section)', 'textarea' ),
			'intro'          => array( 'Short introduction ("Who I am")', 'textarea' ),
			'pillars'        => array( 'The four pillars: one per line as "Title | Description"', 'textarea' ),
			'stats'          => array( 'Experience highlights: one per line as "value|label". Use only facts you can verify. Leave empty to hide.', 'textarea' ),
		),
		'Contact details'        => array(
			'email'       => array( 'Public email address', 'email' ),
			'phone'       => array( 'Telephone (with country code, e.g. +254 7xx xxx xxx)', 'text' ),
			'whatsapp'    => array( 'WhatsApp number (with country code; digits only is fine)', 'text' ),
			'linkedin'    => array( 'LinkedIn profile URL', 'url' ),
			'x'           => array( 'X (Twitter) URL', 'url' ),
			'facebook'    => array( 'Facebook URL', 'url' ),
			'instagram'   => array( 'Instagram URL', 'url' ),
			'youtube'     => array( 'YouTube URL', 'url' ),
			'booking_url' => array( 'Booking link (e.g. Calendly or Google appointment page). Adds a "Book a call" button.', 'url' ),
		),
		'Forms & integrations'   => array(
			'notify_email'     => array( 'Send form notifications to (every submission is also saved under Enquiries / Subscribers)', 'email' ),
			'webhook_url'      => array( 'Automation webhook (optional). Every submission is also POSTed here as JSON, for Zapier, Make, n8n or a CRM.', 'url' ),
			'newsletter_intro' => array( 'Newsletter sign-up text', 'textarea' ),
			'analytics_id'     => array( 'Google Analytics 4 measurement ID (e.g. G-XXXXXXX)', 'text' ),
			'search_console'   => array( 'Google Search Console verification code (content of the meta tag only)', 'text' ),
		),
	);
}

add_action( 'admin_menu', function () {
	add_menu_page( 'Site Profile', 'Site Profile', 'manage_options', 'jn-profile', 'jn_render_options_page', 'dashicons-id-alt', 2 );
} );

add_action( 'admin_init', function () {
	register_setting( 'jn_profile_group', 'jn_profile', array(
		'type'              => 'array',
		'sanitize_callback' => 'jn_sanitize_options',
	) );
} );

function jn_sanitize_options( $input ) {
	$out = array();
	foreach ( jn_option_fields() as $fields ) {
		foreach ( $fields as $key => $f ) {
			$raw = $input[ $key ] ?? '';
			switch ( $f[1] ) {
				case 'textarea':
					$out[ $key ] = sanitize_textarea_field( $raw );
					break;
				case 'email':
					$out[ $key ] = sanitize_email( $raw );
					break;
				case 'url':
					$out[ $key ] = esc_url_raw( $raw );
					break;
				case 'media':
					$out[ $key ] = absint( $raw );
					break;
				default:
					$out[ $key ] = sanitize_text_field( $raw );
			}
		}
	}
	return $out;
}

function jn_render_options_page() {
	$opts = wp_parse_args( (array) get_option( 'jn_profile', array() ), jn_option_defaults() );
	?>
	<div class="wrap">
		<h1>Site Profile</h1>
		<p style="max-width:760px">These settings feed the whole website: your name and positioning, homepage text, contact details and where form submissions go. Pages, services, projects, articles, resources and the timeline each have their own menu on the left.</p>
		<form method="post" action="options.php">
			<?php settings_fields( 'jn_profile_group' ); ?>
			<?php foreach ( jn_option_fields() as $section => $fields ) : ?>
				<h2 class="title"><?php echo esc_html( $section ); ?></h2>
				<table class="form-table" role="presentation"><tbody>
				<?php foreach ( $fields as $key => $f ) : ?>
					<?php
					$val  = $opts[ $key ] ?? '';
					$id   = 'jnopt_' . $key;
					$name = 'jn_profile[' . $key . ']';
					?>
					<tr>
						<th scope="row"><label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $f[0] ); ?></label></th>
						<td>
						<?php if ( 'textarea' === $f[1] ) : ?>
							<textarea id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" rows="5" class="large-text"><?php echo esc_textarea( $val ); ?></textarea>
						<?php elseif ( 'media' === $f[1] ) : ?>
							<?php $file = $val ? wp_get_attachment_url( $val ) : ''; ?>
							<input type="hidden" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $val ); ?>">
							<span class="jn-media-name"><?php echo $file ? esc_html( basename( $file ) ) : 'No file selected'; ?></span>
							<button type="button" class="button jn-media" data-target="<?php echo esc_attr( $id ); ?>">Choose file</button>
							<button type="button" class="button-link jn-media-clear" data-target="<?php echo esc_attr( $id ); ?>">Remove</button>
						<?php else : ?>
							<input type="<?php echo esc_attr( in_array( $f[1], array( 'email', 'url' ), true ) ? $f[1] : 'text' ); ?>" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $val ); ?>" class="regular-text" style="width:100%;max-width:640px">
						<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody></table>
			<?php endforeach; ?>
			<?php submit_button( 'Save profile' ); ?>
		</form>
	</div>
	<?php
}

/** Dashboard welcome panel with the most common tasks. */
add_action( 'wp_dashboard_setup', function () {
	wp_add_dashboard_widget( 'jn_quick', 'Your website: quick tasks', function () {
		$links = array(
			'Write a new article'             => admin_url( 'post-new.php' ),
			'Publish a planned article'       => admin_url( 'edit.php?post_status=draft&post_type=post' ),
			'Add a project, idea or venture'  => admin_url( 'post-new.php?post_type=jn_project' ),
			'Upload a downloadable resource'  => admin_url( 'post-new.php?post_type=jn_resource' ),
			'Edit services'                   => admin_url( 'edit.php?post_type=jn_service' ),
			'Edit the timeline'               => admin_url( 'edit.php?post_type=jn_milestone' ),
			'Edit About page (biography)'     => ( $p = get_page_by_path( 'about' ) ) ? get_edit_post_link( $p->ID ) : admin_url( 'edit.php?post_type=page' ),
			'Contact details, CV, portrait'   => admin_url( 'admin.php?page=jn-profile' ),
			'Read enquiries'                  => admin_url( 'edit.php?post_type=jn_enquiry' ),
			'Newsletter subscribers'          => admin_url( 'edit.php?post_type=jn_subscriber' ),
		);
		echo '<ul style="columns:2;margin:0">';
		foreach ( $links as $label => $url ) {
			printf( '<li><a href="%s">%s</a></li>', esc_url( $url ), esc_html( $label ) );
		}
		echo '</ul>';
	} );
} );
