<?php
// router.php

// Calea completă a fișierului cerut
$requested = __DIR__ . '/' . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Dacă fișierul există, îl servim direct

if (file_exists($requested)) {
    return false;
}

// Altfel, redirecționăm la 404.php
http_response_code(404);
require __DIR__ . '/404.php';
