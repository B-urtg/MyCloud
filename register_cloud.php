<?php declare(strict_types=1);?>
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
                    Zarejestruj się
                </div>
                <form method="post" action="add_cloud.php" enctype="multipart/form-data">
                    <div class="textbox">
                        <label class="login-label">Login</label>
                        <input type="text" name="user" class="inputbox" id="input" maxlength="20" size="20">
                    </div>
                    <div class="textbox">
                        <label class="login-label">Hasło</label>
                        <input type="text" name="pass" class="inputbox" id="input" maxlength="20" size="20">
                    </div>
                    <div class="textbox">
                        <label class="login-label">Powtórz hasło</label>
                        <input type="text" name="pass2" class="inputbox" id="input" maxlength="20" size="20">
                    </div>
                    <label class="login-label">Dodaj avatar</label>
                    <input class="inputbox" type="file" class="form-control" id="avatar" name="avatar" accept=".png, .jpg, .jpeg">
                    <button class="button" type="submit">Zarejestruj</button>
                </form>
            </div>
        </main>
    </body>
</html>