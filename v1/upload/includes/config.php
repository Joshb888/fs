<?php
/**
    Hydrid CAD/MDT - Computer Aided Dispatch / Mobile Data Terminal for use in GTA V Role-playing Communities.
    Copyright (C) 2018 - Hydrid Development Team

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
**/

// Version Number -- Do Not Change
$version = "PR-006";


// Disable Error Reporting
error_reporting(0);


// Oudated Variables/Constants?
$update_in_progress = "No";
define("isDonator", false);

// Get Global Functions
require_once("functions.php");

// Get Site Config
$settingsRow = dbquery('SELECT * FROM settings')[0];

//Define variables
$settings_background_db = $settingsRow['background_color'];
$settings_panel_suspended = $settingsRow['panel_suspended'];
$settings_donator = $settingsRow['donator'];
$settings_site_name_db = $settingsRow['site_name'];
$settings_site_url_db  = $settingsRow['site_url'];
$settings_theme_db      = $settingsRow['theme'];
$settings_btntheme_db  = $settingsRow['button_theme'];
$settings_sign_up_verification_db  = $settingsRow['validation_enabled'];
$settings_identity_verification_db  = $settingsRow['identity_approval_needed'];
$settings_timezone_db  = $settingsRow['timezone'];

//Module checks

//DISCORD MODULE
if (isset($settingsRow['discord_module'])) {
  if ($settingsRow['discord_module'] === "Enabled") {
    define("discordModule_isInstalled", true);
  }
} else {
  define("discordModule_isInstalled", false);
}

//CUSTOM 10 CODE MODULE
if (isset($settingsRow['custom10codes_module'])) {
  if ($settingsRow['custom10codes_module'] === "Enabled") {
    define("custom10codesModule_isInstalled", true);
  }
} else {
  define("custom10codesModule_isInstalled", false);
}

//MAP MODULE
if (isset($settingsRow['map_module'])) {
  if ($settingsRow['map_module'] === "Enabled") {
    $mapModule_link = $settingsRow['map_module_link'];
    define("mapModule_isInstalled", true);
  }
} else {
  define("mapModule_isInstalled", false);
}

//SUB DIVISION MODULE
if (isset($settingsRow['subdivision_module'])) {
  if ($settingsRow['subdivision_module'] === "Enabled") {
    define("subdivisionModule_isInstalled", true);
  } else {
    define("subdivisionModule_isInstalled", false);
  }
} else {
  define("subdivisionModule_isInstalled", false);
}

//End Module Checks


if ($settings_site_name_db === "CHANGE IN SETTINGS" || $settings_site_name_db === "CHANGE ME IN SETTINGS") {
  define("setupComplete", false);
} else {
  define("setupComplete", true);
}

//Important Settings
$background_color = ""; //light_blue, blue, red, gold
$bootstrap_theme = "$settings_theme_db";
$button_style = "$settings_btntheme_db";
$community_name = "$settings_site_name_db";
$community_url = "$settings_site_url_db";
//Validation Settings
$validation_enabled = "$settings_sign_up_verification_db";
$identity_approval_needed = "$settings_identity_verification_db";
//Urls
$url_index = "index.php";
$url_register = "register.php";
$url_welcome = "welcome.php";
$url_login = "login.php";
$url_civ_index = "civ-index.php";
$url_civ_view = "civ-view.php";
$url_civ_driverlicense = "civ-driverlicense.php";
$url_civ_registernewvehicle = "civ-registernewveh.php";
$url_civ_viewveh = "civ-viewveh.php";
$url_civ_firearms = "civ-firearms.php";
$url_civ_newarrant = "civ-addwarrant.php";
$url_civ_viewwarratns = "civ-mywarrants.php";
$url_leo_index = "leo-index.php";
$url_staff_edit_user = "staff-edituser.php";
$url_staff_index = "staff.php";
$url_fire_index = "fire-index.php";
$url_leo_supervisor_view_pending_identities = "leo-pending-identities.php";
$url_leo_supervisor_view_all_identities = "leo-all-identities.php";
$url_dispatch_index = "dispatch-index.php";
$url_staff_setup = "setup.php";
$url_settings = "user-settings.php";
$url_logout = "logout.php";

$message     = '';

$ip = $_SERVER['REMOTE_ADDR'];
date_default_timezone_set($settings_timezone_db);
$date   = date('Y-m-d');
$us_date = date_format(date_create_from_format('Y-m-d', $date), 'm/d/Y');
$time = date('h:i:s A', time());

//REMOVING ANYTHING BELOW THIS LINE WILL VOID SUPPORT.
//________________________________________________________________________________________________________________________________________________________________________________________________________________________

//YOU ARE NOT ALLOWED TO REMOVE THIS. REMOVING THIS, REMOVING BACKLINKS, WILL RESULT IN A DMCA TAKEDOWN AS IT IS A BREACH OF OUR LICENSE (AGPL v3)
$ftter = '<br /><small><strong><a href="https://discord.gg/NeRrWZC" target="_BLANK">Powered by Hydrid</a></strong></small><br />
<small>Version: '.$version;

// Version Check/Control
$data_vc = file_get_contents("https://pastebin.com/raw/d63r81DF");

if ($data_vc > $version) {
  define('isOutdated', true);
} else {
  define('isOutdated', false);
}

//pdo check
if (!class_exists('PDO')) {
  die("Sorry, Hydrid can not be used without PDO being enabled. If you're running on a local machine, It should already be enabled. If you are running off a hosting provider, Please contact them for further assistance.");
}

//php version check
if (floatval(phpversion()) < 5.4) {
  die("Your PHP Version is not supported. Please update to continue using Hydrid.");
}
