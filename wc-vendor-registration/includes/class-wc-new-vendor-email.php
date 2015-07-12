<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * New Vendor WooCommerce Email
 *
 * An email is sent to the vendor when they create an account.
 *
 * @since   0.0.6
 * @class   WC_Email_New_Vendor_Account
 * @package WooCommerce Vendor Registration/Includes/Emails
 * @author  Sebs Studio
 * @extends WC_Email
 */
class WC_Email_New_Vendor_Account extends WC_Email {

	public $user_pass;
	public $user_login;
	public $user_email;

	/**
	 * Constructor
	 *
	 * @since  0.0.6
	 * @access public
	 * @return void
	 */
	public function __construct() {

		// set ID, this simply needs to be a unique name
		$this->id = 'wc_new_vendor';

		// this is the title in WooCommerce Email settings
		$this->title = __( 'New Vendor', 'ss-wc-vendor-registration' );

		// this is the description in WooCommerce email settings
		$this->description = __( 'Vendor "new vendor" emails are sent to the vendor when they sign up via the vendor registration form.', 'ss-wc-vendor-registration' );

		// these are the default heading and subject lines that can be overridden using the settings
		$this->heading = __( 'Your vendor account on {site_title} is ready', 'ss-wc-vendor-registration' );
		$this->subject = __( 'Welcome to {site_title} as a new vendor.', 'ss-wc-vendor-registration' );

		// these define the locations of the templates that this email should use, we'll just use the new order template since this email is similar
		$this->template_html  = 'emails/vendor-new-account.php';
		$this->template_plain = 'emails/plain/vendor-new-account.php';
		$this->template_base  = plugin_dir_path( ss_wc_vendor_registration_plugin_file() ) . 'templates/';

		// Triggered once the user has registered
		add_action( 'ss_wc_created_vendor', array( $this, 'trigger' ), 10, 3 );

		// Call parent constructor to load any other defaults not explicity defined here
		parent::__construct();

		// this sets the recipient to the settings defined below in init_form_fields()
		$this->recipient = $this->get_option( 'recipient' );
	}

	/**
	 * Trigger, determines if the email should actually be sent before sending it.
	 *
	 * @since  0.0.6
	 * @param  int $vendor_id
	 * @param  array $vendor_data
	 * @param  bool $password_generated
	 * @access public
	 * @return void
	 */
	public function trigger( $vendor_id, $vendor_data = array(), $password_generated = false ) {
		// bail if the email was not enabled
		if ( ! $this->is_enabled() )
			return;

		// bail if no vendor ID is present
		if ( ! $vendor_id )
			return;

		$this->object             = new WP_User( $vendor_id );
		$this->user_pass          = $vendor_data->user_pass;
		$this->user_login         = stripslashes( $this->object->user_login );
		$this->user_email         = stripslashes( $this->object->user_email );
		$this->recipient          = $this->user_email;
		$this->password_generated = $password_generated;

		// woohoo, send the email!
		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
	}

	/**
	 * get_content_html function.
	 *
	 * @since  0.0.6
	 * @return string
	 */
	public function get_content_html() {
		ob_start();

		wc_get_template(
			$this->template_html, 
			array(
				'email_heading'      => $this->get_heading(),
				'user_login'         => $this->user_login,
				'user_pass'          => $this->user_pass,
				'blogname'           => $this->get_blogname(),
				'password_generated' => $this->password_generated,
				'sent_to_admin'      => false,
				'plain_text'         => false
			),
			'',
			$this->template_base
		);

		return ob_get_clean();
	}

	/**
	 * get_content_plain function.
	 *
	 * @since  0.0.6
	 * @return string
	 */
	public function get_content_plain() {
		ob_start();

		wc_get_template(
			$this->template_plain, 
			array(
				'email_heading'      => $this->get_heading(),
				'user_login'         => $this->user_login,
				'user_pass'          => $this->user_pass,
				'blogname'           => $this->get_blogname(),
				'password_generated' => $this->password_generated,
				'sent_to_admin'      => false,
				'plain_text'         => true
			),
			'',
			$this->template_base
		);

		return ob_get_clean();
	}

	/**
	 * Initialize Settings Form Fields
	 *
	 * @since 0.0.6
	 */
	public function init_form_fields() {

		$this->form_fields = array(
			'enabled'    => array(
				'title'   => __( 'Enable/Disable', 'ss-wc-vendor-registration' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable this email notification', 'ss-wc-vendor-registration' ),
				'default' => 'yes'
			),
			'subject'    => array(
				'title'       => __( 'Email subject', 'ss-wc-vendor-registration' ),
				'type'        => 'text',
				'description' => sprintf( __( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'ss-wc-vendor-registration' ), $this->subject ),
				'placeholder' => '',
				'default'     => ''
			),
			'heading'    => array(
				'title'       => __( 'Email heading', 'ss-wc-vendor-registration' ),
				'type'        => 'text',
				'description' => sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.', 'ss-wc-vendor-registration' ), $this->heading ),
				'placeholder' => '',
				'default'     => ''
			),
			'email_type' => array(
				'title'       => __( 'Email type', 'ss-wc-vendor-registration' ),
				'type'        => 'select',
				'description' => __( 'Choose which format of email to send.', 'ss-wc-vendor-registration' ),
				'default'     => 'html',
				'class'       => 'email_type wc-enhanced-select',
				'options'     => $this->get_email_type_options()
			)
		);
	}

	/**
	 * Email type options
	 *
	 * @since  0.0.6
	 * @access public
	 * @return array
	 */
	public function get_email_type_options() {
		$types = array(
			'plain' => __( 'Plain text', 'ss-wc-vendor-registration' )
		);

		if ( class_exists( 'DOMDocument' ) ) {
			$types['html'] = __( 'HTML', 'ss-wc-vendor-registration' );
			$types['multipart'] = __( 'Multipart', 'ss-wc-vendor-registration' );
		}

		return $types;
	}

} // end WC_Email_New_Vendor_Account class
