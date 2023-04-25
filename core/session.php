<?php

class Session implements SessionHandlerInterface {
	
	private $_db; // Class scope db
	
	/**
	* Construct method
	*
	* @param $arg array - Values used to create object
	* @return null
	*/
	public function __construct() {
		global $_db; // database
		$this->_db = $_db;
	}

	/* Action performed when a session is opened */
	#[\ReturnTypeWillChange]
	public function open($save_path, $name) {
		return true;
	}

	/* Action performed when a session is closed */
	#[\ReturnTypeWillChange]
	public function close() {
		return true;
	}

	/* Action performed when a sessions is read */
	#[\ReturnTypeWillChange]
	public function read($session_id) {
		$return = '';
		$row = $this->_db->table('sessions')->select(array('data'))->where('sessions.id', '=', $session_id)->first();
		if (!empty($row)) {
			$return  = $row->data;
		}
		return $return;
	}

	/* Action performed when a session is saved */
	#[\ReturnTypeWillChange]
	public function write($session_id, $session_data) {
		$return = false;
		$result = $this->_db->table('sessions')->where('sessions.id', '=', $session_id)->get();
		$count = count($result);
		if ($count > 0) {
			// Update
			$data = [
			    'data' => $session_data,
			    'modified' => $this->_db->raw('now()')
			];
			if ($this->_db->table('sessions')->where('id', $session_id)->update($data)) {
				$return = true;
			}
		} else {
			// Insert
			$data = [
				'id' => $session_id,
			    'data' => $session_data,
			    'created' => $this->_db->raw('now()'),
			    'modified' => $this->_db->raw('now()')
			];
			if ($this->_db->table('sessions')->where('id', $session_id)->insert($data)) {
				$return = true;
			}
		}
        return true;
	}

	/* Action performed when a session is deleted */
	#[\ReturnTypeWillChange]
	public function destroy($session_id) {
		$return = false;
		if($this->_db->table('sessions')->where('id', $session_id)->delete()){
			$return = true;
		}
        return true;
	}

	/* Action performed when session garbage collection occurs */
	#[\ReturnTypeWillChange]
	public function gc($maxlifetime) {
		$this->_db->query("DELETE FROM sessions WHERE (modified + '$maxlifetime seconds') < now()");
		return true;
	}
}

/* Set the save handlers */
$sessionHandler = new Session();

session_set_save_handler($sessionHandler);
register_shutdown_function('session_write_close');

/* Start the session */
session_start();