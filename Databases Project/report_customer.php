<?php 
include('lib/common.php');
include("partials/head.php"); ?>
<div class="row justify-content-between my-4">
  <div class="col-md-6">
    <!-- Report Header -->
    <h1 class="h2">Customer Report</h1>
    <p class="lead mb-0">The list of customers who made reservations in the current month.</p>
  </div>
  <div class="col-auto align-self-end">
    <a href="/report_customer.php" class="btn btn-secondary">
      Reload results
    </a>
  </div>
</div>

</div><!-- Close main page container-->
<div class="container-fluid">

<!-- Report table-->
<table class="table table-sm table-hove">
  <thead class="thead-light">
    <tr>
      <th scope="col">Customer username</th>
      <th scope="col">View Profile</th>
      <th scope="col">First name</th>
	  <th scope="col">Middle name</th>
	  <th scope="col">Last name</th>
      <th scope="col">Email</th>
      <th scope="col">Primary phone</th>
      <th scope="col">Total reservations</th>
      <th scope="col">Total tools</th>
    </tr>
  </thead>
  <tbody>
    <!-- for each Customer who made a reservation in current month -->
        <?php
    $query ="SELECT username,first_name,middle_name,last_name,email,area_code,phone_number,extension,COALESCE(number_of_reservations,0) as num_reservations,COALESCE(total_tools,0) as num_tools
			FROM
			(SELECT * 
			FROM (SELECT * FROM (SELECT * FROM customer) Q1 LEFT OUTER JOIN (SELECT count(*) AS number_of_reservations,customer_username AS customer_number_res FROM Rental AS R WHERE ( YEAR(R.start_date) = YEAR(CURRENT_DATE) AND MONTH(R.start_date) = MONTH(CURRENT_DATE)) GROUP BY customer_username) Q2 ON Q1.username=Q2.customer_number_res) Q3 LEFT OUTER JOIN (SELECT count(*) as total_tools, customer_username FROM RentalRentsTool AS RTT NATURAL JOIN Rental AS R WHERE ( YEAR(R.start_date) = YEAR(CURRENT_DATE) AND MONTH(R.start_date) = MONTH(CURRENT_DATE)) GROUP BY customer_username) Q4 ON Q3.username=Q4.customer_username ORDER BY total_tools) Q5
			NATURAL JOIN
			(SELECT area_code, phone_number,extension,username
			FROM phonenumber as PH
			WHERE PH.is_primary=1) Q6";
			
	$result = mysqli_query($db, $query);
    include('lib/show_queries.php'); 
	
    if ( !is_bool($result) && (mysqli_num_rows($result) > 0) ) {
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			print("<tr>");
			print("<td>".$row['username']."</td>");
			print("<th scope=\"row\">");
			print("<a href=\"view_profile.php?id=".$row['username']."\" target=\"_blank\"> View Profile </a></th>");
			print("<td>".$row['first_name']."</td>");
			print("<td>".$row['middle_name']."</td>");
			print("<td>".$row['last_name']."</td>");
			print("<td>".$row['email']."</td>");
			print("<td>".$row['area_code']."-".$row['phone_number']." ext:".$row['extension']."</td>");
			print("<td>".$row['num_reservations']."</td>");
			print("<td>".$row['num_tools']."</td>");
			print("</tr>");
		}
	}
		?>
    <!-- end for -->
  </tbody>
</table>

</div>
<div class="container"><!-- Re-open main page container-->
<?php include("partials/tail.php"); ?>