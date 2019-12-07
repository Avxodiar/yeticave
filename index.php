<?php
require_once 'src/config.php';
require_once 'src/functions.php';
require_once 'src/data.php';

$indexContent = getTemplate('index.php', ['lots' => checkLots($lots)]);

includeTemplate('Главная', $indexContent);
