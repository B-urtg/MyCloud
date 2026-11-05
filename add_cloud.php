<?php
declare(strict_types=1);
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
        $link = mysqli_connect('localhost', 'root', 'pppp', 'Zawada_cloud');
        if ($link) {
            mysqli_query($link, "SET NAMES 'utf8'");

            $user_safe = mysqli_real_escape_string($link, $user);
            
            // Sprawdzamy czy użytkownik już istnieje
            $check = mysqli_query($link, "SELECT * FROM users WHERE username='$user_safe'");
            if (mysqli_num_rows($check) > 0) {
                $error = "Taki użytkownik już istnieje!";
            } else {
                $avatarPath = "Avatary/user-default.jpg"; // Domyślny awatar

                if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                    $fileTmpPath = $_FILES['avatar']['tmp_name'];
                    $fileName = $_FILES['avatar']['name'];
                    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

                    if (in_array($fileExtension, $allowedExtensions)) {
                        $newFileName = 'avatar_' . uniqid() . '.' . $fileExtension;
                        $uploadFolder = 'Avatary/';

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