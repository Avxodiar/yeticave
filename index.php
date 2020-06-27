<?php
require_once 'src/init.php';

$content = getTemplate('index.php', ['lots' => checkLots($lots)]);

includeTemplate('Главная', $content);
