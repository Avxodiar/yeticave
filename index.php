<?php
require_once 'src/config.php';
require_once 'src/functions.php';

$content = getTemplate('index.php', ['lots' => checkLots($lots)]);

includeTemplate('Главная', $content);
