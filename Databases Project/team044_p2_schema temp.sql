-- CREATE USER 'newuser'@'localhost' IDENTIFIED BY 'password';
CREATE USER IF NOT EXISTS gatechUser@localhost IDENTIFIED BY 'gatech123';

DROP DATABASE IF EXISTS `cs6400_fa17_team044`; 
SET default_storage_engine=InnoDB;
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE DATABASE IF NOT EXISTS cs6400_fa17_team044 
    DEFAULT CHARACTER SET utf8mb4 
    DEFAULT COLLATE utf8mb4_unicode_ci;
USE cs6400_fa17_team044;

GRANT SELECT, INSERT, UPDATE, DELETE, FILE ON *.* TO 'gatechUser'@'localhost';
GRANT ALL PRIVILEGES ON `gatechuser`.* TO 'gatechUser'@'localhost';
GRANT ALL PRIVILEGES ON `cs6400_fa17_team044`.* TO 'gatechUser'@'localhost';
FLUSH PRIVILEGES;

-- Tables 
CREATE TABLE Clerk (
  username varchar(50) NOT NULL,
  employee_number int(16) unsigned NOT NULL AUTO_INCREMENT,
  email varchar(150) NOT NULL,
  first_name varchar(50) NOT NULL,
  middle_name varchar(50) NOT NULL, 
  last_name varchar(50) NOT NULL,
  date_of_hire date NOT NULL,
  password varchar(50) DEFAULT NULL,
  first_login BIT(1) NOT NULL,
   
  PRIMARY KEY (username), 
  UNIQUE KEY email (email),
  KEY employee_number (employee_number)
);

CREATE TABLE Customer (
  username varchar(50) NOT NULL,
  password varchar(50) NOT NULL,
  email varchar(150) NOT NULL,
  first_name varchar(50) NOT NULL,
  middle_name varchar(50) NOT NULL, 
  last_name varchar(50) NOT NULL,
  card_name varchar(100) NOT NULL,
  card_number BIGINT(16) unsigned NOT NULL,
  expiration_month int(16) unsigned NOT NULL,
  expiration_year int(16) unsigned NOT NULL,
  ccv int(16) unsigned NOT NULL,
  street int(16) unsigned NOT NULL,
  state varchar(50) NOT NULL,
  city varchar(50) NOT NULL,
  9_digit_zip int(16) unsigned NOT NULL,
   
  PRIMARY KEY (username), 
  UNIQUE KEY email (email)
);

  
CREATE TABLE Tool (
  tool_number int(16) unsigned NOT NULL AUTO_INCREMENT,
  sub_type varchar(16) NOT NULL,
  sub_option varchar(16) NOT NULL,
  width_diameter DECIMAL NOT NULL,
  length DECIMAL NOT NULL,
  price DECIMAL NOT NULL,
  weight DECIMAL NOT NULL,
  material varchar(50) DEFAULT NULL,
  power_source ENUM('electric','cordless','gas','manual') NOT NULL,  
  manufacturer varchar(100) NOT NULL,  
  PRIMARY KEY (tool_number)
);

CREATE TABLE HandTool (
  tool_number int(16) unsigned NOT NULL,
  PRIMARY KEY (tool_number) 
);

ALTER TABLE HandTool
  ADD CONSTRAINT fk_HandTool_toolNumber_Tool_toolNumber FOREIGN KEY (tool_number) REFERENCES Tool (tool_number);
  
CREATE TABLE Plier (
  tool_number int(16) unsigned NOT NULL,
  adjustable BIT(1) DEFAULT NULL,
  PRIMARY KEY (tool_number) 
);

ALTER TABLE Plier
  ADD CONSTRAINT fk_Plier_toolNumber_HandTool_toolNumber FOREIGN KEY (tool_number) REFERENCES HandTool (tool_number);

CREATE TABLE Wrench (
  tool_number int(16) unsigned NOT NULL,
  drive_size DECIMAL NOT NULL,
  PRIMARY KEY (tool_number) 
);

ALTER TABLE Wrench
  ADD CONSTRAINT fk_Wrench_toolNumber_HandTool_toolNumber FOREIGN KEY (tool_number) REFERENCES HandTool (tool_number);

CREATE TABLE Ratchet (
  tool_number int(16) unsigned NOT NULL,
  drive_size DECIMAL NOT NULL,
  PRIMARY KEY (tool_number) 
);

ALTER TABLE Ratchet
  ADD CONSTRAINT fk_Ratchet_toolNumber_HandTool_toolNumber FOREIGN KEY (tool_number) REFERENCES HandTool (tool_number);

CREATE TABLE Socket (
  tool_number int(16) unsigned NOT NULL,
  drive_size DECIMAL NOT NULL,
  sae_size DECIMAL NOT NULL,
  deep_socket BIT(1) DEFAULT NULL,
  PRIMARY KEY (tool_number) 
);

ALTER TABLE Socket
  ADD CONSTRAINT fk_Socket_toolNumber_HandTool_toolNumber FOREIGN KEY (tool_number) REFERENCES HandTool (tool_number);

CREATE TABLE ScrewDriver (
  tool_number int(16) unsigned NOT NULL,
  screw_size int(16) unsigned NOT NULL,
  PRIMARY KEY (tool_number) 
);

ALTER TABLE ScrewDriver
  ADD CONSTRAINT fk_ScrewDriver_toolNumber_HandTool_toolNumber FOREIGN KEY (tool_number) REFERENCES HandTool (tool_number);
  
CREATE TABLE Hammer (
  tool_number int(16) unsigned NOT NULL,
  anti_vibration BIT(1) DEFAULT NULL,
  PRIMARY KEY (tool_number) 
);

ALTER TABLE Hammer
  ADD CONSTRAINT fk_Hammer_toolNumber_HandTool_toolNumber FOREIGN KEY (tool_number) REFERENCES HandTool (tool_number);
  
CREATE TABLE Gun (
  tool_number int(16) unsigned NOT NULL,
  gauge_rating int(16) unsigned NOT NULL,
  capacity int(16) unsigned NOT NULL,
  PRIMARY KEY (tool_number) 
);

ALTER TABLE Gun
  ADD CONSTRAINT fk_Gun_toolNumber_HandTool_toolNumber FOREIGN KEY (tool_number) REFERENCES HandTool (tool_number);
  
CREATE TABLE PowerTool (
  tool_number int(16) unsigned NOT NULL,
  volt_rating DECIMAL NOT NULL,
  amp_rating DECIMAL NOT NULL,
  min_rpm DECIMAL NOT NULL,
  max_rpm DECIMAL DEFAULT NULL,
  PRIMARY KEY (tool_number) 
);

ALTER TABLE PowerTool
  ADD CONSTRAINT fk_PowerTool_toolNumber_Tool_toolNumber FOREIGN KEY (tool_number) REFERENCES Tool (tool_number);

CREATE TABLE PowerAirCompressor (
  tool_number int(16) unsigned NOT NULL,
  tank_size DECIMAL NOT NULL,
  pressure_rating DECIMAL DEFAULT NULL,
  PRIMARY KEY (tool_number)
);

ALTER TABLE PowerAirCompressor
  ADD CONSTRAINT fk_PowerAirCompressor_toolNumber_PowerTool_toolNumber FOREIGN KEY (tool_number) REFERENCES PowerTool (tool_number);

CREATE TABLE PowerMixer (
  tool_number int(16) unsigned NOT NULL,
  motor_rating DECIMAL NOT NULL,
  drum_size DECIMAL DEFAULT NULL,
  PRIMARY KEY (tool_number)
);

ALTER TABLE PowerMixer
  ADD CONSTRAINT fk_PowerMixer_toolNumber_PowerTool_toolNumber FOREIGN KEY (tool_number) REFERENCES PowerTool (tool_number);

CREATE TABLE PowerGenerator (
  tool_number int(16) unsigned NOT NULL,
  power_rating DECIMAL NOT NULL,
  PRIMARY KEY (tool_number)
);

ALTER TABLE PowerGenerator
  ADD CONSTRAINT fk_PowerGenerator_toolNumber_PowerTool_toolNumber FOREIGN KEY (tool_number) REFERENCES PowerTool (tool_number);

CREATE TABLE CordlessPowerTool (
  tool_number int(16) unsigned NOT NULL,
  battery_type varchar(50) NOT NULL,
  PRIMARY KEY (tool_number)
);

ALTER TABLE CordlessPowerTool
  ADD CONSTRAINT fk_CordlessPowerTool_toolNumber_PowerTool_toolNumber FOREIGN KEY (tool_number) REFERENCES PowerTool (tool_number);
  
CREATE TABLE PowerDrill (
  tool_number int(16) unsigned NOT NULL,
  min_torque DECIMAL NOT NULL,
  max_torque DECIMAL DEFAULT NULL,
  adjustable_clutch BIT(1) DEFAULT NULL,
  PRIMARY KEY (tool_number) 
);

ALTER TABLE PowerDrill
  ADD CONSTRAINT fk_PowerDrill_toolNumber_PowerTool_toolNumber FOREIGN KEY (tool_number) REFERENCES PowerTool (tool_number);

CREATE TABLE Accessory (
  tool_number int(16) unsigned NOT NULL,
  accessory_name varchar(50) NOT NULL,
  accessory_description varchar(120) NOT NULL,
  accessory_quantity int(16) unsigned NOT NULL,
  PRIMARY KEY (tool_number,accessory_name) 
);

ALTER TABLE Accessory
  ADD CONSTRAINT fk_Accessory_toolNumber_PowerTool_toolNumber FOREIGN KEY (tool_number) REFERENCES PowerTool (tool_number);

CREATE TABLE PowerSaw (
  tool_number int(16) unsigned NOT NULL,
  blade_size DECIMAL NOT NULL,
  PRIMARY KEY (tool_number) 
);

ALTER TABLE PowerSaw
  ADD CONSTRAINT fk_PowerSaw_toolNumber_PowerTool_toolNumber FOREIGN KEY (tool_number) REFERENCES PowerTool (tool_number);

CREATE TABLE PowerSander (
  tool_number int(16) unsigned NOT NULL,
  dust_bag DECIMAL DEFAULT NULL,
  PRIMARY KEY (tool_number) 
);

ALTER TABLE PowerSander
  ADD CONSTRAINT fk_PowerSander_toolNumber_PowerTool_toolNumber FOREIGN KEY (tool_number) REFERENCES PowerTool (tool_number);

CREATE TABLE Ladder (
  tool_number int(16) unsigned NOT NULL,
  step_count int(16) unsigned NOT NULL,
  weight_capacity DECIMAL DEFAULT NULL,
  PRIMARY KEY (tool_number) 
);

ALTER TABLE Ladder
  ADD CONSTRAINT fk_Ladder_toolNumber_Tool_toolNumber FOREIGN KEY (tool_number) REFERENCES Tool (tool_number);

CREATE TABLE StraightLadder (
  tool_number int(16) unsigned NOT NULL,
  rubber_feet BIT(1) DEFAULT NULL,
  PRIMARY KEY (tool_number) 
);

ALTER TABLE StraightLadder
  ADD CONSTRAINT fk_StraightLadder_toolNumber_Ladder_toolNumber FOREIGN KEY (tool_number) REFERENCES Ladder (tool_number);

CREATE TABLE StepLadder (
  tool_number int(16) unsigned NOT NULL,
  pail_shelf BIT(1) DEFAULT NULL,
  PRIMARY KEY (tool_number)
);

ALTER TABLE StepLadder
  ADD CONSTRAINT fk_StepLadder_toolNumber_Ladder_toolNumber FOREIGN KEY (tool_number) REFERENCES Ladder (tool_number);

CREATE TABLE GardenTool (
  tool_number int(16) unsigned NOT NULL,
  handle_material varchar(50) NOT NULL,
  PRIMARY KEY (tool_number) 
);

ALTER TABLE GardenTool
  ADD CONSTRAINT fk_GardenTool_toolNumber_Tool_toolNumber FOREIGN KEY (tool_number) REFERENCES Tool (tool_number);

CREATE TABLE Pruning (
  tool_number int(16) unsigned NOT NULL,
  blade_material varchar(50) DEFAULT NULL,
  blade_length DECIMAL NOT NULL,
  PRIMARY KEY (tool_number) 
);

ALTER TABLE Pruning
  ADD CONSTRAINT fk_Pruning_toolNumber_GardenTool_toolNumber FOREIGN KEY (tool_number) REFERENCES GardenTool (tool_number);

CREATE TABLE Striking (
  tool_number int(16) unsigned NOT NULL,
  head_weight DECIMAL NOT NULL,
  PRIMARY KEY (tool_number)
);

ALTER TABLE Striking
  ADD CONSTRAINT fk_Striking_toolNumber_GardenTool_toolNumber FOREIGN KEY (tool_number) REFERENCES GardenTool (tool_number);

CREATE TABLE DIGGING (
  tool_number int(16) unsigned NOT NULL,
  blade_length DECIMAL NOT NULL,
  blade_width DECIMAL DEFAULT NULL,
  PRIMARY KEY (tool_number)
);

ALTER TABLE DIGGING
  ADD CONSTRAINT fk_DIGGING_toolNumber_GardenTool_toolNumber FOREIGN KEY (tool_number) REFERENCES GardenTool (tool_number);

CREATE TABLE Rake (
  tool_number int(16) unsigned NOT NULL,
  tine_count int(16) unsigned NOT NULL,
  PRIMARY KEY (tool_number) 
);

ALTER TABLE Rake
  ADD CONSTRAINT fk_Rake_toolNumber_GardenTool_toolNumber FOREIGN KEY (tool_number) REFERENCES GardenTool (tool_number);

CREATE TABLE WheelBarrow (
  tool_number int(16) unsigned NOT NULL,
  wheel_count int(16) unsigned NOT NULL,
  bin_material varchar(50) NOT NULL,
  bin_volume DECIMAL DEFAULT NULL,
  PRIMARY KEY (tool_number) 
);

ALTER TABLE WheelBarrow
  ADD CONSTRAINT fk_WheelBarrow_toolNumber_GardenTool_toolNumber FOREIGN KEY (tool_number) REFERENCES GardenTool (tool_number);



CREATE TABLE PhoneNumber (
  area_code int(16) unsigned NOT NULL,
  is_primary BIT(1) NOT NULL,
  phone_number int(16) unsigned NOT NULL,
  phone_type varchar (50) DEFAULT NULL,
  extension int(16) unsigned NOT NULL,
  username varchar(50) NOT NULL,
  PRIMARY KEY (username,area_code,phone_number,extension)
);

ALTER TABLE PhoneNumber
  ADD CONSTRAINT fk_PhoneNumber_username_Customer_username FOREIGN KEY (username) REFERENCES Customer (username);

CREATE TABLE Rental (
  confirmation_number int(16) unsigned NOT NULL AUTO_INCREMENT,
  customer_username varchar(50) NOT NULL,
  pickup_clerk_username varchar(50) DEFAULT NULL,
  dropoff_clerk_username varchar(50) DEFAULT NULL,
  start_date datetime NOT NULL,
  end_date datetime NOT NULL,
  PRIMARY KEY (customer_username,confirmation_number),
  KEY confirmation_number (confirmation_number)
);

ALTER TABLE Rental
  ADD CONSTRAINT fk_Rental_customerUsername_Customer_username FOREIGN KEY (customer_username) REFERENCES Customer (username),
  ADD CONSTRAINT fk_Rental_pickupClerkUsername_Clerk_username FOREIGN KEY (pickup_clerk_username) REFERENCES Clerk (username),
  ADD CONSTRAINT fk_Rental_dropoffClerkUsername_Clerk_username FOREIGN KEY (dropoff_clerk_username) REFERENCES Clerk (username);
  
CREATE TABLE RentalRentsTool (
  confirmation_number int(16) unsigned NOT NULL,
  customer_username varchar(50) NOT NULL,
  tool_number int(16) unsigned NOT NULL,
  PRIMARY KEY (customer_username,confirmation_number,tool_number)
);

ALTER TABLE RentalRentsTool
  ADD CONSTRAINT fk_RentalRentsTool_cUsername_Rental_cUsername FOREIGN KEY (customer_username,confirmation_number) REFERENCES Rental (customer_username,confirmation_number),
  ADD CONSTRAINT fk_RentalRentsTool_toolNumber_Tool_toolNumber FOREIGN KEY (tool_number) REFERENCES Tool (tool_number);
  

 /*
 Adding hand tools to database
 */
 
INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (1, 'manual', 'screwdriver', 'phillips', 10, 3, 6, 0.5, 'steel', 'DEWALT');

INSERT INTO `HandTool`
VALUES (1);

INSERT INTO `ScrewDriver` (tool_number, screw_size)
VALUES (1, 2);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (2, 'manual', 'screwdriver', 'hex', 25, 3.2, 8, 0.4, 'iron', 'Ryobi');

INSERT INTO `HandTool`
VALUES (2);

INSERT INTO `ScrewDriver` (tool_number, screw_size)
VALUES (2, 8);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (3, 'manual', 'screwdriver', 'torx', 15, 3.7, 6.7, 0.6, 'Aluminum', 'ABC');

INSERT INTO `HandTool`
VALUES (3);

INSERT INTO `ScrewDriver` (tool_number, screw_size)
VALUES (3, 3);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (4, 'manual', 'socket', 'deep', 7, 4.7, 5.7, 0.55, 'Aluminum', 'Phillips');

INSERT INTO `HandTool`
VALUES (4);

INSERT INTO `Socket` (tool_number, drive_size, sae_size, deep_socket)
VALUES (4, 0.5, 0.25, 1);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (5, 'manual', 'socket', 'standard', 12, 8.7, 5.9, 0.65, 'steel', 'Phillips');

INSERT INTO `HandTool`
VALUES (5);

INSERT INTO `Socket` (tool_number, drive_size, sae_size, deep_socket)
VALUES (5, 0.375, 0.3125, 0);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (6, 'manual', 'ratchet', 'fixed', 17, 3.7, 6.9, 2, 'steel', 'ABC');

INSERT INTO `HandTool`
VALUES (6);

INSERT INTO `Ratchet` (tool_number, drive_size)
VALUES (6, 0.375);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (7, 'manual', 'wrench', 'pipe', 15, 1.7, 8.9, 2.1, 'steel', 'ABC');

INSERT INTO `HandTool`
VALUES (7);

INSERT INTO `Wrench` (tool_number, drive_size)
VALUES (7, 0.375);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (8, 'manual', 'wrench', 'torque', 15, 1.7, 8.9, 2.1, 'steel', 'DEWALT');

INSERT INTO `HandTool`
VALUES (8);

INSERT INTO `Wrench` (tool_number)
VALUES (8);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (9, 'manual', 'plier', 'cutting', 20, 1.1, 9.9, 3.1, 'iron', 'DEWALT');

INSERT INTO `HandTool`
VALUES (9);

INSERT INTO `Plier` (tool_number, adjustable)
VALUES (9, 1);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (10, 'manual', 'plier', 'crimper', 25, 1.1, 9.9, 3.1, 'steel', 'Phillips');

INSERT INTO `HandTool`
VALUES (10);

INSERT INTO `Plier` (tool_number, adjustable)
VALUES (10, 0);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (11, 'manual', 'gun', 'nail', 100, 12.1, 11, 6, 'steel', 'Phillips');

INSERT INTO `HandTool`
VALUES (11);

INSERT INTO `Gun` (tool_number, gauge_rating, capacity)
VALUES (11, 18, 100);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (12, 'manual', 'hammer', 'sledge', 30, 15, 15, 25, 'rubber', 'ABC');

INSERT INTO `HandTool`
VALUES (12);

INSERT INTO `Hammer` (tool_number, anti_vibration)
VALUES (12, 0);

 /*
 Adding garden tools to database
 */
 
INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (13, 'manual', 'digging', 'flat_shovel', 60, 12, 11.1, 10, 'steel', 'Milwaukee');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (13, 'wooden');

INSERT INTO `DIGGING` (tool_number, blade_width, blade_length)
VALUES (13, 6.875, 8.125);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (14, 'manual', 'digging', 'flat_shovel', 60, 12, 11.1, 10, 'steel', 'Milwaukee');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (14, 'wooden');

INSERT INTO `DIGGING` (tool_number, blade_width, blade_length)
VALUES (14, 6.875, 8.125);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (15, 'gas', 'digging', 'gas-auger', 65, 12, 13.1, 50, 'iron', 'ABC');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (15, 'poly');

INSERT INTO `DIGGING` (tool_number, blade_width, blade_length)
VALUES (15, 6.875, 8.125);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (16, 'cordless', 'digging', 'edger', 38, 11, 12.1, 40, 'iron', 'Milwaukee');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (16, 'fiberglass');

INSERT INTO `DIGGING` (tool_number, blade_width, blade_length)
VALUES (16, 9.75, 10.125);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (17, 'electric', 'digging', 'edger', 38, 11, 12.1, 40, 'iron', 'Milwaukee');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (17, 'fiberglass');

INSERT INTO `DIGGING` (tool_number, blade_width, blade_length)
VALUES (17, 9.75, 10.125);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (18, 'manual', 'prunning', 'sheer', 8, 5, 6.1, 3, 'steel', 'ABC');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (18, 'wooden');

INSERT INTO `Pruning` (tool_number, blade_material, blade_length)
VALUES (18, 'steel', 10.125);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (19, 'manual', 'prunning', 'loppers', 80, 6, 6.3, 0.75, 'titanium', 'Phillips');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (19, 'fiberglass');

INSERT INTO `Pruning` (tool_number, blade_material, blade_length)
VALUES (19, 'titanium', 10.125);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (20, 'manual', 'rake', 'leaf', 5, 2, 3.3, 0.45, 'steel', 'Phillips');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (20, 'wooden');

INSERT INTO `Rake` (tool_number, tine_count)
VALUES (20, 14);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (21, 'manual', 'rake', 'rock', 7.5, 2, 3.3, 0.45, 'steel', 'Phillips');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (21, 'wooden');

INSERT INTO `Rake` (tool_number, tine_count)
VALUES (21, 16);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (22, 'manual', 'wheelbarrow', '1_wheel', 50, 14.22, 4.3, 2.45, 'metal', 'ABC');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (22, 'metal');

INSERT INTO `WheelBarrow` (tool_number, bin_material, bin_volume, wheel_count)
VALUES (22, 'fiberglass', 10.2, 1);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (23, 'manual', 'wheelbarrow', '2_wheel', 54, 14.22, 4.3, 2.45, 'metal', 'ABC');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (23, 'metal');

INSERT INTO `WheelBarrow` (tool_number, bin_material, bin_volume, wheel_count)
VALUES (23, 'poly', 10.2, 2);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (24, 'manual', 'striking', 'bar_pry', 12, 7.23, 2.3, 11.9, 'iron', 'Milwaukee');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (24, 'wooden');

INSERT INTO `Striking` (tool_number, head_weight)
VALUES (24, 8.9);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (25, 'manual', 'striking', 'rubber_mallet', 6.75, 7.23, 2.3, 4.9, 'steel', 'ABC');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (25, 'wooden');

INSERT INTO `Striking` (tool_number, head_weight)
VALUES (25, 3.5);

/*
 Adding power tools to database
 */
 
INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (26, 'electric', 'powerdrill', 'driver', 39.99, 12, 13.1, 14, 'steel', 'DEWALT');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (26, 220, 5, 2000, 4500);

INSERT INTO `PowerDrill` (tool_number, min_torque, max_torque, adjustable_clutch)
VALUES (26, 80, 1200.1, 0);

INSERT INTO `Accessory`
VALUES (26, 'drill_bits', 'drill_bits', 12);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (27, 'cordless', 'powerdrill', 'driver', 39.99, 12, 13.1, 14, 'steel', 'DEWALT');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (27, 70, 5, 2000, 4500);

INSERT INTO `PowerDrill` (tool_number, min_torque, max_torque, adjustable_clutch)
VALUES (27, 80, 1200.1, 0);

INSERT INTO `Accessory`
VALUES (27, 'drill_bits', 'drill_bits', 12);

INSERT INTO `Accessory`
VALUES (27, 'battery', 'NiCd', 12);

INSERT INTO `CordlessPowerTool`
VALUES (27, 'NiCd');

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (28, 'cordless', 'powerdrill', 'driver', 40.99, 12, 13.1, 14, 'steel', 'DEWALT');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (28, 60, 5, 2000, 4500);

INSERT INTO `PowerDrill` (tool_number, min_torque, max_torque, adjustable_clutch)
VALUES (28, 80, 1200.1, 0);

INSERT INTO `Accessory`
VALUES (28, 'drill_bits', 'drill_bits', 12);

INSERT INTO `Accessory`
VALUES (28, 'battery', 'li_ion', 12);

INSERT INTO `CordlessPowerTool`
VALUES (28, 'li_ion');

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (29, 'cordless', 'powerdrill', 'hammer', 45.99, 12, 13.1, 14, 'steel', 'DEWALT');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (29, 55, 5, 2000, 4500);

INSERT INTO `PowerDrill` (tool_number, min_torque, max_torque, adjustable_clutch)
VALUES (29, 80, 1200.1, 0);

INSERT INTO `Accessory`
VALUES (29, 'drill_bits', 'drill_bits', 12);

INSERT INTO `Accessory`
VALUES (29, 'battery', 'NiMH', 12);

INSERT INTO `CordlessPowerTool`
VALUES (29, 'NiMH');

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (30, 'electric', 'powersaw', 'circular', 50.11, 14, 15.1, 17, 'steel', 'DEWALT');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (30, 220, 1, 1500, 3500);

INSERT INTO `PowerSaw` (tool_number, blade_size)
VALUES (30, 7.75);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (31, 'electric', 'powersaw', 'reciprocating', 59.99, 14, 15.1, 17, 'steel', 'Ryobi');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (31, 120, 1, 2500, 4500);

INSERT INTO `PowerSaw` (tool_number, blade_size)
VALUES (31, 6.5);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (32, 'electric', 'powersaw', 'jig', 59.99, 14, 15.1, 17, 'steel', 'Ryobi');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (32, 120, 1, 2500, 4500);

INSERT INTO `PowerSaw` (tool_number, blade_size)
VALUES (32, 6.5);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (33, 'cordless', 'powersaw', 'jig', 59.99, 14, 15.1, 17, 'steel', 'Ryobi');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (33, 80, 1, 1500, 3500);

INSERT INTO `PowerSaw` (tool_number, blade_size)
VALUES (33, 6.5);

INSERT INTO `Accessory`
VALUES (33, 'battery', 'NiMH', 1);

INSERT INTO `CordlessPowerTool`
VALUES (33, 'NiMH');

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (34, 'cordless', 'powersaw', 'circular', 55.99, 14, 15.1, 17, 'steel', 'Ryobi');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (34, 80, 1, 1500, 3500);

INSERT INTO `PowerSaw` (tool_number, blade_size)
VALUES (34, 7.75);

INSERT INTO `Accessory`
VALUES (34, 'battery', 'li_ion', 2);

INSERT INTO `CordlessPowerTool`
VALUES (34, 'li_ion');

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (35, 'electric', 'powersander', 'finish', 51.98, 15, 16.1, 25, 'steel', 'Ryobi');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (35, 120, 1, 2500, 3500);

INSERT INTO `PowerSander` (tool_number, dust_bag)
VALUES (35, 1);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (36, 'electric', 'powersander', 'sheet', 51.98, 15, 16.1, 25, 'steel', 'Ryobi');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (36, 220, 1, 2500, 4500);

INSERT INTO `PowerSander` (tool_number, dust_bag)
VALUES (36, 1);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (37, 'electric', 'powersander', 'belt', 51.98, 15, 16.1, 25, 'steel', 'Phillips');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (37, 220, 1, 2500, 4500);

INSERT INTO `PowerSander` (tool_number, dust_bag)
VALUES (37, 1);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (38, 'electric', 'powersander', 'random_orbital', 70.98, 15, 16.1, 25, 'steel', 'Phillips');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (38, 220, 1, 2500, 4500);

INSERT INTO `PowerSander` (tool_number, dust_bag)
VALUES (38, 0);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (39, 'cordless', 'powersander', 'random_orbital', 100.00, 12, 14.1, 35, 'steel', 'Phillips');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (39, 80, 1, 1500, 3500);

INSERT INTO `PowerSander` (tool_number, dust_bag)
VALUES (39, 1);

INSERT INTO `Accessory`
VALUES (39, 'battery', 'li_ion', 1);

INSERT INTO `CordlessPowerTool`
VALUES (39, 'li_ion');

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (40, 'cordless', 'powersander', 'sheet', 100.00, 12, 14.1, 35, 'steel', 'Phillips');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (40, 74, 1, 1500, 3500);

INSERT INTO `PowerSander` (tool_number, dust_bag)
VALUES (40, 0);

INSERT INTO `Accessory`
VALUES (40, 'battery', 'NiMH', 2);

INSERT INTO `CordlessPowerTool`
VALUES (40, 'NiMH');

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (41, 'electric', 'poweraircompressor', 'reciprocating', 100.00, 12, 14.1, 35, NULL, 'Phillips');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (41, 110, 5, 2500, 4500);

INSERT INTO `PowerAirCompressor` (tool_number, tank_size, pressure_rating)
VALUES (41, 7, 300);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (42, 'gas', 'poweraircompressor', 'reciprocating', 120.00, 5, 9, 45, 'steel', 'ABC');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (42, 120, 5, 2500, 3500);

INSERT INTO `PowerAirCompressor` (tool_number, tank_size, pressure_rating)
VALUES (42, 10, 2500);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (43, 'electric', 'powermixer', 'concrete', 300.00, 12, 14.1, 35, NULL, 'Milwaukee');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (43, 220, 1, 1500, 3000);

INSERT INTO `PowerMixer` (tool_number, motor_rating, drum_size)
VALUES (43, 0.5, 3.5);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (44, 'gas', 'powermixer', 'concrete', 200.00, 14, 15.1, 35, NULL, 'Milwaukee');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (44, 120, 1, 1500, 3000);

INSERT INTO `PowerMixer` (tool_number, motor_rating, drum_size)
VALUES (44, 0.25, 6.0);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (45, 'gas', 'powermixer', 'concrete', 200.00, 14, 15.1, 35, 'steel', 'Milwaukee');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (45, 120, 1, 1500, 3000);

INSERT INTO `PowerMixer` (tool_number, motor_rating, drum_size)
VALUES (45, 0.25, 6.0);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (46, 'gas', 'powergenerator', 'electric', 500.10, 50, 40, 120, 'steel', 'ABB');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (46, 120, 1, 1500, 3000);

INSERT INTO `PowerGenerator` (tool_number, power_rating)
VALUES (46, 88);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (47, 'gas', 'powergenerator', 'electric', 420.99, 40, 35, 120, NULL, 'ABC');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (47, 220, 1, 3500, 4500);

INSERT INTO `PowerGenerator` (tool_number, power_rating)
VALUES (47, 1000);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (48, 'gas', 'powergenerator', 'electric', 420.99, 40, 35, 120, NULL, 'ABB');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (48, 220, 1, 3500, 4500);

INSERT INTO `PowerGenerator` (tool_number, power_rating)
VALUES (48, 1000);

INSERT INTO `Accessory`
VALUES (48, 'gas_tank', 'gas_tank', 2);

/* Adding Ladder Tools */
INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (49, 'manual', 'stepladder', 'rigid', 50.41, 30, 300, 30, 'steel', 'DEWALT');

INSERT INTO `Ladder` (tool_number, step_count, weight_capacity)
VALUES (49, 20, 350);

INSERT INTO `StepLadder` (tool_number, pail_shelf)
VALUES (49, 1);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (50, 'manual', 'stepladder', 'telescoping', 35.54, 30, 75, 30, 'steel', 'DEWALT');

INSERT INTO `Ladder` (tool_number, step_count, weight_capacity)
VALUES (50, 8, 200);

INSERT INTO `StepLadder` (tool_number, pail_shelf)
VALUES (50, 1);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (51, 'manual', 'straightladder', 'folding', 37.54, 30, 75, 30, NULL, 'DEWALT');

INSERT INTO `Ladder` (tool_number, step_count, weight_capacity)
VALUES (51, 8, 200);

INSERT INTO `StraightLadder` (tool_number, rubber_feet)
VALUES (51, 1);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (52, 'manual', 'straightladder', 'multi_position', 36.54, 30, 95, 30, NULL, 'DEWALT');

INSERT INTO `Ladder` (tool_number, step_count, weight_capacity)
VALUES (52, 8, 250);

INSERT INTO `StraightLadder` (tool_number, rubber_feet)
VALUES (52, 1);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (53, 'manual', 'straightladder', 'multi_position', 31.94, 25, 45, 15, NULL, 'DEWALT');

INSERT INTO `Ladder` (tool_number, step_count, weight_capacity)
VALUES (53, 4, 250);

INSERT INTO `StraightLadder` (tool_number, rubber_feet)
VALUES (53, 0);


/* Adding customers into database */
INSERT INTO `Customer`
VALUES ('user0', '123user0', 'user0@gmail.com', 'John', 'M0', 'Smith', 'John Smith', 4916416556885039, 1, 2018, 102, 10, 'AL', 'Montgomery', 100000000);
INSERT INTO `Customer`
VALUES ('user1', '123user1', 'user1@gmail.com', 'Bill', 'M1', 'Doe', 'Bill Doe', 4486891566391121, 5, 2019, 102, 15, 'AK', 'Juneau', 100000001);
INSERT INTO `Customer`
VALUES ('user2', '123user2', 'user2@gmail.com', 'Chelsea', 'M2', 'Zhen', 'Chelsea Zhen', 4532729152864149, 7, 2021, 104, 20, 'AZ', 'Phoenix', 100000002);
INSERT INTO `Customer`
VALUES ('user3', '123user3', 'user3@gmail.com', 'Mike', 'M3', 'Omar', 'Mike Omar', 4556705524517939, 9, 2018, 107, 25, 'AR', 'Little Rock', 100000003);
INSERT INTO `Customer`
VALUES ('user4', '123user4', 'user4@gmail.com', 'Tyler', 'M4', 'Zhang', 'Tyler Zhang', 4916763243473103, 12, 2020, 105, 30, 'CA', 'Sacramento', 100000004);
INSERT INTO `Customer`
VALUES ('user5', '123user5', 'user5@gmail.com', 'Malik', 'M5', 'Lee', 'Malik Lee', 4556891100352691, 1, 2018, 106, 35, 'CO', 'Denver', 100000005);
INSERT INTO `Customer`
VALUES ('user6', '123user6', 'user6@gmail.com', 'Ahmad', 'M6', 'Black', 'Ahmad Black', 4532448274217256, 1, 2022, 112, 40, 'CT', 'Hartford', 100000006);
INSERT INTO `Customer`
VALUES ('user7', '123user7', 'user7@gmail.com', 'Mohammed', 'M7', 'Brown', 'Mohammed Brown', 4716520865690550, 10, 2021, 113, 45, 'DC', 'Washington', 100000007);
INSERT INTO `Customer`
VALUES ('user8', '123user8', 'user8@gmail.com', 'Khalid', 'M8', 'Johnson', 'Khalid Johnson', 4556147408531753, 11, 2018, 100, 50, 'DE', 'Dover', 100000008);
INSERT INTO `Customer`
VALUES ('user9', '123user9', 'user9@gmail.com', 'Daisong', 'M9', 'Mccarter', 'Daisong Mccarter', 4486809844217348, 5, 2019, 113, 55, 'FL', 'Tallahassee', 100000009);
INSERT INTO `Customer`
VALUES ('user10', '123user10', 'user10@gmail.com', 'Robert', 'M10', 'Minsky', 'Robert Minsky', 4539108365034506, 7, 2020, 113, 60, 'GA', 'Atlanta', 100000010);
INSERT INTO `Customer`
VALUES ('user11', '123user11', 'user11@gmail.com', 'Mary', 'M11', 'James', 'Mary James', 4556134216560646, 1, 2021, 107, 65, 'HI', 'Honolulu', 100000011);
INSERT INTO `Customer`
VALUES ('user12', '123user12', 'user12@gmail.com', 'Sarah', 'M12', 'Durant', 'Sarah Durant', 4532957192840544, 6, 2019, 114, 70, 'ID', 'Boise', 100000012);
INSERT INTO `Customer`
VALUES ('user13', '123user13', 'user13@gmail.com', 'Jack', 'M13', 'Ng', 'Jack Ng', 4929448608129871, 5, 2022, 114, 75, 'IL', 'Springfield', 100000013);
INSERT INTO `Customer`
VALUES ('user14', '123user14', 'user14@gmail.com', 'Emilly', 'M14', 'Alan', 'Emilly Alan', 4539831035487255, 3, 2021, 112, 80, 'IN', 'Indianapolis', 100000014);

/*
Add Clerks to database
*/
INSERT INTO `Clerk`
VALUES ('clerk1',1,'clerk1@gmail.com','Jack','A','Doee','2008-01-15','123clerk1',0);

INSERT INTO `Clerk`
VALUES ('clerk2',2,'clerk2@gmail.com','Jacky','B','Mark','2011-02-14','123clerk2',1);

INSERT INTO `Clerk`
VALUES ('clerk3',3,'clerk3@gmail.com','Mark','C','Rosa','2015-11-02','123clerk3',0);

INSERT INTO `Clerk`
VALUES ('clerk4',4,'clerk4@gmail.com','Allan','D','Smith','2017-03-11','123clerk4',0);

INSERT INTO `Clerk`
VALUES ('clerk5',5,'clerk5@gmail.com','Ahmad','E','Dabbagh','2009-07-23','123clerk5',1);

INSERT INTO `Clerk`
VALUES ('clerk6',6,'clerk6@gmail.com','Bart','F','Mckinsey','2012-04-05','123clerk6',0);

/*
Add tool reservations
*/

INSERT INTO `Rental`
VALUES (1,'user1', 'clerk1','clerk2','2017-10-31 00:00:00', '2017-11-01 23:59:59');

INSERT INTO `RentalRentsTool`
VALUES (1, 'user1', 1);

INSERT INTO `RentalRentsTool`
VALUES (1, 'user1', 2);

INSERT INTO `Rental` 
VALUES (2,'user2',  'clerk3','clerk2','2017-11-02 00:00:00', '2017-11-04 23:59:59');

INSERT INTO `RentalRentsTool`
VALUES (2, 'user2', 1);

INSERT INTO `RentalRentsTool`
VALUES (2, 'user2', 2);

INSERT INTO `Rental` 
VALUES (3,'user3',  'clerk3','clerk3','2017-11-02 00:00:00', '2017-11-04 23:59:59');

INSERT INTO `RentalRentsTool`
VALUES (3, 'user3', 45);

INSERT INTO `RentalRentsTool`
VALUES (3, 'user3', 20);

INSERT INTO `Rental` 
VALUES (4,'user4', 'clerk1','clerk4','2017-11-05 00:00:00', '2017-11-06 23:59:59');

INSERT INTO `RentalRentsTool`
VALUES (4, 'user4', 45);

INSERT INTO `RentalRentsTool`
VALUES (4, 'user4', 20);

INSERT INTO `Rental` (customer_username, confirmation_number,pickup_clerk_username ,start_date, end_date)
VALUES ('user5',5, 'clerk3','2017-11-02 00:00:00', '2017-11-21 23:59:59');

INSERT INTO `RentalRentsTool`
VALUES (5, 'user5', 45);

INSERT INTO `RentalRentsTool`
VALUES (5, 'user5', 20);

INSERT INTO `Rental` (customer_username, confirmation_number,pickup_clerk_username ,start_date, end_date)
VALUES ('user5',5, 'clerk3','2017-11-02 00:00:00', '2017-11-21 23:59:59');

INSERT INTO `RentalRentsTool`
VALUES (5, 'user5', 45);

INSERT INTO `RentalRentsTool`
VALUES (5, 'user5', 20);
/*
CREATE TABLE Rental (
  confirmation_number int(16) unsigned NOT NULL AUTO_INCREMENT,
  customer_username varchar(50) NOT NULL,
  pickup_clerk_username varchar(50) DEFAULT NULL,
  dropoff_clerk_username varchar(50) DEFAULT NULL,
  start_date datetime NOT NULL,
  end_date datetime NOT NULL,
  PRIMARY KEY (customer_username,confirmation_number),
  KEY confirmation_number (confirmation_number)
  '2017-11-02 23:59:59'
);
*/