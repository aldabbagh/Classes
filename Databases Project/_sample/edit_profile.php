<?php

include('lib/common.php');
// written by GTusername4

if (!isset($_SESSION['email'])) {
	header('Location: login.php');
	exit();
}
 
    $query = "SELECT first_name, last_name, gender, birthdate, current_city, home_town " .
		 "FROM User " .
		 "INNER JOIN RegularUser ON User.email = RegularUser.email " .
		 "WHERE User.email = '{$_SESSION['email']}'";

    $result = mysqli_query($db, $query);
    include('lib/show_queries.php');
    
    if (!is_bool($result) && (mysqli_num_rows($result) > 0) ) {
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    } else {
        array_push($error_msg,  "Query ERROR: Failed to get User Profile... <br>".  __FILE__ ." line:". __LINE__ );
    }
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  
        $gender = mysqli_real_escape_string($db, $_POST['gender']);
        $birthdate = mysqli_real_escape_string($db, $_POST['birthdate']);  
        $current_city = mysqli_real_escape_string($db, $_POST['current_city']);
        $home_town = mysqli_real_escape_string($db, $_POST['home_town']);
        
        if (empty($gender)) {
                array_push($error_msg,  "Please enter a gender.");
        } 
        
        if (!is_date($birthdate)) {
            array_push($error_msg,  "Error: Invalid birthdate ");
        }
        
         if (empty($current_city)) {
            array_push($error_msg,  "Please enter a current_city.");
        }
        
        if (empty($home_town)) {
                array_push($error_msg,  "Please enter an home_town.");
        }

         if ( !empty($birthdate) && !empty($current_city) && !empty($home_town) )   { 
            $query = "UPDATE RegularUser " .
                     "SET gender ='$gender', " .
                     "birthdate='$birthdate', " .
                     "home_town='$home_town', " .
                     "current_city='$current_city' " .
                     "WHERE email='{$_SESSION['email']}'";

            $result = mysqli_query($db, $query);
            include('lib/show_queries.php');
            
             if (mysqli_affected_rows($db) == -1) {
                 array_push($error_msg,  "UPDATE ERROR: Regular User... <br>".  __FILE__ ." line:". __LINE__ );
                 //array_push($error_msg,  'Error# '. mysqli_errno($db) . ": " . mysqli_error($db));
             }  

         }

         
        $interest = mysqli_real_escape_string($db, $_POST['add_interest']);
        if (empty($interest)) {
            array_push($error_msg,  "Error: You must enter an interest ");
        }
        
        if (!empty($_POST['add_interest']) and $_POST['add_interest'] != '(add interest)' and trim($_POST['add_interest']) != '' ) {
             
            $query = "INSERT INTO UserInterest (email, interest) " .
                         "VALUES('{$_SESSION['email']}', '$interest')";
                
            $queryID = mysqli_query($db, $query);

            //if (is_numeric($query) && (mysqli_num_rows($query) > 0) ) {
            include('lib/show_queries.php');

            if ($queryID  == False) {
                 array_push($error_msg, "User Interest '" . $interest .  "'  already an interest... <br>".  __FILE__ ." line:". __LINE__ );
            } 
        }

        $query = "SELECT first_name, last_name, gender, birthdate, current_city, home_town " .
		 "FROM User INNER JOIN RegularUser ON User.email = RegularUser.email " .
		 "WHERE User.email = '{$_SESSION['email']}'";

    $result = mysqli_query($db, $query);
    
    if (!is_bool($result) && (mysqli_num_rows($result) > 0) ) {
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    } else {
        array_push($error_msg,  "SELECT ERROR: User profile... <br>".  __FILE__ ." line:". __LINE__ );
    }
    
}  //end of if($_POST)

if (!empty($_GET['delete_interest'])) {
	
	$interest = mysqli_real_escape_string($db, $_GET['delete_interest']);
	$query = "DELETE FROM UserInterest " .
			 "WHERE email = '{$_SESSION['email']}' " .
			 "AND interest = '$interest'";
	
    $result = mysqli_query($db, $query);
    
     include('lib/show_queries.php');

    if (mysqli_affected_rows($db) == -1) {
        array_push($error_msg, "DELETE ERROR:  User Interest  <br>".  __FILE__ ." line:". __LINE__ );
     }
    
}

function is_date( $str ) { 
	$stamp = strtotime( $str ); 
	if (!is_numeric($stamp)) { 
		return false; 
	} 
	$month = date( 'm', $stamp ); 
	$day   = date( 'd', $stamp ); 
	$year  = date( 'Y', $stamp ); 
  
	if (checkdate($month, $day, $year)) { 
		return true; 
	} 
	return false; 
} 

?>

<?php include("lib/header.php"); ?>
		<title>GTOnline Edit Profile</title>
	</head>
	
	<body>
    	<div id="main_container">
        <?php include("lib/menu.php"); ?>
    
			<div class="center_content">	
				<div class="center_left">
					<div class="title_name"><?php print $row['first_name'] . ' ' . $row['last_name']; ?></div>          
					<div class="features">   
						
                        <div class="profile_section">
							<div class="subtitle">Edit Profile (Note: intentionally missing functionality)</div>   
                            
							<form name="profileform" action="edit_profile.php" method="post">
								<table>
									<tr>
										<td class="item_label">Sex</td>
										<td>
											<select name="gender">
												<option value="Male" <?php if ($row['gender'] == 'Male') { print 'selected="true"';} ?>>Male</option>
												<option value="Female" <?php if ($row['gender'] == 'Female') { print 'selected="true"';} ?>>Female</option>
											</select>
										</td>
									</tr>
									<tr>
										<td class="item_label">Birthdate</td>
										<td>
											<input type="text" name="birthdate" value="<?php if ($row['birthdate']) { print $row['birthdate']; } ?>" />										
										</td>
									</tr>
									<tr>
										<td class="item_label">Current City</td>
										<td>
											<input type="text" name="current_city" value="<?php if ($row['current_city']) { print $row['current_city']; } ?>" />	
										</td>
									</tr>

									<tr>
										<td class="item_label">Hometown</td>
										<td>
											<input type="text" name="home_town" value="<?php if ($row['home_town']) { print $row['home_town']; } ?>" />	
										</td>
									</tr>
									
									<tr>
										<td class="item_label">Interests</td>
										<td>
											<ul>
											<?php
												$query = "SELECT interest FROM UserInterest WHERE email='{$_SESSION['email']}'";
												$result = mysqli_query($db, $query);
												 include('lib/show_queries.php');
												 
                                                if (is_bool($result) && (mysqli_num_rows($result) == 0) ) {
                                                    array_push($error_msg,  "Query ERROR: Failed to get User interests... <br>" . __FILE__ ." line:". __LINE__ );
                                                }                                              
                                                 
												 while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
													print "<li>{$row['interest']} <a href='edit_profile.php?delete_interest=" . 
														urlencode($row['interest']) . "'>delete</a></li>";
												}
											?>
											<li><input type="text" name="add_interest" value="(add interest)" 
													onclick="if(this.value=='(add interest)'){this.value=''}"
													onblur="if(this.value==''){this.value='(add interest)'}"/></li>
											</ul>
										</td>
									</tr>
								</table>
								
								<a href="javascript:profileform.submit();" class="fancy_button">Save</a> 
							
							</form>
						</div>
                        
                        <div class="profile_section">
							<div class="subtitle">Education</div>  
							<table>
								<tr>
									<td class="heading">School</td>
									<td class="heading">Year Graduated</td>
								</tr>							
						
								<?php
									    $query = "SELECT school_name, year_graduated " . 
											 "FROM Attend " .
											 "WHERE email = '" . $_SESSION['email'] . "' " .
											 "ORDER BY year_graduated DESC";
									    $result = mysqli_query($db, $query);
                                        
                                        include('lib/show_queries.php');
                                        
                                        if (is_bool($result) && (mysqli_num_rows($result) == 0) ) {
                                                    array_push($error_msg,  "Query ERROR: Failed to get School information..." . __FILE__ ." line:". __LINE__ );
                                             } 
                                        
									while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
										print "<tr>";
										print "<td>" . $row['school_name'] . "</td>";
										print "<td>" . $row['year_graduated'] . "</td>";
										print "</tr>";
									}
								?>
							</table>						
						</div>	
                    
						<div class="profile_section">
							<div class="subtitle">Professional</div>  
							<table width="80%">
								<tr>
									<td class="heading">Employer</td>
									<td class="heading">Job Title</td>
								</tr>							
						
								<?php
									    $query = "SELECT employer_name, job_title " . 
											 "FROM Employment " .
											 "WHERE email = '" . $_SESSION['email'] . "' " .
											 "ORDER BY employer_name DESC";
									    $result = mysqli_query($db, $query);
                                        include('lib/show_queries.php');
                                        
                                       if (is_bool($result) && (mysqli_num_rows($result) == 0) ) {
                                             array_push($error_msg,  "Query ERROR: Failed to get Employment information..." . __FILE__ ." line:". __LINE__ );
                                        } 
                                             
									while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
										print "<tr>";
										print "<td>" . $row['employer_name'] . "</td>";
										print "<td>" . $row['job_title'] . "</td>";
										print "</tr>";
									}
								?>
							</table>						
						</div>
                        
					 </div> 	
				</div> 
                
                <?php include("lib/error.php"); ?>
                    
				<div class="clear"></div> 		
			</div>    

               <?php include("lib/footer.php"); ?>
				 
		</div>
	</body>
</html>