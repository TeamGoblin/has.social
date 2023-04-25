<?php

use Phroute\Phroute\RouteCollector;
use Phroute\Phroute\Dispatcher;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$router = new RouteCollector();

/*
 * Engine Filters
 * Any thing other than null returned from a filter will prevent the route handler from being dispatched
 */
$router->filter('webAuthenticate', function(){
	$id = 0;

	/* Check if session is logged in*/
	if (!empty($_SESSION['_jet_id'])) {
		$id = $_SESSION['_jet_id'];
	}
	
	$_user = new User(array('id' => $id));

    if ($_user->get('active') === true) {
    	return;
    } else {
    	header('Location: /user/login');
    	return false;
    }
});

$router->filter('apiAuthenticate', function(){    
    // Check if user is authenticated for api
    global $_jwt;
    $id = 0;

    // JWT not found in request
    if (@!preg_match('/Bearer\s(\S+)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
        header('HTTP/1.0 400 Bad Request');
        echo 'Token not found in request';
        return FALSE;
    }

    $jwt = $matches[1];
    // JWT unable to be extracted from the authorization header
    if (!$jwt) {
        header('HTTP/1.0 400 Bad Request');
        return FALSE;
    }

    try {
        $decoded = JWT::decode($jwt, new Key($_jwt['public_key'], 'ES384'));
        $decoded_array = (array) $decoded;
        var_dump($decoded_array);
    } catch (Exception $e) {
        return FALSE;
    }
    return;

    /*
    $_user = new User(array('id' => $id));

    if ($_user->get('active') === true) {
        return;
    } else {
        // @todo remove this return before production.
        return;
        // return false;
        die('Access Unauthorized');
    }
    */
});

$router->filter('webAuthorize', function($roles){
	/* Verify user has proper roles */
});

$router->filter('apiAuthorize', function($roles){
    /* Verify user has proper roles */
});


/* 
 * Engine Routes 
 */
$router->get('/', function(){
	/* WEB function pass controller and functon */
	return JET::web('RootWeb', 'front');

	/* API function pass controller and functon */
	// return JET::api('Controller', 'Function');
}
,['before' => 'webAuthenticate']);
//['before' => 'webAuthorize', 'filterParams' => 'admin']);


/*
 * API
 */
/* 
 * Engine Routes 
 */
$router->get('/api/user', function(){
    /* API function pass controller and functon */
    return JET::api('UserAPI', 'list');
}
,['before' => 'apiAuthenticate']);
/*
$router->get('/api/get/{controller}/{function}?/{id}?', function($controller, $function =  NULL, $id = NULL) {
    $id = explode('?', $id);
    $id = $id[0];
    return JET::api('ApiRouteController', 'route', ['method' => 'GET', 'controller' => $controller, 'function' => $function, 'id' => $id]);
}, ['before' => 'apiAuthenticate']);
/*
$router->post('/api/post/{controller}/{function}?/{id}?', function($controller, $function =  NULL, $id = NULL) {
    $id = explode('?', $id);
    $id = $id[0];
    return JET::api('ApiRouteController', 'route', ['method' => 'POST', 'controller' => $controller, 'function' => $function, 'id' => $id]);
}, ['before' => 'apiAuthenticate']);

$router->patch('/api/patch/{controller}/{function}?/{id}?', function($controller, $function =  NULL, $id = NULL) {
    $id = explode('?', $id);
    $id = $id[0];
    return JET::api('ApiRouteController', 'route', ['method' => 'PATCH', 'controller' => $controller, 'function' => $function, 'id' => $id]);
}, ['before' => 'apiAuthenticate']);

$router->delete('/api/delete/{controller}/{function}?/{id}?', function($controller, $function =  NULL, $id = NULL) {
    $id = explode('?', $id);
    $id = $id[0];
    return JET::api('ApiRouteController', 'route', ['method' => 'DELETE', 'controller' => $controller, 'function' => $function, 'id' => $id]);
}, ['before' => 'apiAuthenticate']);
*/

/* 
 * USERS
 */

$router->get('/user/login', function() {
	return JET::web('UserWeb', 'login');
});

$router->post('/user/loginAction', function() {
	return JET::web('UserWeb', 'loginAction');
});

$router->get('/user/logout', function() {
	return JET::web('UserWeb', 'logout');
},['before' => 'webAuthenticate']);

$router->get('/user/forgot', function() {
	return JET::web('UserWeb', 'forgot');
});

$router->post('/user/forgot', function() {
	return JET::web('UserWeb', 'forgotAction');
});

$router->get('/user/reset/{key}', function($key) {
	return JET::web('UserWeb', 'reset', array('key' => $key));
});

$router->post('/user/reset', function() {
	return JET::web('UserWeb', 'resetAction');
});

$router->get('/users', function() {
	return JET::web('UserWeb', 'list');
},
['before' => 'webAuthenticate']);

$router->get('/users/new', function() {
	return JET::web('UserWeb', 'new');
},['before' => 'webAuthenticate']);

$router->post('/users/newAction', function() {
	return JET::web('UserWeb', 'newAction');
},['before' => 'webAuthenticate']);

$router->get('/users/edit/{id}', function($id) {
	return JET::web('UserWeb', 'edit', array('id' => $id));
},['before' => 'webAuthenticate']);

$router->post('/users/editAction', function() {
	return JET::web('UserWeb', 'editAction');
},['before' => 'webAuthenticate']);

$router->get('/users/delete/{id}', function($id) {
	return JET::web('UserWeb', 'delete', array('id' => $id));
},['before' => 'webAuthenticate']);

$router->post('/users/deleteAction', function() {
	return JET::web('UserWeb', 'deleteAction');
},['before' => 'webAuthenticate']);