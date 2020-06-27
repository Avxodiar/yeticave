<?php
require_once 'src/init.php';

use function yeticave\lot\getNewLots;

// Показываются 9 новых лотов с не истекшим сроком публикации
$content = getTemplate('index.php', ['lots' => getNewLots()]);

includeTemplate('Главная', $content);
