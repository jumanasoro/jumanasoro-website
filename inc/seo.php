<?php
/**
 * Search engine optimisation: titles, descriptions, Open Graph, structured
 * data, analytics and indexing controls. No SEO plugin required.
 */

defined( 'ABSPATH' ) || exit;

/** Default titles and descriptions for the core pages, keyed by slug. */
function jn_seo_defaults() {
	return array(
		'home'            => array(
			'Juma Nasoro | Development Strategist, Fundraising & AI Automation Consultant, Kenya',
			'Juma Nasoro is a programme manager and consultant in Diani, Kenya, helping NGOs, businesses and development initiatives design programmes, mobilise resources and adopt AI and automation.',
		),
		'about'           => array(
			'About Juma Nasoro | Programme Manager & Development Practitioner, Kenya',
			'The professional journey, approach and philosophy of Juma Nasoro: programme management, enterprise development, fundraising and technology-enabled development on Kenya\'s coast.',
		),
		'what-i-do'       => array(
			'Consultancy Services | Programme Management, Fundraising, Enterprise & AI | Juma Nasoro',
			'Programme management, grant writing and fundraising, enterprise development, AI and automation, sustainability and training services for NGOs, CBOs, SMEs and development institutions in Kenya and beyond.',
		),
		'ai-automation'   => array(
			'AI & Automation Consultant Kenya | AI for NGOs and Businesses | Juma Nasoro',
			'Practical AI and business automation for NGOs, CBOs and SMEs in Kenya: grant monitoring, donor follow-ups, reporting, CRM and intelligent workflows. Book an automation assessment.',
		),
		'consultancy'     => array(
			'Juma Nasoro & Co | Independent Development, Fundraising & Digital Consultancy',
			'Juma Nasoro & Co is an independent consultancy helping NGOs, CBOs, SMEs, social enterprises and foundations develop projects, mobilise resources, strengthen systems and adopt AI.',
		),
		'learning-centre' => array(
			'The Learning Centre | Guides on Fundraising, Programme Management, Enterprise & AI',
			'Knowledge, ideas and practical tools for building better organisations, stronger enterprises and sustainable communities.',
		),
		'contact'         => array(
			'Contact Juma Nasoro | Consultancy, Partnerships & Speaking',
			'Get in touch with Juma Nasoro in Diani, Kwale County, Kenya about consultancy, AI and automation projects, fundraising, programme management, training, speaking or partnerships.',
		),
		'projects'        => array(
			'Projects, Initiatives & Ventures | Juma Nasoro',
			'Active and completed projects, ideas worth building, and ventures by Juma Nasoro across community development, circular economy, enterprise and digital solutions in Kenya.',
		),
		'resources'       => array(
			'Resource Library | Templates, Toolkits & Guides | Juma Nasoro',
			'Downloadable templates, frameworks and guides for fundraising, programme management, enterprise development, MEAL and AI automation.',
		),
	);
}

/** Resolve the SEO title and description for the current request. */
function jn_seo_context() {
	static $ctx = null;
	if ( null !== $ctx ) {
		return $ctx;
	}
	$d     = jn_seo_defaults();
	$title = '';
	$desc  = '';
	$image = '';
	$type  = 'website';

	if ( is_front_page() ) {
		list( $title, $desc ) = $d['home'];
	} elseif ( is_home() ) {
		list( $title, $desc ) = $d['learning-centre'];
	} elseif ( is_post_type_archive( 'jn_project' ) ) {
		list( $title, $desc ) = $d['projects'];
	} elseif ( is_post_type_archive( 'jn_resource' ) ) {
		list( $title, $desc ) = $d['resources'];
	} elseif ( is_singular() ) {
		$id   = get_queried_object_id();
		$slug = get_post_field( 'post_name', $id );
		if ( is_page() && isset( $d[ $slug ] ) ) {
			list( $title, $desc ) = $d[ $slug ];
		} else {
			$title = get_the_title( $id ) . ' | Juma Nasoro';
			$desc  = has_excerpt( $id ) ? get_the_excerpt( $id ) : wp_trim_words( wp_strip_all_tags( get_post_field( 'post_content', $id ) ), 28, '…' );
		}
		if ( $t = jn_meta( 'seo_title', $id ) ) {
			$title = $t;
		}
		if ( $m = jn_meta( 'seo_desc', $id ) ) {
			$desc = $m;
		}
		if ( has_post_thumbnail( $id ) ) {
			$image = get_the_post_thumbnail_url( $id, 'jn-wide' );
		}
		if ( is_singular( 'post' ) ) {
			$type = 'article';
		}
	} elseif ( is_category() || is_tax() ) {
		$term  = get_queried_object();
		$title = $term->name . ' | The Learning Centre | Juma Nasoro';
		$desc  = $term->description ? wp_strip_all_tags( $term->description ) : 'Articles and resources on ' . $term->name . ' from Juma Nasoro\'s Learning Centre.';
		if ( is_tax( 'jn_section' ) ) {
			$title = $term->name . ' | Projects, Initiatives & Ventures | Juma Nasoro';
		}
	} elseif ( is_search() ) {
		$title = 'Search results for "' . get_search_query() . '" | Juma Nasoro';
	} elseif ( is_404() ) {
		$title = 'Page not found | Juma Nasoro';
	}

	if ( ! $image ) {
		$pid   = (int) jn_opt( 'portrait_id', 0 );
		$image = $pid ? wp_get_attachment_image_url( $pid, 'jn-wide' ) : JN_URI . '/assets/img/og-default.png';
	}
	$ctx = array(
		'title' => $title,
		'desc'  => wp_strip_all_tags( (string) $desc ),
		'image' => $image,
		'type'  => $type,
	);
	return $ctx;
}

add_filter( 'pre_get_document_title', function ( $t ) {
	$ctx = jn_seo_context();
	return $ctx['title'] ? $ctx['title'] : $t;
} );

add_filter( 'wp_robots', function ( $robots ) {
	if ( is_singular() && jn_meta( 'noindex', get_queried_object_id() ) ) {
		$robots['noindex'] = true;
	}
	if ( is_search() || is_404() ) {
		$robots['noindex'] = true;
	}
	return $robots;
} );

add_action( 'wp_head', function () {
	$ctx  = jn_seo_context();
	$url  = is_singular() ? get_permalink() : home_url( add_query_arg( array() ) );
	$site = get_bloginfo( 'name' );
	echo "\n<!-- SEO -->\n";
	if ( $ctx['desc'] ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( wp_html_excerpt( $ctx['desc'], 300, '' ) ) );
	}
	if ( ! is_singular() && ! is_search() && ! is_404() ) {
		printf( '<link rel="canonical" href="%s">' . "\n", esc_url( strtok( $url, '?' ) ) );
	}
	$og = array(
		'og:site_name'   => $site,
		'og:type'        => $ctx['type'],
		'og:title'       => $ctx['title'] ? $ctx['title'] : wp_get_document_title(),
		'og:description' => $ctx['desc'],
		'og:url'         => $url,
		'og:image'       => $ctx['image'],
		'og:locale'      => 'en_GB',
	);
	foreach ( $og as $k => $v ) {
		if ( $v ) {
			printf( '<meta property="%s" content="%s">' . "\n", esc_attr( $k ), esc_attr( $v ) );
		}
	}
	printf( '<meta name="twitter:card" content="summary_large_image">' . "\n" );
	if ( is_singular( 'post' ) ) {
		printf( '<meta property="article:published_time" content="%s">' . "\n", esc_attr( get_the_date( 'c' ) ) );
		printf( '<meta property="article:modified_time" content="%s">' . "\n", esc_attr( get_the_modified_date( 'c' ) ) );
	}
	if ( $gsc = jn_opt( 'search_console' ) ) {
		printf( '<meta name="google-site-verification" content="%s">' . "\n", esc_attr( $gsc ) );
	}
	echo '<meta name="theme-color" content="#14291d">' . "\n";

	// Structured data.
	$person = array(
		'@type'       => 'Person',
		'@id'         => home_url( '/#person' ),
		'name'        => jn_opt( 'name' ),
		'url'         => home_url( '/' ),
		'jobTitle'    => 'Programme Manager and Development Consultant',
		'description' => 'Development strategist, programme manager, fundraising and enterprise development consultant, and AI and automation consultant based in Diani, Kenya.',
		'address'     => array(
			'@type'           => 'PostalAddress',
			'addressLocality' => 'Diani',
			'addressRegion'   => 'Kwale County',
			'addressCountry'  => 'KE',
		),
		'alumniOf'    => array( '@type' => 'CollegeOrUniversity', 'name' => 'University of Nairobi' ),
		'hasCredential' => array(
			'@type'              => 'EducationalOccupationalCredential',
			'name'               => 'Enterprise Development Advisor (EDA)',
			'recognizedBy'       => array( '@type' => 'Organization', 'name' => 'UNDP' ),
		),
		'knowsAbout'  => array( 'Programme management', 'Fundraising', 'Grant writing', 'Resource mobilisation', 'Enterprise development', 'MSME development', 'Circular economy', 'Community development', 'AI for NGOs', 'Business process automation', 'Digital transformation', 'Monitoring, Evaluation, Accountability and Learning' ),
		'worksFor'    => array(
			array( '@type' => 'Organization', 'name' => 'Juma Nasoro & Co', '@id' => home_url( '/#consultancy' ) ),
		),
	);
	$same = array_values( array_map( function ( $l ) {
		return $l['url'];
	}, jn_social_links() ) );
	if ( $same ) {
		$person['sameAs'] = $same;
	}
	if ( $pid = (int) jn_opt( 'portrait_id', 0 ) ) {
		$person['image'] = wp_get_attachment_image_url( $pid, 'jn-portrait' );
	}
	$graph = array(
		$person,
		array(
			'@type'     => 'WebSite',
			'@id'       => home_url( '/#website' ),
			'url'       => home_url( '/' ),
			'name'      => $site,
			'publisher' => array( '@id' => home_url( '/#person' ) ),
			'potentialAction' => array(
				'@type'       => 'SearchAction',
				'target'      => home_url( '/?s={search_term_string}' ),
				'query-input' => 'required name=search_term_string',
			),
		),
	);
	if ( is_front_page() || is_page( 'consultancy' ) || is_page( 'what-i-do' ) ) {
		$svc = array(
			'@type'       => 'ProfessionalService',
			'@id'         => home_url( '/#consultancy' ),
			'name'        => 'Juma Nasoro & Co',
			'url'         => jn_page_url( 'consultancy' ),
			'founder'     => array( '@id' => home_url( '/#person' ) ),
			'areaServed'  => array( 'Kenya', 'Africa', 'Worldwide' ),
			'address'     => $person['address'],
			'description' => 'Independent consultancy for programme and project management, fundraising and resource mobilisation, enterprise development, AI and automation, sustainability and training.',
		);
		if ( $e = jn_opt( 'email' ) ) {
			$svc['email'] = $e;
		}
		if ( $t = jn_opt( 'phone' ) ) {
			$svc['telephone'] = $t;
		}
		$graph[] = $svc;
	}
	if ( is_singular( 'post' ) ) {
		$graph[] = array(
			'@type'            => 'Article',
			'headline'         => get_the_title(),
			'description'      => $ctx['desc'],
			'datePublished'    => get_the_date( 'c' ),
			'dateModified'     => get_the_modified_date( 'c' ),
			'author'           => array( '@id' => home_url( '/#person' ) ),
			'publisher'        => array( '@id' => home_url( '/#person' ) ),
			'mainEntityOfPage' => get_permalink(),
			'image'            => $ctx['image'],
			'articleSection'   => wp_list_pluck( (array) get_the_category(), 'name' ),
		);
	}
	if ( is_singular() && ! is_front_page() ) {
		$crumbs = array( array( 'Home', home_url( '/' ) ) );
		if ( is_singular( 'post' ) ) {
			$crumbs[] = array( 'Learning Centre', jn_page_url( 'learning-centre' ) );
		} elseif ( is_singular( 'jn_project' ) ) {
			$crumbs[] = array( 'Projects & Ventures', get_post_type_archive_link( 'jn_project' ) );
		} elseif ( is_singular( 'jn_resource' ) ) {
			$crumbs[] = array( 'Resources', get_post_type_archive_link( 'jn_resource' ) );
		}
		$crumbs[] = array( get_the_title(), get_permalink() );
		$items    = array();
		foreach ( $crumbs as $i => $c ) {
			$items[] = array( '@type' => 'ListItem', 'position' => $i + 1, 'name' => wp_strip_all_tags( $c[0] ), 'item' => $c[1] );
		}
		$graph[] = array( '@type' => 'BreadcrumbList', 'itemListElement' => $items );
	}
	echo '<script type="application/ld+json">' . wp_json_encode( array( '@context' => 'https://schema.org', '@graph' => $graph ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";

	// Analytics (only when an ID is configured).
	if ( ( $ga = jn_opt( 'analytics_id' ) ) && preg_match( '/^G-[A-Z0-9]+$/', $ga ) && ! is_user_logged_in() ) {
		printf( '<script async src="https://www.googletagmanager.com/gtag/js?id=%1$s"></script><script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag("js",new Date());gtag("config","%1$s");</script>' . "\n", esc_attr( $ga ) );
	}
}, 2 );

// Sitemap: keep private/utility types and users out.
add_filter( 'wp_sitemaps_post_types', function ( $types ) {
	unset( $types['jn_enquiry'], $types['jn_subscriber'] );
	return $types;
} );
add_filter( 'wp_sitemaps_add_provider', function ( $provider, $name ) {
	return 'users' === $name ? false : $provider;
}, 10, 2 );
add_filter( 'wp_sitemaps_posts_query_args', function ( $args ) {
	$args['meta_query'] = array(
		'relation' => 'OR',
		array( 'key' => '_jn_noindex', 'compare' => 'NOT EXISTS' ),
		array( 'key' => '_jn_noindex', 'value' => '1', 'compare' => '!=' ),
	);
	return $args;
} );

// Do not expose author archives (single-author site).
add_action( 'template_redirect', function () {
	if ( is_author() || is_date() || is_attachment() ) {
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
} );
