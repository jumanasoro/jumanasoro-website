<?php
/** Titles of planned (draft) articles, shown without links. */
defined( 'ABSPATH' ) || exit;
$planned = get_posts( array(
	'post_type'      => 'post',
	'post_status'    => 'draft',
	'posts_per_page' => $args['limit'] ?? 12,
	'meta_key'       => '_jn_planned',
	'meta_value'     => '1',
	'orderby'        => 'menu_order',
	'order'          => 'ASC',
) );
if ( ! $planned ) {
	return;
}
?>
<div class="planned reveal">
	<p class="planned__label"><?php echo jn_icon( 'clock' ); // phpcs:ignore ?> In the pipeline: articles being written</p>
	<ul class="planned__list">
		<?php foreach ( $planned as $p ) : ?>
			<?php $c = get_the_category( $p->ID ); ?>
			<li><span class="planned__cat"><?php echo esc_html( $c ? $c[0]->name : '' ); ?></span><span class="planned__title"><?php echo esc_html( $p->post_title ); ?></span><span class="badge badge--soon">Coming soon</span></li>
		<?php endforeach; ?>
	</ul>
</div>
