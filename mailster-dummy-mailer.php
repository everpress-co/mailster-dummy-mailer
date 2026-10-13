<?php
/**
 * Plugin Name: Mailster Dummy Mailer
 * Requires Plugins: mailster
 * Plugin URI: https://mailster.co/?utm_campaign=wporg&utm_source=wordpress.org&utm_medium=plugin&utm_term=Dummy+Mailer
 * Description: A Dummy Mailer for Mailster
 * Version: 1.3.0
 * Author: EverPress
 * Author URI: https://mailster.co
 * Text Domain: mailster-dummy-mailer
 * License: GPLv2 or later
 *
 * @package Mailster_Dummy_Mailer
 */

namespace EverPress\Mailster\DummyMailer;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Minimum required Mailster version.
 *
 * @since 1.0
 */
define( 'MAILSTER_DUMMYMAILER_REQUIRED_VERSION', '4.0' );

/**
 * Delivery method identifier used in Mailster options.
 *
 * @since 1.0
 */
define( 'MAILSTER_DUMMYMAILER_ID', 'dummymailer' );

/**
 * Absolute path to the main plugin file.
 *
 * @since 1.2
 */
define( 'MAILSTER_DUMMYMAILER_FILE', __FILE__ );

require_once __DIR__ . '/classes/dummymailer.class.php';
add_action( 'plugins_loaded', array( 'EverPress\Mailster\DummyMailer\Mailer', 'instance' ) );
