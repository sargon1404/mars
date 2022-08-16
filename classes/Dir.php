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
	public function checkFilename(string $filename)
	{
		return $this->app->file->checkFilename($filename);
	}

	/**
	* Builds a path from an array.
	* @see \Mars\File::buildPath()
	*/
	public function buildPath(array $elements) : string
	{
		return $this->app->file->buildPath($elements, true);
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
	* Returns the dirs from the specified folder
	* @param string $dir The folder to be searched
	* @param bool $recursive If true will enum. recursive
	* @param bool $full_path If true it will set will return the file's full path
	* @param array $exclude_dirs Array of dirs to exclude, if any
	* @return array The files
	*/
	public function getDirs(string $dir, bool $recursive = false, bool $full_path = true, array $exclude_dirs = []) : array
	{
		$this->checkFilename($dir);

		$iterator = $this->getIterator($dir, $recursive, $exclude_dirs);

		$dirs = [];
		foreach ($iterator as $file) {
			if ($file->isFile()) {
				continue;
			}

			$dirs[] = $this->getName($file, $full_path);
		}

		return $this->app->plugins->filter('dir_get_dirs', $dirs, $dir, $recursive, $full_path, $exclude_dirs, $this);
	}

	/**
	* Returns the files from the specified folder
	* @param string $dir The folder to be searched
	* @param bool $recursive If true will enum. recursive
	* @param bool $full_path If true it will set will return the file's full path
	* @param array $exclude_dirs Array of dirs to exclude, if any
	* @param array $extensions If specified, will return only the files matching the extensions
	* @return array The files
	*/
	public function getFiles(string $dir, bool $recursive = false, bool $full_path = true, array $exclude_dirs = [], array $extensions = []) : array
	{
		$this->checkFilename($dir);

		$iterator = $this->getIterator($dir, $recursive, $exclude_dirs);

		$files = [];
		foreach ($iterator as $file) {
			if ($file->isDir()) {
				continue;
			}
			if ($extensions) {
				if (!in_array($file->getExtension(), $extensions)) {
					continue;
				}
			}

			$files[] = $this->getName($file, $full_path);
		}

		return $this->app->plugins->filter('dir_get_files', $files, $dir, $recursive, $full_path, $exclude_dirs, $extensions, $this);
	}

	/**
	* Returns the iterator used to generate the files
	* @param string $dir The folder to be searched
	* @param bool $recursive If true will enum. recursive
	* @param array $exclude_dirs Array of dirs to exclude, if any
	* @param int $flag Flag to pass to \RecursiveIteratorIterator
	* @return Iterator The iterator
	*/
	public function getIterator(string $dir, bool $recursive = true, array $exclude_dirs = [], int $flag = \RecursiveIteratorIterator::SELF_FIRST) : \Iterator
	{
		$iterator = new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS | \RecursiveDirectoryIterator::CURRENT_AS_SELF);

		if ($exclude_dirs) {
			$iterator = new \RecursiveCallbackFilterIterator($iterator, function ($current, $key, $dir_iterator) use ($exclude_dirs) {
				if (in_array($dir_iterator->getSubPathname(), $exclude_dirs)) {
					return false;
				}

				return true;
			});
		}

		if ($recursive) {
			$iterator = new \RecursiveIteratorIterator($iterator, $flag);
		} else {
			$iterator = new \IteratorIterator($iterator);
		}

		return $this->app->plugins->filter('dir_get_iterator', $iterator, $dir, $recursive, $exclude_dirs, $flag, $this);
	}

	/**
	* @internal
	*/
	protected function getName($file, bool $full_path = false) : string
	{
		if ($full_path) {
			return $file->getPathname();
		} else {
			return $file->getSubPathname();
		}
	}

	/**
	* Create a folder. Does nothing if the folder already exists
	* @param string $dir The name of the folder to create
	* @throws Exception if the folder can't be created
	*/
	public function create(string $dir)
	{
		$this->app->plugins->run('dir_create', $dir, $this);

		$this->checkFilename($dir);

		if (is_dir($dir)) {
			return;
		}

		if (!mkdir($dir)) {
			throw new \Exception(App::__('dir_error_create', ['{DIR}' => $dir]));
		}
	}

	/**
	* Copies a dir
	* @param string $source The source folder
	* @param string $destination The destination folder
	* @throws Exception If folders can't be created/files can't be copied
	*/
	public function copy(string $source, string $destination)
	{
		$this->app->plugins->run('dir_copy', $source, $destination, $this);

		$this->checkFilename($source);
		$this->checkFilename($destination);

		$this->create($destination);

		$destination = App::fixPath($destination);

		$iterator = $this->getIterator($source);
		foreach ($iterator as $file) {
			$target_file = $destination . $this->getName($file);

			if ($file->isDir()) {
				$this->create($target_file);
			} else {
				$this->app->file->copy($file->getPathname(), $target_file);
			}
		}
	}

	/**
	* Moves a dir
	* @param string $source The source folder
	* @param string $destination The destination folder
	* @throws Exception if the dir can't be moved
	*/
	public function move(string $source, string $destination)
	{
		$this->app->plugins->run('dir_move', $source, $destination, $this);

		$this->checkFilename($source);
		$this->checkFilename($destination);

		if (!rename($source, $destination)) {
			throw new \Exception(App::__('dir_error_move', ['{SOURCE}' => $source, '{DESTINATION}' => $destination]));
		}
	}

	/**
	* Deletes a dir
	* @param string $dir The name of the folder to delete
	* @param bool $delete_dir If true, will delete the dir itself; if false, will clean it
	* @throws Exception if the dir can't be deleted
	*/
	public function delete(string $dir, bool $delete_dir = true)
	{
		$this->app->plugins->run('dir_delete', $dir, $delete_dir, $this);

		$this->checkFilename($dir);

		$iterator = $this->getIterator($dir, flag: \RecursiveIteratorIterator::CHILD_FIRST);
		foreach ($iterator as $file) {
			if ($file->isDir()) {
				if (!rmdir($file->getPathname())) {
					throw new \Exception(App::__('dir_error_delete', ['{DIR}' => $file->getPathname()]));
				}
			} else {
				if (!unlink($file->getPathname())) {
					throw new \Exception(App::__('file_error_delete', ['{FILE}' => $file->getPathname()]));
				}
			}
		}

		if ($delete_dir) {
			if (!rmdir($dir)) {
				throw new \Exception(App::__('dir_error_delete', ['{DIR}' => $dir]));
			}
		}

		return true;
	}

	/**
	* Deletes all the files/subdirectories from a directory but does not delete the folder itself
	* @param string $dir The name of the folder to clear
	* @throws Exception if the dir can't be cleaned
	*/
	public function clean(string $dir)
	{
		$this->app->plugins->run('dir_clean', $dir, $this);

		$this->delete($dir, false);
	}
}
