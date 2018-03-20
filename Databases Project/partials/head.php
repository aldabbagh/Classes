<?php
$request_file = basename($_SERVER['REQUEST_URI'], '.php');
//maldabbagh3: I added registration as part of the files that can run without a username in the session.
if (!isset($_SESSION['username'])) {
  if ($request_file !== 'login' && $request_file !== 'registration') {
    header("Location: /login.php");
    exit();
  }
}

if ($request_file !== 'index') {
  $page_name = str_replace('_', ' ', ucwords($request_file));
} else {
  $page_name = '';
}

?>
<!doctype html>
<html class="no-js" lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>
      Tools 4 Rent
      <?= !empty($page_name) ? "- $page_name" : '' ?>
    </title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="/favico.png">
    <link rel="apple-touch-icon" href="/icon.png">

    <link rel="stylesheet" href="/static/css/bootstrap.min.css">
    <link rel="stylesheet" href="/static/css/jquery.datetimepicker.min.css">
    <link rel="stylesheet" href="/static/css/tools4rent.css">
  </head>
  <body>

  <!-- Page Container Start -->
  <div class="container">

    <!-- Page header -->
    <?php include("partials/header.php"); ?>


<?php
unset($request_file);
unset($page_name);
?>
