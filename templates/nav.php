<nav class="nav">
    <ul class="nav__list container">
        <?php
        foreach ($categories as $categoryId => $categoryName): ?>
            <li class="nav__item">
                <a href="category.php?id=<?=$categoryId?>"><?=$categoryName?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
