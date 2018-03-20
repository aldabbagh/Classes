<?php

include('lib/common.php');
// written by GTusername4

if (!isset($_SESSION['email'])) {
	header('Location: login.php');
	exit();
}
    $query = "SELECT first_name, last_name " .
             "FROM User " .
             "WHERE User.email='{$_SESSION['email']}'";
             
    $result = mysqli_query($db, $query);
     include('lib/show_queries.php');
        
    if (!empty($result) && (mysqli_num_rows($result) > 0) ) {
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        $count = mysqli_num_rows($result);
    } else {
            array_push($error_msg,  "SELECT ERROR: User profile... <br>".  __FILE__ ." line:". __LINE__ );
    }

    $user_name = $row['first_name'] . " " . $row['last_name'];

if (!empty($_GET['accept_request'])) {

	$email = mysqli_real_escape_string($db, $_GET['accept_request']);

	$query = "UPDATE Friendship " .
			 "SET date_connected = NOW() " .
			 "WHERE friend_email = '{$_SESSION['email']}' " .
			 "AND email = '$email'";

	$result = mysqli_query($db, $query);
    include('lib/show_queries.php');

     if (mysqli_affected_rows($db) == -1) {
        array_push($error_msg,  "UPDATE ERROR: accept friendship ... <br>".  __FILE__ ." line:". __LINE__ );
	}
         $query = "SELECT first_name,last_name FROM User WHERE User.email ='$email'";
         $result = mysqli_query($db, $query);
         $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
         $friends_name = $row['first_name'] . " " . $row['last_name'];
         
        //SELECT relationship FROM Friendship WHERE Friendship.friend_email ='michael@bluthco.com' AND  Friendship.email = 'pam@dundermifflin.com';
         $query = "SELECT relationship FROM Friendship WHERE Friendship.email='$email' AND Friendship.friend_email='{$_SESSION['email']}' ";
         $result = mysqli_query($db, $query);
         $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
         $relationship = $row['relationship']; 

		$query = "INSERT INTO Friendship (email, friend_email, relationship, date_connected) " .
				 "VALUES ('{$_SESSION['email']}', '$email', '$relationship', NOW())";
        $queryID = mysqli_query($db, $query);
                
        include('lib/show_queries.php');
        
        if ($queryID  == False) {  //INSERT, UPDATE, DELETE, DROP return True on Success  / False on Error
                 array_push($error_msg, "INSERT ERROR: Friendship: ". $friends_name. " "  . $email.  " (" . $relationship .") relationship already sent/accepted! <br>" . __FILE__ ." line:". __LINE__ );
          } 
}

if (!empty($_GET['reject_request'])) {

	$email = mysqli_real_escape_string($db, $_GET['reject_request']);

	$query = "DELETE FROM Friendship " .
			 "WHERE date_connected IS NULL " .
			 "AND friend_email='{$_SESSION['email']}' " .
			 "AND email='$email'";

	$result = mysqli_query($db, $query);
    
    include('lib/show_queries.php');

    if (mysqli_affected_rows($db) == -1) {
        array_push($error_msg, "DELETE ERROR: reject request... <br>" . __FILE__ ." line:". __LINE__ );
     }
}

if (!empty($_GET['cancel_request'])) {

	$email = mysqli_real_escape_string($db, $_GET['cancel_request']);

	$query = "DELETE FROM Friendship " .
			 "WHERE email = '{$_SESSION['email']}' " .
			 "AND friend_email = '$email'";
    
	$result = mysqli_query($db, $query);
    include('lib/show_queries.php');
    
     if (mysqli_affected_rows($db) == -1) {
        array_push($error_msg,  "DELETE ERROR: cancel request...<br>" . __FILE__ ." line:". __LINE__ );
	}
}

?>

<?php include("lib/header.php"); ?>

		<title>GTOnline Friend Requests</title>
	</head>
	
	<body>
		<div id="main_container">
    <?php include("lib/menu.php"); ?>
			
			<div class="center_content">	
				<div class="center_left">
					<div class="title_name"><?php print $user_name; ?></div>          
					<div class="features">   
						<div class="profile_section">						
							<div class="subtitle">Friend Requests Received</div>
							
							<?php
                                $query = "SELECT first_name, last_name, home_town, relationship, Friendship.email " .
                                         "FROM Friendship " .
                                         "INNER JOIN RegularUser ON RegularUser.email=Friendship.email " .
                                         "INNER JOIN User ON User.email=RegularUser.email " .
                                         "WHERE Friendship.friend_email='{$_SESSION['email']}' " .
                                         "AND date_connected IS NULL " .
                                         "ORDER BY last_name, first_name";
                                
                                $result = mysqli_query($db, $query);
								include('lib/show_queries.php');

                                if (is_bool($result) && (mysqli_num_rows($result) == 0) ) {
                                      //false positive if no friends
                                     array_push($error_msg,  "Query ERROR: Failed to get friend request <br>" . __FILE__ ." line:". __LINE__ );
                                }
                                
                                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                                $count = mysqli_num_rows($result);
                                if ($row) {
                                                        
                                    print '<table>';
                                    print '<tr>';
                                    print '<td class="heading">Name</td>';
                                    print '<td class="heading">Hometown</td>';
                                    print '<td class="heading">Relationship</td>';
                                    print '<td class="heading">Accept?</td>';
                                    print '<td class="heading">Reject?</td>';
                                    print '</tr>';
                                
                                    while ($row){
                                                            
                                        print '<tr>';
                                        print '<td>' . $row['first_name'] . ' ' . $row['last_name'] . '</td>';
                                        print '<td>' . $row['home_town'] . '</td>';
                                        print '<td>' . $row['relationship'] . '</td>';
                                        print '<td><a href="view_requests.php?accept_request=' . urlencode($row['email']) . '">Accept</a></td>';
                                        print '<td><a href="view_requests.php?reject_request=' . urlencode($row['email']) . '">Reject</a></td>';
                                        print '</tr>';

                                        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);   
                                    
                                    }
                                    print '</table>';
                                }
                                else {
                                    print "<br/>None!";
                                }
							?>
				
						</div>
						<div class="profile_section">
							<div class="subtitle">Friend Requests Sent</div>
							
							<?php			
                                $query = "SELECT first_name, last_name, home_town, relationship, User.email " .
                                         "FROM Friendship " .
                                         "INNER JOIN RegularUser ON RegularUser.email=Friendship.friend_email " .
                                         "INNER JOIN User ON User.email=RegularUser.email " .
                                         "WHERE Friendship.email='{$_SESSION['email']}' " .
                                         "AND date_connected IS NULL " .
                                         "ORDER BY last_name, first_name";
                                
                                $result = mysqli_query($db, $query);
								include('lib/show_queries.php');

                                if (is_bool($result) && (mysqli_num_rows($result) == 0) ) {
                                    //false positive if no friends
                                    array_push($error_msg,  "Query ERROR: Failed to get friend requests you sent " . __FILE__ ." line:". __LINE__ );
                                }
                                
                                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                                $count = mysqli_num_rows($result);
                                
                                if ($row) {
                                                        
                                    print '<table>';
                                    print '<tr>';
                                    print '<td class="heading">Name</td>';
                                    print '<td class="heading">Hometown</td>';
                                    print '<td class="heading">Relationship</td>';
                                    print '<td class="heading">Cancel?</td>';
                                    print '</tr>';
                                
                                    while ($row){
                                                            
                                        print '<tr>';
                                        print '<td>' . $row['first_name'] . ' ' . $row['last_name'] . '</td>';
                                        print '<td>' . $row['home_town'] . '</td>';
                                        print '<td>' . $row['relationship'] . '</td>';
                                        print '<td><a href="view_requests.php?cancel_request=' . urlencode($row['email']) . '">Cancel</a></td>';
                                        print '</tr>';
                                        
                                        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                                    
                                    }
                                    print '</table>';
                                }
                                else {
                                    print "<br/>None!";
                                }
							?>												
						</div>	
                    
                    <div class="profile_section">
							<div class="subtitle">Friend Requests Accepted</div>
							
							<?php			
                                $query = "SELECT first_name, last_name, home_town, relationship, User.email " .
                                         "FROM Friendship " .
                                         "INNER JOIN RegularUser ON RegularUser.email=Friendship.friend_email " .
                                         "INNER JOIN User ON User.email=RegularUser.email " .
                                         "WHERE Friendship.email='{$_SESSION['email']}' " .
                                         "AND date_connected IS NOT NULL " .
                                         "ORDER BY last_name, first_name";
                                
                                $result = mysqli_query($db, $query);
								include('lib/show_queries.php');
	 
                                if (is_bool($result) && (mysqli_num_rows($result) == 0) ) {
                                    //false positive if no friends
                                    array_push($error_msg,  "Query ERROR: Failed to get friend requests you accepted " . __FILE__ ." line:". __LINE__ );
                                }
                                
                                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                                $count = mysqli_num_rows($result);
                                
                                if ($row) {
                                                        
                                    print '<table>';
                                    print '<tr>';
                                    print '<td class="heading">Name</td>';
                                    print '<td class="heading">Hometown</td>';
                                    print '<td class="heading">Relationship</td>';
                                    print '<td class="heading">Cancel?</td>';
                                    print '</tr>';
                                
                                    while ($row){
                                                            
                                        print '<tr>';
                                        print '<td>' . $row['first_name'] . ' ' . $row['last_name'] . '</td>';
                                        print '<td>' . $row['home_town'] . '</td>';
                                        print '<td>' . $row['relationship'] . '</td>';
                                        print '<td><a href="view_requests.php?cancel_request=' . urlencode($row['email']) . '">Cancel</a></td>';
                                        print '</tr>';
                                        
                                        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                                    
                                    }
                                    print '</table>';
                                }
                                else {
                                    print "<br/>None!";
                                }
							?>												
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