<?php

use Mars\Serializer;

include_once(__DIR__ . '/Base.php');

final class HtmlTest extends Base
{
	public function testImg()
	{
		$html = $this->app->html;

		$this->assertSame($html->img('https://mydomain/mypic.jpg'), '<img src="https://mydomain/mypic.jpg" alt="mypic.jpg">');
		$this->assertSame($html->img('https://mydomain/mypic.jpg', 200, 100, 'My Alt'), '<img src="https://mydomain/mypic.jpg" alt="My Alt" width="200" height="100">');
	}

	public function testImgWh()
	{
		$html = $this->app->html;

		$this->assertSame($html->imgWh(200, 100), ' width="200" height="100"');
		$this->assertSame($html->imgWh(), '');
	}

	public function testPicture()
	{
		$html = $this->app->html;

		$this->assertSame(
			$html->picture('https://mydomain/mypic.jpg', [['url' => 'https://mydomain/mypic-small.jpg', 'min' => 500], ['url' => 'https://mydomain/mypic-big.jpg', 'min' => 1000]]),
			'<picture>
<source media="(min-width:500px)" srcset="https://mydomain/mypic-small.jpg">
<source media="(min-width:1000px)" srcset="https://mydomain/mypic-big.jpg">
<img src="https://mydomain/mypic.jpg" alt="mypic.jpg">
</picture>'
		);

		$this->assertSame(
			$html->picture('https://mydomain/mypic.jpg', [['url' => 'https://mydomain/mypic-small.jpg', 'min' => 500, 'max' => 1000], ['url' => 'https://mydomain/mypic-big.jpg', 'min' => 1000]]),
			'<picture>
<source media="(min-width:500px) and (max-width:1000px)" srcset="https://mydomain/mypic-small.jpg">
<source media="(min-width:1000px)" srcset="https://mydomain/mypic-big.jpg">
<img src="https://mydomain/mypic.jpg" alt="mypic.jpg">
</picture>'
		);
	}

	public function testA()
	{
		$html = $this->app->html;

		$this->assertSame($html->a('https://mydomain/mypage'), '<a href="https://mydomain/mypage">https://mydomain/mypage</a>');
		$this->assertSame($html->a('https://mydomain/mypage', 'My Page'), '<a href="https://mydomain/mypage">My Page</a>');
	}

	public function testUl()
	{
		$html = $this->app->html;

		$this->assertSame($html->ul(['abc', 'def']), '<ul>
<li>abc</li>
<li>def</li>
</ul>');

		$this->assertSame($html->ul(['<a>bc', 'def']), '<ul>
<li><a>bc</li>
<li>def</li>
</ul>');
	}

	public function testFormOpen()
	{
		$html = $this->app->html;

		$this->assertSame($html->formOpen('https://mydomain/mypage'), '<form action="https://mydomain/mypage" method="post">' . "\n");
		$this->assertSame($html->formOpen('https://mydomain/mypage', ['enctype' => 'multipart/form-data']), '<form action="https://mydomain/mypage" method="post" enctype="multipart/form-data">' . "\n");
	}

	public function testFormClose()
	{
		$arr1 = ['a' => 'hello', 'b' => 'world'];
		$arr2 = ['a' => 'goodbye', 'b'=> 'cruel'];

		$html = $this->app->html;

		$this->assertSame($html->formClose(), '</form>');
	}

	public function testInput()
	{
		$html = $this->app->html;

		$this->assertSame($html->input('foo', 'bar', 'bar placeholder'), '<input type="text" name="foo" value="bar" placeholder="bar placeholder" id="foo">' . "\n");
		$this->assertSame($html->input('foo', 'bar', 'bar placeholder'), '<input type="text" name="foo" value="bar" placeholder="bar placeholder" id="foo-1">' . "\n");
		$this->assertSame($html->input('foo', 'bar', 'bar placeholder', false, ['id' => 'my-id']), '<input type="text" name="foo" value="bar" placeholder="bar placeholder" id="my-id">' . "\n");
	}

	public function testInputHidden()
	{
		$html = $this->app->html;
		$this->assertSame($html->inputHidden('foo', 'bar'), '<input type="hidden" name="foo" value="bar" id="foo-2">' . "\n");
		$this->assertSame($html->inputHidden('foo', 'bar', ['id' => 'my-id']), '<input type="hidden" name="foo" value="bar" id="my-id">' . "\n");
	}

	public function testInputEmail()
	{
		$html = $this->app->html;

		$this->assertSame($html->inputEmail('foo-email', 'bar', 'bar placeholder'), '<input type="email" name="foo-email" value="bar" placeholder="bar placeholder" id="foo-email">' . "\n");
	}

	public function testInputPassword()
	{
		$html = $this->app->html;

		$this->assertSame($html->inputPassword('foo-pass', 'bar'), '<input type="password" name="foo-pass" value="bar" id="foo-pass">' . "\n");
	}

	public function testInputPhone()
	{
		$html = $this->app->html;

		$this->assertSame($html->inputPhone('foo-phone', 'bar', 'bar placeholder'), '<input type="tel" name="foo-phone" value="bar" placeholder="bar placeholder" id="foo-phone">' . "\n");
	}

	public function testTextarea()
	{
		$html = $this->app->html;

		$this->assertSame($html->textarea('my-textarea', 'bar'), '<textarea name="my-textarea" id="my-textarea">bar</textarea>' . "\n");
		$this->assertSame($html->textarea('my-textarea1', '<b>bar</b>'), '<textarea name="my-textarea1" id="my-textarea1">&lt;b&gt;bar&lt;/b&gt;</textarea>' . "\n");
	}

	public function testButton()
	{
		$html = $this->app->html;

		$this->assertSame($html->button('click now!'), '<input type="button" value="click now!">' . "\n");
	}

	public function testSubmit()
	{
		$html = $this->app->html;

		$this->assertSame($html->submit('click now!'), '<input type="submit" value="click now!">' . "\n");
	}

	public function testCheckbox()
	{
		$html = $this->app->html;

		$this->assertSame($html->checkbox('my-checkbox'), '<input type="checkbox" name="my-checkbox" value="1" checked id="my-checkbox">' . "\n");
		$this->assertSame($html->checkbox('my-checkbox', 'My Label'), '<input type="checkbox" name="my-checkbox" value="1" checked id="my-checkbox-1"><label for="my-checkbox-1">My Label</label>' . "\n");
	}

	public function testRadio()
	{
		$html = $this->app->html;

		$this->assertSame($html->radio('my-radio'), '<input type="radio" name="my-radio" value="1" checked id="my-radio">' . "\n");
		$this->assertSame($html->radio('my-radio', 'My Label'), '<input type="radio" name="my-radio" value="1" checked id="my-radio-1"><label for="my-radio-1">My Label</label>' . "\n");
	}

	public function testRadioGroup()
	{
		$html = $this->app->html;

		$this->assertSame($html->radioGroup('my-radio-group', ['radio1' => 'Radio1', 'radio2' => 'Radio2'], 'radio2'), '<input type="radio" value="radio1" name="my-radio-group" id="my-radio-group"><label for="my-radio-group">Radio1</label>
<input type="radio" value="radio2" checked name="my-radio-group" id="my-radio-group-1"><label for="my-radio-group-1">Radio2</label>' . "\n");
	}
}
