<?php
include('lib/common.php');

if ($_SESSION['type'] == "customer"){
	print("Error: Customers do not have access to this page. You will be redirected to the main menu");
	header( "refresh:10;url=index.php" );
	exit();
}

?>


<?php include('lib/error.php'); ?>

<?php include("partials/head.php"); ?>

<div class="row my-4">
  <div class="col-12">
    <!-- Reservatoin needing pickup -->
    <h1 class="h2">Pick-Up Reservation</h2>

  </div>
</div>

</div><!-- Close main page container-->


<div class="container-fluid">
<table class="table table-sm table-hover">
  <thead class="thead-light">
    <tr>
      <th scope="col">Confirmation #</th>
      <th scope="col">Customer</th>
      <th scope="col">Customer username</th>
      <th scope="col">Start date</th>
      <th scope="col">End date</th>
    </tr>
  </thead>
  <tbody>
    <!-- for each reservation -->
<?php

	if(isset($_POST['confirmation-number'])){
		$query1 = "SELECT confirmation_number, last_name, customer_username, start_date, end_date FROM (Rental AS R INNER JOIN Customer AS C ON R.customer_username = C.username)
		WHERE pickup_clerk_username IS NULL";
		$result1 = mysqli_query($db, $query1);

		if ( !is_bool($result1) && (mysqli_num_rows($result1) > 0) ) {
			$arr1 = [];
			$count1 = 0;
			while ($row1 = mysqli_fetch_array($result1, MYSQLI_ASSOC)){
				$arr1[$count1] = $row1['confirmation_number'];
				$count1++;
			}
			
		$success = false;
		foreach ($arr1 as $key => $value){
			if($value == $_POST['confirmation-number']){
				$success = true;
		}
		}

		if($success){
			header("Location: pickup_summary.php?id=". $_POST['confirmation-number']);
		}

		else{
			print("Error: " . $_POST['confirmation-number'] . " Pickup number is not on the pickup list");
		}

	} }
	
    $query = "SELECT confirmation_number, last_name, customer_username, start_date, end_date FROM (Rental AS R INNER JOIN Customer AS C ON R.customer_username = C.username)
		WHERE pickup_clerk_username IS NULL";
	$result = mysqli_query($db, $query);
    include('lib/show_queries.php');

    if ( !is_bool($result) && (mysqli_num_rows($result) > 0) ) {
		$arr = [];
		$count = 0;
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			print("<tr>");
			print("<th scope=\"row\">");
			$arr[$count] = $row['confirmation_number'];
			$count++;
			print("<a href=\"reservation_details.php?id=".$row['confirmation_number']."\" target=\"_blank\">".$row['confirmation_number']."</a></th>");
			print("<td>".$row['last_name']."</td>");
			print("<td>".$row['customer_username']."</td>");
			print("<td>".$row['start_date']."</td>");
			print("<td>".$row['end_date']."</td>");
			print("</tr>");
		}
	}
		?>
    <!-- end for -->
  </tbody>
</table>

</div>
<div class="container">
<hr>
<form action="pickup_reservation.php"
  method="post"
  enctype="multipart/form-data">
  <!-- Confirmation # -->
  <div class="form-row">
    <div class="col-sm-8 col-lg-4">
      <div class="form-group">
        <label for="confirmation-number">Confirmation number</label>
        <input class="form-control" id="confirmation-number"
          name="confirmation-number"
          placeholder="Confirmation #">
      </div>
    </div>
    <div class="col-sm-4 col-lg-2 align-self-end">
      <div class="form-group">
        <button type="submit" class="btn btn-primary btn-block">
          Pick-Up
        </button>
      </div>
    </div>
  </div>
</form>

</div>
<div class="container"><!-- Re-open main page container-->

<?php include("partials/tail.php"); ?>
