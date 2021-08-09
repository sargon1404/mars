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
	* Returns the files from the specified folder
	* @param string $dir The folder to be searched
	* @param bool $recursive If true will enum. recursive
	* @param bool $full_path If true it will set will return the file's full path
	* @param array $skip_dirs Array of folders to exclude, if the listing is recursive
	* @param string $base_dir [internal]
	* @return bool Returns true on success or false on failure
	*/
	public function getFiles(string $dir, bool $recursive = false, bool $full_path = true, array $skip_dirs = [], string $base_dir = '') : ?array
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

		$dh = opendir($dirs);
		if (!$dh) {
			return [];
		}

		$files = [];

		while (($file = readdir($dh)) !== false) {
			if ($file == '.' || $file == '..') {
				continue;
			}

			if (is_file($dir . $file)) {
				$files[] = $this->formatFilename($dir, $base_dir, $file, $full_path);
			}
		}

		if ($recursive) {
			$files = array_merge($files, $this->getFiles($dir, $recursive, $full_path, $skip_dirs, $base_dir));
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
	public function listDir(string $dir, ?array &$dirs, ?array &$files, bool $full_path = false, bool $recursive = false, bool $include_extension = true,
	array $skip_dirs = [], bool $use_dir_as_file_key = false, bool $is_tree = false, string $tree_prefix = '--', int $tree_level = 0, string $base_dir = '') : bool
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
		$this->app->plugins->run('dir_delete', $dir, $recursive, $secure_dir, $this);

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
