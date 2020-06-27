<nav class="nav">
    <ul class="nav__list container">
        <?php
        $categories = \yeticave\lot\getCategories();
        foreach ($categories as $categoryId => $categoryName): ?>
            <li class="nav__item">
                <a href="all-lots.html?categoryId=<?=$categoryId?>"><?=$categoryName?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
