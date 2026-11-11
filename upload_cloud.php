<?php
declare(strict_types=1);
ini_set('display_errors', '1');
error_reporting(E_ALL);
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['user_home'])) {
    header("Location: index.php");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_home = rtrim($_SESSION['user_home'], '/');
    $sub_dir = isset($_POST['current_dir']) ? trim($_POST['current_dir'], '/\\') : '';
    $target_dir = $user_home;
    if ($sub_dir !== '') {
        $target_dir .= '/' . $sub_dir;
    }
    $real_user_home = realpath($user_home);
    $real_target_dir = realpath($target_dir);
    if ($real_target_dir !== false && strpos($real_target_dir, $real_user_home) === 0 && is_dir($real_target_dir)) {
        if (isset($_FILES["file_to_upload"]) && $_FILES["file_to_upload"]["error"] === UPLOAD_ERR_OK) {
            $target_file = $real_target_dir . '/' . basename($_FILES["file_to_upload"]["name"]);
            if (move_uploaded_file($_FILES["file_to_upload"]["tmp_name"], $target_file)) {
                $_SESSION['success_message'] = htmlspecialchars(basename($_FILES["file_to_upload"]["name"])) . " został pomyślnie przesłany.";
            } else {
                $_SESSION['error_message'] = "Wystąpił błąd podczas zapisywania pliku na serwerze.";
            }
        } else {
            $_SESSION['error_message'] = "Nie wybrano pliku lub wystąpił błąd przesyłania.";
        }
    } else {
        $_SESSION['error_message'] = "Nieprawidłowy katalog docelowy.";
    }
}
$redirect_url = "user_panel_test.php";
if (!empty($sub_dir)) {
    $redirect_url .= "?dir=" . urlencode($sub_dir);
}
header("Location: " . $redirect_url);
exit();
?>