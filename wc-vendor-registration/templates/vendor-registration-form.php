<form method="post" class="register">

	<?php do_action( 'ss_wc_vendor_registration_form_start' ); ?>

	<?php if ( $message ) echo wpautop( wptexturize( $message ) ); ?>

	<p class="form-row form-row-first">
		<label for="reg_vendor_first_name"><?php _e( 'First name', 'ss-wc-vendor-registration' ); ?> <span class="required">*</span></label>
		<input type="text" class="input-text" name="vendor_first_name" id="reg_vendor_first_name" value="<?php if ( ! empty( $_POST['vendor_first_name'] ) ) esc_attr_e( $_POST['vendor_first_name'] ); ?>" />
	</p>

	<p class="form-row form-row-last">
		<label for="reg_vendor_last_name"><?php _e( 'Last name', 'ss-wc-vendor-registration' ); ?> <span class="required">*</span></label>
		<input type="text" class="input-text" name="vendor_last_name" id="reg_vendor_last_name" value="<?php if ( ! empty( $_POST['vendor_last_name'] ) ) esc_attr_e( $_POST['vendor_last_name'] ); ?>" />
	</p>

	<p class="form-row form-row-first">
		<label for="username"><?php _e( 'Username', 'ss-wc-vendor-registration' ); ?> <span class="required">*</span></label>
		<input type="text" class="input-text" name="username" id="username" value="<?php if ( ! empty( $_POST['username'] ) ) esc_attr_e( $_POST['username'] ); ?>" />
	</p>
	<p class="form-row form-row-last">
		<label for="password"><?php _e( 'Password', 'ss-wc-vendor-registration' ); ?> <span class="required">*</span></label>
		<input class="input-text" type="password" name="password" id="password" />
	</p>

	<p class="form-row form-row-wide">
		<label for="email"><?php _e( 'Email Address', 'ss-wc-vendor-registration' ); ?> <span class="required">*</span></label>
			<input type="text" class="input-text" name="email" id="email" value="<?php if ( ! empty( $_POST['email'] ) ) esc_attr_e( $_POST['email'] ); ?>" />
	</p>

	<div class="clear"></div>

	<p class="form-row form-row-wide">
		<h3><?php _e( 'Your Store Details', 'ss-wc-vendor-registration' ); ?></h3>
	</p>

	<p class="form-row form-row-first">
		<label for="store_name"><?php _e( 'Store Name', 'ss-wc-vendor-registration' ); ?> <span class="required">*</span></label>
		<input type="text" class="input-text" name="store_name" id="store_name" value="<?php if ( ! empty( $_POST['store_name'] ) ) esc_attr_e( $_POST['store_name'] ); ?>" />
		<span><?php _e( 'What is the name of your store?', 'ss-wc-vendor-registration' ); ?></span>
	</p>
	<p class="form-row form-row-last">
		<label for="paypal_email"><?php _e( 'PayPal Email Address', 'ss-wc-vendor-registration' ); ?> <span class="required">*</span></label>
		<input type="text" class="input-text" name="paypal_email" id="paypal_email" value="<?php if ( ! empty( $_POST['paypal_email'] ) ) esc_attr_e( $_POST['paypal_email'] ); ?>" />
		<span><?php _e( 'The PayPal email address of the vendor where their profits will be delivered.', 'ss-wc-vendor-registration' ); ?></span>
	</p>

	<p class="form-row form-row-wide">
		<label for="reg_store_description"><?php _e( 'Store Description', 'ss-wc-vendor-registration' ); ?></label>
		<textarea name="store_description" id="reg_store_description" cols="30" rows="10"><?php echo isset( $_POST['store_description'] ) ? $_POST['store_description'] : '' ?></textarea>
	</p>

	<?php do_action( 'ss_wc_vendor_registration_form' ); ?>

	<p class="form-row">
		<?php wp_nonce_field( 'ss-wc-vendor-registration-form' ); ?>
		<input type="submit" class="button" name="register" value="<?php _e( 'Register', 'ss-wc-vendor-registration' ); ?>" />
		<input type="hidden" name="redirect" value="<?php echo esc_url( $redirect ) ?>" />
	</p>

	<p class="alread_registered">
		<?php _e( 'Already Registered?', 'ss-wc-vendor-registration' ); ?> <a href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ); ?>"><?php _e( 'Login', 'ss-wc-vendor-registration' ); ?></a>
	</p>

	<div class="clear"></div>

	<?php do_action( 'ss_wc_vendor_registration_form_end' ); ?>

</form>
