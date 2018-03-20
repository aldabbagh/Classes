<?php 

include('lib/common.php');
include('lib/error.php');
include('partials/head.php');


$id = mysqli_real_escape_string($db, $_REQUEST['id']);

			$query = "SELECT R.customer_username,  R.confirmation_number, tool_number, power_source, sub_option, sub_type, ROUND(price * 0.15*(TIMESTAMPDIFF(DAY,start_date,end_date) + 1),2) AS rental_price, ROUND(price * 0.4,2) AS deposit_price
						FROM ((RentalRentsTool AS RT NATURAL JOIN TOOL AS T) NATURAL JOIN RENTAL AS R)
						WHERE R.confirmation_number=".$id." ORDER BY R.confirmation_number";
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
		}	}

  ?>

<div class="row my-4">
  <div class="col-12">
    <!-- Tool Details -->
    <h1 class="h2">
      Reservation Details</br>
      <small class="text-muted">
        Confirmation #: <?php print($conf); ?>
      </small>
    </h1>

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

    <div class="card border-dark">
      <h2 class="card-header h3">
        Tools
      </h2>
      <ul class="list-group list-group-flush">
	  
	  <?php 
			foreach ($array as $key => $value){
			print("<li class=\"list-group-item\">");
			print("<a href=\"tool_details.php?id=".$key."\" target=\"_blank\">");
			print($value);
			print("</a></li>");
			}
	  ?>
      </ul>
    </div>

  </div>
</div>

<?php include('partials/tail.php');
