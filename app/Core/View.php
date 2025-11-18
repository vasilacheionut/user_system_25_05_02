<?php

function render_view($viewFile, $data = [], $title = 'Pagina') {
    extract($data); // transformă cheia array-ului în variabile

    ob_start();
    include __DIR__ . '/../Views/' . $viewFile;
    $content = ob_get_clean();

    include __DIR__ . '/../Views/template.php';
}
