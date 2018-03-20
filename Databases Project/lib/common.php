<?php
if (!isset($_SESSION)) {
  session_start();
}

date_default_timezone_set('Asia/Riyadh');

define("SEPARATOR", strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? '\\' : '/');
define('NEWLINE',  '<br>' );
define('REFRESH_TIME', 'Refresh: 1; ');

// Database
define('DB_HOST', "localhost");
define('DB_PORT', "3306");
define('DB_USER', "gatechUser");
define('DB_PASS', "gatech123");
define('DB_SCHEMA', "cs6400_fa17_team044");

// Show cause of HTTP : 500 Internal Server Error
error_reporting(E_ALL);
ini_set('display_errors', 'off');
ini_set("log_errors", 'on');
ini_set("error_log", getcwd() . SEPARATOR ."error.log");

// Allow back button without reposting data
header(
  "Cache-Control: private, no-cache, no-store, proxy-revalidate, no-transform"
);


$error_msg = [];
$query_msg = [];
$showQueries = true;
$showCounts = false;
$dumpResults = false;

$db = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_SCHEMA, DB_PORT);
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error() . NEWLINE;
  echo "Running on: ". DB_HOST . ":". DB_PORT . '<br>' .
    "Username: " . DB_USER . '<br>' .
    "Password: " . DB_PASS . '<br>'.
    "Database: " . DB_SCHEMA;
  phpinfo();
  exit();
}


function empty_to_null($query) {
  $pattern1 = "/(, *'' *,)|(, *,)/";
  $pattern2 = "/, *\)/";

  $query = preg_replace($pattern1, ', NULL,', $query);
  $query = preg_replace($pattern2, ', NULL)', $query);

  return $query;
}

?>
