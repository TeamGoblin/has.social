<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class currentUser extends User implements JsonSerializable {

	private $jwt; // JSON Web Token
	private $logged; // Whether the account is logged in or not
	
	/**
	 * Explicitly set class variables 
	 * @param $var string - variable name
	 * @param $val mixed - variable value
	 * @return bool - true if value was set, false if value was not set
	 */
	public function set($var, $val) {
		$return = false;
		if (property_exists('currentUser', $var)) {
			// Validate
			switch ($var) {
				case 'id': // Integer
					if (filter_var($val, FILTER_VALIDATE_INT) !== FALSE) {
						$this->{$var} = $val;
					}
					break;

				case 'name': // String
					$val = preg_replace("/[^A-Za-z'.\- ]/", '', $val);
					$this->{$var} = $val;
					break;

				case 'password': // String no sanitize
				case 'newpassword': // String no sanitize
				case 'loginpassword': // String no sanitize
					$this->{$var} = $val;
					break;

				case 'email': // Email
					if (filter_var($val, FILTER_VALIDATE_EMAIL) !== FALSE) {
						$this->{$var} = $val;
					}
					break;

				case 'key': // varchar
				case 'jwt': // varchar
					$this->{$var} = $val;
					break;

				case 'created': // Date
				case 'modified': // Date
					$this->{$var} = $val;
					break;
				
				case 'logged': // boolean
				case 'active': // boolean
					$val = filter_var($val, FILTER_VALIDATE_BOOLEAN);
					$this->{$var} = $val;
					break;

				case 'all':
					$this->id = NULL;
					$this->name = NULL;
					$this->email = NULL;
					$this->password = NULL;
					$this->newpassword = NULL;
					$this->loginpassword = NULL;
					$this->active = FALSE;
					$this->key = NULL;
					$this->jwt = NULL;
					$this->created = NULL;
					$this->modified = NULL;
					$this->logged = FALSE;
					break;
			}

			$return = true;
		}
		return $return;
	}

	/**
	 * Get class variables 
	 * @param $var string - class variable name to return value for
	 * @return mixed - whatever class variable was requested's value or false if not found
	 */
	public function get($var) {
		$return = false;
		if (property_exists('currentUser', $var)) {
			$return = $this->{$var};
		} 
		return $return;
	}

	/** 
	 * Load User variables
	 * @return null
	 */
	public function load(){
		
		global $_cache;
		
		if (!empty($_cache->get('currentUser_'.$this->id))) {
			
			$tmpUser = $_cache->get('currentUser_'.$this->id);
			$this->name = $tmpUser->get('name');
			$this->email = $tmpUser->get('email');
			$this->password = $tmpUser->get('password');
			$this->created = $tmpUser->get('created');
			$this->modified = $tmpUser->get('modified');
			$this->active = ($tmpUser->get('active') == 1) ? true : false;
			$this->logged = $tmpUser->get('logged');
			$this->key = $tmpUser->get('key');
			$this->jwt = $tmpUser->get('jwt');
			
		} else {

			$row = $this->_db->table('users')
                ->select(array('name', 'email', 'password', 'active', 'key', 'created', 'modified'))
                ->where('users.id', '=', $this->id)
                ->first();

            if (!empty($row)) {
                $this->name = $row->name;
                $this->email = $row->email;
                $this->password = $row->password;
                $this->active = ($row->active == 1) ? true : false;
                $this->key = $row->key;
                $this->created = $row->created;
                $this->modified = $row->modified;
                $this->logged = TRUE;
                if (!empty($_SESSION['_jet_jwt'])) {
                	$this->jwt = $_SESSION['_jet_jwt'];	
                }
            }
        	$_cache->set($this);
		}
	}

	/**
	 * Reset user JWT
	 * @return null
	 */
	public function resetJWT() {
		global $_jwt;

		$payload = [
		    'iss' => $_ENV['CLIENT_NAME'],
		    'aud' => $_ENV['APP_NAME'],
		    'iat' => time(),
		    'exp' => time() + (24*60), // valid for 24 hours
		    'nbf' => time() - 15,
		    'sub' => $this->id
		];

		$jwt = JWT::encode($payload, $_jwt['private_key'], 'ES384');
		$this->jwt = $jwt;
	}

	/**
	 * Logs the user in
	 * @return bool - true if successful, false if not successful
	 */
	public function login($email, $pass) {
		global $_cache;
		$this->set('loginpassword', $pass);
		$return = false;
		$row = $this->_db->table('users')->where('users.email', '=', $email)->where('users.active', '=', TRUE)->first();
		if (!empty($row)) {
			if ($this->passcheck($row->password)) {
				$this->set('loginpassword', NULL);
				$this->id = $row->id;
				$this->load();
				$this->resetKey();
				$this->resetJWT();
				$this->logged = TRUE;
				$_cache->set($this);

				$_SESSION['_jet_id'] = $this->get('id');
				$_SESSION['_jet_pilot'] = $this->get('email');
				$_SESSION['_jet_key'] = $this->get('key');
				$_SESSION['_jet_jwt'] = $this->get('jwt');
				//$_SESSION['_jet_access'] = $this->get('access');
				$return = true;
			}
		}
		return $return;
	}

	/**
	 * Log user out
	 * @return true
	 */
	public function logout() {
		/* clear cookie and session data */
		$_SESSION['_jet_id'] = '';
		$_SESSION['_jet_pilot'] = '';
		$_SESSION['_jet_key'] = '';
		$_SESSION['_jet_jwt'] = '';
		//$_SESSION['_jet_access'] = '';
		$_SESSION = array();
		session_destroy();

		$this->set('all', NULL);
		return true;
	}

	/**
	 * Allow json decode and encode of User
	 * @return array - list of object variable values
	 */
	#[\ReturnTypeWillChange]
	public function jsonSerialize() {
		return [
			'id' => $this->id,
			'name' => $this->name,
			'email' => $this->email,
			'active' => $this->active,
			'key' => $this->key,
			'jwt' => $this->jwt,
			'created' => $this->created,
			'modified' => $this->modified,
			'logged' => $this->logged,
			'keep' => $this->keep
		];
	}
}