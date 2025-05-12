<?php
require_once '../app/Core/Database.php';
include_once __DIR__ . '/../app/Core/SessionHelper.php';

require_once 'csrf.php';          // Încarci funcțiile csrf
$csrf_token = generate_csrf_token();  // Generezi tokenul (o dată pe sesiune)
//Asta îl pune în $_SESSION['csrf_token'] și îl returnează în $csrf_token.


$pdo = Database::connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    /* 
        Îl verifici la trimiterea formularului    
        Aici compari tokenul primit în POST cu cel stocat în sesiune.
        Dacă nu se potrivesc sau lipsește: oprești tot (die), ca să blochezi o cerere potențial periculoasă.      
        
        🔁 Pe scurt, ai acest flux:
        La prima accesare a register.php ➝ se creează și se stochează tokenul.
        Formularul include tokenul ascuns.
        Când se trimite formularul ➝ se verifică dacă acel token corespunde celui din sesiune.

        🧠 De ce e important?
        Fără acest token, orice site extern ar putea crea un formular cu metoda POST către register.php sau login.php și ar putea abuza de contul tău deschis în alt tab browser.
    */
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        die("CSRF token invalid!");
    }

    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        flash('error', 'Email already registered.');
        header('Location: login.php');
    }

    $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
    $stmt->execute([$email, $password]);

    $user_id = $pdo->lastInsertId();
    $log = $pdo->prepare("INSERT INTO user_logs (user_id, action) VALUES (?, ?)");
    $log->execute([$user_id, 'User registered']);

    flash('success', 'Registration successful.');
    header("Location: login.php");
}


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    include '../app/Views/register_form.php';
    $content = ob_get_clean(); // Capture content
    $title = "Register";
    include 'template.php';
    exit;
}
