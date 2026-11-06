<?php
declare(strict_types=1);
ini_set('display_errors', '1');
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['user_home'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['user'];
$avatar = $_SESSION['avatar'];
$user_home = $_SESSION['user_home'];

// Automatyczne utworzenie katalogu użytkownika, jeśli nie istnieje
if (!is_dir($user_home)) {
    mkdir($user_home, 0755, true);
    chown($user_home, 'www-data');
    chgrp($user_home, 'www-data');
}

$message = '';

// Pobieramy podkatalog z żądania GET
$sub_dir = $_GET['dir'] ?? '';
$sub_dir = trim($sub_dir, '/\\');

// Pełna ścieżka docelowa na dysku
$target_dir = realpath($user_home . $sub_dir);
$real_user_home = realpath($user_home);

// Zabezpieczenie przed Directory Traversal
if ($target_dir === false || strpos($target_dir, $real_user_home) !== 0 || !is_dir($target_dir)) {
    $current_dir = $real_user_home . '/';
    $sub_dir = '';
} else {
    $current_dir = $target_dir . '/';
}

// Ścieżka względna do wyświetlenia w nagłówku
$display_path = '/' . trim(str_replace($real_user_home, '', $current_dir), '/');
if ($display_path !== '/') {
    $display_path .= '/';
}

// TERAZ pobieramy pliki z AKTUALNEGO katalogu (a nie zawsze z głównego!)
$files = array_diff(scandir($current_dir), array('..', '.'));
?>
<!DOCTYPE html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Zawada MyCloud <?php echo htmlspecialchars($_SESSION['user']);?></title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
        <?php include 'header.php';?>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <div class="cloud-panel">
            Cloud panel
        </div>
    </body>
</html>