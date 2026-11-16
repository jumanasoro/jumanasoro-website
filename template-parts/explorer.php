<?php
/** Interactive "What can we automate?" explorer. */
defined( 'ABSPATH' ) || exit;
$items = get_posts( array( 'post_type' => 'jn_automation', 'posts_per_page' => 30, 'orderby' => 'menu_order', 'order' => 'ASC' ) );
if ( ! $items ) {
	return;
}
?>
<div class="explorer" data-explorer>
	<div class="explorer__list" role="tablist" aria-label="Processes that can be automated">
		<?php foreach ( $items as $i => $a ) : ?>
			<button class="explorer__tab" role="tab" type="button" id="ex-tab-<?php echo (int) $a->ID; ?>" aria-controls="ex-panel-<?php echo (int) $a->ID; ?>" aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>" tabindex="<?php echo 0 === $i ? '0' : '-1'; ?>">
				<?php echo jn_icon( jn_meta( 'icon', $a->ID ) ? jn_meta( 'icon', $a->ID ) : 'spark' ); // phpcs:ignore ?>
				<span><?php echo esc_html( $a->post_title ); ?></span>
			</button>
		<?php endforeach; ?>
	</div>
	<div class="explorer__panels">
		<?php foreach ( $items as $i => $a ) : ?>
			<div class="explorer__panel" role="tabpanel" id="ex-panel-<?php echo (int) $a->ID; ?>" aria-labelledby="ex-tab-<?php echo (int) $a->ID; ?>" <?php echo 0 === $i ? '' : 'hidden'; ?>>
				<p class="eyebrow eyebrow--gold"><?php echo esc_html( jn_meta( 'area', $a->ID ) ); ?></p>
				<h3 class="explorer__title"><?php echo esc_html( $a->post_title ); ?></h3>
				<div class="explorer__compare">
					<div class="explorer__before">
						<p class="explorer__label">Today</p>
						<p><?php echo esc_html( jn_meta( 'today', $a->ID ) ); ?></p>
					</div>
					<div class="explorer__after">
						<p class="explorer__label">With automation</p>
						<ol class="explorer__steps">
							<?php foreach ( jn_lines( jn_meta( 'automated', $a->ID ) ) as $step ) : ?>
								<li><?php echo esc_html( $step ); ?></li>
							<?php endforeach; ?>
						</ol>
					</div>
				</div>
				<p class="explorer__outcome"><?php echo jn_icon( 'check' ); // phpcs:ignore ?> <span><?php echo esc_html( jn_meta( 'outcome', $a->ID ) ); ?></span></p>
				<?php if ( jn_meta( 'tools', $a->ID ) ) : ?>
					<p class="small explorer__tools">Example tools: <?php echo esc_html( jn_meta( 'tools', $a->ID ) ); ?></p>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
		<p class="small explorer__disclaimer">Illustrative examples. The right design depends on your processes, data and budget, which is what an assessment establishes.</p>
		<a class="btn btn--gold" href="<?php echo esc_url( jn_page_url( 'ai-automation' ) ); ?>#assessment">Book an Automation Assessment <?php echo jn_icon( 'arrow' ); // phpcs:ignore ?></a>
	</div>
</div>
