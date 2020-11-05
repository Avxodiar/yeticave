<?php

require_once 'src/init.php';

// Определение ставок у завершенных лотов и рассылка писем победителям (ТЗ п.9.2)
include_once 'getwinner.php';

use function yeticave\lot\getNewLots;

// Показываются 9 новых лотов с не истекшим сроком публикации
$content = getTemplate('index.php', ['lots' => getNewLots()]);

includeTemplate('Главная', $content);
