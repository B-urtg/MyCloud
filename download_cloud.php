<?php
declare(strict_types=1);
ini_set('display_errors', '1');
error_reporting(E_ALL);
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['user_home'])) {
    header("Location: index.php");
    exit();
}
if (isset($_GET['file'])) {
    $user_home = rtrim($_SESSION['user_home'], '/');
    $file_param = trim($_GET['file'], '/\\');
    $target_path = $user_home . '/' . $file_param;
    $real_user_home = realpath($user_home);
    $real_target_path = realpath($target_path);
    if ($real_target_path !== false && strpos($real_target_path, $real_user_home) === 0 && !is_dir($real_target_path) && file_exists($real_target_path)) {
        header('Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($target_path) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($target_path));
        flush();
        readfile($target_path);
        exit();
    }
}
$redirect_url = "user_panel_test.php";
if (!empty($sub_dir)) {
    $redirect_url .= "?dir=" . urlencode($sub_dir);
}
header("Location: " . $redirect_url);
exit();
?>