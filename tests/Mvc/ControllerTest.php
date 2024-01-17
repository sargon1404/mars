<?php

use Mars\Mvc\Controller;

include_once(dirname(__DIR__) . '/Base.php');


class MyController extends Controller
{
	protected bool $load_model = false;
	protected bool $load_view = false;

	public function index()
	{
		echo 'my index';
	}

	public function myAction()
	{
		echo 'my action';
	}

	public function mySuccess()
	{
		echo 'my success';
	}

	public function myError()
	{
		echo 'my error';
	}

	protected function myProtectedAction()
	{
		echo 'none';
	}

	public function withSuccess()
	{
		echo 'with success';
		return true;
	}

	public function withError()
	{
		echo 'with error';
		return false;
	}

	public function myJson()
	{
		return [
			'foo' => 'bar'
		];
	}
}

/**
 * @ignore
 */
final class ControllerTest extends Base
{
	public function testDispatch()
	{
		$controller = new MyController;
		$controller->setDefaultSuccessMethod('mySuccess')->setDefaultErrorMethod('myError');

		ob_start();
		$controller->dispatch();
		$this->assertSame('my index', ob_get_clean());

		ob_start();
		$controller->dispatch('index');
		$this->assertSame('my index', ob_get_clean());

		ob_start();
		$controller->dispatch('myAction');
		$this->assertSame('my action', ob_get_clean());

		ob_start();
		$controller->dispatch('myProtectedAction');
		$this->assertSame('my index', ob_get_clean());

		ob_start();
		$controller->dispatch('withSuccess');
		$this->assertSame('with successmy success', ob_get_clean());

		ob_start();
		$controller->dispatch('withError');
		$this->assertSame('with errormy error', ob_get_clean());

		ob_start();
		$controller->dispatch('myJson');
		$this->assertSame('{"success":true,"error":"","message":"","warning":"","info":"","foo":"bar"}', ob_get_clean());
	}
}
