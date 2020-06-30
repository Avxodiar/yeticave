<?php if (SITE_ENABLED) :?>

<nav class="nav">
    <ul class="nav__list container">
        <?php
        $currentCategoryId = (int) ($_GET['id'] ?? 0);
        foreach ($categories as $categoryId => $categoryName)
        {
            $currentCategoryClass = (CATEGORY_PAGE && ($currentCategoryId === $categoryId)) ? 'nav__item--current' : '';
          ?>
            <li class="nav__item <?=$currentCategoryClass?>">
                <a href="category.php?id=<?=$categoryId?>"><?=$categoryName?></a>
            </li>
          <?php
        }
        ?>
    </ul>
</nav>

<?php endif; ?>
