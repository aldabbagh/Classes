<?php
include('lib/common.php');

if ($_SESSION['type'] == "customer"){
	print("Error: Customers do not have access to this page. You will be redirected to the main menu");
	header( "refresh:10;url=index.php" );
	exit();
}

?>


<?php include("partials/head.php"); ?>

<div class="row my-4">
  <div class="col-12">
    <!-- Reservatoin needing pickup -->
    <h1 class="h2">Dropoff Reservation</h2>

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
    $query = "SELECT confirmation_number, last_name, customer_username, start_date, end_date FROM (Rental AS R INNER JOIN Customer AS C ON R.customer_username = C.username)
		WHERE ((dropoff_clerk_username IS NULL) AND (pickup_clerk_username IS NOT NULL)) ";
	$result = mysqli_query($db, $query);
    include('lib/show_queries.php');

    if ( !is_bool($result) && (mysqli_num_rows($result) > 0) ) {
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			print("<tr>");
			print("<th scope=\"row\">");
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
<form action="dropoff_summary.php"
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
          Dropoff
        </button>
      </div>
    </div>
  </div>
</form>

</div>
<div class="container"><!-- Re-open main page container-->

<?php include("partials/tail.php"); ?>
