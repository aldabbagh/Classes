<?php
include("lib/common.php");

// Default values for form inputs
$inputs = [
  'start-date' => '', 'end-date' => '', 'keyword' => '', 'type' => '',
  'powersource' => '', 'subtype' => ''
];

$alert = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  foreach ($_POST as $key => $value) {
    $inputs[$key] = mysqli_real_escape_string($db, $_POST[$key]);
  }

  // Validation for missing inputs
  if ($inputs['start-date'] === '') {
    $errors['start-date'] = 'This field is required';
  }

  if ($inputs['end-date'] === '') {
    $errors['end-date'] = 'This field is required';
  }
}

?>


<?php include("partials/head.php"); ?>

<div class="row my-4">
  <div class="col-12">
    <!-- Alerts -->
    <?php if (count($errors) > 0) { ?>
      <div class="alert alert-danger" role="alert">
        Some fields are missing or invalid. Please correct them.
      </div>
    <?php } elseif ($alert !== '') { ?>
      <div class="alert alert-danger" role="alert">
        <?= $alert ?>
      </div>
    <?php } ?>

    <h1 class="h2">Check Tool Availablility</h2>
    <?php include("partials/customer_tool_search.php"); ?>
  </div>
</div>

</div><!-- Close main page container-->
<div class="container-fluid">

<!-- Search Results -->
<table class="table table-sm table-hover">
  <thead class="thead-light">
    <tr>
      <th scope="col">ID</th>
      <th scope="col">Description</th>
      <th scope="col">Rental Price</th>
      <th scope="col">Deposit Price</th>
    </tr>
  </thead>
  <tbody>

  <?php
  if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
      count($errors) === 0) {

    function validateDate($date, $format = 'Y-m-d H:i:s') {
      $d = DateTime::createFromFormat($format, $date);
      return $d && $d->format($format) == $date;
    }

    $start_date = $inputs['start-date'];
    $start_date = $start_date . " 00:00:00";
    $end_date = $inputs['end-date'];
    $end_date = $end_date . " 23:59:59";

    if (!(validateDate($start_date) &&
        validateDate($end_date) &&
        $start_date < $end_date)) {
      print("The dates provided are invalid, please check them and try again" . NEWLINE);

    } else {

      $whereString = "";
      $keyword = $inputs['keyword'];

      if ($inputs['type'] && $inputs['keyword']) {
        $subOption = $keyword;

        if ($inputs['type']=="hand") {
          $whereString = "WHERE sub_option='".$keyword."' AND (sub_type='screwdriver' or  sub_type='socket' or sub_type='ratchet' or sub_type='wrench' or sub_type='plier' or sub_type='hammer' or sub_type='gun')";
        }

        if ($inputs['type']=='garden') {
          $whereString = "WHERE sub_option='".$keyword."' AND (sub_type='digging' or  sub_type='prunning' or sub_type='rake' or sub_type='wheelbarrow' or sub_type='striking')";
        }

        if ($inputs['type']=='ladder') {
          $whereString = "WHERE sub_option='".$keyword."' AND (sub_type='stepladder' or  sub_type='straightladder')";
        }

        if ($inputs['type']=='power') {
          $whereString = "WHERE sub_option='".$keyword."' AND (sub_type='powerdrill' or  sub_type='powersaw' or sub_type='powersander' or sub_type='poweraircompressor' or sub_type='powermixer' or sub_type='powergenerator')";
        }

        if ($inputs['type']=='all') {
          $whereString = "WHERE sub_option='".$keyword."'";
        }

      } else {

        if ($inputs['type']=='hand') {
          $whereString = "WHERE (sub_type='screwdriver' or  sub_type='socket' or sub_type='ratchet' or sub_type='wrench' or sub_type='plier' or sub_type='hammer' or sub_type='gun')";
        }

        if ($inputs['type']=='garden') {
          $whereString = "WHERE (sub_type='digging' or  sub_type='prunning' or sub_type='rake' or sub_type='wheelbarrow' or sub_type='striking')";
        }

        if ($inputs['type']=='ladder') {
          $whereString = "WHERE (sub_type='stepladder' or  sub_type='straightladder')";
        }

        if ($inputs['type']=='power') {
          $whereString = "WHERE (sub_type='powerdrill' or  sub_type='powersaw' or sub_type='powersander' or sub_type='poweraircompressor' or sub_type='powermixer' or sub_type='powergenerator')";
        }
      }

      if ($inputs['subtype'] && $inputs['subtype'] != "") {
        if ($whereString == "")
          $whereString = " WHERE (sub_type='" . $inputs['subtype'] . "')";
        else
          $whereString = $whereString . " AND (sub_type='" . $inputs['subtype'] . "')";
      }

      if ($inputs['powersource'] && $inputs['powersource'] != "") {
        if ($whereString == "")
          $whereString = " WHERE (power_source='" . $inputs['powersource'] . "')";
        else
          $whereString = $whereString . " AND (power_source='" . $inputs['powersource'] . "')";
      }

      if ($whereString == "") {

        $query = "SELECT T.tool_number, price * 0.15 AS rental_price, price * 0.4 AS deposit_price, power_source, sub_option, sub_type
        FROM Tool AS T
        WHERE T.tool_number NOT IN
        (SELECT tol.tool_number
        FROM Tool as tol NATURAL JOIN rentalrentstool NATURAL JOIN rental as R
        WHERE (R.start_date<='".$end_date."' AND R.start_date>='".$start_date."') OR (R.end_date >='".$start_date."' AND R.end_date<='".$end_date."') OR (R.start_date<='".$start_date."' AND R.end_date >='".$end_date."'))";

      } else {

        $query = "SELECT T.tool_number, price * 0.15 AS rental_price, price * 0.4 AS deposit_price, power_source, sub_option, sub_type
        FROM Tool AS T "
        . $whereString . " AND T.tool_number NOT IN
        (SELECT tol.tool_number
        FROM Tool as tol NATURAL JOIN rentalrentstool NATURAL JOIN rental as R
        WHERE (R.start_date<='".$end_date."' AND R.start_date>='".$start_date."') OR (R.end_date >='".$start_date."' AND R.end_date<='".$end_date."') OR (R.start_date<='".$start_date."' AND R.end_date >='".$end_date."'))";

      }

      $result = mysqli_query($db, $query);
      include('lib/show_queries.php');
      include('lib/error.php');

      if (isset($result) && mysqli_num_rows($result) != 0 ) {
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
          print("<tr>");
          print("<th scope=\"row\">");
          print($row['tool_number']);
          print("</th>");

          if ($row['power_source'] == "manual") {
            $description = $row['sub_option'] . ' ' . $row['sub_type'];
          } else {
            $description = $row['power_source']. ' ' . $row['sub_option'] . ' ' . $row['sub_type'];
          }

          print("<td><a href=\"tool_details.php?id=".$row['tool_number']."\" target=\"_blank\">" . $description . " </a></td>");
          print("<td>".round($row['rental_price'],2)."</td>");
          print("<td>".round($row['deposit_price'],2)."</td>");
          print("</tr>");
        }
      }

    }
  }

  ?>

  </tbody>
</table>

</div>
<div class="container"><!-- Re-open main page container-->

<?php include("partials/tail.php"); ?>
