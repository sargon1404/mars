<?php
/**
* The Mailer Class
* @package Mars
*/

namespace Mars\Helpers;

use PHPMailer;
use Mars\App;

/**
* The Mailer Class
* Used to send emails, using the phpmailer library
*/
class Mailer
{
	use \Mars\AppTrait;

	/**
	* @var PHPMailer $mail The PHPMailer object
	*/
	public object $mail;

	/**
	* Builds the Mailer object
	*/
	public function __construct()
	{
		$this->app = $this->getApp();

		$this->mail = new PHPMailer;
		$this->mail->setLanguage('en', $this->app->libraries_dir . 'php/vendor/phpmailer/phpmailer/language/');
		$this->mail->CharSet = 'UTF-8';

		$this->app->plugins->run('helpers_mailer_construct', $this);
	}

	/**
	* Sets the email's subject
	* @param string $subject The subject of the mail
	* @return $this
	*/
	public function setSubject(string $subject)
	{
		$this->mail->Subject = $subject;

		return $this;
	}

	/**
	* Sets the email's body
	* @param string $body The body of the mail
	* @param bool $is_html If true the mail will be a html mail
	* @return $this
	*/
	public function setBody(string $body, bool $is_html = true)
	{
		$this->mail->Body = $body;
		$this->mail->isHTML($is_html);

		return $this;
	}

	/**
	* Sets the From fields of the email
	* @param string $from The email adress from which the email will be send
	* @param string $from_name The from name field of the email
	* @return $this
	*/
	public function setFrom(string $from = '', string $from_name = '')
	{
		$this->mail->From = $from;
		$this->mail->FromName = $from_name;

		return $this;
	}

	/**
	* Sets the email's charset
	* @param string $encoding the charset of the email
	* @return $this
	*/
	public function setEncoding(string $encoding = 'UTF-8')
	{
		$this->mail->CharSet = $encoding;

		return $this;
	}

	/**
	* Sets the recipient of the email
	* @param string|array $to The address(es) where the mail will be sent
	* @return $this
	*/
	public function setRecipient($to)
	{
		if (!$to) {
			return $this;
		}

		$to = App::getArray($to);

		foreach ($to as $address) {
			$this->mail->addAddress($address);
		}

		return $this;
	}

	/**
	* Sets the mail's recipient as undisclosed-recipients:;
	* @param string|array $to The address(es) where the mail will be sent
	* @return $this
	*/
	public function setRecipientUndisclosed($to)
	{
		if (!$to) {
			return $this;
		}

		$to = App::getArray($to);

		$this->mail->addAddress('undisclosed-recipients:;');
		$this->mail->addBCC(implode(', ', $to));

		return $this;
	}

	/**
	* Sets the mail's recipients as bcc, except the first one
	* @param string|array $to The address(es) where the mail will be sent
	* @return $this
	*/
	public function setRecipientBcc($to)
	{
		if (!$to) {
			return $this;
		}

		$to = App::getArray($to);

		$i = 0;
		foreach ($to as $address) {
			if (!$i) {
				$this->mail->addAddress($address);
			} else {
				$this->mail->addBCC($address);
			}

			$i++;
		}

		return $this;
	}

	/**
	* Sets the sender of the email
	* @param string $reply_to The email address listed as reply to
	* @param string $reply_to_name The reply name, if any
	* @return $this
	*/
	public function setSender(string $reply_to, string $reply_to_name = '')
	{
		if (!$reply_to) {
			return $this;
		}

		$this->mail->addReplyTo($reply_to, $reply_to_name);

		return $this;
	}

	/**
	* Adds the specified files as attachments to the email
	* @param array $attachments The attachments, if any
	* @return $this
	*/
	public function setAttachments(array $attachments = [])
	{
		if (!$attachments) {
			return;
		}

		foreach ($attachments as $attachment) {
			$this->mail->addAttachment($attachment);
		}
	}

	/**
	* Sends the email with smtp
	* @param string $host The host to use
	* @param string $port The port to use
	* @param bool $secure If true, will send the email with smtpsecure = yes
	* @param bool $use_auth True if the smtp server uses auth.
	* @param string $auth_username The auth. username
	* @param string $auth_password The auth password
	* @return $this
	*/
	public function isSmtp(string $host, string $port, bool $secure = true, bool $use_auth = false, string $auth_username = '', string $auth_password = '')
	{
		$this->mail->isSMTP();
		$this->mail->Host = $host;
		$this->mail->Port = $port;
		$this->mail->SMTPSecure = $secure;

		if ($use_auth) {
			$this->mail->SMTPAuth = true;
			$this->mail->Username = $auth_username;
			$this->mail->Password = $auth_password;
		}

		return $this;
	}

	/**
	* Sends the email
	* @param bool True on success, false on failure
	*/
	public function send() : bool
	{
		return $this->mail->send();
	}

	/**
	* Returns the error, if any
	* @return string
	*/
	public function getError() : string
	{
		return $this->mail->ErrorInfo;
	}
}
