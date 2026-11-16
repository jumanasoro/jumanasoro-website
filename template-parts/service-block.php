<?php
/** One service, with the five standard parts: problem, offer, value, audience, engagement. */
defined( 'ABSPATH' ) || exit;
$s       = $args['service'];
$id      = $s->ID;
$anchor  = jn_meta( 'anchor', $id ) ? jn_meta( 'anchor', $id ) : $s->post_name;
$groups  = jn_groups( jn_meta( 'offerings', $id ) );
$is_ai   = 'ai' === $anchor;
$compact = ! empty( $args['compact'] );
$cta_url = $is_ai ? jn_page_url( 'ai-automation' ) . '#assessment' : jn_page_url( 'contact' ) . '?type=' . rawurlencode( array(
	'programme'      => 'programme',
	'fundraising'    => 'fundraising',
	'enterprise'     => 'consultancy',
	'sustainability' => 'partnership',
	'training'       => 'training',
)[ $anchor ] ?? 'consultancy' ) . '#form';
?>
<article class="service reveal<?php echo $is_ai ? ' service--ai' : ''; ?>" id="<?php echo esc_attr( $anchor ); ?>">
	<header class="service__head">
		<span class="service__num"><?php echo esc_html( jn_meta( 'number', $id ) ); ?></span>
		<div>
			<?php echo jn_icon( jn_meta( 'icon', $id ) ? jn_meta( 'icon', $id ) : 'spark', 'icon service__icon' ); // phpcs:ignore ?>
			<h2 class="service__title"><?php echo esc_html( $s->post_title ); ?></h2>
			<?php if ( $s->post_excerpt ) : ?><p class="service__tagline"><?php echo esc_html( $s->post_excerpt ); ?></p><?php endif; ?>
		</div>
	</header>
	<div class="service__grid">
		<div class="service__col">
			<h3 class="service__label">The problem</h3>
			<p><?php echo esc_html( jn_meta( 'problem', $id ) ); ?></p>
			<h3 class="service__label">The value</h3>
			<p><?php echo esc_html( jn_meta( 'value', $id ) ); ?></p>
			<?php if ( jn_meta( 'audience', $id ) ) : ?>
				<h3 class="service__label">Who it is for</h3>
				<ul class="tick-list">
					<?php foreach ( jn_lines( jn_meta( 'audience', $id ) ) as $a ) : ?>
						<li><?php echo jn_icon( 'check' ); // phpcs:ignore ?> <?php echo esc_html( $a ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
		<div class="service__col">
			<h3 class="service__label">Services offered</h3>
			<?php if ( $is_ai && $compact ) : ?>
				<ul class="offer-list offer-list--groups">
					<?php foreach ( $groups as $g ) : ?>
						<li><strong><?php echo esc_html( $g['title'] ); ?></strong> <span class="muted"><?php echo esc_html( implode( ' · ', array_slice( $g['items'], 0, 3 ) ) ); ?>…</span></li>
					<?php endforeach; ?>
				</ul>
				<a class="link-arrow" href="<?php echo esc_url( jn_page_url( 'ai-automation' ) ); ?>">See all AI &amp; automation services <?php echo jn_icon( 'arrow' ); // phpcs:ignore ?></a>
			<?php else : ?>
				<?php foreach ( $groups as $g ) : ?>
					<?php if ( $g['title'] ) : ?><h4 class="offer-group"><?php echo esc_html( $g['title'] ); ?></h4><?php endif; ?>
					<ul class="offer-list">
						<?php foreach ( $g['items'] as $item ) : ?>
							<li><?php echo esc_html( $item ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endforeach; ?>
			<?php endif; ?>
			<div class="service__engage">
				<h3 class="service__label">How to start</h3>
				<p><?php echo esc_html( jn_meta( 'engage', $id ) ); ?></p>
				<?php if ( jn_meta( 'note', $id ) ) : ?><p class="small muted"><?php echo esc_html( jn_meta( 'note', $id ) ); ?></p><?php endif; ?>
				<a class="btn btn--primary" href="<?php echo esc_url( $cta_url ); ?>"><?php echo esc_html( jn_meta( 'cta', $id ) ? jn_meta( 'cta', $id ) : 'Discuss a project' ); ?> <?php echo jn_icon( 'arrow' ); // phpcs:ignore ?></a>
			</div>
		</div>
	</div>
</article>
