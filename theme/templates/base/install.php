<?php
if (!empty($_POST)) {

	/* Input Validation */
	$errors = [];
	if (empty($_POST['type'])) {
		$errors[] = 'type';
	}
	if (empty($_POST['name'])) {
		$errors[] = 'name';
	}
	if (empty($_POST['username'])) {
		$errors[] = 'username';
	}
	if (empty($_POST['password'])) {
		$errors[] = 'password';
	}
	if (empty($_POST['host'])) {
		$errors[] = 'host';
	}
	if (empty($_POST['port']) || filter_var($_POST['port'], FILTER_VALIDATE_INT) == FALSE) {
		$errors[] = 'port';
	}
	if (empty($_POST['business'])) {
		$errors[] = 'business';
	}
	if (empty($_POST['app'])) {
		$errors[] = 'app name';
	}
	
	if (empty($errors)) {
		// Try connection
		$type = $_POST['type'];
		$host = $_POST['host'];
		$username = $_POST['username'];
		$name = $_POST['name'];
		$password = $_POST['password'];
		$port = $_POST['port'];
		$business = $_POST['business'];
		$app = $_POST['app'];
		
		chdir('../'); 
		$filename = getcwd()."/config/.env";
		$dir = getcwd();

		$output = <<<EOF
DEBUG=0
DATE_DEFAULT_TIMEZONE_SET='America/Chicago'
HOME_DIRECTORY='$dir'
SESSION_SAVE_PATH="$dir/tmp"

CLIENT_NAME='$business'
APP_NAME='$app'

DRIVER=$type
HOST=$host
DATABASE=$name
USERNAME=$username
PASSWORD=$password
PORT=$port
EOF;

		switch ($_POST['type']) {
			// Check for postgres
			case "pgsql":
				try {
					$db_conn = pg_connect("host=".$_POST['host']." dbname=".$_POST['name']." user=".$_POST['username']." password=".$_POST['password']."  port=".$_POST['port']." connect_timeout=5");
				} catch (Exception $e) {
					$db_conn = false;
				}

				if ($db_conn) {
					/* Write these values to config */
				    $x = writeConfig($filename, $output);
				    if (!empty($x)) {
				    	$errors = array_merge($errors, $x);
				    }

				    /* Setup DB Tables */
					if (empty($errors)) {

					}
				} else {
					$errors[] = "Database Connection";
				}
			break;
			// Check for mysql
			case 'mysql':
				try {
					$db_conn = new mysqli($_POST['host'], $_POST['username'], $_POST['password'], $_POST['name'], $_POST['port']);
				} catch (Exception $e) {
					$db_conn = false;
				}
				if ($db_conn) {
					/* Write these values to config */
				    $x = writeConfig($filename, $output);
				    if (!empty($x)) {
				    	$errors = array_merge($errors, $x);
				    }

					/* Setup DB Tables */
					if (empty($errors)) {
						$sessions_check = <<<EOL
SELECT count(table_name)
FROM information_schema.tables
WHERE table_schema like '$name' 
	AND table_name = 'sessions';
EOL;

						$users_check = <<<EOL
SELECT count(table_name)
FROM information_schema.tables
WHERE table_schema like '$name' 
	AND table_name = 'users';
EOL;

						$sessions = <<<EOL
CREATE TABLE sessions (
  id varchar(40) NOT NULL,
  data longtext NOT NULL,
  created datetime DEFAULT NULL,
  modified datetime DEFAULT NULL,
  PRIMARY KEY (id)
);
EOL;

						$users = <<<EOL
CREATE TABLE users (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(60) NOT NULL DEFAULT '',
  email varchar(128) NOT NULL DEFAULT '',
  password varchar(128) NOT NULL DEFAULT '',
  active tinyint(1) DEFAULT NULL,
  `key` varchar(128) DEFAULT NULL,
  created datetime DEFAULT NULL,
  modified datetime DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY email_unique (email)
);
EOL;

					$admin = <<<EOL
INSERT INTO users VALUES (
	DEFAULT,
	"admin",
	"admin@jet",
	"\$2y\$10\$l6ArKzhWFJ0bUcku6zbObeiqNdZQnBjmfiksCWjHqtisQM.ihv5te",
	true,
	NULL,
	now(),
	now()
);
EOL;
						$sc = $db_conn->query($sessions_check);
						$uc = $db_conn->query($users_check);
						
						if (!$sc->field_count) {
							$db_conn->query($sessions);
						}
						if (!$uc->field_count) {
							$db_conn->query($users);
							$db_conn->query($admin);
						}
						if ($db_conn->errno) {
							$errors[] = "Database table creation";
						} else {
							// Success - Redirect to landing page
							header("Location: /");
							die();
						}
					}
				} else {
					$errors[] = "Database Connection";
				}
			break;
		}
	}	
}

function writeConfig($filename, $output) {
	$errors = [];
	if (!$fp = fopen($filename, 'w')) {
		$errors[] = "Cannot open file ($filename), ";
	}
	if (fwrite($fp, $output) === FALSE) {
		$errors[] = "Cannot write to file ($filename), ";
	}
	fclose($fp);
	return $errors;
}
?><!DOCTYPE html>
<html lang="en">

<head>
	<!-- Required meta tags always come first -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">

	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="/styles/bootstrap.min.css">

	<!-- FontAwesome CSS -->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css" integrity="sha384-5sAR7xN1Nv6T6+dT2mhtzEpVJvfS3NScPQTrOxhwjIuvcA67KV2R5Jz6kr4abQsz" crossorigin="anonymous">

	<link rel="stylesheet" href="/styles/jet.css">
	
	<title>JET Engine Install</title>
</head>
<body>
<div class="container-fluid">
	<div class="row background">
		<?php if (!empty($errors)) { ?>
		<div class="container error" id="notification-js" style="display: block;">
			<div class="row">
				<div class="col-1" id="notification-js-icon">
					<img src="/img/error.png">
				</div>
				<div class="col-10" id="notification-js-text">
					<ul id="notification-list">
						<?php foreach ($errors as $error) { ?>
						<li><?php echo $error; ?> is invalid.</li>
						<?php } ?>
					</ul>
				</div>
				<div class="col-1" id="notification-js-close">
					<button type="button" class="close" data-dismiss="notification-js"><span aria-hidden="true">×</span></button>
				</div>
			</div>
		</div>
		<?php } ?>
		<div class="container inner">
			<div class="row highlight-a hidden-sm-down">
				<div class="col-lg-12">
					<h4>JET Engine Install</h4>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="container-fluid">
	<div class="row footer">
		<div class="container inner2 txt-center">
			<div class="row row-centered">
				<div class="col-12 col-md-4 txt-center" id="numbers">
					<form action="/" method="post">
						<div class="form-group">
							<label for="business">Business*</label>
							<input type="text" name="business" id="business" class="form-control">
						</div>
						<div class="form-group">
							<label for="app">App Name*</label>
							<input type="text" name="app" id="app" class="form-control">
						</div>
						<div class="form-group">
							<label for="type">Database Type*</label>
							<input type="radio" name="type" value="mysql" class="form-control"> MySQL <br/>
							<input type="radio" name="type" value="pgsql" class="form-control"> PostgreSQL
						</div>
						<div class="form-group">
							<label for="name">Database Name*</label>
							<input type="text" name="name" id="name" class="form-control">
						</div>
						<div class="form-group">
							<label for="username">Database Username*</label>
							<input type="text" name="username" id="username" class="form-control">
						</div>
						<div class="form-group">
							<label for="password">Database password*</label>
							<input type="password" name="password" id="password" class="form-control">
						</div>
						<div class="form-group">
							<label for="host">Database host*</label>
							<input type="text" name="host" id="host" class="form-control" placeholder="localhost">
						</div>
						<div class="form-group">
							<label for="port">Database port*</label>
							<input type="text" name="port" id="port" class="form-control" placeholder="3306 / 5432">
						</div>
						<div class="form-group">
							<input type="submit" value="Save and continue">
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="container-fluid">
	<div class="row footer">
		<div class="container">
			<div class="row row-centered">
				<div class="col-12 txt-center">
					&copy; <?php echo date('Y'); ?> JET Engine.
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Scripts -->

<!-- jQuery first, then Bootstrap JS. -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="/js/bootstrap.min.js"></script>
<script src="/js/jet.js"></script>


</body>
</html>