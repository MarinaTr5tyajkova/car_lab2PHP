<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();

checkAuth();

include 'includes/header.php';
include 'templates/profile.php';
include 'templates/footer.php';
