<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (isset($_SESSION['user'])) {
    header("Location: home.php");
    exit;
} else {
    header("Location: login.php");
    exit;
}


/* require_once __DIR__ . '/../app/Core/SessionHelper.php';
require_once __DIR__ . '/../app/Core/View.php';

// Exemplu de redare a unei pagini
render_view('login.php', [], 'Login'); */
