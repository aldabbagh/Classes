<?php
include('lib/common.php');
include('lib/lists.php');

// Default input values
$inputs = [
  'type'                  => ['value' => '', 'required' => '*'],
  'powersource'           => ['value' => '', 'required' => '*'],
  'subtype'               => ['value' => '', 'required' => '*'],
  'suboption'             => ['value' => '', 'required' => '*'],
  'price'                 => ['value' => '', 'required' => '*'],
  'manufacturer'          => ['value' => '', 'required' => '*'],
  'material'              => ['value' => '', 'required' => []],
  'width'                 => ['value' => '', 'required' => '*'],
  'width-fraction'        => ['value' => '', 'required' => '*'],
  'width-unit'            => ['value' => '', 'required' => '*'],
  'length'                => ['value' => '', 'required' => '*'],
  'length-fraction'       => ['value' => '', 'required' => '*'],
  'length-unit'           => ['value' => '', 'required' => '*'],
  'weight'                => ['value' => '', 'required' => '*'],
  'screw-size'            => ['value' => '', 'required' => ['screwdriver']],
  'drive-size'            => ['value' => '', 'required' => ['socket',
                                                            'ratchet']],
  'sae-size'              => ['value' => '', 'required' => ['socket']],
  'adjustable'            => ['value' => '', 'required' => ['plier']],
  'gauge-rating'          => ['value' => '', 'required' => ['gun']],
  'capacity'              => ['value' => '', 'required' => ['gun']],
  'anti-vibration'        => ['value' => '', 'required' => []],
  'handle-material'       => ['value' => '', 'required' => ['garden']],
  'blade-material'        => ['value' => '', 'required' => []],
  'blade-length'          => ['value' => '', 'required' => ['pruning',
                                                            'digging']],
  'head-weight'           => ['value' => '', 'required' => ['striking']],
  'blade-width'           => ['value' => '', 'required' => ['digging']],
  'tine-count'            => ['value' => '', 'required' => ['rake']],
  'bin-material'          => ['value' => '', 'required' => ['wheelbarrow']],
  'bin-volume'            => ['value' => '', 'required' => []],
  'wheel-count'           => ['value' => '', 'required' => ['wheelbarrow']],
  'step-count'            => ['value' => '', 'required' => ['ladder']],
  'weight-capacity'       => ['value' => '', 'required' => ['ladder']],
  'rubber-feet'           => ['value' => '', 'required' => []],
  'pail-shelf'            => ['value' => '', 'required' => []],
  'volt-rating'           => ['value' => '', 'required' => ['electric','gas']],
  'amp-rating'            => ['value' => '', 'required' => ['power']],
  'amp-unit'              => ['value' => '', 'required' => ['power']],
  'min-rpm-rating'        => ['value' => '', 'required' => ['power']],
  'max-rpm-rating'        => ['value' => '', 'required' => []],
  'adjustable-clutch'     => ['value' => '', 'required' => []],
  'min-torque-rating'     => ['value' => '', 'required' => ['powerdrill']],
  'max-torque-rating'     => ['value' => '', 'required' => []],
  'blade-size'            => ['value' => '', 'required' => ['powersaw']],
  'dust-bag'              => ['value' => '', 'required' => ['powersander']],
  'tank-size'             => ['value' => '', 'required' =>
                                                    ['poweraircompressor']],
  'pressure-rating'       => ['value' => '', 'required' => []],
  'motor-rating'          => ['value' => '', 'required' => ['powermixer']],
  'drum-size'             => ['value' => '', 'required' => ['powermixer']],
  'power-rating'          => ['value' => '', 'required' => ['powergenerator']],
  'power-unit'            => ['value' => '', 'required' => ['powergenerator']],
  'battery-type'          => ['value' => '', 'required' => ['cordless']],
  'battery-volt-rating'   => ['value' => '', 'required' => ['cordless']],
  'battery-quantity'      => ['value' => '', 'required' => ['cordless']],

  'accessories'           => ['value' => [], 'required' => []]
];

// Default error states
$alert = '';
$success = '';
$errors = [];

if ( $_SERVER['REQUEST_METHOD'] === 'POST') {

  // sanitize inputs for mysql
  foreach ($_POST as $key => $value) {
    if ($key !== 'accessories') {
      $inputs[$key]['value'] = mysqli_real_escape_string($db, $_POST[$key]);

    } else {
      foreach ($_POST[$key] as $index => $accessory) {
        array_push(
          $inputs[$key]['value'],
          ['accessory-description' =>
            mysqli_real_escape_string($db, $accessory['accessory-description']),
           'accessory-quantity' =>
            mysqli_real_escape_string($db, $accessory['accessory-quantity']),
          ]
        );
      }
    }
  }

  // Validation for missing inputs
  foreach ($inputs as $key => $input) {
    if ($input['value'] === '') {
      if ($input['required'] === '*') {
        $errors[$key] = 'This field is required';

      } elseif (count($input['required']) > 0) {
        if (in_array($inputs['type']['value'], $input['required']) ||
            in_array($inputs['subtype']['value'], $input['required']) ||
            in_array($inputs['powersource']['value'], $input['required'])) {
          $errors[$key] = 'This field is required';
        }
      }
    }
  }

  foreach ($inputs['accessories']['value'] as $i => $accessory) {
    if ($accessory['accessory-description'] === '') {
      $errors['accessories'][$i]['accessory-description'] = 'This field is required';
    }
    if ($accessory['accessory-quantity'] === '') {
      $errors['accessories'][$i]['accessory-quantity'] = 'This field is required';
    }
  }


  // Validation for value of inputs
  if ($inputs['type']['value'] !== '') {
    if ($inputs['type']['value'] !== 'hand' &&
        $inputs['type']['value'] !== 'garden' &&
        $inputs['type']['value'] !== 'ladder' &&
        $inputs['type']['value'] !== 'power') {
      $errors['type'] = 'Please select a valid type';
    }
  }

  if ($inputs['powersource']['value'] !== '') {
    if ($inputs['powersource']['value'] !== 'electric' &&
        $inputs['powersource']['value'] !== 'cordless' &&
        $inputs['powersource']['value'] !== 'gas' &&
        $inputs['powersource']['value'] !== 'manual') {
      $errors['powersource'] = 'Please select a valid power source';
    }
  }

  if ($inputs['subtype']['value'] !== '') {
    if (!in_array($inputs['subtype']['value'],
                  ['screwdriver', 'socket', 'ratchet', 'wrench', 'plier',
                  'gun', 'hammer', 'digging', 'pruning', 'rake', 'wheelbarrow',
                  'striking', 'straightladder', 'stepladder', 'powerdrill',
                  'powersaw', 'powersander', 'powermixer', 'poweraircompressor',
                  'powergenerator'])) {
      $errors['subtype'] = 'Please select a valid subtype';
    }
  }

  if ($inputs['suboption']['value'] !== '') {
    if (strlen($inputs['suboption']['value']) > 16) {
      $errors['suboption'] = 'Please select a valid subtype';
    }
  }

  foreach (['price', 'weight', 'sae-size', 'blade-width',
            'head-weight', 'blade-length', 'bin-volume',
            'weight-capacity', 'blade-size', 'tank-size', 'pressure-rating',
            'motor-rating', 'drum-size', 'min-rpm-rating', 'max-rpm-rating',
            'min-torque-rating', 'max-torque-rating'] as $key) {
    if ($inputs[$key]['value'] !== '') {
      if (floatval($inputs[$key]['value']) <= 0 ||
          floatval($inputs[$key]['value']) > 999999.9999 ||
          !is_numeric($inputs[$key]['value'])) {
        $errors[$key] = 'Must be between 0 and 999,999.9999';
      }
    }
  }

  if ($inputs['manufacturer']['value'] !== '') {
    if (strlen($inputs['manufacturer']['value']) > 100) {
      $errors['manufacturer'] = 'Cannot exceed 100 characters';
    }
  }

  foreach (['material', 'handle-material', 'blade-material',
            'bin-material'] as $key) {
    if ($inputs[$key]['value'] !== '') {
      if (strlen($inputs[$key]['value']) > 50) {
        $errors[$key] = 'Cannot exceed 50 characters';
      }
    }
  }

  if ($inputs['width']['value'] !== '') {
    if (floatval($inputs['width']['value']) <= 0 ||
       (floatval($inputs['width']['value']) +
        floatval($inputs['width-fraction']['value'])) *
        floatval($inputs['width-unit']['value']) > 999999.9999 ||
        !is_numeric($inputs['width']['value'])) {
      $errors['width'] = 'Must be between 0 and 999,999.9999';
    }
  }

  if ($inputs['length']['value'] !== '') {
    if (floatval($inputs['length']['value']) <= 0 ||
       (floatval($inputs['length']['value']) +
        floatval($inputs['length-fraction']['value'])) *
        floatval($inputs['length-unit']['value']) > 999999.9999 ||
        !is_numeric($inputs['length']['value'])) {
      $errors['length'] = 'Must be between 0 and 999,999.9999';
    }
  }

  if ($inputs['screw-size']['value'] !== '') {
    if (intval($inputs['screw-size']['value']) < 0 ||
        intval($inputs['screw-size']['value']) > 12 ||
        !is_numeric($inputs['screw-size']['value'])) {
      $errors['screw-size'] = 'Must be between 0 and 12';
    }
  }

  if ($inputs['drive-size']['value'] !== '') {
    if (!in_array($inputs['drive-size']['value'], $drive_size_list)) {
      $errors['drive-size'] = 'Please select a valid drive size';
    }
  }

  foreach (['adjustable', 'anti-vibration', 'rubber-feet',
            'pail-shelf', 'adjustable-clutch', 'dust-bag'] as $key) {
    if ($inputs[$key]['value'] !== '') {
      if ($inputs[$key]['value'] !== '1' &&
          $inputs[$key]['value'] !== '0') {
        $errors[$key] = 'Please select a valid value';
      }
    }
  }

  if ($inputs['gauge-rating']['value'] !== '') {
    if (!in_array($inputs['gauge-rating']['value'], $gauge_rating_list)) {
      $errors['gauge-rating'] = 'Please select a valid gauge rating';
    }
  }

  foreach (['capacity', 'tine-count', 'wheel-count', 'step-count',
            'battery-quantity'] as $key) {
    if ($inputs[$key]['value'] !== '') {
      if (intval($inputs[$key]['value']) <= 0 ||
          intval($inputs[$key]['value']) > 2147483647 ||
          !is_numeric($inputs[$key]['value'])) {
        $errors[$key] = 'Must be between 0 and 2147483647';
      }
    }
  }

  if ($inputs['volt-rating']['value'] !== '') {
    if (!in_array($inputs['volt-rating']['value'], $volt_rating_list)) {
      $errors['volt-rating'] = 'Please select a valid volt rating';
    }
  }

  if ($inputs['amp-rating']['value'] !== '') {
    if (floatval($inputs['amp-rating']['value']) <= 0 ||
       (floatval($inputs['amp-rating']['value']) *
        floatval($inputs['amp-unit']['value'])) > 999999.9999 ||
        !is_numeric($inputs['amp-rating']['value'])) {
      $errors['amp-rating'] = 'Must be between 0 and 999,999.9999';
    }
  }

  if ($inputs['power-rating']['value'] !== '') {
    if (floatval($inputs['power-rating']['value']) <= 0 ||
       (floatval($inputs['power-rating']['value']) *
        floatval($inputs['power-unit']['value'])) > 999999.9999 ||
        !is_numeric($inputs['power-rating']['value'])) {
      $errors['power-rating'] = 'Must be between 0 and 999,999.9999';
    }
  }

  foreach ($inputs['accessories']['value'] as $i => $accessory) {
    if ($accessory['accessory-description'] !== '') {
      if (strlen($accessory['accessory-description']) > 120) {
        $errors['accessories'][$i]['accessory-description'] = 'Cannot exceed 120 characters';
      }
    }
    if ($accessory['accessory-quantity'] !== '') {
      if (intval($accessory['accessory-quantity']) <= 0 ||
          intval($accessory['accessory-quantity']) > 2147483647 ||
          !is_numeric($$accessory['accessory-quantity'])) {
        $errors['accessories'][$i]['accessory-quantity'] = 'Must be between 0 and 2147483647';
      }
    }
  }

  if ($inputs['battery-type']['value'] !== '') {
    if (!in_array($inputs['battery-type']['value'], ['Li-Ion', 'NiCd',
                                                    'NiMH'])) {
      $errors['battery-type'] = 'Please select a valid volt rating';
    }
  }

  if ($inputs['battery-volt-rating']['value'] !== '') {
    if (floatval($inputs['battery-volt-rating']['value']) < 7.2 ||
        floatval($inputs['battery-volt-rating']['value']) > 80 ||
        !is_numeric($inputs['battery-volt-rating']['value'])) {
      $errors['battery-volt-rating'] = 'Must be between 7.2V and 80V';
    }
  }


  if (count($errors) === 0) {

    $toolType = $inputs['type']['value'];
    $powerSource = $inputs['powersource']['value'];
    $subType = $inputs['subtype']['value'];
    $subOption = $inputs['suboption']['value'];
    $price = $inputs['price']['value'];
    $width = $inputs['width']['value'];
    $length = $inputs['length']['value'];
    $weight = $inputs['weight']['value'];
    $material = $inputs['material']['value'];
    $manufacturer = $inputs['manufacturer']['value'];

    mysqli_begin_transaction($db);


    // HAND
    if ($toolType == 'hand') {

      $query = empty_to_null("INSERT INTO `Tool` (power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
      VALUES ('".$powerSource."', '".$subType."', '".$subOption."', ".$price.",".$inputs['width-unit']['value']."*(".$width."+".$inputs['width-fraction']['value']."), ".$inputs['length-unit']['value']."*(".$length."+".$inputs['length-fraction']['value']."), ".$weight.", '".$material."', '".$manufacturer."')");

      $queryID = mysqli_query($db, $query);

      include('lib/show_queries.php');
      if ($queryID  == False) {
        $alert = 'Internal server error :(';
        mysqli_rollback($db);

      } else {

        $query = "SET @last_id_in_tool = LAST_INSERT_ID()";
        $queryID = mysqli_query($db, $query);

        $query ="INSERT INTO `HandTool`
        VALUES (@last_id_in_tool)";

        $queryID = mysqli_query($db, $query);

        include('lib/show_queries.php');
        if ($queryID  == False) {
          $alert = 'Internal server error :(';
          mysqli_rollback($db);

        } else {

          if ($subType == 'screwdriver') {
            $query ="INSERT INTO `ScrewDriver` (tool_number, screw_size)
            VALUES (@last_id_in_tool, ".$inputs['screw-size']['value'].")";
            $queryID = mysqli_query($db, $query);

          } else if ($subType == 'socket') {
            $query ="INSERT INTO `Socket` (tool_number, drive_size, sae_size)
            VALUES (@last_id_in_tool, ".$inputs['drive-size']['value'].",".$inputs['sae-size']['value'].")";
            $queryID = mysqli_query($db, $query);

          } elseif ($subType == 'ratchet') {
            $query ="INSERT INTO `Ratchet` (tool_number, drive_size)
            VALUES (@last_id_in_tool, ".$inputs['drive-size']['value'].")";
            $queryID = mysqli_query($db, $query);

          } elseif ($subType == 'wrench') {
            $query ="INSERT INTO `Wrench` (tool_number, drive_size)
            VALUES (@last_id_in_tool, ".$inputs['drive-size']['value'].")";
            $queryID = mysqli_query($db, $query);

          } elseif ($subType == 'plier') {
            $query ="INSERT INTO `Plier` (tool_number, adjustable)
            VALUES (@last_id_in_tool, ".$inputs['adjustable']['value'].")";
            $queryID = mysqli_query($db, $query);

          } elseif ($subType == 'gun') {
            $query ="INSERT INTO `Gun` (tool_number, gauge_rating, capacity)
            VALUES (@last_id_in_tool, ".$inputs['gauge-rating']['value'].",".$inputs['capacity']['value'].")";
            $queryID = mysqli_query($db, $query);

          //hammer
          } else {
            $query = empty_to_null("INSERT INTO `Hammer` (tool_number, anti_vibration)
            VALUES (@last_id_in_tool, ".$inputs['anti-vibration']['value'].")");
            $queryID = mysqli_query($db, $query);
          }

          include('lib/show_queries.php');
          if ($queryID  == False) {
            $alert = 'Internal server error :(';
            mysqli_rollback($db);
          } else {
            $success = 'Hand tool added';
            mysqli_commit($db);
          }
        }
      }


    // GARDEN
    } else if ($toolType == 'garden') {

      $query = empty_to_null("INSERT INTO `Tool` (power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
      VALUES ('".$powerSource."', '".$subType."', '".$subOption."', ".$price.", ".$inputs['width-unit']['value']."*(".$width."+".$inputs['width-fraction']['value']."), ".$inputs['length-unit']['value']."*(".$length."+".$inputs['length-fraction']['value']."), ".$weight.", '".$material."', '".$manufacturer."')");

      $queryID = mysqli_query($db, $query);

      include('lib/show_queries.php');
      if ($queryID  == False) {
        $alert = 'Internal server error :(';
        mysqli_rollback($db);

      } else {

        $query = "SET @last_id_in_tool = LAST_INSERT_ID()";
        $queryID = mysqli_query($db, $query);

        $query ="INSERT INTO `GardenTool`(tool_number, handle_material)
        VALUES (@last_id_in_tool,'".$inputs['handle-material']['value']."')";

        $queryID = mysqli_query($db, $query);

        include('lib/show_queries.php');
        if ($queryID  == False) {
          $alert = 'Internal server error :(';
          mysqli_rollback($db);

        } else {

          if ($subType == 'digging') {
            $query ="INSERT INTO `DIGGING` (tool_number, blade_width, blade_length)
            VALUES (@last_id_in_tool, ".$inputs['blade-width']['value'].",".$inputs['blade-length']['value'].")";
            $queryID = mysqli_query($db, $query);

          } elseif ($subType == 'pruning') {
            $query = empty_to_null("INSERT INTO `Pruning` (tool_number, blade_material, blade_length)
            VALUES (@last_id_in_tool, '".$inputs['blade-material']['value']."',".$inputs['blade-length']['value'].")");
            $queryID = mysqli_query($db, $query);

          } elseif ($subType == 'rake') {
            $query ="INSERT INTO `Rake` (tool_number, tine_count)
            VALUES (@last_id_in_tool, ".$inputs['tine-count']['value'].")";
            $queryID = mysqli_query($db, $query);

          } elseif ($subType == 'wheelbarrow') {
            $query = empty_to_null("INSERT INTO `WheelBarrow` (tool_number, bin_material, bin_volume, wheel_count)
            VALUES (@last_id_in_tool,'".$inputs['bin-material']['value']."', ".$inputs['bin-volume']['value'].",".$inputs['wheel-count']['value'].")");
            $queryID = mysqli_query($db, $query);

          //striking
          } else {
            $query ="INSERT INTO `Striking` (tool_number, head_weight)
            VALUES (@last_id_in_tool, ".$inputs['head-weight']['value'].")";
            $queryID = mysqli_query($db, $query);
          }

          include('lib/show_queries.php');
          if ($queryID  == False) {
            $alert = 'Internal server error :(';
            mysqli_rollback($db);
          } else {
            $success = 'Garden tool added';
            mysqli_commit($db);
          }
        }
      }


    // LADDER
    } elseif ($toolType == 'ladder') {

      $query = empty_to_null("INSERT INTO `Tool` (power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
      VALUES ('".$powerSource."', '".$subType."', '".$subOption."', ".$price.", ".$inputs['width-unit']['value']."*(".$width."+".$inputs['width-fraction']['value']."), ".$inputs['length-unit']['value']."*(".$length."+".$inputs['length-fraction']['value']."), ".$weight.", '".$material."', '".$manufacturer."')");

      $queryID = mysqli_query($db, $query);

      include('lib/show_queries.php');
      if ($queryID  == False) {
        $alert = 'Internal server error :(';
        mysqli_rollback($db);

      } else {

        $query = "SET @last_id_in_tool = LAST_INSERT_ID()";
        $queryID = mysqli_query($db, $query);

        $query ="INSERT INTO `Ladder`(tool_number, step_count, weight_capacity)
        VALUES (@last_id_in_tool, ".$inputs['step-count']['value'].",".$inputs['weight-capacity']['value'].")";

        $queryID = mysqli_query($db, $query);

        include('lib/show_queries.php');
        if ($queryID  == False) {
          $alert = 'Internal server error :(';
          mysqli_rollback($db);

        } else {

          if ($subType == 'straightladder') {
            $query = empty_to_null("INSERT INTO `StraightLadder` (tool_number, rubber_feet)
            VALUES (@last_id_in_tool, ".$inputs['rubber-feet']['value'].")");
            $queryID = mysqli_query($db, $query);

          // step
          } else {
            $query = empty_to_null("INSERT INTO `StepLadder` (tool_number, pail_shelf)
            VALUES (@last_id_in_tool, ".$inputs['pail-shelf']['value'].")");
            $queryID = mysqli_query($db, $query);
          }

          include('lib/show_queries.php');
          if ($queryID  == False) {
            $alert = 'Internal server error :(';
            mysqli_rollback($db);
          } else {
            $success = 'Ladder tool added';
            mysqli_commit($db);
          }
        }
      }


    // POWER
    } elseif ($toolType == 'power') {

      $query = empty_to_null("INSERT INTO `Tool` (power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
      VALUES ('".$powerSource."', '".$subType."', '".$subOption."', ".$price.", ".$inputs['width-unit']['value']."*(".$width."+".$inputs['width-fraction']['value']."), ".$inputs['length-unit']['value']."*(".$length."+".$inputs['length-fraction']['value']."), ".$weight.", '".$material."', '".$manufacturer."')");

      $queryID = mysqli_query($db, $query);

      include('lib/show_queries.php');
      if ($queryID  == False) {
        $alert = 'Internal server error :(';
        mysqli_rollback($db);

      } else {

        $query = "SET @last_id_in_tool = LAST_INSERT_ID()";
        $queryID = mysqli_query($db, $query);

        if ($powerSource == 'cordless') {
          $query = empty_to_null("INSERT INTO `PowerTool`(tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
          VALUES (@last_id_in_tool,".$inputs['battery-volt-rating']['value'].",".$inputs['amp-unit']['value']."*".$inputs['amp-rating']['value'].",".$inputs['min-rpm-rating']['value'].",".$inputs['max-rpm-rating']['value'].")");

          $queryID = mysqli_query($db, $query);

          include('lib/show_queries.php');
          if ($queryID  == False) {
            $alert = 'Internal server error :(';
            mysqli_rollback($db);

          } else {

            $query ="INSERT INTO `Accessory`
            VALUES (@last_id_in_tool, 'battery','".$inputs['battery-type']['value']."',".$inputs['battery-quantity']['value'].")";

            $queryID = mysqli_query($db, $query);

            include('lib/show_queries.php');
            if ($queryID  == False) {
              $alert = 'Internal server error :(';
              mysqli_rollback($db);

            } else {

              $query ="INSERT INTO `CordlessPowerTool`
              VALUES (@last_id_in_tool, '".$inputs['battery-type']['value']."')";

              $queryID = mysqli_query($db, $query);

              include('lib/show_queries.php');
              if ($queryID  == False) {
                $alert = 'Internal server error :(';
                mysqli_rollback($db);
              }
            }
          }

        } else {
          $query = empty_to_null("INSERT INTO `PowerTool`(tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
          VALUES (@last_id_in_tool,".$inputs['volt-rating']['value'].",".$inputs['amp-unit']['value']."*".$inputs['amp-rating']['value'].",".$inputs['min-rpm-rating']['value'].",".$inputs['max-rpm-rating']['value'].")");

          $queryID = mysqli_query($db, $query);

          include('lib/show_queries.php');
          if ($queryID  == False) {
            $alert = 'Internal server error :(';
            mysqli_rollback($db);
          }
        }

        if ($queryID != False) {

          if ($subType == 'powerdrill') {
            $query = empty_to_null("INSERT INTO `PowerDrill` (tool_number, min_torque, max_torque, adjustable_clutch)
            VALUES (@last_id_in_tool, ".$inputs['min-torque-rating']['value'].",".$inputs['max-torque-rating']['value'].",".$inputs['adjustable-clutch']['value'].")");
            $queryID = mysqli_query($db, $query);

          } elseif ($subType == 'powersaw') {
            $query ="INSERT INTO `PowerSaw` (tool_number, blade_size)
            VALUES (@last_id_in_tool, ".$inputs['blade-size']['value'].")";
            $queryID = mysqli_query($db, $query);

          } elseif ($subType == 'powersander') {
            $query ="INSERT INTO `PowerSander` (tool_number, dust_bag)
            VALUES (@last_id_in_tool, ".$inputs['dust-bag']['value'].")";
            $queryID = mysqli_query($db, $query);

          } elseif ($subType == 'poweraircompressor') {
            $query = empty_to_null("INSERT INTO `PowerAirCompressor` (tool_number, tank_size, pressure_rating)
            VALUES (@last_id_in_tool, ".$inputs['tank-size']['value'].",".$inputs['pressure-rating']['value'].")");
            $queryID = mysqli_query($db, $query);

          } elseif ($subType == 'powermixer') {
            $query ="INSERT INTO `PowerMixer` (tool_number, motor_rating, drum_size)
            VALUES (@last_id_in_tool, ".$inputs['motor-rating']['value'].",".$inputs['drum-size']['value'].")";
            $queryID = mysqli_query($db, $query);

          // generator
          } else {
            $query ="INSERT INTO `PowerGenerator` (tool_number, power_rating)
            VALUES (@last_id_in_tool, ".$inputs['power-unit']['value']."*".$inputs['power-rating']['value'].")";
            $queryID = mysqli_query($db, $query);
          }

          include('lib/show_queries.php');
          if ($queryID == False) {
            $alert = 'Internal server error :(';
            mysqli_rollback($db);

          } else {

            // ACCESSORIES
            if (count($inputs['accessories']['value']) > 0) {
              foreach ($inputs['accessories']['value'] as $i => $accessory) {
                $query ="INSERT INTO `Accessory`
                VALUES (@last_id_in_tool, 'Accessory-".$i."','".$accessory['accessory-description']."',".$accessory['accessory-quantity'].")";
                $queryID = mysqli_query($db, $query);

                include('lib/show_queries.php');
                if ($queryID == False) {
                  $alert = 'Internal server error :(';
                  mysqli_rollback($db);
                } else {
                  $success = 'Power tool added';
                  mysqli_commit($db);
                }
              }

            // NO ACCESSORIES
            } else {
              $success = 'Power tool added';
              mysqli_commit($db);
            }
          }
        }
      }
    }
  }

}

?>


<?php include("lib/error.php"); ?>

<?php include("partials/head.php"); ?>

<!-- Alerts -->
<?php if (count($errors) > 0) { ?>
  <div class="alert alert-danger my-4" role="alert">
    Some fields are missing or invalid. Please correct them.
  </div>
<?php } elseif ($alert !== '') { ?>
  <div class="alert alert-danger my-4" role="alert">
    <?= $alert ?>
  </div>
<?php } elseif ($success !== '') { ?>
  <div class="alert alert-success my-4" role="alert">
    <?= $success ?>
  </div>
<?php } ?>


<h1 class="h2 my-4">Add Tool</h1>
<form id="t4r-addtool"
  action="/add_tool.php"
  method="post"
  enctype="multipart/form-data"
  autocomplete="off">


  <!-- Main attributes -->
  <div class="form-group border border-secondary rounded p-3">
    <div class="form-row">
      <!-- Tool type -->
      <fieldset class="col-lg-6 form-group" id="type">
        <legend class="h6">Type</legend>
        <div class="form-check form-check-inline">
          <label class="form-check-label">
            <input class="form-check-input"
              id="type-hand"
              type="radio"
              name="type"
              value="hand"
              <?php echo ($inputs['type']['value']=='hand')?'checked':''?>
              data-subtypes="screwdriver,socket,ratchet,wrench,plier,gun,hammer"
              data-powersources="manual"
              data-attributes="">
              Hand tool
          </label>
        </div>
        <div class="form-check form-check-inline">
          <label class="form-check-label">
            <input class="form-check-input"
              id="type-garden"
              type="radio"
              name="type"
              value="garden"
              <?php echo ($inputs['type']['value']=='garden')?'checked':''?>
              data-subtypes="digging,pruning,rake,wheelbarrow,striking"
              data-powersources="manual"
              data-attributes="handle-material">
              Garden tool
          </label>
        </div>
        <div class="form-check form-check-inline">
          <label class="form-check-label">
            <input class="form-check-input"
              id="type-ladder"
              type="radio"
              name="type"
              value="ladder"
              <?php echo ($inputs['type']['value']=='ladder')?'checked':''?>
              data-subtypes="straightladder,stepladder"
              data-powersources="manual"
              data-attributes="step-count,weight-capacity">
              Ladder
          </label>
        </div>
        <div class="form-check form-check-inline">
          <label class="form-check-label">
            <input class="form-check-input"
              id="type-power"
              type="radio"
              name="type"
              value="power"
              <?php echo ($inputs['type']['value']=='power')?'checked':''?>
              data-subtypes="powerdrill,powersaw,powersander,poweraircompressor,powermixer,powergenerator"
              data-powersources="electric,cordless,gas"
              data-attributes="volt-rating,amp-rating,min-rpm-rating,max-rpm-rating">
              Power tool
          </label>
        </div>
        <?php if (isset($errors['type'])) { ?>
          <div class="invalid-feedback d-block">
            <?= $errors['type']; ?>
          </div>
        <?php } ?>
      </fieldset>

      <!-- Powersource -->
      <div class="col-lg-6 form-group">
        <label for="powersource">Power source</label>
        <select class="form-control t4r-addtool-dependent
          <?=isset($errors['powersource'])?'is-invalid':'';?>"
          id="powersource"
          name="powersource"
          <?php if ($inputs['type']['value'] === '') { ?>
            <?= 'disabled' ?>
          <?php } elseif ($inputs['type']['value'] !== 'power') { ?>
            <?= 'readonly' ?>
          <?php } ?>>

          <option value="" disabled hidden
            <?=($inputs['powersource']['value']==='')?'selected':''?>>
            Select a power source
          </option>

          <option value="manual"
            <?php if ($inputs['type']['value'] !== '' &&
                      $inputs['type']['value'] !== 'power') { ?>
              <?= 'selected' ?>
            <?php } else { ?>
              <?= 'disabled hidden' ?>
            <?php } ?>
            data-subtypes="screwdriver,socket,ratchet,wrench,plier,gun,hammer,digging,pruning,rake,wheelbarrow,striking,straightladder,stepladder">
            Manual
          </option>
          <option value="electric"
            <?=($inputs['type']['value']!=='power')?'disabled hidden':''?>
            <?=($inputs['powersource']['value']==='electric')?'selected':''?>
            data-subtypes="powerdrill,powersaw,powersander,poweraircompressor,powermixer"
            data-types="powerdrill,powersaw,powersander,poweraircompressor,powermixer">
            Electric (A/C)
          </option>
          <option value="cordless"
            <?=($inputs['type']['value']!=='power')?'disabled hidden':''?>
            <?=($inputs['powersource']['value']==='cordless')?'selected':''?>
            data-subtypes="powerdrill,powersaw,powersander"
            data-types="powerdrill,powersaw,powersander">
            Cordless (D/C)
          </option>
          <option value="gas"
            <?=($inputs['type']['value']!=='power')?'disabled hidden':''?>
            <?=($inputs['powersource']['value']==='gas')?'selected':''?>
            data-subtypes="poweraircompressor,powermixer,powergenerator"
            data-types="poweraircompressor,powermixer,powergenerator">
            Gas
          </option>
        </select>
        <?php if (isset($errors['powersource'])) { ?>
          <div class="invalid-feedback d-block">
            <?= $errors['powersource']; ?>
          </div>
        <?php } ?>
      </div>
    </div>

    <div class="form-row">
      <!-- Subtype -->
      <div class="col-md form-group">
        <label for="subtype">Sub-type</label>
        <select class="form-control t4r-addtool-dependent
          <?=isset($errors['subtype'])?'is-invalid':'';?>"
          id="subtype"
          name="subtype"
          <?php if ($inputs['powersource']['value'] === '') { ?>
            <?= 'disabled' ?>
          <?php } ?>>

          <option value="" disabled hidden
            <?=($inputs['subtype']['value']==='')?'selected':''?>>
            Select a sub-type
          </option>

          <option value="screwdriver"
            <?=($inputs['subtype']['value']==='screwdriver')?'selected':''?>
            data-options="phillips (cross),hex,torx,slotted"
            data-attributes="screw-size">
            Screwdriver
          </option>
          <option value="socket"
            <?=($inputs['subtype']['value']==='socket')?'selected':''?>
            data-options="deep,standard"
            data-attributes="drive-size,sae-size">
            Socket
          </option>
          <option value="ratchet"
            <?=($inputs['subtype']['value']==='ratchet')?'selected':''?>
            data-options="adjustable,fixed"
            data-attributes="drive-size">
            Ratchet
          </option>
          <option value="wrench"
            <?=($inputs['subtype']['value']==='wrench')?'selected':''?>
            data-options="crescent,torque,pipe"
            data-attributes="drive-size">
            Wrench
          </option>
          <option value="plier"
            <?=($inputs['subtype']['value']==='plier')?'selected':''?>
            data-options="needle nose,cutting,crimper"
            data-attributes="adjustable">
            Pliers
          </option>
          <option value="gun"
            <?=($inputs['subtype']['value']==='gun')?'selected':''?>
            data-options="nail,staple"
            data-attributes="gauge-rating,capacity">
            Gun
          </option>
          <option value="hammer"
            <?=($inputs['subtype']['value']==='hammer')?'selected':''?>
            data-options="claw,sledge,framing"
            data-attributes="anti-vibration">
            Hammer
          </option>

          <option value="digging"
            <?=($inputs['subtype']['value']==='digging')?'selected':''?>
            data-options="pointed shovel,flat shovel,scoop shovel,edger"
            data-attributes="blade-width,blade-length">
            Digger
          </option>
          <option value="pruning"
            <?=($inputs['subtype']['value']==='pruning')?'selected':''?>
            data-options="sheer,loppers,hedge"
            data-attributes="blade-material,blade-length">
            Pruner
          </option>
          <option value="rake"
            <?=($inputs['subtype']['value']==='rake')?'selected':''?>
            data-options="leaf,landscaping,rock"
            data-attributes="tine-count">
            Rakes
          </option>
          <option value="wheelbarrow"
            <?=($inputs['subtype']['value']==='wheelbarrow')?'selected':''?>
            data-options="1-wheel,2-wheel"
            data-attributes="bin-material,bin-volume,wheel-count">
            Wheelbarrows
          </option>
          <option value="striking"
            <?=($inputs['subtype']['value']==='striking')?'selected':''?>
            data-options="bar pry,rubber mallet,tamper,pick axe,single bit axe"
            data-attributes="head-weight">
            Striking
          </option>

          <option value="straightladder"
            <?=($inputs['subtype']['value']==='straightladder')?'selected':''?>
            data-options="rigid,telescoping"
            data-attributes="rubber-feet">
            Straight
          </option>
          <option value="stepladder"
            <?=($inputs['subtype']['value']==='stepladder')?'selected':''?>
            data-options="folding,multi-position"
            data-attributes="pail-shelf">
            Step
          </option>

          <option value="powerdrill"
            <?=($inputs['subtype']['value']==='powerdrill')?'selected':''?>
            data-powersource="electric,cordless"
            data-options="driver,hammer"
            data-attributes="adjustable-clutch,min-torque-rating,max-torque-rating">
            Drill
          </option>
          <option value="powersaw"
            <?=($inputs['subtype']['value']==='powersaw')?'selected':''?>
            data-powersource="electric,cordless"
            data-options="circular,reciprocating,jig"
            data-attributes="blade-size">
            Saw
          </option>
          <option value="powersander"
            <?=($inputs['subtype']['value']==='powersander')?'selected':''?>
            data-powersource="electric,cordless"
            data-options="finish,sheet,belt,random orbital"
            data-attributes="dust-bag">
            Sander
          </option>
          <option value="poweraircompressor"
            <?=($inputs['subtype']['value']==='poweraircompressor')?'selected':''?>
            data-powersource="electric,gas"
            data-options="reciprocating"
            data-attributes="tank-size,pressure-rating">
            Air-compressor
          </option>
          <option value="powermixer"
            <?=($inputs['subtype']['value']==='powermixer')?'selected':''?>
            data-powersource="electric,gas"
            data-options="concrete"
            data-attributes="motor-rating,drum-size">
            Mixer
          </option>
          <option value="powergenerator"
            <?=($inputs['subtype']['value']==='powergenerator')?'selected':''?>
            data-options="electric"
            data-attributes="power-rating">
            Generator
          </option>
        </select>
        <?php if (isset($errors['subtype'])) { ?>
          <div class="invalid-feedback d-block">
            <?= $errors['subtype']; ?>
          </div>
        <?php } ?>
      </div>

      <!-- suboption -->
      <div class="col-md form-group">
        <label for="suboption">Sub-option</label>
        <select class="form-control t4r-addtool-dependent
          <?=isset($errors['suboption'])?'is-invalid':'';?>"
          id="suboption"
          name="suboption"
          <?php if ($inputs['subtype']['value'] === '') { ?>
            <?= 'disabled' ?>
          <?php } ?>>>

          <option value="" disabled hidden
            <?=($inputs['suboption']['value']==='')?'selected':''?>>
            Select a sub-option
          </option>

          <?php if ($inputs['subtype']['value'] !== '') { ?>
            <option value="<?= $inputs['suboption']['value'] ?>"
              selected>
              <?= ucfirst($inputs['suboption']['value']); ?>
            </option>
          <?php } ?>
        </select>
        <?php if (isset($errors['suboption'])) { ?>
          <div class="invalid-feedback d-block">
            <?= $errors['suboption']; ?>
          </div>
        <?php } ?>
      </div>
    </div>
  </div>


  <!-- Common -->
  <div class="form-group border border-secondary rounded p-3">
    <div class="form-row">
      <!-- Price -->
      <div class="col-md">
        <div class="form-group">
          <label for="price">Purchase Price</label>
          <input class="form-control
            <?=isset($errors['price'])?'is-invalid':'';?>"
            id="price"
            name="price"
            placeholder="10.00"
            value="<?= $inputs['price']['value']; ?>">
            <?php if (isset($errors['price'])) { ?>
              <div class="invalid-feedback d-block">
                <?= $errors['price']; ?>
              </div>
            <?php } ?>
        </div>
      </div>

      <!-- Manufacturer -->
      <div class="col-md">
        <div class="form-group">
          <label for="manufacturer">Manufacturer</label>
          <input class="form-control
            <?=isset($errors['manufacturer'])?'is-invalid':'';?>"
            id="manufacturer"
            name="manufacturer"
            placeholder="Manufacturer"
            value="<?= $inputs['manufacturer']['value']; ?>">
            <?php if (isset($errors['manufacturer'])) { ?>
              <div class="invalid-feedback d-block">
                <?= $errors['manufacturer']; ?>
              </div>
            <?php } ?>
        </div>
      </div>

      <!-- Material -->
      <div class="col-md">
        <div class="form-group">
          <label for="material">Material</label>
          <input class="form-control
            <?=isset($errors['material'])?'is-invalid':'';?>"
            id="material"
            name="material"
            placeholder="Material"
            value="<?= $inputs['material']['value']; ?>">
            <?php if (isset($errors['material'])) { ?>
              <div class="invalid-feedback d-block">
                <?= $errors['material']; ?>
              </div>
            <?php } ?>
        </div>
      </div>
    </div>

    <!-- Width -->
    <div class="form-row">
      <div class="col-md">
        <div class="form-group">
          <label for="width">Width/Diameter</label>
          <input class="form-control
            <?=isset($errors['width'])?'is-invalid':'';?>"
            id="width"
            name="width"
            placeholder="Width"
            value="<?= $inputs['width']['value']; ?>">
            <?php if (isset($errors['width'])) { ?>
              <div class="invalid-feedback d-block">
                <?= $errors['width']; ?>
              </div>
            <?php } ?>
        </div>
      </div>
      <div class="col-md">
        <div class="form-group">
          <label for="width-fraction">Width/Diameter fraction</label>
          <select class="form-control
            <?=isset($errors['width-fraction'])?'is-invalid':'';?>"
            id="width-fraction"
            name="width-fraction">

            <option value="" disabled hidden
              <?=($inputs['width-fraction']['value']==='')?'selected':''?>>
              Select a width fraction
            </option>

            <?php foreach ($fraction_list as $key => $value) { ?>
              <option value="<?= $value ?>"
                <?php if ($inputs['width-fraction']['value'] === $value) { ?>
                  selected
                <?php } ?>
                > <?= $key ?>
              </option>
            <?php } ?>

          </select>
          <?php if (isset($errors['width-fraction'])) { ?>
            <div class="invalid-feedback d-block">
              <?= $errors['width-fraction']; ?>
            </div>
          <?php } ?>
        </div>
      </div>
      <div class="col-md">
        <div class="form-group">
          <label for="width-unit">Width/Diameter unit</label>
          <select class="form-control
            <?=isset($errors['width-unit'])?'is-invalid':'';?>"
            id="width-unit"
            name="width-unit">

            <option value="" disabled hidden
              <?=($inputs['width-unit']['value']==='')?'selected':''?>>
              Select a width unit
            </option>

            <option value="1">Inches</option>
            <option value="12"
              <?=($inputs['width-unit']['value']==='12')?'selected':'';?>>
              Feet
            </option>
          </select>
          <?php if (isset($errors['width-unit'])) { ?>
            <div class="invalid-feedback d-block">
              <?= $errors['width-unit']; ?>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>

    <!-- Length -->
    <div class="form-row">
      <div class="col-md">
        <div class="form-group">
          <label for="length">Length</label>
          <input class="form-control
            <?=isset($errors['length'])?'is-invalid':'';?>"
            id="length"
            name="length"
            placeholder="Length"
            value="<?= $inputs['length']['value']; ?>">
            <?php if (isset($errors['length'])) { ?>
              <div class="invalid-feedback d-block">
                <?= $errors['length']; ?>
              </div>
            <?php } ?>
        </div>
      </div>
      <div class="col-md">
        <div class="form-group">
          <label for="length-fraction">Length fraction</label>
          <select class="form-control
            <?=isset($errors['length-fraction'])?'is-invalid':'';?>"
            id="length-fraction"
            name="length-fraction">

            <option value="" disabled hidden
              <?=($inputs['length-fraction']['value']==='')?'selected':''?>>
              Select a length fraction
            </option>

            <?php foreach ($fraction_list as $key => $value) { ?>
              <option value="<?= $value ?>"
                <?php if ($inputs['length-fraction']['value'] === $value) { ?>
                  selected
                <?php } ?>
                > <?= $key ?>
              </option>
            <?php } ?>

          </select>
          <?php if (isset($errors['length-fraction'])) { ?>
            <div class="invalid-feedback d-block">
              <?= $errors['length-fraction']; ?>
            </div>
          <?php } ?>
        </div>
      </div>
      <div class="col-md">
        <div class="form-group">
          <label for="length-unit">Length unit</label>
          <select class="form-control
            <?=isset($errors['length-unit'])?'is-invalid':'';?>"
            id="length-unit"
            name="length-unit">

            <option value="" disabled hidden
              <?=($inputs['length-unit']['value']==='')?'selected':''?>>
              Select a length unit
            </option>

            <option value="1">Inches</option>
            <option value="12"
              <?=($inputs['length-unit']['value']==='12')?'selected':'';?>>
              Feet
            </option>
          </select>
          <?php if (isset($errors['length-unit'])) { ?>
            <div class="invalid-feedback d-block">
              <?= $errors['length-unit']; ?>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>

    <!-- Weight -->
    <div class="form-row">
      <div class="col-md-4">
        <div class="form-group">
          <label for="weight">Weight (lbs)</label>
          <input class="form-control
            <?=isset($errors['weight'])?'is-invalid':'';?>"
            id="weight"
            name="weight"
            placeholder="Weight in pounds"
            value="<?= $inputs['weight']['value']; ?>">
            <?php if (isset($errors['weight'])) { ?>
              <div class="invalid-feedback d-block">
                <?= $errors['weight']; ?>
              </div>
            <?php } ?>
        </div>
      </div>
    </div>
  </div>


  <!-- Category and subtype specific -->
  <div class="form-group border border-secondary rounded p-3 d-none
    t4r-addtool-selective-box">
    <div class="form-row">
      <div class="col-md-4 d-none t4r-addtool-selective">
        <div class="form-group">
          <label for="screw-size">Screw size (#)</label>
          <input class="form-control
            <?=isset($errors['screw-size'])?'is-invalid':'';?>"
            id="screw-size"
            name="screw-size"
            placeholder="2"
            value="<?= $inputs['screw-size']['value']; ?>">
            <?php if (isset($errors['screw-size'])) { ?>
              <div class="invalid-feedback d-block">
                <?= $errors['screw-size']; ?>
              </div>
            <?php } ?>
        </div>
      </div>
      <div class="col-md-4 d-none t4r-addtool-selective">
        <div class="form-group">
          <label for="drive-size">Drive size (in.)</label>
          <select class="form-control
            <?=isset($errors['drive-size'])?'is-invalid':'';?>"
            id="drive-size"
            name="drive-size">

            <option value="" disabled hidden
              <?=($inputs['drive-size']['value']==='')?'selected':''?>>
              Select a drive size
            </option>

            <?php foreach ($drive_size_list as $key => $value) { ?>
              <option value="<?= $value ?>"
                <?php if ($inputs['drive-size']['value'] === $value) { ?>
                  selected
                <?php } ?>
                > <?= $key ?>
              </option>
            <?php } ?>

          </select>
          <?php if (isset($errors['drive-size'])) { ?>
            <div class="invalid-feedback d-block">
              <?= $errors['drive-size']; ?>
            </div>
          <?php } ?>
        </div>
      </div>
      <div class="col-md-4 d-none t4r-addtool-selective">
        <div class="form-group">
          <label for="sae-size">SAE size (in.)</label>
          <input class="form-control
            <?=isset($errors['sae-size'])?'is-invalid':'';?>"
            id="sae-size"
            name="sae-size"
            placeholder="SAE size"
            value="<?= $inputs['sae-size']['value']; ?>">
            <?php if (isset($errors['sae-size'])) { ?>
              <div class="invalid-feedback d-block">
                <?= $errors['sae-size']; ?>
              </div>
            <?php } ?>
        </div>
      </div>
    <!--
    <div class="col-md-4 d-none t4r-addtool-selective">
        <div class="form-group">
          <label for="deep-socket">Deep Socket</label>
          <select class="form-control
            <?php //echo isset($errors['deep-socket'])?'is-invalid':'';?>"
            id="deep-socket"
            name="deep-socket">

            <option value="" disabled hidden
              <?php//echo($inputs['deep-socket']['value']==='')?'selected':''?>>
              Deep socket?
            </option>

            <option value="1"
              <?php//echo($inputs['deep-socket']['value']==='1')?'selected':''?>>
              Yes
            </option>
            <option value="0"
              <?php//echo($inputs['deep-socket']['value']==='0')?'selected':''?>>
              No
            </option>
          </select>
          <?php// if (isset($errors['deep-socket'])) { ?>
            <div class="invalid-feedback d-block">
              <?php// $errors['deep-socket']; ?>
            </div>
          <?php// } ?>
        </div>
      </div> -->
      <div class="col-md-4 d-none t4r-addtool-selective">
        <div class="form-group">
          <label for="adjustable">Adjustable</label>
          <select class="form-control
            <?=isset($errors['adjustable'])?'is-invalid':'';?>"
            id="adjustable"
            name="adjustable">

            <option value="" disabled hidden
              <?=($inputs['adjustable']['value']==='')?'selected':''?>>
              Adjustable?
            </option>

            <option value="1"
              <?=($inputs['adjustable']['value']==='1')?'selected':''?>>
              Yes
            </option>
            <option value="0"
              <?=($inputs['adjustable']['value']==='0')?'selected':''?>>
              No
            </option>
          </select>
          <?php if (isset($errors['adjustable'])) { ?>
            <div class="invalid-feedback d-block">
              <?= $errors['adjustable']; ?>
            </div>
          <?php } ?>
        </div>
      </div>
      <div class="col-md-4 d-none t4r-addtool-selective">
        <div class="form-group">
          <label for="gauge-rating">Gauge rating (G)</label>
          <select class="form-control
            <?=isset($errors['gauge-rating'])?'is-invalid':'';?>"
            id="gauge-rating"
            name="gauge-rating">

            <option value="" disabled hidden
              <?=($inputs['gauge-rating']['value']==='')?'selected':''?>>
              Select a gauge rating
            </option>

            <?php foreach ($gauge_rating_list as $value) { ?>
              <option value="<?= $value ?>"
                <?php if ($inputs['gauge-rating']['value'] === $value) { ?>
                  selected
                <?php } ?>
                > <?= $value ?>
              </option>
            <?php } ?>

          </select>
          <?php if (isset($errors['gauge-rating'])) { ?>
            <div class="invalid-feedback d-block">
              <?= $errors['gauge-rating']; ?>
            </div>
          <?php } ?>
        </div>
      </div>
      <div class="col-md-4 d-none t4r-addtool-selective">
        <div class="form-group">
          <label for="capacity">Capacity</label>
          <input class="form-control
            <?=isset($errors['capacity'])?'is-invalid':'';?>"
            id="capacity"
            name="capacity"
            placeholder="20"
            value="<?= $inputs['capacity']['value']; ?>">
            <?php if (isset($errors['capacity'])) { ?>
              <div class="invalid-feedback d-block">
                <?= $errors['capacity']; ?>
              </div>
            <?php } ?>
        </div>
      </div>
      <div class="col-md-4 d-none t4r-addtool-selective">
        <div class="form-group">
          <label for="anti-vibration">Anti-vibration</label>
          <select class="form-control
            <?=isset($errors['anti-vibration'])?'is-invalid':'';?>"
            id="anti-vibration"
            name="anti-vibration">

            <option value="" disabled hidden
              <?=($inputs['anti-vibration']['value']==='')?'selected':''?>>
              Anti-vibration?
            </option>

            <option value="1"
              <?=($inputs['anti-vibration']['value']==='1')?'selected':''?>>
              Yes
            </option>
            <option value="0"
              <?=($inputs['anti-vibration']['value']==='0')?'selected':''?>>
              No
            </option>
          </select>
          <?php if (isset($errors['anti-vibration'])) { ?>
            <div class="invalid-feedback d-block">
              <?= $errors['anti-vibration']; ?>
            </div>
          <?php } ?>
        </div>
      </div>

      <div class="col-md-4 d-none t4r-addtool-selective">
        <div class="form-group">
          <label for="handle-material">Handle material</label>
          <input class="form-control
            <?=isset($errors['handle-material'])?'is-invalid':'';?>"
            id="handle-material"
            name="handle-material"
            placeholder="wooden"
            value="<?= $inputs['handle-material']['value']; ?>">
            <?php if (isset($errors['handle-material'])) { ?>
              <div class="invalid-feedback d-block">
                <?= $errors['handle-material']; ?>
              </div>
            <?php } ?>
        </div>
      </div>
      <div class="col-md-4 d-none t4r-addtool-selective">
        <div class="form-group">
          <label for="blade-material">Blade material</label>
          <input class="form-control
            <?=isset($errors['blade-material'])?'is-invalid':'';?>"
            id="blade-material"
            name="blade-material"
            placeholder="steel"
            value="<?= $inputs['blade-material']['value']; ?>">
            <?php if (isset($errors['blade-material'])) { ?>
              <div class="invalid-feedback d-block">
                <?= $errors['blade-material']; ?>
              </div>
            <?php } ?>
        </div>
      </div>
      <div class="col-md-4 d-none t4r-addtool-selective">
        <div class="form-group">
          <label for="blade-width">Blade width (in.)</label>
          <input class="form-control
            <?=isset($errors['blade-width'])?'is-invalid':'';?>"
            id="blade-width"
            name="blade-width"
            placeholder="9.75"
            value="<?= $inputs['blade-width']['value']; ?>">
            <?php if (isset($errors['blade-width'])) { ?>
              <div class="invalid-feedback d-block">
                <?= $errors['blade-width']; ?>
              </div>
            <?php } ?>
        </div>
      </div>
      <div class="col-md-4 d-none t4r-addtool-selective">
        <div class="form-group">
          <label for="blade-length">Blade length (in.)</label>
          <input class="form-control
            <?=isset($errors['blade-length'])?'is-invalid':'';?>"
            id="blade-length"
            name="blade-length"
            placeholder="24"
            value="<?= $inputs['blade-length']['value']; ?>">
            <?php if (isset($errors['blade-length'])) { ?>
              <div class="invalid-feedback d-block">
                <?= $errors['blade-length']; ?>
              </div>
            <?php } ?>
        </div>
      </div>
      <div class="col-md-4 d-none t4r-addtool-selective">
        <div class="form-group">
          <label for="head-weight">Head weight (lb.)</label>
          <input class="form-control
            <?=isset($errors['head-weight'])?'is-invalid':'';?>"
            id="head-weight"
            name="head-weight"
            placeholder="3.5"
            value="<?= $inputs['head-weight']['value']; ?>">
            <?php if (isset($errors['head-weight'])) { ?>
              <div class="invalid-feedback d-block">
                <?= $errors['head-weight']; ?>
              </div>
            <?php } ?>
        </div>
      </div>
      <div class="col-md-4 d-none t4r-addtool-selective">
        <div class="form-group">
          <label for="tine-count">Tine count</label>
          <input class="form-control
            <?=isset($errors['tine-count'])?'is-invalid':'';?>"
            id="tine-count"
            name="tine-count"
            placeholder="14"
            value="<?= $inputs['tine-count']['value']; ?>">
            <?php if (isset($errors['tine-count'])) { ?>
              <div class="invalid-feedback d-block">
                <?= $errors['tine-count']; ?>
              </div>
            <?php } ?>
        </div>
      </div>
      <div class="col-md-4 d-none t4r-addtool-selective">
        <div class="form-group">
          <label for="bin-material">Bin material</label>
          <input class="form-control
            <?=isset($errors['bin-material'])?'is-invalid':'';?>"
            id="bin-material"
            name="bin-material"
            placeholder="steel"
            value="<?= $inputs['bin-material']['value']; ?>">
            <?php if (isset($errors['bin-material'])) { ?>
              <div class="invalid-feedback d-block">
                <?= $errors['bin-material']; ?>
              </div>
            <?php } ?>
        </div>
      </div>
      <div class="col-md-4 d-none t4r-addtool-selective">
        <div class="form-group">
          <label for="bin-volume">Bin volume (cu ft.)</label>
          <input class="form-control
            <?=isset($errors['bin-volume'])?'is-invalid':'';?>"
            id="bin-volume"
            name="bin-volume"
            placeholder="5.9"
            value="<?= $inputs['bin-volume']['value']; ?>">
            <?php if (isset($errors['bin-volume'])) { ?>
              <div class="invalid-feedback d-block">
                <?= $errors['bin-volume']; ?>
              </div>
            <?php } ?>
        </div>
      </div>
      <div class="col-md-4 d-none t4r-addtool-selective">
        <div class="form-group">
          <label for="wheel-count">Wheel count</label>
          <input class="form-control
            <?=isset($errors['wheel-count'])?'is-invalid':'';?>"
            id="wheel-count"
            name="wheel-count"
            placeholder="1"
            value="<?= $inputs['wheel-count']['value']; ?>">
            <?php if (isset($errors['wheel-count'])) { ?>
              <div class="invalid-feedback d-block">
                <?= $errors['wheel-count']; ?>
              </div>
            <?php } ?>
        </div>
      </div>

      <div class="col-md-4 d-none t4r-addtool-selective">
        <div class="form-group">
          <label for="step-count">Step count (-step)</label>
          <input class="form-control
            <?=isset($errors['step-count'])?'is-invalid':'';?>"
            id="step-count"
            name="step-count"
            placeholder="1"
            value="<?= $inputs['step-count']['value']; ?>">
            <?php if (isset($errors['step-count'])) { ?>
              <div class="invalid-feedback d-block">
                <?= $errors['step-count']; ?>
              </div>
            <?php } ?>
        </div>
      </div>
      <div class="col-md-4 d-none t4r-addtool-selective">
        <div class="form-group">
          <label for="weight-capacity">
            Weight capacity (lb. capacity)
          </label>
          <input class="form-control
            <?=isset($errors['weight-capacity'])?'is-invalid':'';?>"
            id="weight-capacity"
            name="weight-capacity"
            placeholder="250"
            value="<?= $inputs['weight-capacity']['value']; ?>">
            <?php if (isset($errors['weight-capacity'])) { ?>
              <div class="invalid-feedback d-block">
                <?= $errors['weight-capacity']; ?>
              </div>
            <?php } ?>
        </div>
      </div>
      <div class="col-md-4 d-none t4r-addtool-selective">
        <div class="form-group">
          <label for="rubber-feet">Ruber feet</label>
          <select class="form-control
            <?=isset($errors['rubber-feet'])?'is-invalid':'';?>"
            id="rubber-feet"
            name="rubber-feet">

            <option value="" disabled hidden
              <?=($inputs['rubber-feet']['value']==='')?'selected':''?>>
              Rubber feet?
            </option>

            <option value="1"
              <?=($inputs['rubber-feet']['value']==='1')?'selected':''?>>
              Yes
            </option>
            <option value="0"
              <?=($inputs['rubber-feet']['value']==='0')?'selected':''?>>
              No
            </option>
          </select>
          <?php if (isset($errors['rubber-feet'])) { ?>
            <div class="invalid-feedback d-block">
              <?= $errors['rubber-feet']; ?>
            </div>
          <?php } ?>
        </div>
      </div>
      <div class="col-md-4 d-none t4r-addtool-selective">
        <div class="form-group">
          <label for="pail-shelf">Pail shelf</label>
          <select class="form-control
            <?=isset($errors['pail-shelf'])?'is-invalid':'';?>"
            id="pail-shelf"
            name="pail-shelf">

            <option value="" disabled hidden
              <?=($inputs['pail-shelf']['value']==='')?'selected':''?>>
              Pail shelf?
            </option>

            <option value="1"
              <?=($inputs['pail-shelf']['value']==='1')?'selected':''?>>
              Yes
            </option>
            <option value="0"
              <?=($inputs['pail-shelf']['value']==='0')?'selected':''?>>
              No
            </option>
          </select>
          <?php if (isset($errors['pail-shelf'])) { ?>
            <div class="invalid-feedback d-block">
              <?= $errors['pail-shelf']; ?>
            </div>
          <?php } ?>
        </div>
      </div>

      <div class="col-md-4 d-none t4r-addtool-selective">
        <div class="form-group">
          <label for="volt-rating">Volt rating</label>
          <select class="form-control
            <?=isset($errors['volt-rating'])?'is-invalid':'';?>"
            id="volt-rating"
            name="volt-rating">

            <option value="" disabled hidden
              <?=($inputs['volt-rating']['value']==='')?'selected':''?>>
              Select a volt rating
            </option>

            <?php foreach ($volt_rating_list as $key => $value) { ?>
              <option value="<?= $value ?>"
                <?php if ($inputs['volt-rating']['value'] === $value) { ?>
                  selected
                <?php } ?>
                > <?= $key ?>
              </option>
            <?php } ?>

          </select>
          <?php if (isset($errors['volt-rating'])) { ?>
            <div class="invalid-feedback d-block">
              <?= $errors['volt-rating']; ?>
            </div>
          <?php } ?>
        </div>
      </div>
      <div class="col-md-8 d-none t4r-addtool-selective">
        <div class="form-row">
          <div class="col-md">
            <div class="form-group">
              <label for="amp-rating">Amp rating (A)</label>
              <input class="form-control
                <?=isset($errors['amp-rating'])?'is-invalid':'';?>"
                id="amp-rating"
                name="amp-rating"
                placeholder="1.0"
                value="<?= $inputs['amp-rating']['value']; ?>">
                <?php if (isset($errors['amp-rating'])) { ?>
                  <div class="invalid-feedback d-block">
                    <?= $errors['amp-rating']; ?>
                  </div>
                <?php } ?>
            </div>
          </div>
          <div class="col-md">
            <div class="form-group">
              <label for="amp-unit">Amp unit</label>
              <select class="form-control
                <?=isset($errors['amp-unit'])?'is-invalid':'';?>"
                id="amp-unit"
                name="amp-unit">

                <option value="" disabled hidden
                  <?=($inputs['amp-unit']['value']==='')?'selected':''?>>
                  Select an amp unit
                </option>

                <option value="1"
                  <?=($inputs['amp-unit']['value']==='1')?'selected':'';?>>
                  Amps
                </option>
                <option value="0.001"
                  <?=($inputs['amp-unit']['value']==='0.001')?'selected':'';?>>
                  Milliamps
                </option>
                <option value="1000"
                  <?=($inputs['amp-unit']['value']==='1000')?'selected':'';?>>
                  Kiloamps
                </option>
              </select>
              <?php if (isset($errors['amp-unit'])) { ?>
                <div class="invalid-feedback d-block">
                  <?= $errors['amp-unit']; ?>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4 d-none t4r-addtool-selective">
        <div class="form-group">
          <label for="min-rpm-rating">Min RPM rating</label>
          <input class="form-control
            <?=isset($errors['min-rpm-rating'])?'is-invalid':'';?>"
            id="min-rpm-rating"
            name="min-rpm-rating"
            placeholder="2000"
            value="<?= $inputs['min-rpm-rating']['value']; ?>">
            <?php if (isset($errors['min-rpm-rating'])) { ?>
              <div class="invalid-feedback d-block">
                <?= $errors['min-rpm-rating']; ?>
              </div>
            <?php } ?>
        </div>
      </div>
      <div class="col-md-4 d-none t4r-addtool-selective">
        <div class="form-group">
          <label for="max-rpm-rating">Max RPM rating</label>
          <input class="form-control
            <?=isset($errors['max-rpm-rating'])?'is-invalid':'';?>"
            id="max-rpm-rating"
            name="max-rpm-rating"
            placeholder="3000"
            value="<?= $inputs['max-rpm-rating']['value']; ?>">
            <?php if (isset($errors['max-rpm-rating'])) { ?>
              <div class="invalid-feedback d-block">
                <?= $errors['max-rpm-rating']; ?>
              </div>
            <?php } ?>
        </div>
      </div>
      <div class="col-md-4 d-none t4r-addtool-selective">
        <div class="form-group">
          <label for="adjustable-clutch">Adjustable clutch</label>
          <select class="form-control
            <?=isset($errors['adjustable-clutch'])?'is-invalid':'';?>"
            id="adjustable-clutch"
            name="adjustable-clutch">

            <option value="" disabled hidden
              <?=($inputs['adjustable-clutch']['value']==='')?'selected':''?>>
              Adjustable clutch?
            </option>

            <option value="1"
              <?=($inputs['adjustable-clutch']['value']==='1')?'selected':''?>>
              Yes
            </option>
            <option value="0"
              <?=($inputs['adjustable-clutch']['value']==='0')?'selected':''?>>
              No
            </option>
          </select>
          <?php if (isset($errors['adjustable-clutch'])) { ?>
            <div class="invalid-feedback d-block">
              <?= $errors['adjustable-clutch']; ?>
            </div>
          <?php } ?>
        </div>
      </div>
      <div class="col-md-4 d-none t4r-addtool-selective">
        <div class="form-group">
          <label for="min-torque-rating">
            Min torque rating (ft-lb.)
          </label>
          <input class="form-control
            <?=isset($errors['min-torque-rating'])?'is-invalid':'';?>"
            id="min-torque-rating"
            name="min-torque-rating"
            placeholder="80.0"
            value="<?= $inputs['min-torque-rating']['value']; ?>">
            <?php if (isset($errors['min-torque-rating'])) { ?>
              <div class="invalid-feedback d-block">
                <?= $errors['min-torque-rating']; ?>
              </div>
            <?php } ?>
        </div>
      </div>
      <div class="col-md-4 d-none t4r-addtool-selective">
        <div class="form-group">
          <label for="max-torque-rating">
            Max torque rating (ft-lb.)
          </label>
          <input class="form-control
            <?=isset($errors['max-torque-rating'])?'is-invalid':'';?>"
            id="max-torque-rating"
            name="max-torque-rating"
            placeholder="120.2"
            value="<?= $inputs['max-torque-rating']['value']; ?>">
            <?php if (isset($errors['max-torque-rating'])) { ?>
              <div class="invalid-feedback d-block">
                <?= $errors['max-torque-rating']; ?>
              </div>
            <?php } ?>
        </div>
      </div>
      <div class="col-md-4 d-none t4r-addtool-selective">
        <div class="form-group">
          <label for="blade-size">Blade size (in.)</label>
          <input class="form-control
            <?=isset($errors['blade-size'])?'is-invalid':'';?>"
            id="blade-size"
            name="blade-size"
            placeholder="7.75"
            value="<?= $inputs['blade-size']['value']; ?>">
            <?php if (isset($errors['blade-size'])) { ?>
              <div class="invalid-feedback d-block">
                <?= $errors['blade-size']; ?>
              </div>
            <?php } ?>
        </div>
      </div>
      <div class="col-md-4 d-none t4r-addtool-selective">
        <div class="form-group">
          <label for="dust-bag">Dust bag</label>
          <select class="form-control
            <?=isset($errors['dust-bag'])?'is-invalid':'';?>"
            id="dust-bag"
            name="dust-bag">

            <option value="" disabled hidden
              <?=($inputs['dust-bag']['value']==='')?'selected':''?>>
              Dust bag?
            </option>

            <option value="1"
              <?=($inputs['dust-bag']['value']==='1')?'selected':''?>>
              Yes
            </option>
            <option value="0"
              <?=($inputs['dust-bag']['value']==='0')?'selected':''?>>
              No
            </option>
          </select>
          <?php if (isset($errors['dust-bag'])) { ?>
            <div class="invalid-feedback d-block">
              <?= $errors['dust-bag']; ?>
            </div>
          <?php } ?>
        </div>
      </div>
      <div class="col-md-4 d-none t4r-addtool-selective">
        <div class="form-group">
          <label for="tank-size">Tank size (gal)</label>
          <input class="form-control
            <?=isset($errors['tank-size'])?'is-invalid':'';?>"
            id="tank-size"
            name="tank-size"
            placeholder="7"
            value="<?= $inputs['tank-size']['value']; ?>">
            <?php if (isset($errors['tank-size'])) { ?>
              <div class="invalid-feedback d-block">
                <?= $errors['tank-size']; ?>
              </div>
            <?php } ?>
        </div>
      </div>
      <div class="col-md-4 d-none t4r-addtool-selective">
        <div class="form-group">
          <label for="pressure-rating">Pressure rating</label>
          <input class="form-control
            <?=isset($errors['pressure-rating'])?'is-invalid':'';?>"
            id="pressure-rating"
            name="pressure-rating"
            placeholder="300.0"
            value="<?= $inputs['pressure-rating']['value']; ?>">
            <?php if (isset($errors['pressure-rating'])) { ?>
              <div class="invalid-feedback d-block">
                <?= $errors['pressure-rating']; ?>
              </div>
            <?php } ?>
        </div>
      </div>
      <div class="col-md-4 d-none t4r-addtool-selective">
        <div class="form-group">
          <label for="motor-rating">Motor rating (HP)</label>
          <input class="form-control
            <?=isset($errors['motor-rating'])?'is-invalid':'';?>"
            id="motor-rating"
            name="motor-rating"
            placeholder="0.5"
            value="<?= $inputs['motor-rating']['value']; ?>">
            <?php if (isset($errors['motor-rating'])) { ?>
              <div class="invalid-feedback d-block">
                <?= $errors['motor-rating']; ?>
              </div>
            <?php } ?>
        </div>
      </div>
      <div class="col-md-4 d-none t4r-addtool-selective">
        <div class="form-group">
          <label for="drum-size">Drum size (cu-ft.)</label>
          <input class="form-control
            <?=isset($errors['drum-size'])?'is-invalid':'';?>"
            id="drum-size"
            name="drum-size"
            placeholder="3.5"
            value="<?= $inputs['drum-size']['value']; ?>">
            <?php if (isset($errors['drum-size'])) { ?>
              <div class="invalid-feedback d-block">
                <?= $errors['drum-size']; ?>
              </div>
            <?php } ?>
        </div>
      </div>
      <div class="col-12 d-none t4r-addtool-selective">
        <div class="form-row">
          <div class="col-md-4">
            <div class="form-group">
              <label for="power-rating">Power rating (W)</label>
              <input class="form-control
                <?=isset($errors['power-rating'])?'is-invalid':'';?>"
                id="power-rating"
                name="power-rating"
                placeholder="18.0"
                value="<?= $inputs['power-rating']['value']; ?>">
                <?php if (isset($errors['power-rating'])) { ?>
                  <div class="invalid-feedback d-block">
                    <?= $errors['power-rating']; ?>
                  </div>
                <?php } ?>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="power-unit">Power unit</label>
              <select class="form-control
                <?=isset($errors['power-unit'])?'is-invalid':'';?>"
                id="power-unit"
                name="power-unit">

                <option value="" disabled hidden
                  <?=($inputs['power-unit']['value']==='')?'selected':''?>>
                  Select a power unit
                </option>

                <option value="1"
                  <?=($inputs['power-unit']['value']==='1')?'selected':'';?>>
                  Watts
                </option>
                <option value="0.001"
                  <?=($inputs['power-unit']['value']==='0.001')?'selected':'';?>>
                  Milliwatts
                </option>
                <option value="1000"
                  <?=($inputs['power-unit']['value']==='1000')?'selected':'';?>>
                  Kilowatts
                </option>
              </select>
              <?php if (isset($errors['power-unit'])) { ?>
                <div class="invalid-feedback d-block">
                  <?= $errors['power-unit']; ?>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


  <!-- Batteries -->
  <fieldset class="form-group border border-secondary rounded p-3 d-none"
    id="batteries-box">
    <h2 class="h4">Batteries</h2>
    <div class="form-row">
      <div class="col-md-4">
        <div class="form-group">
          <label for="battery-type">Type</label>
          <select class="form-control
            <?=isset($errors['battery-type'])?'is-invalid':'';?>"
            id="battery-type"
            name="battery-type">

            <option value="" disabled hidden
              <?=($inputs['battery-type']['value']==='')?'selected':''?>>
              Select a battery type
            </option>

            <?php foreach (['Li-Ion', 'NiCd', 'NiMH'] as $value) { ?>
              <option value="<?= $value ?>"
                <?php if ($inputs['battery-type']['value'] === $value) { ?>
                  selected
                <?php } ?>
                > <?= $value ?>
              </option>
            <?php } ?>

          </select>
          <?php if (isset($errors['battery-type'])) { ?>
            <div class="invalid-feedback d-block">
              <?= $errors['battery-type']; ?>
            </div>
          <?php } ?>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label for="battery-volt-rating">D/C volt rating</label>
          <input class="form-control
            <?=isset($errors['battery-volt-rating'])?'is-invalid':'';?>"
            id="battery-volt-rating"
            name="battery-volt-rating"
            placeholder="7.2-80"
            value="<?= $inputs['battery-volt-rating']['value']; ?>">
            <?php if (isset($errors['battery-volt-rating'])) { ?>
              <div class="invalid-feedback d-block">
                <?= $errors['battery-volt-rating']; ?>
              </div>
            <?php } ?>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label for="battery-quantity">Quantity</label>
          <input class="form-control
            <?=isset($errors['battery-quantity'])?'is-invalid':'';?>"
            id="battery-quantity"
            name="battery-quantity"
            placeholder="1"
            type='number'
            value="<?= $inputs['battery-quantity']['value']; ?>">
            <?php if (isset($errors['battery-quantity'])) { ?>
              <div class="invalid-feedback d-block">
                <?= $errors['battery-quantity']; ?>
              </div>
            <?php } ?>
        </div>
      </div>
    </div>
  </fieldset>


  <!-- Accessories -->
  <fieldset class="form-group border border-secondary rounded p-3 d-none"
    id="accessories-box">
    <h2 class="h4">Accessories</h2>
    <p>
      Accessories are optional for power tools.
      Use the below button to add any number of accessories.
    </p>

    <div id="accessories"
      data-counter="<?= count($inputs['accessories']['value']); ?>">
      <!-- Accessory fields inserted with Javascript here -->
      <?php foreach ($inputs['accessories']['value'] as $i => $accessory) { ?>
        <div class="form-row accessory"
          data-id="<?= $i ?>"
          data-index="<?= $i ?>">
          <div class="col-sm-8 col-md-6 col-lg-8">
            <div class="form-group">
              <label class="d-md-none" for="accessory-description">
                Description
              </label>
              <input class="form-control
                <?=isset($errors['accessories'][$i]['accessory-description'])?'is-invalid':'';?>"
                name="accessories[<?= $i ?>][accessory-description]"
                placeholder="Accessory description"
                data-name="accessory-description"
                value="<?= $accessory['accessory-description'] ?>">
              <?php if (isset($errors['battery-quantity'])) { ?>
                <div class="invalid-feedback d-block">
                  <?= $errors['accessories'][$i]['accessory-description']; ?>
                </div>
              <?php } ?>
            </div>
          </div>
          <div class="col-sm-4 col-md-3 col-lg-2">
            <div class="form-group">
              <label class="d-md-none" for="accessory-quantity">
                Quantity
              </label>
              <input class="form-control
                <?=isset($errors['accessories'][$i]['accessory-quantity'])?'is-invalid':'';?>"
                name="accessories[<?= $i ?>][accessory-quantity]"
                placeholder="1"
                type='number'
                data-name="accessory-quantity"
                value="<?= $accessory['accessory-quantity'] ?>">
              <?php if (isset($errors['battery-quantity'])) { ?>
                <div class="invalid-feedback d-block">
                  <?= $errors['accessories'][$i]['accessory-quantity']; ?>
                </div>
              <?php } ?>
            </div>
          </div>
          <div class="col-md-3 col-lg-2">
            <div class="form-group">
              <label class="d-md-none invisible">-</label>
              <button type="button"
                class="btn btn-block btn-outline-danger accessory-btn-remove"
                onclick="removeAccessory(<?= $i ?>);">
                Remove
              </button>
            </div>
          </div>
        </div>
      <?php } ?>
    </div>

    <div class="form-row">
      <div class="col-md-auto">
        <div class="form-group">
          <button type="button" class="btn btn-block btn-secondary"
            id="accessory-btn-add">
            Add accessory
          </button>
        </div>
      </div>
    </div>

    <template id="accessory-template">
      <div class="form-row accessory"
        data-id="0"
        data-index="0">
        <div class="col-sm-8 col-md-6 col-lg-8">
          <div class="form-group">
            <label class="d-md-none" for="accessory-description">
              Description
            </label>
            <input class="form-control"
              name="accessories[0][accessory-description]"
              placeholder="Accessory description"
              data-name="accessory-description">
          </div>
        </div>
        <div class="col-sm-4 col-md-3 col-lg-2">
          <div class="form-group">
            <label class="d-md-none" for="accessory-quantity">
              Quantity
            </label>
            <input class="form-control"
              name="accessories[0][accessory-quantity]"
              placeholder="1"
              type='number'
              data-name="accessory-quantity">
          </div>
        </div>
        <div class="col-md-3 col-lg-2 align-self-end">
          <div class="form-group">
            <button type="button"
              class="btn btn-block btn-outline-danger accessory-btn-remove">
              Remove
            </button>
          </div>
        </div>
      </div>
    </template>
  </fieldset>


  <!-- Submit -->
  <div class="form-row">
    <div class="col-md-auto">
      <div class="form-group">
        <button type="submit" class="btn btn-block btn-primary">
          Add tool
        </button>
      </div>
    </div>
  </div>

</form>

<?php include("partials/tail.php"); ?>
