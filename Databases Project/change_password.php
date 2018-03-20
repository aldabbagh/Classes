<?php
include('lib/common.php');

// Default values for form inputs
$username = '';
$password = '';

$alert = '';
$errors = [];

if( $_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = mysqli_real_escape_string($db, $_SESSION['username']);
  $password = mysqli_real_escape_string($db, $_POST['password']);
  $password_again = mysqli_real_escape_string($db, $_POST['password-confirm']);

  if (empty($password)) {
    $errors['password'] = "Please enter a password";
  }

  if (empty($password_again)) {
    $errors['password-confirm'] = "Please re-type your password";
  }

  if ($password_again !== $password) {
    $errors['password-confirm'] = "The passwords do not match";
  }

  if (count($errors) === 0) {
    $query = "UPDATE Clerk SET password = '".$password."', first_login = 0 WHERE username = '".$username."'";

    $queryID = mysqli_query($db, $query);

    include('lib/show_queries.php');
    if ($queryID == False) {
      $alert = 'Internal server error :(';

    } else {
      $_SESSION['username'] = $username;
      $_SESSION['type'] = "clerk";
      array_push($query_msg, "logging in... ");
      header("Location: index.php");
      exit();
    }
  }
}

?>


<?php include("lib/error.php"); ?>

<?php include("partials/head.php"); ?>

<div class="row justify-content-center my-5">
  <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
    <!-- Alerts -->
    <?php if (count($errors) > 0) { ?>
      <div class="alert alert-danger" role="alert">
        Some fields are missing or invalid. Please correct them.
      </div>
    <?php } elseif ($alert !== '') { ?>
      <div class="alert alert-danger" role="alert">
        <?= $alert ?>
      </div>
    <?php } ?>

    <div class="card">
      <div class="card-header bg-warning font-weight-bold">
        Password change required
      </div>
      <div class="card-body">
        <h1 class="card-title h3">Change your password</h1>
        <h2 class="card-subtitle h6 mb-2 text-muted">
          Since this is your first time logging in, you have to change your password.
        </h2>
        <form action="/change_password.php"
          method="post"
          enctype="multipart/form-data"
          autocomplete="off">
          <!-- Password -->
          <div class="form-group">
            <label for="password">Password</label>
            <input class="form-control
              <?= isset($errors['password']) ? 'is-invalid':''; ?>"
              id="password"
              name="password"
              type="password"
              placeholder="Password"
              value="<?= $password ?>">
            <?php if (isset($errors['password'])) { ?>
              <div class="invalid-feedback">
                <?php echo $errors['password']; ?>
              </div>
            <?php } ?>
          </div>

          <!-- Password Confirmation -->
          <div class="form-group">
            <label for="password-confirm">Password confirmation</label>
            <input class="form-control
              <?= isset($errors['password-confirm']) ? 'is-invalid':''; ?>"
              id="password-confirm"
              name="password-confirm"
              type="password"
              placeholder="Re-type you password"
              value="<?= $password_again ?>">
            <?php if (isset($errors['password-confirm'])) { ?>
              <div class="invalid-feedback">
                <?php echo $errors['password-confirm']; ?>
              </div>
            <?php } ?>
          </div>

          <!-- Change -->
          <button type="submit" class="btn btn-secondary">Change</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include("partials/tail.php"); ?>
