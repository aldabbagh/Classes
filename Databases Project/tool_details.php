
<?php 

include('lib/common.php');
include('lib/error.php');
include('partials/head.php');

if(!$_REQUEST['id']){
		print("Error: There is no tool in the URL!! You will be redirected to the main page in 10 seconds");
		header( "refresh:10;url=index.php" );
		exit();
}

else{
$id = mysqli_real_escape_string($db, $_REQUEST['id']);

			$fractions = array(
			"0" => "0",
			"0.125" => "1/8",
			"0.25" => "1/4",
			"0.375" => "3/8",
			"0.5" => "1/2",
			"0.625" => "5/8",
			"0.75" => "3/4",
			"0.875" => "7/8");
			
			
			$query = "SELECT tool_number, power_source, sub_option, sub_type, ROUND(price,2) AS price, width_diameter, length, weight, manufacturer
						FROM TOOL
						WHERE tool_number=".$id;
						
			$result = mysqli_query($db, $query);
			include('lib/show_queries.php'); 
			
			if (mysqli_num_rows($result) == 0) {
				print("Error: There is no tool with id = " . $_REQUEST['id'] . " , you will be redirected to the main page");
				header( "refresh:10;url=index.php" );
				exit();
			}
			
			if (isset($result)) {
				
			$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			$st = $row['sub_type'];
			
			$type = "";
			$quotient = (int)$row['width_diameter'] / 1;
			$mod = $fractions["".fmod($row['width_diameter'],1)];
			if($mod == 0)
				$mod = " in. W";
			else
				$mod = "-" . $mod . " in. W";
			$description = $quotient . $mod;
			$this_accessory ="N/A";
			$quotient = (int)$row['length'] / 1;
			$mod = $fractions["".fmod($row['length'],1)];
			if($mod == 0)
				$mod = " in. L";
			else
				$mod = "-" . $mod . " in. L";
			$description = $description . " " . $quotient . $mod;
			$description = $description . " " .  ($row['weight'] + 0) . " lb.";
			
			if($row['power_source'] == "manual")
				$description = $description . " " . $row['sub_option'] . ' ' . $row['sub_type'];
			else
				$description = $description . " " . $row['power_source']. ' ' . $row['sub_option'] . ' ' . $row['sub_type'];
			
			if($st == "digging") {
				$type = "Garden";
				
				$query = "SELECT *
				FROM gardentool as T NATURAL JOIN digging
				WHERE T.tool_number = ".$row['tool_number'] ;

				$result = mysqli_query($db, $query);
				include('lib/show_queries.php'); 
				if (!empty($result)) {
					array_push($error_msg,  "SELECT ERROR:Failed to find friends ... <br>" . __FILE__ ." line:". __LINE__ );
				}
				
				if (isset($result)) {
					
					$rowTool = mysqli_fetch_array($result, MYSQLI_ASSOC);
					if(!is_null($rowTool['handle_material']))
						$description = $description . " " . $rowTool['handle_material'];
					if(!is_null($rowTool['blade_width'])){
						
						$description = $description . " " . round($rowTool['blade_width'],1) . " in.";
					}
					if(!is_null($rowTool['blade_length'])){
						
						$description = $description . " " . round($rowTool['blade_length'],1) . " in.";

					}    
			}
			
			}
			
			elseif($st == "pruning") {
				$type = "Garden";
				
				$query = "SELECT *
				FROM gardentool as T NATURAL JOIN pruning
				WHERE T.tool_number = ".$row['tool_number'] ;

				$result = mysqli_query($db, $query);
				include('lib/show_queries.php'); 
				if (!empty($result)) {
					array_push($error_msg,  "SELECT ERROR:Failed to find friends ... <br>" . __FILE__ ." line:". __LINE__ );
				}
				
				if (isset($result)) {
					
					$rowTool = mysqli_fetch_array($result, MYSQLI_ASSOC);
					if(!is_null($rowTool['handle_material']))
						$description = $description . " " . $rowTool['handle_material'];
					if(!is_null($rowTool['blade_material']))
						$description = $description . " " . $rowTool['blade_material'] . " in.";
					if(!is_null($rowTool['blade_length'])){
						
						$description = $description . " " . round($rowTool['blade_length'],1) . " in.";
					}   
			}
			
			}
			
			elseif($st == "striking") {
				$type = "Garden";
				
				$query = "SELECT *
				FROM gardentool as T NATURAL JOIN striking
				WHERE T.tool_number = ".$row['tool_number'] ;

				$result = mysqli_query($db, $query);
				include('lib/show_queries.php'); 
				if (!empty($result)) {
					array_push($error_msg,  "SELECT ERROR:Failed to find friends ... <br>" . __FILE__ ." line:". __LINE__ );
				}
				
				if (isset($result)) {
					
					$rowTool = mysqli_fetch_array($result, MYSQLI_ASSOC);
					if(!is_null($rowTool['handle_material']))
						$description = $description . " " . $rowTool['handle_material'];
					if(!is_null($rowTool['head_weight']))
						$description = $description . " " . round($rowTool['head_weight'],1) . " lb.";
			}
			
			}
			
			elseif($st == "rake") {
				$type = "Garden";
				
				$query = "SELECT *
				FROM gardentool as T NATURAL JOIN rake
				WHERE T.tool_number = ".$row['tool_number'] ;

				$result = mysqli_query($db, $query);
				include('lib/show_queries.php'); 
				if (!empty($result)) {
					array_push($error_msg,  "SELECT ERROR:Failed to find friends ... <br>" . __FILE__ ." line:". __LINE__ );
				}
				
				if (isset($result)) {
					
					$rowTool = mysqli_fetch_array($result, MYSQLI_ASSOC);
					if(!is_null($rowTool['handle_material']))
						$description = $description . " " . $rowTool['handle_material'];
					if(!is_null($rowTool['tine_count']))
						$description = $description . " " . $rowTool['tine_count'] . " tine";    
			}
			
			}
			
			elseif($st == "wheelbarrow") {
				$type = "Garden";
				
				$query = "SELECT *
				FROM gardentool as T NATURAL JOIN wheelbarrow
				WHERE T.tool_number = ".$row['tool_number'] ;

				$result = mysqli_query($db, $query);
				include('lib/show_queries.php'); 
				if (!empty($result)) {
					array_push($error_msg,  "SELECT ERROR:Failed to find friends ... <br>" . __FILE__ ." line:". __LINE__ );
				}
				
				if (isset($result)) {
					
					$rowTool = mysqli_fetch_array($result, MYSQLI_ASSOC);
					if(!is_null($rowTool['handle_material']))
						$description = $description . " " . $rowTool['handle_material'];
					if(!is_null($rowTool['bin_meterial']))
						$description = $description . " " . $rowTool['bin_meterial'];
					if(!is_null($rowTool['bin_volume']))
						$description = $description . " " . ($rowTool['bin_volume']+0) . " cu ft.";   
					if(!is_null($rowTool['wheel_count']))
						$description = $description . " " . $rowTool['wheel_count'] . " wheeled";   					
			}
			
			}

			
			elseif($st == "screwdriver") {
				$type = "Hand";
				
				$query = "SELECT *
				FROM screwdriver
				WHERE tool_number = ".$row['tool_number'] ;

				$result = mysqli_query($db, $query);
				include('lib/show_queries.php'); 
				if (!empty($result)) {
					array_push($error_msg,  "SELECT ERROR:Failed to find friends ... <br>" . __FILE__ ." line:". __LINE__ );
				}
				
				if (isset($result)) {
					
					$rowTool = mysqli_fetch_array($result, MYSQLI_ASSOC);
					if(!is_null($rowTool['screw_size']))
						$description = $description . " #" . $rowTool['screw_size'];  					
			}
			
			}
			
			elseif($st == "socket") {
				$type = "Hand";
				
				$query = "SELECT *
				FROM socket
				WHERE tool_number = ".$row['tool_number'] ;

				$result = mysqli_query($db, $query);
				include('lib/show_queries.php'); 
				if (!empty($result)) {
					array_push($error_msg,  "SELECT ERROR:Failed to find friends ... <br>" . __FILE__ ." line:". __LINE__ );
				}
				
				if (isset($result)) {
					
					$rowTool = mysqli_fetch_array($result, MYSQLI_ASSOC);
					if(!is_null($rowTool['drive_size'])){
						
						$description = $description . " " . round($rowTool['drive_size'],1) . " in.";
					}	

					if(!is_null($rowTool['sae_size'])){
						$description = $description . " " . round($rowTool['sae_size'],1) . " in.";
					}					
			}
			
			}
			
			elseif($st == "ratchet") {
				$type = "Hand";
				
				$query = "SELECT *
				FROM ratchet
				WHERE tool_number = ".$row['tool_number'] ;

				$result = mysqli_query($db, $query);
				include('lib/show_queries.php'); 
				if (!empty($result)) {
					array_push($error_msg,  "SELECT ERROR:Failed to find friends ... <br>" . __FILE__ ." line:". __LINE__ );
				}
				
				if (isset($result)) {
					
					$rowTool = mysqli_fetch_array($result, MYSQLI_ASSOC);
					if(!is_null($rowTool['drive_size'])){
						
						$description = $description . " " . round($rowTool['drive_size'],1) . " in.";
					}  					
			}
			
			}
			
			elseif($st == "wrench") {
				$type = "Hand";
				
				$query = "SELECT *
				FROM wrench
				WHERE tool_number = ".$row['tool_number'] ;

				$result = mysqli_query($db, $query);
				include('lib/show_queries.php'); 
				if (!empty($result)) {
					array_push($error_msg,  "SELECT ERROR:Failed to find friends ... <br>" . __FILE__ ." line:". __LINE__ );
				}
				
				if (isset($result)) {
					
					$rowTool = mysqli_fetch_array($result, MYSQLI_ASSOC);
					if(!is_null($rowTool['drive_size'])){
						
						$description = $description . " " . round($rowTool['drive_size'],1) . " in.";
					}					
			}
			
			}
			
			elseif($st == "plier") {
				$type = "Hand";
				
				$query = "SELECT *
				FROM plier
				WHERE tool_number = ".$row['tool_number'] ;

				$result = mysqli_query($db, $query);
				include('lib/show_queries.php'); 
				if (!empty($result)) {
					array_push($error_msg,  "SELECT ERROR:Failed to find friends ... <br>" . __FILE__ ." line:". __LINE__ );
				}
				
				if (isset($result)) {
					
					$rowTool = mysqli_fetch_array($result, MYSQLI_ASSOC);
					if(!is_null($rowTool['adjustable'])){
						if( $rowTool['adjustable'] == 1 )
							$description = $description . " adjustable"; 	
					}
						 					
			}
			
			}
			
			elseif($st == "hammer") {
				$type = "Hand";
				
				$query = "SELECT *
				FROM hammer
				WHERE tool_number = ".$row['tool_number'] ;

				$result = mysqli_query($db, $query);
				include('lib/show_queries.php'); 
				if (!empty($result)) {
					array_push($error_msg,  "SELECT ERROR:Failed to find friends ... <br>" . __FILE__ ." line:". __LINE__ );
				}
				
				if (isset($result)) {
					
					$rowTool = mysqli_fetch_array($result, MYSQLI_ASSOC);
					if(!is_null($rowTool['anti_vibration'])){
						if( $rowTool['anti_vibration'] == 1 )
							$description = $description . " anti-vibration"; 	
					}
						 					
			}
			
			}
			
			elseif($st == "gun") {
				$type = "Hand";
				
				$query = "SELECT *
				FROM gun
				WHERE tool_number = ".$row['tool_number'] ;

				$result = mysqli_query($db, $query);
				include('lib/show_queries.php'); 
				if (!empty($result)) {
					array_push($error_msg,  "SELECT ERROR:Failed to find friends ... <br>" . __FILE__ ." line:". __LINE__ );
				}
				
				if (isset($result)) {
					
					$rowTool = mysqli_fetch_array($result, MYSQLI_ASSOC);
					if(!is_null($rowTool['gauge_rating']))
						$description = $description . " " . $rowTool['gauge_rating'] . " G"; 

					if(!is_null($rowTool['capacity']))
						$description = $description . " " . $rowTool['capacity']; 					
			}
			
			}
			
			elseif($st == "stepladder") {
				$type = "Ladder";
				
				$query = "SELECT *
				FROM ladder as T NATURAL JOIN stepladder
				WHERE T.tool_number = ".$row['tool_number'] ;

				$result = mysqli_query($db, $query);
				include('lib/show_queries.php'); 
				if (!empty($result)) {
					array_push($error_msg,  "SELECT ERROR:Failed to find friends ... <br>" . __FILE__ ." line:". __LINE__ );
				}
				
				if (isset($result)) {
					
					$rowTool = mysqli_fetch_array($result, MYSQLI_ASSOC);
					if(!is_null($rowTool['step_count']))
						$description = $description . " " . $rowTool['step_count'] . "-step";
					if(!is_null($rowTool['weight_capacity']))
						$description = $description . " " . ($rowTool['weight_capacity']+0) . " lb.";
					
					if(!is_null($rowTool['pail_shelf'])){
						if( $rowTool['pail_shelf'] == 1 )
							$description = $description . " pail-shelf"; 	
					}
			}
			
			}
			
			elseif($st == "straightladder") {
				$type = "Ladder";
				
				$query = "SELECT *
				FROM ladder as T NATURAL JOIN straightladder
				WHERE T.tool_number = ".$row['tool_number'] ;

				$result = mysqli_query($db, $query);
				include('lib/show_queries.php'); 
				if (!empty($result)) {
					array_push($error_msg,  "SELECT ERROR:Failed to find friends ... <br>" . __FILE__ ." line:". __LINE__ );
				}
				
				if (isset($result)) {
					
					$rowTool = mysqli_fetch_array($result, MYSQLI_ASSOC);
					if(!is_null($rowTool['step_count']))
						$description = $description . " " . $rowTool['step_count'] . "-step";
					if(!is_null($rowTool['weight_capacity']))
						$description = $description . " " . ($rowTool['weight_capacity']+0) . " lb.";
					
					if(!is_null($rowTool['rubber_feet'])){
						if( $rowTool['rubber_feet'] == 1 )
							$description = $description . " rubber-feet"; 	
					}
			}
			
			}
			
			elseif($st == "powerdrill") {
				$type = "Power";
				
				$query = "SELECT *
				FROM powertool as T NATURAL JOIN powerdrill
				WHERE T.tool_number = ".$row['tool_number'] ;

				$result = mysqli_query($db, $query);
				include('lib/show_queries.php'); 
				if (!empty($result)) {
					array_push($error_msg,  "SELECT ERROR:Failed to find friends ... <br>" . __FILE__ ." line:". __LINE__ );
				}
				
				if (isset($result)) {
					
					$rowTool = mysqli_fetch_array($result, MYSQLI_ASSOC);
					if(!is_null($rowTool['volt_rating']))
						$description = $description . " " . ($rowTool['volt_rating']+0) . " V";
					if(!is_null($rowTool['amp_rating']))
						$description = $description . " " . ($rowTool['amp_rating']+0) . " A";
					if(!is_null($rowTool['min_rpm']))
						$description = $description . " " . ($rowTool['min_rpm']+0) . " RPM";
					if(!is_null($rowTool['max_rpm']))
						$description = $description . " " . ($rowTool['max_rpm']+0) . " RPM";
					
					
					if(!is_null($rowTool['min_torque']))
						$description = $description . " " . ($rowTool['min_torque']+0) . " ft-lb";
					if(!is_null($rowTool['max_torque']))
						$description = $description . " " . ($rowTool['max_torque']+0) . " ft-lb";
					if(!is_null($rowTool['adjustable_clutch'])){
						if( $rowTool['adjustable_clutch'] == 1 )
							$description = $description . " adjustable-clutch"; 	
					}
					$accessory_query = "SELECT *
					FROM accessory as A NATURAL JOIN powerdrill
					WHERE A.tool_number = ".$row['tool_number'] ;
					$sql_result = mysqli_query($db, $accessory_query);
					if(mysqli_num_rows($sql_result)>0){
						$this_accessory ="";
						while ($accessory_row = mysqli_fetch_array($sql_result, MYSQLI_ASSOC)){
							$this_accessory = $this_accessory.$accessory_row['accessory_name'].": ".$accessory_row['accessory_description'].", Qty: ".$accessory_row['accessory_quantity'];
							$this_accessory = $this_accessory."\n";
						}
					}
				}
			
			}
			
			elseif($st == "powersaw") {
				$type = "Power";
				
				$query = "SELECT *
				FROM powertool as T NATURAL JOIN powersaw
				WHERE T.tool_number = ".$row['tool_number'] ;

				$result = mysqli_query($db, $query);
				include('lib/show_queries.php'); 
				if (!empty($result)) {
					array_push($error_msg,  "SELECT ERROR:Failed to find friends ... <br>" . __FILE__ ." line:". __LINE__ );
				}
				
				if (isset($result)) {
					
					$rowTool = mysqli_fetch_array($result, MYSQLI_ASSOC);
					if(!is_null($rowTool['volt_rating']))
						$description = $description . " " . ($rowTool['volt_rating']+0) . " V";
					if(!is_null($rowTool['amp_rating']))
						$description = $description . " " . ($rowTool['amp_rating']+0) . " A";
					if(!is_null($rowTool['min_rpm']))
						$description = $description . " " . ($rowTool['min_rpm']+0) . " RPM";
					if(!is_null($rowTool['max_rpm']))
						$description = $description . " " . ($rowTool['max_rpm']+0) . " RPM";
					
					
					if(!is_null($rowTool['blade_size'])){
						$quotient = (int)$rowTool['blade_size'] / 1;
						$mod = $fractions["".fmod($rowTool['blade_size'],1)];
						if($mod == 0)
							$mod = " in.";
						else
							$mod = "-" . $mod . " in.";
						$description = $description . " " . $quotient . $mod;
						
					}
					$accessory_query = "SELECT *
					FROM accessory as A NATURAL JOIN powersaw
					WHERE A.tool_number = ".$row['tool_number'] ;
					$sql_result = mysqli_query($db, $accessory_query);
					if(mysqli_num_rows($sql_result)>0){
						$this_accessory ="";
						while ($accessory_row = mysqli_fetch_array($sql_result, MYSQLI_ASSOC)){
							$this_accessory = $this_accessory.$accessory_row['accessory_name'].": ".$accessory_row['accessory_description'].", Qty: ".$accessory_row['accessory_quantity'];
							$this_accessory = $this_accessory."\n";
						}
					}
				}
			}
			
			elseif($st == "powersander") {
				$type = "Power";
				
				$query = "SELECT *
				FROM powertool as T NATURAL JOIN powersander
				WHERE T.tool_number = ".$row['tool_number'] ;

				$result = mysqli_query($db, $query);
				include('lib/show_queries.php'); 
				if (!empty($result)) {
					array_push($error_msg,  "SELECT ERROR:Failed to find friends ... <br>" . __FILE__ ." line:". __LINE__ );
				}
				
				if (isset($result)) {
					
					$rowTool = mysqli_fetch_array($result, MYSQLI_ASSOC);
					if(!is_null($rowTool['volt_rating']))
						$description = $description . " " . ($rowTool['volt_rating']+0) . " V";
					if(!is_null($rowTool['amp_rating']))
						$description = $description . " " . ($rowTool['amp_rating']+0) . " A";
					if(!is_null($rowTool['min_rpm']))
						$description = $description . " " . ($rowTool['min_rpm']+0) . " RPM";
					if(!is_null($rowTool['max_rpm']))
						$description = $description . " " . ($rowTool['max_rpm']+0) . " RPM";
					
					
					if(!is_null($rowTool['dust_bag'])){
						if( $rowTool['dust_bag'] == 1 )
							$description = $description . " dust-bag"; 	
					}
					$accessory_query = "SELECT *
					FROM accessory as A NATURAL JOIN powersander
					WHERE A.tool_number = ".$row['tool_number'] ;
					$sql_result = mysqli_query($db, $accessory_query);
					if(mysqli_num_rows($sql_result)>0){
						$this_accessory ="";
						while ($accessory_row = mysqli_fetch_array($sql_result, MYSQLI_ASSOC)){
							$this_accessory = $this_accessory.$accessory_row['accessory_name'].": ".$accessory_row['accessory_description'].", Qty: ".$accessory_row['accessory_quantity'];
							$this_accessory = $this_accessory."\n";
						}
					}
				}
			
			}
			
			elseif($st == "poweraircompressor") {
				$type = "Power";
				
				$query = "SELECT *
				FROM powertool as T NATURAL JOIN poweraircompressor
				WHERE T.tool_number = ".$row['tool_number'] ;

				$result = mysqli_query($db, $query);
				include('lib/show_queries.php'); 
				if (!empty($result)) {
					array_push($error_msg,  "SELECT ERROR:Failed to find friends ... <br>" . __FILE__ ." line:". __LINE__ );
				}
				
				if (isset($result)) {
					
					$rowTool = mysqli_fetch_array($result, MYSQLI_ASSOC);
					if(!is_null($rowTool['volt_rating']))
						$description = $description . " " . ($rowTool['volt_rating']+0) . " V";
					if(!is_null($rowTool['amp_rating']))
						$description = $description . " " . ($rowTool['amp_rating']+0) . " A";
					if(!is_null($rowTool['min_rpm']))
						$description = $description . " " . ($rowTool['min_rpm']+0) . " RPM";
					if(!is_null($rowTool['max_rpm']))
						$description = $description . " " . ($rowTool['max_rpm']+0) . " RPM";
					
					
					if(!is_null($rowTool['tank_size']))
						$description = $description . " " . ($rowTool['tank_size']+0) . " gal";
					if(!is_null($rowTool['pressure_rating']))
						$description = $description . " " . ($rowTool['pressure_rating']+0) . " psi";
					$accessory_query = "SELECT *
					FROM accessory as A NATURAL JOIN poweraircompressor
					WHERE A.tool_number = ".$row['tool_number'] ;
					$sql_result = mysqli_query($db, $accessory_query);
					if(mysqli_num_rows($sql_result)>0){
						$this_accessory ="";
						while ($accessory_row = mysqli_fetch_array($sql_result, MYSQLI_ASSOC)){
							$this_accessory = $this_accessory.$accessory_row['accessory_name'].": ".$accessory_row['accessory_description'].", Qty: ".$accessory_row['accessory_quantity'];
							$this_accessory = $this_accessory."\n";
						}
					}
			}
			
			}
			
			elseif($st == "powermixer") {
				$type = "Power";
				
				$query = "SELECT *
				FROM powertool as T NATURAL JOIN powermixer
				WHERE T.tool_number = ".$row['tool_number'] ;

				$result = mysqli_query($db, $query);
				include('lib/show_queries.php'); 
				if (!empty($result)) {
					array_push($error_msg,  "SELECT ERROR:Failed to find friends ... <br>" . __FILE__ ." line:". __LINE__ );
				}
				
				if (isset($result)) {
					
					$rowTool = mysqli_fetch_array($result, MYSQLI_ASSOC);
					if(!is_null($rowTool['volt_rating']))
						$description = $description . " " . ($rowTool['volt_rating']+0) . " V";
					if(!is_null($rowTool['amp_rating']))
						$description = $description . " " . ($rowTool['amp_rating']+0) . " A";
					if(!is_null($rowTool['min_rpm']))
						$description = $description . " " . ($rowTool['min_rpm']+0) . " RPM";
					if(!is_null($rowTool['max_rpm']))
						$description = $description . " " . ($rowTool['max_rpm']+0) . " RPM";
					
					
					if(!is_null($rowTool['motor_rating']))
						$description = $description . " " . $fractions["".fmod($rowTool['motor_rating'],1)] . " HP";
					if(!is_null($rowTool['drum_size']))
						$description = $description . " " . ($rowTool['drum_size']+0) . " cu-ft";
					$accessory_query = "SELECT *
					FROM accessory as A NATURAL JOIN powermixer
					WHERE A.tool_number = ".$row['tool_number'] ;
					$sql_result = mysqli_query($db, $accessory_query);
					if(mysqli_num_rows($sql_result)>0){
						$this_accessory ="";
						while ($accessory_row = mysqli_fetch_array($sql_result, MYSQLI_ASSOC)){
							$this_accessory = $this_accessory.$accessory_row['accessory_name'].": ".$accessory_row['accessory_description'].", Qty: ".$accessory_row['accessory_quantity'];
							$this_accessory = $this_accessory."\n";
						}
					}
			}
			
			}
			
			elseif($st == "powergenerator") {
				$type = "Power";
				
				$query = "SELECT *
				FROM powertool as T NATURAL JOIN powergenerator
				WHERE T.tool_number = ".$row['tool_number'] ;

				$result = mysqli_query($db, $query);
				include('lib/show_queries.php'); 
				if (!empty($result)) {
					array_push($error_msg,  "SELECT ERROR:Failed to find friends ... <br>" . __FILE__ ." line:". __LINE__ );
				}
				
				if (isset($result)) {
					
					$rowTool = mysqli_fetch_array($result, MYSQLI_ASSOC);
					if(!is_null($rowTool['volt_rating']))
						$description = $description . " " . ($rowTool['volt_rating']+0) . " V";
					if(!is_null($rowTool['amp_rating']))
						$description = $description . " " . ($rowTool['amp_rating']+0) . " A";
					if(!is_null($rowTool['min_rpm']))
						$description = $description . " " . ($rowTool['min_rpm']+0) . " RPM";
					if(!is_null($rowTool['max_rpm']))
						$description = $description . " " . ($rowTool['max_rpm']+0) . " RPM";
					
					
					if(!is_null($rowTool['power_rating']))
						$description = $description . " " . ($rowTool['power_rating']+0) . " Watt";
					$accessory_query = "SELECT *
					FROM accessory as A NATURAL JOIN powergenerator
					WHERE A.tool_number = ".$row['tool_number'] ;
					$sql_result = mysqli_query($db, $accessory_query);
					if(mysqli_num_rows($sql_result)>0){
						$this_accessory ="";
						while ($accessory_row = mysqli_fetch_array($sql_result, MYSQLI_ASSOC)){
							$this_accessory = $this_accessory.$accessory_row['accessory_name'].": ".$accessory_row['accessory_description'].", Qty: ".$accessory_row['accessory_quantity'];
							$this_accessory = $this_accessory."\n";
						}
					}
			}
			
			}
			
			
			
		$description = $description . " by " . $row['manufacturer'];}
			
}
			
?>


<div class="row my-4">
  <div class="col-12">
    <!-- Tool Details -->
    <h1 class="h2">
      Tool Details</br>
    </h1>
      <dl class="row">
        <dt class="col-sm-3 col-lg-2">Tool number</dt>
        <dd class="col-sm-9 col-lg-10">
          <?php print($row['tool_number']); ?>
        </dd>

        <dt class="col-sm-3 col-lg-2">Type</dt>
        <dd class="col-sm-9 col-lg-10">
          <?php print($type); ?>
        </dd>
		
		<dt class="col-sm-3 col-lg-2">Short Description</dt>
        <dd class="col-sm-9 col-lg-10">
		<?php
			if($row['power_source'] == "manual")
				print($row['sub_option'] . ' ' . $row['sub_type']);
			else
				print($row['power_source']. ' ' . $row['sub_option'] . ' ' . $row['sub_type']);
		
		?>
        </dd>
		
        <dt class="col-sm-3 col-lg-2">Full Description</dt>
        <dd class="col-sm-9 col-lg-10">
           <?php print($description); ?>
        </dd>

        <!-- If exists-->
        <dt class="col-sm-3 col-lg-2">Deposit price</dt>
        <dd class="col-sm-9 col-lg-10">
          <?php print(round($row['price'] * 0.4,2)); ?>
        </dd>

        <!-- If exists-->
        <dt class="col-sm-3 col-lg-2">Rental price</dt>
        <dd class="col-sm-9 col-lg-10">
          <?php print(round($row['price'] * 0.15,2)); ?>
        </dd>
		
		<dt class="col-sm-3 col-lg-2">Accessories</dt>
        <dd class="col-sm-9 col-lg-10">
          <?php print(nl2br($this_accessory)); ?>
        </dd>
		
      </dl>
  </div>
</div>
