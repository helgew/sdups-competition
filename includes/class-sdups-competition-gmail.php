<?php

/**
 * A class to send mail using the Google Client API.
 *
 * @link       https://github.com/helgew/sdups-competition
 * @since      1.0.0
 *
 * @package    SDUPS_Competition
 * @subpackage SDUPS_Competition/includes
 * @author     Helge Weissig <helgew@grajagan.org>
 */
class SDUPS_Competition_GMail {

	/**
	 * The logger.
	 *
	 * @var $LOGGER SDUPS_Competition_Logger our logger
	 */
	static $LOGGER;

	/**
	 * The singleton instance.
	 *
	 * @var $instance SDUPS_Competition_GMail our singleton instance.
	 */
	private static $instance = null;

	// private constructor for singleton
	private function __construct() {
		putenv( "GOOGLE_APPLICATION_CREDENTIALS=" .
		        plugin_dir_path( __DIR__ ) . 'config/api-secret.json' );
	}

	/**
	 * Send email using the Gmail API service.
	 *
	 * @param $to string the recipient of the mail.
	 * @param $subject string the subject of the mail.
	 * @param $message string the message to be sent.
	 *
	 * @return void
	 */
	public function send_mail( string $to, string $subject, string $message ): void {
		try {
			$sender  = '"' . SDUPS_COMPETITION_USER_NAME . '" <' . SDUPS_COMPETITION_USER_EMAIL . '>';
			$msg     = $this->compose_message( $sender, $to, $subject, $message );
			$service = $this->get_service();
			$service->users_messages->send( SDUPS_COMPETITION_USER_EMAIL, $msg );

			self::$LOGGER->debug( "Email to $to with subject '$subject' sent successfully!" );

		} catch ( Exception $e ) {
			self::$LOGGER->error( "Cannot send message: " . $e->getMessage() );
		}
	}

	/**
	 * Send email asynchronously using the Gmail API service.
	 *
	 * @param $to string the recipient of the mail.
	 * @param $subject string the subject of the mail.
	 * @param $message string the message to be sent.
	 *
	 * @return void
	 */
	public function send_mail_async( string $to, string $subject, string $message ): void {
		// not sure why sending a simple array and using send_mail as a hook does not work,
		// but here we are.
		wp_schedule_single_event( time(), 'send_mail', [ [ $to, $subject, $message ] ] );
	}

	/**
	 * The hook used for our async email call (configured in the main class).
	 *
	 * @param $args array an array of [ to, subject, message]
	 *
	 * @return void
	 */
	public function send_mail_delayed( array $args ): void {
		$this->send_mail( $args[0], $args[1], $args[2] );
	}

	// get the Gmail service
	private function get_service(): Google_Service_Gmail {
		$client = new Google_Client();
		$client->useApplicationDefaultCredentials();
		$client->setSubject( SDUPS_COMPETITION_USER_EMAIL );
		$client->setApplicationName( "SDUPS Competition" );
		$client->setScopes( [
			"https://mail.google.com/"
		] );

		return new Google_Service_Gmail( $client );
	}

	// compose our message
	private function compose_message(
		string $sender, string $to, string $subject, string $content
	): Google_Service_Gmail_Message {
		$raw_msg = "From: $sender\r\n";
		$raw_msg .= "To: $to\r\n";
		$raw_msg .= 'Subject: =?utf-8?B?' . base64_encode( $subject ) . "?=\r\n";
		$raw_msg .= "MIME-Version: 1.0\r\n";
		$raw_msg .= "Content-Type: text/html; charset=utf-8\r\n";
		$raw_msg .= "Content-Transfer-Encoding: quoted-printable\r\n\r\n";
		$raw_msg .= "$content\r\n";

		// Encode the message in Base64
		$mime = rtrim( strtr( base64_encode( $raw_msg ), '+/', '-_' ), '=' );

		$msg = new Google_Service_Gmail_Message();
		$msg->setRaw( $mime );

		return $msg;
	}

	/**
	 * We are a singleton!
	 *
	 * @return SDUPS_Competition_GMail
	 */
	public static function instance(): SDUPS_Competition_GMail {
		if ( is_null( self::$instance ) ) {
			self::$instance = new SDUPS_Competition_GMail();
		}

		return self::$instance;
	}
}

SDUPS_Competition_GMail::$LOGGER = new SDUPS_Competition_Logger( SDUPS_Competition_GMail::class,
	SDUPS_Competition_Log_Level::DEBUG );