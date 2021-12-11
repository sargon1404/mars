<?php
/**
* The PhpMailer Class
* @package Mars
*/

namespace Mars\Mail;

use Mars\App;

/**
* The PhpMailer Class
* Mail driver which uses PhpMailer
*/
class PhpMailer implements DriverInterface
{
	use \Mars\AppTrait;

	/**
	* @var object $handle The driver's handle
	*/
	protected object $handle;

	/**
	* Builds the PhpMailer object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		$this->handle = new \PHPMailer\PHPMailer\PHPMailer;
		$this->handle->setLanguage('en', $this->app->libraries_path . 'php/vendor/phpmailer/phpmailer/language/');
		$this->handle->CharSet = 'UTF-8';

		if ($this->app->config->mail_smtp) {
			$this->handle->isSMTP();
			$this->handle->Host = $this->app->config->mail_smtp_host;
			$this->handle->Port = $this->app->config->mail_smtp_port;
			$this->handle->SMTPSecure = $this->app->config->mail_smtp_secure;

			if ($this->app->config->mail_smtp_username && $this->app->config->mail_smtp_password) {
				$this->handle->SMTPAuth = true;
				$this->handle->Username = $this->app->config->mail_smtp_username;
				$this->handle->Password = $this->app->config->mail_smtp_password;
			}
		}
	}

	/**
	* @see \Mars\Mail\DriverInterface::setRecipient()
	* {@inheritdoc}
	*/
	public function setRecipient(string|array $to)
	{
		$to = App::getArray($to);

		foreach ($to as $address) {
			$this->handle->addAddress($address);
		}
	}

	/**
	* @see \Mars\Mail\DriverInterface::setRecipientBcc()
	* {@inheritdoc}
	*/
	public function setRecipientBcc(string|array $to)
	{
		$to = App::getArray($to);

		foreach ($to as $address) {
			$this->handle->addBCC($address);
		}
	}

	/**
	* @see \Mars\Mail\DriverInterface::setSubject()
	* {@inheritdoc}
	*/
	public function setSubject(string $subject)
	{
		$this->handle->Subject = $subject;
	}

	/**
	* @see \Mars\Mail\DriverInterface::setBody()
	* {@inheritdoc}
	*/
	public function setBody(string $body, bool $is_html = true)
	{
		$this->handle->Body = $body;
		$this->handle->isHTML($is_html);
	}

	/**
	* @see \Mars\Mail\DriverInterface::setFrom()
	* {@inheritdoc}
	*/
	public function setFrom(string $from, string $from_name = '')
	{
		$this->handle->From = $from;
		$this->handle->FromName = $from_name;
	}

	/**
	* @see \Mars\Mail\DriverInterface::setSender()
	* {@inheritdoc}
	*/
	public function setSender(string $reply_to, string $reply_to_name = '')
	{
		$this->handle->addReplyTo($reply_to, $reply_to_name);
	}

	/**
	* @see \Mars\Mail\DriverInterface::setAttachments()
	* {@inheritdoc}
	*/
	public function setAttachments(array $attachments)
	{
		foreach ($attachments as $attachment) {
			$this->handle->addAttachment($attachment);
		}
	}

	/**
	* @see \Mars\Mail\DriverInterface::send()
	* {@inheritdoc}
	*/
	public function send()
	{
		if (!$this->handle->send()) {
			throw new \Exception($this->handle->ErrorInfo);
		}
	}
}
