<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Handle language change
if (isset($_GET['lang'])) {
    $allowed_langs = ['en', 'es', 'nl', 'eu'];
    $new_lang = $_GET['lang'];

    if (in_array($new_lang, $allowed_langs)) {
        $_SESSION['lang'] = $new_lang;
        header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
        exit();
    }
}

// Set default language if not set
$lang = $_SESSION['lang'] ?? 'en';

// Define flag images
$flags = [
    'en' => 'https://flagcdn.com/w20/gb.png',
    'es' => 'https://flagcdn.com/w20/es.png',
    'nl' => 'https://flagcdn.com/w20/nl.png',
    'eu' => 'assets/images/basque.png'
];

$currentFlag = $flags[$lang] ?? $flags['en'];

// Include translation file
$translations = include("translations/{$lang}.php");

// Now include your header
require_once 'header.php';
?>

<nav class="navbar sticky-top navbar-expand-lg" style="background-color: #66735A">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mx-auto nav-fill w-100">
            <li class="nav-item">
                <a class=" nav-link" href="about-us.php"><?= $translations['about_us'] ?? 'About Us' ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php"><?= $translations['home'] ?? 'Home' ?></a>
            </li>
            <?php if (!isset($_SESSION['user'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="login.php"><?= $translations['login'] ?? 'Login' ?></a>
                </li>
            <?php endif; ?>

            <?php if (isset($_SESSION['user'])): ?>
                <li class="nav-item dropdown">
                    <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="profile.php"><i class="fas fa-sliders-h fa-fw"></i> <?= $translations['profile'] ?? 'Profile' ?></a></li>
                        <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cog fa-fw"></i> <?= $translations['settings'] ?? 'Settings' ?></a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt fa-fw"></i> <?= $translations['logout'] ?? 'Logout' ?></a></li>
                    </ul>
                </li>
            <?php endif; ?>

            <li class="nav-item dropdown">
                <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <img src="<?= htmlspecialchars($currentFlag) ?>" width="20" height="15" class="me-1" alt="<?= strtoupper($lang) ?> Flag">
                    <?= strtoupper($lang) ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="?lang=en"><img src="https://flagcdn.com/w20/gb.png" width="20" height="15" class="me-1" alt="English Flag"> English</a></li>
                    <li><a class="dropdown-item" href="?lang=es"><img src="https://flagcdn.com/w20/es.png" width="20" height="15" class="me-1" alt="Spanish Flag"> Español</a></li>
                    <li><a class="dropdown-item" href="?lang=nl"><img src="https://flagcdn.com/w20/nl.png" width="20" height="15" class="me-1" alt="Dutch Flag"> Nederlands</a></li>
                    <li><a class="dropdown-item" href="?lang=eu"><img src="assets/images/basque.png" width="20" height="15" class="me-1" alt="Basque Flag"> Basque</a></li>
                </ul>
            </li>
        </ul>
    </div>
</nav>