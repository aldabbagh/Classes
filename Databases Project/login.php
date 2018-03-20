<?php
include('lib/common.php');
////maldabbagh3: I removed unneeded prints and outputs from login
// Default values for form inputs
$username = '';
$password = '';
$type = 'customer';

$alert = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $password = mysqli_real_escape_string($db, $_POST['password']);
  $type = mysqli_real_escape_string($db, $_POST['type']);

  if (empty($username)) {
    $errors['username'] = "Please enter an email address";
  }

  if (empty($password)) {
    $errors['password'] = "Please enter a password";
  }

  if (empty($type)) {
    $errors['type'] = "Please select a user type";
  }

  if (count($errors) === 0) {
    if ($type === "customer") {
      $query = "SELECT password, first_name
                FROM Customer WHERE username='$username'";
      $result = mysqli_query($db, $query);
      $count = mysqli_num_rows($result);

      if (!empty($result) && ($count > 0)) {
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        $storedPassword = $row['password'];

        $options = ['cost' => 8];
        $enteredHash = password_hash($password, PASSWORD_DEFAULT, $options);
        $storedHash = password_hash(
          $storedPassword, PASSWORD_DEFAULT, $options
        );


        if (password_verify($password, $storedHash)) {
          array_push($query_msg, "Password is Valid! ");
          $_SESSION['username'] = $username;
          $_SESSION['type'] = "customer";
          $_SESSION['first_name'] = $row['first_name'];
          array_push($query_msg, "logging in... ");
          header("Location: index.php");
          die();

        } else {
          $alert = 'Login Failed. Make sure the password entered is correct.';
        }

      } else {
        $query = "SELECT password FROM Clerk WHERE username='$username'";
        $result = mysqli_query($db, $query);
        $count = mysqli_num_rows($result);

        if (!empty($result) && ($count > 0)) {
          $alert = "The username entered is for a clerk: $username";
        } else {
          header("Location: registration.php");
          die();
        }
      }

    } else {
      $query = "SELECT password, first_name ,first_login
                FROM Clerk WHERE username='$username'";
      $result = mysqli_query($db, $query);
      $count = mysqli_num_rows($result);

      if (!empty($result) && ($count > 0)) {
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        $storedPassword = $row['password'];

        $options = ['cost' => 8];
        $enteredHash = password_hash($password, PASSWORD_DEFAULT, $options);
        $storedHash = password_hash(
          $storedPassword, PASSWORD_DEFAULT, $options
        );


        if (password_verify($password, $storedHash) ) {
          if ($row['first_login'] != 1) {
            $_SESSION['username'] = $username;
            $_SESSION['type'] = "clerk";
            $_SESSION['first_name'] = $row['first_name'];
            header("Location: index.php");
            die();
//maldabbagh3: I modified the below eqality from === to == because they are the same value but not the same type.
          } elseif ($row['first_login'] == 1) {
            $_SESSION['username'] = $username;
            header("Location: change_password.php");
            die();
          }

        } else {
          $alert = "Login Failed. Make sure the password entered is correct.";
        }

      } else {
        $query = "SELECT password FROM Customer WHERE username='$username'";
        $result = mysqli_query($db, $query);
        $count = mysqli_num_rows($result);

        if (!empty($result) && ($count > 0)) {
          $alert = "The username entered is for a customer: $username";
        }

        else {
          $alert = "A Clerk with this username does not exist.";
        }
      }
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
      <div class="card-body">
        <h1 class="card-title h2">Login</h1>
        <form action="/login.php" method="post" enctype="multipart/form-data">
          <!-- Username -->
          <div class="form-group">
            <label for="username">Username</label>
            <input class="form-control
              <?= isset($errors['username']) ? 'is-invalid':''; ?>"
              id="username"
              name="username"
              placeholder="Username"
              value="<?= $username ?>">
            <?php if (array_key_exists('username', $errors)) { ?>
              <div class="invalid-feedback">
                <?php echo $errors['username']; ?>
              </div>
            <?php } ?>
          </div>

          <!-- Password -->
          <div class="form-group">
            <label for="password">Password</label>
            <input class="form-control
              <?= isset($errors['password']) ? 'is-invalid':''; ?>"
              id="password"
              name="password"
              type="password"
              placeholder="Password">
            <?php if (array_key_exists('password', $errors)) { ?>
              <div class="invalid-feedback">
                <?php echo $errors['password']; ?>
              </div>
            <?php } ?>
          </div>

          <!-- User Type -->
          <div class="form-check form-check-inline">
            <label class="form-check-label">
              <input class="form-check-input
                <?= isset($errors['type']) ? 'is-invalid':''; ?>"
                id="type-customer"
                type="radio"
                name="type"
                value="customer"
                <?php if ($type === 'customer') echo 'checked'; ?>>
                Customer
            </label>
          </div>
          <div class="form-check form-check-inline">
            <label class="form-check-label">
              <input class="form-check-input"
                id="type-clerk"
                type="radio"
                name="type"
                value="clerk"
                <?php if ($type === 'clerk') echo 'checked'; ?>>
                Clerk
            </label>
          </div>

          <!-- Submit -->
          <div class="form-group my-2">
            <input type="submit" class="btn btn-block btn-primary"
              value="Sign in">
            </input>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include("partials/tail.php"); ?>
