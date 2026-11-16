<?php
/**
 * Form handling: enquiries, automation assessments, initiative interest,
 * newsletter sign-ups and gated resource downloads.
 *
 * Every submission is (1) validated, (2) checked for spam, (3) saved in the
 * dashboard, (4) emailed to the notification address and (5) optionally
 * POSTed to an automation webhook (CRM, Zapier, Make, n8n…).
 */

defined( 'ABSPATH' ) || exit;

function jn_enquiry_types() {
	return array(
		'consultancy'   => 'Consultancy enquiry',
		'automation'    => 'AI and automation project',
		'assessment'    => 'Automation assessment',
		'fundraising'   => 'Fundraising and grant consultancy',
		'programme'     => 'Programme management assignment',
		'training'      => 'Training engagement',
		'speaking'      => 'Speaking invitation',
		'partnership'   => 'Strategic partnership',
		'research'      => 'Research collaboration',
		'venture'       => 'Venture or initiative collaboration',
		'other'         => 'Something else',
	);
}

function jn_contact_methods() {
	return array(
		'email'    => 'Email',
		'phone'    => 'Phone call',
		'whatsapp' => 'WhatsApp',
	);
}

/** Signed render-time token used for the minimum fill-time check. */
function jn_form_token() {
	$ts = time();
	return $ts . '.' . substr( hash_hmac( 'sha256', (string) $ts, wp_salt( 'nonce' ) ), 0, 16 );
}

function jn_form_token_ok( $token ) {
	$parts = explode( '.', (string) $token );
	if ( 2 !== count( $parts ) ) {
		return false;
	}
	$ts = (int) $parts[0];
	if ( ! hash_equals( substr( hash_hmac( 'sha256', (string) $ts, wp_salt( 'nonce' ) ), 0, 16 ), $parts[1] ) ) {
		return false;
	}
	return ( time() - $ts ) >= 3; // Humans need at least a few seconds.
}

/** Hidden fields every form carries. */
function jn_form_hidden( $kind, $extra = array() ) {
	echo '<input type="hidden" name="action" value="jn_form">';
	echo '<input type="hidden" name="jn_kind" value="' . esc_attr( $kind ) . '">';
	echo '<input type="hidden" name="jn_token" value="' . esc_attr( jn_form_token() ) . '">';
	echo '<input type="hidden" name="jn_return" value="' . esc_url( home_url( add_query_arg( array() ) ) ) . '">';
	foreach ( $extra as $k => $v ) {
		echo '<input type="hidden" name="' . esc_attr( $k ) . '" value="' . esc_attr( $v ) . '">';
	}
	// Honeypot: hidden from people, tempting for bots.
	echo '<div class="hp" aria-hidden="true"><label>Leave this empty<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>';
}

add_action( 'admin_post_nopriv_jn_form', 'jn_handle_form' );
add_action( 'admin_post_jn_form', 'jn_handle_form' );

function jn_handle_form() {
	$p      = wp_unslash( $_POST ); // phpcs:ignore WordPress.Security.NonceVerification
	$kind   = sanitize_key( $p['jn_kind'] ?? 'enquiry' );
	$ajax   = ! empty( $p['jn_ajax'] );
	$return = wp_validate_redirect( esc_url_raw( $p['jn_return'] ?? '' ), home_url( '/' ) );

	$fail = function ( $msg, $code = 400 ) use ( $ajax, $return ) {
		if ( $ajax ) {
			wp_send_json( array( 'ok' => false, 'message' => $msg ), $code );
		}
		wp_safe_redirect( add_query_arg( 'jn_error', rawurlencode( $msg ), $return ) . '#form' );
		exit;
	};

	// Spam checks: honeypot, fill time, rate limit.
	if ( ! empty( $p['website'] ) || ! jn_form_token_ok( $p['jn_token'] ?? '' ) ) {
		$fail( 'Your message could not be sent. Please wait a moment and try again.' );
	}
	$ip_key = 'jn_rl_' . md5( ( $_SERVER['REMOTE_ADDR'] ?? '' ) . wp_salt() );
	$count  = (int) get_transient( $ip_key );
	if ( $count >= 6 ) {
		$fail( 'Too many submissions in a short time. Please try again in a few minutes.', 429 );
	}
	set_transient( $ip_key, $count + 1, 10 * MINUTE_IN_SECONDS );

	$email = sanitize_email( $p['email'] ?? '' );
	if ( ! is_email( $email ) ) {
		$fail( 'Please enter a valid email address.' );
	}

	$data = array( 'email' => $email );

	if ( 'subscribe' === $kind || 'resource' === $kind ) {
		$data['name'] = sanitize_text_field( $p['name'] ?? '' );
		if ( 'resource' === $kind ) {
			$rid = absint( $p['resource_id'] ?? 0 );
			if ( ! $rid || 'jn_resource' !== get_post_type( $rid ) ) {
				$fail( 'That resource could not be found.' );
			}
			$data['resource'] = get_the_title( $rid );
		}
		$data['source']     = 'resource' === $kind ? 'Resource download' : 'Newsletter';
		$data['newsletter'] = ( 'subscribe' === $kind || ! empty( $p['newsletter'] ) ) ? 'Yes' : 'No';
		jn_save_subscriber( $data );
		jn_notify( $kind, $data );

		$message = 'subscribe' === $kind ? 'Thank you. You are on the list.' : 'Thank you. Your download is ready.';
		$extra   = array();
		if ( 'resource' === $kind ) {
			$extra['download'] = jn_resource_file_url( $rid );
		}
		if ( $ajax ) {
			wp_send_json( array_merge( array( 'ok' => true, 'message' => $message ), $extra ) );
		}
		$args = array( 'jn_sent' => $kind );
		if ( 'resource' === $kind ) {
			$args['jn_dl'] = $rid . '.' . jn_download_sig( $rid );
		}
		wp_safe_redirect( add_query_arg( $args, $return ) . '#form' );
		exit;
	}

	// Enquiry-style forms.
	$name = sanitize_text_field( $p['name'] ?? '' );
	$msg  = sanitize_textarea_field( $p['message'] ?? '' );
	if ( strlen( $name ) < 2 ) {
		$fail( 'Please tell me your name.' );
	}
	if ( strlen( $msg ) < 10 ) {
		$fail( 'Please add a short description of your challenge or opportunity.' );
	}
	if ( strlen( $msg ) > 6000 ) {
		$fail( 'Your message is a little long. Please shorten it to under 6,000 characters.' );
	}
	$types  = jn_enquiry_types();
	$type   = sanitize_key( $p['enquiry_type'] ?? 'other' );
	$method = sanitize_key( $p['contact_method'] ?? 'email' );
	$data   = array(
		'name'             => $name,
		'organisation'     => sanitize_text_field( $p['organisation'] ?? '' ),
		'email'            => $email,
		'telephone'        => sanitize_text_field( $p['phone'] ?? '' ),
		'enquiry_type'     => $types[ $type ] ?? $types['other'],
		'preferred_contact' => jn_contact_methods()[ $method ] ?? 'Email',
		'message'          => $msg,
	);
	foreach ( array( 'organisation_type', 'team_size', 'processes', 'tools', 'initiative' ) as $opt_field ) {
		if ( ! empty( $p[ $opt_field ] ) ) {
			$data[ $opt_field ] = is_array( $p[ $opt_field ] ) ? implode( ', ', array_map( 'sanitize_text_field', $p[ $opt_field ] ) ) : sanitize_text_field( $p[ $opt_field ] );
		}
	}
	$data['page'] = esc_url_raw( $return );

	$post_id = wp_insert_post( array(
		'post_type'   => 'jn_enquiry',
		'post_status' => 'private',
		'post_title'  => sprintf( '%s · %s', $data['enquiry_type'], $name ),
	) );
	if ( is_wp_error( $post_id ) || ! $post_id ) {
		$fail( 'Something went wrong on our side. Please try again, or use the email address on this page.', 500 );
	}
	update_post_meta( $post_id, '_jn_data', $data );
	update_post_meta( $post_id, '_jn_type', $type );
	jn_notify( 'enquiry', $data );

	$ok = 'Thank you, ' . $name . '. Your message has been received and I will get back to you personally.';
	if ( $ajax ) {
		wp_send_json( array( 'ok' => true, 'message' => $ok ) );
	}
	wp_safe_redirect( add_query_arg( 'jn_sent', 'enquiry', $return ) . '#form' );
	exit;
}

function jn_save_subscriber( $data ) {
	$existing = get_posts( array(
		'post_type'      => 'jn_subscriber',
		'post_status'    => 'any',
		'title'          => $data['email'],
		'posts_per_page' => 1,
		'fields'         => 'ids',
	) );
	if ( $existing ) {
		$old = (array) get_post_meta( $existing[0], '_jn_data', true );
		if ( ! empty( $old['resource'] ) && ! empty( $data['resource'] ) && false === strpos( $old['resource'], $data['resource'] ) ) {
			$data['resource'] = $old['resource'] . ', ' . $data['resource'];
		}
		if ( 'Yes' === ( $old['newsletter'] ?? '' ) ) {
			$data['newsletter'] = 'Yes';
		}
		update_post_meta( $existing[0], '_jn_data', array_merge( $old, array_filter( $data ) ) );
		return $existing[0];
	}
	$id = wp_insert_post( array(
		'post_type'   => 'jn_subscriber',
		'post_status' => 'private',
		'post_title'  => $data['email'],
	) );
	if ( $id && ! is_wp_error( $id ) ) {
		$data['subscribed_on'] = current_time( 'mysql' );
		update_post_meta( $id, '_jn_data', $data );
	}
	return $id;
}

function jn_notify( $kind, $data ) {
	$to = jn_opt( 'notify_email' );
	if ( $to && is_email( $to ) ) {
		$subjects = array(
			'enquiry'   => 'New enquiry: ' . ( $data['enquiry_type'] ?? '' ) . ' from ' . ( $data['name'] ?? $data['email'] ),
			'subscribe' => 'New newsletter subscriber: ' . $data['email'],
			'resource'  => 'Resource downloaded: ' . ( $data['resource'] ?? '' ) . ' by ' . $data['email'],
		);
		$body = '';
		foreach ( $data as $k => $v ) {
			$body .= ucwords( str_replace( '_', ' ', $k ) ) . ': ' . $v . "\n";
		}
		$body .= "\nView all submissions: " . admin_url( 'enquiry' === $kind ? 'edit.php?post_type=jn_enquiry' : 'edit.php?post_type=jn_subscriber' );
		$headers = array( 'Reply-To: ' . ( $data['name'] ?? '' ) . ' <' . $data['email'] . '>' );
		wp_mail( $to, '[' . get_bloginfo( 'name' ) . '] ' . ( $subjects[ $kind ] ?? 'New submission' ), $body, $headers );
	}
	$hook = jn_opt( 'webhook_url' );
	if ( $hook ) {
		wp_remote_post( $hook, array(
			'blocking' => false,
			'timeout'  => 3,
			'headers'  => array( 'Content-Type' => 'application/json' ),
			'body'     => wp_json_encode( array( 'kind' => $kind, 'site' => home_url(), 'submitted_at' => gmdate( 'c' ), 'data' => $data ) ),
		) );
	}
}

/** Public download URL of a resource (uploaded file or external link). */
function jn_resource_file_url( $id ) {
	$file = jn_meta( 'file_id', $id );
	if ( $file && wp_get_attachment_url( $file ) ) {
		return wp_get_attachment_url( $file );
	}
	return (string) jn_meta( 'file_url', $id );
}

function jn_download_sig( $id ) {
	return substr( hash_hmac( 'sha256', 'dl' . $id, wp_salt( 'auth' ) ), 0, 12 );
}

// Admin list columns and CSV export for submissions.
add_filter( 'manage_jn_enquiry_posts_columns', function () {
	return array( 'cb' => '<input type="checkbox">', 'title' => 'Enquiry', 'jn_email' => 'Email', 'jn_org' => 'Organisation', 'jn_contact' => 'Preferred contact', 'date' => 'Received' );
} );
add_action( 'manage_jn_enquiry_posts_custom_column', function ( $col, $id ) {
	$d = (array) get_post_meta( $id, '_jn_data', true );
	$map = array( 'jn_email' => 'email', 'jn_org' => 'organisation', 'jn_contact' => 'preferred_contact' );
	if ( isset( $map[ $col ] ) ) {
		echo esc_html( $d[ $map[ $col ] ] ?? '' );
	}
}, 10, 2 );
add_filter( 'manage_jn_subscriber_posts_columns', function () {
	return array( 'cb' => '<input type="checkbox">', 'title' => 'Email', 'jn_name' => 'Name', 'jn_source' => 'Source', 'jn_news' => 'Newsletter', 'date' => 'Joined' );
} );
add_action( 'manage_jn_subscriber_posts_custom_column', function ( $col, $id ) {
	$d   = (array) get_post_meta( $id, '_jn_data', true );
	$map = array( 'jn_name' => 'name', 'jn_source' => 'source', 'jn_news' => 'newsletter' );
	if ( isset( $map[ $col ] ) ) {
		echo esc_html( $d[ $map[ $col ] ] ?? '' );
	}
}, 10, 2 );

add_action( 'restrict_manage_posts', function ( $type ) {
	if ( in_array( $type, array( 'jn_enquiry', 'jn_subscriber' ), true ) ) {
		printf( '<a class="button" href="%s">Export CSV</a>', esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=jn_export&type=' . $type ), 'jn_export' ) ) );
	}
} );

add_action( 'admin_post_jn_export', function () {
	if ( ! current_user_can( 'edit_posts' ) || ! check_admin_referer( 'jn_export' ) ) {
		wp_die( 'Not allowed.' );
	}
	$type = 'jn_subscriber' === ( $_GET['type'] ?? '' ) ? 'jn_subscriber' : 'jn_enquiry';
	$rows = get_posts( array( 'post_type' => $type, 'post_status' => 'any', 'posts_per_page' => -1 ) );
	$keys = array();
	$data = array();
	foreach ( $rows as $r ) {
		$d             = (array) get_post_meta( $r->ID, '_jn_data', true );
		$d['received'] = $r->post_date;
		$data[]        = $d;
		$keys          = array_unique( array_merge( $keys, array_keys( $d ) ) );
	}
	nocache_headers();
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=' . $type . '-' . gmdate( 'Y-m-d' ) . '.csv' );
	$out = fopen( 'php://output', 'w' );
	fputcsv( $out, $keys );
	foreach ( $data as $d ) {
		$line = array();
		foreach ( $keys as $k ) {
			$v      = $d[ $k ] ?? '';
			$v      = is_array( $v ) ? implode( ', ', $v ) : (string) $v;
			$line[] = preg_match( '/^[=+\-@]/', $v ) ? "'" . $v : $v; // Avoid spreadsheet formula injection.
		}
		fputcsv( $out, $line );
	}
	fclose( $out );
	exit;
} );

/** Message banner after a non-JS submission. */
function jn_form_feedback( $kind = 'enquiry' ) {
	if ( isset( $_GET['jn_error'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		echo '<div class="notice notice--error" role="alert">' . esc_html( sanitize_text_field( wp_unslash( $_GET['jn_error'] ) ) ) . '</div>';
	}
	if ( isset( $_GET['jn_sent'] ) && $kind === $_GET['jn_sent'] ) { // phpcs:ignore WordPress.Security.NonceVerification
		$msgs = array(
			'enquiry'   => 'Thank you. Your message has been received and I will get back to you personally.',
			'subscribe' => 'Thank you. You are on the list.',
			'resource'  => 'Thank you. Your download is ready below.',
		);
		echo '<div class="notice notice--ok" role="status">' . esc_html( $msgs[ $kind ] ?? 'Thank you.' ) . '</div>';
	}
}
