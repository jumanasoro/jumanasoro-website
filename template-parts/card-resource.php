<?php
defined( 'ABSPATH' ) || exit;
$rid    = get_the_ID();
$access = jn_meta( 'access', $rid ) ? jn_meta( 'access', $rid ) : 'free';
$url    = jn_resource_file_url( $rid );
$ext    = $url ? strtoupper( pathinfo( wp_parse_url( $url, PHP_URL_PATH ), PATHINFO_EXTENSION ) ) : '';
$cats   = get_the_category( $rid );
?>
<article class="resource reveal">
	<div class="resource__icon"><?php echo jn_icon( 'file' ); // phpcs:ignore ?><?php if ( $ext && strlen( $ext ) <= 4 ) : ?><span><?php echo esc_html( $ext ); ?></span><?php endif; ?></div>
	<div class="resource__body">
		<p class="card__meta">
			<?php if ( $cats ) : ?><span class="card__section"><?php echo esc_html( $cats[0]->name ); ?></span><?php endif; ?>
			<?php if ( jn_meta( 'format', $rid ) ) : ?><span class="badge badge--resource"><?php echo esc_html( jn_meta( 'format', $rid ) ); ?></span><?php endif; ?>
		</p>
		<h3 class="resource__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<p class="card__text"><?php echo esc_html( get_the_excerpt() ); ?></p>
	</div>
	<div class="resource__action">
		<?php if ( 'coming-soon' === $access || ! $url ) : ?>
			<span class="badge badge--soon">Coming soon</span>
		<?php elseif ( 'registration' === $access ) : ?>
			<a class="btn btn--ghost" href="<?php the_permalink(); ?>#form"><?php echo jn_icon( 'lock' ); // phpcs:ignore ?> Get it free</a>
		<?php else : ?>
			<a class="btn btn--primary" href="<?php echo esc_url( $url ); ?>" download><?php echo jn_icon( 'download' ); // phpcs:ignore ?> Download</a>
		<?php endif; ?>
	</div>
</article>
