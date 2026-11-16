<?php
defined( 'ABSPATH' ) || exit;
get_header();
the_post();
$ai     = get_posts( array( 'post_type' => 'jn_service', 'posts_per_page' => 1, 'meta_key' => '_jn_anchor', 'meta_value' => 'ai' ) );
$groups = $ai ? jn_groups( jn_meta( 'offerings', $ai[0]->ID ) ) : array();
$letters = range( 'A', 'Z' );

jn_part( 'page-hero', array(
	'eyebrow' => 'AI, Automation &amp; Digital Transformation',
	'title'   => 'Work Smarter. Automate Better. Achieve More.',
	'lead'    => 'Practical AI and automation for NGOs, community organisations, businesses and institutions that want to do more with the people and resources they already have.',
	'dark'    => true,
	'actions' => '<a class="btn btn--gold btn--lg" href="#assessment">Book an Automation Assessment ' . jn_icon( 'arrow' ) . '</a><a class="btn btn--ghost-light btn--lg" href="#explore">What can we automate?</a>',
) );
?>
<section class="section section--tight">
	<div class="wrap wrap--narrow prose prose--lead"><?php the_content(); ?></div>
</section>

<?php if ( $groups ) : ?>
<section class="section section--ai-groups">
	<div class="wrap">
		<?php jn_section_head( 'Services', 'Six ways I help organisations use technology', '' ); ?>
		<div class="ai-groups">
			<?php foreach ( $groups as $i => $g ) : ?>
				<div class="ai-group reveal">
					<span class="ai-group__letter"><?php echo esc_html( $letters[ $i ] ); ?></span>
					<h3><?php echo esc_html( $g['title'] ); ?></h3>
					<ul>
						<?php foreach ( $g['items'] as $item ) : ?><li><?php echo esc_html( $item ); ?></li><?php endforeach; ?>
					</ul>
				</div>
			<?php endforeach; ?>
		</div>
		<?php if ( $ai && jn_meta( 'note', $ai[0]->ID ) ) : ?>
			<p class="delivery-note reveal"><?php echo jn_icon( 'link' ); // phpcs:ignore ?> <span><?php echo esc_html( jn_meta( 'note', $ai[0]->ID ) ); ?></span></p>
		<?php endif; ?>
	</div>
</section>
<?php endif; ?>

<section class="section section--explorer" id="explore">
	<div class="wrap">
		<?php jn_section_head( 'Interactive', 'What can we automate?', 'Choose a process to see how it typically works today and how automation could change it.' ); ?>
		<?php jn_part( 'explorer' ); ?>
	</div>
</section>

<section class="section">
	<div class="wrap">
		<?php jn_section_head( 'How it works', 'From first conversation to working system', '' ); ?>
		<ol class="steps">
			<li class="reveal"><span>1</span><h3>Assess</h3><p>We map how work actually flows today, where time goes and which tools you already pay for.</p></li>
			<li class="reveal"><span>2</span><h3>Prioritise</h3><p>We choose one to three processes where automation pays off quickly and risks are low.</p></li>
			<li class="reveal"><span>3</span><h3>Design &amp; build</h3><p>I design the solution and build or coordinate it, using affordable, well-supported tools.</p></li>
			<li class="reveal"><span>4</span><h3>Train &amp; hand over</h3><p>Your team learns to run and adjust it, with simple documentation in plain language.</p></li>
			<li class="reveal"><span>5</span><h3>Improve</h3><p>We review what is working and extend automation step by step.</p></li>
		</ol>
	</div>
</section>

<section class="section section--responsible">
	<div class="wrap responsible reveal">
		<div>
			<p class="eyebrow">Responsible adoption</p>
			<h2 class="section-title">Useful, safe and in your control</h2>
		</div>
		<ul class="tick-list tick-list--cols">
			<li><?php echo jn_icon( 'check' ); // phpcs:ignore ?> People stay in charge of decisions; AI drafts, people approve.</li>
			<li><?php echo jn_icon( 'check' ); // phpcs:ignore ?> Personal and beneficiary data is handled with care, in line with the Kenya Data Protection Act, 2019.</li>
			<li><?php echo jn_icon( 'check' ); // phpcs:ignore ?> Tools are chosen for cost and maintainability, not novelty.</li>
			<li><?php echo jn_icon( 'check' ); // phpcs:ignore ?> No lock-in: you own your data, accounts and workflows.</li>
		</ul>
	</div>
</section>

<section class="section section--form" id="assessment">
	<div class="wrap form-layout">
		<div class="form-layout__aside">
			<p class="eyebrow">Automation assessment</p>
			<h2 class="section-title">Book an Automation Assessment</h2>
			<p>Tell me about your organisation and where time is being lost. I will review your answers and get back to you to arrange a conversation about practical first steps.</p>
			<ul class="tick-list">
				<li><?php echo jn_icon( 'check' ); // phpcs:ignore ?> For NGOs, CBOs, SMEs, startups and institutions</li>
				<li><?php echo jn_icon( 'check' ); // phpcs:ignore ?> No technical knowledge needed</li>
				<li><?php echo jn_icon( 'check' ); // phpcs:ignore ?> You leave with a clear idea of what is worth automating first</li>
			</ul>
		</div>
		<?php jn_part( 'form-enquiry', array( 'variant' => 'assessment', 'button' => 'Request an assessment' ) ); ?>
	</div>
</section>

<?php
get_footer();
