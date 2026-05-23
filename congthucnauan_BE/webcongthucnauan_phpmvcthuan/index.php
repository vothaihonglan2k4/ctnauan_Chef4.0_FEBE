<?php
session_start();
require_once 'app/config/config.php';
require_once 'app/core/App.php';
require_once 'app/core/Controller.php';
require_once 'app/core/Database.php';

// Include Helper Files
require_once 'app/helpers/flash_helper.php';
require_once 'app/helpers/session_helper.php';
require_once 'app/helpers/debug_helper.php';

// Initialize the application
$app = new App();
?>
