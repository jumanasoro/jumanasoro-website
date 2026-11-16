<?php
/** Projects, Initiatives & Ventures. */
defined( 'ABSPATH' ) || exit;
get_header();

$sections = get_terms( array( 'taxonomy' => 'jn_section', 'hide_empty' => false, 'orderby' => 'term_id' ) );
$preset   = isset( $_GET['section'] ) ? sanitize_key( wp_unslash( $_GET['section'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
if ( is_tax( 'jn_section' ) ) {
	$preset = get_queried_object()->slug;
}

jn_part( 'page-hero', array(
	'eyebrow' => 'Portfolio',
	'title'   => 'Projects, Initiatives &amp; Ventures',
	'lead'    => 'Work I have led or contributed to, initiatives I am developing, and ventures in progress. Every item carries its status and my role, so it is always clear what is complete, what is active and what is still an idea.',
) );
?>
<section class="section section--tight">
	<div class="wrap">
		<div class="filters" data-filters>
			<div class="filters__tabs" role="group" aria-label="Filter by category">
				<button type="button" class="chip<?php echo $preset ? '' : ' is-active'; ?>" data-filter-section="" aria-pressed="<?php echo $preset ? 'false' : 'true'; ?>">All</button>
				<?php foreach ( $sections as $s ) : ?>
					<button type="button" class="chip<?php echo $preset === $s->slug ? ' is-active' : ''; ?>" data-filter-section="<?php echo esc_attr( $s->slug ); ?>" aria-pressed="<?php echo $preset === $s->slug ? 'true' : 'false'; ?>"><?php echo esc_html( $s->name ); ?></button>
				<?php endforeach; ?>
			</div>
			<div class="filters__controls">
				<label class="screen-reader-text" for="pf-status">Filter by status</label>
				<select id="pf-status" data-filter-status>
					<option value="">Any status</option>
					<?php foreach ( jn_statuses() as $k => $label ) : ?>
						<option value="<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
				<label class="screen-reader-text" for="pf-search">Search projects</label>
				<input id="pf-search" type="search" placeholder="Search projects" data-filter-search>
			</div>
		</div>
		<?php foreach ( $sections as $s ) : ?>
			<p class="section-note" data-section-note="<?php echo esc_attr( $s->slug ); ?>" <?php echo $preset === $s->slug ? '' : 'hidden'; ?>><?php echo esc_html( $s->description ); ?></p>
		<?php endforeach; ?>
	</div>
</section>

<section class="section section--portfolio">
	<div class="wrap">
		<?php if ( have_posts() ) : ?>
			<div class="cards cards--3" data-filter-grid>
				<?php
				while ( have_posts() ) :
					the_post();
					jn_part( 'card-project' );
				endwhile;
				?>
			</div>
			<p class="empty-state" data-filter-empty hidden>No items match these filters yet.</p>
		<?php else : ?>
			<p class="empty-state">Portfolio items will appear here soon.</p>
		<?php endif; ?>

		<dl class="status-legend">
			<div><dt><span class="badge badge--concept">Concept</span> <span class="badge badge--in-development">In Development</span> <span class="badge badge--seeking-partners">Seeking Partners</span> <span class="badge badge--seeking-funding">Seeking Funding</span></dt><dd>Proposals and initiatives that have not yet been implemented.</dd></div>
			<div><dt><span class="badge badge--pilot">Pilot</span> <span class="badge badge--active">Active</span></dt><dd>Work that is under way.</dd></div>
			<div><dt><span class="badge badge--completed">Completed</span></dt><dd>Finished work.</dd></div>
		</dl>
	</div>
</section>

<?php
jn_part( 'cta-band', array(
	'title'  => 'Want to build one of these together?',
	'text'   => 'I am looking for partners, funders and collaborators for several initiatives. If one of them connects with your work, let\'s explore it.',
	'button' => 'Explore a Partnership',
	'url'    => jn_page_url( 'contact' ) . '?type=venture#form',
) );
get_footer();
