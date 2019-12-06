<?php
/**
* The Alert Class
* @package Mars
*/

namespace Mars\Alerts;

use Mars\App;

/**
* The Alert Class
* Encapsulates the properties of an alert [title, text]
*/
class Alert
{
	/**
	* @var string $title The title of the alert
	*/
	public string $title = '';

	/**
	* @var string $text The text of the alert
	*/
	public string $text = '';

	/**
	* @var bool $escape_html If true, will escape the title when outputing it
	*/
	public bool $escape_html = true;

	/**
	* @var bool $nl2br If true, will call nl2br on text
	*/
	public bool $nl2br = false;

	/**
	* Builds the alert
	* @param string $text The text of the alert
	* @param string $title The title of the alert
	* @param bool $escape_html If true, will escape the title when outputing it
	* @param bool $nl2br If true, will call nl2br on text
	*/
	public function __construct(string $text, string $title = '', bool $escape_html = true, bool $nl2br = false)
	{
		$this->title = $title;
		$this->text = $text;
		$this->escape_html = $escape_html;
		$this->nl2br = $nl2br;
	}

	/**
	* Outputs the title
	*/
	public function outputTitle()
	{
		echo $this->getTitle();
	}

	/**
	* Outputs the text
	*/
	public function outputText()
	{
		echo $this->getText();
	}

	/**
	* Alias for output_text
	*/
	public function output()
	{
		$this->outputText();
	}

	/**
	* Returns the title of the alert
	* @return string
	*/
	public function getTitle() : string
	{
		$title = $this->title;
		if ($this->escape_html) {
			$title = App::e($title);
		}

		return $title;
	}

	/**
	* Returns the text of the alert
	* @return string
	*/
	public function getText() : string
	{
		$text = $this->text;
		if ($this->escape_html) {
			$text = App::e($text);
		}

		$text = nl2br($text);

		return $text;
	}
}
