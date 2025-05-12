<?php
ob_start();
require_once '../app/Core/Database.php';
include_once __DIR__ . '/../app/Core/SessionHelper.php';

phpinfo();

$content = ob_get_clean(); // Capture content
// Include template-ul cu totul
include __DIR__ . '/template.php';
