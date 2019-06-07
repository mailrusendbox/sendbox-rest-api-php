<?php
/*
	Plugin Name: Sendbox Email Marketing Newsletter
	Plugin URI: https://wordpress.org/plugins/sendbox-email-marketing-newsletter/
	Description: Add e-mail subscription form, send marketing newsletters and create autoresponders.
	Version: 1.0.0
	Author: Mail.ru for business
	Author URI: https://biz.mail.ru/sendbox
	License:     GPL2
	License URI: https://www.gnu.org/licenses/gpl-2.0.html
	Text Domain: sendbox-email-marketing-newsletter
	Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


include_once( 'inc/class-sendbox-newsletter-requirement.php' );

$requirement = new Sendbox_Newsletter_Requirement();

if ( $requirement->is_success() ) {

	include_once( 'inc/class-sendbox-newsletter-loader.php' );

	new Sendbox_Newsletter_Loader(
		plugins_url( '/', __FILE__ ),
		basename( dirname( __FILE__ ) ) . '/languages/'
	);

}