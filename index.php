<?php declare(strict_types=1);
require_once 'config.php';
session_destroy();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');
$ipaddress = $_SERVER["REMOTE_ADDR"];
if (!isset($_SESSION['visitor_logged']))
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($link) 
    {
        mysqli_query($link, "SET NAMES 'utf8'");
        $sql = "INSERT INTO visitors (ipaddress) VALUES ('$ipaddress')";
        if (mysqli_query($link, $sql))
        {
            $_SESSION['visitor_logged'] = $ipaddress;
        }
        mysqli_close($link);
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Zawada MyCloud</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
        <main class="login">
            <div class="logbox">
                <div class="login-header">
                    <svg class="cloud-icon" xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M17.5 19H9a7 7 0 1 1 6.71-9h1.79a4.5 4.5 0 1 1 0 9Z"></path>
                    </svg>
                </div>
                <div class="login-header">
                    Zaloguj się
                </div>
                <form method="post" action="verify_cloud.php">
                    <div class="textbox">
                        <label class="login-label">Login</label>
                        <input type="text" name="user" class="inputbox" id="input" maxlength="20" size="20">
                    </div>
                    <div class="textbox">
                        <label class="login-label">Hasło</label>
                        <input type="password" name="pass" class="inputbox" id="pass" maxlength="20" size="20">
                    </div>
                    <button class="button" type="submit">Zaloguj</button>
                </form>
                <div style="padding: 15px;">
                    <a class="button" href="register_cloud.php">Rejestracja</a>
                </div>
                <div style="padding: 45px;">
                    <a class="button" href="../index.php">Home</a>
                </div>
            </div>
        </main>
    </body>
</html>