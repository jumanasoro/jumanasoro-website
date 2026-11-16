<?php
/**
 * Enquiry form. Args: type (preselected enquiry type), title, intro,
 * variant ('assessment' adds operational questions), initiative, button.
 */
defined( 'ABSPATH' ) || exit;
$type       = $args['type'] ?? 'consultancy';
$variant    = $args['variant'] ?? 'default';
$initiative = $args['initiative'] ?? '';
$button     = $args['button'] ?? 'Send message';
$uid        = 'f' . wp_rand( 100, 999 );
?>
<form class="form js-form" id="form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-kind="enquiry" novalidate>
	<?php if ( ! empty( $args['title'] ) ) : ?>
		<h3 class="form__title"><?php echo esc_html( $args['title'] ); ?></h3>
	<?php endif; ?>
	<?php if ( ! empty( $args['intro'] ) ) : ?>
		<p class="form__intro"><?php echo esc_html( $args['intro'] ); ?></p>
	<?php endif; ?>
	<?php jn_form_feedback( 'enquiry' ); ?>
	<?php jn_form_hidden( 'enquiry', $initiative ? array( 'initiative' => $initiative ) : array() ); ?>

	<div class="form__grid">
		<div class="form__row">
			<label for="<?php echo esc_attr( $uid ); ?>-name">Name <span aria-hidden="true">*</span></label>
			<input id="<?php echo esc_attr( $uid ); ?>-name" name="name" type="text" required minlength="2" autocomplete="name">
		</div>
		<div class="form__row">
			<label for="<?php echo esc_attr( $uid ); ?>-org">Organisation</label>
			<input id="<?php echo esc_attr( $uid ); ?>-org" name="organisation" type="text" autocomplete="organization">
		</div>
		<div class="form__row">
			<label for="<?php echo esc_attr( $uid ); ?>-email">Email <span aria-hidden="true">*</span></label>
			<input id="<?php echo esc_attr( $uid ); ?>-email" name="email" type="email" required autocomplete="email">
		</div>
		<div class="form__row">
			<label for="<?php echo esc_attr( $uid ); ?>-phone">Telephone</label>
			<input id="<?php echo esc_attr( $uid ); ?>-phone" name="phone" type="tel" autocomplete="tel" placeholder="+254…">
		</div>
		<?php if ( 'assessment' === $variant ) : ?>
			<input type="hidden" name="enquiry_type" value="assessment">
			<div class="form__row">
				<label for="<?php echo esc_attr( $uid ); ?>-otype">Type of organisation</label>
				<select id="<?php echo esc_attr( $uid ); ?>-otype" name="organisation_type">
					<option value="">Choose…</option>
					<option>NGO / CBO</option><option>Foundation or donor</option><option>SME or business</option><option>Startup or social enterprise</option><option>Research organisation</option><option>Public institution</option><option>Other</option>
				</select>
			</div>
			<div class="form__row">
				<label for="<?php echo esc_attr( $uid ); ?>-size">Team size</label>
				<select id="<?php echo esc_attr( $uid ); ?>-size" name="team_size">
					<option value="">Choose…</option><option>1–5</option><option>6–20</option><option>21–50</option><option>51+</option>
				</select>
			</div>
			<fieldset class="form__row form__row--full">
				<legend>Which processes take the most time today?</legend>
				<div class="chips">
					<?php
					$auto_items = get_posts( array( 'post_type' => 'jn_automation', 'posts_per_page' => 20, 'orderby' => 'menu_order', 'order' => 'ASC' ) );
					foreach ( $auto_items as $a ) :
						?>
						<label class="chip"><input type="checkbox" name="processes[]" value="<?php echo esc_attr( $a->post_title ); ?>"> <span><?php echo esc_html( $a->post_title ); ?></span></label>
					<?php endforeach; ?>
				</div>
			</fieldset>
			<div class="form__row form__row--full">
				<label for="<?php echo esc_attr( $uid ); ?>-tools">Tools you already use (optional)</label>
				<input id="<?php echo esc_attr( $uid ); ?>-tools" name="tools" type="text" placeholder="e.g. Google Workspace, Excel, WhatsApp, QuickBooks">
			</div>
		<?php else : ?>
			<div class="form__row form__row--full">
				<label for="<?php echo esc_attr( $uid ); ?>-type">Type of enquiry</label>
				<select id="<?php echo esc_attr( $uid ); ?>-type" name="enquiry_type">
					<?php foreach ( jn_enquiry_types() as $k => $label ) : ?>
						<option value="<?php echo esc_attr( $k ); ?>" <?php selected( $type, $k ); ?>><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		<?php endif; ?>
		<div class="form__row form__row--full">
			<label for="<?php echo esc_attr( $uid ); ?>-msg"><?php echo 'assessment' === $variant ? 'Describe your operational challenges' : 'Brief description of your challenge or opportunity'; ?> <span aria-hidden="true">*</span></label>
			<textarea id="<?php echo esc_attr( $uid ); ?>-msg" name="message" rows="5" required minlength="10" maxlength="6000"></textarea>
		</div>
		<fieldset class="form__row form__row--full">
			<legend>Preferred contact method</legend>
			<div class="radios">
				<?php foreach ( jn_contact_methods() as $k => $label ) : ?>
					<label><input type="radio" name="contact_method" value="<?php echo esc_attr( $k ); ?>" <?php checked( 'email', $k ); ?>> <?php echo esc_html( $label ); ?></label>
				<?php endforeach; ?>
			</div>
		</fieldset>
	</div>
	<div class="form__foot">
		<button class="btn btn--primary" type="submit"><?php echo esc_html( $button ); ?> <?php echo jn_icon( 'arrow' ); // phpcs:ignore ?></button>
		<p class="small muted">Your details are used only to reply to you. <a href="<?php echo esc_url( jn_page_url( 'privacy-policy' ) ); ?>">Privacy</a></p>
	</div>
	<div class="form__status" role="status" aria-live="polite"></div>
</form>
