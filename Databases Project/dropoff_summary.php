<?php
include('lib/common.php');
include('lib/error.php');
include("partials/head.php");

if ($_SESSION['type'] == "customer"){
	print("Error: Customers do not have access to this page. You will be redirected to the main menu");
	header( "refresh:10;url=index.php" );
	exit();
}

if(!isset($_POST['confirmation-number'])){
	print("Error: No dropoff Confirmation Number was provided, page will be redirected to main menu");
	header( "refresh:10;url=index.php" );
	exit();
}

			$query = "SELECT confirmation_number FROM Rental
			WHERE ((dropoff_clerk_username IS NULL) AND (pickup_clerk_username IS NOT NULL)) ";
			$result = mysqli_query($db, $query);
			include('lib/show_queries.php');

			if ( !is_bool($result) && (mysqli_num_rows($result) > 0) ) {
				$success = false;
				while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
					if($row['confirmation_number'] == $_POST['confirmation-number'])
						$success = true;
				}

			if(!$success){
				print("Error: dropoff confirmation number is not on the dropoff list, page will redirect back");
				header( "refresh:5;url=dropoff_reservation.php" );
				exit();
			}

			else{

			$query = "SELECT R.customer_username,  R.confirmation_number, tool_number, power_source, sub_option, sub_type, ROUND(price * 0.15*(TIMESTAMPDIFF(DAY,start_date,end_date) + 1),2) AS rental_price, ROUND(price * 0.4,2) AS deposit_price
						FROM ((RentalRentsTool AS RT NATURAL JOIN TOOL AS T) NATURAL JOIN RENTAL AS R)
						WHERE R.confirmation_number=".$_POST['confirmation-number']." ORDER BY R.confirmation_number";
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

		if($_POST['second_post']) {
			$query = "UPDATE rental SET dropoff_clerk_username =\"".$_SESSION['username']."\"
			WHERE confirmation_number = \"".$_POST['confirmation-number']."\"";

			$queryID = mysqli_query($db, $query);

			include('lib/show_queries.php');

			if (mysqli_affected_rows($db) == -1) {
				array_push($error_msg,  "UPDATE ERROR: Regular User... <br>".  __FILE__ ." line:". __LINE__ );
				 //array_push($error_msg,  'Error# '. mysqli_errno($db) . ": " . mysqli_error($db));
			 }

			else {
				header("Location: confirm_dropoff.php?id=".$_POST['confirmation-number']);
				die();
			}

			}}}

			else{
				print("Error: No dropoffs found, page will be redirected to main menu");
				header( "refresh:10;url=index.php" );
				exit();
			}


?>




<div class="row my-4">
  <div class="col-12">
    <!-- Reservatoin Summary -->
    <h1 class="h2">
      Dropoff Reservation</br>
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
    <form action="dropoff_summary.php"
      method="post"
      enctype="multipart/form-data">

	  <input type="hidden" name="confirmation-number" value="<?php print($conf); ?>">
	  <input type="hidden" name="second_post" value="<?php print($conf); ?>">
      <!-- Confirm -->
      <button type="submit" class="btn btn-primary">
        Confirm Dropoff
      </button>
    </form>

  </div>
</div>

<?php include("partials/tail.php");
