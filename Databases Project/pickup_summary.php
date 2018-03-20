<?php
include('lib/common.php');
include('lib/error.php');

if ($_SESSION['type'] == "customer"){
	print("Error: Customers do not have access to this page. You will be redirected to the main menu");
	header( "refresh:10;url=index.php" );
	exit();
}

if(!$_REQUEST['id']){
	print("Error: No confirmation number was provided for pickup , page will redirect to main menu");
	header( "refresh:10;url=index.php" );
	exit();
}

			$query = "SELECT confirmation_number FROM Rental AS R
			WHERE pickup_clerk_username IS NULL";
			$result = mysqli_query($db, $query);
			include('lib/show_queries.php');

			if ( !is_bool($result) && (mysqli_num_rows($result) > 0) ) {
				$arr;
				$success = false;
				while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
					if($row['confirmation_number'] == $_REQUEST['id'])
						$success = true;
				}

			if(!$success){
				print("Error: pickup number is not on the pickup list, page will redirect to main menu");
				header( "refresh:10;url=index.php" );
				exit();
			}

			else{

			$query = "SELECT R.customer_username,  R.confirmation_number, tool_number, power_source, sub_option, sub_type, ROUND(price * 0.15*(TIMESTAMPDIFF(DAY,start_date,end_date) + 1),2) AS rental_price, ROUND(price * 0.4,2) AS deposit_price
						FROM ((RentalRentsTool AS RT NATURAL JOIN TOOL AS T) NATURAL JOIN RENTAL AS R)
						WHERE R.confirmation_number=" . $_REQUEST['id'] ." ORDER BY R.confirmation_number";
			$result = mysqli_query($db, $query);
			include('lib/show_queries.php');

			if (mysqli_affected_rows($db) == -1) {
				array_push($error_msg,  "SELECT ERROR:Failed to find friends ... <br>" . __FILE__ ." line:". __LINE__ );
			}

			if (isset($result)) {
			$start = true;
			$rowCount = mysqli_num_rows($result);
			$count = 0;
			$array = array('x' => "Placeholder");
			unset($array['x']);
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				if($start) {
					$conf = $row['confirmation_number'];
					$user = $row['customer_username'];
					if($row['power_source'] == "manual")
						$array[$row['tool_number']] = $row['sub_option'] . ' ' . $row['sub_type'];
					else
						$array[$row['tool_number']] = $row['power_source']. ' ' . $row['sub_option'] . ' ' . $row['sub_type'];
					$rp = $row['rental_price'];
					$dp = $row['deposit_price'];
					$start = false;
					$count++;
					if($count == $rowCount) {

						$queryCustomer = "SELECT first_name, middle_name, last_name FROM Customer WHERE username='".$row['customer_username']."'";
						$resultCustomer = mysqli_query($db, $queryCustomer);
						$rowCustomer = mysqli_fetch_array($resultCustomer, MYSQLI_ASSOC);
						if (!empty($resultCustomer) && (mysqli_num_rows($resultCustomer) > 0) ) {
							$customer = $rowCustomer['first_name'] . ' ' . $rowCustomer['middle_name'] . ' ' . $rowCustomer['last_name'];
						}
					}
				}

				else {
					if($row['power_source'] == "manual")
						$array[$row['tool_number']] = $row['sub_option'] . ' ' . $row['sub_type'];
					else
						$array[$row['tool_number']] = $row['power_source']. ' ' . $row['sub_option'] . ' ' . $row['sub_type'];

					$rp = $rp + $row['rental_price'];
					$dp = $dp + $row['deposit_price'];
					$count++;
					if($count == $rowCount) {

						$queryCustomer = "SELECT first_name, middle_name, last_name FROM Customer WHERE username='".$row['customer_username']."'";
						$resultCustomer = mysqli_query($db, $queryCustomer);
						$rowCustomer = mysqli_fetch_array($resultCustomer, MYSQLI_ASSOC);
						if (!empty($resultCustomer) && (mysqli_num_rows($resultCustomer) > 0) ) {
							$customer = $rowCustomer['first_name'] . ' ' . $rowCustomer['middle_name'] . ' ' . $rowCustomer['last_name'];
						}
					}
				}


			}
		}

		if($_POST['cc-status'] == "new") {
            $alert = '';
            $errors = [];
            
            if (strlen($_POST['cc-name']) > 100) {
                $errors['cc_name'] = 'Name cannot exceed 100 characters';
            }
            
            if ($inputs['cc_month'] !== '' &&
               (intval($inputs['cc_month']) > 12 ||
                intval($inputs['cc_month']) < 1)) {
                $errors['cc_month'] = 'Please select a valid month';
            }
            
			$query = "UPDATE customer SET card_name =\"".$_POST['cc-name']."\" , card_number =".$_POST['cc-number']." ,
			expiration_month =".$_POST['cc-month']." , expiration_year=".$_POST['cc-year']." , ccv=".$_POST['cc-cvc']."
			WHERE username = \"".$user."\"";

			$queryID = mysqli_query($db, $query);

			include('lib/show_queries.php');

			if (mysqli_affected_rows($db) == -1) {
				array_push($error_msg,  "UPDATE ERROR: Regular User... <br>".  __FILE__ ." line:". __LINE__ );
                 //array_push($error_msg,  'Error# '. mysqli_errno($db) . ": " . mysqli_error($db));
             }

			else {
				$query = "UPDATE rental SET pickup_clerk_username =\"".$_SESSION['username']."\"
				WHERE confirmation_number = \"".$_REQUEST['id']."\"";

				$queryID = mysqli_query($db, $query);

				include('lib/show_queries.php');

				if (mysqli_affected_rows($db) == -1) {
					array_push($error_msg,  "UPDATE ERROR: Regular User... <br>".  __FILE__ ." line:". __LINE__ );
					 //array_push($error_msg,  'Error# '. mysqli_errno($db) . ": " . mysqli_error($db));
				 }

				else {

					header("Location: confirm_pickup.php?id=".$_REQUEST['id']);
					die();
				}
			}

		}

		else if($_POST['cc-status'] == "existing") {
			$query = "UPDATE rental SET pickup_clerk_username =\"".$_SESSION['username']."\"
			WHERE confirmation_number = \"".$_REQUEST['id']."\"";

			$queryID = mysqli_query($db, $query);

			include('lib/show_queries.php');

			if (mysqli_affected_rows($db) == -1) {
				array_push($error_msg,  "UPDATE ERROR: Regular User... <br>".  __FILE__ ." line:". __LINE__ );
				 //array_push($error_msg,  'Error# '. mysqli_errno($db) . ": " . mysqli_error($db));
			 }

			else {


				header("Location: confirm_pickup.php?id=".$_REQUEST['id']);
				die();
			}

		}

			}}
?>


<?php include("partials/head.php"); ?>

<div class="row my-4">
  <div class="col-12">
    <!-- Reservatoin Summary -->
    <h1 class="h2">
      Pick-Up Reservation</br>
      <small class="text-muted">
        Confirmation #: <?php print($conf); ?>
      </small>
    </h2>
    <dl class="row">
      <dt class="col-sm-3 col-lg-2">Customer name</dt>
      <dd class="col-sm-9 col-lg-10">
        <?php print($customer); ?>
      </dd>

      <dt class="col-sm-3 col-lg-2">Total deposit price</dt>
      <dd class="col-sm-9 col-lg-10">
        <?php print($dp); ?>
      </dd>

      <dt class="col-sm-3 col-lg-2">Total rental price</dt>
      <dd class="col-sm-9 col-lg-10">
        <?php print($rp); ?>
      </dd>
    </dl>

    <!-- Credit card form -->
    <form action="pickup_summary.php?id=<?php echo ($_REQUEST['id']) ?>"
      method="post"
      enctype="multipart/form-data">

	  <input type="hidden" name="confirmation-number" value="<?php print($conf); ?>">
      <fieldset class="form-group">
        <legend class="h6">Credit card</legend>
		Warning: This will overwrite your current credit card on file!!!
		<br>
        <div class="form-check form-check-inline">
          <label class="form-check-label">
            <input class="form-check-input" id="cc-existing"
              type="radio"
              name="cc-status"
              value="existing"
              checked
              onclick="$('#cc-fieldset').collapse('hide');"> Existing
          </label>
        </div>
        <div class="form-check form-check-inline">
          <label class="form-check-label">
            <input class="form-check-input" id="cc-new"
              type="radio"
              name="cc-status"
              value="new"
              onclick="$('#cc-fieldset').collapse('show');"> New
          </label>
        </div>
      </fieldset>

      <fieldset class="form-group collapse" id="cc-fieldset">
        <div class="form-row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="cc-name">Name</label>
              <input class="form-control" id="cc-name"
                name="cc-name"
                type="text"
                placeholder="Name on card">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="cc-number">Number</label>
              <input class="form-control" id="cc-number"
                name="cc-number"
                type="text"
                placeholder="Credit card number">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="cc-month">Expiration Month</label>
              <select class="form-control" id="cc-month"
                name="cc-month">
                <option value="" disabled <?php echo ($_POST['cc-year']=='')?'selected':''?> hidden>
                  Select a month
                </option>
                <option <?php echo ($_POST['cc-month']=='1')?'selected':''?> value="1">January</option>
                <option <?php echo ($_POST['cc-month']=='2')?'selected':''?> value="2">Feburary</option>
                <option <?php echo ($_POST['cc-month']=='3')?'selected':''?> value="3">March</option>
                <option <?php echo ($_POST['cc-month']=='4')?'selected':''?> value="4">April</option>
                <option <?php echo ($_POST['cc-month']=='5')?'selected':''?> value="5">May</option>
                <option <?php echo ($_POST['cc-month']=='6')?'selected':''?> value="6">June</option>
                <option <?php echo ($_POST['cc-month']=='7')?'selected':''?> value="7">July</option>
                <option <?php echo ($_POST['cc-month']=='8')?'selected':''?> value="8">August</option>
                <option <?php echo ($_POST['cc-month']=='9')?'selected':''?> value="9">September</option>
                <option <?php echo ($_POST['cc-month']=='10')?'selected':''?> value="10">October</option>
                <option <?php echo ($_POST['cc-month']=='11')?'selected':''?> value="11">November</option>
                <option <?php echo ($_POST['cc-month']=='12')?'selected':''?> value="12">December</option>
              </select>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="cc-year">Expiration Year</label>
              <select class="form-control" id="cc-year"
                name="cc-year">
                <option value="" disabled <?php echo ($_POST['cc-year']=='')?'selected':''?> hidden>
                  Select a year
                </option>
							<option <?php echo ($_POST['cc-year']=='')?'selected':''?> value=''>--Select Year--</option>
							<option <?php echo ($_POST['cc-year']=='2017')?'selected':''?> value="2017">2017</option>
							<option <?php echo ($_POST['cc-year']=='2018')?'selected':''?> value="2018">2018</option>
							<option <?php echo ($_POST['cc-year']=='2019')?'selected':''?> value="2019">2019</option>
							<option <?php echo ($_POST['cc-year']=='2020')?'selected':''?> value="2020">2020</option>
							<option <?php echo ($_POST['cc-year']=='2021')?'selected':''?> value="2021">2021</option>
							<option <?php echo ($_POST['cc-year']=='2022')?'selected':''?> value="2022">2022</option>
							<option <?php echo ($_POST['cc-year']=='2023')?'selected':''?> value="2023">2023</option>
							<option <?php echo ($_POST['cc-year']=='2024')?'selected':''?> value="2024">2024</option>
							<option <?php echo ($_POST['cc-year']=='2025')?'selected':''?> value="2025">2025</option>
							<option <?php echo ($_POST['cc-year']=='2026')?'selected':''?> value="2026">2026</option>
							<option <?php echo ($_POST['cc-year']=='2027')?'selected':''?> value="2027">2027</option>
                <!-- Generate a list of years starting from current -->
              </select>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="cc-cvc">CVC</label>
              <input class="form-control" id="cc-cvc"
                name="cc-cvc"
                type="text"
                placeholder="Security number on back of card">
            </div>
          </div>
        </div>
      </fieldset>

      <!-- Confirm -->
      <button type="submit" class="btn btn-primary">
        Confirm Pick-Up
      </button>
    </form>

  </div>
</div>

<?php include("partials/tail.php");
