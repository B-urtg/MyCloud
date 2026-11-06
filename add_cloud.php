<?php
declare(strict_types=1);
require_once 'config.php';
session_start();

// Logika rejestracji uruchamiana po wysłaniu formularza metodą POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['user'] ?? '';
    $pass = $_POST['pass'] ?? '';
    $pass2 = $_POST['pass2'] ?? '';

    // Proste sprawdzenie, czy hasła się zgadzają
    if ($pass !== $pass2) {
        $error = "Hasła nie są identyczne!";
    } else {
        $link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($link) {
            mysqli_query($link, "SET NAMES 'utf8'");

            $user_safe = mysqli_real_escape_string($link, $user);
            
            // Sprawdzamy czy użytkownik już istnieje
            $check = mysqli_query($link, "SELECT * FROM users WHERE username='$user_safe'");
            if (mysqli_num_rows($check) > 0) {
                $error = "Taki użytkownik już istnieje!";
            } else {
                $avatarPath = "Avatars/user-default.jpg"; // Domyślny awatar

                if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                    $fileTmpPath = $_FILES['avatar']['tmp_name'];
                    $fileName = $_FILES['avatar']['name'];
                    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

                    if (in_array($fileExtension, $allowedExtensions)) {
                        $newFileName = 'avatar_' . uniqid() . '.' . $fileExtension;
                        $uploadFolder = 'Avatars/';

                        if (!is_dir($uploadFolder)) {
                            mkdir($uploadFolder, 0755, true);
                        }

                        $destPath = $uploadFolder . $newFileName;
                        if (move_uploaded_file($fileTmpPath, $destPath)) {
                            $avatarPath = $destPath;
                        }
                    }
                }
                $sql = "INSERT INTO users (username, password, avatar, role) VALUES ('$user_safe', '$pass', '$avatarPath', 'user')";
                
                if (mysqli_query($link, $sql)) {
                    $clean_user_dir_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $user);
                    $user_home_dir = '/mnt/drive/users/' . $clean_user_dir_name . '/';
                    if (!is_dir($user_home_dir)) {
                        // Tworzy katalog wraz z rodzicami, jeśli nie istnieją
                        mkdir($user_home_dir, 0755, true);
                        
                        // Przypisanie uprawnień dla serwera WWW Apache
                        chown($user_home_dir, 'www-data');
                        chgrp($user_home_dir, 'www-data');
                    }
                    header("Location: index.php"); // Przekierowanie po udanej rejestracji
                    exit();
                } else {
                    $error = "Błąd bazy danych: " . mysqli_error($link);
                }
            }
            mysqli_close($link);
        } else {
            $error = "Błąd połączenia z bazą danych.";
        }
    }
}
?>