<?php
/**
 * Plugin Name: WooCommerce Vendor Registration
 * Plugin URI:  https://github.com/Sebs-Studio/WooCommerce-Vendor-Registration
 * Description: Allows users to register as a vendor and create a store on your e-commerce marketplace site and add their products. Requires WooCommerce Product Vendors!
 * Version:     0.0.1
 * Author:      Sebs Studio
 * Author URI:  http://www.sebs-studio.com
 * Text Domain: ss-wc-vendor-registration
 * Domain Path: languages
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

## What the plugin does!
## ---------------------
## Register User as new user role called Vendor.
## Validate form details
## Create vendor shop
## Provide a shortcode to insert registration page.
## ------------------------------------------------

/**
 * This runs once the plugin is activated.
 *
 * @since  0.0.2
 * @access public
 */
function ss_wc_vendor_registration_activate(){
	ss_wc_create_user_role_vendor(); // Register new user role.
} // END ss_wc_vendor_registration_activate()
register_activation_hook( __FILE__, 'ss_wc_vendor_registration_activate' );

/**
 * Create user role and user capabilities for vendor.
 *
 * @since  0.0.1
 * @access public
 * @global $wp_roles
 * @see    http://codex.wordpress.org/Roles_and_Capabilities
 */
function ss_wc_create_user_role_vendor() {
	global $wp_roles;

	if ( class_exists( 'WP_Roles' ) ) {
		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}
	}

	if ( is_object( $wp_roles ) ) {

		add_role( 'vendor', __( 'Vendor', 'ss-wc-vendor-registration' ), array(
			'level_9'                => false,
			'level_8'                => false,
			'level_7'                => false,
			'level_6'                => false,
			'level_5'                => false,
			'level_4'                => false,
			'level_3'                => true,
			'level_2'                => true,
			'level_1'                => true,
			'level_0'                => true,
			'read'                   => true,
			'read_private_pages'     => false,
			'read_private_posts'     => false,
			'edit_users'             => false,
			'edit_posts'             => false,
			'edit_pages'             => false,
			'edit_published_posts'   => false,
			'edit_published_pages'   => false,
			'edit_private_pages'     => false,
			'edit_private_posts'     => false,
			'edit_others_posts'      => true,
			'edit_others_pages'      => true,
			'publish_posts'          => false,
			'publish_pages'          => false,
			'delete_posts'           => false,
			'delete_pages'           => false,
			'delete_private_pages'   => false,
			'delete_private_posts'   => false,
			'delete_published_pages' => false,
			'delete_published_posts' => false,
			'delete_others_posts'    => true,
			'delete_others_pages'    => true,
			'manage_categories'      => false,
			'manage_links'           => false,
			'moderate_comments'      => false,
			'unfiltered_html'        => false,
			'upload_files'           => true,
			'export'                 => false,
			'import'                 => false,
			'list_users'             => false
		) );

		$capabilities = ss_wc_vendor_register_get_core_capabilities();

		foreach( $capabilities as $cap_group ) {
			foreach( $cap_group as $cap ) {
				$wp_roles->add_cap( 'vendor', $cap );
			}
		}
	}
} // END ss_wc_create_user_role_vendor()

/**
 * Get capabilities for WooCommerce Vendor Registration.
 *
 * These are assigned to admin and any other
 * user role capabilities during installation
 * or resetting the plugin.
 *
 * @since  0.0.1
 * @access public
 * @filter ss_wc_vendor_register_capability_post_types
 * @return array
 */
function ss_wc_vendor_register_get_core_capabilities() {
	$capabilities = array();

	$capabilities['core'] = array(
		'manage_woocommerce',
		'view_woocommerce_reports'
	);

	$capability_types = apply_filters( 'ss_wc_vendor_register_capability_post_types', array( 'media', 'product', 'shop_order', 'shop_coupon' ) );

	foreach( $capability_types as $capability_type ) {

		$capabilities[ $capability_type ] = array(
			// Post type
			"edit_{$capability_type}",
			"read_{$capability_type}",
			"delete_{$capability_type}",
			"edit_{$capability_type}s",
			"edit_others_{$capability_type}s",
			//"publish_{$capability_type}s",
			"read_private_{$capability_type}s",
			"delete_{$capability_type}s",
			"delete_private_{$capability_type}s",
			"delete_published_{$capability_type}s",
			"delete_others_{$capability_type}s",
			"edit_private_{$capability_type}s",
			"edit_published_{$capability_type}s",

			// Terms
			"manage_{$capability_type}_terms",
			"edit_{$capability_type}_terms",
			"delete_{$capability_type}_terms",
			"assign_{$capability_type}_terms",
			"upload_files",
			"manage_bookings",
		);
	}

	return $capabilities;
} // END ss_wc_vendor_register_get_core_capabilities()

/**
 * Used when uninstalling the plugin.
 * Only removes the user role if asked to remove it.
 *
 * @since  0.0.1
 * @access public
 */
function ss_wc_vendor_remove_roles() {
	global $wp_roles;

	if ( ! class_exists( 'WP_Roles' ) ) {
		return;
	}

	if ( ! isset( $wp_roles ) ) {
		$wp_roles = new WP_Roles();
	}

	$capabilities = ss_wc_vendor_register_get_core_capabilities();

	foreach ( $capabilities as $cap_group ) {
		foreach ( $cap_group as $cap ) {
			$wp_roles->remove_cap( 'vendor', $cap );
		}
	}

	remove_role( 'vendor' );
} // END ss_wc_vendor_remove_roles()

/**
 * Create a new vendor
 *
 * @since  0.0.1
 * @param  string $email
 * @param  string $username
 * @param  string $password
 * @return int|WP_Error on failure, Int (user ID) on success
 */
function ss_wc_create_new_vendor( $email, $username = '', $password = '' ) {
	// Check the e-mail address
	if ( empty( $email ) || ! is_email( $email ) ) {
		return new WP_Error( 'registration-error', __( 'Please provide a valid email address.', 'ss-wc-vendor-registration' ) );
	}

	if ( email_exists( $email ) ) {
		return new WP_Error( 'registration-error', __( 'An account is already registered with your email address. Please login.', 'ss-wc-vendor-registration' ) );
	}

	// Handle username creation
	if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) || ! empty( $username ) ) {

		$username = sanitize_user( $username );

		if ( empty( $username ) || ! validate_username( $username ) ) {
			return new WP_Error( 'registration-error', __( 'Please enter a valid account username.', 'ss-wc-vendor-registration' ) );
		}

		if ( username_exists( $username ) )
			return new WP_Error( 'registration-error', __( 'An account is already registered with that username. Please choose another.', 'ss-wc-vendor-registration' ) );
	} else {

		$username = sanitize_user( current( explode( '@', $email ) ), true );

		// Ensure username is unique
		$append     = 1;
		$o_username = $username;

		while ( username_exists( $username ) ) {
			$username = $o_username . $append;
			$append ++;
		}
	}

	// Handle password creation
	if ( 'yes' === get_option( 'woocommerce_registration_generate_password' ) && empty( $password ) ) {
		$password = wp_generate_password();
		$password_generated = true;

	} elseif ( empty( $password ) ) {
		return new WP_Error( 'registration-error', __( 'Please enter an account password.', 'ss-wc-vendor-registration' ) );

	} else {
		$password_generated = false;
	}

	// WP Validation
	$validation_errors = new WP_Error();

	do_action( 'ss_wc_register_vendor', $username, $email, $validation_errors );

	$validation_errors = apply_filters( 'woocommerce_registration_errors', $validation_errors, $username, $email );

	if ( $validation_errors->get_error_code() )
		return $validation_errors;

	$new_vendor_data = apply_filters( 'ss_wc_new_vendor_data', array(
		'user_login' => $username,
		'user_pass'  => $password,
		'user_email' => $email,
		'role'       => 'vendor'
	) );

	$vendor_id = wp_insert_user( $new_vendor_data );

	if ( is_wp_error( $vendor_id ) ) {
		return new WP_Error( 'registration-error', '<strong>' . __( 'ERROR', 'ss-wc-vendor-registration' ) . '</strong>: ' . __( 'Couldn&#8217;t register you&hellip; please contact us if you continue to have problems.', 'ss-wc-vendor-registration' ) );
	}

	do_action( 'ss_wc_created_vendor', $vendor_id, $new_vendor_data, $password_generated );

	return $vendor_id;
} // END ss_wc_create_new_vendor()

function ss_wc_validate_register_fields() {
	if ( isset( $_POST['vendor_first_name'] ) && empty( $_POST['vendor_first_name'] ) ) {
		$validation_errors->add( 'vendor_first_name_error', __( '<strong>Error</strong>: First name is required!', 'ss-wc-vendor-registration' ) );
	}

	if ( isset( $_POST['vendor_last_name'] ) && empty( $_POST['vendor_last_name'] ) ) {
		$validation_errors->add( 'vendor_last_name_error', __( '<strong>Error</strong>: Last name is required!.', 'ss-wc-vendor-registration' ) );
	}
} // END ss_wc_validate_register_fields()
add_action( 'ss_wc_register_vendor', 'ss_wc_validate_register_fields', 10, 3 );

// Saves the vendors details.
function ss_wc_save_vendor_details( $vendor_id ) {
	if ( isset( $_POST['vendor_first_name'] ) ) {
		// WordPress default first name field.
		update_user_meta( $vendor_id, 'first_name', sanitize_text_field( $_POST['vendor_first_name'] ) );

		// WooCommerce Vendor first name.
		update_user_meta( $vendor_id, 'vendor_first_name', sanitize_text_field( $_POST['vendor_first_name'] ) );
	}

	if ( isset( $_POST['vendor_last_name'] ) ) {
		// WordPress default last name field.
		update_user_meta( $vendor_id, 'last_name', sanitize_text_field( $_POST['vendor_last_name'] ) );

		// WooCommerce Vendor last name.
		update_user_meta( $vendor_id, 'vendor_last_name', sanitize_text_field( $_POST['vendor_last_name'] ) );
	}
} // END ss_wc_save_vendor_details()
add_action( 'ss_wc_created_vendor', 'ss_wc_save_vendor_details' );

// Create the vendors store.
function ss_wc_create_vendor_store( $vendor_id ) {
	$store_details = array(
		'name'              => trim( $_POST['store_name'] ),
		'description'       => trim( $_POST['store_description'] ),
		'vendor_admins'     => $vendor_id,
		'vendor_commission' => '', // Left blank, up to the admin to decide commission rate.
		'paypal_email'      => $_POST['paypal_email']
	);

	// Create vendor store.
	$new_vendor_id = wp_insert_term( $store_details['name'], 'shop_vendor', array(
		'description' => $shop_details['description'],
		'slug'        => trim( strtolower( str_replace( ' ', '-', $shop_details['name'] ) ) ),
		'parent'      => ''
	) );

	if( ! is_wp_error( $new_vendor_id ) ) {
		// Get term id, set default to 0 if not set.
		$new_vendor_id = isset( $new_vendor_id['term_id'] ) ? $new_vendor_id['term_id'] : 0;

		do_action( 'ss_wc_save_vendor_details', $vendor_id, $new_vendor_id, $store_details );
	}

} // END ss_wc_create_vendor_store()
add_action( 'ss_wc_created_vendor', 'ss_wc_create_vendor_store' );

// The Token
function ss_wc_vendor_reg_get_token() {
	return 'shop_vendor';
} // END ss_wc_vendor_reg_get_token()

// Save the store details once the store has been created.
function ss_wc_save_store_details( $vendor_id, $new_vendor_id, $store_details ) {
	if ( ! $vendor_id ) {
		return;
	}

	// PayPal account email address
	$paypal_address = $store_details['paypal_email'];
	$vendor_data['paypal_email'] = $paypal_address;

	// Commission
	$commission = $store_details['commission'];
	$vendor_date['vendor_commission'] = $commission;

	// Apply the ID of the Vendor
	$vendor = $store_details['vedor_admins'];
	$vendor_date['vendor_admins'] = $vendor;

	update_option( ss_wc_vendor_reg_get_token() . '_' . $new_vendor_id, $vendor_data );

	// Vendor description
	$args = array(
		'description' => $store_details['description']
	);

	wp_update_term( $new_vendor_id, ss_wc_vendor_reg_get_token(), $args );
} // END ss_wc_save_store_details()
add_action( 'ss_wc_save_vendor_details', 'ss_wc_save_store_details', 3 );

/**
 * Insert the vendor regsitration form where you
 * want using this shortcode.
 *
 * @how Insert [vendor_registration] in your page.
 * @version 0.0.1
 */
function ss_wc_register_vendor_form_shortcode() {
	// If the user is already logged in, return nothing.
	if ( is_user_logged_in() ) { return; }

	if ( ! is_user_logged_in() ) {
		include_once( 'templates/vendor-registration-form.php' );
	}

} // END ss_wc_register_vendor_form_shortcode()
add_shortcode( 'vendor_registration', 'ss_wc_register_vendor_form_shortcode' );

/**
 * Process vendor registration form.
 *
 * @since  0.0.2
 * @access public
 * @filter ss_wc_process_vendor_registration_errors
 * @filter ss_wc_vendor_registration_redirect
 */
function ss_wc_vendor_process_registration_form() {
			if ( ! empty( $_POST['register'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'ss-wc-vendor-registration-form' ) ) {

			if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) {
				$_username = $_POST['username'];
			} else {
				$_username = '';
			}

			if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) {
				$_password = $_POST['password'];
			} else {
				$_password = '';
			}

			try {

				$validation_error = new WP_Error();
				$validation_error = apply_filters( 'ss_wc_process_vendor_registration_errors', $validation_error, $_username, $_password, $_POST['email'] );

				if ( $validation_error->get_error_code() ) {
					throw new Exception( '<strong>' . __( 'Error', 'ss-wc-vendor-registration' ) . ':</strong> ' . $validation_error->get_error_message() );
				}

			} catch ( Exception $e ) {

				wc_add_notice( $e->getMessage(), 'error' );
				return;

			}

			$username   = ! empty( $_username ) ? wc_clean( $_username ) : '';
			$email      = ! empty( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
			$password   = $_password;

			// Anti-spam trap
			/*if ( ! empty( $_POST['email_2'] ) ) {
				wc_add_notice( '<strong>' . __( 'ERROR', 'ss-wc-vendor-registration' ) . '</strong>: ' . __( 'Anti-spam field was filled in.', 'ss-wc-vendor-registration' ), 'error' );
				return;
			}*/

			$new_customer = ss_wc_create_new_vendor( $email, $username, $password );

			if ( is_wp_error( $new_customer ) ) {
				wc_add_notice( $new_customer->get_error_message(), 'error' );
				return;
			}

			wc_set_customer_auth_cookie( $new_customer );

			// Redirect
			if ( wp_get_referer() ) {
				$redirect = esc_url( wp_get_referer() );
			} else {
				$redirect = esc_url( get_permalink( wc_get_page_id( 'myaccount' ) ) );
			}

			wp_redirect( apply_filters( 'ss_wc_vendor_registration_redirect', $redirect ) );
			exit;
		}
	}
} // END ss_wc_vendor_process_registration_form()
add_action( 'init', 'ss_wc_vendor_process_registration_form' );
