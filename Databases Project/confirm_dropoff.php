<?php 

include('lib/common.php');
include('lib/error.php');
include('partials/head.php');

$id = mysqli_real_escape_string($db, $_REQUEST['id']);

			$query = "SELECT dropoff_clerk_username, C.first_name AS cFirstName, C.middle_name As cMiddleName,
			C.last_name AS cLastName, Cl.first_name AS clFirstName, Cl.middle_name As clMiddleName,
			Cl.last_name AS clLastName, card_number, start_date, end_date, (TIMESTAMPDIFF(DAY,start_date,end_date) + 1) as timediff
			FROM ((Rental AS R INNER JOIN Customer AS C ON R.customer_username = C.username) INNER JOIN Clerk AS Cl on 
			Cl.username = dropoff_clerk_username)
			WHERE R.confirmation_number = ".$id ;

			$result = mysqli_query($db, $query);
			include('lib/show_queries.php'); 
			if (!empty($result)) {
				array_push($error_msg,  "SELECT ERROR:Failed to find friends ... <br>" . __FILE__ ." line:". __LINE__ );
			}
			
			if (isset($result)) {
				
			$rowRental = mysqli_fetch_array($result, MYSQLI_ASSOC);

			}	

  ?>

<div class="row my-4">
  <div class="col-12">
    <!-- Customer Info -->
    <h1 class="h2">Reservation Confirmation</h2>
      <dl class="row">
        <dt class="col-sm-3 col-lg-2 text-info">Confirmation #</dt>
        <dd class="col-sm-9 col-lg-10">
          <?php print($id); ?>
        </dd>
		
		<dt class="col-sm-3 col-lg-2 text-info">Dropoff Clerk</dt>
        <dd class="col-sm-9 col-lg-10">
          <?php print($rowRental['clFirstName'] . ' ' . $rowRental['clMiddleName'] . ' ' . $rowRental['clLastName']); ?>
        </dd>

        <dt class="col-sm-3 col-lg-2">Customer Name</dt>
        <dd class="col-sm-9 col-lg-10">
           <?php print($rowRental['cFirstName'] . ' ' . $rowRental['cMiddleName'] . ' ' . $rowRental['cLastName']); ?>
        </dd>

        <!-- If exists-->
        <dt class="col-sm-3 col-lg-2">Start Date</dt>
        <dd class="col-sm-9 col-lg-10">
          <?php print($rowRental['start_date']) ?>
        </dd>

        <!-- If exists-->
        <dt class="col-sm-3 col-lg-2">End Date</dt>
        <dd class="col-sm-9 col-lg-10">
          <?php print($rowRental['end_date']) ?>
		  

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
	  
		$query = "SELECT tool_number, power_source, sub_option, sub_type, ROUND(price,2) as price
		FROM (RentalRentsTool RRT NATURAL JOIN Tool)
		WHERE RRT.confirmation_number = ".$id ;
		
		$result = mysqli_query($db, $query);
		include('lib/show_queries.php'); 
		
		if (mysqli_affected_rows($db) == -1) {
			array_push($error_msg,  "SELECT ERROR:Failed to find friends ... <br>" . __FILE__ ." line:". __LINE__ );
		}
		
		if (isset($result)) {
			$trp = 0;
			$tdp = 0;
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
				
				$dp = $row['price'] * 0.40;
				$tdp = $tdp + $dp;
				print("<td>".$dp."</td>");
				$rp = $row['price'] * 0.15 * $rowRental['timediff'];
				$trp = $trp + $rp;
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
			
		}
		
		?>
    </tbody>
  </table>

</div>

      <button type="submit" class="btn btn-primary" onclick="window.print();">
        Print Contract
      </button>
	  
	  <br>
	  <br>
	  <h1 class="h2">Signatures</h2>
	  <br>
	  <input type="text" style="border: 0 ; border-bottom: 1px solid #000;" disabled />
	  <br>
	  Customer:
	  <br>
	  <?php print($rowRental['cFirstName'] . ' ' . $rowRental['cMiddleName'] . ' ' . $rowRental['cLastName'] . NEWLINE);
	  print("Date: ".explode(" ",$rowRental['end_date'])[0]);	  ?>
	  <br>
	  <br>
	  	  <input type="text" style="border: 0 ; border-bottom: 1px solid #000;" disabled />
	  <br>
	  Clerk: 
	  <br>
	  <?php print($rowRental['clFirstName'] . ' ' . $rowRental['clMiddleName'] . ' ' . $rowRental['clLastName'] . NEWLINE);
		print("Date: ".explode(" ",$rowRental['end_date'])[0]);	  ?>
	  <br>
	  <br>
<div class="container"><!-- Re-open main page container-->

<?php include('partials/tail.php'); ?>
