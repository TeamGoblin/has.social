<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class User implements JsonSerializable {

	protected $_db; // Class scope db
	protected $id; // User id
	protected $name; // First Name
	protected $email; // Email address
	protected $password; // Password
	protected $newpassword; // Whether or not this is a new password
	protected $loginpassword; // Password Attempt to login
	protected $active; // Whether account is active
	protected $key; // Used for changing password / account verification if needed
	protected $created; // Account creation time
	protected $modified; // Account last modified time

	/**
	* Construct method
	*
	* @param $arg array - Values used to create object
	* @return null
	*/
	public function __construct($arg = array()) {
		global $_db; // database
		$this->_db = $_db;
		if (isset($arg['id'])) {
			$this->set('id', $arg['id']);
			$this->load();
		}
	}

	/**
	 * Explicitly set class variables 
	 * @param $var string - variable name
	 * @param $val mixed - variable value
	 * @return bool - true if value was set, false if value was not set
	 */
	public function set($var, $val) {
		$return = false;
		if (property_exists('User', $var)) {
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
					$this->{$var} = $val;
					break;

				case 'created': // Date
				case 'modified': // Date
					$this->{$var} = $val;
					break;
				
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
					$this->created = NULL;
					$this->modified = NULL;
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
		if (property_exists('User', $var)) {
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
		
		if (!empty($_cache->get('User_'.$this->id))) {
			
			$tmpUser = $_cache->get('User_'.$this->id);
			$this->name = $tmpUser->get('name');
			$this->email = $tmpUser->get('email');
			$this->password = $tmpUser->get('password');
			$this->created = $tmpUser->get('created');
			$this->modified = $tmpUser->get('modified');
			$this->active = ($tmpUser->get('active') == 1) ? true : false;
			$this->key = $tmpUser->get('key');
			
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
            }

        	$_cache->set($this);
		}
		
	}

    /**
     * Create New User
     * @return bool - true if saved, false if did not save
     */
	private function create() {
		$return = false;
        $hash = password_hash($this->password, PASSWORD_BCRYPT);
        $access_token = md5(JET::randomString() . $this->email);

        // Check for existing user
        $row = $this->_db->table('users')
            ->where('email', '=', $this->email)
            ->first();

        // If no existing user create new
        if (empty($row)) {

            $data = [
            	'id' => $this->_db->raw('default'),
                'name' => $this->name,
                'email' => $this->email,
                'password' => $hash,
                'created' => $this->_db->raw('now()'),
                'modified' => $this->_db->raw('now()'),
                'active' => $this->_db->raw('TRUE')
            ];

            $userInsertID = $this->_db->table('users')->insert($data);

            $this->id = $userInsertID;
            $this->load();
            $return = true;

        } else { // User already exists
            Notification::t('error');
            Notification::m('Failed to create new user.');
        }
        return $return;
    }

    /**
     * Update User
     * @return bool - true if saved, false if did not save
     */
    private function update() {
    	$return = false;
        // Check new password or not
        if (!empty($this->newpassword)) {
            $hash = password_hash($this->newpassword, PASSWORD_BCRYPT);

            if (!empty($this->id)) {
                $data = [
                    'email' => $this->email,
                    'password' => $hash,
                    'active' => $this->active,
                    'modified' => $this->_db->raw('now()')
                ];

                if ($this->_db->table('users')->where('id', $this->id)->update($data)) {
                    $return = true;
                }
            }
        } else {
            if (!empty($this->id)) {
                $data = [
                    'email' => $this->email,
                    'active' => $this->active,
                    'modified' => $this->_db->raw('now()')
                ];

                if ($this->_db->table('users')->where('id', $this->id)->update($data)) {
                    $return = true;
                }
            }
        }
        if (!$return) {
            Notification::t('error');
            Notification::m("Failed to update user into database");
        }
        return $return;
    }

	/**
	 * Save User to DB
	 * @return bool - true if saved, false if did not save
	 */
	public function save() {
		$return = false;
		// Check update or new
		if (empty($this->id)) { // New
			$return = $this->create();
		} else { // Update
			$return = $this->update();
		}
		return $return;
	}

	public function delete() {
		$return = false;
		if ($this->_db->table('users')->where('id', $this->id)->delete()) {
			$return = true;
		} else {
			Notification::t('error');
			Notification::m("Failed to delete user with ID: $this->id");
		}			
		return $return;
	}

	protected function saveKey() {
		$return = false;
		$data = [
		    'key' => $this->key,
		    'modified' => $this->_db->raw('now()')
		];
		if ($this->_db->table('users')->where('id', $this->id)->update($data)) {
			$return = true;
		} else {
			Notification::t('error');
			Notification::m("Failed to save key for user with ID: $this->id");
		}
		return $return;
	}

	/**
	 * Save user password only
	 * @return bool - true if saved, false if did not save
	 */
	public function savePassword() {
		$return = false;
		
		if ($this->userCheck()) {
			// Check to see that the password matches the user id
			if (!empty($this->newpassword)) {
				$hash = password_hash($this->newpassword, PASSWORD_BCRYPT);
				$data = [
			    	'password' => $hash,
			    	'modified' => $this->_db->raw('now()')
				];
				if ($this->_db->table('users')->where('id', $this->id)->update($data)) {
					$return = true;
				} else {
					Notification::t('error');
					Notification::m("Failed to update password for user with ID: $this->id");
				}
			}
		}
	
		return $return;
	}

	/**
	 * Reset user password
	 * @return null
	 */
	public function resetPassword() {
		$password = JET::randomString();
		$this->password = $password;
		$this->save();
	}

	/**
	 * Reset user key 
	 * @return null
	 */
	public function resetKey() {
		$key = md5(JET::randomString() . $this->email);
		$this->key = $key;
		$this->saveKey();
	}

	/**
	 * Check user password
	 * @param $hash string - password hash from bcrypt
	 * @return bool - true if password matches hash, false if it doesn't match
	 */
	public function passcheck($hash) {
		return password_verify($this->loginpassword, $hash);
	}

	/**
	 * User check - verify userID matches given KEY
	 * @return bool - true if key matches for given user, false otherwise
	 */
	public function check($key) {
		$return = false;
		
		if ($key == $this->key) {
			$return = true;
		}
		
		return $return;
	}

	/**
	 * Check key for user
	 * @return mixed - false if it fails, user ID if successful
	 */
	public function keyCheck($key) {
		$return = false;

		$row = $this->_db->table('users')->select(['id'])->where('users.key', '=', $key)->first();
		
		if (!empty($row)) {
			$return = $row->id;
		}
		
		return $return;
	}

	/**
	 * Check email address and return key
	 * @return mixed - false if it fails, user key if successful
	 */
	public function emailCheck($email) {
		$return = false;
		
		$row = $this->_db->table('users')->select(['key'])->where('users.email', '=', $email)->first();
		
		if (!empty($row)) {
			$return = $row->key;
		}
		
		return $return;
	}

	/**
	 * User check - verify userID matches given KEY
	 * @return bool - true if key matches for given user, false otherwise
	 */
	public function userCheck() {
		$return = false;

		$row = $this->_db->table('users')->select(['key'])->where('users.id', '=', $this->id)->first();
		if (!empty($row)) {
			if ($this->key == $row->key) {
				$return = true;
			}
		}
		return $return;
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
			'created' => $this->created,
			'modified' => $this->modified,
		];
	}
}