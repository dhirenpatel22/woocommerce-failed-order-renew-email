<?php
/**
 * Plugin Name: WooCommerce Custom Failed to Renewal Order Email
 * Plugin URI: https://github.com/dhirenpatel22/woocommerce-failed-order-renew-email
 * Description: Plugin for adding a custom WooCommerce email that sends admins an email when an failed renewal order is renewed.
 * Author: Dhiren
 * Author URI: https://www.dhirenpatel.com
 * Version: 1.3
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 *  Add a custom email to the list of emails WooCommerce should load
 *
 * @since 0.1
 * @param array $email_classes available email classes
 * @return array filtered available email classes
 */
function add_failed_renew_order_woocommerce_email( $email_classes ) {

	// include our custom email class
	require_once 'includes/class-wc-failed-renew-order-email.php';

	// add the email class to the list of email classes that WooCommerce loads
	$email_classes['WC_Failed_Renew_Order_Email'] = new WC_Failed_Renew_Order_Email();

	return $email_classes;

}
add_filter( 'woocommerce_email_classes', 'add_failed_renew_order_woocommerce_email' );
