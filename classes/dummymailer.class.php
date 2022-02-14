<?php

class MailsterDummyMailer {

	private $plugin_path;
	private $plugin_url;

	public function __construct() {

		$this->plugin_path = plugin_dir_path( MAILSTER_DUMMYMAILER_FILE );
		$this->plugin_url  = plugin_dir_url( MAILSTER_DUMMYMAILER_FILE );

		register_activation_hook( MAILSTER_DUMMYMAILER_FILE, array( &$this, 'activate' ) );
		register_deactivation_hook( MAILSTER_DUMMYMAILER_FILE, array( &$this, 'deactivate' ) );

		load_plugin_textdomain( 'mailster-dummy-mailer' );

		add_action( 'init', array( &$this, 'init' ) );
	}


	/**
	 *
	 *
	 * @param unknown $network_wide
	 */
	public function activate( $network_wide ) {

		if ( function_exists( 'mailster' ) ) {

			$defaults = array(
				'dummymailer_admin_notice'      => true,
				'dummymailer_simulate'          => true,
				'dummymailer_openrate'          => 50,
				'dummymailer_clickrate'         => 20,
				'dummymailer_unsubscriberate'   => 2,
				'dummymailer_bouncerate'        => 0.4,
				'dummymailer_successrate'       => 100,
				'dummymailer_campaignerrorrate' => 0,
			);

			$mailster_options = mailster_options();

			foreach ( $defaults as $key => $value ) {
				if ( ! isset( $mailster_options[ $key ] ) ) {
					mailster_update_option( $key, $value );
				}
			}
		}
	}


	/**
	 *
	 *
	 * @param unknown $network_wide
	 */
	public function deactivate( $network_wide ) {

		if ( function_exists( 'mailster' ) ) {
			if ( mailster_option( 'deliverymethod' ) == MAILSTER_DUMMYMAILER_ID ) {
				mailster_update_option( 'deliverymethod', 'simple' );
			}
		}

	}


	/**
	 * init function.
	 *
	 * init the plugin
	 *
	 * @access public
	 * @return void
	 */
	public function init() {

		if ( ! function_exists( 'mailster' ) ) {

		} else {

			add_filter( 'mailster_delivery_methods', array( &$this, 'delivery_method' ) );
			add_action( 'mailster_deliverymethod_tab_dummymailer', array( &$this, 'deliverytab' ) );
			add_filter( 'mailster_verify_options', array( &$this, 'verify_options' ) );

			if ( mailster_option( 'deliverymethod' ) == MAILSTER_DUMMYMAILER_ID ) {

				add_filter( 'mailster_get_ip', array( &$this, 'random_ip' ) );
				add_filter( 'mailster_get_user_client', array( &$this, 'get_user_client' ) );
				add_filter( 'gettext', array( &$this, 'gettext' ), 20, 3 );

				add_filter( 'mailster_subscriber_errors', array( &$this, 'subscriber_errors' ) );

				add_action( 'mailster_cron_worker', array( &$this, 'simulate' ), 1000 );
				add_action( 'admin_notices', array( &$this, 'admin_notice' ) );

				add_action( 'mailster_initsend', array( &$this, 'initsend' ) );
				add_action( 'mailster_presend', array( &$this, 'presend' ) );
				add_action( 'mailster_dosend', array( &$this, 'dosend' ) );

			}
		}

	}


	/**
	 * simulate function.
	 *
	 * simulates opens, clicks, unsubscribes and bounces
	 *
	 * @access public
	 * @param unknown $translated_text
	 * @param unknown $untranslated_text
	 * @param unknown $domain
	 * @return void
	 */
	public function gettext( $translated_text, $untranslated_text, $domain ) {

		if ( $domain != 'mailster' ) {
			return $translated_text;
		}

		switch ( $untranslated_text ) {
			case 'Message sent. Check your inbox!':
				return __( 'You are using the Dummy Mailer, no email has been sent!', 'mailster-dummy-mailer' );
		}

		return $translated_text;
	}


	public function admin_notice() {

		$screen = get_current_screen();

		switch ( $screen->parent_file ) {
			case 'edit.php?post_type=newsletter':
				if ( mailster_option( 'dummymailer_admin_notice' ) ) : ?>
					<div class="error"><p><?php _e( 'All outgoing mails and statistics are simulated so do not expect anything in your inbox!', 'mailster-dummy-mailer' ); ?></p></div>
					<?php
					endif;
				break;
		}

	}


	public function simulate() {

		if ( ! mailster_option( 'dummymailer_simulate' ) ) {
			return false;
		}

		$campaigns = mailster( 'campaigns' )->get_campaigns( array( 'post_status' => array( 'finished', 'active', 'paused' ) ) );

		if ( empty( $campaigns ) ) {
			return;
		}

		define( 'MAILSTER_DUMMYMAILER_SIMULATE', true );
		$now        = time();
		$timeoffset = mailster( 'helper' )->gmt_offset( true );

		$openrate        = mailster_option( 'dummymailer_openrate' );
		$clickrate       = mailster_option( 'dummymailer_clickrate' );
		$unsubscriberate = mailster_option( 'dummymailer_unsubscriberate' );
		$bouncerate      = mailster_option( 'dummymailer_bouncerate' );

		foreach ( $campaigns as $i => $campaign ) {

			$sent = mailster( 'campaigns' )->get_sent_rate( $campaign->ID );

			if ( ! $sent ) {
				continue;
			}

			$open = mailster( 'campaigns' )->get_open_rate( $campaign->ID );

			if ( $open * 100 >= $openrate ) {
				continue;
			}

			$links = mailster( 'campaigns' )->get_links( $campaign->ID );

			$links = array_values( array_filter( array_diff( $links, array( '#' ) ) ) );

			$explicitopen = $this->rand( 33 );

			$subscribers = mailster( 'campaigns' )->get_sent_subscribers( $campaign->ID );

			if ( empty( $subscribers ) ) {
				continue;
			}

			$meta = mailster( 'campaigns' )->meta( $campaign->ID );

			foreach ( $subscribers as $j => $subscriber ) {

				if ( $explicitopen ) {

					if ( $this->rand( $openrate ) && $open * 100 < $openrate ) {
						do_action( 'mailster_open', $subscriber, $campaign->ID, true );
					}
				} else {
					if ( $this->rand( $openrate ) && $open * 100 < $openrate ) {
						do_action( 'mailster_open', $subscriber, $campaign->ID, false );

						if ( $this->rand( $clickrate ) && mailster( 'campaigns' )->get_click_rate( $campaign->ID ) * 100 < $clickrate ) {
							do_action( 'mailster_click', $subscriber, $campaign->ID, $links[ array_rand( $links ) ], false );
						}

						if ( $this->rand( $unsubscriberate ) && mailster( 'campaigns' )->get_unsubscribe_rate( $campaign->ID ) * 100 < $unsubscriberate ) {
							$unsublink = mailster()->get_unsubscribe_link( $campaign->ID );
							do_action( 'mailster_click', $subscriber, $campaign->ID, $unsublink, false );
							mailster( 'subscribers' )->unsubscribe( $subscriber, $campaign->ID );
						}
					} elseif ( $this->rand( $bouncerate ) && mailster( 'campaigns' )->get_bounce_rate( $campaign->ID ) * 100 < $bouncerate ) {
						mailster( 'subscribers' )->bounce( $subscriber, $campaign->ID, true );
					}
				}

				if ( $j > ( $now - $meta['timestamp'] ) / 5 ) {
					break;
				}
			}
		}

	}


	public function random_ip() {

		return defined( 'MAILSTER_DUMMYMAILER_SIMULATE' ) ? rand( 1, 200 ) . '.' . rand( 1, 255 ) . '.' . rand( 1, 255 ) . '.' . rand( 1, 255 ) : null;
	}


	public function get_user_client() {

		$clients = array(

			array(
				'client'  => 'Thunderbird',
				'version' => rand( 23, 70 ),
				'type'    => 'desktop',
			),
			array(
				'client'  => 'Gmail App (Android)',
				'version' => '',
				'type'    => 'mobile',
			),
			array(
				'client'  => 'Gmail',
				'version' => '',
				'type'    => 'webmail',
			),
			array(
				'client'  => 'WebClient (unknown)',
				'version' => '',
				'type'    => 'webmail',
			),
			array(
				'client'  => 'iPad',
				'version' => 'iOS ' . rand( 8, 14 ),
				'type'    => 'mobile',
			),
			array(
				'client'  => 'iPhone',
				'version' => 'iOS ' . rand( 8, 14 ),
				'type'    => 'mobile',
			),
			array(
				'client'  => 'Microsoft Outlook',
				'version' => rand( 2010, 2016 ),
				'type'    => 'desktop',
			),
			array(
				'client'  => 'Microsoft Outlook',
				'version' => '2003-2007',
				'type'    => 'desktop',
			),
			array(
				'client'  => 'Windows Live Mail',
				'version' => '',
				'type'    => 'desktop',
			),
		);

		return (object) $clients[ array_rand( $clients ) ];

	}


	/**
	 * initsend function.
	 *
	 * uses mailster_initsend hook to set initial settings
	 *
	 * @access public
	 * @return void
	 * @param mixed $mailobject
	 */
	public function initsend( $mailobject ) {}




	/**
	 * presend function.
	 *
	 * uses the mailster_presend hook to apply setttings before each mail
	 *
	 * @access public
	 * @return void
	 * @param mixed $mailobject
	 */
	public function presend( $mailobject ) {

		// use pre_send from the main class
		$mailobject->pre_send();

	}


	/**
	 * dosend function.
	 *
	 * uses the ymail_dosend hook and triggers the send
	 *
	 * @access public
	 * @return void
	 * @param mixed $mailobject
	 */
	public function dosend( $mailobject ) {

		$successrate       = mailster_option( 'dummymailer_successrate' );
		$campaignerrorrate = mailster_option( 'dummymailer_campaignerrorrate' );
		$mailobject->sent  = $this->rand( $successrate );
		if ( ! $mailobject->sent ) {
			if ( $this->rand( $campaignerrorrate ) ) {
				$mailobject->last_error = new Exception( 'DummyMailer Campaign Error' );
			} else {
				$mailobject->last_error = new Exception( 'DummyMailer Subscriber Error' );
			}
		} else {
		}

	}




	/**
	 * rand function.
	 *
	 * add the delivery method to the options
	 *
	 * @access public
	 * @param mixed $p
	 * @return void
	 */
	public function rand( $p ) {
		return mt_rand( 0, 10000 ) <= ( $p * 100 );
	}


	/**
	 * subscriber_errors function.
	 *
	 * adds a subscriber error
	 *
	 * @access public
	 * @param unknown $errors
	 * @return $errors
	 */
	public function subscriber_errors( $errors ) {
		$errors[] = 'DummyMailer Subscriber Error';
		return $errors;
	}


	/**
	 * delivery_method function.
	 *
	 * add the delivery method to the options
	 *
	 * @access public
	 * @param mixed $delivery_methods
	 * @return void
	 */
	public function delivery_method( $delivery_methods ) {
		$delivery_methods[ MAILSTER_DUMMYMAILER_ID ] = 'DummyMailer';
		return $delivery_methods;
	}


	/**
	 * deliverytab function.
	 *
	 * the content of the tab for the options
	 *
	 * @access public
	 * @return void
	 */
	public function deliverytab() {

		$verified = mailster_option( MAILSTER_DUMMYMAILER_ID . '_verified' );

		?>

		<p class="description"><?php _e( 'The Dummy Mailer doesn\'t send any real mail rather it simulates a real environment. The rates you can define will be used to simulate the campaigns', 'mailster-dummy-mailer' ); ?></p>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e( 'Admin Notice', 'mailster-dummy-mailer' ); ?>
				</th>
				<td><label><input type="hidden" name="mailster_options[dummymailer_admin_notice]" value="0"><input type="checkbox" name="mailster_options[dummymailer_admin_notice]" value="1" <?php checked( mailster_option( 'dummymailer_admin_notice' ) ); ?>> <?php _e( 'Display Admin Notice on the Newsletter page', 'mailster-dummy-mailer' ); ?></label>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( 'Simulate Rates', 'mailster-dummy-mailer' ); ?>
				</th>
				<td><label><input type="hidden" name="mailster_options[dummymailer_simulate]" value="0"><input type="checkbox" name="mailster_options[dummymailer_simulate]" value="1" <?php checked( mailster_option( 'dummymailer_simulate' ) ); ?>> <?php _e( 'Simulate Rates based on the settings below', 'mailster-dummy-mailer' ); ?></label>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( 'Rates', 'mailster-dummy-mailer' ); ?>
				<p class="description"><?php _e( 'Define the rates which should get simulated', 'mailster-dummy-mailer' ); ?></p>
				</th>
				<td>
				<div class="mailster_text"><label><?php _e( 'Open Rate', 'mailster-dummy-mailer' ); ?>:</label> <input type="number" min="0" max="100" step="0.1" name="mailster_options[dummymailer_openrate]" class="postform textright" value="<?php echo mailster_option( 'dummymailer_openrate' ); ?>">% </div>
				<div class="mailster_text"><label><?php _e( 'Click Rate', 'mailster-dummy-mailer' ); ?>:</label> <input type="number" min="0" max="100" step="0.1" name="mailster_options[dummymailer_clickrate]" class="postform textright" value="<?php echo mailster_option( 'dummymailer_clickrate' ); ?>">% </div>
				<div class="mailster_text"><label><?php _e( 'Unsubscribe Rate', 'mailster-dummy-mailer' ); ?>:</label> <input type="number" min="0" max="100" step="0.1" name="mailster_options[dummymailer_unsubscriberate]" class="postform textright" value="<?php echo mailster_option( 'dummymailer_unsubscriberate' ); ?>">% </div>
				<div class="mailster_text"><label><?php _e( 'Bounce Rate', 'mailster-dummy-mailer' ); ?>:</label> <input type="number" min="0" max="100" step="0.1" name="mailster_options[dummymailer_bouncerate]" class="postform textright" value="<?php echo mailster_option( 'dummymailer_bouncerate' ); ?>">% </div>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( 'Error Rates', 'mailster-dummy-mailer' ); ?>
				</th>
				<td>
				<div class="mailster_text"><label><?php _e( 'Success Rate', 'mailster-dummy-mailer' ); ?>:</label> <input type="number" min="0" max="100" step="0.1" name="mailster_options[dummymailer_successrate]" class="postform textright" value="<?php echo mailster_option( 'dummymailer_successrate' ); ?>">% </div>
				<div class="mailster_text"><label><?php _e( 'Campaign Error Rate', 'mailster-dummy-mailer' ); ?>:</label> <input type="number" min="0" max="100" step="0.1" name="mailster_options[dummymailer_campaignerrorrate]" class="postform textright" value="<?php echo mailster_option( 'dummymailer_campaignerrorrate' ); ?>">% </div>
				</td>
			</tr>
		</table>

		<?php

	}



	/**
	 * verify_options function.
	 *
	 * some verification if options are saved
	 *
	 * @access public
	 * @param mixed $options
	 * @return void
	 */
	public function verify_options( $options ) {

		// only if delivery method is dummymailer
		if ( $options['deliverymethod'] == MAILSTER_DUMMYMAILER_ID ) {

		}

		return $options;
	}


}


new MailsterDummyMailer();
