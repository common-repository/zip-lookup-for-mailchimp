<?php
/**
 * 		Plugin Name:       Zip Lookup for MailChimp
 * 		Plugin URI:        http://www.yikesinc.com
 * 		Description:       This extension hides all address fields other than the zip field, and populates those fields based on the user supplied zipcode, utilizing the HERE geocode API.
 * 		Version:           1.1.3
 * 		Author:            YIKES, Inc.
 * 		Author URI:        http://www.yikesinc.com
 * 		License:           GPL-3.0+
 *		License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * 		Text Domain:       zip-lookup-for-mailchimp
 * 		Domain Path:       /languages
 *
 * 		Zip Lookup for MailChimp is free software: you can redistribute it and/or modify
 * 		it under the terms of the GNU General Public License as published by
 * 		the Free Software Foundation, either version 2 of the License, or
 * 		any later version.
 *
 * 		Zip Lookup for MailChimp is distributed in the hope that it will be useful,
 * 		but WITHOUT ANY WARRANTY; without even the implied warranty of
 * 		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * 		GNU General Public License for more details.
 *
 * 		You should have received a copy of the GNU General Public License
 *		along with Easy Forms for MailChimp. If not, see <http://www.gnu.org/licenses/>.
 *
 *		We at YIKES, Inc. embrace the open source philosophy on a daily basis. We donate company time back to the WordPress project,
 *		and constantly strive to improve the WordPress project and community as a whole. We eat, sleep and breath WordPress.
 *
 *		"'Free software' is a matter of liberty, not price. To understand the concept, you should think of 'free' as in 'free speech,' not as in 'free beer'."
 *		- Richard Stallman
 *
**/


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/* Check if base plugin is active */

// must include plugin.php to use is_plugin_active()
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( ! is_plugin_active( 'yikes-inc-easy-mailchimp-extender/yikes-inc-easy-mailchimp-extender.php' ) ) {
	deactivate_plugins( '/yikes-inc-easy-mailchimp-zip-lookup-extension/yikes-inc-easy-mailchimp-zip-lookup-extension.php' );
	add_action( 'admin_notices' , 'yikes_inc_mailchimp_zip_lookup_display_activation_error' );
}

function yikes_inc_mailchimp_zip_lookup_display_activation_error() {
	?>
		<!-- hide the 'Plugin Activated' default message -->
		<style>
		#message.updated {
			display: none;
		}
		</style>
		<!-- display our error message -->
		<div class="error">
			<p><?php _e( 'Zip Lookup for MailChimp could not be activated because the base plugin is not installed and active.', 'zip-lookup-for-mailchimp' ); ?></p>
			<p><?php printf( __( 'Please install and activate %s before activating this extension.', 'zip-lookup-for-mailchimp' ) , '<a href="' . esc_url_raw( admin_url( 'plugin-install.php?tab=search&type=term&s=Yikes+Inc.+Easy+MailChimp+Forms' ) ) . '" title="Easy MailChimp Forms">Easy MailChimp Forms</a>' ); ?></p>
		</div>
	<?php
}
/* End plugin base active check */

/*
*	Enqueue custom js file wherever our shortcode is used
*/
function enqueue_here_geocode_api_with_yikes_mailchimp() {
	// enqueue our geocode js file
	wp_enqueue_script( 'mailchimp-geocode-example', plugin_dir_url(__FILE__) . '/js/geocode-mailchimp.min.js', array( 'jquery' ), 'all' );
	// localize the script to pass in some PHP
	wp_localize_script( 'mailchimp-geocode-example', 'admin_data', array(
		'ajax_url' => esc_url( admin_url( 'admin-ajax.php' ) ),
		'preloader' => esc_url( admin_url( 'images/wpspin_light.gif' ) ),
		'red_x' => esc_url( trailingslashit( plugin_dir_url(__FILE__) ) . 'img/red-x.png' ),
		'green_checkmark' => esc_url( trailingslashit( plugin_dir_url(__FILE__) ) . 'img/green-check.png' ),
	) );
}
add_action( 'yikes-mailchimp-shortcode-enqueue-scripts-styles' , 'enqueue_here_geocode_api_with_yikes_mailchimp' );

/*
*	Disable the submit button on load
* 	for our gecoding form example (only if an address field is setup)
*/
function yikes_zip_disable_form_submit_button( $submit_button, $form_id ) {
	global $wpdb;

	$field_results = array();
	$form_results = get_option( 'yikes_easy_mailchimp_extender_forms' );
	if ( isset( $form_results[$form_id]['fields'] ) ) {
		$field_results = $form_results[$form_id]['fields'];
	}

	if ( ! empty( $field_results ) ) {
		foreach( $field_results as $field ) {
			if( $field['type'] == 'address' ) {
				$button = '<input type="submit" value="Submit" class="yikes-easy-mc-submit-button yikes-easy-mc-submit-button-' . intval( $form_id ) . '" disabled>';
			} else {
				$button = $submit_button;
			}
		}
		return $button;		
	} else {
		return null;
	}
}
add_action( 'yikes-mailchimp-form-submit-button' , 'yikes_zip_disable_form_submit_button', 10, 2 );

/*
*	Hide all address fields other than Zip
*/
function yikes_zip_hide_address_input_fields() {
	?>
		<style>
			label[data-attr-name="country-field"],
			label[data-attr-name="state-dropdown"],
			label[data-attr-name="city-field"],
			label[data-attr-name="addr1-field"],
			label[data-attr-name="addr2-field"] {
				display: none !important;
			}
		</style>
	<?php
}
add_action( 'wp_print_scripts' , 'yikes_zip_hide_address_input_fields' );

/*
*	Geocode AJAX handler
*	(API request etc.)
*/
function yikes_ajax_geocode_mc_zip_lookup() {

	$ApplicationID   = '9Zr024vkNLs7zZ8RYqOq';
	$ApplicationCode = '0aC9THFESAmb4kMb6bUN5w';

	// Catch the user entered zip.
	$user_zip_code = filter_var( $_POST['zip_code'], FILTER_SANITIZE_NUMBER_INT );
	$country       = apply_filters( 'yikes-mailchimp-zip-lookup-country', 'USA' );
	$search_text   = urlencode( $user_zip_code . ' ' . $country );

	// Setup our address.
    $geocode_api_url = esc_url_raw( "https://geocoder.api.here.com/6.2/geocode.json?app_id={$ApplicationID}&app_code={$ApplicationCode}&searchtext={$search_text}" );

	// Submit our request.
	$geocode_response = wp_remote_get( $geocode_api_url );

	// Confirm there is no error.
	if ( is_wp_error( $geocode_response ) ) {
		return $geocode_response->getMessage();
	}

	// Grab the response body.
	$geocode_response_body = json_decode( wp_remote_retrieve_body( $geocode_response ), true );

	// Ensure we have a response.
	if ( $geocode_response_body ) {

		// Drill down the response chain...
		if ( isset( $geocode_response_body['Response'] ) && isset( $geocode_response_body['Response']['View'] ) && isset( $geocode_response_body['Response']['View'][0] ) && isset( $geocode_response_body['Response']['View'][0]['Result'] ) && isset( $geocode_response_body['Response']['View'][0]['Result'][0] ) && isset( $geocode_response_body['Response']['View'][0]['Result'][0]['Location'] ) && isset( $geocode_response_body['Response']['View'][0]['Result'][0]['Location']['Address'] ) ) {

			$address = $geocode_response_body['Response']['View'][0]['Result'][0]['Location']['Address'];

			$city    = isset( $address['City'] ) ? $address['City'] : '';
			$state   = isset( $address['State'] ) ? $address['State'] : '';
			$country = isset( $address['Country'] ) ? $address['Country'] : '';

			// Submit the json response back to our js handler.
			wp_send_json( array(
				'city'    => $city,
				'state'   => $state,
				'country' => substr( $country, 0, 2 ), // pass first two letters of country (ie: US)
			) );
		}
	}
	exit;
}
add_action( 'wp_ajax_geocode_zip', 'yikes_ajax_geocode_mc_zip_lookup' );

/*
* Setting up i18n
*/
function yikes_zip_load_zip_lookup_text_domain() {
	load_plugin_textdomain(
		'zip-lookup-for-mailchimp',
		false,
		dirname( __FILE__  ) . '/languages/'
	);
}
add_action( 'plugins_loaded', 'yikes_zip_load_zip_lookup_text_domain' );