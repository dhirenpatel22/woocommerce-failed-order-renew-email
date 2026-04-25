<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * A custom Failed Renewal Order WooCommerce Email class
 *
 * @since 0.1
 * @extends \WC_Email
 */
class WC_Failed_Renew_Order_Email extends WC_Email {


	/**
	 * Set email defaults
	 *
	 * @since 0.1
	 */
	public function __construct() {

		// Set ID, this simply needs to be a unique name.
		$this->id = 'wc_failed_renew_order';

		// This is the title in WooCommerce Email settings.
		$this->title = 'Failed to Processing of Renewal Order';

		// This is the description in WooCommerce email settings.
		$this->description = 'Order Notification emails are sent to the recepient(s) when a failed renewal order is renewed successfully.';

		// These are the default heading and subject lines that can be overridden using the settings.
		$this->heading = 'Payment for Failed Renewal Order is Successful';
		$this->subject = 'Payment received for Failed Renewal Order';

		// These define the locations of the templates that this email should use, we'll just use the new order template since this email is similar.
		$this->template_html  = 'emails/admin-failed-renew-order.php';
		//$this->template_plain = 'emails/plain/admin-failed-renew-order.php';

		// Trigger on new paid orders.
		//add_action( 'woocommerce_order_status_processing',  array( $this, 'trigger' ) );

		// Call parent constructor to load any other defaults not explicitly defined here.
		parent::__construct();

		// This sets the recipient to the settings defined below in init_form_fields().
		$this->recipient = $this->get_option( 'recipient' );

		// If none was entered, just use the WP admin email as a fallback.
		if ( ! $this->recipient ) {
			$this->recipient = get_option( 'admin_email' );
		}
	}


	/**
	 * Determine if the email should actually be sent and setup email merge variables
	 *
	 * @since 0.1
	 * @param int $order_id
	 */
	public function trigger( $order_id ) {
		
		// Bail if no order ID is present.
		if ( ! $order_id ) {
			return;
		}

		// Setup order object.
		$this->object = wc_get_order( $order_id );

		// Replace variables in the subject/headings.
		$this->find[] = '{order_date}';
		$this->replace[] = wp_date( 'Y-m-d', $this->object->get_date_created()->getTimestamp() );

		$this->find[] = '{order_number}';
		$this->replace[] = $this->object->get_order_number();

		if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
			return;
		}

		// Woohoo, send the email!
		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
	}


	/**
	 * Get content html function.
	 *
	 * @since 0.1
	 * @return string
	 */
	public function get_content_html() {
		ob_start();
		woocommerce_get_template( $this->template_html, array(
			'order'         => $this->object,
			'email_heading' => $this->get_heading()
		) );
		return ob_get_clean();
	}


	/**
	 * Get content plain function.
	 *
	 * @since 0.1
	 * @return string
	 */
	public function get_content_plain() {
		ob_start();
		woocommerce_get_template( $this->template_plain, array(
			'order'         => $this->object,
			'email_heading' => $this->get_heading()
		) );
		return ob_get_clean();
	}

	/**
	 * Initialize Settings Form Fields
	 *
	 * @since 2.0
	 */
	public function init_form_fields() {

		$this->form_fields = array(
			'enabled'    => array(
				'title'   => 'Enable/Disable',
				'type'    => 'checkbox',
				'label'   => 'Enable this email notification',
				'default' => 'yes'
			),
			'recipient'  => array(
				'title'       => 'Recipient(s)',
				'type'        => 'text',
				'description' => sprintf( 'Enter recipients (comma separated) for this email. Defaults to <code>%s</code>.', esc_attr( get_option( 'admin_email' ) ) ),
				'placeholder' => '',
				'default'     => ''
			),
			'subject'    => array(
				'title'       => 'Subject',
				'type'        => 'text',
				'description' => sprintf( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', $this->subject ),
				'placeholder' => '',
				'default'     => ''
			),
			'heading'    => array(
				'title'       => 'Email Heading',
				'type'        => 'text',
				'description' => sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.', 'woocommerce' ), $this->heading ),
				'placeholder' => '',
				'default'     => ''
			),
			'email_type' => array(
				'title'       => 'Email type',
				'type'        => 'select',
				'description' => 'Choose which format of email to send.',
				'default'     => 'html',
				'class'       => 'email_type',
				'options'     => array(
					'plain'      => __( 'Plain text', 'woocommerce' ),
					'html'       => __( 'HTML', 'woocommerce' ),
					'multipart'  => __( 'Multipart', 'woocommerce' ),
				)
			)
		);
	}


} // end \WC_Failed_Renew_Order_Email class
