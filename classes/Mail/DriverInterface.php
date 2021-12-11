<?php
/**
* The Mail Driver Interface
* @package Mars
*/

namespace Mars\Mail;

/**
* The Mail Driver Interface
*/
interface DriverInterface
{
	/**
	* Sets the recipient(s) of the email
	* @param string|array $to The adress(es) where the mail will be sent
	*/
	public function setRecipient(string|array $to);

	/**
	* Sets the mail's recipients as bcc
	* @param string|array $to The Bcc address(es) where the mail will be sent
	* @return $this
	*/
	public function setRecipientBcc(string|array $to);

	/**
	* Sets the email's subject
	* @param string $subject The subject of the mail
	*/
	public function setSubject(string $subject);

	/**
	* Sets the email's body
	* @param string $body The body of the mail
	* @param bool $is_html If true the mail will be a html mail
	*/
	public function setBody(string $body, bool $is_html = true);

	/**
	* Sets the From fields of the email
	* @param string $from The email adress from which the email will be send
	* @param string $from_name The from name field of the email
	*/
	public function setFrom(string $from, string $from_name = '');

	/**
	* Sets the sender of the email
	* @param string $reply_to The email address listed as reply to
	* @param string $reply_to_name The reply name, if any
	*/
	public function setSender(string $reply_to, string $reply_to_name = '');

	/**
	* Adds the specified files as attachments to the email
	* @param array $attachments The attachments, if any
	*/
	public function setAttachments(array $attachments);

	/**
	* Sends the email
	* @throws Exception
	*/
	public function send();
}
