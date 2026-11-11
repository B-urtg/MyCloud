<?php
declare(strict_types=1);
ini_set('display_errors', '1');
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['user_home'])) {
    header("Location: index.php");
    exit();
}

$sub_dir = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Odbieramy podfolder, z którego wywołano usunięcie
    if (isset($_POST['current_dir'])) {
        $sub_dir = trim($_POST['current_dir'], '/\\');
    }

    if (isset($_POST['file_to_delete'])) {
        $user_home = rtrim($_SESSION['user_home'], '/');
        $target_name = trim($_POST['file_to_delete']);
        $target_path = $user_home . '/' . $target_name;

        if (file_exists($target_path) && strpos(realpath($target_path), realpath($user_home)) === 0) {
            if (is_dir($target_path)) {
                $items = array_diff(scandir($target_path), ['.', '..']);
                if (empty($items)) {
                    @rmdir($target_path);
                } else {
                    $_SESSION['error_message'] = "Nie można usunąć katalogu, ponieważ nie jest pusty!";
                }
            } else {
                @unlink($target_path);
            }
        }
    }
}

// 2. Budujemy poprawny URL powrotny uwzględniający podfolder
$redirect_url = "user_panel_test.php";
if (!empty($sub_dir)) {
    $redirect_url .= "?dir=" . urlencode($sub_dir);
}

header("Location: " . $redirect_url);
exit();
?>