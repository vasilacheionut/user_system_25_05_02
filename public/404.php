<?php
include_once __DIR__ . '/../app/Core/SessionHelper.php';
http_response_code(404);
$title = 'Pagina negăsită';

ob_start();
?>
<h2>404 - Pagina nu a fost găsită</h2>
<p>Pagina pe care o cauți nu există sau a fost mutată.</p>
<?php
$content = ob_get_clean();

// Include template-ul cu totul
include __DIR__ . '/template.php';
