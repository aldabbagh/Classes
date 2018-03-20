<?php
if (isset($_SESSION['type'])) {
  if ($_SESSION['type'] == 'customer') {
    $nav_id = "navbarNavCustomer";
  } elseif ($_SESSION['type'] == 'clerk') {
    $nav_id = "navbarNavClerk";
  }
}
?>


<nav class="navbar navbar-expand-lg navbar-dark bg-dark">

  <!-- Logo -->
  <a class="navbar-brand" href="/">
    Tools <span class="t4r-brand-4">4</span> Rent
  </a>

  <?php if (isset($_SESSION['type'])) { ?>
    <!-- Resoponsive Menu button -->
    <button class="navbar-toggler" type="button"
      data-toggle="collapse"
      data-target="<?php echo "#" . $nav_id ?>"
      aria-controls="<?php echo $nav_id ?>"
      aria-expanded="false"
      aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNavCustomer">
      <ul class="navbar-nav mr-auto">
        <!-- Menu Customer -->
        <?php if ($_SESSION['type'] == 'customer') { ?>
          <li class="nav-item active">
            <a class="nav-link" href="/view_profile.php">
              View Profile <span class="sr-only">(current)</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/check_tool_availability.php">
              Check Tool Availability</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/make_reservation.php">
              Make Reservation</a>
          </li>
        <?php } ?>

        <!-- Menu Clerk -->
        <?php if ($_SESSION['type'] == 'clerk') { ?>
          <li class="nav-item active">
            <a class="nav-link" href="/pickup_reservation.php">
              Pick-Up <span class="sr-only">(current)</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/dropoff_reservation.php">
              Drop-Off
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/add_tool.php">
              Add Tool
            </a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="navbarDropdownMenuLink"
              href="#" data-toggle="dropdown"
              aria-haspopup="true" aria-expanded="false">
              Reports
            </a>
            <div class="dropdown-menu"
              aria-labelledby="navbarDropdownMenuLink">
              <a class="dropdown-item" href="/report_clerk.php">
                Clerk Report
              </a>
              <a class="dropdown-item" href="/report_customer.php">
                Customer Report
              </a>
              <a class="dropdown-item" href="/report_tool.php">
                Tool Inventory Report
              </a>
            </div>
          </li>
        <?php } ?>
      </ul>

      <?php if (isset($_SESSION['username'])) { ?>
        <!-- User Welcome -->
        <span class="navbar-text d-none d-lg-block mr-3">
          Hi <?= isset($_SESSION['first_name']) ? $_SESSION['first_name']:'' ?>
        </span>

        <!-- Logout button -->
        <a class="btn btn-outline-danger" href="/logout.php">
          Logout
        </a>
      <?php } ?>
    </div>
  <?php } ?>

</nav>
