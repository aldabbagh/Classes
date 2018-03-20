<?php 

include('lib/common.php');
include('lib/error.php');
include('partials/head.php');

if (!isset($_SESSION['username'])) {
  header('Location: login.php');
  exit();
}

if ($_SESSION['type'] == "clerk"){
	print("Error: Clerks do not have access to this page. You will be redirected to the main menu");
	header( "refresh:10;url=index.php" );
	exit();
}

if (!isset($_SESSION['addedItems'])){
	print("Error: 11111A problem occured, you will be redirected to make reservation page");
	header( "refresh:10;url=make_reservation.php" );
	exit();
}

if (!isset($_REQUEST['id'])){
	print("Error: 222222A problem occured, you will be redirected to make reservation page");
	header( "refresh:10;url=make_reservation.php" );
	exit();
}

			$addedItems = $_SESSION['addedItems'];
			/* $startDate = explode(" ",$_POST['start-date'])[0];
			$endDate = explode(" ",$_POST['end-date'])[0]; */
			$startDate = explode(" " , $_SESSION['start-date'])[0] ;
			$startDateDate = new DateTime($startDate);
			$endDate = explode(" " , $_SESSION['end-date'])[0] ;
			$endDateDate = new DateTime($endDate);
			$diff = intval(($startDateDate->diff($endDateDate))->format('%a'));
			
			$start = true;
			$query = "";
			foreach ($addedItems as $key => $value){
				if($start){
					$query = "SELECT T.tool_number, price * 0.15 AS rental_price, price * 0.4 AS deposit_price, power_source, sub_option, sub_type
						FROM Tool AS T
						WHERE T.tool_number=".$value;
					$start = false;
				}
				else
					$query = $query . " OR T.tool_number=".$value;
			}
			$result = mysqli_query($db, $query);
			include('lib/show_queries.php'); 
			
			if (isset($result) && mysqli_num_rows($result) != 0){
				
				
				$trp = 0;
				$tdp = 0;
				while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
					$trp = $trp + round(round($row['rental_price'],2) * $diff,2);
					$tdp = $tdp + round($row['deposit_price'],2);
				}
				
			

			}
			unset($_SESSION['addedItems']);
			unset($_SESSION['start-date']);
			unset($_SESSION['end-date']);

//print(date_diff($startDate, $endDate));			

  ?>

<div class="row my-4">
  <div class="col-12">
    <!-- Customer Info -->
    <h1 class="h2">Reservation Confirmation</h2>
      <dl class="row">
	  <dt class="col-sm-3 col-lg-2">Reservation ID#</dt>
        <dd class="col-sm-9 col-lg-10">
          <?php print($_REQUEST['id']); ?>
        </dd>
        <dt class="col-sm-3 col-lg-2">Reservation Dates</dt>
        <dd class="col-sm-9 col-lg-10">
          <?php print($startDate . " - " . $endDate); ?>
        </dd>
		
		<dt class="col-sm-3 col-lg-2">Number of Days Rented</dt>
        <dd class="col-sm-9 col-lg-10">
          <?php print($diff); ?>
        </dd>

        <dt class="col-sm-3 col-lg-2">Total Deposit Price</dt>
        <dd class="col-sm-9 col-lg-10">
           <?php print(round($tdp,2)); ?>
        </dd>

        <dt class="col-sm-3 col-lg-2">Total Rental Price</dt>
        <dd class="col-sm-9 col-lg-10">
           <?php print(round($trp,2)); ?>
        </dd>

      </dl>
  </div>
</div>

<h1 class="h2">Tools</h2>

</div><!-- Close main page container-->
<div class="container-fluid">

  <!-- Tools -->
  <table class="table table-sm table-hover">
    <thead class="thead-light">
      <tr>
        <th scope="col">ID</th>
        <th scope="col">Description</th>
        <th scope="col">Deposit Price</th>
        <th scope="col">Rental Price</th>
      </tr>
    </thead>
    <tbody>
      <!-- for each tool in reservation -->
	  <?php 
			mysqli_data_seek($result,0);
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				print("<tr>");
				print("<th scope=\"row\">");
				print("<a href=\"tool_details.php?id=".$row['tool_number']."\" target=\"_blank\">".$row['tool_number']."</a>");
				print("</th>");
				print("<td>");
				if($row['power_source'] == "manual")
					print($row['sub_option'] . ' ' . $row['sub_type']);
				else
					print($row['power_source']. ' ' . $row['sub_option'] . ' ' . $row['sub_type']);
				print("</td>");
				
				$dp = round($row['deposit_price'],2);
				print("<td>".$dp."</td>");
				$rp = round(round($row['rental_price'],2) * $diff,2 );
				print("<td>".$rp."</td>");
				print("</tr>");
			}
			
			print("<tr>");
			print("<th scope=\"row\" colspan=\"2\">Totals</th>");
			print("<th scope=\"col\">".$tdp."</td>");
			print("<th scope=\"col\">".$trp."</td>");
			print("</tr>");
			
			print("<tr>");
			print("<th scope=\"row\" colspan=\"3\">Total Due</th>");
			print("<th scope=\"col\">".($trp - $tdp)."</td>");
			print("</tr>");
			
		
		
		?>
    </tbody>
  </table>

</div>

<div class="container"><!-- Re-open main page container-->

<?php include('partials/tail.php'); ?>
