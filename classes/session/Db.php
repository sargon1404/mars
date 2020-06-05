<?php
/**
* The Db Session Class
* @package Mars
*/

namespace Mars\Session;

use Mars\App;

/**
* The Db Session Class
* Session driver which uses the database
*/
class Db implements DriverInterface, \SessionHandlerInterface, \SessionUpdateTimestampHandlerInterface
{
	use \Mars\AppTrait;

	/**
	* @var string $table The table where the sessions are stored
	*/
	protected string $table = '';

	/**
	* Builds the Db Session driver
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		$this->table = $this->app->config->session_table;

		if (!$this->table) {
			throw new \Exception('The database table where the session data is stored is not set');
		}

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
	* @param string $id The session's id
	*/
	public function read($id)
	{
		$data = $this->app->db->selectResult($this->table, 'data', ['id' => $id]);
		if (!$data) {
			return '';
		}

		return $data;
	}

	/**
	* Writes the session data
	* @see \SessionHandler::write()
	* @param string $id The session id
	* @param string $data The data
	*/
	public function write($id, $data)
	{
		$values = [
			'id' => $id,
			'timestamp' => time(),
			'data' => $data
		];

		$this->app->db->replace($this->table, $values);

		return true;
	}

	/**
	* Destroy the session data
	* @see \SessionHandler::destroy()
	* @param string $id The session id
	*/
	public function destroy($id)
	{
		$this->app->db->deleteById($this->table, $id);

		return true;
	}

	/**
	* Deletes expired sessions
	* @see \SessionHandler::gc()
	* @param int $maxlifetime The max lifetime
	*/
	public function gc($maxlifetime)
	{
		$cutoff = time() - $maxlifetime;

		$this->app->writeQuery("DELETE FROM {$this->table} WHERE `timestamp` < {$cutoff}");

		return true;
	}

	/**
	* Checks if a session identifier already exists or not
	* @see \SessionUpdateTimestampHandlerInterface::valideId()
	* @param string $id The session id
	*/
	public function validateId($id)
	{
		return $this->app->db->exists($this->table, ['id' => $id], 'id');
	}

	/**
	* Updates the timestamp of a session when its data didn't change
	* @see \SessionUpdateTimestampHandlerInterface::updateTimestamp()
	* @param string $id The session id
	* @param string $data The data
	*/
	public function updateTimestamp($id, $data)
	{
		$this->app->db->update($this->table, ['timestamp' => time()], ['id' => $id]);

		return true;
	}
}
