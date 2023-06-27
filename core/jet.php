<?php
class JET {
	
	public static function web($controller, $function, $params=array()){
		include_once(__DIR__.'/../engines/web/web.php');
		include_once(__DIR__.'/../engines/web/'.strtolower($controller).'.php');
		global $_FUEL;
		foreach ($_FUEL as $key => $val) {
			if (!array_key_exists($key, $params)) {
				$params[$key] = $val;
			}
		}

		/* Create User */		
		$id = 0;
		if (!empty($_SESSION['_jet_id'])) {
			$id = $_SESSION['_jet_id'];
		}
		$_user = new currentUser(array('id' => $id));
		
		$_controller = new $controller($_user);
		$_controller->$function($params);
		return $_controller;

	}
	
	public static function api($controller, $function, $params=array()){
		include_once(__DIR__.'/../engines/api/api.php');
		include_once(__DIR__.'/../engines/api/'.strtolower($controller).'.php');
		global $_FUEL;
		foreach ($_FUEL as $key => $val) {
			if (!array_key_exists($key, $params)) {
				$params[$key] = $val;
			}
		}

		/* Create User */
		$id = (!empty($_FUEL['user'])) ? $_FUEL['user'] : 0;
		$_user = new currentUser(array('id' => $id));

		$_controller = new $controller();
		$_controller->$function($params);
		return $_controller;
	}


	/* Set base pass for including files */
	public static function base() { 
		return dirname(__DIR__);
	}

	/* Include PHP code into a variable */
	public static function inject($filename, $vars=array(), $data=array()) {
		global $_n;
		
		if (!empty($vars)) {
			extract ($vars);
		}
		if (!empty($data)) {
			extract ($data);
		}
		if (is_file($filename)) {
			ob_start();
			include $filename;
			return ob_get_clean();
		}
		return false;
	}

	/* cURL call */
	public static function curl($url,$headers,$post){ 
		// $post is a URL encoded string of variable-value pairs separated by &
		$curl = curl_init();
		curl_setopt ($curl, CURLOPT_URL, $url);
		curl_setopt ($curl, CURLOPT_POST, 1);
		curl_setopt ($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt ($curl, CURLOPT_POSTFIELDS, $post); 
		curl_setopt ($curl, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt ($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt ($curl, CURLOPT_CONNECTTIMEOUT, 3); // 3 seconds to connect
		curl_setopt ($curl, CURLOPT_TIMEOUT, 10); // 10 seconds to complete
		$output = curl_exec($curl);
		curl_close($curl);
		return $output;
	}

	/**
	* Generate a random string/password
	* @return string - random string 12 chars long
	*/
	public static function randomString() {
		$alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
		$pass = array(); //remember to declare $pass as an array
		$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
		for ($i = 0; $i < 12; $i++) {
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}
		return implode($pass); //turn the array into a string
	}
}