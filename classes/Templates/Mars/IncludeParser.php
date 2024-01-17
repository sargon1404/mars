<?php
/**
* The Include Hander
* @package Mars
*/

namespace Mars\Templates\Mars;

use Mars\App;

/**
 * The Include Hander
 */
class IncludeParser
{
	/**
	 * @see \Mars\Templates\DriverInterface::parse()
	 * {@inheritdoc}
	 */
	public function parse(string $content, array $params = []) : string
	{
		return preg_replace_callback('/\{\%\s*include(.*)\%\}/U', function (array $match) use ($params) {
			$template = trim($match[1]);
			if (!$template) {
				return '';
			}

			if (!empty($params['template'])) {
				$parts = explode('/', $params['template']);
				$count = count($parts);

				if ($count > 1) {
					$path = implode('/', array_slice($parts, 0, $count - 1));
					$template = $path . '/' . $template;
				}

				return '<?php $this->render(\'' . $template . '\');?>';
			}

			$path = dirname($params['filename']);
			$filename = $path . '/' . $template . '.' . App::FILE_EXTENSIONS['templates'];

			return '<?php $this->renderFilename(\'' . $filename . '\');?>';
		}, $content);
	}
}
