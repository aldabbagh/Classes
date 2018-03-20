<?php
include('lib/common.php');
include('lib/lists.php');

// Default input values
$inputs = [
  'first_name' => '', 'middle_name' => '', 'last_name' => '',
  'home_phone_area' => '', 'home_phone_number' => '', 'home_phone_ext' => '',
  'work_phone_area' => '', 'work_phone_number' => '', 'work_phone_ext' => '',
  'cell_phone_area' => '', 'cell_phone_number' => '', 'cell_phone_ext' => '',
  'primary_phone_type' => '', 'email' => '', 'password' => '',
  'password_confirm' => '', 'street' => '', 'city' => '', 'state' => '',
  '9_digit_zip' => '', 'cc_name' => '', 'cc_number' => '', 'cc_month' => '',
  'cc_year' => '', 'cc_ccv' => ''
];

$alert = '';
$errors = [];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  foreach ($_POST as $key => $value) {
    $inputs[$key] = mysqli_real_escape_string($db, $_POST[$key]);
  }

  // Validation for missing inputs
  foreach ($inputs as $key => $value) {
    if ($key === 'home_phone_area' || $key === 'home_phone_number') {
      if ($inputs['primary_phone_type'] === 'home' && $value === '') {
        $errors[$key] = 'Primary phone cannot be empty';
      }

    } elseif ($key === 'work_phone_area' || $key === 'work_phone_number') {
      if ($inputs['primary_phone_type'] === 'work' && $value === '') {
        $errors[$key] = 'Primary phone cannot be empty';
      }

    } elseif ($key === 'cell_phone_area' || $key === 'cell_phone_number') {
      if ($inputs['primary_phone_type'] === 'cell' && $value === '') {
        $errors[$key] = 'Primary phone cannot be empty';
      }

    } elseif ($key === 'home_phone_ext' ||
              $key === 'work_phone_ext' ||
              $key === 'cell_phone_ext') {
      // It's okay

    } elseif ($key === 'primary_phone_type') {
      if ($value !== 'home' && $value !== 'work' && $value !== 'cell') {
        $errors[$key] = 'You must select a primary phone';

      } elseif ($inputs[$value.'_phone_area'] === '' ||
                $inputs[$value.'_phone_number'] === '') {
        $errors[$key] = 'Primary phone cannot be empty';
      }

    } elseif ($value === '') {
      $errors[$key] = 'This field is required';
    }
  }

  // validation for value of inputs
  foreach (['password', 'password_confirm',
            'first_name', 'middle_name', 'last_name', 'street', 'state',
            'city'] as $value) {
    if (strlen($inputs[$value]) > 50) {
      $errors[$value] = 'Cannot exceed 50 characters';
    }
  }

  if (strlen($inputs['password']) < 6) {
    $errors['password'] = 'Minimum password length is 6 characters';
  }

  if ($inputs['password_confirm'] !== $inputs['password']) {
    $errors['password_confirm'] = 'The passwords do not match';
  }

  if (strlen($inputs['cc_name']) > 100) {
    $errors['cc_name'] = 'Name cannot exceed 100 characters';
  }

  if ($inputs['email'] !== '' &&
      (filter_var($inputs['email'], FILTER_VALIDATE_EMAIL) === false ||
      strlen($inputs['email']) > 150)) {
    $errors['email'] = 'Please enter a valid email';
  }

  if ($inputs['cc_month'] !== '' &&
      (intval($inputs['cc_month']) > 12 ||
      intval($inputs['cc_month']) < 1)) {
    $errors['cc_month'] = 'Please select a valid month';
  }

  if ($inputs['cc_year'] !== '' &&
      (intval($inputs['cc_year']) > (intval(date('Y')) + 9) ||
      intval($inputs['cc_year']) < intval(date('Y')))) {
    $errors['cc_year'] = 'Please select a valid year';
  }

  if ($inputs['cc_ccv'] !== '' &&
      (intval($inputs['cc_ccv']) === 0 ||
      strlen($inputs['cc_ccv']) > 4 ||
      strlen($inputs['cc_ccv']) < 3)) {
    $errors['cc_ccv'] = 'Please enter a valid ccv';
  }

  if ($inputs['9_digit_zip'] !== '' &&
      (intval($inputs['9_digit_zip']) === 0 ||
      strlen($inputs['9_digit_zip']) !== 5)) {
    $errors['9_digit_zip'] = 'Please enter a valid zip code';
  }

  if ($inputs['cc_number'] !== '' &&
      (intval($inputs['cc_number']) === 0 ||
      strlen($inputs['cc_number']) < 12 ||
      strlen($inputs['cc_number']) > 19)) {
    $errors['cc_number'] = 'Please enter a valid card number';
  }

  foreach (['home_phone_area',
            'work_phone_area',
            'cell_phone_area'] as $value) {
    if (intval($inputs[$value]) !== 0) {
      if (intval($inputs[$value]) === 0 ||
          strlen($inputs[$value]) > 3 ||
          strlen($inputs[$value]) < 2) {
        $errors[$value] = 'Area code consists of 3 digits';
      }
    }
  }

  foreach (['home_phone_ext',
            'work_phone_ext',
            'cell_phone_ext'] as $value) {
    if (intval($inputs[$value]) !== 0) {
      if (intval($inputs[$value]) === 0 || strlen($inputs[$value]) > 4) {
        $errors[$value] = 'At most 4 digits';
      }
    }
  }

  foreach (['home_phone_number',
            'work_phone_number',
            'cell_phone_number'] as $value) {
    if (intval($inputs[$value]) !== 0) {
      if (intval($inputs[$value]) === 0 || strlen($inputs[$value]) > 12 ||
          strlen($inputs[$value]) < 3) {
        $errors[$value] = 'Between 3 and 12 digits';
      }
    }
  }


  if (count($errors) === 0) {

    // Check if the username exists or the email is associated with another user
    $query = "SELECT username FROM Customer WHERE username='".$inputs['email']."' OR email='".$inputs['email']."'";

    $result = mysqli_query($db, $query);
    include('lib/show_queries.php');
    $count = mysqli_num_rows($result);

    // Yes
    if (!empty($result) && ($count > 0) ) {
      $alert = "Username or email Already Exists";

    // No
    } else {

      mysqli_begin_transaction($db);

      $query = "INSERT INTO Customer
      VALUES ('".$inputs['email']."', '".$inputs['password']."',
        '".$inputs['email']."', '".$inputs['first_name']."',
        '".$inputs['middle_name']."', '".$inputs['last_name']."',
        '".$inputs['cc_name']."', ".$inputs['cc_number'].",
        ".$inputs['cc_month'].", ".$inputs['cc_year'].", ".$inputs['cc_ccv'].",
        '".$inputs['street']."', '".$inputs['state']."', '".$inputs['city']."',
        ".$inputs['9_digit_zip'].")";

      $queryID = mysqli_query($db, $query);

      include('lib/show_queries.php');

      // Insert failure
      if ($queryID  == False) {
        $alert = 'Internal server error :(';
        mysqli_rollback($db);

      // Ok, now add phones
      } else {
        $queryID = true;

        if ($inputs['home_phone_number'] != "") {
          if ($inputs['primary_phone_type'] == "home") {
            $is_primary = 1;
          } else {
            $is_primary = 0;
          }

		  if ($inputs['home_phone_ext']==""){
			  $query = "INSERT INTO PhoneNumber
			  VALUES (".$inputs['home_phone_area'].", ".$is_primary.", ".$inputs['home_phone_number'].", 'home', NULL, '".$inputs['email']."')";
		  }else{
			  $query = "INSERT INTO PhoneNumber
			  VALUES (".$inputs['home_phone_area'].", ".$is_primary.", ".$inputs['home_phone_number'].", 'home', ".$inputs['home_phone_ext'].", '".$inputs['email']."')";
		  }

          $queryID = mysqli_query($db, $query);

          include('lib/show_queries.php');

          if ($queryID  == False) {
            $alert = 'Internal server error :(';
            mysqli_rollback($db);
          }
        }

        if ($inputs['work_phone_number'] != "" && $queryID !== false) {
          if ($inputs['primary_phone_type'] == "work") {
            $is_primary = 1;
          } else {
            $is_primary = 0;
          }

		  if ($inputs['work_phone_ext']==""){
			  $query = "INSERT INTO PhoneNumber
			  VALUES (".$inputs['work_phone_area'].", ".$is_primary.", ".$inputs['work_phone_number'].", 'work', NULL, '".$inputs['email']."')";
		  }else{
			  $query = "INSERT INTO PhoneNumber
			  VALUES (".$inputs['work_phone_area'].", ".$is_primary.", ".$inputs['work_phone_number'].", 'work', ".$inputs['work_phone_ext'].", '".$inputs['email']."')";
		  }

          $queryID = mysqli_query($db, $query);

          include('lib/show_queries.php');

          if ($queryID  == False) {
            $alert = 'Internal server error :(';
            mysqli_rollback($db);
          }
        }

        if ($inputs['cell_phone_number'] != "" && $queryID !== false) {
          if ($inputs['primary_phone_type'] == "cell") {
            $is_primary = 1;
          } else {
            $is_primary = 0;
          }

		  if ($inputs['cell_phone_ext']==""){
			  $query = "INSERT INTO PhoneNumber
              VALUES (".$inputs['cell_phone_area'].", ".$is_primary.", ".$inputs['cell_phone_number'].", 'cell', NULL, '".$inputs['email']."')";
		  }else{
			  $query = "INSERT INTO PhoneNumber
              VALUES (".$inputs['cell_phone_area'].", ".$is_primary.", ".$inputs['cell_phone_number'].", 'cell', ".$inputs['cell_phone_ext'].", '".$inputs['email']."')";
		  }



          $queryID = mysqli_query($db, $query);

          include('lib/show_queries.php');

          if ($queryID  == False) {
            $alert = 'Internal server error :(';
            mysqli_rollback($db);
          }
        }

        if ($queryID === true) {
          mysqli_commit($db);
          $_SESSION['username'] = $inputs['email'];
          $_SESSION['type'] = "customer";
          array_push($query_msg, "logging in... ");
          header("Location: index.php");
          die();
        }

      }
    }
  }
}

?>

<?php include("lib/error.php"); ?>

<?php include("partials/head.php"); ?>

<div class="row my-4">
  <div class="col">
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


    <h1 class="h2">Registration</h1>
    <form action="registration.php"
      method="post"
      enctype="multipart/form-data"
      autocomplete="off">

      <h2 class="h4">Personal Details</h2>
      <!-- Full Name -->
      <fieldset class="form-group">
        <div class="form-row">
          <div class="col-md-4">
            <div class="form-group">
              <label for="first-name">First name</label>
              <input class="form-control
                <?= isset($errors['first_name']) ? 'is-invalid':''; ?>"
                id="first_name"
                name="first_name"
                type="text"
                placeholder="First name"
                value="<?php echo $inputs['first_name']; ?>">
              <?php if (array_key_exists('first_name', $errors)) { ?>
                <div class="invalid-feedback">
                  <?= $errors['first_name']; ?>
                </div>
              <?php } ?>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="middle_name">Middle name</label>
              <input class="form-control
                <?= isset($errors['middle_name']) ? 'is-invalid':''; ?>"
                id="middle_name"
                name="middle_name"
                type="text"
                placeholder="Middle name"
                value="<?php echo $inputs['middle_name']; ?>">
              <?php if (array_key_exists('middle_name', $errors)) { ?>
                <div class="invalid-feedback">
                  <?= $errors['middle_name']; ?>
                </div>
              <?php } ?>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="last_name">Last name</label>
              <input class="form-control
                <?= isset($errors['last_name']) ? 'is-invalid':''; ?>"
                id="last_name"
                name="last_name"
                type="text"
                placeholder="Last name"
                value="<?php echo $inputs['last_name']; ?>">
              <?php if (array_key_exists('last_name', $errors)) { ?>
                <div class="invalid-feedback">
                  <?= $errors['last_name']; ?>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
      </fieldset>


      <!-- Phones -->
      <div class="form-group">
        <div class="form-row">

          <div class="col-md-4">
            <fieldset class="form-group">
              <legend class="h6">Home phone</legend>
              <div class="form-row">
                <div class="mt-1 col-sm-3 col-md-12 col-lg-3">
                  <input class="form-control
                    <?= isset($errors['home_phone_area']) ? 'is-invalid':''; ?>"
                    id="home_phone_area"
                    name="home_phone_area"
                    type="text"
                    placeholder="Area"
                    value="<?php echo $inputs['home_phone_area']; ?>">
                </div>
                <div class="mt-1 col-sm-6 col-md-12 col-lg-6">
                  <input class="form-control
                    <?= isset($errors['home_phone_number']) ? 'is-invalid':''; ?>"
                    id="home_phone_number"
                    name="home_phone_number"
                    type="text"
                    placeholder="Number"
                    value="<?php echo $inputs['home_phone_number']; ?>">
                </div>
                <div class="mt-1 col-sm-3 col-md-12 col-lg-3">
                  <input class="form-control
                    <?= isset($errors['home_phone_ext']) ? 'is-invalid':''; ?>"
                    id="home_phone_ext"
                    name="home_phone_ext"
                    type="text"
                    placeholder="Ext."
                    value="<?php echo $inputs['home_phone_ext']; ?>">
                </div>
            </fieldset>
          </div>

          <div class="col-md-4">
            <fieldset class="form-group">
              <legend class="h6">Work phone</legend>
              <div class="form-row">
                <div class="mt-1 col-sm-3 col-md-12 col-lg-3">
                  <input class="form-control
                    <?= isset($errors['work_phone_area']) ? 'is-invalid':''; ?>"
                    id="work_phone_area"
                    name="work_phone_area"
                    type="text"
                    placeholder="Area"
                    value="<?php echo $inputs['work_phone_area']; ?>">
                </div>
                <div class="mt-1 col-sm-6 col-md-12 col-lg-6">
                  <input class="form-control
                    <?= isset($errors['work_phone_number']) ? 'is-invalid':''; ?>"
                    id="work_phone_number"
                    name="work_phone_number"
                    type="text"
                    placeholder="Number"
                    value="<?php echo $inputs['work_phone_number']; ?>">
                </div>
                <div class="mt-1 col-sm-3 col-md-12 col-lg-3">
                  <input class="form-control
                    <?= isset($errors['work_phone_ext']) ? 'is-invalid':''; ?>"
                    id="work_phone_ext"
                    name="work_phone_ext"
                    type="text"
                    placeholder="Ext."
                    value="<?php echo $inputs['work_phone_ext']; ?>">
                </div>
              </div>
            </fieldset>
          </div>

          <div class="col-md-4">
            <fieldset class="form-group">
              <legend class="h6">Cell phone</legend>
              <div class="form-row">
                <div class="mt-1 col-sm-3 col-md-12 col-lg-3">
                  <input class="form-control
                    <?= isset($errors['cell_phone_area']) ? 'is-invalid':''; ?>"
                    id="cell_phone_area"
                    name="cell_phone_area"
                    type="text"
                    placeholder="Area"
                    value="<?php echo $inputs['cell_phone_area']; ?>">
                </div>
                <div class="mt-1 col-sm-6 col-md-12 col-lg-6">
                  <input class="form-control
                    <?= isset($errors['cell_phone_number']) ? 'is-invalid':''; ?>"
                    id="cell_phone_number"
                    name="cell_phone_number"
                    type="text"
                    placeholder="Number"
                    value="<?php echo $inputs['cell_phone_number']; ?>">
                </div>
                <div class="mt-1 col-sm-3 col-md-12 col-lg-3">
                  <input class="form-control
                    <?= isset($errors['cell_phone_ext']) ? 'is-invalid':''; ?>"
                    id="cell_phone_ext"
                    name="cell_phone_ext"
                    type="text"
                    placeholder="Ext."
                    value="<?php echo $inputs['cell_phone_ext']; ?>">
                </div>
              </div>
            </fieldset>
          </div>

        </div>
      </div>


      <!-- Primary Phone -->
      <fieldset class="form-group">
        <legend class="h6">Primary Phone</legend>
        <div class="form-check form-check-inline">
          <label class="form-check-label">
            <input class="form-check-input"
              id="phone-type-home"
              type="radio"
              name="primary_phone_type"
              value="home"
              <?php echo ($inputs['primary_phone_type']=='home')?'checked':''?>>
              Home
          </label>
        </div>
        <div class="form-check form-check-inline">
          <label class="form-check-label">
            <input class="form-check-input"
              id="phone-type-work"
              type="radio"
              name="primary_phone_type"
              value="work"
              <?php echo ($inputs['primary_phone_type']=='work')?'checked':''?>>
              Work
          </label>
        </div>
        <div class="form-check form-check-inline">
          <label class="form-check-label">
            <input class="form-check-input"
              id="phone-type-cell"
              type="radio"
              name="primary_phone_type"
              value="cell"
              <?php echo ($inputs['primary_phone_type']=='cell')?'checked':''?>>
              Cell
          </label>
        </div>
        <?php if (array_key_exists('primary_phone_type', $errors)) { ?>
          <div class="invalid-feedback d-block">
            <?= $errors['primary_phone_type']; ?>
          </div>
        <?php } ?>
      </fieldset>


      <!-- Account Details -->
      <h2 class="h4">Account Details</h2>
      <fieldset class="form-group">
        <div class="form-row">
          <div class="col-12">
            <div class="form-group">
              <label for="email">Email</label>
              <input class="form-control
                <?= isset($errors['email']) ? 'is-invalid':''; ?>"
                id="email"
                name="email"
                type="text"
                placeholder="email@example.com"
                value="<?php echo $inputs['email']; ?>">
              <?php if (array_key_exists('email', $errors)) { ?>
                <div class="invalid-feedback">
                  <?= $errors['email']; ?>
                </div>
              <?php } ?>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="password">Password</label>
              <input class="form-control
                <?= isset($errors['password']) ? 'is-invalid':''; ?>"
                id="password"
                name="password"
                type="password"
                placeholder="Password"
                value="<?php echo $inputs['password']; ?>">
              <?php if (array_key_exists('password', $errors)) { ?>
                <div class="invalid-feedback">
                  <?= $errors['password']; ?>
                </div>
              <?php } ?>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="password-confirm">Password Confirmation</label>
              <input class="form-control
                <?= isset($errors['password_confirm']) ? 'is-invalid':''; ?>"
                id="password-confirm"
                name="password_confirm"
                type="password"
                placeholder="Re-type your password"
                value="<?php echo $inputs['password_confirm']; ?>">
              <?php if (array_key_exists('password_confirm', $errors)) { ?>
                <div class="invalid-feedback">
                  <?= $errors['password_confirm']; ?>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
      </fieldset>


      <!-- Address-->
      <h2 class="h4">Address</h2>
      <fieldset class="form-group">
        <div class="form-row">
          <div class="col-12">
            <div class="form-group">
              <label for="street">Street Address</label>
              <input class="form-control
                <?= isset($errors['street']) ? 'is-invalid':''; ?>"
                id="street"
                name="street"
                type="text"
                placeholder="3125 Abi Muhammad Al Mahhali"
                value="<?php echo $inputs['street']; ?>">
              <?php if (array_key_exists('street', $errors)) { ?>
                <div class="invalid-feedback">
                  <?= $errors['street']; ?>
                </div>
              <?php } ?>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="city">City</label>
              <input class="form-control
                <?= isset($errors['city']) ? 'is-invalid':''; ?>"
                id="city"
                name="city"
                type="text"
                placeholder="City"
                value="<?php echo $inputs['city']; ?>">
              <?php if (array_key_exists('city', $errors)) { ?>
                <div class="invalid-feedback">
                  <?= $errors['city']; ?>
                </div>
              <?php } ?>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="state">State</label>
              <select class="form-control
                <?= isset($errors['state']) ? 'is-invalid':''; ?>"
                id="state"
                name="state">
                <option value="" disabled hidden
                <?php echo ($inputs['state']=='')?'selected':''?>>
                  Select a state
                </option>

                <?php foreach($state_list as $state_value => $state_name) { ?>
                  <option
                    value="<?= $state_value ?>"
                    <?php echo ($inputs['state'] === $state_value) ? 'selected':''?>>
                    <?= $state_name ?>
                  </option>
                <?php } ?>

              </select>
            <?php if (array_key_exists('state', $errors)) { ?>
              <div class="invalid-feedback">
                <?= $errors['state']; ?>
              </div>
            <?php } ?>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="zip">Zip Code</label>
              <input class="form-control
                <?= isset($errors['9_digit_zip']) ? 'is-invalid':''; ?>"
                id="zip"
                name="9_digit_zip"
                type="text"
                placeholder="14215"
                value="<?php echo $inputs['9_digit_zip']; ?>">
              <?php if (array_key_exists('9_digit_zip', $errors)) { ?>
                <div class="invalid-feedback">
                  <?= $errors['9_digit_zip']; ?>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
      </fieldset>


      <!-- Credit Card-->
      <h2 class="h4">Credit Card</h2>
      <fieldset class="form-group">
        <div class="form-row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="cc-name">Name</label>
              <input class="form-control
                <?= isset($errors['cc_name']) ? 'is-invalid':''; ?>"
                id="cc-name"
                name="cc_name"
                type="text"
                placeholder="Name on card"
                value="<?php echo $inputs['cc_name']; ?>">
              <?php if (array_key_exists('cc_name', $errors)) { ?>
                <div class="invalid-feedback">
                  <?= $errors['cc_name']; ?>
                </div>
              <?php } ?>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label for="cc-number">Number</label>
              <input class="form-control
                <?= isset($errors['cc_number']) ? 'is-invalid':''; ?>"
                id="cc-number"
                name="cc_number"
                type="text"
                placeholder="Credit card number"
                value="<?php echo $inputs['cc_number']; ?>">
              <?php if (array_key_exists('cc_number', $errors)) { ?>
                <div class="invalid-feedback">
                  <?= $errors['cc_number']; ?>
                </div>
              <?php } ?>
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-group">
              <label for="cc-month">Expiration Month</label>
              <select class="form-control
                <?= isset($errors['cc_month']) ? 'is-invalid':''; ?>"
                id="cc-month"
                name="cc_month">
                <option value="" disabled hidden
                  <?php echo ($inputs['cc_month']=='')?'selected':''?>>
                  Select Expiration month
                </option>

                <?php foreach($month_list as $month_value => $month_name) { ?>
                  <option
                    value="<?= $month_value + 1 ?>"
                    <?php echo ($inputs['cc_month'] === $month_value) ? 'selected' : ''?>>
                    <?= $month_name ?>
                  </option>
                <?php } ?>

              </select>
              <?php if (array_key_exists('cc_month', $errors)) { ?>
                <div class="invalid-feedback">
                  <?= $errors['cc_month']; ?>
                </div>
              <?php } ?>
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-group">
              <label for="cc-year">Expiration Year</label>
              <select class="form-control
                <?= isset($errors['cc_year']) ? 'is-invalid':''; ?>"
                id="cc-year"
                name="cc_year">
                <option value="" disabled hidden
                  <?php echo ($inputs['cc_year']=='')?'selected':''?>>
                  Select a year
                </option>

                <?php $now_year = intval(date('Y')); ?>
                <?php for ($i = $now_year; $i < $now_year + 10; $i++) { ?>
                  <option
                    value="<?= $i ?>"
                    <?php echo ($inputs['cc_year'] == $i) ? 'selected' : ''?>>
                    <?= $i ?>
                  </option>
                <?php } ?>

              </select>
              <?php if (array_key_exists('cc_year', $errors)) { ?>
                <div class="invalid-feedback">
                  <?= $errors['cc_year']; ?>
                </div>
              <?php } ?>
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-group">
              <label for="cc-cvc">CCV</label>
              <input class="form-control
                <?= isset($errors['cc_ccv']) ? 'is-invalid':''; ?>"
                id="cc-ccv"
                name="cc_ccv"
                type="text"
                placeholder="Security number on back of card"
                value="<?php echo $inputs['cc_ccv']; ?>">
              <?php if (array_key_exists('cc_ccv', $errors)) { ?>
                <div class="invalid-feedback">
                  <?= $errors['cc_ccv']; ?>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
      </fieldset>


      <!-- Register -->
      <div class="form-row">
        <div class="col-lg-auto">
          <button type="submit" class="btn btn-block btn-primary">
            Register
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<?php include("partials/tail.php"); ?>
