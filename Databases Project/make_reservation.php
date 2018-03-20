<?php 
include("lib/common.php");
include("partials/head.php"); 
//unset($_SESSION['addedItems']);?>

<div class="row my-4">
  <div class="col-12">
    <h1 class="h2">Make Reservation</h2>
<?php include("partials/customer_tool_search.php"); ?>
<hr>
<div class="container">	
<h2 class="h3">Tools Added to reservation</h2>
</div>
<div class="container-fluid">

<!-- Reserved Tools -->
<table class="table table-sm table-hover">
  <thead class="thead-light">
    <tr>
      <th scope="col">ID</th>
      <th scope="col">Description</th>
      <th scope="col">Rental Price</th>
      <th scope="col">Deposit Price</th>
      <th scope="col">Remove</th>
    </tr>
  </thead>
  <tbody>
    <!-- Generated client-side -->
	
	<?php
	if(isset($_SESSION['addedItems'])){
		$addedItems = $_SESSION['addedItems'];
		if(isset($_POST['addingItems'])){
			$addingItems = $_POST['addingItems'];
			if(!in_array($addingItems, $addedItems) && !(count($addedItems) >= 10)){
				$addedItems[$addingItems] = $addingItems;
				unset($_SESSION['addedItems']);
				$_SESSION['addedItems'] = $addedItems;
			}
			
			else
				print("Error: You cannot add more than 10 items");
		
		}
		
		if(isset($_POST['removingItems'])){
			$removingItems = $_POST['removingItems'];
			if(in_array($removingItems, $addedItems)){
				unset($addedItems[$removingItems]);
				unset($_SESSION['addedItems']);
				if(!count($addedItems) <= 0)
					$_SESSION['addedItems'] = $addedItems;
			}
		
		}
		if(count($addedItems) >=0 && (count($addedItems) <= 11)){
			$start = true;
			$query1 = "";
			foreach ($addedItems as $key => $value){
				if($start){
					$query1 = "SELECT T.tool_number, price * 0.15 AS rental_price, price * 0.4 AS deposit_price, power_source, sub_option, sub_type
						FROM Tool AS T
						WHERE T.tool_number=".$value;
					$start = false;
				}
				else
					$query1 = $query1 . " OR T.tool_number=".$value;
			}
			$result1 = mysqli_query($db, $query1);
			include('lib/show_queries.php'); 
			
			if (isset($result1) && mysqli_num_rows($result1) != 0 ){
				
				print("<form id=\"removing\" action=\"make_reservation.php\" method=\"post\" enctype=\"multipart/form-data\">");
				foreach ($_POST as $key1 => $value1){
					if($key1 == "powersource" || $key1 == "subtype" || $key1 == "type" || $key1 == "keyword" || $key1 == "end-date" || $key1 == "start-date")
						print("<input type=\"hidden\" name=\"". $key1 . "\" value=\"". $value1 . "\">");
				}
				while ($row1 = mysqli_fetch_array($result1, MYSQLI_ASSOC)){
					print("<tr>");
					print("<th scope=\"row\">");
					print($row1['tool_number']);
					print("</th>");
					if($row1['power_source'] == "manual")	
						$description1 = $row1['sub_option'] . ' ' . $row1['sub_type'];
					else
						$description1 = $row1['power_source']. ' ' . $row1['sub_option'] . ' ' . $row1['sub_type'];
					print("<td><a href=\"tool_details.php?id=".$row1['tool_number']."\" target=\"_blank\">" . $description1 . " </a></td>");
					print("<td>".round($row1['rental_price'],2)."</td>");
					print("<td>".round($row1['deposit_price'],2)."</td>");
					print("<td><button type=\"submit\" name=\"removingItems\" value =\"" . $row1['tool_number'] . "\" class=\"btn btn-secondary\" style=\"width: 14em;\">Remove From Reservation</button> </td>");
					print("</tr>");
					
				}
					
				print("</form>");}
				
				else{
					
				}
			
			}
	}
		
	
	elseif(isset($_POST['addingItems'])){
		$addingItems = $_POST['addingItems'];
		$addedItems[$addingItems] = $addingItems;
		$_SESSION['addedItems'] = $addedItems;
		$query1 = "SELECT T.tool_number, price * 0.15 AS rental_price, price * 0.4 AS deposit_price, power_source, sub_option, sub_type
					FROM Tool AS T
					WHERE T.tool_number=".$addingItems;
		
		$result1 = mysqli_query($db, $query1);
		include('lib/show_queries.php'); 
		
		if (isset($result1) && mysqli_num_rows($result1) != 0 ){

			print("<form id=\"removing\" action=\"make_reservation.php\" method=\"post\" enctype=\"multipart/form-data\">");
			foreach ($_POST as $key1 => $value1){
				if($key1 == "powersource" || $key1 == "subtype" || $key1 == "type" || $key1 == "keyword" || $key1 == "end-date" || $key1 == "start-date")
					print("<input type=\"hidden\" name=\"". $key1 . "\" value=\"". $value1 . "\">");
			}
			while ($row1 = mysqli_fetch_array($result1, MYSQLI_ASSOC)){
				print("<tr>");
				print("<th scope=\"row\">");
				print($row1['tool_number']);
				print("</th>");
				if($row1['power_source'] == "manual")	
					$description1 = $row1['sub_option'] . ' ' . $row1['sub_type'];
				else
					$description1 = $row1['power_source']. ' ' . $row1['sub_option'] . ' ' . $row1['sub_type'];
				print("<td><a href=\"tool_details.php?id=".$row1['tool_number']."\" target=\"_blank\">" . $description1 . " </a></td>");
				print("<td>".round($row1['rental_price'],2)."</td>");
				print("<td>".round($row1['deposit_price'],2)."</td>");
				print("<td><button type=\"submit\" name=\"removingItems\" value =\"" . $row1['tool_number'] . "\" class=\"btn btn-danger\" style=\"width: 14em;\">Remove From Reservation</button> </td>");
				print("</tr>");
				
			}
				
			print("</form>");}
		
	}
	else{
		
	} ?>
	
  </tbody>
</table>

</div>

<form id="submitReservation" action="reservation_summary.php" method="post" enctype="multipart/form-data">
<?php					
		
		foreach ($_POST as $key => $value){
			if($key == "end-date" || $key == "start-date")
			print("<input type=\"hidden\" name=\"". $key . "\" value=\"". $value . "\">");
		}
?>
<button type="submit" name="submittingReservation" class="btn btn-primary" style="width: 14em;"> Calculate Total</button>
    <hr>
    <h2 class="h3">Available Tools</h2>
</form>
  </div>
</div>

<div class="container-fluid">

<!-- Search Results -->
<table class="table table-sm table-hover">
  <thead class="thead-light">
    <tr>
      <th scope="col">ID</th>
      <th scope="col">Description</th>
      <th scope="col">Rental Price</th>
      <th scope="col">Deposit Price</th>
	  <th scope="col">Add</th>
    </tr>
  </thead>
  <tbody>
    <!-- for each unreserved tool based on filters -->
	
	<?php 
	
			$success = true;
			if(!$_POST['start-date']){
				print("There was no start date provided" . NEWLINE);
				$success = false;
			}
			
			if(!$_POST['end-date']){
				print("There was no end date provided" . NEWLINE);
				$success = false;
			}
			
			if($success){
				function validateDate($date, $format = 'Y-m-d H:i:s'){
					$d = DateTime::createFromFormat($format, $date);
					return $d && $d->format($format) == $date;
				}
				
				$start_date = $_POST['start-date'];
				$start_date = $start_date . " 00:00:00";
				$end_date = $_POST['end-date'];
				$end_date = $end_date . " 00:00:00";
				if(!(validateDate($start_date) && validateDate($end_date) && $start_date < $end_date && $start_date > date('Y-m-d H:i:s') && $end_date > date('Y-m-d H:i:s') ))
					print("The dates provided are invalid, please check them and try again" . NEWLINE);
				else{
					$whereString = "";
					$keyword = $_POST['keyword'];
					if($_POST['keyword'] && $_POST['keyword']){
						$subOption = $keyword;
						if($_POST['type']=="hand"){
							$whereString = "WHERE sub_option='".$keyword."' AND (sub_type='screwdriver' or  sub_type='socket' or sub_type='ratchet' or sub_type='wrench' or sub_type='plier' or sub_type='hammer' or sub_type='gun')";
						}if($_POST['type']=='garden'){
							$whereString = "WHERE sub_option='".$keyword."' AND (sub_type='digging' or  sub_type='prunning' or sub_type='rake' or sub_type='wheelbarrow' or sub_type='striking')";
						}if($_POST['type']=='ladder'){
							$whereString = "WHERE sub_option='".$keyword."' AND (sub_type='stepladder' or  sub_type='straightladder')";
						}if($_POST['type']=='power'){
							$whereString = "WHERE sub_option='".$keyword."' AND (sub_type='powerdrill' or  sub_type='powersaw' or sub_type='powersander' or sub_type='poweraircompressor' or sub_type='powermixer' or sub_type='powergenerator')";
						}if($_POST['type']=='all'){
							$whereString = "WHERE sub_option='".$keyword."'";
						}
					}else{
						if($_POST['type']=='hand'){
							$whereString = "WHERE (sub_type='screwdriver' or  sub_type='socket' or sub_type='ratchet' or sub_type='wrench' or sub_type='plier' or sub_type='hammer' or sub_type='gun')";
						}
						if($_POST['type']=='garden'){
							$whereString = "WHERE (sub_type='digging' or  sub_type='prunning' or sub_type='rake' or sub_type='wheelbarrow' or sub_type='striking')";
						}
						if($_POST['type']=='ladder'){
							$whereString = "WHERE (sub_type='stepladder' or  sub_type='straightladder')";
						}
						if($_POST['type']=='power'){
							$whereString = "WHERE (sub_type='powerdrill' or  sub_type='powersaw' or sub_type='powersander' or sub_type='poweraircompressor' or sub_type='powermixer' or sub_type='powergenerator')";
						}	
					}
					if($_POST['subtype'] && $_POST['subtype'] != ""){
						if($whereString == "")
							$whereString = " WHERE (sub_type='" . $_POST['subtype'] . "')";
						else
							$whereString = $whereString . " AND (sub_type='" . $_POST['subtype'] . "')";
						
					}
					
					if($_POST['powersource'] && $_POST['powersource'] != ""){
						if($whereString == "")
							$whereString = " WHERE (power_source='" . $_POST['powersource'] . "')";
						else
							$whereString = $whereString . " AND (power_source='" . $_POST['powersource'] . "')";
						
					}
					
					if($whereString == ""){
						$query = "SELECT T.tool_number, price * 0.15 AS rental_price, price * 0.4 AS deposit_price, power_source, sub_option, sub_type
					FROM Tool AS T
					WHERE T.tool_number NOT IN 
					(SELECT tol.tool_number
					FROM Tool as tol NATURAL JOIN rentalrentstool NATURAL JOIN rental as R 
					WHERE (R.start_date<='".$end_date."' AND R.start_date>='".$start_date."') OR (R.end_date >='".$start_date."' AND R.end_date<='".$end_date."') OR (R.start_date<='".$start_date."' AND R.end_date >'".$end_date."'))";
					}
					
					else {
						$query = "SELECT T.tool_number, price * 0.15 AS rental_price, price * 0.4 AS deposit_price, power_source, sub_option, sub_type
					FROM Tool AS T "
					. $whereString . " AND T.tool_number NOT IN 
					(SELECT tol.tool_number
					FROM Tool as tol NATURAL JOIN rentalrentstool NATURAL JOIN rental as R 
					WHERE (R.start_date<='".$end_date."' AND R.start_date>='".$start_date."') OR (R.end_date >='".$start_date."' AND R.end_date<='".$end_date."') OR (R.start_date<='".$start_date."' AND R.end_date >='".$end_date."'))";
					
					}
					$result = mysqli_query($db, $query);
					include('lib/show_queries.php'); 
					
					if (isset($result) && mysqli_num_rows($result) != 0 ){
						
					print("<form id=\"adding\" action=\"make_reservation.php\" method=\"post\" enctype=\"multipart/form-data\">");
					foreach ($_POST as $key => $value){
						if($key == "powersource" || $key == "subtype" || $key == "type" || $key == "keyword" || $key == "end-date" || $key == "start-date")
						print("<input type=\"hidden\" name=\"". $key . "\" value=\"". $value . "\">");
					}
					while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
					$print = true;
					if(isset($_SESSION['addedItems'])){
						$addedItems3 = $_SESSION['addedItems'];
						
						foreach ($addedItems3 as $key3 => $value3){
								if($value3 == $row['tool_number'])
									$print = false;
						}
					}
						if($print){
							print("<tr>");
							print("<th scope=\"row\">");
							print($row['tool_number']);
							print("</th>");
							if($row['power_source'] == "manual")	
								$description = $row['sub_option'] . ' ' . $row['sub_type'];
							else
								$description = $row['power_source']. ' ' . $row['sub_option'] . ' ' . $row['sub_type'];
							print("<td><a href=\"tool_details.php?id=".$row['tool_number']."\" target=\"_blank\">" . $description . " </a></td>");
							print("<td>".round($row['rental_price'],2)."</td>");
							print("<td>".round($row['deposit_price'],2)."</td>");
							print("<td><button type=\"submit\" name=\"addingItems\" value =\"" . $row['tool_number'] . "\" class=\"btn btn-secondary\" style=\"width: 14em;\">Add to Reservation</button> </td>");
							print("</tr>");
						}
          
				}
				
				print("</form>");}}
				
			}?>
  </tbody>
</table>

</div>

<!-- Close main page container-->



<div class="container"><!-- Re-open main page container-->
<?php include("partials/tail.php"); ?>