<?php
declare(strict_types=1);
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['user_home'])) {
    header("Location: index.php");
    exit();
}

$user_home = $_SESSION['user_home'];
$real_user_home = realpath($user_home);

// utworzenie katalogu domowego
if ($real_user_home === false || !is_dir($user_home)) {
    if (!is_dir($user_home)) {
        mkdir($user_home, 0755, true);
        if (function_exists('chown')) @chown($user_home, 'www-data');
        if (function_exists('chgrp')) @chgrp($user_home, 'www-data');
    }
    $real_user_home = realpath($user_home);
}

// podkatalog
$sub_dir = $_GET['dir'] ?? '';
$sub_dir = trim($sub_dir, '/\\');

// ścieżka docelowa
$target_dir = realpath($user_home . DIRECTORY_SEPARATOR . $sub_dir);

// directory traversal
if ($target_dir === false || strpos($target_dir, $real_user_home) !== 0 || !is_dir($target_dir)) {
    $current_dir = $real_user_home . DIRECTORY_SEPARATOR;
    $sub_dir = '';
} else {
    $current_dir = $target_dir . DIRECTORY_SEPARATOR;
}

// przejdź wyżej
$parent_dir = '';
if ($sub_dir !== '') {
    $path_segments = explode('/', str_replace('\\', '/', $sub_dir));
    array_pop($path_segments);
    $parent_dir = implode('/', $path_segments);
}

// ścieżka względna do nagłówka
$display_path = '/' . trim(str_replace($real_user_home, '', $current_dir), '/');
if ($display_path !== '/') {
    $display_path .= '/';
}

// pobranie zawartości katalogu
$files = array_diff(scandir($current_dir), array('..', '.'));