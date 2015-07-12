<?php
/**
 * Vendor new account email
 *
 * @author  Sebs Studio
 * @package WooCommerce Vendor Registration/Templates/Emails
 * @version 0.0.6
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<?php do_action( 'woocommerce_email_header', $email_heading ); ?>

<p><?php printf( __( "Thank you for registering as a vendor on %s. Your username is <strong>%s</strong>.", 'ss-wc-vendor-registration' ), esc_html( $blogname ), esc_html( $user_login ) ); ?></p>

<?php if ( get_option( 'woocommerce_registration_generate_password' ) == 'yes' && $password_generated ) : ?>

	<p><?php printf( __( "Your password has been automatically generated: <strong>%s</strong>", 'ss-wc-vendor-registration' ), esc_html( $user_pass ) ); ?></p>

<?php endif; ?>

<p><?php printf( __( 'You can access your account area to manage your products and view your sales. You can change your password here: %s.', 'ss-wc-vendor-registration' ), wc_get_page_permalink( 'myaccount' ) ); ?></p>

<?php do_action( 'woocommerce_email_footer' ); ?>
