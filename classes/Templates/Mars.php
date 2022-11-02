<?php
/**
* The Mars Templates Engine
* @package Mars
*/

namespace Mars\Templates;

use Mars\App;
use Mars\Handlers;

/**
* The Mars Templates Engine
*
* The currently supported modifiers are:
* raw,js,jscode,lower,upper,url,urlencode,urlrawencode,timestamp,date,date,cut,cut_middle,empty,strip_tags,nl2br,trim,http,https,ajax,to_url,ip,e,escape,round,count,number,path
*
* Syntax for including subtemplates
* {% include template_name %}
* template_name must not include the .tpl extension
*
* Syntax for IF structures:
* {% if $var %}
* {% elseif %}
* {% else %}
* {% endif %}
*
* Syntax for FOREACH structures
* {% foreach $foo as $bar %} OR {% foreach $foo as $i => $bar %}
* {{ $bar.element1 }}
* {{ $bar.element2 }}
* {% endforeach %}
*/
class Mars implements DriverInterface
{
	use \Mars\AppTrait;

	/**
	* @var Handlers $handlers The handlers object
	*/
	public readonly Handlers $handlers;

	/**
	* @var array $supported_rules The list of supported rules
	*/
	protected array $supported_handlers = [
		'include' => '\Mars\Templates\Mars\IncludeParser',
		'variable_double' => '\Mars\Templates\Mars\VariableDoubleParser',
		'variable_raw' => '\Mars\Templates\Mars\VariableRawParser',
		'variable' => '\Mars\Templates\Mars\VariableParser',
		'if' => '\Mars\Templates\Mars\IfParser',
		'foreach' => '\Mars\Templates\Mars\ForeachParser'
	];

	/**
	* @var array $parsers The parsers array
	*/
	protected array $parsers = [];

	/**
	* Builds the Mars Template object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		ini_set('pcre.backtrack_limit', 10000000);

		$this->app = $app;
		$this->handlers = new Handlers($this->supported_handlers, $this->app);
		$this->parsers = $this->handlers->getAll();

		$this->app->plugins->run('templates_mars_construct', $this);
	}

	/**
	* @see \Mars\Templates\DriverInterface::parse()
	* {@inheritdoc}
	*/
	public function parse(string $content, array $params) : string
	{
		foreach ($this->parsers as $parser) {
			$content = $parser->parse($content, $params);
		}

		return $content;
	}
}
