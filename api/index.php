<?php
/**
 * Vercel Serverless Function Entry Point
 * Routes all incoming requests through SkinCheck's front controller.
 */

// Ensure current working directory is set to root
chdir(__DIR__ . '/..');

// Require main application bootstrap
require_once __DIR__ . '/../public/index.php';
