<?php

class UserWeb extends WEB {

	/* Set defaults for theme variables in constructor */
	public function __construct($pilot) {
		parent::__construct($pilot);
	}

	/* Login page */
	public function login() {
		$this->theme->setDir('user/login');
	}

	/* Logout page/action */
	public function logout() {
		$this->pilot->logout();
		session_destroy();

		/* Redirect to login */
		header('Location: /user/login');
		exit;
	}

	/* Login action */
	public function loginAction($params) {
		if ($this->pilot->login($params['email'], $params['password'])) {
			header('Location: /');
		} else {
			header('Location: /user/login');
		}
		exit;
	}

	/* List page */
	public function list() {
		global $_db;
		/* Get list of users */
		$uzers = array();
		$results = $_db->table('users')->get();
		foreach ($results as $r) {
			$uzers[] = new User(array('id' => $r->id));
		}
		$users = collect($uzers);

		/* Load user and allow update */
		$this->theme->setDir('users/list');
		$this->setVars(array('users' => $users));
		$this->theme->addJS('https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js');
		$this->theme->addJS('https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js');
		$this->theme->addJS('/js/list.js');
		$this->theme->addCSS('https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css');
	}

	/* New user page */
	public function new() {
		global $_db;
		$results = $_db->table('user_types')->get();
		$user_types = collect($results);
		$this->setVars(array('user_types' => $user_types));
		$this->theme->setDir('users/new');
	}

	/* New User action */
	public function newAction($params) {
                
		global $_db, $_n;
		
		$newUser = new User();
		$newUser->set('first_name', $params['first_name']);
		$newUser->set('last_name', $params['last_name']);
		$newUser->set('birth_date', $params['birth_date']);
		$newUser->set('gender', $params['gender']);
		$newUser->set('email', $params['email']);
		$newUser->set('password', $params['password']);
		$newUser->set('user_type_id', $params['user_type_id']);
		$r = $newUser->save();

		if ($r) {
			// User save worked
			Notification::t('success');
			/* @TODO Add link to new user page in this message */
			Notification::m($newUser->get('email') . ' added.');
			header('Location: /users');
		} else {
			header('Location: /users/new');
		}

	}

	/* Edit user page */
	public function edit($params) {
		global $_db;
		$results = $_db->table('user_types')->get();
		$user_types = collect($results);
		$tmpUser = new User(array('id' => $params['id']));
		$this->setVars(array('user_types' => $user_types, 'tmpUser' => $tmpUser));
		$this->theme->setDir('users/edit');
	}

	/* Edit User action */
	public function editAction($params) {
		
		global $_db;
		
		$newUser = new User(array('id' => $params['id']));
		$newUser->set('first_name', $params['first_name']);
		$newUser->set('last_name', $params['last_name']);
		$newUser->set('birth_date', $params['birth_date']);
		$newUser->set('gender', $params['gender']);
		$newUser->set('email', $params['email']);
		$newUser->set('newpassword', $params['newpassword']);
		$newUser->set('user_type_id', $params['user_type_id']);
		$r = $newUser->save();

		if ($r) {
			// User save worked
			Notification::t('success');
			/* @TODO Add link to new user page in this message */
			Notification::m($newUser->get('email') . ' saved');

			/* Load Users List page */
			header('Location: /users');
		} else {
			header('Location: /users/edit/'.$params['id']);
		}
	}

	/* Delete user page */
	public function delete($params) {
		global $_db;
		$results = $_db->table('user_types')->get();
		$user_types = collect($results);
		$tmpUser = new User(array('id' => $params['id']));
		$this->setVars(array('user_types' => $user_types, 'tmpUser' => $tmpUser));
		$this->theme->setDir('users/delete');
	}

	/* Delete user action */
	public function deleteAction($params) {
		global $_db;
		$tmpUser = new User(array('id' => $params['id']));
		$r = $tmpUser->delete();

		if ($r) {
			// User save worked
			Notification::t('success');
			/* @TODO Add link to new user page in this message */
			Notification::m($tmpUser->get('email') . ' deleted');
		}
		
		/* Load Users List page */
		header('Location: /users');
	}

	/* Forgot password user page */
	public function forgot($params=array()) {
		$this->theme->setDir('user/forgot');
	}
	/* Forgot password action */
	public function forgotAction($params=array()) {
		// Get key associated with email address

        $User = new User();
		$key = $User->emailCheck($params['email']);
		$pass = false;

		if ($key) {
		    /*
		     * @todo change this to use google imap
		     */
			// Email key to associated email address
			$name = ""; //senders name
			$from = ""; //senders e-mail address
			$mail_body = ""; //mail body
			$subject = ""; //subject
			$eol = "\n";
			$headers = "From: $name <$from>".$eol;
			$headers .= "Reply-To: $name <$from>".$eol;
			$headers .= "Return-Path: $name <$from>".$eol;
			$headers .= "Message-ID:<".time()." TheSystem@".$_SERVER['SERVER_NAME'].">".$eol;
			$headers .= "X-Mailer: PHP v".phpversion().$eol;
			$headers2 = "-f $from";
			mail($params['email'], $subject, $mail_body, $headers, $headers2); //mail command :)
			$pass = true;
		}
		
		$this->setVars(array('pass' => $pass));
		$this->theme->setDir('user/forgotAction');
	}

	/* Reset Password page */
	public function reset($params=array()) {
		$key = $params['key'];
		// Look up users by key
		$user = new User();
		$uid = $user->keyCheck($key);
		
		if (!empty($uid)) { // If user exists 
			$tmpUser = new User(array('id' => $uid));
			$this->setVars(array('tmpUser' => $tmpUser, 'key' => $key));
			$this->theme->setDir('user/reset');
		} else { // Else redirect
			header('Location: /user/login');
		}
	}
	
	/* Reset PW action */
	public function resetAction($params=array()) {
		$key = $params['key'];

		$this->pilot = new User(array('id' => $params['id']));
		if ($key == $this->pilot->get('key')) {
			$this->pilot->set('newpassword', $params['password']);
			$this->pilot->savepw();
			
			if ($this->pilot->login($this->pilot->get('email'), $params['password'])) {
				$_SESSION['_jet_id'] = $this->pilot->get('id');
				$_SESSION['_jet_pilot'] = $this->pilot->get('email');
				$_SESSION['_jet_key'] = md5(JET::randomString() . $this->pilot->get('email'));
				$_SESSION['_jet_access'] = $this->pilot->get('access');
				header('Location: /');
			} else {
				header('Location: /user/login');	
			}
		} else { // Else redirect
			header('Location: /user/login');
		}
	}
}