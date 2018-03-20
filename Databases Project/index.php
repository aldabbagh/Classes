<?php
include('lib/common.php');

?>


<?php include('partials/head.php'); ?>

<div class="row my-4">
  <div class="col">
    <h1 class="h2">Main Menu</h1>

    <!-- Menu Customer -->
    <?php if ($_SESSION['type'] == 'customer') { ?>
      <div class="list-group">
        <a href="/view_profile.php"
          class="list-group-item list-group-item-action">
          View Profile
        </a>
        <a href="check_tool_availability.php"
          class="list-group-item list-group-item-action">
          Check Tool Availability
        </a>
        <a href="/make_reservation.php"
          class="list-group-item list-group-item-action">
          Make Reservation
        </a>
      </div>
    <?php } ?>

    <!-- Menu Clerk -->
    <?php if ($_SESSION['type'] == 'clerk') { ?>
      <h2 class="h3">Reservations</h2>
      <div class="list-group">
        <a href="/pickup_reservation.php"
          class="list-group-item list-group-item-action">
          Pick-Up Reservation
        </a>
        <a href="/dropoff_reservation.php"
          class="list-group-item list-group-item-action">
          Drop-Off Reservation
        </a>
      </div>
<!-- maldabbagh3: I changed the links from x_report to report_x-->
      <h2 class="h3 mt-4">Reports</h2>
      <div class="list-group">
        <a href="/report_clerk.php"
          class="list-group-item list-group-item-action">
          Clerk Report
        </a>
        <a href="/report_customer.php"
          class="list-group-item list-group-item-action">
          Customer Report
        </a>
        <a href="/report_tool.php"
          class="list-group-item list-group-item-action">
          Tool Inventory Report
        </a>
      </div>
    <?php } ?>

    <!-- Logout button -->
    <div class="list-group my-4">
      <a href="/logout.php"
        class="btn btn-block btn-outline-danger">
       Logout
      </a>
    </div>
  </div>
</div>

<?php include('partials/tail.php'); ?>
