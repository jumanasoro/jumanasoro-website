<?php
defined( 'ABSPATH' ) || exit;
$pid  = get_the_ID();
$cats = get_the_category( $pid );
$type = get_post_type( $pid );
?>
<article class="card card--article reveal" data-cats="<?php echo esc_attr( implode( ' ', wp_list_pluck( (array) $cats, 'slug' ) ) ); ?>">
	<a class="card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true"><?php echo jn_card_media( $pid ); // phpcs:ignore ?></a>
	<div class="card__body">
		<div class="card__meta">
			<?php if ( 'jn_resource' === $type ) : ?>
				<span class="badge badge--resource"><?php echo esc_html( jn_meta( 'format', $pid ) ? jn_meta( 'format', $pid ) : 'Resource' ); ?></span>
			<?php elseif ( 'jn_project' === $type ) : ?>
				<?php echo jn_status_badge( $pid ); // phpcs:ignore ?>
			<?php elseif ( 'page' !== $type && $cats ) : ?>
				<span class="card__section"><?php echo esc_html( $cats[0]->name ); ?></span>
			<?php endif; ?>
		</div>
		<h3 class="card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<p class="card__text"><?php echo esc_html( get_the_excerpt() ); ?></p>
		<?php if ( 'post' === $type ) : ?>
			<p class="card__foot small muted"><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time> · <?php echo (int) jn_reading_time(); ?> min read</p>
		<?php endif; ?>
	</div>
</article>
