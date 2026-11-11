<?php
declare(strict_types=1);
ini_set('display_errors', '1');
error_reporting(E_ALL);
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
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
        <script>
            window.addEventListener('pageshow', function(event) {
                if (event.persisted) {
                    window.location.reload();
                }
            });
        </script>
    </head>
    <body>
        <div class="grid">
            <?php include 'header.php';?>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
            <div class="cloud-panel">
                <?php foreach ($files as $file):
                    if ($file === '.' || $file === '..' || $file === '.thumbs' || str_starts_with($file, '.')) {
                        continue;
                    }
                    $is_dir = is_dir($current_dir . $file);
                    $next_dir = ($sub_dir !== '' ? $sub_dir . '/' : '') . $file;
                    $file_url = "download_cloud.php?file=" . urlencode($next_dir);
                ?>
                <div class="element-panel <?php echo (!$is_dir && (in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']) || in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['mp4', 'webm', 'ogg']))) ? 'media-element-panel' : ''; ?>">
                    <?php if ($is_dir): ?>
                        <a class="element" href="user_panel.php?dir=<?php echo urlencode($next_dir); ?>">
                            <div class="content-box">
                                <?php echo htmlspecialchars($file); ?>
                            </div>
                        </a>
                    <?php else: 
                        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                        $is_image = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
                        $is_audio = in_array($ext, ['mp3', 'wav', 'ogg', 'm4a']);
                        $is_video = in_array($ext, ['mp4', 'webm', 'ogg']);
                    ?>
                        <?php if ($is_image): ?>
                            <?php
                                $original_path = $current_dir . $file;
                                $thumbs_dir = $current_dir . '.thumbs';
                                if (!is_dir($thumbs_dir)) {
                                    @mkdir($thumbs_dir, 0755, true);
                                }
                                $thumb_filename = md5($file) . '_' . $file;
                                $thumb_path = $thumbs_dir . '/' . $thumb_filename;
                                $thumb_web_url = "download_cloud.php?file=" . urlencode(($sub_dir !== '' ? $sub_dir . '/' : '') . '.thumbs/' . $thumb_filename);
                                if (!file_exists($thumb_path) || filemtime($original_path) > filemtime($thumb_path)) {
                                    $img_info = @getimagesize($original_path);
                                    if ($img_info !== false) {
                                        $width = $img_info[0];
                                        $height = $img_info[1];
                                        $mime = $img_info['mime'];
                                        $src_img = null;
                                        if ($mime == 'image/jpeg') $src_img = @imagecreatefromjpeg($original_path);
                                        elseif ($mime == 'image/png') $src_img = @imagecreatefrompng($original_path);
                                        elseif ($mime == 'image/gif') $src_img = @imagecreatefromgif($original_path);
                                        elseif ($mime == 'image/webp') $src_img = @imagecreatefromwebp($original_path);
                                        if ($src_img) {
                                            $thumb_size = 150;
                                            $src_aspect = $width / $height;
                                            $thumb_img = imagecreatetruecolor($thumb_size, $thumb_size);
                                            if ($mime == 'image/png' || $mime == 'image/gif' || $mime == 'image/webp') {
                                                imagecolortransparent($thumb_img, imagecolorallocatealpha($thumb_img, 0, 0, 0, 127));
                                                imagealphablending($thumb_img, false);
                                                imagesavealpha($thumb_img, true);
                                            }
                                            if ($src_aspect > 1) {
                                                $new_w = (int)($height * $src_aspect);
                                                $new_h = $height;
                                                $src_x = (int) (($width - $new_h) / 2);
                                                $src_y = 0;
                                                $src_w = $new_h;
                                                $src_h = $height;
                                            } else {
                                                $new_w = $width;
                                                $new_h = (int)($width / $src_aspect);
                                                $src_x = 0;
                                                $src_y = (int) (($height - $new_w) / 2);
                                                $src_w = $width;
                                                $src_h = $width;
                                            }
                                            imagecopyresampled($thumb_img, $src_img, 0, 0, $src_x, $src_y, $thumb_size, $thumb_size, $src_w, $src_h);
                                            if ($mime == 'image/jpeg') imagejpeg($thumb_img, $thumb_path, 80);
                                            elseif ($mime == 'image/png') imagepng($thumb_img, $thumb_path, 6);
                                            elseif ($mime == 'image/gif') imagegif($thumb_img, $thumb_path);
                                            elseif ($mime == 'image/webp') imagewebp($thumb_img, $thumb_path, 80);
                                            imagedestroy($src_img);
                                            imagedestroy($thumb_img);
                                        }
                                    }
                                }
                            ?>
                            <div class="element media-tile" onclick="openImageModal('<?php echo $file_url; ?>', '<?php echo htmlspecialchars($file); ?>')" title="Kliknij, aby powiększyć">
                                <div class="content-box">
                                    <?php echo htmlspecialchars($file); ?>
                                </div>
                                <img src="<?php echo file_exists($thumb_path) ? $thumb_web_url : $file_url; ?>" alt="" class="file-thumb" loading="lazy">
                            </div>

                        <?php elseif ($is_audio): ?>
                            <div class="element audio-container" onclick="event.stopPropagation();">
                                <div class="content-box"><?php echo htmlspecialchars($file); ?></div>
                                <audio controls preload="none" style="width: 100%; height: 30px; margin-top: 5px;">
                                    <source src="<?php echo $file_url; ?>">
                                    Twoja przeglądarka nie wspiera odtwarzacza audio.
                                </audio>
                            </div>

                        <?php elseif ($is_video): ?>
                            <div class="element media-tile" onclick="openVideoModal('<?php echo $file_url; ?>', '<?php echo htmlspecialchars($file); ?>')" title="Kliknij, aby odtworzyć wideo">
                                <div class="content-box">
                                    <?php echo htmlspecialchars($file); ?>
                                </div>
                                <video class="file-thumb" preload="metadata" muted>
                                    <source src="<?php echo $file_url; ?>">
                                </video>
                            </div>

                        <?php else: ?>>
                            <a class="element" href="<?php echo $file_url; ?>">
                                <div class="content-box">
                                    <?php echo htmlspecialchars($file); ?>
                                </div>
                            </a>
                        <?php endif; ?>
                        <?php if ($is_audio || $is_image || $is_video): ?>
                        <a href="<?php echo $file_url; ?>" class="download-btn" title="Pobierz" onclick="event.stopPropagation();">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="7 10 12 15 17 10"></polyline>
                                <line x1="12" y1="15" x2="12" y2="3"></line>
                            </svg>
                        </a>
                        <?php endif; ?>
                    <?php endif; ?>
                    <label for="popup-delete-<?php echo md5($next_dir); ?>" class="del-btn" title="Usuń" onclick="event.stopPropagation();">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="trash-icon">
                            <path d="M3 6h18"></path>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            <line x1="10" y1="11" x2="10" y2="17"></line>
                            <line x1="14" y1="11" x2="14" y2="17"></line>
                        </svg>
                    </label>
                    <input type="checkbox" id="popup-delete-<?php echo md5($next_dir); ?>" class="popup-checkbox">
                    <div class="popup-overlay" id="overlay-delete-<?php echo md5($next_dir); ?>">
                        <div class="popup-box">
                            <h3>Usuwanie elementu</h3>
                            <p>Czy na pewno chcesz usunąć: <strong><?php echo htmlspecialchars($next_dir); ?></strong>?</p>
                            <form action="delete_cloud.php" method="POST">
                                <input type="hidden" name="current_dir" value="<?php echo htmlspecialchars($sub_dir); ?>">
                                <input type="hidden" name="file_to_delete" value="<?php echo htmlspecialchars($next_dir); ?>">
                                <div class="popup-buttons">
                                    <button type="submit" class="button-popup">Usuń</button>
                                    <label for="popup-delete-<?php echo md5($next_dir); ?>" class="button-popup" style="display:inline-block; text-align:center; line-height:normal; cursor:pointer;">Anuluj</label>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
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
                        <form action="create_dir_cloud.php" method="POST">
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
                        <form action="upload_cloud.php" method="POST" enctype="multipart/form-data">
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
        <div id="mediaModal" class="media-modal" onclick="closeMediaModal()">
            <div class="media-modal-content" onclick="event.stopPropagation()">
                <span class="media-close" onclick="closeMediaModal()">&times;</span>
                <div id="modalBody"></div>
            </div>
        </div>
        <script>
            function openImageModal(url, title) {
                const modal = document.getElementById('mediaModal');
                const body = document.getElementById('modalBody');
                body.innerHTML = `<img src="${url}" alt="${title}" style="max-width:100%; max-height:80vh; border-radius:8px;"><p style="text-align:center; margin-top:10px; color:#c4a9a9;">${title}</p>`;
                modal.style.display = 'flex';
            }

            function openVideoModal(url, title) {
                const modal = document.getElementById('mediaModal');
                const body = document.getElementById('modalBody');
                body.innerHTML = `<video controls style="max-width:100%; max-height:80vh; border-radius:8px;"><source src="${url}"></video><p style="text-align:center; margin-top:10px; color:#c4a9a9;">${title}</p>`;
                modal.style.display = 'flex';
            }

            function closeMediaModal() {
                const modal = document.getElementById('mediaModal');
                const body = document.getElementById('modalBody');
                modal.style.display = 'none';
                body.innerHTML = ''; 
            }
        </script>
    </body>
</html>