<?php
/* Setup Config Options */
include_once('../config/config.php');

if (isset($_ENV['DEBUG']) && $_ENV['DEBUG'] == 1) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

/* Set Timezone */
date_default_timezone_set($_ENV['DATE_DEFAULT_TIMEZONE_SET']);
ini_set('session.save_path', $_ENV['SESSION_SAVE_PATH']);
ini_set('session.cookie_httponly', 1);

/* Setup DB */
global $_db;	// database
global $_n; 	// notifications
global $_jwt;   // jwt

/* Connect to database */
/* @TODO Not sure if both are needed - 1st creates persistent connection, second uses PDO, not sure if both needed for session.php to work */
/* Check db driver type setup db connection based on type */
switch ($db_config['driver']) {
	// Check for postgres
    case !strcasecmp($db_config['driver'], 'PGSQL'):
		$_db = (new \Pecee\Pixie\Connection('pgsql', $db_config))->getQueryBuilder();
        break;
    // Check for mysql
    case !strcasecmp($db_config['driver'],'MYSQL'):
        $_db = (new \Pecee\Pixie\Connection('mysql', $db_config))->getQueryBuilder();
        break;
}


/* Setup FUEL variable */
global $_FUEL;

/* Parse GET, PUT, POST, and DELETE vars into $_FUEL */
switch ($_SERVER['REQUEST_METHOD']) {
    case !strcasecmp($_SERVER['REQUEST_METHOD'],'DELETE'):
        parse_str(file_get_contents( 'php://input' ), $_FUEL);
        break;
    case !strcasecmp($_SERVER['REQUEST_METHOD'],'PUT'):
        parse_str(file_get_contents( 'php://input' ), $_FUEL);
        break;
    case !strcasecmp($_SERVER['REQUEST_METHOD'],'GET'):
        $_FUEL = $_GET;
        break;
    case !strcasecmp($_SERVER['REQUEST_METHOD'],'POST'):
        $_FUEL['raw_body'] = json_decode( file_get_contents( 'php://input' ), true );
        $_FUEL += $_POST;
        $_FUEL += $_FILES;
        break;
}

if (isset($_SERVER['QUERY_STRING'])) {
    $_FUEL['query_string'] = $_SERVER['QUERY_STRING'];
    $_FUEL['query_params'] = getQueryParams($_FUEL['query_string']);
}

function getQueryParams($query_string) {
    $query_params = [];
    $query_string = explode('&', $query_string);
    if (count($query_string) >= 1) {

        if (!empty($query_string[0])) {
            foreach ($query_string as $query_param) {
                $query_param = explode('=', $query_param);
                $query_params[$query_param[0]] = $query_param[1];
            }
        }
    }
    return $query_params;
}

if (!function_exists('apache_request_headers')) {
    function apache_request_headers() {
        $array = array();
        $regex_http = '/\AHTTP_/';
        foreach($_SERVER as $key => $val) {
            if (preg_match($regex_http, $key)) {
                $array_key = preg_replace($regex_http, '', $key);
                $regex_matches = array();
                // do some nasty string manipulations to restore the original letter case
                // this should work in most cases
                $regex_matches = explode('_', $array_key);
                if (count($regex_matches) > 0 and strlen($array_key) > 2) {
                    foreach ($regex_matches as $ak_key => $ak_val) $regex_matches[$ak_key] = ucfirst($ak_val);
                    $array_key = implode('-', $regex_matches);
                }
                $array[$array_key] = $val;
            }
        }
        return( $array );
    }
}
$headers = apache_request_headers();
if (!empty($headers)) {
    $fuel_headers = [];
    foreach ($headers as $header => $value) {
        $fuel_headers[$header] = $value;
    }
    $_FUEL['http_headers'] = $fuel_headers;
}

/* Load Utility Functions */
include_once('utility.php');

/* Load Session Handler */
include_once('session.php');

/* Include cache model */
include_once(__DIR__.'/../engines/data/cache.php');

/* Include user model */
include_once(__DIR__.'/../engines/data/user.php');

/* Include currentUser model */
include_once(__DIR__.'/../engines/data/currentUser.php');

/* Include JET Class */
include_once('jet.php');

/* Include Theme  */
include_once('theme.php');

/* Include Notification  */
include_once('notification.php');

$_n = new Notification();
if (isset($_SESSION['notification'])) {
    $_n->setType($_SESSION['notification']['type']);
    $_n->setMsg($_SESSION['notification']['msg']);
    $_SESSION['notification'] = NULL;
}

$_jwt['private_key'] = file_get_contents(__DIR__.'/../config/jwtES384key.pem');
$_jwt['public_key'] = file_get_contents(__DIR__.'/../config/jwtES384pubkey.pem');