<?php
/**
* The Dir Class
* @package Mars
*/

namespace Mars;

/**
* The Dir Class
* Folder Filesystem functionality
*/
class Dir
{
	use AppTrait;

	/**
	* Check that the filname [file/folder] doesn't contain invalid chars. and is located in the right path. Throws a fatal error for an invalid filename
	* @see \Mars\File::checkFilename()
	*/
	public function checkFilename(string $filename, string $secure_dir = '')
	{
		return $this->app->file->checkFilename($filename, $secure_dir);
	}

	/**
	* Builds a path from an array.
	* @see \Mars\File::buildPath()
	*/
	public function buildPath(array $elements) : string
	{
		return $this->app->file->buildPath($elements);
	}

	/**
	* Checks if a filename is inside a dir
	* @param string $dir The dir
	* @param string $filename The filename to check
	* @return bool True if $filename is inside $dir
	*/
	public function contains(string $dir, string $filename) : bool
	{
		if ($filename == $dir) {
			return false;
		}

		if (!str_contains($filename, $dir)) {
			return false;
		}

		return true;
	}

	/**
	* Returns the files from the specified folder
	* @param string $dir The folder to be searched
	* @param bool $recursive If true will enum. recursive
	* @param bool $full_path If true it will set will return the file's full path
	* @param array $skip_dirs Array of folders to exclude, if the listing is recursive
	* @param bool $use_dir_as_file_key If true, the $files array will have the dir name as a key
	* @param string $base_dir [internal]
	* @return array The files
	*/
	public function getFiles(string $dir, bool $recursive = false, bool $full_path = true, array $skip_dirs = [], bool $use_dir_as_file_key = false, string $base_dir = '') : array
	{
		$this->checkFilename($dir);

		$dir = App::sl($dir);

		if ($recursive && $skip_dirs) {
			if (in_array($dir, $skip_dirs)) {
				return [];
			}
		}

		if (!$base_dir) {
			$base_dir = $dir;
		}

		$dh = opendir($dir);
		if (!$dh) {
			return [];
		}

		$files = [];

		while (($file = readdir($dh)) !== false) {
			if ($file == '.' || $file == '..') {
				continue;
			}

			if (is_file($dir . $file)) {
				if ($use_dir_as_file_key) {
					$files[$dir][] = $this->formatFilename($dir, $base_dir, $file, $full_path);
				} else {
					$files[] = $this->formatFilename($dir, $base_dir, $file, $full_path);
				}
			} elseif ($recursive) {
				$files = array_merge($files, $this->getFiles($dir . $file, $recursive, $full_path, $skip_dirs, $use_dir_as_file_key, $base_dir));
			}
		}
		closedir($dh);

		return $files;
	}

	/**
	* @internal
	*/
	protected function formatFilename(string $dir, string $base_dir, string $file, bool $full_path) : string
	{
		if ($full_path) {
			return $dir . $file;
		} else {
			return str_replace($base_dir, '', $dir . $file);
		}
	}

	/**
	* Returns the files from the specified folder in a tree format
	* @param string $dir The folder to be searched
	* @param bool $full_path If true it will set will return the file's full path
	* @param array $skip_dirs Array of folders to exclude, if the listing is recursive
	* @param string $tree_prefix The tree's prefix
	* @param string $tree_level [internal]
	* @param string $base_dir [internal]
	* @return array The files
	*/
	public function getFilesTree(string $dir, bool $full_path = true, array $skip_dirs = [], string $tree_prefix = '--', int $tree_level = 0, string $base_dir = '') : array
	{
		$this->checkFilename($dir);

		$dir = App::sl($dir);

		if (in_array($dir, $skip_dirs)) {
			return [];
		}

		if (!$base_dir) {
			$base_dir = $dir;
		}

		$dh = opendir($dir);
		if (!$dh) {
			return [];
		}

		$files = [];

		while (($file = readdir($dh)) !== false) {
			if ($file == '.' || $file == '..') {
				continue;
			}

			if (is_file($dir . $file)) {
				$key = $this->formatFilename($dir, $base_dir, $file, $full_path);

				if ($full_path) {
					$files[$key] = $this->getFilesTreePrefix($tree_level, $tree_prefix) . $this->formatFilename($dir, $base_dir, $file, false);
				} else {
					$files[$key] = $this->getFilesTreePrefix($tree_level, $tree_prefix) . $key;
				}
			} else {
				$files = array_merge($files, $this->getFilesTree($dir . $file, $full_path, $skip_dirs, $tree_prefix, $tree_level + 1, $base_dir));
			}
		}
		closedir($dh);

		return $files;
	}

	/**
	* @internal
	*/
	protected function getFilesTreePrefix(int $level, string $prefix) : string
	{
		return str_repeat($prefix, $level);
	}

	/**
	* Create a folder. Does nothing if the folder already exists
	* @param string $dir The name of the folder to create
	* @return bool Returns true on success or false on failure
	*/
	public function create(string $dir) : bool
	{
		$this->app->plugins->run('dir_create', $dir, $this);

		$this->checkFilename($dir);

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
	public function copy(string $source_dir, string $destination_dir, bool $recursive = true) : bool
	{
		$this->app->plugins->run('dir_copy', $source_dir, $destination_dir, $recursive, $this);

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
					if ($this->create($dird . $file)) {
						$this->copy($dirs . $file, $dird . $file, $recursive);
					}
				}
			} else {
				$this->file->copy($dirs . $file, $dird . $file);
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
	public function move(string $source_dir, string $destination_dir) : bool
	{
		$this->app->plugins->run('dir_move', $source_dir, $destination_dir, $this);

		$this->checkFilename($source_dir);
		$this->checkFilename($destination_dir);

		return rename($source_dir, $destination_dir);
	}

	/**
	* Deletes a dir
	* @param string $dir The name of the folder to delete
	* @param bool $recursive If true will delete recursively
	* @param bool $delete_dir If true, will delete the dir itself; if false, will clean it
	* @param string $secure_dir The folder where $dir is supposed to be
	* @return bool Returns true on success or false on failure
	*/
	public function delete(string $dir, bool $recursive = true, bool $delete_dir = true, string $secure_dir = '') : bool
	{
		$this->app->plugins->run('dir_delete', $dir, $recursive, $delete_dir, $secure_dir, $this);

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
					if (!$this->delete($dir . $file, $recursive)) {
						break;
					}
				}
			} else {
				if (!$this->app->file->delete($dir . $file)) {
					break;
				}
			}
		}

		closedir($dh);

		if ($delete_dir) {
			if (!rmdir($dir)) {
				return false;
			}
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
	public function clean(string $dir, bool $recursive = true, string $secure_dir = '') : bool
	{
		$this->app->plugins->run('dir_clean', $dir, $recursive, $secure_dir, $this);

		return $this->delete($dir, $recursive, false, $secure_dir);
	}
}
