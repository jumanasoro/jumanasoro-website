<?php
/**
 * Small template helpers used across the theme.
 */

defined( 'ABSPATH' ) || exit;

/** Default values for the Site Profile settings. */
function jn_option_defaults() {
	return array(
		// Identity.
		'name'              => 'Juma Nasoro',
		'positioning'       => 'Development Strategist · Programme Manager · Fundraising, Enterprise Growth & AI Automation Consultant',
		'location'          => 'Diani, Kwale County, Kenya',
		'portrait_id'       => 0,
		'cv_id'             => 0,
		// Homepage hero.
		'hero_headline'     => 'Building Better Organisations. Empowering Enterprises. Automating the Future.',
		'hero_text'         => 'I work at the intersection of development, enterprise, sustainability and technology, helping organisations transform ideas into actionable programmes, mobilise resources and harness AI and automation to create greater impact.',
		'hero_statement'    => 'Connecting ideas, people, technology and resources to build stronger institutions, sustainable enterprises and thriving communities.',
		'intro'             => "I am a programme manager and development practitioner based in Diani, on Kenya's south coast. For around ten years I have worked in enterprise and community development across the coastal region: leading youth enterprise and employment initiatives, building programmes, raising funds and strengthening the organisations that deliver them.\n\nToday I manage programmes at HERI-Kenya and run Juma Nasoro & Co, my independent practice for fundraising, enterprise development, programme management and digital transformation. A growing part of my work is helping organisations use AI and automation to do more with the people and money they already have.",
		'pillars'           => "Development & Programme Management | Designing, managing and strengthening development initiatives, programmes and institutions.\nFundraising & Enterprise Development | Helping organisations access resources, develop sustainable enterprises and build strategic partnerships.\nAI, Automation & Digital Transformation | Helping organisations and businesses use technology to automate processes, improve productivity and build smarter systems.\nSustainability & Innovation | Advancing circular economy solutions, environmental sustainability and community resilience.",
		// Experience highlights: one per line, "value|label". Only verified facts.
		'stats'             => "~10 years|in enterprise and community development on Kenya's coast\nUNDP|certified Enterprise Development Advisor\nKYEOP|youth enterprise and employment work in Kwale County\nBCom|University of Nairobi",
		// Contact.
		'email'             => '',
		'phone'             => '',
		'whatsapp'          => '',
		'linkedin'          => '',
		'x'                 => '',
		'facebook'          => '',
		'instagram'         => '',
		'youtube'           => '',
		'booking_url'       => '',
		// Forms.
		'notify_email'      => get_option( 'admin_email' ),
		'webhook_url'       => '',
		'newsletter_intro'  => 'Occasional, useful notes on fundraising, programme management, enterprise and practical AI. No spam, unsubscribe any time.',
		// Integrations.
		'analytics_id'      => '',
		'search_console'    => '',
	);
}

/** Read one Site Profile value. */
function jn_opt( $key, $fallback = null ) {
	static $opts = null;
	if ( null === $opts ) {
		$opts = wp_parse_args( (array) get_option( 'jn_profile', array() ), jn_option_defaults() );
	}
	if ( isset( $opts[ $key ] ) && '' !== $opts[ $key ] ) {
		return $opts[ $key ];
	}
	return null === $fallback ? ( jn_option_defaults()[ $key ] ?? '' ) : $fallback;
}

/** Split a textarea into trimmed, non-empty lines. */
function jn_lines( $text ) {
	$out = array();
	foreach ( preg_split( '/\r\n|\r|\n/', (string) $text ) as $line ) {
		$line = trim( $line );
		$line = preg_replace( '/^[-*•]\s*/u', '', $line );
		if ( '' !== $line ) {
			$out[] = $line;
		}
	}
	return $out;
}

/**
 * Turn a textarea into groups. Lines starting with "## " open a new group.
 * Returns array of [ 'title' => string, 'items' => string[] ].
 */
function jn_groups( $text ) {
	$groups  = array();
	$current = array( 'title' => '', 'items' => array() );
	foreach ( preg_split( '/\r\n|\r|\n/', (string) $text ) as $line ) {
		$line = trim( $line );
		if ( '' === $line ) {
			continue;
		}
		if ( 0 === strpos( $line, '## ' ) ) {
			if ( $current['title'] || $current['items'] ) {
				$groups[] = $current;
			}
			$current = array( 'title' => trim( substr( $line, 3 ) ), 'items' => array() );
			continue;
		}
		$current['items'][] = preg_replace( '/^[-*•]\s*/u', '', $line );
	}
	if ( $current['title'] || $current['items'] ) {
		$groups[] = $current;
	}
	return $groups;
}

/** Post meta shortcut. */
function jn_meta( $key, $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	return get_post_meta( $post_id, '_jn_' . $key, true );
}

/** Portfolio statuses (editable per item). */
function jn_statuses() {
	return array(
		'concept'          => 'Concept',
		'in-development'   => 'In Development',
		'seeking-partners' => 'Seeking Partners',
		'seeking-funding'  => 'Seeking Funding',
		'pilot'            => 'Pilot',
		'active'           => 'Active',
		'completed'        => 'Completed',
	);
}

/** Statuses that describe work that has not yet been implemented. */
function jn_is_proposed_status( $status ) {
	return in_array( $status, array( 'concept', 'in-development', 'seeking-partners', 'seeking-funding' ), true );
}

function jn_status_badge( $post_id = null ) {
	$status = jn_meta( 'status', $post_id );
	$labels = jn_statuses();
	if ( ! isset( $labels[ $status ] ) ) {
		return '';
	}
	return sprintf( '<span class="badge badge--%1$s">%2$s</span>', esc_attr( $status ), esc_html( $labels[ $status ] ) );
}

/** The portfolio section (taxonomy term) of a project. */
function jn_project_section( $post_id = null ) {
	$terms = get_the_terms( $post_id ? $post_id : get_the_ID(), 'jn_section' );
	return ( $terms && ! is_wp_error( $terms ) ) ? $terms[0] : null;
}

/** Contribution type label (personal vs organisational vs proposal). */
function jn_contribution_labels() {
	return array(
		'led'          => 'I led this work',
		'contributed'  => 'I contributed to this work',
		'organisation' => 'Organisational work I am part of',
		'proposal'     => 'My proposal, not yet implemented',
	);
}

/** Estimated reading time in minutes. */
function jn_reading_time( $post_id = null ) {
	$words = str_word_count( wp_strip_all_tags( get_post_field( 'post_content', $post_id ? $post_id : get_the_ID() ) ) );
	return max( 1, (int) ceil( $words / 220 ) );
}

/** WhatsApp click-to-chat link, or '' when no number is set. */
function jn_whatsapp_url( $text = '' ) {
	$num = preg_replace( '/\D+/', '', (string) jn_opt( 'whatsapp' ) );
	if ( ! $num ) {
		return '';
	}
	$url = 'https://wa.me/' . $num;
	return $text ? $url . '?text=' . rawurlencode( $text ) : $url;
}

/** Social profile links that have been filled in. */
function jn_social_links() {
	$map = array(
		'linkedin'  => 'LinkedIn',
		'x'         => 'X',
		'facebook'  => 'Facebook',
		'instagram' => 'Instagram',
		'youtube'   => 'YouTube',
	);
	$out = array();
	foreach ( $map as $key => $label ) {
		$url = jn_opt( $key );
		if ( $url ) {
			$out[ $key ] = array( 'label' => $label, 'url' => $url );
		}
	}
	return $out;
}

/** URL of a page by its slug, with a safe fallback. */
function jn_page_url( $slug ) {
	$page = get_page_by_path( $slug );
	return $page ? get_permalink( $page ) : home_url( '/' . $slug . '/' );
}

/** Inline SVG icons (stroke style, inherit currentColor). */
function jn_icon( $name, $class = 'icon' ) {
	$paths = array(
		'arrow'      => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'arrow-down' => '<path d="M12 5v14M6 13l6 6 6-6"/>',
		'programme'  => '<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18M8 4v5M8 14h8M8 17h5"/>',
		'fundraise'  => '<circle cx="12" cy="8" r="5"/><path d="M12 5.5v5M10 7h3a1 1 0 010 2h-2a1 1 0 000 2h3"/><path d="M4 21c2-3 5-4 8-4s6 1 8 4"/>',
		'enterprise' => '<path d="M3 21h18M5 21V10l7-5 7 5v11M9 21v-6h6v6"/>',
		'ai'         => '<rect x="5" y="5" width="14" height="14" rx="3"/><path d="M9 1v4M15 1v4M9 19v4M15 19v4M1 9h4M1 15h4M19 9h4M19 15h4"/><path d="M9.5 14.5l1.5-5 1.5 5M10 13h2M14.5 9.5v5"/>',
		'leaf'       => '<path d="M5 19c0-8 6-14 15-14 0 9-6 15-14 15"/><path d="M5 19c3-4 6-6 10-8"/>',
		'training'   => '<path d="M2 8l10-5 10 5-10 5z"/><path d="M6 10v5c2 2 10 2 12 0v-5M22 8v6"/>',
		'search'     => '<circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/>',
		'download'   => '<path d="M12 3v12M7 10l5 5 5-5M4 21h16"/>',
		'mail'       => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>',
		'phone'      => '<path d="M5 3h4l2 5-3 2a12 12 0 006 6l2-3 5 2v4a2 2 0 01-2 2A18 18 0 013 5a2 2 0 012-2z"/>',
		'whatsapp'   => '<path d="M4 20l1.3-4A8 8 0 1112 20a8 8 0 01-4-1z"/><path d="M9 9c0 3 3 6 6 6l1-1.5-2-1-1 1c-1 0-2.5-1.5-2.5-2.5l1-1-1-2z"/>',
		'linkedin'   => '<rect x="3" y="3" width="18" height="18" rx="3"/><path d="M8 10v7M8 7v.01M12 17v-4a2 2 0 014 0v4M12 10v7"/>',
		'pin'        => '<path d="M12 21s-7-6.2-7-12a7 7 0 0114 0c0 5.8-7 12-7 12z"/><circle cx="12" cy="9" r="2.5"/>',
		'spark'      => '<path d="M12 2v6M12 16v6M2 12h6M16 12h6M5 5l3.5 3.5M15.5 15.5L19 19M5 19l3.5-3.5M15.5 8.5L19 5"/>',
		'check'      => '<path d="M4 12l5 5L20 6"/>',
		'link'       => '<path d="M10 14a4 4 0 005.7 0l3-3a4 4 0 00-5.7-5.7l-1 1M14 10a4 4 0 00-5.7 0l-3 3a4 4 0 005.7 5.7l1-1"/>',
		'clock'      => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
		'lock'       => '<rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 018 0v4"/>',
		'menu'       => '<path d="M3 7h18M3 12h18M3 17h18"/>',
		'close'      => '<path d="M5 5l14 14M19 5L5 19"/>',
		'idea'       => '<path d="M9 18h6M10 21h4M12 3a6 6 0 00-4 10.5c.7.7 1 1.5 1 2.5h6c0-1 .3-1.8 1-2.5A6 6 0 0012 3z"/>',
		'flask'      => '<path d="M9 3h6M10 3v6l-5 9a2 2 0 002 3h10a2 2 0 002-3l-5-9V3M7.5 15h9"/>',
		'file'       => '<path d="M14 3H6a2 2 0 00-2 2v14a2 2 0 002 2h12a2 2 0 002-2V9z"/><path d="M14 3v6h6"/>',
	);
	$p = $paths[ $name ] ?? $paths['spark'];
	return '<svg class="' . esc_attr( $class ) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $p . '</svg>';
}

/**
 * A designed placeholder visual used wherever no photograph has been chosen
 * yet. Deterministic per seed so each card keeps the same artwork.
 */
function jn_placeholder_art( $seed = 'jn', $variant = '' ) {
	$h       = abs( crc32( (string) $seed ) );
	$palette = array(
		array( '#1f3d2b', '#2e5a40', '#b08a4e' ),
		array( '#17181a', '#2b2d31', '#b08a4e' ),
		array( '#24402f', '#3b6b4f', '#d9c7a3' ),
		array( '#2a2620', '#4a3f30', '#c9a46a' ),
	);
	$c   = $palette[ $h % count( $palette ) ];
	$id  = 'g' . substr( md5( $seed . $variant ), 0, 8 );
	$r1  = 60 + ( $h % 90 );
	$x1  = 80 + ( ( $h >> 3 ) % 240 );
	$y1  = 40 + ( ( $h >> 6 ) % 140 );
	$x2  = 60 + ( ( $h >> 9 ) % 280 );
	$y2  = 60 + ( ( $h >> 12 ) % 120 );
	$svg = '<svg class="art" viewBox="0 0 400 240" preserveAspectRatio="xMidYMid slice" role="presentation" aria-hidden="true">'
		. '<defs><linearGradient id="' . $id . '" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="' . $c[0] . '"/><stop offset="1" stop-color="' . $c[1] . '"/></linearGradient></defs>'
		. '<rect width="400" height="240" fill="url(#' . $id . ')"/>'
		. '<g fill="none" stroke="' . $c[2] . '" stroke-opacity=".55" stroke-width="1">'
		. '<circle cx="' . $x1 . '" cy="' . $y1 . '" r="' . $r1 . '"/>'
		. '<circle cx="' . $x1 . '" cy="' . $y1 . '" r="' . ( $r1 + 26 ) . '" stroke-opacity=".25"/>'
		. '<path d="M0 ' . ( 200 - ( $h % 60 ) ) . ' C 120 ' . $y2 . ', 260 ' . ( 240 - $y2 ) . ', 400 ' . ( 80 + ( $h % 80 ) ) . '"/>'
		. '<line x1="' . $x1 . '" y1="' . $y1 . '" x2="' . $x2 . '" y2="' . ( $y2 + 60 ) . '" stroke-opacity=".8"/>'
		. '</g>'
		. '<g fill="' . $c[2] . '"><circle cx="' . $x1 . '" cy="' . $y1 . '" r="3"/><circle cx="' . $x2 . '" cy="' . ( $y2 + 60 ) . '" r="3"/></g>'
		. '</svg>';
	return $svg;
}

/** Featured image of a post, or the designed placeholder. */
function jn_card_media( $post_id = null, $size = 'jn-card' ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	if ( has_post_thumbnail( $post_id ) ) {
		return get_the_post_thumbnail( $post_id, $size, array( 'loading' => 'lazy', 'decoding' => 'async' ) );
	}
	return jn_placeholder_art( get_post_field( 'post_name', $post_id ) . $post_id );
}

/** Portrait image or a monogram placeholder. */
function jn_portrait( $class = 'portrait' ) {
	$id = (int) jn_opt( 'portrait_id', 0 );
	if ( $id && wp_get_attachment_image_src( $id, 'jn-portrait' ) ) {
		return '<figure class="' . esc_attr( $class ) . '">' . wp_get_attachment_image( $id, 'jn-portrait', false, array( 'alt' => esc_attr( jn_opt( 'name' ) . ', ' . jn_opt( 'location' ) ), 'fetchpriority' => 'high' ) ) . '</figure>';
	}
	return '<figure class="' . esc_attr( $class ) . ' portrait--placeholder" aria-label="Portrait placeholder">'
		. '<svg viewBox="0 0 400 500" role="img" aria-hidden="true"><defs><linearGradient id="pg" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#2e5a40"/><stop offset="1" stop-color="#14291d"/></linearGradient></defs>'
		. '<rect width="400" height="500" fill="url(#pg)"/>'
		. '<circle cx="200" cy="190" r="78" fill="none" stroke="#b08a4e" stroke-opacity=".6"/>'
		. '<path d="M70 500c10-110 70-170 130-170s120 60 130 170" fill="none" stroke="#b08a4e" stroke-opacity=".6"/>'
		. '<text x="200" y="208" text-anchor="middle" font-family="Fraunces, Georgia, serif" font-size="56" fill="#f6f2ea">JN</text></svg>'
		. '<figcaption>Portrait coming soon</figcaption></figure>';
}

/** Social share links for the current post. */
function jn_share_links( $post_id = null ) {
	$url   = rawurlencode( get_permalink( $post_id ) );
	$title = rawurlencode( wp_strip_all_tags( get_the_title( $post_id ) ) );
	$links = array(
		'LinkedIn' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . $url,
		'X'        => 'https://twitter.com/intent/tweet?url=' . $url . '&text=' . $title,
		'WhatsApp' => 'https://wa.me/?text=' . $title . '%20' . $url,
		'Email'    => 'mailto:?subject=' . $title . '&body=' . $url,
	);
	$html = '<div class="share"><span class="eyebrow">Share</span>';
	foreach ( $links as $label => $href ) {
		$html .= '<a href="' . esc_url( $href ) . '" target="_blank" rel="noopener">' . esc_html( $label ) . '</a>';
	}
	$html .= '<button type="button" class="share__copy" data-copy="' . esc_attr( get_permalink( $post_id ) ) . '">Copy link</button></div>';
	return $html;
}

/** Section heading block. */
function jn_section_head( $eyebrow, $title, $lead = '', $tag = 'h2' ) {
	echo '<header class="section-head">';
	if ( $eyebrow ) {
		echo '<p class="eyebrow">' . esc_html( $eyebrow ) . '</p>';
	}
	printf( '<%1$s class="section-title">%2$s</%1$s>', esc_attr( $tag ), wp_kses_post( $title ) );
	if ( $lead ) {
		echo '<p class="lead">' . wp_kses_post( $lead ) . '</p>';
	}
	echo '</header>';
}

/** Allow template parts to receive arguments on older WordPress too. */
function jn_part( $slug, $args = array() ) {
	get_template_part( 'template-parts/' . $slug, null, $args );
}
