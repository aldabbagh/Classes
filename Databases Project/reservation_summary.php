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
	print("Error: There are no items in your cart, you will be redirected to make reservation page");
	header( "refresh:10;url=make_reservation.php" );
	exit();
}

if (isset($_POST['reset'])){
	print("You chose to cancel this cart, you will be redirected to make reservation page");
	unset($_SESSION['addedItems']);
	header( "refresh:10;url=make_reservation.php" );
	exit();
}

if (!isset($_POST['start-date']) || !isset($_POST['end-date'])){
	print("Error: There were no dates set. Please go to make reservation page and set the dates");
	header( "refresh:10;url=make_reservation.php" );
	exit();	
}

if (isset($_POST['submit'])){
	$count = 0;
	$start_date = $_POST['start-date'] . " 00:00:00";
	$end_date = $_POST['end-date'] . " 00:00:00";
	$addedItems3 = $_SESSION['addedItems'];
	$query3 = "SELECT T.tool_number
					FROM Tool AS T
					WHERE T.tool_number NOT IN 
					(SELECT tol.tool_number
					FROM Tool as tol NATURAL JOIN rentalrentstool NATURAL JOIN rental as R 
					WHERE (R.start_date<='".$end_date."' AND R.start_date>='".$start_date."') OR (R.end_date >='".$start_date."' AND R.end_date<='".$end_date."') OR (R.start_date<='".$start_date."' AND R.end_date >='".$end_date."'))";
					
	$result3 = mysqli_query($db, $query3);
	
	if (isset($result3) && mysqli_num_rows($result3) != 0){
		
		while ($row3 = mysqli_fetch_array($result3, MYSQLI_ASSOC)){
		foreach ($addedItems3 as $key3 => $value3){
			if($value3 == $row3['tool_number'])
				$count++;
			}
		
		}
		
	}
	if($count != count($addedItems3) || $count == 0){
		
	print("Error: The items you selected are no longer available during the time period you selected, you will be redirected to make reservation page");
	unset($_SESSION['addedItems']);
	header( "refresh:5;url=make_reservation.php" );
	exit();
	}
	else{
		$queryR = "INSERT INTO rental (customer_username,start_date, end_date)
		VALUES ('". $_SESSION['username'] . "','".$start_date."', '".$end_date."')";
		$queryID = mysqli_query($db, $queryR);
		if($queryID){
			$conf = mysqli_insert_id($db);
			$success4 = true;
			foreach ($addedItems3 as $key4 => $value4){
				$queryRRT = "INSERT INTO RentalRentsTool
				VALUES (".$conf.", '".$_SESSION['username']."', ".$value4.")";
				$queryID = mysqli_query($db, $queryRRT);
				if($queryID){
					$_SESSION['start-date'] = $start_date;
					$_SESSION['end-date'] = $end_date;

				}
				else{
					$success4 = false;
				}
				
			}
			
			print("Redirecting!");
			header( "refresh:1;url=reservation_confirmation.php?id=". $conf);
			exit();
		}	
		
	else{
		print("Failure");
	}
	
	}
}

			$addedItems = $_SESSION['addedItems'];
			/* $startDate = explode(" ",$_POST['start-date'])[0];
			$endDate = explode(" ",$_POST['end-date'])[0]; */
			$startDate = $_POST['start-date'] ;
			$startDateDate = new DateTime($startDate);
			$endDate = $_POST['end-date'];
			$endDateDate = new DateTime($endDate);
			$diff = intval(($startDateDate->diff($endDateDate))->format('%a'));
			// shows the total amount of days (not divided into years, months and days like above)
			
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
			
			if (isset($result) && mysqli_num_rows($result) != 0){
				
				
				$trp = 0;
				$tdp = 0;
				while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
					$trp = $trp + round(round($row['rental_price'],2) * $diff,2);
					$tdp = $tdp + round($row['deposit_price'],2);
				}
				
			

			}

//print(date_diff($startDate, $endDate));			

  ?>

<div class="row my-4">
  <div class="col-12">
    <!-- Customer Info -->
    <h1 class="h2">Reservation Summary</h2>
      <dl class="row">
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

<form id="submitReservation" action="reservation_summary.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="submit" value="yes"></input>
<?php					
		
		foreach ($_POST as $key => $value){
			if($key == "end-date" || $key == "start-date")
			print("<input type=\"hidden\" name=\"". $key . "\" value=\"". $value . "\">");
		}
?>
<button type="submit" name="submittingReservation" class="btn btn-primary" style="width: 14em;"> Calculate Total</button>
</form>
<br>
<form id="cancelReservation" action="reservation_summary.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="reset" value="yes"></input>
<button type="submit" name="cancellingReservation" class="btn btn-danger" style="width: 14em;"> Reset</button>
</form>
<div class="container"><!-- Re-open main page container-->

<?php include('partials/tail.php'); ?>
