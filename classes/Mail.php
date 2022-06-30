<?php
/**
* The Mail Class
* @package Mars
*/

namespace Mars;

/**
* The Mail Class
* The system's mailer object
*/
class Mail
{
	use AppTrait;
	use DriverTrait;

	/**
	* @var string $driver The used driver
	*/
	protected string $driver = '';

	/**
	* @var string $driver_key The name of the key from where we'll read additional supported drivers from app->config->drivers
	*/
	protected string $driver_key = 'mail';

	/**
	* @var string $driver_interface The interface the driver must implement
	*/
	protected string $driver_interface = '\Mars\Mail\DriverInterface';

	/**
	* @var array $supported_drivers The supported drivers
	*/
	protected array $supported_drivers = [
		'phpmailer' => '\Mars\Mail\PhpMailer'
	];

	/**
	* Constructs the mail object
	* @param App $app The app object
	* @param string $driver The driver to use. phpmailer is currently supported
	* @param string $host The host to connect to
	* @param string $port The port to connect to
	* @param string $key Secret key used to identify the site
	*/
	public function __construct(App $app, string $driver = '')
	{
		$this->app = $app;

		if (!$driver) {
			$driver = $this->app->config->mail_driver;
		}

		$this->driver = $driver;
		$this->handle = $this->getHandle();
	}

	/**
	* Sends a mail
	* @param string|array $to The adress(es) where the mail will be sent
	* @param string $subject The subject of the mail
	* @param string $message The body of the mail
	* @param array $attachments The attachments, if any, to the mail
	* @param string|array $bcc Bcc recipients, if any
	* @param string $from The email adress from which the email will be send.By default $this->app->config->mail_from is used
	* @param string $from_name The from name field of the email.by default $this->app->config->mail_from_name is used
	* @param string $reply_to The email address to which to reply to
	* @param string $reply_to_name The reply name associated with the $reply_to email
	* @param bool $is_html If true the mail will be a html mail
	*/
	public function send(string|array $to, string $subject, string $message, array $attachments = [], string|array $bcc = [], string $from = '', string $from_name = '', string $reply_to = '', string $reply_to_name = '', bool $is_html = true)
	{
		$this->app->plugins->run('mail_send', $to, $subject, $message, $from, $from_name, $reply_to, $reply_to_name, $is_html, $attachments, $this);

		try {
			if (!$from) {
				$from = $this->app->config->mail_from;
			}
			if (!$from_name) {
				$from_name = $this->app->config->mail_from_name;
			}

			$this->handle->setRecipient($to);
			$this->handle->setSubject($subject);
			$this->handle->setBody($message, $is_html);
			$this->handle->setFrom($from, $from_name);
			$this->handle->setSender($reply_to, $reply_to_name);

			if ($attachments) {
				$this->handle->setAttachments($attachments);
			}
			if ($bcc) {
				$this->handle->setRecipientBcc($bcc);
			}

			$this->handle->send();
			
			$this->app->plugins->run('mail_sent', $to, $subject, $message, $from, $from_name, $reply_to, $reply_to_name, $is_html, $attachments, $this);
		} catch (\Exception $e) {
			$this->app->plugins->run('mail_send_error', $e->getMessage(), $to, $subject, $message, $from, $from_name, $reply_to, $reply_to_name, $is_html, $attachments, $this);
			
			throw new \Exception("Error sending mail: {$e->getMessage()}");
		}
	}
}
