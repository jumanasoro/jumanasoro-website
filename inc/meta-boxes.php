<?php
/**
 * Simple, dependency-free custom fields for every content type.
 */

defined( 'ABSPATH' ) || exit;

/** Field definitions per post type. */
function jn_field_sets() {
	$icons = array(
		'programme'  => 'Programme',
		'fundraise'  => 'Fundraising',
		'enterprise' => 'Enterprise',
		'ai'         => 'AI / automation',
		'leaf'       => 'Sustainability',
		'training'   => 'Training',
		'idea'       => 'Idea',
		'flask'      => 'Experiment',
		'mail'       => 'Email',
		'search'     => 'Research',
		'clock'      => 'Scheduling',
		'file'       => 'Documents',
	);
	return array(
		'jn_project'    => array(
			'title'  => 'Portfolio details',
			'help'   => 'Choose a section on the right (Active & Completed, Ideas Worth Building, or Ventures & Experiments). Status and "my contribution" are shown on every card so visitors can tell finished work from proposals.',
			'fields' => array(
				'status'        => array( 'Status', 'select', jn_statuses() ),
				'contribution'  => array( 'My contribution', 'select', jn_contribution_labels() ),
				'role'          => array( 'My role', 'text', 'e.g. Programme Manager' ),
				'organisation'  => array( 'Organisation / host', 'text', 'e.g. HERI-Kenya' ),
				'period'        => array( 'Period', 'text', 'e.g. 2021 – present. Leave empty if unsure.' ),
				'location'      => array( 'Location', 'text', 'e.g. Diani, Kwale County' ),
				'featured'      => array( 'Feature on the homepage', 'checkbox' ),
				'highlights'    => array( 'Focus areas / highlights (one per line)', 'textarea' ),
				'outcomes'      => array( 'Verified results (one per line, only facts you can stand behind)', 'textarea' ),
				'problem'       => array( 'Problem or opportunity', 'textarea' ),
				'solution'      => array( 'Proposed solution / approach', 'textarea' ),
				'vision'        => array( 'Vision and intended impact', 'textarea' ),
				'model'         => array( 'Potential implementation model', 'textarea' ),
				'partners'      => array( 'Potential partners (one per line)', 'textarea' ),
				'resources'     => array( 'Resource requirements', 'textarea' ),
				'collaboration' => array( 'Collaboration opportunity', 'textarea' ),
			),
		),
		'jn_service'    => array(
			'title'  => 'Service details',
			'help'   => 'Offerings: one per line. Start a line with "## " to begin a sub-group (used for the AI & Automation service).',
			'fields' => array(
				'number'    => array( 'Number', 'text', 'e.g. 01' ),
				'icon'      => array( 'Icon', 'select', $icons ),
				'anchor'    => array( 'Anchor (URL fragment)', 'text', 'e.g. fundraising' ),
				'problem'   => array( 'The problem it addresses', 'textarea' ),
				'offerings' => array( 'Services offered', 'textarea' ),
				'value'     => array( 'The value you can expect', 'textarea' ),
				'audience'  => array( 'Who should engage (one per line)', 'textarea' ),
				'engage'    => array( 'How to start an engagement', 'textarea' ),
				'note'      => array( 'Delivery note (optional)', 'textarea' ),
				'cta'       => array( 'Button label', 'text', 'e.g. Discuss a project' ),
			),
		),
		'jn_automation' => array(
			'title'  => 'How automation changes this process',
			'help'   => 'Shown in the interactive "What can we automate?" explorer. Keep it illustrative; do not promise specific savings.',
			'fields' => array(
				'area'      => array( 'Area', 'text', 'e.g. Fundraising' ),
				'icon'      => array( 'Icon', 'select', $icons ),
				'today'     => array( 'How it usually works today', 'textarea' ),
				'automated' => array( 'How it could work with automation (one step per line)', 'textarea' ),
				'outcome'   => array( 'What improves', 'textarea' ),
				'tools'     => array( 'Example tools (illustrative)', 'text' ),
			),
		),
		'jn_milestone'  => array(
			'title'  => 'Milestone details',
			'help'   => 'Order with the "Order" box (lower numbers appear first). Leave the date empty rather than guessing; empty dates are simply not shown.',
			'fields' => array(
				'when'         => array( 'Date or period', 'text', 'e.g. 2018 or 2019 – 2022' ),
				'kind'         => array( 'Type', 'select', array(
					'role'          => 'Role',
					'education'     => 'Education',
					'certification' => 'Certification',
					'project'       => 'Project',
					'venture'       => 'Venture',
					'development'   => 'Professional development',
				) ),
				'organisation' => array( 'Organisation', 'text' ),
			),
		),
		'jn_resource'   => array(
			'title'  => 'Download',
			'help'   => 'Upload the file (PDF, DOCX, XLSX, PPTX, ZIP…) or paste an external link. "Email required" asks for an email address before the download link is shown.',
			'fields' => array(
				'file_id'  => array( 'File', 'media' ),
				'file_url' => array( 'Or external link', 'url' ),
				'format'   => array( 'Format label (optional)', 'text', 'e.g. PDF template' ),
				'access'   => array( 'Access', 'select', array(
					'free'         => 'Free download',
					'registration' => 'Email required',
					'coming-soon'  => 'Coming soon (no download yet)',
				) ),
				'featured' => array( 'Feature this resource', 'checkbox' ),
			),
		),
		'post'          => array(
			'title'  => 'Learning Centre options',
			'help'   => 'Planned topics are saved as drafts and appear on the Learning Centre as "In the pipeline" (title only, no link) until you publish them.',
			'fields' => array(
				'featured'    => array( 'Featured article', 'checkbox' ),
				'recommended' => array( 'Recommended reading', 'checkbox' ),
				'planned'     => array( 'Show this draft as a planned topic', 'checkbox' ),
			),
			'context' => 'side',
		),
	);
}

/** SEO box on every public type. */
function jn_seo_types() {
	return array( 'page', 'post', 'jn_project', 'jn_resource' );
}

add_action( 'add_meta_boxes', function () {
	foreach ( jn_field_sets() as $type => $set ) {
		add_meta_box( 'jn_fields_' . $type, $set['title'], 'jn_render_fields', $type, $set['context'] ?? 'normal', 'high', array( 'type' => $type ) );
	}
	foreach ( jn_seo_types() as $type ) {
		add_meta_box( 'jn_seo', 'Search & sharing', 'jn_render_seo', $type, 'normal', 'low' );
	}
	foreach ( array( 'jn_enquiry', 'jn_subscriber' ) as $type ) {
		add_meta_box( 'jn_record', 'Submission', 'jn_render_record', $type, 'normal', 'high' );
	}
} );

function jn_render_fields( $post, $box ) {
	$set = jn_field_sets()[ $box['args']['type'] ];
	wp_nonce_field( 'jn_save_fields', 'jn_fields_nonce' );
	if ( ! empty( $set['help'] ) ) {
		echo '<p class="description" style="margin:8px 0 14px">' . esc_html( $set['help'] ) . '</p>';
	}
	echo '<div class="jn-fields">';
	foreach ( $set['fields'] as $key => $f ) {
		jn_render_field( $post->ID, $key, $f );
	}
	echo '</div>';
}

function jn_render_field( $post_id, $key, $f ) {
	$name  = 'jn[' . $key . ']';
	$id    = 'jn_' . $key;
	$value = get_post_meta( $post_id, '_jn_' . $key, true );
	$type  = $f[1];
	echo '<p class="jn-field jn-field--' . esc_attr( $type ) . '">';
	if ( 'checkbox' === $type ) {
		printf( '<label><input type="checkbox" name="%1$s" value="1" %2$s> %3$s</label>', esc_attr( $name ), checked( $value, '1', false ), esc_html( $f[0] ) );
		echo '</p>';
		return;
	}
	printf( '<label for="%1$s"><strong>%2$s</strong></label><br>', esc_attr( $id ), esc_html( $f[0] ) );
	switch ( $type ) {
		case 'select':
			printf( '<select id="%1$s" name="%2$s"><option value="">—</option>', esc_attr( $id ), esc_attr( $name ) );
			foreach ( $f[2] as $k => $label ) {
				printf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( $k ), selected( $value, $k, false ), esc_html( $label ) );
			}
			echo '</select>';
			break;
		case 'textarea':
			printf( '<textarea id="%1$s" name="%2$s" rows="4" class="widefat">%3$s</textarea>', esc_attr( $id ), esc_attr( $name ), esc_textarea( $value ) );
			break;
		case 'media':
			$file = $value ? wp_get_attachment_url( $value ) : '';
			printf(
				'<input type="hidden" id="%1$s" name="%2$s" value="%3$s"><span class="jn-media-name">%4$s</span> <button type="button" class="button jn-media" data-target="%1$s">Choose file</button> <button type="button" class="button-link jn-media-clear" data-target="%1$s">Remove</button>',
				esc_attr( $id ),
				esc_attr( $name ),
				esc_attr( $value ),
				$file ? esc_html( basename( $file ) ) : 'No file selected'
			);
			break;
		default:
			printf( '<input type="%4$s" id="%1$s" name="%2$s" value="%3$s" class="widefat" placeholder="%5$s">', esc_attr( $id ), esc_attr( $name ), esc_attr( $value ), 'url' === $type ? 'url' : 'text', esc_attr( $f[2] ?? '' ) );
	}
	echo '</p>';
}

function jn_render_seo( $post ) {
	wp_nonce_field( 'jn_save_fields', 'jn_fields_nonce' );
	echo '<p class="description">Optional. Leave empty to use the title and excerpt automatically.</p>';
	jn_render_field( $post->ID, 'seo_title', array( 'SEO title (about 60 characters)', 'text' ) );
	jn_render_field( $post->ID, 'seo_desc', array( 'Meta description (about 155 characters)', 'textarea' ) );
	jn_render_field( $post->ID, 'noindex', array( 'Hide this page from search engines', 'checkbox' ) );
}

function jn_render_record( $post ) {
	$data = get_post_meta( $post->ID, '_jn_data', true );
	if ( ! is_array( $data ) ) {
		echo '<p>No data.</p>';
		return;
	}
	echo '<table class="widefat striped"><tbody>';
	foreach ( $data as $k => $v ) {
		printf( '<tr><th style="width:200px">%s</th><td>%s</td></tr>', esc_html( ucwords( str_replace( '_', ' ', $k ) ) ), nl2br( esc_html( is_array( $v ) ? implode( ', ', $v ) : $v ) ) );
	}
	echo '</tbody></table>';
	$email = $data['email'] ?? '';
	if ( $email ) {
		printf( '<p><a class="button button-primary" href="mailto:%1$s">Reply by email</a></p>', esc_attr( $email ) );
	}
}

add_action( 'save_post', function ( $post_id, $post ) {
	if ( ! isset( $_POST['jn_fields_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['jn_fields_nonce'] ) ), 'jn_save_fields' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$sets   = jn_field_sets();
	$fields = $sets[ $post->post_type ]['fields'] ?? array();
	if ( in_array( $post->post_type, jn_seo_types(), true ) ) {
		$fields['seo_title'] = array( '', 'text' );
		$fields['seo_desc']  = array( '', 'textarea' );
		$fields['noindex']   = array( '', 'checkbox' );
	}
	$input = isset( $_POST['jn'] ) ? (array) wp_unslash( $_POST['jn'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	foreach ( $fields as $key => $f ) {
		$raw = $input[ $key ] ?? '';
		switch ( $f[1] ) {
			case 'checkbox':
				$val = $raw ? '1' : '';
				break;
			case 'textarea':
				$val = sanitize_textarea_field( $raw );
				break;
			case 'url':
				$val = esc_url_raw( $raw );
				break;
			case 'media':
				$val = absint( $raw ) ? (string) absint( $raw ) : '';
				break;
			default:
				$val = sanitize_text_field( $raw );
		}
		if ( '' === $val ) {
			delete_post_meta( $post_id, '_jn_' . $key );
		} else {
			update_post_meta( $post_id, '_jn_' . $key, $val );
		}
	}
}, 10, 2 );

add_action( 'admin_enqueue_scripts', function ( $hook ) {
	if ( in_array( $hook, array( 'post.php', 'post-new.php', 'toplevel_page_jn-profile' ), true ) ) {
		wp_enqueue_media();
		wp_enqueue_script( 'jn-admin', JN_URI . '/assets/js/admin.js', array( 'jquery' ), JN_VERSION, true );
	}
} );

// Helpful admin columns.
add_filter( 'manage_jn_project_posts_columns', function ( $cols ) {
	$cols['jn_status'] = 'Status';
	$cols['jn_featured'] = 'Homepage';
	return $cols;
} );
add_action( 'manage_jn_project_posts_custom_column', function ( $col, $id ) {
	if ( 'jn_status' === $col ) {
		echo esc_html( jn_statuses()[ jn_meta( 'status', $id ) ] ?? '—' );
	}
	if ( 'jn_featured' === $col ) {
		echo jn_meta( 'featured', $id ) ? '★' : '';
	}
}, 10, 2 );
