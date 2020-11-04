<?php
use yeticave\Database;

require 'vendor/autoload.php';
require_once 'src/config.php';

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
