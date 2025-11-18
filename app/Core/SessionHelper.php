<?php
// app/Core/SessionHelper.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


function flash(string $key, string $message): void {
    $_SESSION['flash'][$key] = $message;
}

function get_flash(string $key): ?string {
    if (!empty($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]); // se șterge automat după afișare
        return $msg;
    }
    return null;
}


function display_flash(): void {
    foreach (['success', 'error'] as $type) {
        if ($message = get_flash($type)) {
            $class = $type === 'success' ? 'flash-success' : 'flash-error';
            echo "<div class=\"$class\">" . htmlspecialchars($message) . "</div>";
        }
    }
}
