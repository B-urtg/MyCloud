<?php 
declare(strict_types=1); 
$sub_dir = $sub_dir ?? ($_GET['dir'] ?? '');
$sub_dir = trim($sub_dir, '/\\');
$segments = $sub_dir !== '' ? explode('/', $sub_dir) : [];
$accumulated_path = '';
?>
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="header_style.css">
</head>
<nav class="navbar">
    <u1 aria-label="breadcrumb-nav">
        <ol class="breadcrumb">
            <?php if (empty($segments)): ?>
                <li class="breadcrumb-item active" aria-current="page">MyCloud</li>
            <?php else: ?>
                <li class="breadcrumb-item"><a href="user_panel_test.php" class="breadcrumb-text">MyCloud</a></li>
            <?php endif; ?>
            <?php foreach ($segments as $index => $segment): 
                if ($accumulated_path === '') {
                    $accumulated_path = $segment;
                } else {
                    $accumulated_path .= '/' . $segment;
                }
                $is_last = ($index === count($segments) - 1);
            ?>
            <?php if ($is_last): ?>
                <li class="breadcrumb-item active" aria-current="page">
                    <?php echo htmlspecialchars($segment); ?>
                </li>
            <?php else: ?>
                <li class="breadcrumb-item">
                    <a href="user_panel_test.php?dir=<?php echo urlencode($accumulated_path); ?>" class="breadcrumb-text">
                        <?php echo htmlspecialchars($segment); ?>
                    </a>
                </li>
            <?php endif; ?>
            <?php endforeach; ?>
        </ol>
    </u1>
    <div class="user-panel">
        <div class="profile">
            <img src="<?php echo htmlspecialchars($_SESSION['avatar']); ?>" class="avatar">
        </div>
    </div>
</nav>