<?php
/** Article template. */
defined( 'ABSPATH' ) || exit;
get_header();
the_post();
$cats = get_the_category();
?>
<article class="article">
	<header class="article__head">
		<div class="wrap wrap--narrow">
			<p class="eyebrow">
				<a href="<?php echo esc_url( jn_page_url( 'learning-centre' ) ); ?>">The Learning Centre</a>
				<?php if ( $cats ) : ?> · <a href="<?php echo esc_url( get_category_link( $cats[0] ) ); ?>"><?php echo esc_html( $cats[0]->name ); ?></a><?php endif; ?>
			</p>
			<h1 class="article__title"><?php the_title(); ?></h1>
			<?php if ( has_excerpt() ) : ?><p class="article__dek"><?php echo esc_html( get_the_excerpt() ); ?></p><?php endif; ?>
			<div class="article__meta">
				<span class="author-chip"><span class="brand__mark" aria-hidden="true">JN</span> <?php echo esc_html( jn_opt( 'name' ) ); ?></span>
				<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
				<span><?php echo jn_icon( 'clock' ); // phpcs:ignore ?> <?php echo (int) jn_reading_time(); ?> min read</span>
			</div>
		</div>
	</header>
	<?php if ( has_post_thumbnail() ) : ?>
		<figure class="wrap article__image"><?php the_post_thumbnail( 'jn-wide', array( 'fetchpriority' => 'high' ) ); ?><?php if ( get_the_post_thumbnail_caption() ) : ?><figcaption><?php the_post_thumbnail_caption(); ?></figcaption><?php endif; ?></figure>
	<?php endif; ?>
	<div class="wrap wrap--narrow prose article__body">
		<?php the_content(); ?>
	</div>
	<footer class="wrap wrap--narrow article__foot">
		<?php echo jn_share_links(); // phpcs:ignore ?>
		<div class="author-box">
			<?php echo jn_portrait( 'portrait author-box__img' ); // phpcs:ignore ?>
			<div>
				<p class="eyebrow">About the author</p>
				<p class="author-box__name"><?php echo esc_html( jn_opt( 'name' ) ); ?></p>
				<p><?php echo esc_html( jn_opt( 'positioning' ) ); ?>, based in <?php echo esc_html( jn_opt( 'location' ) ); ?>.</p>
				<a class="link-arrow" href="<?php echo esc_url( jn_page_url( 'about' ) ); ?>">More about me <?php echo jn_icon( 'arrow' ); // phpcs:ignore ?></a>
			</div>
		</div>
	</footer>
</article>

<?php
$related = get_posts( array(
	'post_type'      => array( 'post', 'jn_resource' ),
	'posts_per_page' => 3,
	'post__not_in'   => array( get_the_ID() ),
	'category__in'   => wp_list_pluck( (array) $cats, 'term_id' ),
) );
if ( $related ) :
	?>
<section class="section section--related">
	<div class="wrap">
		<?php jn_section_head( '', 'Related reading', '' ); ?>
		<div class="cards cards--3">
			<?php
			global $post;
			foreach ( $related as $post ) : // phpcs:ignore
				setup_postdata( $post );
				jn_part( 'card-article' );
			endforeach;
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
<?php endif; ?>

<section class="section section--tight">
	<div class="wrap">
		<div class="newsletter-band">
			<div><h2 class="newsletter-band__title">Get new articles by email</h2></div>
			<?php jn_part( 'newsletter' ); ?>
		</div>
	</div>
</section>
<?php
get_footer();
