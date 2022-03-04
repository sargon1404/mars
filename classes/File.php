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
	* If specified, will limit that can be accessed to folder $open_basedir
	*/
	protected string $open_basedir = '';

	/**
	* @var int $max_chars The maximum number of chars allowed in $filename
	*/
	protected int $max_chars = 300;

	/**
	* Constructs the file object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		if ($this->app->config->open_basedir === true) {
			$this->open_basedir = $this->app->path;
		} else {
			$this->open_basedir = $this->app->config->open_basedir;
		}
	}

	/**
	* Checks a filename for invalid characters. Throws a fatal error if it founds invalid chars.
	* @param string $filename The filename
	* @return static
	* @throws Exception if the filename contains invalid chars
	*/
	public function checkForInvalidChars(string $filename) : static
	{
		if (str_contains($filename, '../') || str_contains($filename, './')
		    || str_contains($filename, '..\\') || str_contains($filename, '.\\')
		    || str_starts_with($filename, strtolower('php:'))) {
			throw new \Exception("Invalid filename! Filename {$filename} contains invalid characters!");
		}

		return $this;
	}

	/**
	* Check that the filname [file/folder] doesn't contain invalid chars. and is located in the right path. Throws a fatal error for an invalid filename
	* @param string $filename The filename
	* @return static
	* @throws Exception if the filename is not valid
	*/
	public function checkFilename(string $filename) : static
	{
		if (!$filename) {
			return $this;
		}

		if (strlen(basename($filename)) > $this->max_chars) {
			throw new \Exception("Invalid filename! Filename {$filename} contains too many characters!");
		}

		$this->checkForInvalidChars($filename);

		if (!$this->open_basedir) {
			return $this;
		}

		$filename = realpath($filename);
		if (!$filename) {
			return $this;
		}

		//The filename must be inside the secure dir. If it's not it will be treated as an invalid file
		if (!$this->app->dir->contains($this->open_basedir, $filename)) {
			throw new \Exception("Invalid filename! Filename {$filename} is not inside the base dir: {$this->open_basedir}");
		}

		return $this;
	}

	/**
	* Returns the basename from $filename
	* @param string $filename The filename for which the basename will be returned
	* @return string The basename of filename
	*/
	public function basename(string $filename) : string
	{
		return basename($filename);
	}

	/**
	* Alias for getDirname
	* @see File::getDirname()
	*/
	public function dirname(string $filename) : string
	{
		return $this->getDirname($filename);
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

		return $dir . '/';
	}

	/**
	* Returns the relative path of a filename. Eg: /var/www/mars/dir/some_file.txt => dir/some_file.txt
	* @param string $filename The filename
	* @return string The relative path
	*/
	public function getRel(string $filename) : string
	{
		return str_replace($this->app->path, '', $filename);
	}

	/**
	* Returns the filename(strips the extension) of a file
	* @param string $filename The filename for which the filename will be returned
	* @return string The filename, without the extension
	*/
	public function getFilename(string $filename) : string
	{
		return pathinfo($filename, PATHINFO_FILENAME);
	}

	/**
	* Generates a random filename
	* @param string $extension The extension of the file, if any
	* @return string A random filename
	*/
	public function getRandomFilename(string $extension = '') : string
	{
		$filename = $this->app->random->getString();
		if (!$extension) {
			return $filename;
		}

		return $this->addExtension($extension, $filename);
	}

	/**
	* Appends $append_str to $filename (before the extension)
	* @param string $filename The filename
	* @param string $append The text to append
	* @return string The filename with $append_str appended
	*/
	public function appendToFilename(string $filename, string $append) : string
	{
		return $this->getFilename($filename) . $append . $this->getExtension($filename, true);
	}

	/**
	* Returns the extension of a file in lowercase. Eg: jpg
	* @param string $filename The filename
	* @param bool $include_dot If true will include the dot in the returned value. Eg: .jpg
	* @return string The extension
	*/
	public function getExtension(string $filename, bool $include_dot = false) : string
	{
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		if (!$ext) {
			return '';
		}

		$ext = strtolower($ext);

		if ($include_dot) {
			return '.' . $ext;
		}

		return $ext;
	}

	/**
	* Adds extension to filename
	* @param string $filename The filename to append the extension to
	* @param string $extension The extension
	* @return string The filename + extension
	*/
	public function addExtension(string $filename, string $extension) : string
	{
		if (!$extension) {
			return $filename;
		}

		return $filename . '.' . $extension;
	}

	/**
	* Builds a path from an array.
	* @param array $elements The elements from which the path will be built. Eg: $elements=array('/var','www'); it will return /var/www
	* @param bool $fix_path If true, will fix the path by adding a slash
	* @return string The built path
	*/
	public function buildPath(array $elements, bool $fix_path = false) : string
	{
		if (!$elements) {
			return '';
		}

		$elements = array_filter($elements);

		$path = '/' . implode('/', $elements);
		if ($fix_path) {
			$path = App::fixPath($path);
		}

		return $path;
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
	* Reads the content of a file
	* @param string $filename
	* @return string Returns the contents of the file
	* @throws Exception if the file can't be read
	*/
	public function read(string $filename) : string
	{
		$this->app->plugins->run('file_read', $filename, $this);

		$this->checkFilename($filename);

		$content = file_get_contents($filename);
		if ($content === false) {
			throw new \Exception("Unable to read file: {$filename}");
		}

		return $content;
	}

	/**
	* Writes a file
	* @param string $filename The name of the file to write
	* @param string $content The content to write
	* @param bool $append If true will append the data to the file rather than create the file
	* @return bool Returns the number of written bytes
	* @throws Exception if the file can't be written
	*/
	public function write(string $filename, string $content, bool $append = false) : int
	{
		$this->app->plugins->run('file_write', $filename, $content, $append, $this);

		$this->checkFilename($filename);

		$flags = 0;
		if ($append) {
			$flags = FILE_APPEND;
		}

		$bytes = file_put_contents($filename, $content, $flags);
		if ($bytes === false) {
			throw new \Exception("Unable to write file: {$filename}");
		}

		return $bytes;
	}

	/**
	* Deletes a file
	* @param string filename The filename to delete
	* @return static
	* @throws Exception if the file can't be deleted
	*/
	public function delete(string $filename) : static
	{
		$this->app->plugins->run('file_delete', $filename, $this);

		$this->checkFilename($filename);

		if (unlink($filename) === false) {
			throw new \Exception("Unable to delete file: {$filename}");
		}

		return $this;
	}

	/**
	* Copies a file
	* @param string $source The source file
	* @param string $destination The destination file
	* @return static
	* @throws Exception if the file can't be copied
	*/
	public function copy(string $source, string $destination) : static
	{
		$this->app->plugins->run('file_copy', $source, $destination, $this);

		$this->checkFilename($source);
		$this->checkFilename($destination);

		if (copy($source, $destination) === false) {
			throw new \Exception("Unable to copy file: {$source} to {$destination}");
		}

		return $this;
	}

	/**
	* Moves a file
	* @param string $source The source file
	* @param string $destination The destination file
	* @return static
	* @throws Exception if the file can't be moved
	*/
	public function move(string $source, string $destination) : static
	{
		$this->app->plugins->run('file_move', $source, $destination, $this);

		$this->checkFilename($source);
		$this->checkFilename($destination);

		if (rename($source, $destination) === false) {
			throw new \Exception("Unable to move file: {$source} to {$destination}");
		}

		return $this;
	}

	/**
	* Returns the mime type of a file
	* @param string $filename The filename
	* @return string The mime type of $extension
	*/
	public function getMimeType(string $filename) : string
	{
		return mime_content_type($filename);
	}

	/**
	* Outputs a file for download. Notice: It doesn't call die after it outputs the content,it is the caller's job to do it
	* @param string $filename The filename to output
	* @param string $output_name The name under which the user will be prompted to save the file
	* @throws Exception if the file can't be opened
	*/
	public function promptForDownload(string $filename, string $output_name = '')
	{
		$f = fopen($filename, 'r');
		if ($f === false) {
			throw new \Exception("Unable to open file: {$filename}");
		}

		$size = filesize($filename);
		if (!$output_name) {
			$output_name = basename($filename);
		}

		header('Content-Type: ' . $this->getMimeType($filename));
		header('Content-Length: ' . $size);
		header('Content-Disposition: attachment; filename="' . $this->app->filter->filename($output_name) . '"');

		while ($data = fread($f, 4096)) {
			echo $data;
		}

		fclose($f);
	}
}
