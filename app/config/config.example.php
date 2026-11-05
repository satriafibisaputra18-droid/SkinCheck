<?php
/**
 * SkinCheck - Application Configuration Example Template
 * Copy this file to config.php and fill in your actual credentials.
 * DO NOT commit config.php to version control if it contains production secrets.
 */

// Database Parameters
define('DB_HOST', 'localhost');
define('DB_USER', 'your_db_username');
define('DB_PASS', 'your_db_password');
define('DB_NAME', 'skincheck_db');

// Application Path Root
define('APPROOT', dirname(dirname(__FILE__)));

// URL Root (Set your deployment base URL, e.g., 'http://localhost/Skin Check' or 'https://skincheck.com')
define('URLROOT', 'http://localhost/Skin Check');

// Site Brand Name
define('SITENAME', 'SkinCheck');
define('SITE_VERSION', '1.0.0');
