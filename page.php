<?php
defined( 'ABSPATH' ) || exit;
get_header();
the_post();
jn_part( 'page-hero', array( 'title' => esc_html( get_the_title() ), 'lead' => has_excerpt() ? esc_html( get_the_excerpt() ) : '' ) );
?>
<section class="section">
	<div class="wrap wrap--narrow prose"><?php the_content(); ?></div>
</section>
<?php
get_footer();
