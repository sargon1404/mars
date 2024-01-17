<?php
/**
* The FILES Request Class
* @package Mars
*/

namespace Mars\Request;

use Mars\App;

/**
 * The FILES Request Class
 * Handles the $_UPLOAD interactions
 */
class Files extends Base
{
	use \Mars\AppTrait;

	/**
	 * @var array $disallowed_extensions The extensions of the files which are disallowed at upload
	 */
	protected array $disallowed_extensions = ['php', 'cgi', 'pl', 'py', 'exe', 'sh', 'bin'];

	/**
	 * Builds the Files Request object
	 * @param App $app The app object
	 */
	public function __construct(App $app)
	{
		$this->app = $app;

		$this->data = &$_FILES;
	}

	/**
	 * Checks if a file is an uploaded file
	 * @param string $name The name of the file
	 * @return bool Returns true if the file is uploaded
	 */
	public function isUploaded(string $name) : bool
	{
		if (!$this->has($name)) {
			return false;
		}

		$filename_array = (array) $this->data[$name]['tmp_name'];
		foreach ($filename_array as $filename) {
			if ($filename) {
				if (!is_uploaded_file($filename)) {
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Basic upload file
	 * @param string $name The name of the file
	 * @param string $filename The filename where the file will be uploaded
	 * @return bool Returns true if the file was succesfully uploaded
	 */
	public function uploadRaw(string $name, string $filename) : bool
	{
		if (!move_uploaded_file($_FILES[$name]['tmp_name'], $filename)) {
			return false;
		}

		return true;
	}

	/**
	 * Uploads a file/files
	 * @param string $name The name of the file
	 * @param string $upload_dir Destination folder
	 * @param string|array $allowed_extensions Array containing the extensions of the file that are allowed to be uploaded. If '*' is passed all types of files are allowed [minus those deemed unsafe]
	 * @param bool $append_suffix If true, will always generate a random suffix for the uploaded filename
	 * @param bool $append_suffix_if_file_exists If true, will generate a random suffix if the file already exists
	 * @return array Returns the list of uploaded files
	 * @throws Exception if the upload failed
	 */
	public function upload(string $name, string $upload_dir, string|array $allowed_extensions = [], bool $append_suffix = false, bool $append_suffix_if_file_exists = true) : array
	{
		$this->app->plugins->run('request_files_upload', $name, $upload_dir, $allowed_extensions, $append_suffix, $append_suffix_if_file_exists, $this);

		if (!$this->has($name)) {
			return [];
		}

		$uploaded_files = [];
		$file = $this->data[$name];
		$is_array = is_array($file['tmp_name']);
		$tmp_array = (array) $file['tmp_name'];
		$errors_array = (array) $file['error'];
		$name_array = (array)$file['name'];

		foreach ($tmp_array as $i => $tmp_filename) {
			if (!$tmp_filename) {
				continue;
			}

			$name = $name_array[$i];

			$this->checkCanUpload($name);

			$filename = $this->getFilename($name, $upload_dir, $append_suffix, $append_suffix_if_file_exists);

			if (move_uploaded_file($tmp_filename, $filename)) {
				$this->app->plugins->run('request_files_upload_success', $filename, $this);

				$uploaded_files[$name] = $filename;
			} else {
				$this->app->plugins->run('request_files_upload_error', $filename, $tmp_filename, $upload_dir, $this);

				throw new \Exception($this->getUploadError($errors_array[$i], $name));
			}
		}

		return $uploaded_files;
	}

	/**
	 * Checks if $filename can be uploaded, based on extension
	 * @param string $filename The filename to check
	 * @param string|array $allowed_extensions Array containing the extensions of the file that are allowed to be uploaded. If '*' is passed all types of files are allowed [minus those deemed unsafe]
	 * @throws Exception if the file can't be uploaded
	 */
	protected function checkCanUpload(string $filename, string|array $allowed_extensions = [])
	{
		$extension = $this->app->file->getExtension($filename);

		if (in_array($extension, $this->disallowed_extensions)) {
			throw new \Exception(App::__('upload_error_invalid_type', ['{FILE}' => $filename]));
		}

		if ($allowed_extensions && $allowed_extensions != '*') {
			if (!in_array($extension, (array)$allowed_extensions)) {
				throw new \Exception(App::__('upload_error_invalid_type', ['{FILE}' => $filename]));
			}
		}
	}

	/**
	 * Returns the filename where the file will be uploaded
	 * @param string $name The name of the file
	 * @param string $upload_dir Destination folder
	 * @param bool $append_suffix If true, will always generate a random suffix for the uploaded filename
	 * @param bool $append_suffix_if_file_exists If true, will generate a random suffix if the file already exists
	 * @return string
	 */
	protected function getFilename(string $name, string $upload_dir, bool $append_suffix = false, bool $append_suffix_if_file_exists = true) : string
	{
		$filename = App::fixPath($upload_dir) . $this->app->filter->filename($name);

		if (!$append_suffix && $append_suffix_if_file_exists) {
			if (is_file($filename)) {
				$append_suffix = true;
			}
		}

		if ($append_suffix) {
			$suffix = $this->app->random->getString(20);
			$filename = $this->app->file->appendToFilename($filename, '-' . $suffix);
		}

		return $filename;
	}

	/**
	 * Returns the upload error
	 * @param string $code The error code
	 * @param string $file The file
	 * @return
	 */
	protected function getUploadError(string $code, string $file) : string
	{
		switch ($code) {
			case UPLOAD_ERR_INI_SIZE:
				return App::__('upload_error_size', ['{SIZE}' => ini_get('upload_max_filesize')]);
				break;
			case UPLOAD_ERR_PARTIAL:
				return App::__('upload_error_partial');
			case UPLOAD_ERR_NO_FILE:
				return App::__('upload_error_nofile');
			case UPLOAD_ERR_NO_TMP_DIR:
				return App::__('upload_error_tmp');
		}

		return App::__('upload_error_generic', ['{FILE}' => $file]);
	}
}
