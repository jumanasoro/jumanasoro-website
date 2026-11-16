<?php
defined( 'ABSPATH' ) || exit;
get_header();

$pillar_links = array(
	jn_page_url( 'what-i-do' ) . '#programme',
	jn_page_url( 'what-i-do' ) . '#fundraising',
	jn_page_url( 'ai-automation' ),
	jn_page_url( 'what-i-do' ) . '#sustainability',
);
$pillar_icons = array( 'programme', 'fundraise', 'ai', 'leaf' );
?>

<section class="hero">
	<div class="wrap hero__inner">
		<div class="hero__copy">
			<h1 class="hero__title">
				<span class="hero__name"><?php echo esc_html( jn_opt( 'name' ) ); ?></span>
				<span class="hero__headline"><?php echo esc_html( jn_opt( 'hero_headline' ) ); ?></span>
			</h1>
			<p class="hero__text"><?php echo esc_html( jn_opt( 'hero_text' ) ); ?></p>
			<p class="hero__roles"><?php echo esc_html( jn_opt( 'positioning' ) ); ?></p>
			<div class="hero__actions">
				<a class="btn btn--primary btn--lg" href="<?php echo esc_url( jn_page_url( 'contact' ) ); ?>#form">Work With Me <?php echo jn_icon( 'arrow' ); // phpcs:ignore ?></a>
				<a class="btn btn--ghost btn--lg" href="<?php echo esc_url( get_post_type_archive_link( 'jn_project' ) ); ?>">Explore My Work</a>
			</div>
			<p class="hero__loc"><?php echo jn_icon( 'pin' ); // phpcs:ignore ?> <?php echo esc_html( jn_opt( 'location' ) ); ?></p>
		</div>
		<div class="hero__visual">
			<svg class="hero__lines" viewBox="0 0 520 620" preserveAspectRatio="none" aria-hidden="true" focusable="false">
				<g fill="none" stroke="currentColor" stroke-width="1">
					<ellipse cx="260" cy="310" rx="250" ry="290" opacity=".16"/>
					<path class="draw" d="M30 590 C 140 470, 200 380, 260 310 S 400 120, 490 30"/>
					<path class="draw draw--2" d="M30 30 C 140 150, 200 240, 260 310 S 400 500, 490 590"/>
				</g>
				<g class="nodes" fill="currentColor">
					<circle cx="30" cy="30" r="4"/><circle cx="490" cy="30" r="4"/><circle cx="30" cy="590" r="4"/><circle cx="490" cy="590" r="4"/>
				</g>
				<g class="node-labels" font-size="11" letter-spacing=".16em" fill="currentColor">
					<text x="44" y="34">IDEAS</text><text x="476" y="34" text-anchor="end">TECHNOLOGY</text><text x="44" y="594">PEOPLE</text><text x="476" y="594" text-anchor="end">RESOURCES</text>
				</g>
			</svg>
			<?php echo jn_portrait( 'portrait hero__portrait' ); // phpcs:ignore ?>
		</div>
	</div>
	<div class="wrap">
		<ul class="hero__strip" aria-label="Areas of work">
			<li>Development</li><li>Enterprise</li><li>Fundraising</li><li>Sustainability</li><li>AI &amp; Automation</li>
		</ul>
	</div>
</section>

<section class="section section--intro">
	<div class="wrap intro">
		<div class="intro__label"><p class="eyebrow">Who I am</p></div>
		<div class="intro__body reveal">
			<p class="statement"><?php echo esc_html( jn_opt( 'hero_statement' ) ); ?></p>
			<?php foreach ( preg_split( "/\n\s*\n/", (string) jn_opt( 'intro' ) ) as $para ) : ?>
				<p><?php echo esc_html( trim( $para ) ); ?></p>
			<?php endforeach; ?>
			<a class="link-arrow" href="<?php echo esc_url( jn_page_url( 'about' ) ); ?>">More about my journey and approach <?php echo jn_icon( 'arrow' ); // phpcs:ignore ?></a>
		</div>
	</div>
</section>

<section class="section section--pillars">
	<div class="wrap">
		<?php jn_section_head( 'What I do', 'Four connected areas of practice', 'Each one strengthens the others. Programmes need funding, enterprises need systems, and almost everything can be done better with the right technology.' ); ?>
		<div class="pillars">
			<?php
			$i = 0;
			foreach ( jn_lines( jn_opt( 'pillars' ) ) as $line ) :
				$parts = array_map( 'trim', explode( '|', $line, 2 ) );
				$url   = $pillar_links[ $i ] ?? jn_page_url( 'what-i-do' );
				?>
				<a class="pillar reveal" href="<?php echo esc_url( $url ); ?>">
					<span class="pillar__num"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
					<?php echo jn_icon( $pillar_icons[ $i ] ?? 'spark', 'icon pillar__icon' ); // phpcs:ignore ?>
					<h3 class="pillar__title"><?php echo esc_html( $parts[0] ); ?></h3>
					<p class="pillar__text"><?php echo esc_html( $parts[1] ?? '' ); ?></p>
					<span class="pillar__more">Learn more <?php echo jn_icon( 'arrow' ); // phpcs:ignore ?></span>
				</a>
				<?php
				$i++;
			endforeach;
			?>
		</div>
	</div>
</section>

<?php
$featured = new WP_Query( array(
	'post_type'      => 'jn_project',
	'posts_per_page' => 3,
	'meta_key'       => '_jn_featured',
	'meta_value'     => '1',
	'orderby'        => 'menu_order',
	'order'          => 'ASC',
	'tax_query'      => array( array( 'taxonomy' => 'jn_section', 'field' => 'slug', 'terms' => 'active-completed' ) ),
) );
if ( $featured->have_posts() ) :
	?>
<section class="section section--projects">
	<div class="wrap">
		<div class="section-head-row">
			<?php jn_section_head( 'Featured projects & initiatives', 'Work in the field', 'Projects I lead, manage or contribute to. Each one is labelled with its status and my role, so it is clear what is complete, what is under way and what is organisational work.' ); ?>
			<a class="link-arrow" href="<?php echo esc_url( get_post_type_archive_link( 'jn_project' ) ); ?>">All projects &amp; ventures <?php echo jn_icon( 'arrow' ); // phpcs:ignore ?></a>
		</div>
		<div class="cards cards--3">
			<?php
			while ( $featured->have_posts() ) :
				$featured->the_post();
				jn_part( 'card-project' );
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
<?php endif; ?>

<section class="section section--ai">
	<div class="wrap ai-feature">
		<div class="ai-feature__copy reveal">
			<p class="eyebrow eyebrow--gold">AI &amp; Automation</p>
			<h2 class="ai-feature__title">Work Smarter. Automate Better. Achieve More.</h2>
			<p>Small teams lose hours every week to work a machine could do: searching for opportunities, copying data, chasing follow-ups and assembling reports. I help organisations and businesses find those inefficiencies, automate the repetitive parts and adopt AI tools in a way that is practical, affordable and safe.</p>
			<p>The approach is simple. We look at how work actually flows, pick the processes where automation pays off quickly, and build from there with your team.</p>
			<div class="hero__actions">
				<a class="btn btn--gold btn--lg" href="<?php echo esc_url( jn_page_url( 'ai-automation' ) ); ?>">Explore AI &amp; Automation Solutions <?php echo jn_icon( 'arrow' ); // phpcs:ignore ?></a>
			</div>
		</div>
		<div class="ai-feature__examples reveal">
			<p class="ai-feature__label">What can be automated, for example</p>
			<ul class="auto-list">
				<?php
				$autos = get_posts( array( 'post_type' => 'jn_automation', 'posts_per_page' => 8, 'orderby' => 'menu_order', 'order' => 'ASC' ) );
				foreach ( $autos as $a ) :
					?>
					<li>
						<?php echo jn_icon( jn_meta( 'icon', $a->ID ) ? jn_meta( 'icon', $a->ID ) : 'spark' ); // phpcs:ignore ?>
						<div><strong><?php echo esc_html( $a->post_title ); ?></strong><span><?php echo esc_html( jn_meta( 'outcome', $a->ID ) ); ?></span></div>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</section>

<?php
$ideas = new WP_Query( array(
	'post_type'      => 'jn_project',
	'posts_per_page' => 3,
	'orderby'        => 'menu_order',
	'order'          => 'ASC',
	'tax_query'      => array( array( 'taxonomy' => 'jn_section', 'field' => 'slug', 'terms' => array( 'ideas', 'ventures' ) ) ),
) );
?>
<section class="section section--ideas">
	<div class="wrap">
		<div class="section-head-row">
			<?php jn_section_head( 'Ideas worth building', 'What I am thinking about next', 'Initiatives, ventures and concepts I am developing and would like to build with the right partners. These are proposals, clearly marked as such.' ); ?>
			<a class="link-arrow" href="<?php echo esc_url( get_post_type_archive_link( 'jn_project' ) ); ?>?section=ideas">Explore Ideas &amp; Ventures <?php echo jn_icon( 'arrow' ); // phpcs:ignore ?></a>
		</div>
		<?php if ( $ideas->have_posts() ) : ?>
			<div class="cards cards--3">
				<?php
				while ( $ideas->have_posts() ) :
					$ideas->the_post();
					jn_part( 'card-project' );
				endwhile;
				wp_reset_postdata();
				?>
				<a class="card card--invite reveal" href="<?php echo esc_url( jn_page_url( 'contact' ) ); ?>?type=venture#form">
					<?php echo jn_icon( 'idea' ); // phpcs:ignore ?>
					<h3 class="card__title">Have an idea that fits?</h3>
					<p class="card__text">If you are working on something in this space, or want to help build one of these initiatives, I would like to hear from you.</p>
					<span class="link-arrow">Collaborate on an initiative <?php echo jn_icon( 'arrow' ); // phpcs:ignore ?></span>
				</a>
			</div>
		<?php endif; ?>
	</div>
</section>

<section class="section section--learning">
	<div class="wrap">
		<div class="section-head-row">
			<?php jn_section_head( 'The Learning Centre', 'Knowledge, ideas and practical tools', 'For building better organisations, stronger enterprises and sustainable communities.' ); ?>
			<a class="link-arrow" href="<?php echo esc_url( jn_page_url( 'learning-centre' ) ); ?>">Explore the Learning Centre <?php echo jn_icon( 'arrow' ); // phpcs:ignore ?></a>
		</div>
		<?php
		$recent = new WP_Query( array( 'post_type' => array( 'post', 'jn_resource' ), 'posts_per_page' => 3, 'post_status' => 'publish' ) );
		if ( $recent->have_posts() ) :
			?>
			<div class="cards cards--3">
				<?php
				while ( $recent->have_posts() ) :
					$recent->the_post();
					jn_part( 'card-article' );
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		<?php else : ?>
			<?php jn_part( 'planned-topics', array( 'limit' => 6 ) ); ?>
		<?php endif; ?>
		<div class="newsletter-band reveal">
			<div>
				<h3 class="newsletter-band__title">Subscribe to the Learning Centre</h3>
			</div>
			<?php jn_part( 'newsletter' ); ?>
		</div>
	</div>
</section>

<?php
$stats      = jn_lines( jn_opt( 'stats', '' ) );
$milestones = get_posts( array( 'post_type' => 'jn_milestone', 'posts_per_page' => 6, 'orderby' => 'menu_order', 'order' => 'ASC' ) );
if ( $stats || $milestones ) :
	?>
<section class="section section--experience">
	<div class="wrap">
		<?php jn_section_head( 'Experience', 'Professional milestones', '' ); ?>
		<?php if ( $stats ) : ?>
			<dl class="stats">
				<?php foreach ( $stats as $s ) : ?>
					<?php $parts = array_map( 'trim', explode( '|', $s, 2 ) ); ?>
					<div class="stat reveal"><dt><?php echo esc_html( $parts[0] ); ?></dt><dd><?php echo esc_html( $parts[1] ?? '' ); ?></dd></div>
				<?php endforeach; ?>
			</dl>
		<?php endif; ?>
		<?php if ( $milestones ) : ?>
			<ol class="milestones">
				<?php foreach ( $milestones as $m ) : ?>
					<li class="reveal">
						<span class="milestone__kind"><?php echo esc_html( ucfirst( jn_meta( 'kind', $m->ID ) ) ); ?></span>
						<strong><?php echo esc_html( $m->post_title ); ?></strong>
						<span class="muted"><?php echo esc_html( trim( jn_meta( 'organisation', $m->ID ) . ( jn_meta( 'when', $m->ID ) ? ' · ' . jn_meta( 'when', $m->ID ) : '' ) ) ); ?></span>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php endif; ?>
		<a class="link-arrow" href="<?php echo esc_url( jn_page_url( 'about' ) ); ?>#timeline">Full timeline <?php echo jn_icon( 'arrow' ); // phpcs:ignore ?></a>
	</div>
</section>
<?php endif; ?>

<?php jn_part( 'cta-band' ); ?>

<?php
get_footer();
