<?php
/**
* The Memcache Session Class
* @package Mars
*/

namespace Mars\Session;

use Mars\App;

/**
* The Memcache Session Class
* Session driver which uses the memcache
*/
class Memcache implements DriverInterface, \SessionHandlerInterface, \SessionUpdateTimestampHandlerInterface
{
	use \Mars\AppTrait;

	/**
	* @var int $lifetime The session's lifetime
	*/
	protected int $lifetime = 0;

	/**
	* Builds the Memcache Session driver
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		if (!$this->app->memcache->isEnabled()) {
			throw new \Exception('Memcache must be enable to be able to use the session memcache driver');
		}

		$this->lifetime = ini_get('session.gc_maxlifetime');

		session_set_save_handler($this);
	}

	/**
	* Initialize the session
	* @see \SessionHandler::open()
	* @param string $save_path The save path
	* @param string $session_name The session name
	*/
	public function open($save_path, $session_name)
	{
		return true;
	}

	/**
	* Closes the session
	* @see \SessionHandler::close()
	*/
	public function close()
	{
		return true;
	}

	/**
	* Reads the session data
	* @see \SessionHandler::read()
	* @param string $sid The session id
	*/
	public function read($sid)
	{
		$data = $this->app->memcache->get("session-{$sid}");
		if (!$data) {
			return '';
		}

		return $data;
	}

	/**
	* Writes the session data
	* @see \SessionHandler::write()
	* @param string $sid The session id
	* @param string $data The data
	*/
	public function write($sid, $data)
	{
		$this->app->memcache->set("session-{$sid}", $data, false, '', $this->lifetime);

		return true;
	}

	/**
	* Destroy the session data
	* @see \SessionHandler::destroy()
	* @param string $sid The session id
	*/
	public function destroy($sid)
	{
		$this->app->memcache->delete("session-{$sid}");

		return true;
	}

	/**
	* Deletes expired sessions
	* @see \SessionHandler::gc()
	* @param int $maxlifetime The max lifetime
	*/
	public function gc($maxlifetime)
	{
		return true;
	}

	/**
	* Checks if a session identifier already exists or not
	* @see \SessionUpdateTimestampHandlerInterface::valideId()
	* @param string $sid The session id
	*/
	public function validateId($sid)
	{
		return $this->app->memcache->exists("session-{$sid}");
	}

	/**
	* Updates the timestamp of a session when its data didn't change
	* @see \SessionUpdateTimestampHandlerInterface::updateTimestamp()
	* @param string $sid The session id
	* @param string $data The data
	*/
	public function updateTimestamp($sid, $data)
	{
		$this->write($sid, $data);

		return true;
	}
}
