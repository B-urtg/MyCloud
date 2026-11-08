<?php
declare(strict_types=1);
ini_set('display_errors', '1');
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['user_home'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['user'];
$avatar = $_SESSION['avatar'];
require_once 'navigation_cloud.php';
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
            <?php include 'header.php';?>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
            <div class="cloud-panel">
                <?php foreach ($files as $file):
                    $is_dir = is_dir($current_dir . $file);
                    $next_dir = ($sub_dir !== '' ? $sub_dir . '/' : '') . $file;
                ?>
                <a class="element" href="user_panel.php?dir=<?php echo urlencode($next_dir); ?>">
                    <?php if ($is_dir): ?>
                        <div class="content-box">
                            <?php echo htmlspecialchars($file); ?>
                        </div>    
                    <?php else: ?>
                        <div class="content-box">
                            <?php echo htmlspecialchars($file); ?>
                        </div>
                    <?php endif; ?>
                </a>
                <?php endforeach; ?>
            </div>
            <div class="new-elements">
                <label for="popup-file" class="element">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="file-plus-icon">
                        <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                        <polyline points="13 2 13 9 20 9"></polyline>
                        <line x1="12" y1="12" x2="12" y2="18"></line>
                        <line x1="9" y1="15" x2="15" y2="15"></line>
                    </svg>
                    </label>
                <?php if (empty($sub_dir)): ?>
                    <label for="popup-folder" class="element">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="folder-icon">
                            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                            <line x1="12" y1="11" x2="12" y2="17"></line>
                            <line x1="9" y1="14" x2="15" y2="14"></line>
                        </svg>
                </label>
                <?php endif; ?>
                <input type="checkbox" id="popup-folder" class="popup-checkbox">
                <div class="popup-overlay" id="overlay-folder">
                    <div class="popup-box">
                        <h3>Utwórz nowy folder</h3>
                        <form action="create_dir.php" method="POST">
                            <input type="hidden" name="current_dir" value="<?php echo htmlspecialchars($sub_dir); ?>">
                            <input type="text" name="dir_name" placeholder="Nazwa folderu..." required class="inputbox" autocomplete="off">
                            <div class="popup-buttons">
                                <label for="popup-folder" class="button-popup">Anuluj</label>
                                <button type="submit" class="button-popup">Utwórz</button>
                            </div>
                        </form>
                    </div>
                </div>
                <input type="checkbox" id="popup-file" class="popup-checkbox">
                <div class="popup-overlay" id="overlay-file">
                    <div class="popup-box">
                        <h3>Dodaj nowy plik</h3>
                        <form action="upload_file.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="current_dir" value="<?php echo htmlspecialchars($sub_dir); ?>">
                            <input type="file" name="file_to_upload" required class="inputbox">
                            <div class="popup-buttons">
                                <label for="popup-file" class="button-popup">Anuluj</label>
                                <button type="submit" class="button-popup">Wyślij</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>