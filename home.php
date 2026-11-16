<?php
/** The Learning Centre (posts page). */
defined( 'ABSPATH' ) || exit;
get_header();

jn_part( 'page-hero', array(
	'eyebrow' => 'The Learning Centre',
	'title'   => 'Knowledge, ideas and practical tools',
	'lead'    => 'For building better organisations, stronger enterprises and sustainable communities: insights, practical guides, templates, frameworks and curated resources.',
) );
$paged     = max( 1, (int) get_query_var( 'paged' ) );
$featured  = 1 === $paged ? get_posts( array( 'post_type' => 'post', 'posts_per_page' => 1, 'meta_key' => '_jn_featured', 'meta_value' => '1' ) ) : array();
$recommend = get_posts( array( 'post_type' => 'post', 'posts_per_page' => 4, 'meta_key' => '_jn_recommended', 'meta_value' => '1' ) );
?>
<section class="section section--tight">
	<div class="wrap">
		<?php jn_part( 'lc-nav' ); ?>
	</div>
</section>

<?php if ( $featured ) : ?>
	<?php $f = $featured[0]; ?>
	<section class="section section--tight">
		<div class="wrap">
			<article class="feature-article reveal">
				<a class="feature-article__media" href="<?php echo esc_url( get_permalink( $f ) ); ?>" tabindex="-1" aria-hidden="true"><?php echo jn_card_media( $f->ID, 'jn-wide' ); // phpcs:ignore ?></a>
				<div class="feature-article__body">
					<p class="eyebrow">Featured</p>
					<h2 class="feature-article__title"><a href="<?php echo esc_url( get_permalink( $f ) ); ?>"><?php echo esc_html( get_the_title( $f ) ); ?></a></h2>
					<p><?php echo esc_html( get_the_excerpt( $f ) ); ?></p>
					<p class="small muted"><?php echo esc_html( get_the_date( '', $f ) ); ?> · <?php echo (int) jn_reading_time( $f->ID ); ?> min read</p>
				</div>
			</article>
		</div>
	</section>
<?php endif; ?>

<section class="section">
	<div class="wrap">
		<?php if ( have_posts() ) : ?>
			<?php jn_section_head( '', 'Recently published', '', 'h2' ); ?>
			<div class="cards cards--3">
				<?php
				while ( have_posts() ) :
					the_post();
					jn_part( 'card-article' );
				endwhile;
				?>
			</div>
			<?php the_posts_pagination( array( 'mid_size' => 1 ) ); ?>
		<?php else : ?>
			<div class="empty-state reveal">
				<?php echo jn_icon( 'idea' ); // phpcs:ignore ?>
				<h2>The first articles are on their way</h2>
				<p>The Learning Centre is new. Subscribe below to hear when the first guides and articles are published.</p>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php if ( $recommend ) : ?>
<section class="section section--tight">
	<div class="wrap">
		<?php jn_section_head( '', 'Recommended reading', '' ); ?>
		<ul class="reading-list">
			<?php foreach ( $recommend as $r ) : ?>
				<li><a href="<?php echo esc_url( get_permalink( $r ) ); ?>"><?php echo esc_html( get_the_title( $r ) ); ?></a> <span class="muted small"><?php echo (int) jn_reading_time( $r->ID ); ?> min</span></li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
<?php endif; ?>

<section class="section section--tight">
	<div class="wrap"><?php jn_part( 'planned-topics' ); ?></div>
</section>

<section class="section section--topics">
	<div class="wrap">
		<?php jn_section_head( 'Topics', 'Seven areas of knowledge', '' ); ?>
		<div class="topics">
			<?php foreach ( get_categories( array( 'hide_empty' => false, 'exclude' => array( (int) get_option( 'default_category' ) ) ) ) as $i => $c ) : ?>
				<a class="topic reveal" href="<?php echo esc_url( get_category_link( $c ) ); ?>">
					<span class="topic__num"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
					<h3><?php echo esc_html( $c->name ); ?></h3>
					<p><?php echo esc_html( $c->description ); ?></p>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="section section--tight">
	<div class="wrap">
		<div class="newsletter-band reveal">
			<div><h2 class="newsletter-band__title">Subscribe to the Learning Centre</h2></div>
			<?php jn_part( 'newsletter' ); ?>
		</div>
	</div>
</section>
<?php
get_footer();
