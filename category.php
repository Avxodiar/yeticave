<?php
require_once 'src/init.php';

use function yeticave\lot\getCategories;
use function yeticave\lot\getCategoryLots;

$id = (int)$_GET['id'];

$categories = getCategories();
if(!$id || !isset($categories[$id])) {
    errorPage(404);
}

$lots = getCategoryLots($id);

$content = getTemplate('category.php', ['catName' => $categories[$id], 'lots' => $lots]);

includeTemplate($categories[$id], $content);
