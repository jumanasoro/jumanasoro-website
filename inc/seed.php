<?php
/**
 * One-time starter content. Runs once after the theme is activated and never
 * overwrites anything you have edited (it checks the "jn_seeded" option).
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', function () {
	if ( get_option( 'jn_seeded' ) ) {
		return;
	}
	update_option( 'jn_seeded', 'running', false );
	jn_seed_all();
	update_option( 'jn_seeded', JN_VERSION, false );
	update_option( 'jn_needs_flush', 1, false );
}, 99 );

// Rewrite rules are rebuilt on the request after seeding, once the new
// permalink and category settings are loaded by every taxonomy.
add_action( 'init', function () {
	if ( get_option( 'jn_needs_flush' ) && ! get_option( 'jn_seeded_flush_lock' ) ) {
		delete_option( 'jn_needs_flush' );
		flush_rewrite_rules( false );
	}
}, 100 );
add_action( 'after_switch_theme', function () {
	update_option( 'jn_needs_flush', 1, false );
} );

/** Very small markdown-ish to block markup converter for seeded copy. */
function jn_blocks( $text ) {
	$out   = '';
	$lines = preg_split( '/\n/', trim( $text ) );
	$list  = array();
	$cls   = '';
	$flush = function () use ( &$list, &$out, &$cls ) {
		if ( ! $list ) {
			return;
		}
		$attrs = $cls ? ' {"className":"' . $cls . '"}' : '';
		$out  .= '<!-- wp:list' . $attrs . ' --><ul' . ( $cls ? ' class="' . $cls . '"' : '' ) . '>';
		foreach ( $list as $li ) {
			$out .= '<!-- wp:list-item --><li>' . $li . '</li><!-- /wp:list-item -->';
		}
		$out .= '</ul><!-- /wp:list -->' . "\n\n";
		$list = array();
		$cls  = '';
	};
	foreach ( $lines as $line ) {
		$line = trim( $line );
		if ( '' === $line ) {
			$flush();
			continue;
		}
		if ( preg_match( '/^\{(.+)\}$/', $line, $m ) ) { // {class} before a list.
			$flush();
			$cls = $m[1];
			continue;
		}
		if ( 0 === strpos( $line, '- ' ) ) {
			$list[] = substr( $line, 2 );
			continue;
		}
		$flush();
		if ( 0 === strpos( $line, '## ' ) ) {
			$out .= '<!-- wp:heading --><h2 class="wp-block-heading">' . substr( $line, 3 ) . '</h2><!-- /wp:heading -->' . "\n\n";
		} elseif ( 0 === strpos( $line, '### ' ) ) {
			$out .= '<!-- wp:heading {"level":3} --><h3 class="wp-block-heading">' . substr( $line, 4 ) . '</h3><!-- /wp:heading -->' . "\n\n";
		} elseif ( 0 === strpos( $line, '> ' ) ) {
			$out .= '<!-- wp:pullquote --><figure class="wp-block-pullquote"><blockquote><p>' . substr( $line, 2 ) . '</p></blockquote></figure><!-- /wp:pullquote -->' . "\n\n";
		} else {
			$out .= '<!-- wp:paragraph --><p>' . $line . '</p><!-- /wp:paragraph -->' . "\n\n";
		}
	}
	$flush();
	return $out;
}

function jn_seed_page( $slug, $title, $content = '', $excerpt = '' ) {
	$page = get_page_by_path( $slug );
	if ( $page && 'publish' === $page->post_status ) {
		return $page->ID;
	}
	if ( $page ) { // e.g. the default draft Privacy Policy page.
		wp_update_post( array(
			'ID'           => $page->ID,
			'post_status'  => 'publish',
			'post_title'   => $title,
			'post_content' => $content ? jn_blocks( $content ) : '',
		) );
		return $page->ID;
	}
	return wp_insert_post( array(
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_name'    => $slug,
		'post_title'   => $title,
		'post_content' => $content ? jn_blocks( $content ) : '',
		'post_excerpt' => $excerpt,
	) );
}

function jn_seed_item( $type, $title, $meta = array(), $args = array() ) {
	$existing = get_posts( array( 'post_type' => $type, 'title' => $title, 'post_status' => 'any', 'posts_per_page' => 1, 'fields' => 'ids' ) );
	if ( $existing ) {
		return $existing[0];
	}
	$id = wp_insert_post( array_merge( array(
		'post_type'   => $type,
		'post_status' => 'publish',
		'post_title'  => $title,
	), $args ) );
	if ( $id && ! is_wp_error( $id ) ) {
		foreach ( $meta as $k => $v ) {
			update_post_meta( $id, '_jn_' . $k, $v );
		}
	}
	return $id;
}

function jn_seed_all() {
	// Site settings.
	update_option( 'blogname', 'Juma Nasoro' );
	update_option( 'blogdescription', 'Development strategist, programme manager, fundraising, enterprise and AI automation consultant' );
	update_option( 'timezone_string', 'Africa/Nairobi' );
	update_option( 'date_format', 'j F Y' );
	global $wp_rewrite;
	$wp_rewrite->set_permalink_structure( '/learning-centre/%postname%/' );
	$wp_rewrite->set_category_base( 'learning-centre/topic' );
	update_option( 'default_comment_status', 'closed' );
	update_option( 'default_ping_status', 'closed' );
	update_option( 'uploads_use_yearmonth_folders', 1 );

	// Remove WordPress sample content.
	foreach ( array( 'hello-world' ) as $slug ) {
		$p = get_page_by_path( $slug, OBJECT, 'post' );
		if ( $p ) {
			wp_delete_post( $p->ID, true );
		}
	}
	$sample = get_page_by_path( 'sample-page' );
	if ( $sample ) {
		wp_delete_post( $sample->ID, true );
	}

	// ---------------------------------------------------------------- Pages.
	$home = jn_seed_page( 'home', 'Home' );

	$about = jn_seed_page( 'about', 'About', <<<'TXT'
I have spent around ten years working where development meets enterprise on Kenya's coast. One lesson runs through all of it: good ideas only change things when they are turned into programmes, institutions and systems that can carry them.

## My professional journey

I trained in commerce at the University of Nairobi and later qualified as a UNDP-certified Enterprise Development Advisor. That grounding shaped how I see small businesses: as engines of livelihoods that need sound models, real markets and good management, not only start-up capital.

Much of my early work was in enterprise and community development in Kwale County and the wider coastal region, with a strong focus on young people and their economic opportunities. This included leading youth enterprise and employment development work under the Kenya Youth Employment and Opportunities Project (KYEOP) in Kwale County. It taught me where programmes succeed or stall in practice: in the quality of training, the follow-up, the data and the partnerships around them.

Over time my work widened into programme management, fundraising and institutional development. Today I serve as Programme Manager at HERI-Kenya (Health + Environmental Research Institute – Kenya), a community-based organisation working across environmental research, community health, sustainability and community development. There I lead programme development, resource mobilisation, partnerships and community engagement, including circular economy and waste-to-value initiatives.

Alongside this I run Juma Nasoro & Co, my independent consultancy practice. Through it I support organisations and businesses with fundraising, enterprise development, project management and digital transformation, from websites and digital systems to, increasingly, AI and automation.

## My approach to development

Sustainable development requires more than good intentions. In my experience, lasting results depend on eight things working together:

{jn-grid}
- <strong>Strong institutions</strong> that can hold the work long after a project ends.
- <strong>Appropriate resources</strong>, raised in ways that build independence rather than dependence.
- <strong>Knowledge and research</strong>, so that decisions rest on evidence and local understanding.
- <strong>Strategic partnerships</strong> that combine what each partner does best.
- <strong>Effective implementation</strong>: plans that survive contact with reality.
- <strong>Accountability</strong> to communities, funders and partners alike.
- <strong>Innovation</strong>, including the willingness to test new tools and models.
- <strong>Long-term sustainability</strong>, designed in from the start rather than added at the end.

## How I think about development

> Ideas become meaningful when they are translated into practical solutions, institutions and systems capable of delivering lasting results.

I am drawn to the space between a good idea and a working system. Most communities and organisations I have worked with do not lack ideas. What they often lack is the structure to move from intention to delivery: a clear programme design, a credible case for funding, a team with defined roles, and information that shows what is working and what is not. My work is to help build that structure, and then keep improving it.

I also believe that African development is best led from within: designed with the people it serves, grounded in local knowledge, and connected to partners across Africa, Europe and North America on equal terms.

## My interests and evolving practice

My practice is moving towards the places where technology can multiply what small teams achieve. I am especially interested in:

{jn-grid}
- <strong>AI-enabled development</strong>: using AI to strengthen research, planning, reporting and learning.
- <strong>Business process automation</strong> that frees people from repetitive work.
- <strong>Digital transformation</strong> for organisations that cannot afford to waste staff time.
- <strong>Technology for nonprofits</strong> that is affordable, appropriate and maintainable.
- <strong>Sustainable enterprise models</strong> that let good work pay for itself.
- <strong>Circular economy</strong> approaches that turn waste into value and income.
- <strong>Institutional innovation</strong>: new ways for organisations to govern, learn and deliver.
- <strong>African-led development solutions</strong> built on local insight and global collaboration.
TXT
	);

	$services_page = jn_seed_page( 'what-i-do', 'What I Do', <<<'TXT'
I work with organisations at very different stages: a community group shaping its first programme, an NGO strengthening its fundraising, a growing business looking for systems that scale, or an institution exploring what AI could do for its operations. The six areas below are connected, and most engagements draw on more than one.

Some assignments I deliver personally. Others, particularly larger technical builds, I design and coordinate with trusted specialists. Either way you have one accountable point of contact from the first conversation to the final handover.
TXT
	);

	$ai_page = jn_seed_page( 'ai-automation', 'AI & Automation', <<<'TXT'
Most organisations I work with are not short of commitment. They are short of time. Staff spend hours searching for funding calls, copying data between spreadsheets, chasing follow-ups and compiling reports, while the work that needs judgement waits.

I help organisations and businesses find those inefficiencies, automate the repetitive parts and adopt AI tools in a way that is practical, affordable and safe. The aim is not technology for its own sake. It is more time for the work only people can do.
TXT
	);

	$consultancy = jn_seed_page( 'consultancy', 'Juma Nasoro & Co', <<<'TXT'
Juma Nasoro & Co is my independent consultancy practice. It brings together programme management, fundraising, enterprise development and digital transformation under one roof, so that clients can work with one partner on problems that rarely fit neatly into a single discipline.

Clients work directly with me, and I bring in specialist collaborators where a piece of work needs them.
TXT
	);

	$lc = jn_seed_page( 'learning-centre', 'The Learning Centre' );

	$contact = jn_seed_page( 'contact', 'Contact', <<<'TXT'
Whether you have a clear brief or an early idea, I am happy to talk. Tell me a little about the challenge or opportunity and how you would like me to get in touch.
TXT
	);

	$privacy = jn_seed_page( 'privacy-policy', 'Privacy Policy', <<<'TXT'
This website is operated by Juma Nasoro, Diani, Kwale County, Kenya. This notice explains what personal information the site collects and how it is used.

## What is collected

When you send an enquiry, request an assessment, subscribe to the newsletter or download a resource, the site stores the details you provide (for example your name, organisation, email address, telephone number and message), together with the date and the page you used.

## How it is used

Your details are used only to respond to you, to send the newsletter you asked for, or to provide the resource you requested. They are not sold or shared with third parties for marketing.

## Where it is stored

Submissions are stored in this website's database on servers provided by Hostinger and may be forwarded to the site owner's email. If a customer relationship management or email marketing tool is connected in future, this notice will be updated to name it.

## Your rights

You may ask to see, correct or delete the information held about you, or unsubscribe at any time, by using the contact page. This site aims to follow the Kenya Data Protection Act, 2019.

## Cookies and analytics

The site uses only the cookies needed for it to work. If website analytics are switched on, anonymous usage statistics are collected to understand which content is useful.

[Placeholder: review this notice and add a contact email and the date it was last updated before the site goes live on its own domain.]
TXT
	);
	if ( $privacy ) {
		update_option( 'wp_page_for_privacy_policy', $privacy );
	}

	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $home );
	update_option( 'page_for_posts', $lc );
	update_option( 'posts_per_page', 12 );

	// ---------------------------------------------------- Portfolio sections.
	$sections = array(
		'active-completed' => array( 'Active & Completed Projects', 'Projects I have managed, led, contributed to or supported.' ),
		'ideas'            => array( 'Ideas Worth Building', 'Initiatives I am developing and want to build with partners. These are proposals, not completed projects.' ),
		'ventures'         => array( 'Ventures & Experiments', 'Independent ventures, digital products and experiments in progress.' ),
	);
	$sec_ids = array();
	foreach ( $sections as $slug => $s ) {
		$t = term_exists( $slug, 'jn_section' );
		if ( ! $t ) {
			$t = wp_insert_term( $s[0], 'jn_section', array( 'slug' => $slug, 'description' => $s[1] ) );
		}
		$sec_ids[ $slug ] = is_array( $t ) ? (int) $t['term_id'] : (int) $t;
	}

	// -------------------------------------------------------------- Projects.
	$projects = array(
		array(
			'title'   => 'HERI-Kenya: Community Development and Planetary Health',
			'section' => 'active-completed',
			'excerpt' => 'Programme leadership at a community-based organisation working across environmental research, community health, circular economy and community resilience on Kenya\'s south coast.',
			'order'   => 1,
			'meta'    => array(
				'status'       => 'active',
				'contribution' => 'organisation',
				'role'         => 'Programme Manager',
				'organisation' => 'HERI-Kenya (Health + Environmental Research Institute – Kenya)',
				'location'     => 'Kwale County, Kenya',
				'featured'     => '1',
				'highlights'   => "Community-based environmental research\nWaste management and circular economy\nBlack Soldier Fly waste-to-value initiatives\nEnvironmental education and School Eco-Clubs\nCommunity capacity development\nFundraising and partnerships\nPlanetary health and environmental resilience",
			),
			'content' => <<<'TXT'
HERI-Kenya (Health + Environmental Research Institute – Kenya) is a community-based organisation working at the intersection of environmental research, community health, sustainability and community development.

## My role

As Programme Manager, my work covers:

- Programme development and management
- Resource mobilisation and fundraising
- Strategic partnerships and institutional development
- Community engagement
- Environmental sustainability, circular economy and waste-to-value initiatives
- Training and capacity development
- Research coordination and implementation

## The organisation's work

HERI-Kenya's programmes connect people's health with the health of the environment they depend on. Its work includes community-based environmental research, waste management and circular economy initiatives (including Black Soldier Fly waste-to-value work), environmental education through School Eco-Clubs, and community capacity development.

This entry describes HERI-Kenya's work as an organisation and my role within it. Organisational results belong to the whole HERI-Kenya team and its partners.
TXT
		),
		array(
			'title'   => 'Black Soldier Fly Circular Economy Initiative, Diani',
			'section' => 'active-completed',
			'excerpt' => 'An organic waste-to-value initiative that uses Black Soldier Fly larvae to turn organic waste into insect protein and organic fertiliser.',
			'order'   => 2,
			'meta'    => array(
				'status'       => 'in-development',
				'contribution' => 'organisation',
				'role'         => 'Programme Manager',
				'organisation' => 'HERI-Kenya',
				'location'     => 'Diani, Kwale County',
				'featured'     => '1',
				'highlights'   => "Circular economy\nOrganic waste management\nSustainable agriculture\nEnterprise development\nWomen's economic empowerment\nEnvironmental innovation\nResearch and learning",
			),
			'content' => <<<'TXT'
Organic waste is one of the most visible environmental challenges in a busy coastal town like Diani. This initiative treats it as a resource.

## The idea

Black Soldier Fly larvae feed on organic waste and convert it into two useful products: protein-rich larvae that can be used in animal feed, and frass, an organic fertiliser. A well-run facility reduces the waste going to dumpsites while creating products with a market.

## Why it matters

- It links environmental management with enterprise, creating potential income streams from waste.
- It supports sustainable agriculture through locally produced feed and fertiliser.
- It creates room for women's economic empowerment through roles in collection, processing and sales.
- It generates research and learning on waste-to-value models suited to coastal Kenya.

## Status

The initiative is being developed. Progress, partners and verified results will be added here as they are confirmed.
TXT
		),
		array(
			'title'   => 'Community Enterprise Development, Kwale County',
			'section' => 'active-completed',
			'excerpt' => 'Youth enterprise and employment development, business training and employment-oriented programmes in Kwale County, including the Kenya Youth Employment and Opportunities Project (KYEOP).',
			'order'   => 3,
			'meta'    => array(
				'status'       => 'completed',
				'contribution' => 'led',
				'location'     => 'Kwale County, Kenya',
				'featured'     => '1',
				'highlights'   => "Youth enterprise development\nEmployment-oriented programmes\nBusiness training\nKenya Youth Employment and Opportunities Project (KYEOP)\nYouth economic empowerment",
			),
			'content' => <<<'TXT'
A large part of my career has been spent helping young people in Kwale County build livelihoods through enterprise and employment.

## What the work involved

I led youth enterprise and employment development initiatives, including work under the Kenya Youth Employment and Opportunities Project (KYEOP) in Kwale County. The work combined business training, enterprise support and employment-oriented activities for young people.

## What it taught me

Enterprise programmes succeed or fail on the details: training that reflects real local markets, follow-up after the classroom, reliable data on participants, and partnerships that open doors to finance and buyers. Those lessons shape how I design enterprise and livelihoods programmes today.

Dates, scale and verified results for this work will be added here.
TXT
		),
		array(
			'title'   => 'Digital Solutions and Organisational Development',
			'section' => 'active-completed',
			'excerpt' => 'Websites, digital systems, AI-enabled workflows and technology solutions that help organisations work more effectively.',
			'order'   => 4,
			'meta'    => array(
				'status'       => 'active',
				'contribution' => 'led',
				'organisation' => 'Juma Nasoro & Co',
				'highlights'   => "Website development\nDigital systems and databases\nAI-enabled workflows\nOrganisational process design\nTechnology solutions for NGOs and SMEs",
			),
			'content' => <<<'TXT'
Alongside programme work, I build and advise on the digital systems organisations run on: websites, data collection tools, databases and, increasingly, AI-enabled workflows.

## Focus

- Websites for organisations and businesses
- Digital forms, databases and information management
- Workflow design and automation
- AI-assisted research, documentation and reporting
- Technology selection and adoption for small teams

Examples of this work will be added here as case studies.
TXT
		),
		array(
			'title'   => 'The Social Economic Pipeline',
			'section' => 'ideas',
			'excerpt' => 'A proposed exchange that hosts skilled visitors in Diani in return for the technology, skills, innovation and ideas they contribute to local socio-economic development.',
			'order'   => 10,
			'meta'    => array(
				'status'        => 'concept',
				'contribution'  => 'proposal',
				'location'      => 'Diani, Kwale County',
				'featured'      => '1',
				'problem'       => 'Diani attracts visitors from all over the world, yet local enterprises, community organisations and schools often struggle to access the specialised skills, technology and fresh ideas that could help them grow.',
				'solution'      => 'Host professionals, technologists and innovators in Diani at no cost to them, in exchange for structured contributions of time, skills, technology or ideas to local initiatives.',
				'vision'        => 'A steady pipeline of knowledge and collaboration into Diani, leaving behind practical improvements in local enterprises and organisations, and lasting relationships between visitors and the community.',
				'model'         => 'Pilot cohorts matched to specific local needs, with hosting provided by partners and contributions agreed before arrival. Details to be developed with partners.',
				'partners'      => "Hotels and hosts in Diani\nLocal enterprises and community organisations\nProfessionals and organisations who want to contribute skills",
				'resources'     => 'To be defined with partners: accommodation, local coordination and a small programme budget.',
				'collaboration' => 'I would like to hear from hosts, potential contributors and local organisations that could benefit.',
			),
			'content' => <<<'TXT'
The Social Economic Pipeline is an idea I am developing to connect people who have skills, technology or ideas with the places in Diani where they could make a real difference.

This is a concept, not an established programme. If it resonates with you, use the form on this page to tell me how you would like to be involved.
TXT
		),
		array(
			'title'   => 'NGO Operations Automation Toolkit (working title)',
			'section' => 'ventures',
			'excerpt' => 'A packaged set of affordable workflows for small NGOs: data collection, M&E dashboards, donor reporting, beneficiary records, grants tracking and communications.',
			'order'   => 20,
			'meta'    => array(
				'status'        => 'concept',
				'contribution'  => 'proposal',
				'featured'      => '1',
				'problem'       => 'Small NGOs and CBOs run on spreadsheets, paper forms and goodwill. Reporting to boards and donors takes days, and data sits in too many places to be useful.',
				'solution'      => 'A set of ready-to-adapt workflows built on affordable tools, covering data collection, monitoring and evaluation dashboards, donor reporting, beneficiary records, grants tracking and communications.',
				'vision'        => 'Small organisations with the operational systems of much larger ones: reports on demand, data in one place and more staff time for programmes.',
				'model'         => 'Build and test with one organisation first, then package for others with setup support and an affordable subscription.',
				'collaboration' => 'Looking for NGOs willing to be early users and partners interested in co-developing the toolkit.',
			),
			'content' => <<<'TXT'
This is an early-stage venture idea that grows out of my work on programme systems and automation. It is being explored and has not yet been launched.
TXT
		),
	);
	foreach ( $projects as $p ) {
		$id = jn_seed_item( 'jn_project', $p['title'], $p['meta'], array(
			'post_excerpt' => $p['excerpt'],
			'post_content' => jn_blocks( $p['content'] ),
			'menu_order'   => $p['order'],
		) );
		if ( $id && isset( $sec_ids[ $p['section'] ] ) ) {
			wp_set_object_terms( $id, array( $sec_ids[ $p['section'] ] ), 'jn_section' );
		}
	}

	// -------------------------------------------------------------- Services.
	$services = array(
		array(
			'Programme and Project Management',
			'Designing, delivering and strengthening programmes that achieve results.',
			array(
				'number'    => '01',
				'icon'      => 'programme',
				'anchor'    => 'programme',
				'problem'   => 'Many projects are funded on the strength of a good idea and then struggle in delivery: unclear results frameworks, overstretched teams, weak data and reports written in a rush at the end of each quarter. Learning is lost, funders lose confidence and impact is hard to demonstrate.',
				'offerings' => "Programme design and development\nProject planning and implementation\nProject coordination\nMonitoring, Evaluation, Accountability and Learning (MEAL)\nProject reporting\nPerformance management\nInstitutional systems development\nProject documentation\nProgramme sustainability planning",
				'value'     => 'Programmes designed to be delivered: clear results, realistic workplans and simple systems for tracking progress. Better reporting, stronger funder confidence and an organisation that learns from its own work.',
				'audience'  => "NGOs and community-based organisations\nDevelopment programmes and consortia\nFoundations and their grantees\nResearch organisations moving into implementation",
				'engage'    => 'Share your programme document, concept note or current challenge. We agree the scope, from a short review to ongoing programme support, before any work begins.',
				'cta'       => 'Discuss a programme',
			),
		),
		array(
			'Fundraising, Grants and Resource Mobilisation',
			'Moving from funding dependence to structured, diversified resource mobilisation.',
			array(
				'number'    => '02',
				'icon'      => 'fundraise',
				'anchor'    => 'fundraising',
				'problem'   => 'Many organisations depend on one or two funders and chase calls for proposals one deadline at a time. Without a pipeline, donor research or a clear case for support, fundraising stays reactive and every funding gap becomes a crisis.',
				'offerings' => "Grant opportunity identification\nGrant research\nGrant proposal development\nFundraising strategy\nDonor mapping\nDonor engagement\nResource mobilisation\nFundraising campaign development\nPartnership development\nDonor stewardship\nInstitutional fundraising capacity strengthening",
				'value'     => 'A clear funding strategy, a pipeline of researched opportunities, stronger proposals and relationships with funders that last beyond a single grant.',
				'audience'  => "CBOs and NGOs seeking institutional funding\nOrganisations diversifying their funding base\nSocial enterprises and research groups preparing bids\nTeams building in-house fundraising capacity",
				'engage'    => 'Tell me about your organisation, your current funding and what you want to fund. A first conversation usually shows whether you need a strategy, a specific proposal or both.',
				'cta'       => 'Talk about fundraising',
			),
		),
		array(
			'Enterprise and Business Development',
			'Building viable, growing enterprises and the programmes that support them.',
			array(
				'number'    => '03',
				'icon'      => 'enterprise',
				'anchor'    => 'enterprise',
				'problem'   => 'Small enterprises create most livelihoods in our communities, yet many operate without a tested business model, a reliable market or basic management systems. Support programmes often focus on start-up capital and training days rather than the steady work of building a viable business.',
				'offerings' => "Business model development\nEnterprise assessments\nBusiness planning\nBusiness strategy\nMarket development\nMSME development\nEntrepreneurship training\nBusiness growth support\nEnterprise sustainability\nSocial enterprise development",
				'value'     => 'Enterprises and enterprise programmes grounded in a working model: clearer value propositions, realistic plans, better market linkages and the management habits that help businesses survive and grow. This draws on my training as a UNDP-certified Enterprise Development Advisor and my work in youth economic empowerment in Kwale County.',
				'audience'  => "MSMEs and entrepreneurs\nYouth and women's enterprise programmes\nSocial enterprises\nNGOs designing livelihoods or enterprise components",
				'engage'    => 'Describe the business or programme and where it is stuck. We start with an assessment, then agree what support makes sense.',
				'cta'       => 'Grow an enterprise',
			),
		),
		array(
			'AI, Automation and Digital Transformation',
			'Helping organisations and businesses work smarter with technology, automation and AI.',
			array(
				'number'    => '04',
				'icon'      => 'ai',
				'anchor'    => 'ai',
				'problem'   => 'Small teams spend a large share of their week on repetitive work: searching for funding calls, chasing follow-ups, copying data between spreadsheets and compiling reports. At the same time AI tools are changing quickly, and most organisations are unsure where to start or what is safe and worthwhile.',
				'offerings' => "## AI for Organisations\nAI-powered organisational assistants\nAI research and documentation workflows\nKnowledge management assistants\nAutomated reporting\nAI-assisted content development\nInternal information retrieval systems\nAI-enabled administrative support\n## NGO and Fundraising Automation\nAutomated grant opportunity monitoring\nFundraising research workflows\nDonor relationship management\nCRM implementation\nAutomated donor follow-ups\nProposal development workflows\nFundraising campaign automation\nStakeholder communication systems\nAutomated programme reporting\n## Business Process Automation\nCustomer relationship management\nLead generation and management\nAutomated customer follow-ups\nSales and marketing automation\nCustomer support workflows\nAdministrative process automation\nBusiness operations automation\nAppointment and booking workflows\nAutomated business reporting\n## AI Agents and Intelligent Workflows\nCustom AI assistants\nAI-powered research agents\nBusiness knowledge assistants\nAutomated information processing\nMulti-step workflow automation\nAI-enabled organisational support systems\n## Digital Infrastructure\nWebsite development\nBusiness websites\nOrganisational portals\nDigital databases\nInformation management systems\nDigital forms and data collection\nSystems integration\nDigital workflow development\n## Technology Consulting\nOrganisational digital assessments\nAutomation opportunity assessments\nTechnology selection\nAI adoption strategies\nDigital transformation planning\nTechnology integration\nOrganisational capacity development",
				'value'     => 'Less time on routine work and more on the work that matters: clearer information, faster reporting, more consistent follow-up with donors and customers, and an adoption plan your team can sustain.',
				'audience'  => "NGOs, CBOs and foundations\nSMEs and startups\nResearch organisations\nAny team losing hours to manual admin",
				'engage'    => 'Book an automation assessment. We map a few of your core processes, identify what can be automated or AI-assisted, and agree a practical first step.',
				'note'      => 'I lead the assessment, solution design and implementation coordination. Where a build needs specialist engineering, I work with trusted technical collaborators and remain your single point of contact.',
				'cta'       => 'Book an automation assessment',
			),
		),
		array(
			'Environmental Sustainability and Circular Economy',
			'Connecting environmental action with enterprise and community economic opportunity.',
			array(
				'number'    => '05',
				'icon'      => 'leaf',
				'anchor'    => 'sustainability',
				'problem'   => 'Organic and plastic waste is a daily problem in coastal communities and is usually treated purely as a cost. At the same time, environmental programmes often struggle to create incomes, which makes them hard to sustain once funding ends.',
				'offerings' => "Circular economy programme development\nCommunity-based waste management\nWaste-to-value initiatives\nEnvironmental awareness\nClimate resilience\nGreen enterprise development\nSustainability strategy\nEnvironmental training\nCommunity environmental innovation",
				'value'     => 'Sustainability initiatives where environmental outcomes and economic opportunity reinforce each other: waste becomes a resource, communities gain income streams and the work becomes easier to sustain.',
				'audience'  => "Community-based organisations\nEnvironmental NGOs and research institutions\nCounty and community development actors\nGreen entrepreneurs and enterprises",
				'engage'    => 'Share the challenge or idea, whether a waste stream, a community or a programme concept, and we explore what a viable model could look like.',
				'cta'       => 'Explore a sustainability idea',
			),
		),
		array(
			'Training and Capacity Development',
			'Learning that turns into new practice.',
			array(
				'number'    => '06',
				'icon'      => 'training',
				'anchor'    => 'training',
				'problem'   => 'Training is often delivered as a one-off event that leaves little behind. Without good design, relevant materials and follow-up, new knowledge rarely becomes new practice.',
				'offerings' => "Training programme design\nCurriculum development\nLearning material development\nEnterprise development training\nFundraising capacity development\nDigital skills training\nAI literacy and adoption\nCommunity facilitation\nOrganisational strengthening",
				'value'     => 'Learning designed around real tasks, with practical materials participants keep using. Teams leave able to do something they could not do before, from writing a proposal to using AI tools safely.',
				'audience'  => "NGOs and CBOs\nEnterprise and youth programmes\nBusinesses adopting new tools\nDevelopment partners commissioning training",
				'engage'    => 'Tell me who the training is for and what they should be able to do afterwards. I will propose a format, from a half-day workshop to a structured programme.',
				'cta'       => 'Plan a training',
			),
		),
	);
	foreach ( $services as $i => $s ) {
		jn_seed_item( 'jn_service', $s[0], $s[2], array( 'post_excerpt' => $s[1], 'menu_order' => $i + 1 ) );
	}

	// ---------------------------------------------------- Automation examples.
	$auto = array(
		array( 'Grant research and opportunity tracking', 'Fundraising', 'search', 'Someone checks funder websites and newsletters when they have time. Good opportunities are found late or missed entirely.', "Funder sites, newsletters and databases are monitored automatically\nAI screens each call against your eligibility and priorities\nMatches land in a shared tracker with deadlines and a short summary\nThe team gets a weekly digest and reminders before deadlines", 'A steady, searchable pipeline of relevant opportunities and fewer missed deadlines.', 'RSS and email parsing, an AI model, Google Sheets or Airtable' ),
		array( 'Donor follow-ups', 'Fundraising', 'fundraise', 'Thank-you notes, updates and reports depend on someone remembering. Relationships cool between grants.', "Every donor and gift is recorded in a simple CRM\nThank-you messages go out automatically after a gift\nUpdate and report reminders are scheduled from grant dates\nDrafts are prepared for a person to personalise and send", 'Consistent stewardship and donors who hear from you at the right moments.', 'A CRM such as HubSpot, Zoho or Airtable, email automation' ),
		array( 'Email management', 'Operations', 'mail', 'Shared inboxes overflow. Important messages wait while staff sort routine ones by hand.', "Incoming email is labelled and routed by topic\nRoutine questions get a suggested reply drafted for review\nAction items are pulled into a task list\nA daily summary highlights what needs attention", 'Faster responses and fewer messages lost in the inbox.', 'Gmail or Outlook rules, an AI assistant, Zapier or Make' ),
		array( 'Customer enquiries', 'Business', 'phone', 'Enquiries arrive by phone, WhatsApp, email and social media, and are answered when someone is free.', "Enquiries from every channel are captured in one place\nCommon questions are answered instantly\nComplex ones are passed to the right person with full context\nEvery enquiry is tracked until it is resolved", 'Quicker answers, fewer lost customers and a record of what people ask about.', 'WhatsApp Business, website forms, a helpdesk or CRM' ),
		array( 'Lead generation and management', 'Business', 'enterprise', 'Leads are noted in notebooks or chat threads. Follow-up depends on memory.', "Leads from forms, events and referrals flow into one pipeline\nEach lead is scored and assigned\nFollow-up sequences start automatically\nA dashboard shows the pipeline at a glance", 'More leads converted and a clear view of where business comes from.', 'A CRM, website forms, email sequences' ),
		array( 'Project reporting', 'Programmes', 'programme', 'Before each report, staff gather figures from several spreadsheets and rewrite the same narrative again.', "Field and programme data feed a single source of truth\nIndicators update automatically on a dashboard\nAI drafts the narrative from the data and field notes\nThe programme team reviews, edits and approves", 'Reports in hours rather than days, with consistent figures across documents.', 'KoboToolbox or Google Forms, Google Sheets, Looker Studio, an AI model' ),
		array( 'Staff task allocation', 'Operations', 'check', 'Tasks are assigned in meetings and chat groups. It is hard to see who is doing what.', "Requests are logged through one simple form\nTasks are assigned by rules or workload\nDeadlines and reminders are automatic\nManagers see progress without chasing", 'Clear ownership, fewer dropped tasks and less time in status meetings.', 'Trello, Asana, ClickUp or Google Workspace' ),
		array( 'Meeting scheduling', 'Operations', 'clock', 'Finding a time takes a chain of messages. Notes and actions are rarely shared afterwards.', "People book from your real availability through a link\nReminders reduce no-shows\nAI produces notes and action points\nActions are sent to attendees and the task list", 'Less back-and-forth and meetings that lead to action.', 'Calendly or Google appointment schedules, meeting note tools' ),
		array( 'Data collection', 'Programmes', 'file', 'Paper forms are filled in the field and typed up later, with errors and delays.', "Mobile forms work offline in the field\nData is validated as it is entered\nSubmissions sync to a central database automatically\nDashboards refresh without manual work", 'Cleaner data available the same day it is collected.', 'KoboToolbox, ODK, Google Forms, AppSheet' ),
		array( 'Invoice and document workflows', 'Finance', 'file', 'Invoices, receipts and approvals move by email and paper. Documents are hard to find later.', "Documents are captured from email or photos automatically\nKey details are extracted and checked\nApprovals are routed to the right person\nEverything is filed and searchable", 'Faster approvals, fewer errors and an audit trail.', 'Accounting software, document AI, Google Drive or SharePoint' ),
		array( 'Marketing communications', 'Communications', 'spark', 'Newsletters and social posts are written from scratch each time, when there is time.', "A simple content calendar plans the month\nAI drafts posts and newsletters from programme updates\nA person reviews and approves\nContent is scheduled across channels automatically", 'Regular, consistent communication without a full-time communications officer.', 'Brevo or Mailchimp, a social scheduler, an AI model' ),
		array( 'Knowledge management', 'Organisation', 'idea', 'Policies, reports and lessons are scattered across laptops and inboxes. Staff ask the same questions repeatedly.', "Key documents are gathered into one organised library\nAn AI assistant answers staff questions from those documents\nAnswers cite the source document\nThe library grows as new documents are added", 'Institutional knowledge that stays when people move on, and faster answers for staff.', 'Google Drive or SharePoint, an AI knowledge assistant' ),
	);
	foreach ( $auto as $i => $a ) {
		jn_seed_item( 'jn_automation', $a[0], array(
			'area'      => $a[1],
			'icon'      => $a[2],
			'today'     => $a[3],
			'automated' => $a[4],
			'outcome'   => $a[5],
			'tools'     => $a[6],
		), array( 'menu_order' => $i + 1 ) );
	}

	// -------------------------------------------------------------- Timeline.
	$milestones = array(
		array( 'Bachelor of Commerce', 'education', 'University of Nairobi', '', 'Undergraduate degree in commerce, the foundation for my work in enterprise, finance and management.' ),
		array( 'UNDP-certified Enterprise Development Advisor (EDA)', 'certification', 'UNDP', '', 'Certification in enterprise development advisory practice.' ),
		array( 'Youth enterprise and employment development, Kwale County', 'project', 'Including the Kenya Youth Employment and Opportunities Project (KYEOP)', '', 'Led youth enterprise and employment development initiatives, including business training and employment-oriented programmes.' ),
		array( 'Programme Manager', 'role', 'HERI-Kenya', 'Present', 'Programme development and management, resource mobilisation, partnerships, community engagement and research coordination.' ),
		array( 'Juma Nasoro & Co', 'venture', 'Independent consultancy practice', '', 'Fundraising, enterprise development, project management and digital transformation support for organisations and businesses.' ),
		array( 'AI and automation practice', 'development', 'Juma Nasoro & Co', 'Ongoing', 'Building a practice in AI adoption and process automation for NGOs and businesses.' ),
	);
	foreach ( $milestones as $i => $m ) {
		jn_seed_item( 'jn_milestone', $m[0], array( 'kind' => $m[1], 'organisation' => $m[2], 'when' => $m[3] ), array( 'post_excerpt' => $m[4], 'menu_order' => $i + 1 ) );
	}

	// ------------------------------------------- Learning Centre categories.
	$cats = array(
		'ai-automation'     => array( 'AI, Automation and Digital Transformation', 'AI for NGOs, business automation, AI agents, automation tools, digital transformation, AI for fundraising, productivity systems and digital innovation.' ),
		'fundraising'       => array( 'Fundraising, Grants and Resource Mobilisation', 'Grant writing, donor research, fundraising strategy, donor engagement, institutional funding, fundraising campaigns and resource mobilisation.' ),
		'programme-management' => array( 'Programme and Project Management', 'Programme design, project implementation, MEAL, project reporting, results-based management and organisational systems.' ),
		'enterprise'        => array( 'Enterprise and Business Development', 'Entrepreneurship, business models, MSME development, enterprise sustainability, market development and social enterprise.' ),
		'sustainability'    => array( 'Sustainability and Circular Economy', 'Waste management, circular economy, climate resilience, planetary health, environmental innovation and sustainable enterprise.' ),
		'leadership'        => array( 'Leadership, Strategy and Institutional Development', 'Strategic thinking, leadership, institutional strengthening, organisational development, partnership building and development philosophy.' ),
		'toolkits'          => array( 'Practical Guides, Templates and Toolkits', 'Grant proposal templates, fundraising frameworks, project management tools, business planning templates, AI prompts, automation workflow guides, NGO templates, MEAL tools and training materials.' ),
	);
	$cat_ids = array();
	foreach ( $cats as $slug => $c ) {
		$t = term_exists( $slug, 'category' );
		if ( ! $t ) {
			$t = wp_insert_term( $c[0], 'category', array( 'slug' => $slug, 'description' => $c[1] ) );
		}
		$cat_ids[ $slug ] = is_array( $t ) ? (int) $t['term_id'] : (int) $t;
	}
	// Rename "Uncategorized" so it is not shown publicly by accident.
	$unc = get_term_by( 'slug', 'uncategorized', 'category' );
	if ( $unc ) {
		wp_update_term( $unc->term_id, 'category', array( 'name' => 'General', 'slug' => 'general' ) );
	}

	// Planned articles: drafts only, never published automatically.
	$planned = array(
		array( 'How NGOs Can Use AI to Improve Their Operations', 'ai-automation' ),
		array( 'Why Good Projects Struggle Without Strong Institutional Systems', 'programme-management' ),
		array( 'Building a Sustainable Fundraising Strategy for Community-Based Organisations', 'fundraising' ),
		array( 'From Research to Implementation: Closing the Development Gap', 'leadership' ),
		array( 'How Business Automation Can Improve Productivity for SMEs', 'ai-automation' ),
		array( 'The Circular Economy Opportunity in Coastal Kenya', 'sustainability' ),
		array( 'Why Community-Based Enterprises Matter', 'enterprise' ),
		array( 'Practical AI Tools for Programme Managers', 'ai-automation' ),
	);
	foreach ( $planned as $i => $pa ) {
		$id = jn_seed_item( 'post', $pa[0], array( 'planned' => '1' ), array(
			'post_status'  => 'draft',
			'post_content' => '<!-- wp:paragraph --><p>Draft: write this article here. When it is ready, add a featured image and an excerpt, untick "Show this draft as a planned topic" if you like, and press Publish.</p><!-- /wp:paragraph -->',
			'menu_order'   => $i,
		) );
		if ( $id ) {
			wp_set_post_categories( $id, array( $cat_ids[ $pa[1] ] ) );
		}
	}

	// A draft resource to show how the library works (not public).
	jn_seed_item( 'jn_resource', 'Example resource (draft – replace me)', array( 'access' => 'free', 'format' => 'PDF guide' ), array(
		'post_status'  => 'draft',
		'post_excerpt' => 'One or two sentences describing what the resource is and who it helps.',
		'post_content' => '<!-- wp:paragraph --><p>Describe the resource here. Upload the file in the "Download" box below, choose a category on the right, then publish.</p><!-- /wp:paragraph -->',
	) );

	// ----------------------------------------------------------------- Menus.
	if ( ! wp_get_nav_menu_object( 'Main navigation' ) ) {
		$menu_id = wp_create_nav_menu( 'Main navigation' );
		if ( ! is_wp_error( $menu_id ) ) {
			$items = array(
				array( 'Home', 'page', $home ),
				array( 'About', 'page', $about ),
				array( 'What I Do', 'page', $services_page ),
				array( 'Projects & Ventures', 'archive', 'jn_project' ),
				array( 'Learning Centre', 'page', $lc ),
				array( 'Contact', 'page', $contact ),
			);
			foreach ( $items as $pos => $it ) {
				$args = array(
					'menu-item-title'    => $it[0],
					'menu-item-status'   => 'publish',
					'menu-item-position' => $pos + 1,
				);
				if ( 'page' === $it[1] ) {
					$args['menu-item-object']    = 'page';
					$args['menu-item-object-id'] = $it[2];
					$args['menu-item-type']      = 'post_type';
				} else {
					$args['menu-item-type']   = 'post_type_archive';
					$args['menu-item-object'] = $it[2];
				}
				wp_update_nav_menu_item( $menu_id, 0, $args );
			}
			$locations            = get_theme_mod( 'nav_menu_locations', array() );
			$locations['primary'] = $menu_id;
			set_theme_mod( 'nav_menu_locations', $locations );
		}
	}

	// Keep the temporary domain out of search results until the real domain is connected.
	if ( false !== strpos( home_url(), 'hostingersite.com' ) ) {
		update_option( 'blog_public', '0' );
	}
}
