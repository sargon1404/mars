<?php
/**
* The File Class
* @package Mars
*/

namespace Mars;

/**
* The File Class
* Filesystem functionality
*/
class File
{
	use AppTrait;

	/**
	* @internal
	*/
	protected int $error_code = 0;

	/**
	* Returns the basename from $filename with invalid characters removed
	* @param string $filename The filename for which the basename will be returned
	* @return string The basename of filename
	*/
	public function basename(string $filename) : string
	{
		return $this->app->filter->filename(basename($filename));
	}

	/**
	* Returns the relative path of a filename. Eg: /var/www/mars/dir/some_file.txt => dir/some_file.txt
	* @param string $filename The filename
	* @param bool $ext If false, will strip out the extension
	* @return string The relative path
	*/
	public function getRel(string $filename, bool $ext = true) : string
	{
		if (!$ext) {
			$filename = $this->stripExtension($filename);
		}

		return str_replace($this->app->site_dir, '', $filename);
	}

	/**
	* Returns the parent folder of $filename or empty if there isn't one
	* @param string $filename The filename for which the parent folder will be returned
	* @return string The parent folder of filename or '' if there isn't one
	*/
	public function getDirname(string $filename) : string
	{
		$dir = dirname($filename);
		if ($dir == '.') {
			return '';
		}

		return $dir;
	}

	/**
	* Alias for get_dirname
	* @see File::getDirname()
	*/
	public function dirname(string $filename) : string
	{
		return $this->getDirname($filename);
	}

	/**
	* Returns the filename(strips the extension) of a file
	* @param string $filename The filename for which the filename will be returned
	* @param string $dirname If true, will also include the dirname
	* @return string The filename, without the extension
	*/
	public function getFilename(string $filename, bool $dirname = false) : string
	{
		$pi = pathinfo($filename);

		if ($dirname && $pi['dirname'] != '.') {
			return $pi['dirname'] . '/' . $pi['filename'];
		} else {
			return $pi['filename'];
		}
	}

	/**
	* Generates a random filename
	* @param string $extension The extension of the file, if any
	* @return string A random filename
	*/
	public function getRandomFilename(string $extension = '') : string
	{
		$filename = App::randStr();
		if (!$extension) {
			return $filename;
		}

		return $this->addExtension($extension, $filename);
	}

	/**
	* Appends $append_str to $filename (before the extension)
	* @param string $filename The filename
	* @param string $append_str The text to append
	* @param string $dirname If true, will also include the dirname. If false, will strip it
	* @return string The filename with $append_str appended
	*/
	public function appendToFilename(string $filename, string $append_str, bool $dirname = true) : string
	{
		$ext = $this->getExtension($filename, true);
		$file = $this->getFilename($filename, $dirname);

		return $file . $append_str . $ext;
	}

	/**
	* Returns the extension of a file in lowercase. Eg: jpg
	* @param string $filename The filename
	* @param bool $include_dot If true will include the dot in the returned value. Eg: .jpg
	* @return string The extension
	*/
	public function getExtension(string $filename, bool $include_dot = false) : string
	{
		$pi = pathinfo($filename);
		if (empty($pi['extension'])) {
			return '';
		}

		$ext = strtolower($pi['extension']);

		if ($include_dot) {
			return '.' . $ext;
		}

		return $ext;
	}

	/**
	* Adds extension to filename
	* @param string $extension The extension
	* @param string $filename The filename to append the extension to, if any
	* @return string The filename + extension
	*/
	public function addExtension(string $extension, string $filename = '') : string
	{
		if (!$extension) {
			return $filename;
		}

		return $filename . '.' . $extension;
	}

	/**
	* Strips the extension of a filename
	* @param string $filename The filename
	* @return string The filename without the extension
	*/
	public function stripExtension(string $filename) : string
	{
		$pi = pathinfo($filename);

		if ($pi['dirname'] == '.') {
			return $pi['filename'];
		} else {
			return $pi['dirname'] . '/' . $pi['filename'];
		}
	}

	/**
	* Builds a path from an array.
	* @param array $elements The elements from which the path will be built. Eg: $elements=array('/var','www'); it will return /var/www
	* @return string The built path
	*/
	public function buildPath(array $elements) : string
	{
		if (!$elements) {
			return '';
		}

		$path = '';
		foreach ($elements as $element) {
			if (!$element) {
				continue;
			}

			$path.= App::sl($element);
		}

		return $path;
	}

	/**
	* Checks if a filename is inside a dir
	* @param string $dir The dir
	* @param string $filename The filename to check
	* @return bool True if $filename is inside $dir
	*/
	public function isInsideDir(string $dir, string $filename) : bool
	{
		if ($filename == $dir) {
			return false;
		}

		if (strpos($filename, $dir) === false) {
			return false;
		}

		return true;
	}

	/**
	* Returns the name of a subdir, generated from a file. Usually the first 4 chars
	* @param string $file The name of the file
	* @param bool $rawurlencode If true will call $rawurlencode
	* @param int The number of chars of the returned subdir
	* @return string
	*/
	public function getSubdir(string $file, bool $rawurlencode = false, int $chars = 4) : string
	{
		$name = substr($file, 0, $chars);
		$name = str_replace(['.'], [''], $name);
		$name = strtolower($name);

		if ($rawurlencode) {
			$name = rawurlencode($name);
		}

		return $name . '/';
	}

	/**
	* Returns the known extensions for images
	* @return array The known image extensions
	*/
	public function getImageExtensions() : array
	{
		return ['jpg', 'jpeg', 'gif', 'png', 'bmp', 'webp'];
	}

	/**
	* Determines if $filename if an image,based on extension
	* @param string $filename The filename
	* @return bool Returns true if $filename is an image, false otherwise
	*/
	public function isImage(string $filename): bool
	{
		return in_array($this->getExtension($filename), $this->getImageExtensions());
	}

	/**
	* Checks a filename for invalid characters. Throws a fatal error if it founds invalid chars.
	* @param string $filename The filename
	* @return $this
	*/
	public function checkForInvalidChars(string $filename)
	{
		if (strpos($filename, '../') !== false || strpos($filename, './') !== false || strpos($filename, '..\\') !== false || strpos($filename, '.\\') !== false) {
			throw new \Exception("Invalid filename! Filename {$filename} contains invalid characters!");
		}

		return $this;
	}

	/**
	* Check that the filname [file/folder] doesn't contain invalid chars. and is located in the right path. Throws a fatal error for an invalid filename
	* @param string $filename The filename
	* @param string $secure_dir The folder where $filename is supposed to be, if any
	* @return $this
	*/
	public function checkFilename(string $filename, string $secure_dir = '')
	{
		if (!$filename) {
			return $this;
		}

		$max_chars = 500;

		if (strlen(basename($filename)) > $max_chars) {
			throw new \Exception("Invalid filename! Filename {$filename} contains to many characters!");
		}

		$this->checkForInvalidChars($filename);

		if (!$secure_dir) {
			return $this;
		}

		$filename = realpath($filename);
		if (!$filename) {
			return $this;
		}

		//The filename must be inside the secure dir. If it's not it will be treated as an invalid file
		if (!$this->isInsideDir($secure_dir, $filename)) {
			throw new \Exception("Invalid filename! Filename {$filename} is not inside the secure dir: {$secure_dir}");
		}

		return $this;
	}

	/**
	* Reads the content of a file
	* @param string $filename
	* @param string $secure_dir The folder where $filename is supposed to be
	* @return string Returns the contents of the file, or false on error
	*/
	public function readFile(string $filename, string $secure_dir = '')
	{
		$this->app->plugins->run('fileReadFile', $filename, $secure_dir, $this);

		$this->checkFilename($filename, $secure_dir);

		return file_get_contents($filename);
	}

	/**
	* Writes a file
	* @param string $filename The name of the file to write
	* @param string $content The content to write
	* @param bool $append If true will append the data to the file rather than create the file
	* @param string $secure_dir The folder where $filename is supposed to be
	* @return bool Returns true on success or false on failure
	*/
	public function writeFile(string $filename, string $content, bool $append = false, string $secure_dir = '') : bool
	{
		$this->app->plugins->run('fileWriteFile', $filename, $content, $append, $secure_dir, $this);

		$this->checkFilename($filename, $secure_dir);

		$flags = 0;
		if ($append) {
			$flags = FILE_APPEND;
		}

		return file_put_contents($filename, $content, $flags);
	}

	/**
	* Deletes a file
	* @param string filename The filename to delete
	* @param string $secure_dir The folder where $filename is supposed to be
	* @return bool Returns true on success or false on failure
	*/
	public function deleteFile(string $filename, string $secure_dir = '') : bool
	{
		$this->app->plugins->run('fileDeleteFile', $filename, $secure_dir, $this);

		$this->checkFilename($filename, $secure_dir);

		if (!is_file($filename)) {
			return true;
		}

		return unlink($filename);
	}

	/**
	* Copies a file
	* @param string $source The source file
	* @param string $destination The destination file
	* @param string $secure_dir The folder where $destination is supposed to be
	* @return bool Returns true on success or false on failure
	*/
	public function copyFile(string $source, string $destination, string $secure_dir = '') : bool
	{
		$this->app->plugins->run('fileCopyFile', $source, $destination, $secure_dir, $this);

		$this->checkFilename($source);
		$this->checkFilename($destination, $secure_dir);

		return copy($source, $destination);
	}

	/**
	* Moves a file
	* @param string $source The source file
	* @param string $destination The destination file
	* @param string $secure_dir The folder where $destination is supposed to be
	* @return bool Returns true on success or false on failure
	*/
	public function moveFile(string $source, string $destination, string $secure_dir = '') : bool
	{
		$this->app->plugins->run('fileMoveFile', $source, $destination, $secure_dir, $this);

		$this->checkFilename($source);
		$this->checkFilename($destination, $secure_dir);

		return rename($source, $destination);
	}

	/**
	* Lists the dirs/files from the specified folder
	* @param string $dir The folder to be searched
	* @param array $dirs Output array with all the found folders
	* @param array $files Output array with all the found files
	* @param bool $full_path If true it will set $dirs/$files to the absolute paths of the found folders/files,if false the relative paths
	* @param bool $recursive If true will enum. recursive
	* @param bool $include_extension If false,will strip the extension from the filename
	* @param array $skip_dirs Array of folders to exclude, if the listing is recursive
	* @param bool $use_dir_as_file_key If true, the $files array will have the dir name as a key
	* @param bool $is_tree If true, will return the $dirs as a tree
	* @param string $tree_prefix The tree's prefix, if $is_tree is true
	* @param int $tree_level [internal]
	* @param string $base_dir [internal]
	* @return bool Returns true on success or false on failure
	*/
	public function listDir(string $dir, ?array &$dirs, ?array &$files, bool $full_path = false, bool $recursive = false, bool $include_extension = true, array $skip_dirs = [], bool $use_dir_as_file_key = false, bool $is_tree = false, string $tree_prefix = '--', int $tree_level = 0, string $base_dir = '') : bool
	{
		$this->checkFilename($dir);

		$dir = App::sl($dir);

		if ($recursive && $skip_dirs) {
			if (in_array($dir, $skip_dirs)) {
				return true;
			}
		}

		if (!$base_dir) {
			$base_dir = $dir;
		}

		if ($dh = opendir($dir)) {
			$dirs_array = [];

			while (($file = readdir($dh)) !== false) {
				if ($file == '.' || $file == '..') {
					continue;
				}

				if (is_file($dir . $file)) {
					if ($is_tree) {
						continue;
					}

					if ($use_dir_as_file_key) {
						$files[$dir][] = $this->getListFileName($dir, $base_dir, $file, $full_path, $include_extension);
					} else {
						$files[] = $this->getListFileName($dir, $base_dir, $file, $full_path, $include_extension);
					}
				} else {
					$dirs_array[] = $dir . $file;
				}
			}

			foreach ($dirs_array as $dir_name) {
				if ($is_tree) {
					$key = $this->getListDirName($dir_name, $base_dir, $full_path);
					$dirs[$key] = $this->getListTreePrefix($tree_level, $tree_prefix) . basename($dir_name);
				} else {
					$dirs[] = $this->getListDirName($dir_name, $base_dir, $full_path);
				}

				if ($recursive) {
					$this->listDir($dir_name, $dirs, $files, $full_path, $recursive, $include_extension, $skip_dirs, $use_dir_as_file_key, $is_tree, $tree_prefix, $tree_level + 1, $base_dir);
				}
			}
		} else {
			$dirs = [];
			$files = [];

			return false;
		}

		return true;
	}

	/**
	* @internal
	*/
	protected function getListDirName(string $dir, string $base_dir, string $full_path) : string
	{
		if ($full_path) {
			return $dir;
		} else {
			return str_replace($base_dir, '', $dir);
		}
	}

	/**
	* @internal
	*/
	protected function getListFileName(string $dir, string $base_dir, string $file, bool $full_path, bool $include_extension) : string
	{
		if (!$include_extension) {
			$file = $this->getFilename($file);
		}

		if ($full_path) {
			return $dir . $file;
		} else {
			return str_replace($base_dir, '', $dir . $file);
		}
	}

	/**
	* @internal
	*/
	protected function getListTreePrefix(int $level, string $prefix) : string
	{
		$str = '';
		for ($i = 1; $i <= $level; $i++) {
			$str.= $prefix;
		}

		return $str;
	}

	/**
	* Create a folder.Does nothing if the folder already exists
	* @param string $dir The name of the folder to create
	* @return bool Returns true on success or false on failure
	*/
	public function createDir(string $dir) : bool
	{
		$this->app->plugins->run('fileCreateDir', $dir, $this);

		if (is_dir($dir)) {
			return true;
		}

		return mkdir($dir);
	}

	/**
	* Copies a dir
	* @param string $source_dir The source folder
	* @param string $destination_dir The destination folder
	* @param $recursive	If trye,will copy recursive
	* @return bool Returns true on success or false on failure
	*/
	public function copyDir(string $source_dir, string $destination_dir, bool $recursive = true) : bool
	{
		$this->app->plugins->run('fileCopyDir', $source_dir, $destination_dir, $recursive, $this);

		$this->checkFilename($source_dir);
		$this->checkFilename($destination_dir);

		$dirs = App::sl($source_dir);
		$dird = App::sl($destination_dir);

		$dh = opendir($dirs);
		if (!$dh) {
			return false;
		}

		while (($file = readdir($dh)) !== false) {
			if ($file == '.' || $file == '..') {
				continue;
			}

			if (is_dir($dirs . $file)) {
				if ($recursive) {
					if ($this->createDir($dird . $file)) {
						$this->copyDir($dirs . $file, $dird . $file, $recursive);
					}
				}
			} else {
				$this->copyFile($dirs . $file, $dird . $file);
			}
		}

		closedir($dh);

		return true;
	}

	/**
	* Moves a dir
	* @param string $source_dir The source folder
	* @param string $destination_dir The destination folder
	* @return bool Returns true on success or false on failure
	*/
	public function moveDir(string $source_dir, string $destination_dir) : bool
	{
		$this->app->plugins->run('fileMoveDir', $source_dir, $destination_dir, $this);

		$this->checkFilename($source_dir);
		$this->checkFilename($destination_dir);

		return rename($source_dir, $destination_dir);
	}

	/**
	* Deletes a folder
	* @param string $dir The name of the folder to delete
	* @param bool $recursive If true will delete recursively
	* @param string $secure_dir The folder where $dir is supposed to be
	* @return bool Returns true on success or false on failure
	*/
	public function deleteDir(string $dir, bool $recursive = true, string $secure_dir = '') : bool
	{
		$this->app->plugins->run('fileDeleteDir', $dir, $recursive, $secure_dir, $this);

		$this->checkFilename($dir, $secure_dir);

		$dir = App::sl($dir);

		$dh = opendir($dir);
		if (!$dh) {
			$this->error_code = 1;

			return false;
		}

		while (($file = readdir($dh)) !== false) {
			if ($file == '.' || $file == '..') {
				continue;
			}

			if (is_dir($dir . $file)) {
				if ($recursive) {
					if (!$this->deleteDir($dir . $file, $recursive)) {
						break;
					}
				}
			} else {
				if (!$this->deleteFile($dir . $file)) {
					break;
				}
			}
		}

		closedir($dh);

		if (!rmdir($dir)) {
			$this->error_code = 2;

			return false;
		}

		return true;
	}

	/**
	* Deletes all the files/subdirectories from a directory but does not delete the folder itself
	* @param string $dir The name of the folder to clear
	* @param bool $recursive If true will clear recursively
	* @param string $secure_dir The folder where $dir is supposed to be
	* @return bool Returns true on success or false on failure
	*/
	public function cleanDir(string $dir, bool $recursive = true, string $secure_dir = '') : bool
	{
		$this->app->plugins->run('fileCleanDir', $dir, $recursive, $secure_dir, $this);

		$this->checkFilename($dir, $secure_dir);

		$dir = App::sl($dir);

		$dh = opendir($dir);
		if (!$dh) {
			return false;
		}

		while (($file = readdir($dh)) !== false) {
			if ($file == '.' || $file == '..') {
				continue;
			}

			if (is_dir($dir . $file)) {
				if ($recursive) {
					if (!$this->deleteDir($dir . $file, $recursive)) {
						break;
					}
				}
			} else {
				if (!$this->deleteFile($dir . $file)) {
					break;
				}
			}
		}

		closedir($dh);

		return true;
	}

	/**
	* Returns the mime type based on extension
	* @param string $extension The extension
	* @return string The mime type of $extension
	*/
	public function getMimeType(string $extension) : string
	{
		$file_browser = new Helpers\FileBrowser;
		$type = $file_browser->getMimeType($extension);

		return $type;
	}

	/**
	* Outputs a file for download. Notice: It doesn't call die after it outputs the content,it is the caller's job to do it
	* @param string $filename The filename on the disk to output
	* @param string $output_name The name under which the user will be prompted to save the file
	* @return bool Returns true on success or false on failure
	*/
	public function promptForDownload(string $filename, string $output_name = '') : bool
	{
		$f = fopen($filename, 'r');
		if ($f === false) {
			return false;
		}

		$size = filesize($filename);
		if (!$output_name) {
			$output_name = basename($filename);
		}

		$ext = $this->getExtension($filename);
		$type = $this->getMimeType($ext);

		header('Content-Type: ' . $type);
		header('Content-Length: ' . $size);
		header('Content-Disposition: attachment; filename="' . str_replace(['"', '/'], ['\\"'], $output_name) . '"');

		while ($data = fread($f, 4096)) {
			echo $data;
		}

		fclose($f);

		return true;
	}

	/**
	* Outputs content for download.It doesn't call die after it outputs the content,it is the caller's job to do it
	* @param string $file The name of the file under which the user is prompted to download the content
	* @param string $content The content to be outputed
	*/
	public function outputForDownload(string $file, string $content)
	{
		$ext = $this->getExtension($file);
		$type = $this->getMimeType($ext);
		$file = str_replace(['"', '/'], ['\\"'], $file);
		$file = basename($file);

		header('Content-type: ' . $type);
		header('Content-Length: ' . strlen($content));
		header('Content-Disposition: attachment; filename="' . $file . '"');

		echo $content;
	}
}
