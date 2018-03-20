<?php 
include('lib/common.php');
include("partials/head.php"); ?>
<div class="row justify-content-between my-4">
  <div class="col-md-6">
    <!-- Report Header -->
    <h1 class="h2">Clerk Report</h1>
    <p class="lead mb-0">The list of clerks along with their details and total pickups and dropoffs for the current month.</p>
  </div>
  <div class="col-auto align-self-end">
    <a href="/report_clerk.php" class="btn btn-secondary">
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
      <th scope="col">Clerk ID</th>
      <th scope="col">First name</th>
	  <th scope="col">Middle name</th>
	  <th scope="col">Last name</th>
      <th scope="col">Email</th>
      <th scope="col">Hire date</th>
      <th scope="col">Pickups</th>
      <th scope="col">Dropoffs</th>
      <th scope="col">Combined total</th>
    </tr>
  </thead>
  <tbody>
    <!-- for each clerk in database -->
    <?php
    $query = "SELECT employee_number, first_name,middle_name, last_name,email,date_of_hire,COALESCE(number_of_pickups, 0 ) as number_of_pickups,COALESCE(number_of_dropoffs, 0 ) as number_of_dropoffs,COALESCE(number_of_pickups, 0 )+COALESCE(number_of_dropoffs, 0 ) AS total FROM
			(SELECT * FROM
			(SELECT username, employee_number,email,first_name,middle_name,last_name,date_of_hire
			FROM clerk) Q1
			LEFT OUTER JOIN
			(SELECT count(*) as number_of_pickups, pickup_clerk_username
			from rental as R
			WHERE (YEAR(R.start_date) = YEAR(CURRENT_DATE) AND MONTH(R.start_date) = MONTH(CURRENT_DATE)) and R.pickup_clerk_username IS NOT NULL
			GROUP BY R.pickup_clerk_username) Q2
			ON Q1.username=Q2.pickup_clerk_username) Q3
			LEFT OUTER JOIN
			(SELECT count(*) as number_of_dropoffs, dropoff_clerk_username
			from rental as R
			WHERE (YEAR(R.end_date) = YEAR(CURRENT_DATE) AND MONTH(R.end_date) = MONTH(CURRENT_DATE)) and R.dropoff_clerk_username IS NOT NULL
			GROUP BY R.dropoff_clerk_username) Q4
			ON Q3.username=Q4.dropoff_clerk_username";
			
	$result = mysqli_query($db, $query);
    include('lib/show_queries.php'); 
	
    if ( !is_bool($result) && (mysqli_num_rows($result) > 0) ) {
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			print("<tr>");
			print("<td>".$row['employee_number']."</td>");
			print("<td>".$row['first_name']."</td>");
			print("<td>".$row['middle_name']."</td>");
			print("<td>".$row['last_name']."</td>");
			print("<td>".$row['email']."</td>");
			print("<td>".$row['date_of_hire']."</td>");
			print("<td>".$row['number_of_pickups']."</td>");
			print("<td>".$row['number_of_dropoffs']."</td>");
			print("<td>".$row['total']."</td>");
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
