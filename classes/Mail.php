<?php
/**
* The Mail Class
* @package Mars
*/

namespace Mars;

use Mars\Mail\DriverInterface;

/**
* The Mail Class
* The system's mailer object
*/
class Mail
{
	use AppTrait;

	/**
	* @var Drivers $drivers The drivers object
	*/
	public readonly Drivers $drivers;

	/**
	* @var DriverInterface $driver The driver object
	*/
	protected DriverInterface $driver;

	/**
	* @var array $supported_drivers The supported drivers
	*/
	protected array $supported_drivers = [
		'phpmailer' => '\Mars\Mail\PhpMailer'
	];

	/**
	* Constructs the mail object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;
		$this->drivers = new Drivers($this->supported_drivers, DriverInterface::class, 'mail', $this->app);
		$this->driver = $this->drivers->get($this->app->config->mail_driver);
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

			$this->driver->setRecipient($to);
			$this->driver->setSubject($subject);
			$this->driver->setBody($message, $is_html);
			$this->driver->setFrom($from, $from_name);
			$this->driver->setSender($reply_to, $reply_to_name);

			if ($attachments) {
				$this->driver->setAttachments($attachments);
			}
			if ($bcc) {
				$this->driver->setRecipientBcc($bcc);
			}

			$this->driver->send();

			$this->app->plugins->run('mail_sent', $to, $subject, $message, $from, $from_name, $reply_to, $reply_to_name, $is_html, $attachments, $this);
		} catch (\Exception $e) {
			$this->app->plugins->run('mail_send_error', $e->getMessage(), $to, $subject, $message, $from, $from_name, $reply_to, $reply_to_name, $is_html, $attachments, $this);

			throw new \Exception(App::__('mail_error', ['{ERROR}' => $e->getMessage()]));
		}
	}
}
