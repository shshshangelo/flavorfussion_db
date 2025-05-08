<?php
// index.php

// Get the requested URL
$url = isset($_GET['url']) ? $_GET['url'] : '/';

<link rel="icon" href="images/favicon.ico" type="image/x-icon">


// Define your routes
switch ($url) {
    case '/':
        include 'admin_login.php';
        break;
    // Add more routes as needed
    default:
        include '404.php';
        break;
}
