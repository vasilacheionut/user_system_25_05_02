<?php
//logout.php
session_start();
require_once 'csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validate_csrf_token($_POST['csrf_token'] ?? '')) {
    die('CSRF token invalid or invalid request method.');
}

session_destroy();
header("Location: login.php");
exit;
