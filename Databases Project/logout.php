<?php

// written by GTusername1

/* destroy session data */
session_start();
session_destroy();
$_SESSION = array();

/* redirect to login page */
header('Location: login.php');

?>