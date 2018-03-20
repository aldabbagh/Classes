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
  first_login TINYINT(1) NOT NULL,

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
  street varchar(50) NOT NULL,
  state varchar(50) NOT NULL,
  city varchar(50) NOT NULL,
  9_digit_zip int(16) unsigned NOT NULL,

  PRIMARY KEY (username),
  UNIQUE KEY email (email)
);

CREATE TABLE Tool (
  tool_number int(16) unsigned NOT NULL AUTO_INCREMENT,
  sub_type varchar(24) NOT NULL,
  sub_option varchar(16) NOT NULL,
  width_diameter DECIMAL(10,4) NOT NULL,
  length DECIMAL(10,4) NOT NULL,
  price DECIMAL(10,4) NOT NULL,
  weight DECIMAL(10,4) NOT NULL,
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
  adjustable TINYINT(1) DEFAULT NULL,
  PRIMARY KEY (tool_number)
);

ALTER TABLE Plier
  ADD CONSTRAINT fk_Plier_toolNumber_HandTool_toolNumber FOREIGN KEY (tool_number) REFERENCES HandTool (tool_number);

CREATE TABLE Wrench (
  tool_number int(16) unsigned NOT NULL,
  drive_size DECIMAL(10,4) DEFAULT NULL,
  PRIMARY KEY (tool_number)
);

ALTER TABLE Wrench
  ADD CONSTRAINT fk_Wrench_toolNumber_HandTool_toolNumber FOREIGN KEY (tool_number) REFERENCES HandTool (tool_number);

CREATE TABLE Ratchet (
  tool_number int(16) unsigned NOT NULL,
  drive_size DECIMAL(10,4) NOT NULL,
  PRIMARY KEY (tool_number)
);

ALTER TABLE Ratchet
  ADD CONSTRAINT fk_Ratchet_toolNumber_HandTool_toolNumber FOREIGN KEY (tool_number) REFERENCES HandTool (tool_number);

CREATE TABLE Socket (
  tool_number int(16) unsigned NOT NULL,
  drive_size DECIMAL(10,4) NOT NULL,
  sae_size DECIMAL(10,4) NOT NULL,
  deep_socket TINYINT(1) DEFAULT NULL,
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
  anti_vibration TINYINT(1) DEFAULT NULL,
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
  volt_rating DECIMAL(10,4) NOT NULL,
  amp_rating DECIMAL(10,4) NOT NULL,
  min_rpm DECIMAL(10,4) NOT NULL,
  max_rpm DECIMAL(10,4) DEFAULT NULL,
  PRIMARY KEY (tool_number)
);

ALTER TABLE PowerTool
  ADD CONSTRAINT fk_PowerTool_toolNumber_Tool_toolNumber FOREIGN KEY (tool_number) REFERENCES Tool (tool_number);

CREATE TABLE PowerAirCompressor (
  tool_number int(16) unsigned NOT NULL,
  tank_size DECIMAL(10,4) NOT NULL,
  pressure_rating DECIMAL(10,4) DEFAULT NULL,
  PRIMARY KEY (tool_number)
);

ALTER TABLE PowerAirCompressor
  ADD CONSTRAINT fk_PowerAirCompressor_toolNumber_PowerTool_toolNumber FOREIGN KEY (tool_number) REFERENCES PowerTool (tool_number);

CREATE TABLE PowerMixer (
  tool_number int(16) unsigned NOT NULL,
  motor_rating DECIMAL(10,4) NOT NULL,
  drum_size DECIMAL(10,4) NOT NULL,
  PRIMARY KEY (tool_number)
);

ALTER TABLE PowerMixer
  ADD CONSTRAINT fk_PowerMixer_toolNumber_PowerTool_toolNumber FOREIGN KEY (tool_number) REFERENCES PowerTool (tool_number);

CREATE TABLE PowerGenerator (
  tool_number int(16) unsigned NOT NULL,
  power_rating DECIMAL(10,4) NOT NULL,
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
  min_torque DECIMAL(10,4) NOT NULL,
  max_torque DECIMAL(10,4) DEFAULT NULL,
  adjustable_clutch TINYINT(1) DEFAULT NULL,
  PRIMARY KEY (tool_number)
);

ALTER TABLE PowerDrill
  ADD CONSTRAINT fk_PowerDrill_toolNumber_PowerTool_toolNumber FOREIGN KEY (tool_number) REFERENCES PowerTool (tool_number);

CREATE TABLE Accessory (
  tool_number int(16) unsigned NOT NULL,
  accessory_name varchar(120) NOT NULL,
  accessory_description varchar(120) NOT NULL,
  accessory_quantity int(16) unsigned NOT NULL,
  PRIMARY KEY (tool_number,accessory_name)
);

ALTER TABLE Accessory
  ADD CONSTRAINT fk_Accessory_toolNumber_PowerTool_toolNumber FOREIGN KEY (tool_number) REFERENCES PowerTool (tool_number);

CREATE TABLE PowerSaw (
  tool_number int(16) unsigned NOT NULL,
  blade_size DECIMAL(10,4) NOT NULL,
  PRIMARY KEY (tool_number)
);

ALTER TABLE PowerSaw
  ADD CONSTRAINT fk_PowerSaw_toolNumber_PowerTool_toolNumber FOREIGN KEY (tool_number) REFERENCES PowerTool (tool_number);

CREATE TABLE PowerSander (
  tool_number int(16) unsigned NOT NULL,
  dust_bag TINYINT(1) DEFAULT NULL,
  PRIMARY KEY (tool_number)
);

ALTER TABLE PowerSander
  ADD CONSTRAINT fk_PowerSander_toolNumber_PowerTool_toolNumber FOREIGN KEY (tool_number) REFERENCES PowerTool (tool_number);

CREATE TABLE Ladder (
  tool_number int(16) unsigned NOT NULL,
  step_count int(16) unsigned NOT NULL,
  weight_capacity DECIMAL(10,4) DEFAULT NULL,
  PRIMARY KEY (tool_number)
);

ALTER TABLE Ladder
  ADD CONSTRAINT fk_Ladder_toolNumber_Tool_toolNumber FOREIGN KEY (tool_number) REFERENCES Tool (tool_number);

CREATE TABLE StraightLadder (
  tool_number int(16) unsigned NOT NULL,
  rubber_feet TINYINT(1) DEFAULT NULL,
  PRIMARY KEY (tool_number)
);

ALTER TABLE StraightLadder
  ADD CONSTRAINT fk_StraightLadder_toolNumber_Ladder_toolNumber FOREIGN KEY (tool_number) REFERENCES Ladder (tool_number);

CREATE TABLE StepLadder (
  tool_number int(16) unsigned NOT NULL,
  pail_shelf TINYINT(1) DEFAULT NULL,
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
  blade_length DECIMAL(10,4) NOT NULL,
  PRIMARY KEY (tool_number)
);

ALTER TABLE Pruning
  ADD CONSTRAINT fk_Pruning_toolNumber_GardenTool_toolNumber FOREIGN KEY (tool_number) REFERENCES GardenTool (tool_number);

CREATE TABLE Striking (
  tool_number int(16) unsigned NOT NULL,
  head_weight DECIMAL(10,4) NOT NULL,
  PRIMARY KEY (tool_number)
);

ALTER TABLE Striking
  ADD CONSTRAINT fk_Striking_toolNumber_GardenTool_toolNumber FOREIGN KEY (tool_number) REFERENCES GardenTool (tool_number);

CREATE TABLE DIGGING (
  tool_number int(16) unsigned NOT NULL,
  blade_length DECIMAL(10,4) NOT NULL,
  blade_width DECIMAL(10,4) NOT NULL,
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
  bin_volume DECIMAL(10,4) DEFAULT NULL,
  PRIMARY KEY (tool_number)
);

ALTER TABLE WheelBarrow
  ADD CONSTRAINT fk_WheelBarrow_toolNumber_GardenTool_toolNumber FOREIGN KEY (tool_number) REFERENCES GardenTool (tool_number);

CREATE TABLE PhoneNumber (
  area_code varchar(50) NOT NULL,
  is_primary TINYINT(1) NOT NULL,
  phone_number varchar(50) NOT NULL,
  phone_type varchar (50) DEFAULT NULL,
  extension varchar(50) DEFAULT NULL,
  username varchar(50) NOT NULL,
  PRIMARY KEY (username,area_code,phone_number)
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
VALUES (18, 'manual', 'pruning', 'sheer', 8, 5, 6.1, 3, 'steel', 'ABC');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (18, 'wooden');

INSERT INTO `Pruning` (tool_number, blade_material, blade_length)
VALUES (18, 'steel', 10.125);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (19, 'manual', 'pruning', 'loppers', 80, 6, 6.3, 0.75, 'titanium', 'Phillips');

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
VALUES (26, 'Accessory-0', 'drill_bits', 12);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (27, 'cordless', 'powerdrill', 'driver', 39.99, 12, 13.1, 14, 'steel', 'DEWALT');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (27, 70, 5, 2000, 4500);

INSERT INTO `PowerDrill` (tool_number, min_torque, max_torque, adjustable_clutch)
VALUES (27, 80, 1200.1, 0);

INSERT INTO `Accessory`
VALUES (27, 'Accessory-0', 'drill_bits', 12);

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
VALUES (28, 'Accessory-0', 'drill_bits', 12);

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
VALUES (29, 'Accessory-0', 'drill_bits', 12);

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
VALUES (48, 'Accessory-0', 'gas_tank', 2);

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

/* Additional Tools */
INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (54, 'manual', 'screwdriver', 'phillips', 10, 3, 6, 0.5, 'steel', 'DEWALT');

INSERT INTO `HandTool`
VALUES (54);

INSERT INTO `ScrewDriver` (tool_number, screw_size)
VALUES (54, 2);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (55, 'manual', 'screwdriver', 'hex', 25, 3.2, 8, 0.4, 'iron', 'Ryobi');

INSERT INTO `HandTool`
VALUES (55);

INSERT INTO `ScrewDriver` (tool_number, screw_size)
VALUES (55, 8);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (56, 'manual', 'screwdriver', 'torx', 15, 3.7, 6.7, 0.6, 'Aluminum', 'ABC');

INSERT INTO `HandTool`
VALUES (56);

INSERT INTO `ScrewDriver` (tool_number, screw_size)
VALUES (56, 3);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (57, 'manual', 'socket', 'deep', 7, 4.7, 5.7, 0.55, 'Aluminum', 'Phillips');

INSERT INTO `HandTool`
VALUES (57);

INSERT INTO `Socket` (tool_number, drive_size, sae_size, deep_socket)
VALUES (57, 0.5, 0.25, 1);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (58, 'manual', 'socket', 'standard', 12, 8.7, 5.9, 0.65, 'steel', 'Phillips');

INSERT INTO `HandTool`
VALUES (58);

INSERT INTO `Socket` (tool_number, drive_size, sae_size, deep_socket)
VALUES (58, 0.375, 0.3125, 0);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (59, 'manual', 'ratchet', 'fixed', 17, 3.7, 6.9, 2, 'steel', 'ABC');

INSERT INTO `HandTool`
VALUES (59);

INSERT INTO `Ratchet` (tool_number, drive_size)
VALUES (59, 0.375);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (60, 'manual', 'wrench', 'pipe', 15, 1.7, 8.9, 2.1, 'steel', 'ABC');

INSERT INTO `HandTool`
VALUES (60);

INSERT INTO `Wrench` (tool_number, drive_size)
VALUES (60, 0.375);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (61, 'manual', 'wrench', 'torque', 15, 1.7, 8.9, 2.1, 'steel', 'DEWALT');

INSERT INTO `HandTool`
VALUES (61);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (62, 'manual', 'plier', 'cutting', 20, 1.1, 9.9, 3.1, 'iron', 'DEWALT');

INSERT INTO `HandTool`
VALUES (62);

INSERT INTO `Plier` (tool_number, adjustable)
VALUES (62, 1);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (63, 'manual', 'plier', 'crimper', 25, 1.1, 9.9, 3.1, 'steel', 'Phillips');

INSERT INTO `HandTool`
VALUES (63);

INSERT INTO `Plier` (tool_number, adjustable)
VALUES (63, 0);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (64, 'manual', 'gun', 'nail', 100, 12.1, 11, 6, 'steel', 'Phillips');

INSERT INTO `HandTool`
VALUES (64);

INSERT INTO `Gun` (tool_number, gauge_rating, capacity)
VALUES (64, 18, 100);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (65, 'manual', 'hammer', 'sledge', 30, 15, 15, 25, 'rubber', 'ABC');

INSERT INTO `HandTool`
VALUES (65);

INSERT INTO `Hammer` (tool_number, anti_vibration)
VALUES (65, 0);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (66, 'manual', 'digging', 'flat_shovel', 60, 12, 11.1, 10, 'steel', 'Milwaukee');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (66, 'wooden');

INSERT INTO `DIGGING` (tool_number, blade_width, blade_length)
VALUES (66, 6.875, 8.125);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (67, 'manual', 'digging', 'flat_shovel', 60, 12, 11.1, 10, 'steel', 'Milwaukee');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (67, 'wooden');

INSERT INTO `DIGGING` (tool_number, blade_width, blade_length)
VALUES (67, 6.875, 8.125);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (68, 'gas', 'digging', 'gas-auger', 65, 12, 13.1, 50, 'iron', 'ABC');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (68, 'poly');

INSERT INTO `DIGGING` (tool_number, blade_width, blade_length)
VALUES (68, 6.875, 8.125);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (69, 'cordless', 'digging', 'edger', 38, 11, 12.1, 40, 'iron', 'Milwaukee');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (69, 'fiberglass');

INSERT INTO `DIGGING` (tool_number, blade_width, blade_length)
VALUES (69, 9.75, 10.125);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (70, 'electric', 'digging', 'edger', 38, 11, 12.1, 40, 'iron', 'Milwaukee');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (70, 'fiberglass');

INSERT INTO `DIGGING` (tool_number, blade_width, blade_length)
VALUES (70, 9.75, 10.125);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (71, 'manual', 'pruning', 'sheer', 8, 5, 6.1, 3, 'steel', 'ABC');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (71, 'wooden');

INSERT INTO `Pruning` (tool_number, blade_material, blade_length)
VALUES (71, 'steel', 10.125);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (72, 'manual', 'pruning', 'loppers', 80, 6, 6.3, 0.75, 'titanium', 'Phillips');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (72, 'fiberglass');

INSERT INTO `Pruning` (tool_number, blade_material, blade_length)
VALUES (72, 'titanium', 10.125);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (73, 'manual', 'rake', 'leaf', 5, 2, 3.3, 0.45, 'steel', 'Phillips');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (73, 'wooden');

INSERT INTO `Rake` (tool_number, tine_count)
VALUES (73, 14);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (74, 'manual', 'rake', 'rock', 7.5, 2, 3.3, 0.45, 'steel', 'Phillips');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (74, 'wooden');

INSERT INTO `Rake` (tool_number, tine_count)
VALUES (74, 16);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (75, 'manual', 'wheelbarrow', '1_wheel', 50, 14.22, 4.3, 2.45, 'metal', 'ABC');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (75, 'metal');

INSERT INTO `WheelBarrow` (tool_number, bin_material, bin_volume, wheel_count)
VALUES (75, 'fiberglass', 10.2, 1);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (76, 'manual', 'wheelbarrow', '2_wheel', 54, 14.22, 4.3, 2.45, 'metal', 'ABC');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (76, 'metal');

INSERT INTO `WheelBarrow` (tool_number, bin_material, bin_volume, wheel_count)
VALUES (76, 'poly', 10.2, 2);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (77, 'manual', 'striking', 'bar_pry', 12, 7.23, 2.3, 11.9, 'iron', 'Milwaukee');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (77, 'wooden');

INSERT INTO `Striking` (tool_number, head_weight)
VALUES (77, 8.9);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (78, 'manual', 'striking', 'rubber_mallet', 6.75, 7.23, 2.3, 4.9, 'steel', 'ABC');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (78, 'wooden');

INSERT INTO `Striking` (tool_number, head_weight)
VALUES (78, 3.5);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (79, 'electric', 'powerdrill', 'driver', 39.99, 12, 13.1, 14, 'steel', 'DEWALT');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (79, 220, 5, 2000, 4500);

INSERT INTO `PowerDrill` (tool_number, min_torque, max_torque, adjustable_clutch)
VALUES (79, 80, 1200.1, 0);

INSERT INTO `Accessory`
VALUES (79, 'Accessory-0', 'drill_bits', 12);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (80, 'cordless', 'powerdrill', 'driver', 39.99, 12, 13.1, 14, 'steel', 'DEWALT');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (80, 70, 5, 2000, 4500);

INSERT INTO `PowerDrill` (tool_number, min_torque, max_torque, adjustable_clutch)
VALUES (80, 80, 1200.1, 0);

INSERT INTO `Accessory`
VALUES (80, 'Accessory-0', 'drill_bits', 12);

INSERT INTO `Accessory`
VALUES (80, 'battery', 'NiCd', 12);

INSERT INTO `CordlessPowerTool`
VALUES (80, 'NiCd');

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (81, 'cordless', 'powerdrill', 'driver', 40.99, 12, 13.1, 14, 'steel', 'DEWALT');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (81, 60, 5, 2000, 4500);

INSERT INTO `PowerDrill` (tool_number, min_torque, max_torque, adjustable_clutch)
VALUES (81, 80, 1200.1, 0);

INSERT INTO `Accessory`
VALUES (81, 'Accessory-0', 'drill_bits', 12);

INSERT INTO `Accessory`
VALUES (81, 'battery', 'li_ion', 12);

INSERT INTO `CordlessPowerTool`
VALUES (81, 'li_ion');

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (82, 'cordless', 'powerdrill', 'hammer', 45.99, 12, 13.1, 14, 'steel', 'DEWALT');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (82, 55, 5, 2000, 4500);

INSERT INTO `PowerDrill` (tool_number, min_torque, max_torque, adjustable_clutch)
VALUES (82, 80, 1200.1, 0);

INSERT INTO `Accessory`
VALUES (82, 'Accessory-0', 'drill_bits', 12);

INSERT INTO `Accessory`
VALUES (82, 'battery', 'NiMH', 12);

INSERT INTO `CordlessPowerTool`
VALUES (82, 'NiMH');

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (83, 'electric', 'powersaw', 'circular', 50.11, 14, 15.1, 17, 'steel', 'DEWALT');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (83, 220, 1, 1500, 3500);

INSERT INTO `PowerSaw` (tool_number, blade_size)
VALUES (83, 7.75);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (84, 'electric', 'powersaw', 'reciprocating', 59.99, 14, 15.1, 17, 'steel', 'Ryobi');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (84, 120, 1, 2500, 4500);

INSERT INTO `PowerSaw` (tool_number, blade_size)
VALUES (84, 6.5);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (85, 'electric', 'powersaw', 'jig', 59.99, 14, 15.1, 17, 'steel', 'Ryobi');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (85, 120, 1, 2500, 4500);

INSERT INTO `PowerSaw` (tool_number, blade_size)
VALUES (85, 6.5);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (86, 'cordless', 'powersaw', 'jig', 59.99, 14, 15.1, 17, 'steel', 'Ryobi');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (86, 80, 1, 1500, 3500);

INSERT INTO `PowerSaw` (tool_number, blade_size)
VALUES (86, 6.5);

INSERT INTO `Accessory`
VALUES (86, 'battery', 'NiMH', 1);

INSERT INTO `CordlessPowerTool`
VALUES (86, 'NiMH');

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (87, 'cordless', 'powersaw', 'circular', 55.99, 14, 15.1, 17, 'steel', 'Ryobi');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (87, 80, 1, 1500, 3500);

INSERT INTO `PowerSaw` (tool_number, blade_size)
VALUES (87, 7.75);

INSERT INTO `Accessory`
VALUES (87, 'battery', 'li_ion', 2);

INSERT INTO `CordlessPowerTool`
VALUES (87, 'li_ion');

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (88, 'electric', 'powersander', 'finish', 51.98, 15, 16.1, 25, 'steel', 'Ryobi');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (88, 120, 1, 2500, 3500);

INSERT INTO `PowerSander` (tool_number, dust_bag)
VALUES (88, 1);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (89, 'electric', 'powersander', 'sheet', 51.98, 15, 16.1, 25, 'steel', 'Ryobi');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (89, 220, 1, 2500, 4500);

INSERT INTO `PowerSander` (tool_number, dust_bag)
VALUES (89, 1);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (90, 'electric', 'powersander', 'belt', 51.98, 15, 16.1, 25, 'steel', 'Phillips');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (90, 220, 1, 2500, 4500);

INSERT INTO `PowerSander` (tool_number, dust_bag)
VALUES (90, 1);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (91, 'electric', 'powersander', 'random_orbital', 70.98, 15, 16.1, 25, 'steel', 'Phillips');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (91, 220, 1, 2500, 4500);

INSERT INTO `PowerSander` (tool_number, dust_bag)
VALUES (91, 0);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (92, 'cordless', 'powersander', 'random_orbital', 100.00, 12, 14.1, 35, 'steel', 'Phillips');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (92, 80, 1, 1500, 3500);

INSERT INTO `PowerSander` (tool_number, dust_bag)
VALUES (92, 1);

INSERT INTO `Accessory`
VALUES (92, 'battery', 'li_ion', 1);

INSERT INTO `CordlessPowerTool`
VALUES (92, 'li_ion');

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (93, 'cordless', 'powersander', 'sheet', 100.00, 12, 14.1, 35, 'steel', 'Phillips');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (93, 74, 1, 1500, 3500);

INSERT INTO `PowerSander` (tool_number, dust_bag)
VALUES (93, 0);

INSERT INTO `Accessory`
VALUES (93, 'battery', 'NiMH', 2);

INSERT INTO `CordlessPowerTool`
VALUES (93, 'NiMH');

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (94, 'electric', 'poweraircompressor', 'reciprocating', 100.00, 12, 14.1, 35, NULL, 'Phillips');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (94, 110, 5, 2500, 4500);

INSERT INTO `PowerAirCompressor` (tool_number, tank_size, pressure_rating)
VALUES (94, 7, 300);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (95, 'gas', 'poweraircompressor', 'reciprocating', 120.00, 5, 9, 45, 'steel', 'ABC');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (95, 120, 5, 2500, 3500);

INSERT INTO `PowerAirCompressor` (tool_number, tank_size, pressure_rating)
VALUES (95, 10, 2500);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (96, 'electric', 'powermixer', 'concrete', 300.00, 12, 14.1, 35, NULL, 'Milwaukee');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (96, 220, 1, 1500, 3000);

INSERT INTO `PowerMixer` (tool_number, motor_rating, drum_size)
VALUES (96, 0.5, 3.5);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (97, 'gas', 'powermixer', 'concrete', 200.00, 14, 15.1, 35, NULL, 'Milwaukee');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (97, 120, 1, 1500, 3000);

INSERT INTO `PowerMixer` (tool_number, motor_rating, drum_size)
VALUES (97, 0.25, 6.0);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (98, 'gas', 'powermixer', 'concrete', 200.00, 14, 15.1, 35, 'steel', 'Milwaukee');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (98, 120, 1, 1500, 3000);

INSERT INTO `PowerMixer` (tool_number, motor_rating, drum_size)
VALUES (98, 0.25, 6.0);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (99, 'gas', 'powergenerator', 'electric', 500.10, 50, 40, 120, 'steel', 'ABB');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (99, 120, 1, 1500, 3000);

INSERT INTO `PowerGenerator` (tool_number, power_rating)
VALUES (99, 88);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (100, 'gas', 'powergenerator', 'electric', 420.99, 40, 35, 120, NULL, 'ABC');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (100, 220, 1, 3500, 4500);

INSERT INTO `PowerGenerator` (tool_number, power_rating)
VALUES (100, 1000);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (101, 'gas', 'powergenerator', 'electric', 420.99, 40, 35, 120, NULL, 'ABB');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (101, 220, 1, 3500, 4500);

INSERT INTO `PowerGenerator` (tool_number, power_rating)
VALUES (101, 1000);

INSERT INTO `Accessory`
VALUES (101, 'Accessory-0', 'gas_tank', 2);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (102, 'manual', 'stepladder', 'rigid', 50.41, 30, 300, 30, 'steel', 'DEWALT');

INSERT INTO `Ladder` (tool_number, step_count, weight_capacity)
VALUES (102, 20, 350);

INSERT INTO `StepLadder` (tool_number, pail_shelf)
VALUES (102, 1);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (103, 'manual', 'stepladder', 'telescoping', 35.54, 30, 75, 30, 'steel', 'DEWALT');

INSERT INTO `Ladder` (tool_number, step_count, weight_capacity)
VALUES (103, 8, 200);

INSERT INTO `StepLadder` (tool_number, pail_shelf)
VALUES (103, 1);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (104, 'manual', 'straightladder', 'folding', 37.54, 30, 75, 30, NULL, 'DEWALT');

INSERT INTO `Ladder` (tool_number, step_count, weight_capacity)
VALUES (104, 8, 200);

INSERT INTO `StraightLadder` (tool_number, rubber_feet)
VALUES (104, 1);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (105, 'manual', 'straightladder', 'multi_position', 36.54, 30, 95, 30, NULL, 'DEWALT');

INSERT INTO `Ladder` (tool_number, step_count, weight_capacity)
VALUES (105, 8, 250);

INSERT INTO `StraightLadder` (tool_number, rubber_feet)
VALUES (105, 1);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (106, 'manual', 'straightladder', 'multi_position', 31.94, 25, 45, 15, NULL, 'DEWALT');

INSERT INTO `Ladder` (tool_number, step_count, weight_capacity)
VALUES (106, 4, 250);

INSERT INTO `StraightLadder` (tool_number, rubber_feet)
VALUES (106, 0);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (107, 'manual', 'screwdriver', 'phillips', 10, 3, 6, 0.5, 'steel', 'DEWALT');

INSERT INTO `HandTool`
VALUES (107);

INSERT INTO `ScrewDriver` (tool_number, screw_size)
VALUES (107, 2);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (108, 'manual', 'screwdriver', 'hex', 25, 3.2, 8, 0.4, 'iron', 'Ryobi');

INSERT INTO `HandTool`
VALUES (108);

INSERT INTO `ScrewDriver` (tool_number, screw_size)
VALUES (108, 8);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (109, 'manual', 'screwdriver', 'torx', 15, 3.7, 6.7, 0.6, 'Aluminum', 'ABC');

INSERT INTO `HandTool`
VALUES (109);

INSERT INTO `ScrewDriver` (tool_number, screw_size)
VALUES (109, 3);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (110, 'manual', 'socket', 'deep', 7, 4.7, 5.7, 0.55, 'Aluminum', 'Phillips');

INSERT INTO `HandTool`
VALUES (110);

INSERT INTO `Socket` (tool_number, drive_size, sae_size, deep_socket)
VALUES (110, 0.5, 0.25, 1);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (111, 'manual', 'socket', 'standard', 12, 8.7, 5.9, 0.65, 'steel', 'Phillips');

INSERT INTO `HandTool`
VALUES (111);

INSERT INTO `Socket` (tool_number, drive_size, sae_size, deep_socket)
VALUES (111, 0.375, 0.3125, 0);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (112, 'manual', 'ratchet', 'fixed', 17, 3.7, 6.9, 2, 'steel', 'ABC');

INSERT INTO `HandTool`
VALUES (112);

INSERT INTO `Ratchet` (tool_number, drive_size)
VALUES (112, 0.375);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (113, 'manual', 'wrench', 'pipe', 15, 1.7, 8.9, 2.1, 'steel', 'ABC');

INSERT INTO `HandTool`
VALUES (113);

INSERT INTO `Wrench` (tool_number, drive_size)
VALUES (113, 0.375);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (114, 'manual', 'wrench', 'torque', 15, 1.7, 8.9, 2.1, 'steel', 'DEWALT');

INSERT INTO `HandTool`
VALUES (114);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (115, 'manual', 'plier', 'cutting', 20, 1.1, 9.9, 3.1, 'iron', 'DEWALT');

INSERT INTO `HandTool`
VALUES (115);

INSERT INTO `Plier` (tool_number, adjustable)
VALUES (115, 1);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (116, 'manual', 'plier', 'crimper', 25, 1.1, 9.9, 3.1, 'steel', 'Phillips');

INSERT INTO `HandTool`
VALUES (116);

INSERT INTO `Plier` (tool_number, adjustable)
VALUES (116, 0);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (117, 'manual', 'gun', 'nail', 100, 12.1, 11, 6, 'steel', 'Phillips');

INSERT INTO `HandTool`
VALUES (117);

INSERT INTO `Gun` (tool_number, gauge_rating, capacity)
VALUES (117, 18, 100);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (118, 'manual', 'hammer', 'sledge', 30, 15, 15, 25, 'rubber', 'ABC');

INSERT INTO `HandTool`
VALUES (118);

INSERT INTO `Hammer` (tool_number, anti_vibration)
VALUES (118, 0);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (119, 'manual', 'digging', 'flat_shovel', 60, 12, 11.1, 10, 'steel', 'Milwaukee');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (119, 'wooden');

INSERT INTO `DIGGING` (tool_number, blade_width, blade_length)
VALUES (119, 6.875, 8.125);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (120, 'manual', 'digging', 'flat_shovel', 60, 12, 11.1, 10, 'steel', 'Milwaukee');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (120, 'wooden');

INSERT INTO `DIGGING` (tool_number, blade_width, blade_length)
VALUES (120, 6.875, 8.125);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (121, 'gas', 'digging', 'gas-auger', 65, 12, 13.1, 50, 'iron', 'ABC');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (121, 'poly');

INSERT INTO `DIGGING` (tool_number, blade_width, blade_length)
VALUES (121, 6.875, 8.125);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (122, 'cordless', 'digging', 'edger', 38, 11, 12.1, 40, 'iron', 'Milwaukee');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (122, 'fiberglass');

INSERT INTO `DIGGING` (tool_number, blade_width, blade_length)
VALUES (122, 9.75, 10.125);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (123, 'electric', 'digging', 'edger', 38, 11, 12.1, 40, 'iron', 'Milwaukee');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (123, 'fiberglass');

INSERT INTO `DIGGING` (tool_number, blade_width, blade_length)
VALUES (123, 9.75, 10.125);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (124, 'manual', 'pruning', 'sheer', 8, 5, 6.1, 3, 'steel', 'ABC');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (124, 'wooden');

INSERT INTO `Pruning` (tool_number, blade_material, blade_length)
VALUES (124, 'steel', 10.125);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (125, 'manual', 'pruning', 'loppers', 80, 6, 6.3, 0.75, 'titanium', 'Phillips');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (125, 'fiberglass');

INSERT INTO `Pruning` (tool_number, blade_material, blade_length)
VALUES (125, 'titanium', 10.125);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (126, 'manual', 'rake', 'leaf', 5, 2, 3.3, 0.45, 'steel', 'Phillips');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (126, 'wooden');

INSERT INTO `Rake` (tool_number, tine_count)
VALUES (126, 14);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (127, 'manual', 'rake', 'rock', 7.5, 2, 3.3, 0.45, 'steel', 'Phillips');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (127, 'wooden');

INSERT INTO `Rake` (tool_number, tine_count)
VALUES (127, 16);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (128, 'manual', 'wheelbarrow', '1_wheel', 50, 14.22, 4.3, 2.45, 'metal', 'ABC');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (128, 'metal');

INSERT INTO `WheelBarrow` (tool_number, bin_material, bin_volume, wheel_count)
VALUES (128, 'fiberglass', 10.2, 1);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (129, 'manual', 'wheelbarrow', '2_wheel', 54, 14.22, 4.3, 2.45, 'metal', 'ABC');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (129, 'metal');

INSERT INTO `WheelBarrow` (tool_number, bin_material, bin_volume, wheel_count)
VALUES (129, 'poly', 10.2, 2);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (130, 'manual', 'striking', 'bar_pry', 12, 7.23, 2.3, 11.9, 'iron', 'Milwaukee');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (130, 'wooden');

INSERT INTO `Striking` (tool_number, head_weight)
VALUES (130, 8.9);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (131, 'manual', 'striking', 'rubber_mallet', 6.75, 7.23, 2.3, 4.9, 'steel', 'ABC');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (131, 'wooden');

INSERT INTO `Striking` (tool_number, head_weight)
VALUES (131, 3.5);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (132, 'electric', 'powerdrill', 'driver', 39.99, 12, 13.1, 14, 'steel', 'DEWALT');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (132, 220, 5, 2000, 4500);

INSERT INTO `PowerDrill` (tool_number, min_torque, max_torque, adjustable_clutch)
VALUES (132, 80, 1200.1, 0);

INSERT INTO `Accessory`
VALUES (132, 'Accessory-0', 'drill_bits', 12);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (133, 'cordless', 'powerdrill', 'driver', 39.99, 12, 13.1, 14, 'steel', 'DEWALT');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (133, 70, 5, 2000, 4500);

INSERT INTO `PowerDrill` (tool_number, min_torque, max_torque, adjustable_clutch)
VALUES (133, 80, 1200.1, 0);

INSERT INTO `Accessory`
VALUES (133, 'Accessory-0', 'drill_bits', 12);

INSERT INTO `Accessory`
VALUES (133, 'battery', 'NiCd', 12);

INSERT INTO `CordlessPowerTool`
VALUES (133, 'NiCd');

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (134, 'cordless', 'powerdrill', 'driver', 40.99, 12, 13.1, 14, 'steel', 'DEWALT');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (134, 60, 5, 2000, 4500);

INSERT INTO `PowerDrill` (tool_number, min_torque, max_torque, adjustable_clutch)
VALUES (134, 80, 1200.1, 0);

INSERT INTO `Accessory`
VALUES (134, 'Accessory-0', 'drill_bits', 12);

INSERT INTO `Accessory`
VALUES (134, 'battery', 'li_ion', 12);

INSERT INTO `CordlessPowerTool`
VALUES (134, 'li_ion');

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (135, 'cordless', 'powerdrill', 'hammer', 45.99, 12, 13.1, 14, 'steel', 'DEWALT');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (135, 55, 5, 2000, 4500);

INSERT INTO `PowerDrill` (tool_number, min_torque, max_torque, adjustable_clutch)
VALUES (135, 80, 1200.1, 0);

INSERT INTO `Accessory`
VALUES (135, 'Accessory-0', 'drill_bits', 12);

INSERT INTO `Accessory`
VALUES (135, 'battery', 'NiMH', 12);

INSERT INTO `CordlessPowerTool`
VALUES (135, 'NiMH');

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (136, 'electric', 'powersaw', 'circular', 50.11, 14, 15.1, 17, 'steel', 'DEWALT');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (136, 220, 1, 1500, 3500);

INSERT INTO `PowerSaw` (tool_number, blade_size)
VALUES (136, 7.75);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (137, 'electric', 'powersaw', 'reciprocating', 59.99, 14, 15.1, 17, 'steel', 'Ryobi');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (137, 120, 1, 2500, 4500);

INSERT INTO `PowerSaw` (tool_number, blade_size)
VALUES (137, 6.5);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (138, 'electric', 'powersaw', 'jig', 59.99, 14, 15.1, 17, 'steel', 'Ryobi');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (138, 120, 1, 2500, 4500);

INSERT INTO `PowerSaw` (tool_number, blade_size)
VALUES (138, 6.5);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (139, 'cordless', 'powersaw', 'jig', 59.99, 14, 15.1, 17, 'steel', 'Ryobi');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (139, 80, 1, 1500, 3500);

INSERT INTO `PowerSaw` (tool_number, blade_size)
VALUES (139, 6.5);

INSERT INTO `Accessory`
VALUES (139, 'battery', 'NiMH', 1);

INSERT INTO `CordlessPowerTool`
VALUES (139, 'NiMH');

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (140, 'cordless', 'powersaw', 'circular', 55.99, 14, 15.1, 17, 'steel', 'Ryobi');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (140, 80, 1, 1500, 3500);

INSERT INTO `PowerSaw` (tool_number, blade_size)
VALUES (140, 7.75);

INSERT INTO `Accessory`
VALUES (140, 'battery', 'li_ion', 2);

INSERT INTO `CordlessPowerTool`
VALUES (140, 'li_ion');

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (141, 'electric', 'powersander', 'finish', 51.98, 15, 16.1, 25, 'steel', 'Ryobi');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (141, 120, 1, 2500, 3500);

INSERT INTO `PowerSander` (tool_number, dust_bag)
VALUES (141, 1);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (142, 'electric', 'powersander', 'sheet', 51.98, 15, 16.1, 25, 'steel', 'Ryobi');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (142, 220, 1, 2500, 4500);

INSERT INTO `PowerSander` (tool_number, dust_bag)
VALUES (142, 1);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (143, 'electric', 'powersander', 'belt', 51.98, 15, 16.1, 25, 'steel', 'Phillips');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (143, 220, 1, 2500, 4500);

INSERT INTO `PowerSander` (tool_number, dust_bag)
VALUES (143, 1);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (144, 'electric', 'powersander', 'random_orbital', 70.98, 15, 16.1, 25, 'steel', 'Phillips');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (144, 220, 1, 2500, 4500);

INSERT INTO `PowerSander` (tool_number, dust_bag)
VALUES (144, 0);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (145, 'cordless', 'powersander', 'random_orbital', 100.00, 12, 14.1, 35, 'steel', 'Phillips');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (145, 80, 1, 1500, 3500);

INSERT INTO `PowerSander` (tool_number, dust_bag)
VALUES (145, 1);

INSERT INTO `Accessory`
VALUES (145, 'battery', 'li_ion', 1);

INSERT INTO `CordlessPowerTool`
VALUES (145, 'li_ion');

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (146, 'cordless', 'powersander', 'sheet', 100.00, 12, 14.1, 35, 'steel', 'Phillips');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (146, 74, 1, 1500, 3500);

INSERT INTO `PowerSander` (tool_number, dust_bag)
VALUES (146, 0);

INSERT INTO `Accessory`
VALUES (146, 'battery', 'NiMH', 2);

INSERT INTO `CordlessPowerTool`
VALUES (146, 'NiMH');

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (147, 'electric', 'poweraircompressor', 'reciprocating', 100.00, 12, 14.1, 35, NULL, 'Phillips');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (147, 110, 5, 2500, 4500);

INSERT INTO `PowerAirCompressor` (tool_number, tank_size, pressure_rating)
VALUES (147, 7, 300);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (148, 'gas', 'poweraircompressor', 'reciprocating', 120.00, 5, 9, 45, 'steel', 'ABC');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (148, 120, 5, 2500, 3500);

INSERT INTO `PowerAirCompressor` (tool_number, tank_size, pressure_rating)
VALUES (148, 10, 2500);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (149, 'electric', 'powermixer', 'concrete', 300.00, 12, 14.1, 35, NULL, 'Milwaukee');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (149, 220, 1, 1500, 3000);

INSERT INTO `PowerMixer` (tool_number, motor_rating, drum_size)
VALUES (149, 0.5, 3.5);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (150, 'gas', 'powermixer', 'concrete', 200.00, 14, 15.1, 35, NULL, 'Milwaukee');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (150, 120, 1, 1500, 3000);

INSERT INTO `PowerMixer` (tool_number, motor_rating, drum_size)
VALUES (150, 0.25, 6.0);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (151, 'gas', 'powermixer', 'concrete', 200.00, 14, 15.1, 35, 'steel', 'Milwaukee');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (151, 120, 1, 1500, 3000);

INSERT INTO `PowerMixer` (tool_number, motor_rating, drum_size)
VALUES (151, 0.25, 6.0);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (152, 'gas', 'powergenerator', 'electric', 500.10, 50, 40, 120, 'steel', 'ABB');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (152, 120, 1, 1500, 3000);

INSERT INTO `PowerGenerator` (tool_number, power_rating)
VALUES (152, 88);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (153, 'gas', 'powergenerator', 'electric', 420.99, 40, 35, 120, NULL, 'ABC');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (153, 220, 1, 3500, 4500);

INSERT INTO `PowerGenerator` (tool_number, power_rating)
VALUES (153, 1000);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (154, 'gas', 'powergenerator', 'electric', 420.99, 40, 35, 120, NULL, 'ABB');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (154, 220, 1, 3500, 4500);

INSERT INTO `PowerGenerator` (tool_number, power_rating)
VALUES (154, 1000);

INSERT INTO `Accessory`
VALUES (154, 'Accessory-0', 'gas_tank', 2);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (155, 'manual', 'stepladder', 'rigid', 50.41, 30, 300, 30, 'steel', 'DEWALT');

INSERT INTO `Ladder` (tool_number, step_count, weight_capacity)
VALUES (155, 20, 350);

INSERT INTO `StepLadder` (tool_number, pail_shelf)
VALUES (155, 1);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (156, 'manual', 'stepladder', 'telescoping', 35.54, 30, 75, 30, 'steel', 'DEWALT');

INSERT INTO `Ladder` (tool_number, step_count, weight_capacity)
VALUES (156, 8, 200);

INSERT INTO `StepLadder` (tool_number, pail_shelf)
VALUES (156, 1);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (157, 'manual', 'straightladder', 'folding', 37.54, 30, 75, 30, NULL, 'DEWALT');

INSERT INTO `Ladder` (tool_number, step_count, weight_capacity)
VALUES (157, 8, 200);

INSERT INTO `StraightLadder` (tool_number, rubber_feet)
VALUES (157, 1);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (158, 'manual', 'straightladder', 'multi_position', 36.54, 30, 95, 30, NULL, 'DEWALT');

INSERT INTO `Ladder` (tool_number, step_count, weight_capacity)
VALUES (158, 8, 250);

INSERT INTO `StraightLadder` (tool_number, rubber_feet)
VALUES (158, 1);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (159, 'manual', 'straightladder', 'multi_position', 31.94, 25, 45, 15, NULL, 'DEWALT');

INSERT INTO `Ladder` (tool_number, step_count, weight_capacity)
VALUES (159, 4, 250);

INSERT INTO `StraightLadder` (tool_number, rubber_feet)
VALUES (159, 0);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (160, 'manual', 'screwdriver', 'phillips', 10, 3, 6, 0.5, 'steel', 'DEWALT');

INSERT INTO `HandTool`
VALUES (160);

INSERT INTO `ScrewDriver` (tool_number, screw_size)
VALUES (160, 2);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (161, 'manual', 'screwdriver', 'hex', 25, 3.2, 8, 0.4, 'iron', 'Ryobi');

INSERT INTO `HandTool`
VALUES (161);

INSERT INTO `ScrewDriver` (tool_number, screw_size)
VALUES (161, 8);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (162, 'manual', 'screwdriver', 'torx', 15, 3.7, 6.7, 0.6, 'Aluminum', 'ABC');

INSERT INTO `HandTool`
VALUES (162);

INSERT INTO `ScrewDriver` (tool_number, screw_size)
VALUES (162, 3);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (163, 'manual', 'socket', 'deep', 7, 4.7, 5.7, 0.55, 'Aluminum', 'Phillips');

INSERT INTO `HandTool`
VALUES (163);

INSERT INTO `Socket` (tool_number, drive_size, sae_size, deep_socket)
VALUES (163, 0.5, 0.25, 1);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (164, 'manual', 'socket', 'standard', 12, 8.7, 5.9, 0.65, 'steel', 'Phillips');

INSERT INTO `HandTool`
VALUES (164);

INSERT INTO `Socket` (tool_number, drive_size, sae_size, deep_socket)
VALUES (164, 0.375, 0.3125, 0);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (165, 'manual', 'ratchet', 'fixed', 17, 3.7, 6.9, 2, 'steel', 'ABC');

INSERT INTO `HandTool`
VALUES (165);

INSERT INTO `Ratchet` (tool_number, drive_size)
VALUES (165, 0.375);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (166, 'manual', 'wrench', 'pipe', 15, 1.7, 8.9, 2.1, 'steel', 'ABC');

INSERT INTO `HandTool`
VALUES (166);

INSERT INTO `Wrench` (tool_number, drive_size)
VALUES (166, 0.375);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (167, 'manual', 'wrench', 'torque', 15, 1.7, 8.9, 2.1, 'steel', 'DEWALT');

INSERT INTO `HandTool`
VALUES (167);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (168, 'manual', 'plier', 'cutting', 20, 1.1, 9.9, 3.1, 'iron', 'DEWALT');

INSERT INTO `HandTool`
VALUES (168);

INSERT INTO `Plier` (tool_number, adjustable)
VALUES (168, 1);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (169, 'manual', 'plier', 'crimper', 25, 1.1, 9.9, 3.1, 'steel', 'Phillips');

INSERT INTO `HandTool`
VALUES (169);

INSERT INTO `Plier` (tool_number, adjustable)
VALUES (169, 0);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (170, 'manual', 'gun', 'nail', 100, 12.1, 11, 6, 'steel', 'Phillips');

INSERT INTO `HandTool`
VALUES (170);

INSERT INTO `Gun` (tool_number, gauge_rating, capacity)
VALUES (170, 18, 100);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (171, 'manual', 'hammer', 'sledge', 30, 15, 15, 25, 'rubber', 'ABC');

INSERT INTO `HandTool`
VALUES (171);

INSERT INTO `Hammer` (tool_number, anti_vibration)
VALUES (171, 0);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (172, 'manual', 'digging', 'flat_shovel', 60, 12, 11.1, 10, 'steel', 'Milwaukee');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (172, 'wooden');

INSERT INTO `DIGGING` (tool_number, blade_width, blade_length)
VALUES (172, 6.875, 8.125);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (173, 'manual', 'digging', 'flat_shovel', 60, 12, 11.1, 10, 'steel', 'Milwaukee');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (173, 'wooden');

INSERT INTO `DIGGING` (tool_number, blade_width, blade_length)
VALUES (173, 6.875, 8.125);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (174, 'gas', 'digging', 'gas-auger', 65, 12, 13.1, 50, 'iron', 'ABC');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (174, 'poly');

INSERT INTO `DIGGING` (tool_number, blade_width, blade_length)
VALUES (174, 6.875, 8.125);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (175, 'cordless', 'digging', 'edger', 38, 11, 12.1, 40, 'iron', 'Milwaukee');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (175, 'fiberglass');

INSERT INTO `DIGGING` (tool_number, blade_width, blade_length)
VALUES (175, 9.75, 10.125);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (176, 'electric', 'digging', 'edger', 38, 11, 12.1, 40, 'iron', 'Milwaukee');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (176, 'fiberglass');

INSERT INTO `DIGGING` (tool_number, blade_width, blade_length)
VALUES (176, 9.75, 10.125);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (177, 'manual', 'pruning', 'sheer', 8, 5, 6.1, 3, 'steel', 'ABC');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (177, 'wooden');

INSERT INTO `Pruning` (tool_number, blade_material, blade_length)
VALUES (177, 'steel', 10.125);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (178, 'manual', 'pruning', 'loppers', 80, 6, 6.3, 0.75, 'titanium', 'Phillips');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (178, 'fiberglass');

INSERT INTO `Pruning` (tool_number, blade_material, blade_length)
VALUES (178, 'titanium', 10.125);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (179, 'manual', 'rake', 'leaf', 5, 2, 3.3, 0.45, 'steel', 'Phillips');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (179, 'wooden');

INSERT INTO `Rake` (tool_number, tine_count)
VALUES (179, 14);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (180, 'manual', 'rake', 'rock', 7.5, 2, 3.3, 0.45, 'steel', 'Phillips');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (180, 'wooden');

INSERT INTO `Rake` (tool_number, tine_count)
VALUES (180, 16);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (181, 'manual', 'wheelbarrow', '1_wheel', 50, 14.22, 4.3, 2.45, 'metal', 'ABC');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (181, 'metal');

INSERT INTO `WheelBarrow` (tool_number, bin_material, bin_volume, wheel_count)
VALUES (181, 'fiberglass', 10.2, 1);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (182, 'manual', 'wheelbarrow', '2_wheel', 54, 14.22, 4.3, 2.45, 'metal', 'ABC');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (182, 'metal');

INSERT INTO `WheelBarrow` (tool_number, bin_material, bin_volume, wheel_count)
VALUES (182, 'poly', 10.2, 2);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (183, 'manual', 'striking', 'bar_pry', 12, 7.23, 2.3, 11.9, 'iron', 'Milwaukee');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (183, 'wooden');

INSERT INTO `Striking` (tool_number, head_weight)
VALUES (183, 8.9);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (184, 'manual', 'striking', 'rubber_mallet', 6.75, 7.23, 2.3, 4.9, 'steel', 'ABC');

INSERT INTO `GardenTool` (tool_number, handle_material)
VALUES (184, 'wooden');

INSERT INTO `Striking` (tool_number, head_weight)
VALUES (184, 3.5);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (185, 'electric', 'powerdrill', 'driver', 39.99, 12, 13.1, 14, 'steel', 'DEWALT');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (185, 220, 5, 2000, 4500);

INSERT INTO `PowerDrill` (tool_number, min_torque, max_torque, adjustable_clutch)
VALUES (185, 80, 1200.1, 0);

INSERT INTO `Accessory`
VALUES (185, 'Accessory-0', 'drill_bits', 12);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (186, 'cordless', 'powerdrill', 'driver', 39.99, 12, 13.1, 14, 'steel', 'DEWALT');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (186, 70, 5, 2000, 4500);

INSERT INTO `PowerDrill` (tool_number, min_torque, max_torque, adjustable_clutch)
VALUES (186, 80, 1200.1, 0);

INSERT INTO `Accessory`
VALUES (186, 'Accessory-0', 'drill_bits', 12);

INSERT INTO `Accessory`
VALUES (186, 'battery', 'NiCd', 12);

INSERT INTO `CordlessPowerTool`
VALUES (186, 'NiCd');

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (187, 'cordless', 'powerdrill', 'driver', 40.99, 12, 13.1, 14, 'steel', 'DEWALT');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (187, 60, 5, 2000, 4500);

INSERT INTO `PowerDrill` (tool_number, min_torque, max_torque, adjustable_clutch)
VALUES (187, 80, 1200.1, 0);

INSERT INTO `Accessory`
VALUES (187, 'Accessory-0', 'drill_bits', 12);

INSERT INTO `Accessory`
VALUES (187, 'battery', 'li_ion', 12);

INSERT INTO `CordlessPowerTool`
VALUES (187, 'li_ion');

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (188, 'cordless', 'powerdrill', 'hammer', 45.99, 12, 13.1, 14, 'steel', 'DEWALT');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (188, 55, 5, 2000, 4500);

INSERT INTO `PowerDrill` (tool_number, min_torque, max_torque, adjustable_clutch)
VALUES (188, 80, 1200.1, 0);

INSERT INTO `Accessory`
VALUES (188, 'Accessory-0', 'drill_bits', 12);

INSERT INTO `Accessory`
VALUES (188, 'battery', 'NiMH', 12);

INSERT INTO `CordlessPowerTool`
VALUES (188, 'NiMH');

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (189, 'electric', 'powersaw', 'circular', 50.11, 14, 15.1, 17, 'steel', 'DEWALT');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (189, 220, 1, 1500, 3500);

INSERT INTO `PowerSaw` (tool_number, blade_size)
VALUES (189, 7.75);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (190, 'electric', 'powersaw', 'reciprocating', 59.99, 14, 15.1, 17, 'steel', 'Ryobi');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (190, 120, 1, 2500, 4500);

INSERT INTO `PowerSaw` (tool_number, blade_size)
VALUES (190, 6.5);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (191, 'electric', 'powersaw', 'jig', 59.99, 14, 15.1, 17, 'steel', 'Ryobi');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (191, 120, 1, 2500, 4500);

INSERT INTO `PowerSaw` (tool_number, blade_size)
VALUES (191, 6.5);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (192, 'cordless', 'powersaw', 'jig', 59.99, 14, 15.1, 17, 'steel', 'Ryobi');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (192, 80, 1, 1500, 3500);

INSERT INTO `PowerSaw` (tool_number, blade_size)
VALUES (192, 6.5);

INSERT INTO `Accessory`
VALUES (192, 'battery', 'NiMH', 1);

INSERT INTO `CordlessPowerTool`
VALUES (192, 'NiMH');

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (193, 'cordless', 'powersaw', 'circular', 55.99, 14, 15.1, 17, 'steel', 'Ryobi');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (193, 80, 1, 1500, 3500);

INSERT INTO `PowerSaw` (tool_number, blade_size)
VALUES (193, 7.75);

INSERT INTO `Accessory`
VALUES (193, 'battery', 'li_ion', 2);

INSERT INTO `CordlessPowerTool`
VALUES (193, 'li_ion');

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (194, 'electric', 'powersander', 'finish', 51.98, 15, 16.1, 25, 'steel', 'Ryobi');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (194, 120, 1, 2500, 3500);

INSERT INTO `PowerSander` (tool_number, dust_bag)
VALUES (194, 1);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (195, 'electric', 'powersander', 'sheet', 51.98, 15, 16.1, 25, 'steel', 'Ryobi');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (195, 220, 1, 2500, 4500);

INSERT INTO `PowerSander` (tool_number, dust_bag)
VALUES (195, 1);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (196, 'electric', 'powersander', 'belt', 51.98, 15, 16.1, 25, 'steel', 'Phillips');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (196, 220, 1, 2500, 4500);

INSERT INTO `PowerSander` (tool_number, dust_bag)
VALUES (196, 1);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (197, 'electric', 'powersander', 'random_orbital', 70.98, 15, 16.1, 25, 'steel', 'Phillips');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (197, 220, 1, 2500, 4500);

INSERT INTO `PowerSander` (tool_number, dust_bag)
VALUES (197, 0);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (198, 'cordless', 'powersander', 'random_orbital', 100.00, 12, 14.1, 35, 'steel', 'Phillips');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (198, 80, 1, 1500, 3500);

INSERT INTO `PowerSander` (tool_number, dust_bag)
VALUES (198, 1);

INSERT INTO `Accessory`
VALUES (198, 'battery', 'li_ion', 1);

INSERT INTO `CordlessPowerTool`
VALUES (198, 'li_ion');

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (199, 'cordless', 'powersander', 'sheet', 100.00, 12, 14.1, 35, 'steel', 'Phillips');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (199, 74, 1, 1500, 3500);

INSERT INTO `PowerSander` (tool_number, dust_bag)
VALUES (199, 0);

INSERT INTO `Accessory`
VALUES (199, 'battery', 'NiMH', 2);

INSERT INTO `CordlessPowerTool`
VALUES (199, 'NiMH');

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (200, 'electric', 'poweraircompressor', 'reciprocating', 100.00, 12, 14.1, 35, NULL, 'Phillips');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (200, 110, 5, 2500, 4500);

INSERT INTO `PowerAirCompressor` (tool_number, tank_size, pressure_rating)
VALUES (200, 7, 300);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (201, 'gas', 'poweraircompressor', 'reciprocating', 120.00, 5, 9, 45, 'steel', 'ABC');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (201, 120, 5, 2500, 3500);

INSERT INTO `PowerAirCompressor` (tool_number, tank_size, pressure_rating)
VALUES (201, 10, 2500);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (202, 'electric', 'powermixer', 'concrete', 300.00, 12, 14.1, 35, NULL, 'Milwaukee');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (202, 220, 1, 1500, 3000);

INSERT INTO `PowerMixer` (tool_number, motor_rating, drum_size)
VALUES (202, 0.5, 3.5);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (203, 'gas', 'powermixer', 'concrete', 200.00, 14, 15.1, 35, NULL, 'Milwaukee');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (203, 120, 1, 1500, 3000);

INSERT INTO `PowerMixer` (tool_number, motor_rating, drum_size)
VALUES (203, 0.25, 6.0);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (204, 'gas', 'powermixer', 'concrete', 200.00, 14, 15.1, 35, 'steel', 'Milwaukee');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (204, 120, 1, 1500, 3000);

INSERT INTO `PowerMixer` (tool_number, motor_rating, drum_size)
VALUES (204, 0.25, 6.0);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (205, 'gas', 'powergenerator', 'electric', 500.10, 50, 40, 120, 'steel', 'ABB');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (205, 120, 1, 1500, 3000);

INSERT INTO `PowerGenerator` (tool_number, power_rating)
VALUES (205, 88);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (206, 'gas', 'powergenerator', 'electric', 420.99, 40, 35, 120, NULL, 'ABC');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (206, 220, 1, 3500, 4500);

INSERT INTO `PowerGenerator` (tool_number, power_rating)
VALUES (206, 1000);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (207, 'gas', 'powergenerator', 'electric', 420.99, 40, 35, 120, NULL, 'ABB');

INSERT INTO `PowerTool` (tool_number, volt_rating, amp_rating, min_rpm, max_rpm)
VALUES (207, 220, 1, 3500, 4500);

INSERT INTO `PowerGenerator` (tool_number, power_rating)
VALUES (207, 1000);

INSERT INTO `Accessory`
VALUES (207, 'Accessory-0', 'gas_tank', 2);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (208, 'manual', 'stepladder', 'rigid', 50.41, 30, 300, 30, 'steel', 'DEWALT');

INSERT INTO `Ladder` (tool_number, step_count, weight_capacity)
VALUES (208, 20, 350);

INSERT INTO `StepLadder` (tool_number, pail_shelf)
VALUES (208, 1);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (209, 'manual', 'stepladder', 'telescoping', 35.54, 30, 75, 30, 'steel', 'DEWALT');

INSERT INTO `Ladder` (tool_number, step_count, weight_capacity)
VALUES (209, 8, 200);

INSERT INTO `StepLadder` (tool_number, pail_shelf)
VALUES (209, 1);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (210, 'manual', 'straightladder', 'folding', 37.54, 30, 75, 30, NULL, 'DEWALT');

INSERT INTO `Ladder` (tool_number, step_count, weight_capacity)
VALUES (210, 8, 200);

INSERT INTO `StraightLadder` (tool_number, rubber_feet)
VALUES (210, 1);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (211, 'manual', 'straightladder', 'multi_position', 36.54, 30, 95, 30, NULL, 'DEWALT');

INSERT INTO `Ladder` (tool_number, step_count, weight_capacity)
VALUES (211, 8, 250);

INSERT INTO `StraightLadder` (tool_number, rubber_feet)
VALUES (211, 1);

INSERT INTO `Tool` (tool_number, power_source, sub_type, sub_option, price, width_diameter, length, weight, material, manufacturer)
VALUES (212, 'manual', 'straightladder', 'multi_position', 31.94, 25, 45, 15, NULL, 'DEWALT');

INSERT INTO `Ladder` (tool_number, step_count, weight_capacity)
VALUES (212, 4, 250);

INSERT INTO `StraightLadder` (tool_number, rubber_feet)
VALUES (212, 0);

/* Adding customers into database */
INSERT INTO `Customer`
VALUES ('user0@gmail.com', '123user0', 'user0@gmail.com', 'John', 'M0', 'Smith', 'John Smith', 4916416556885039, 1, 2018, 102, '10 N Oak St', 'AL', 'Montgomery', 100000000);
INSERT INTO `Customer`
VALUES ('user1@gmail.com', '123user1', 'user1@gmail.com', 'Bill', 'M1', 'Doe', 'Bill Doe', 4486891566391121, 5, 2019, 102, '15 Linn Dr', 'AK', 'Juneau', 100000001);
INSERT INTO `Customer`
VALUES ('user2@gmail.com', '123user2', 'user2@gmail.com', 'Chelsea', 'M2', 'Zhen', 'Chelsea Zhen', 4532729152864149, 7, 2021, 104, '20 Plymouth Rd', 'AZ', 'Phoenix', 100000002);
INSERT INTO `Customer`
VALUES ('user3@gmail.com', '123user3', 'user3@gmail.com', 'Mike', 'M3', 'Omar', 'Mike Omar', 4556705524517939, 9, 2018, 107, '25 Arbor Lane', 'AR', 'Little Rock', 100000003);
INSERT INTO `Customer`
VALUES ('user4@gmail.com', '123user4', 'user4@gmail.com', 'Tyler', 'M4', 'Zhang', 'Tyler Zhang', 4916763243473103, 12, 2020, 105, '30 Harmony Rd', 'CA', 'Sacramento', 100000004);
INSERT INTO `Customer`
VALUES ('user5@gmail.com', '123user5', 'user5@gmail.com', 'Malik', 'M5', 'Lee', 'Malik Lee', 4556891100352691, 1, 2018, 106, '35 Q St', 'CO', 'Denver', 100000005);
INSERT INTO `Customer`
VALUES ('user6@gmail.com', '123user6', 'user6@gmail.com', 'Ahmad', 'M6', 'Black', 'Ahmad Black', 4532448274217256, 1, 2022, 112, '40 Riyadh St', 'CT', 'Hartford', 100000006);
INSERT INTO `Customer`
VALUES ('user7@gmail.com', '123user7', 'user7@gmail.com', 'Mohammed', 'M7', 'Brown', 'Mohammed Brown', 4716520865690550, 10, 2021, 113, '45 18th st', 'DC', 'Washington', 100000007);
INSERT INTO `Customer`
VALUES ('user8@gmail.com', '123user8', 'user8@gmail.com', 'Khalid', 'M8', 'Johnson', 'Khalid Johnson', 4556147408531753, 11, 2018, 100, '50 Randolph St', 'DE', 'Dover', 100000008);
INSERT INTO `Customer`
VALUES ('user9@gmail.com', '123user9', 'user9@gmail.com', 'Daisong', 'M9', 'Mccarter', 'Daisong Mccarter', 4486809844217348, 5, 2019, 113, '55 Barry Ave', 'FL', 'Tallahassee', 100000009);
INSERT INTO `Customer`
VALUES ('user10@gmail.com', '123user10', 'user10@gmail.com', 'Robert', 'M10', 'Minsky', 'Robert Minsky', 4539108365034506, 7, 2020, 113, '60 Wilson Blvd', 'GA', 'Atlanta', 100000010);
INSERT INTO `Customer`
VALUES ('user11@gmail.com', '123user11', 'user11@gmail.com', 'Mary', 'M11', 'James', 'Mary James', 4556134216560646, 1, 2021, 107, '65 Irving St', 'HI', 'Honolulu', 100000011);
INSERT INTO `Customer`
VALUES ('user12@gmail.com', '123user12', 'user12@gmail.com', 'Sarah', 'M12', 'Durant', 'Sarah Durant', 4532957192840544, 6, 2019, 114, '70 Hudson Rd', 'ID', 'Boise', 100000012);
INSERT INTO `Customer`
VALUES ('user13@gmail.com', '123user13', 'user13@gmail.com', 'Jack', 'M13', 'Ng', 'Jack Ng', 4929448608129871, 5, 2022, 114, '75 6th St', 'IL', 'Springfield', 100000013);
INSERT INTO `Customer`
VALUES ('user14@gmail.com', '123user14', 'user14@gmail.com', 'Emilly', 'M14', 'Alan', 'Emilly Alan', 4539831035487255, 3, 2021, 112, '80 Lincoln St', 'IN', 'Indianapolis', 100000014);

/*
Add customer phonenumbers
*/
INSERT INTO `PhoneNumber`
VALUES ('294',1,'2432162','cell','','user0@gmail.com');

INSERT INTO `PhoneNumber`
VALUES ('325',1,'2439861','cell','','user1@gmail.com');

INSERT INTO `PhoneNumber`
VALUES ('858',1,'6279448','cell','','user2@gmail.com');

INSERT INTO `PhoneNumber`
VALUES ('369',1,'5222241','cell','','user3@gmail.com');

INSERT INTO `PhoneNumber`
VALUES ('491',1,'3217231','cell','','user4@gmail.com');

INSERT INTO `PhoneNumber`
VALUES ('387',1,'2400991','cell','','user5@gmail.com');

INSERT INTO `PhoneNumber`
VALUES ('757',1,'2455431','cell','','user6@gmail.com');

INSERT INTO `PhoneNumber`
VALUES ('449',1,'1954967','cell','','user7@gmail.com');

INSERT INTO `PhoneNumber`
VALUES ('674',1,'7994063','cell','','user8@gmail.com');

INSERT INTO `PhoneNumber`
VALUES ('529',1,'4964822','cell','','user9@gmail.com');

INSERT INTO `PhoneNumber`
VALUES ('394',1,'5569092','cell','','user10@gmail.com');

INSERT INTO `PhoneNumber`
VALUES ('515',1,'9655680','cell','','user11@gmail.com');

INSERT INTO `PhoneNumber`
VALUES ('444',1,'7038168','cell','','user12@gmail.com');

INSERT INTO `PhoneNumber`
VALUES ('707',1,'4642446','cell','','user13@gmail.com');

INSERT INTO `PhoneNumber`
VALUES ('708',1,'7043843','cell','','user14@gmail.com');

INSERT INTO `PhoneNumber`
VALUES ('320',0,'9358799','home','','user0@gmail.com');

INSERT INTO `PhoneNumber`
VALUES ('676',0,'9391906','home','','user1@gmail.com');

INSERT INTO `PhoneNumber`
VALUES ('635',0,'3049064','home','','user2@gmail.com');

INSERT INTO `PhoneNumber`
VALUES ('229',0,'6514974','home','','user3@gmail.com');

INSERT INTO `PhoneNumber`
VALUES ('103',0,'3433346','home','','user4@gmail.com');

INSERT INTO `PhoneNumber`
VALUES ('174',0,'3118684','home','','user5@gmail.com');

INSERT INTO `PhoneNumber`
VALUES ('303',0,'7932592','home','','user6@gmail.com');

INSERT INTO `PhoneNumber`
VALUES ('390',0,'9116533','home','','user7@gmail.com');

INSERT INTO `PhoneNumber`
VALUES ('551',0,'6570583','home','','user8@gmail.com');

INSERT INTO `PhoneNumber`
VALUES ('274',0,'6849092','home','','user9@gmail.com');

INSERT INTO `PhoneNumber`
VALUES ('360',0,'3472453','home','','user10@gmail.com');

INSERT INTO `PhoneNumber`
VALUES ('333',0,'4919348','home','','user11@gmail.com');

INSERT INTO `PhoneNumber`
VALUES ('373',0,'3846556','home','','user12@gmail.com');

INSERT INTO `PhoneNumber`
VALUES ('152',0,'8393270','work','9557','user10@gmail.com');

INSERT INTO `PhoneNumber`
VALUES ('760',0,'3080893','work','1011','user11@gmail.com');

INSERT INTO `PhoneNumber`
VALUES ('452',0,'5070201','work','5921','user12@gmail.com');

INSERT INTO `PhoneNumber`
VALUES ('706',0,'4460944','work','2876','user13@gmail.com');

INSERT INTO `PhoneNumber`
VALUES ('820',0,'4736324','work','9650','user14@gmail.com');

/*
Add Clerks to database
*/
INSERT INTO `Clerk`
VALUES ('clerk1@gmail.com',1,'clerk1@gmail.com','Jack','A','Doee','2008-01-15','123clerk1',0);

INSERT INTO `Clerk`
VALUES ('clerk2@gmail.com',2,'clerk2@gmail.com','Jacky','B','Mark','2011-02-14','123clerk2',1);

INSERT INTO `Clerk`
VALUES ('clerk3@gmail.com',3,'clerk3@gmail.com','Mark','C','Rosa','2015-11-02','123clerk3',0);

INSERT INTO `Clerk`
VALUES ('clerk4@gmail.com',4,'clerk4@gmail.com','Allan','D','Smith','2017-03-11','123clerk4',0);

INSERT INTO `Clerk`
VALUES ('clerk5@gmail.com',5,'clerk5@gmail.com','Ahmad','E','Dabbagh','2009-07-23','123clerk5',1);

INSERT INTO `Clerk`
VALUES ('clerk6@gmail.com',6,'clerk6@gmail.com','Bart','F','Mckinsey','2012-04-05','123clerk6',0);

/*
Add tool reservations
*/

INSERT INTO `Rental`
VALUES (1,'user1@gmail.com', 'clerk1@gmail.com','clerk2@gmail.com','2017-10-31 00:00:00', '2017-11-01 23:59:59');

INSERT INTO `RentalRentsTool`
VALUES (1, 'user1@gmail.com', 1);

INSERT INTO `RentalRentsTool`
VALUES (1, 'user1@gmail.com', 2);

INSERT INTO `Rental`
VALUES (2,'user2@gmail.com',  'clerk3@gmail.com','clerk2@gmail.com','2017-11-02 00:00:00', '2017-11-04 23:59:59');

INSERT INTO `RentalRentsTool`
VALUES (2, 'user2@gmail.com', 1);

INSERT INTO `RentalRentsTool`
VALUES (2, 'user2@gmail.com', 2);

INSERT INTO `Rental`
VALUES (3,'user3@gmail.com',  'clerk3@gmail.com','clerk3@gmail.com','2017-11-02 00:00:00', '2017-11-04 23:59:59');

INSERT INTO `RentalRentsTool`
VALUES (3, 'user3@gmail.com', 45);

INSERT INTO `RentalRentsTool`
VALUES (3, 'user3@gmail.com', 20);

INSERT INTO `Rental`
VALUES (4,'user4@gmail.com', 'clerk1@gmail.com','clerk4@gmail.com','2017-11-05 00:00:00', '2017-11-06 23:59:59');

INSERT INTO `RentalRentsTool`
VALUES (4, 'user4@gmail.com', 45);

INSERT INTO `RentalRentsTool`
VALUES (4, 'user4@gmail.com', 20);

INSERT INTO `Rental` (customer_username, confirmation_number,pickup_clerk_username ,start_date, end_date)
VALUES ('user5@gmail.com',5, 'clerk3@gmail.com','2017-11-02 00:00:00', '2017-11-21 23:59:59');

INSERT INTO `RentalRentsTool`
VALUES (5, 'user5@gmail.com', 45);

INSERT INTO `RentalRentsTool`
VALUES (5, 'user5@gmail.com', 20);

INSERT INTO `Rental`
VALUES (6,'user5@gmail.com', 'clerk2@gmail.com','clerk4@gmail.com','2017-11-14 00:00:00', '2017-11-16 23:59:59');

INSERT INTO `RentalRentsTool`
VALUES (6, 'user5@gmail.com', 13);

INSERT INTO `RentalRentsTool`
VALUES (6, 'user5@gmail.com', 2);

INSERT INTO `Rental` (customer_username, confirmation_number,start_date, end_date)
VALUES ('user6@gmail.com',7,'2017-11-19 00:00:00', '2017-11-21 23:59:59');

INSERT INTO `RentalRentsTool`
VALUES (7, 'user6@gmail.com', 2);

INSERT INTO `RentalRentsTool`
VALUES (7, 'user6@gmail.com', 23);

INSERT INTO `Rental`
VALUES (8,'user9@gmail.com', 'clerk4@gmail.com','clerk1@gmail.com','2017-5-14 00:00:00', '2017-5-16 23:59:59');

INSERT INTO `RentalRentsTool`
VALUES (8, 'user9@gmail.com', 11);

INSERT INTO `RentalRentsTool`
VALUES (8, 'user9@gmail.com', 2);

INSERT INTO `RentalRentsTool`
VALUES (8, 'user9@gmail.com', 20);

INSERT INTO `RentalRentsTool`
VALUES (8, 'user9@gmail.com', 37);

INSERT INTO `Rental` (customer_username, confirmation_number,pickup_clerk_username ,start_date, end_date)
VALUES ('user11@gmail.com',9, 'clerk2@gmail.com','2017-11-10 00:00:00', '2017-11-30 23:59:59');

INSERT INTO `RentalRentsTool`
VALUES (9, 'user11@gmail.com', 14);

INSERT INTO `Rental` (customer_username, confirmation_number,start_date, end_date)
VALUES ('user8@gmail.com',10,'2017-12-01 00:00:00', '2017-12-04 23:59:59');

INSERT INTO `RentalRentsTool`
VALUES (10, 'user8@gmail.com', 14);
