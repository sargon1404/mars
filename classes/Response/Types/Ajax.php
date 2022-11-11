<?php
/**
* The Ajax Response Class
* @package Mars
*/

namespace Mars\Response\Types;

use Mars\App;

/**
* The Ajax Response Class
* Generates a json response
*/
class Ajax implements DriverInterface
{
	use \Mars\AppTrait;

	/**
	* Returns a basic response array
	* @return array
	*/
	public function get() : array
	{
		$data = ['ok'=> true, 'error' => $this->app->errors->getFirst(true), 'message' => $this->app->messages->getFirst(true), 'warning' => $this->app->warnings->getFirst(true), 'info' => $this->app->info->getFirst(true), 'html' => ''];
		if (!$this->app->ok()) {
			$data['ok'] = false;
		}

		return $data;
	}

	/**
	* Returns a basic response array, not populated with any values
	* @return array
	*/
	public function getData() : array
	{
		$data = ['ok'=> true, 'error' => '', 'message' => '', 'warning' => '', 'info' => '', 'html' => ''];

		return $data;
	}

	/**
	* Calls json_encode on data, outputs it and dies
	* @param mixed $data The data to send
	*/
	public function send($data)
	{
		header('Content-Type: application/json');

		if ($data) {
			echo \json_encode($data);
		}

		die;
	}

	/**
	* @see \Mars\Response\DriverInterface::output()
	* {@inheritdoc}
	*/
	public function output(string $content, $data = [])
	{
		if (!$data) {
			$data = $this->get();
		}

		if ($data['ok']) {
			$data['html'] = $content;
		}

		$this->send($data);
	}
}
