<?php
use yeticave\Database;

require_once 'src/config.php';
require_once 'src/SingletonTrait.php';
require_once 'src/Database.php';
require_once 'src/functions.php';
require_once 'src/user.php';
require_once 'src/lots.php';

if (!SITE_ENABLED) {
    $content = getTemplate(
        'off.php', [
            'message' => 'Сайт на техническом обслуживании',
        ]
    );
    includeTemplate('Главная', $content);
    die;
}

session_start();
