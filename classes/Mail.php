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
	* @var string $from The from address
	*/
	public string $from = '';

	/**
	* @var string $from_name The from name
	*/
	public string $from_name = '';

	/**
	* @var string $reply_to The reply to address
	*/
	public string $reply_to = '';

	/**
	* @var string $reply_to_name The reply to name
	*/
	public string $reply_to_name = '';

	/**
	* @var bool $is_html If true, will send the email as html
	*/
	public bool $is_html = true;

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
	public function __construct(App $app = null)
	{
		$this->app = $app ?? $this->getApp();
		$this->drivers = new Drivers($this->supported_drivers, DriverInterface::class, 'mail', $this->app);
		$this->driver = $this->drivers->get($this->app->config->mail_driver);

		$this->from = $this->app->config->mail_from;
		$this->from_name = $this->app->config->mail_from_name;
	}

	/**
	* Sets the From fields of the email
	* @param string $from The email adress from which the email will be send
	* @param string $from_name The from name field of the email
	* @return static
	*/
	public function setFrom(string $from, string $from_name = '') : static
	{
		$this->from = $from;
		if ($from_name) {
			$this->from_name = $from_name;
		}

		return $this;
	}

	/**
	* Sets the sender of the email
	* @param string $reply_to The email address listed as reply to
	* @param string $reply_to_name The reply name, if any
	* @return static
	*/
	public function setSender(string $reply_to, string $reply_to_name = '') : static
	{
		$this->reply_to = $reply_to;
		if ($reply_to_name) {
			$this->reply_to_name = $reply_to_name;
		}
	}

	/**
	* Sets the way how the email message is send
	* @param bool $is_html If true, will send the email as html
	* @return static
	*/
	public function isHtml(bool $is_html) : static
	{
		$this->is_html = $is_html;

		return $this;
	}

	/**
	* Returns the content of a template to be used as the body of the email
	* @param string $filename The template's filename
	* @param array $vars Vars to add as template vars
	* @return string The template's content
	*/
	public function getTemplate(string $filename, array $vars = []) : string
	{
		$content = '';
		if (is_file($filename)) {
			$content = $this->app->theme->getTemplateFromFilename($filename, $vars, 'mail');
		} else {
			$content = $this->app->theme->getTemplate($filename, $vars, 'mail');
		}

		return nl2br($content);
	}

	/**
	* Sends a mail
	* @param string|array $to The adress(es) where the mail will be sent
	* @param string $subject The subject of the mail
	* @param string $message The body of the mail
	* @param array $attachments The attachments, if any, to the mail
	* @param string|array $bcc Bcc recipients, if any
	*/
	public function send(string|array $to, string $subject, string $message, array $attachments = [], string|array $bcc = [])
	{
		$this->app->plugins->run('mail_send', $to, $subject, $message, $attachments, $this);

		try {
			$this->driver->setRecipient($to);
			$this->driver->setSubject($subject);
			$this->driver->setBody($message, $this->is_html);
			$this->driver->setFrom($this->from, $this->from_name);
			$this->driver->setSender($this->reply_to, $this->reply_to_name);

			if ($attachments) {
				$this->driver->setAttachments($attachments);
			}
			if ($bcc) {
				$this->driver->setRecipientBcc($bcc);
			}

			$this->driver->send();

			$this->app->plugins->run('mail_sent', $to, $subject, $message, $attachments, $this);
		} catch (\Exception $e) {
			$this->app->plugins->run('mail_send_error', $e->getMessage(), $to, $subject, $message, $attachments, $this);

			throw new \Exception(App::__('mail_error', ['{ERROR}' => $e->getMessage()]));
		}
	}
}
