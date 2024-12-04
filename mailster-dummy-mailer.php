<?php
/*
Plugin Name: Mailster Dummy Mailer
Requires Plugins: mailster
Plugin URI: https://mailster.co/?utm_campaign=wporg&utm_source=wordpress.org&utm_medium=plugin&utm_term=Dummy+Mailer
Description: A Dummy Mailer for Mailster
Version: 1.2.1
Author: EverPress
Author URI: https://mailster.co
Text Domain: mailster-dummy-mailer
License: GPLv2 or later
*/

define( 'MAILSTER_DUMMYMAILER_REQUIRED_VERSION', '4.0' );
define( 'MAILSTER_DUMMYMAILER_ID', 'dummymailer' );
define( 'MAILSTER_DUMMYMAILER_FILE', __FILE__ );

require_once __DIR__ . '/classes/dummymailer.class.php';
new MailsterDummyMailer();
