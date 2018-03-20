<?php 
include('lib/common.php');
include("partials/head.php"); ?>
<div class="row justify-content-between my-4">
  <div class="col-md-6">
    <!-- Report Header -->
    <h1 class="h2">Tool Inventory Report</h1>
    <p class="lead mb-0">Lists all tools along with their cost and total profit for all time.</p>
  </div>
  <div class="col-auto align-self-end">
    <a href="/report_tool.php" class="btn btn-secondary">
      Reload results
    </a>
  </div>
</div>

<!-- Filtering -->
<form class="form-row"
  action="/report_tool.php"
  method="post"
  enctype="multipart/form-data">
  <!-- Tool type -->
  <fieldset class="col-xl-7 form-group">
    <legend class="h6">Tool type</legend>
    <div class="form-check form-check-inline">
      <label class="form-check-label">
        <input class="form-check-input" id="tool-type-all"
          type="radio"
          name="tool-type"
          value="all"
          checked> All tools
      </label>
    </div>
    <div class="form-check form-check-inline">
      <label class="form-check-label">
        <input class="form-check-input" id="tool-type-hand"
          type="radio"
          name="tool-type"
          value="hand"> Hand tool
      </label>
    </div>
    <div class="form-check form-check-inline">
      <label class="form-check-label">
        <input class="form-check-input" id="tool-type-garden"
          type="radio"
          name="tool-type"
          value="garden"> Garden tool
      </label>
    </div>
    <div class="form-check form-check-inline">
      <label class="form-check-label">
        <input class="form-check-input" id="tool-type-ladder"
          type="radio"
          name="tool-type"
          value="ladder"> Ladder
      </label>
    </div>
    <div class="form-check form-check-inline">
      <label class="form-check-label">
        <input class="form-check-input" id="tool-type-power"
          type="radio"
          name="tool-type"
          value="power"> Power tool
      </label>
    </div>
  </fieldset>

  <!-- Keyword -->
  <div class="col-lg-5 col-xl-4">
    <div class="form-group">
      <label for="keyword">Custom search</label>
      <input class="form-control" id="keyword"
        name="keyword"
        type="text"
        placeholder="Keywords">
    </div>
  </div>

  <!-- Search -->
  <div class="col-lg-auto col-xl-1 align-self-end">
    <div class="form-group">
      <button type="submit" class="btn btn-primary btn-block">
        Search
      </button>
    </div>
  </div>
</form>

</div><!-- Close main page container-->
<div class="container-fluid">

<!-- Report table-->
<table class="table table-sm table-hove">
  <thead class="thead-light">
    <tr>
      <th scope="col">Tool number</th>
      <th scope="col">Status</th>
      <th scope="col">Return date</th>
      <th scope="col">Description</th>
      <th scope="col">Rental profit</th>
      <th scope="col">Total cost</th>
      <th scope="col">Total profit</th>
    </tr>
  </thead>
  <tbody>
    <!-- for each tool in database -->
    <?php
	$whereString ="";
	$keyword = $_POST['keyword'];
	if($_POST['keyword']){
		$subOption = $keyword;
		if($_POST['tool-type']=="hand"){
			$whereString = "WHERE sub_option='".$keyword."' AND (sub_type='screwdriver' or  sub_type='socket' or sub_type='ratchet' or sub_type='wrench' or sub_type='plier' or sub_type='hammer' or sub_type='gun')";
		}if($_POST['tool-type']=='garden'){
			$whereString = "WHERE sub_option='".$keyword."' AND (sub_type='digging' or  sub_type='prunning' or sub_type='rake' or sub_type='wheelbarrow' or sub_type='striking')";
		}if($_POST['tool-type']=='ladder'){
			$whereString = "WHERE sub_option='".$keyword."' AND (sub_type='stepladder' or  sub_type='straightladder')";
		}if($_POST['tool-type']=='power'){
			$whereString = "WHERE sub_option='".$keyword."' AND (sub_type='powerdrill' or  sub_type='powersaw' or sub_type='powersander' or sub_type='poweraircompressor' or sub_type='powermixer' or sub_type='powergenerator')";
		}if($_POST['tool-type']=='all'){
			$whereString = "WHERE sub_option='".$keyword."'";
		}
	}else{
		if($_POST['tool-type']=='hand'){
			$whereString = "WHERE (sub_type='screwdriver' or  sub_type='socket' or sub_type='ratchet' or sub_type='wrench' or sub_type='plier' or sub_type='hammer' or sub_type='gun')";
		}
		if($_POST['tool-type']=='garden'){
			$whereString = "WHERE (sub_type='digging' or  sub_type='prunning' or sub_type='rake' or sub_type='wheelbarrow' or sub_type='striking')";
		}
		if($_POST['tool-type']=='ladder'){
			$whereString = "WHERE (sub_type='stepladder' or  sub_type='straightladder')";
		}
		if($_POST['tool-type']=='power'){
			$whereString = "WHERE (sub_type='powerdrill' or  sub_type='powersaw' or sub_type='powersander' or sub_type='poweraircompressor' or sub_type='powermixer' or sub_type='powergenerator')";
		}
	}
    $query ="SELECT tool_number,sub_type, sub_option, width_diameter,length,price,weight,material,power_source,manufacturer,date,status,ROUND(COALESCE(days_rented,0)*0.15*price,2) AS rental_profit, ROUND(price,2) as total_cost, ROUND((COALESCE(days_rented,0)*0.15*price)-price,2) as total_profit
			FROM
			(SELECT tool_number,sub_type, sub_option, width_diameter,length,price,weight,material,power_source,manufacturer,COALESCE(end_date,'') as date,COALESCE(status,'available') as status
			FROM
			(SELECT *
			FROM tool)Q1
			LEFT OUTER JOIN
			(SELECT DISTINCT(tool_number) as vrr,end_date,'rented' as status
			FROM RentalRentsTool AS RTT NATURAL JOIN Rental AS R
			WHERE R.end_date > CURRENT_DATE AND CURRENT_DATE>=R.start_date)Q2
			on Q1.tool_number = Q2.vrr) Q3
			LEFT OUTER JOIN
			(SELECT count(*),tool_number as profited_tool,SUM(DATEDIFF(R.end_date, R.start_date)) as days_rented
			FROM RentalRentsTool AS RTT NATURAL JOIN Rental AS R
			WHERE R.start_date <= CURRENT_DATE
			GROUP BY profited_tool)Q4
			on Q4.profited_tool = Q3.tool_number
			".$whereString."";
			
	$result = mysqli_query($db, $query);
    include('lib/show_queries.php'); 
	
    if ( !is_bool($result) && (mysqli_num_rows($result) > 0) ) {
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			print("<tr>");
			print("<td>".$row['tool_number']."</td>");
			if ($row['status']=='rented'){
				print("<td style='color: black; background-color:yellow;'>".$row['status']."</td>");
			}else{
				print("<td style='color: black; background-color:#78F878;'>".$row['status']."</td>");
			}
			print("<td>".$row['date']."</td>");
			print("<th scope=\"row\">");
			print("<a href=\"tool_details.php?id=".$row['tool_number']."\" target=\"_blank\">".$row['sub_option']." ".$row['sub_type']."</a></th>");
			print("<td>".$row['rental_profit']."</td>");
			print("<td>".$row['total_cost']."</td>");
			print("<td>".$row['total_profit']."</td>");
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