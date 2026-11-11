<?php
declare(strict_types=1);
ini_set('display_errors', '1');
error_reporting(E_ALL);
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['user_home'])) {
    header("Location: index.php");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dir_name'])) {
    $user_home = rtrim($_SESSION['user_home'], '/');
    $sub_dir = isset($_POST['current_dir']) ? trim($_POST['current_dir'], '/\\') : '';
    $dir_name = trim($_POST['dir_name']);
    $target_base = $user_home;
    if ($sub_dir !== '') {
        $target_base .= '/' . $sub_dir;
    }
    $new_dir_path = $target_base . '/' . $dir_name;
    $real_user_home = realpath($user_home);
    $real_target_base = realpath($target_base);
    if ($real_target_base !== false && strpos($real_target_base, $real_user_home) === 0 && is_dir($real_target_base)) {
        if (!file_exists($new_dir_path)) {
            if (mkdir($new_dir_path, 0755, true)) {
                if (function_exists('chown')) @chown($new_dir_path, 'www-data');
                if (function_exists('chgrp')) @chgrp($new_dir_path, 'www-data');
                $_SESSION['success_message'] = "Folder został pomyślnie utworzony.";
            } else {
                $_SESSION['error_message'] = "Nie udało się utworzyć folderu.";
            }
        } else {
            $_SESSION['error_message'] = "Katalog lub plik o takiej nazwie już istnieje.";
        }
    } else {
        $_SESSION['error_message'] = "Nieprawidłowa ścieżka docelowa.";
    }
}
$redirect_url = "user_panel_test.php";
if (!empty($sub_dir)) {
    $redirect_url .= "?dir=" . urlencode($sub_dir);
}
header("Location: " . $redirect_url);
exit();
?>