<?php
include('lib/common.php');
include("lib/error.php");
include('partials/head.php');

if ($_SESSION['type'] == "clerk" &&  !isset($_REQUEST['id'])) {
	array_push($error_msg,  "You did not provide a user to view their profile");
	header( "refresh:10;url=index.php" );
}

else{
  $username = $_SESSION['username'];
  if($_SESSION['type'] == "clerk")
	  $username = $_REQUEST['id'];
    // ERROR: demonstrating SQL error handlng, to fix
    // replace 'sex' column with 'gender' below:
    $query = "SELECT email, first_name, middle_name, last_name, street, state, city, 9_digit_zip FROM Customer WHERE username='".$username."'";
  $result = mysqli_query($db, $query);

    if ( !is_bool($result) && (mysqli_num_rows($result) > 0) ) {
    $userInfo = mysqli_fetch_array($result, MYSQLI_ASSOC);

    $query = "SELECT area_code, phone_number, extension, phone_type FROM PhoneNumber WHERE username='".$username."'";
    $result = mysqli_query($db, $query);

    if ( !is_bool($result) && (mysqli_num_rows($result) > 0) ) {
      $home = "";
      $work = "";
      $cell = "";

      while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

        if($row['phone_type'] == "home"){
          $home = $row['area_code'] . "-" . $row['phone_number'] . " ext:" . $row['extension'] ;
        }

        else if($row['phone_type'] == "work"){
          $work = $row['area_code'] . "-" . $row['phone_number'] . " ext:" . $row['extension'] ;
        }

        else {
          $cell = $row['area_code'] . "-" . $row['phone_number'] . " ext:" . $row['extension'] ;
        }

      }

      $query = "SELECT R.confirmation_number, start_date, end_date, pickup_clerk_username, dropoff_clerk_username, power_source, sub_option, sub_type, price * 0.15*(TIMESTAMPDIFF(DAY,start_date,end_date)) AS rental_price, price * 0.4 AS deposit_price, (TIMESTAMPDIFF(DAY,start_date,end_date)) as timediff
            FROM ((RentalRentsTool AS RT NATURAL JOIN TOOL AS T) NATURAL JOIN RENTAL AS R)
            WHERE R.customer_username='".$username."' ORDER BY start_date DESC, R.confirmation_number";
      $result = mysqli_query($db, $query);

      if (mysqli_affected_rows($db) == -1) {
        array_push($error_msg,  "SELECT ERROR:Failed to find friends ... <br>" . __FILE__ ." line:". __LINE__ );
      }

    } else {
        array_push($error_msg,  "Query ERROR: Failed to get User profile...<br>" . __FILE__ ." line:". __LINE__ );
    }

    } else {
        array_push($error_msg,  "ERROR: username (" . $username . ") was not found in the database" );
}}

?>




<div class="row my-4">
  <div class="col-12">
    <!-- Customer Info -->
    <h1 class="h2">Customer Info</h2>
      <dl class="row">
        <dt class="col-sm-3 col-lg-2">Email</dt>
        <dd class="col-sm-9 col-lg-10">
          <?php print $userInfo['email']; ?>
        </dd>

        <dt class="col-sm-3 col-lg-2">Full Name</dt>
        <dd class="col-sm-9 col-lg-10">
      <?php print $userInfo['first_name'] . ' ' . $userInfo['middle_name'] . ' ' . $userInfo['last_name']; ?>
        </dd>

        <!-- If exists-->
        <dt class="col-sm-3 col-lg-2">Home Phone</dt>
        <dd class="col-sm-9 col-lg-10" >
          <?php print $home; ?>
        </dd>

        <!-- If exists-->
        <dt class="col-sm-3 col-lg-2">Work Phone</dt>
        <dd class="col-sm-9 col-lg-10">
          <?php print $work; ?>
        </dd>

        <!-- If exists-->
        <dt class="col-sm-3 col-lg-2">Cell Phone</dt>
        <dd class="col-sm-9 col-lg-10">
          <?php print $cell; ?>
        </dd>

        <dt class="col-sm-3 col-lg-2">Address</dt>
        <dd class="col-sm-9 col-lg-10">
          <?php print $userInfo['street']; ?>,
          <?php print $userInfo['city']; ?>,
          <?php print $userInfo['state']; ?>
          <?php print $userInfo['9_digit_zip']; ?>
        </dd>
      </dl>
  </div>
</div>

<h1 class="h2">Reservations</h2>

</div><!-- Close main page container-->
<div class="container-fluid">


  <!-- Reservations -->
  <table class="table table-sm table-hover">

    <thead class="thead-light">
      <tr>
        <th scope="col">ID</th>
        <th scope="col">Tools</th>
        <th scope="col">Start Date</th>
        <th scope="col">End Date</th>
        <th scope="col">Pickup Clerk</th>
        <th scope="col">Dropoff Clerk</th>
        <th scope="col">Number of Days</th>
        <th scope="col">Total Deposit Price</th>
        <th scope="col">Total Rental Price</th>
      </tr>
    </thead>
    <tbody>
      <!-- for each reservation -->

      <tr>
    <?php
    if (isset($result)) {
      $start = true;
      $rowCount = mysqli_num_rows($result);
      $count = 0;

      while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
        if($start) {
          $conf = $row['confirmation_number'];
          $startd = $row['start_date'];
          $end = $row['end_date'];
          $pick = $row['pickup_clerk_username'];
          $drop = $row['dropoff_clerk_username'];
          if($row['power_source'] == "manual")
            $toolDescription = $row['sub_option'] . ' ' . $row['sub_type'];
          else
            $toolDescription = $row['power_source']. ' ' . $row['sub_option'] . ' ' . $row['sub_type'];
          $rp = $row['rental_price'];
          $dp = $row['deposit_price'];
          $time = $row['timediff'];
          $start = false;


          $count++;
          if($count == $rowCount) {

            $queryClerk = "SELECT first_name, middle_name, last_name FROM Clerk WHERE username='".$pick."'";
            $resultClerk = mysqli_query($db, $queryClerk);
            $rowClerk = mysqli_fetch_array($resultClerk, MYSQLI_ASSOC);
            if (!empty($resultClerk) && (mysqli_num_rows($result) > 0) ) {
              $pick = $rowClerk['first_name'] . ' ' . $rowClerk['middle_name'] . ' ' . $rowClerk['last_name'];
            }

            $queryClerk = "SELECT first_name, middle_name, last_name FROM Clerk WHERE username='".$drop."'";
            $resultClerk = mysqli_query($db, $queryClerk);
            $rowClerk = mysqli_fetch_array($resultClerk, MYSQLI_ASSOC);
            if (!empty($resultClerk) && (mysqli_num_rows($result) > 0) ) {
              $drop = $rowClerk['first_name'] . ' ' . $rowClerk['middle_name'] . ' ' . $rowClerk['last_name'];
            }
            print("<tr>");
            print("<th scope=\"row\">".$conf."</th>");
            print("<td>");
            print($toolDescription);
            print("</td>");
            print("<td>".explode(" ",$startd)[0]."</td>");
            print("<td>".explode(" ",$end)[0]."</td>");
            print("<td>".$pick."</td>");
            print("<td>".$drop."</td>");
            print("<td>".$time."</td>");
            print("<td>".round($dp,2)."</td>");
            print("<td>".round($rp,2)."</td>");
            print("</tr>");
          }
        }

        else if ($conf == $row['confirmation_number']) {
          if($row['power_source'] == "manual")
            $toolDescription = $toolDescription . NEWLINE . $row['sub_option'] . ' ' . $row['sub_type'];
          else
            $toolDescription = $toolDescription . NEWLINE . $row['power_source']. ' ' . $row['sub_option'] . ' ' . $row['sub_type'];
          $rp = $rp + $row['rental_price'];
          $dp = $dp + $row['deposit_price'];
          $count++;
          if($count == $rowCount) {

            $queryClerk = "SELECT first_name, middle_name, last_name FROM Clerk WHERE username='".$pick."'";
            $resultClerk = mysqli_query($db, $queryClerk);
            $rowClerk = mysqli_fetch_array($resultClerk, MYSQLI_ASSOC);
            if (!empty($resultClerk) && (mysqli_num_rows($result) > 0) ) {
              $pick = $rowClerk['first_name'] . ' ' . $rowClerk['middle_name'] . ' ' . $rowClerk['last_name'];
            }

            $queryClerk = "SELECT first_name, middle_name, last_name FROM Clerk WHERE username='".$drop."'";
            $resultClerk = mysqli_query($db, $queryClerk);
            $rowClerk = mysqli_fetch_array($resultClerk, MYSQLI_ASSOC);
            if (!empty($resultClerk) && (mysqli_num_rows($result) > 0) ) {
              $drop = $rowClerk['first_name'] . ' ' . $rowClerk['middle_name'] . ' ' . $rowClerk['last_name'];
            }
            print("<tr>");
            print("<th scope=\"row\">".$conf."</th>");
            print("<td>");
            print($toolDescription);
            print("</td>");
            print("<td>".explode(" ",$startd)[0]."</td>");
            print("<td>".explode(" ",$end)[0]."</td>");
            print("<td>".$pick."</td>");
            print("<td>".$drop."</td>");
            print("<td>".$time."</td>");
            print("<td>".round($dp,2)."</td>");
            print("<td>".round($rp,2)."</td>");
            print("</tr>");
          }
        }

        else {
          $queryClerk = "SELECT first_name, middle_name, last_name FROM Clerk WHERE username='".$pick."'";
          $resultClerk = mysqli_query($db, $queryClerk);
          $rowClerk = mysqli_fetch_array($resultClerk, MYSQLI_ASSOC);
          if (!empty($resultClerk) && (mysqli_num_rows($result) > 0) ) {
          $pick = $rowClerk['first_name'] . ' ' . $rowClerk['middle_name'] . ' ' . $rowClerk['last_name'];
          }

          $queryClerk = "SELECT first_name, middle_name, last_name FROM Clerk WHERE username='".$drop."'";
          $resultClerk = mysqli_query($db, $queryClerk);
          $rowClerk = mysqli_fetch_array($resultClerk, MYSQLI_ASSOC);
          if (!empty($resultClerk) && (mysqli_num_rows($result) > 0) ) {
            $drop = $rowClerk['first_name'] . ' ' . $rowClerk['middle_name'] . ' ' . $rowClerk['last_name'];
          }
          print("<tr>");
          print("<th scope=\"row\">".$conf."</th>");
          print("<td>");
          print($toolDescription);
          print("</td>");
          print("<td>".explode(" ",$startd)[0]."</td>");
          print("<td>".explode(" ",$end)[0]."</td>");
          print("<td>".$pick."</td>");
          print("<td>".$drop."</td>");
          print("<td>".$time."</td>");
          print("<td>".round($dp,2)."</td>");
          print("<td>".round($rp,2)."</td>");
          print("</tr>");

          $conf = $row['confirmation_number'];
          $start = $row['start_date'];
          $end = $row['end_date'];
          $pick = $row['pickup_clerk_username'];
          $drop = $row['dropoff_clerk_username'];
          if($row['power_source'] == "manual")
            $toolDescription = $row['sub_option'] . ' ' . $row['sub_type'];
          else
            $toolDescription = $row['power_source']. ' ' . $row['sub_option'] . ' ' . $row['sub_type'];
          $rp = $row['rental_price'];
          $dp = $row['deposit_price'];
          $time = $row['timediff'];
          $count++;
          if($count == $rowCount) {

            $queryClerk = "SELECT first_name, middle_name, last_name FROM Clerk WHERE username='".$pick."'";
            $resultClerk = mysqli_query($db, $queryClerk);
            $rowClerk = mysqli_fetch_array($resultClerk, MYSQLI_ASSOC);
            if (!empty($resultClerk) && (mysqli_num_rows($result) > 0) ) {
              $pick = $rowClerk['first_name'] . ' ' . $rowClerk['middle_name'] . ' ' . $rowClerk['last_name'];
            }

            $queryClerk = "SELECT first_name, middle_name, last_name FROM Clerk WHERE username='".$drop."'";
            $resultClerk = mysqli_query($db, $queryClerk);
            $rowClerk = mysqli_fetch_array($resultClerk, MYSQLI_ASSOC);
            if (!empty($resultClerk) && (mysqli_num_rows($result) > 0) ) {
              $drop = $rowClerk['first_name'] . ' ' . $rowClerk['middle_name'] . ' ' . $rowClerk['last_name'];
            }
            print("<tr>");
            print("<th scope=\"row\">".$conf."</th>");
            print("<td>");
            print($toolDescription);
            print("</td>");
            print("<td>".explode(" ",$startd)[0]."</td>");
            print("<td>".explode(" ",$end)[0]."</td>");
            print("<td>".$pick."</td>");
            print("<td>".$drop."</td>");
            print("<td>".$time."</td>");
            print("<td>".round($dp,2)."</td>");
            print("<td>".round($rp,2)."</td>");
            print("</tr>");
          }

        }


      }
    }  ?>
    </tr>
      <!-- end for -->
    </tbody>
  </table>

</div>
<div class="container"><!-- Re-open main page container-->

<?php include("partials/tail.php");
