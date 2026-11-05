<?php
declare(strict_types=1);
ini_set('display_errors', '1');
error_reporting(E_ALL);

session_start();

// 1. Sprawdzenie, czy użytkownik jest zalogowany i ma przypisany katalog domowy
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['user_home'])) {
    header("Location: index.php");
    exit();
}

$user_home = $_SESSION['user_home'];
$username = $_SESSION['user'];
$avatar = $_SESSION['avatar'] ?? 'Avatary/user-default.jpg';

// 2. Automatyczne utworzenie katalogu użytkownika, jeśli fizycznie jeszcze nie istnieje na dysku
if (!is_dir($user_home)) {
    mkdir($user_home, 0755, true);
    chown($user_home, 'www-data');
    chgrp($user_home, 'www-data');
}

$message = '';
$files = array_diff(scandir($user_home), array('..', '.'));
// Pobieramy podkatalog z żądania GET (np. panel.php?dir=zdjęcia/wakacje)
$sub_dir = $_GET['dir'] ?? '';

// Bezpieczne oczyszczenie ścieżki (usuwamy niebezpieczne znaki)
$sub_dir = trim($sub_dir, '/\\');

// Pełna ścieżka docelowa na dysku
$target_dir = realpath($user_home . $sub_dir);
$real_user_home = realpath($user_home);

// Zabezpieczenie przed Directory Traversal (użytkownik nie może wyjść poza swój user_home)
if ($target_dir === false || strpos($target_dir, $real_user_home) !== 0 || !is_dir($target_dir)) {
    $current_dir = $real_user_home . '/';
    $sub_dir = '';
} else {
    $current_dir = $target_dir . '/';
}

// Ścieżka względna do wyświetlenia w nagłówku (np. / lub /zdjęcia/wakacje/)
$display_path = '/' . trim(str_replace($real_user_home, '', $current_dir), '/');
if ($display_path !== '/') {
    $display_path .= '/';
}
?>
<!DOCTYPE html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Zawada MyCloud <?php echo htmlspecialchars($_SESSION['user']);?></title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
        <div class="grid">
            <div class="header">
                <h1>Moja Chmura</h1>
                <!-- Wyświetlanie obecnej ścieżki -->
                <div class="current-path">
                    📂 Lokalizacja: <strong><?php echo htmlspecialchars($display_path); ?></strong>
                    
                    <?php if ($sub_dir !== ''): ?>
                        <!-- Przycisk powrotu do katalogu wyżej -->
                        <?php 
                            $parent_dir = dirname($sub_dir);
                            $parent_link = ($parent_dir === '.' || $parent_dir === '') ? 'panel.php' : 'panel.php?url=' . urlencode($parent_dir); // uwaga: zmień na &dir= w zależności od parametru
                        ?>
                        <a href="panel.php?dir=<?php echo urlencode(dirname($sub_dir) === '.' ? '' : dirname($sub_dir)); ?>" class="btn-secondary" style="padding: 2px 6px; font-size: 12px; margin-left: 10px;">⬅ W górę</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="cloud-panel">
            </div>
        </div>
    </body>
</html>