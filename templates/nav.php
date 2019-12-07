<nav class="nav">
    <ul class="nav__list container">
        <?php foreach ($categories as $catName): ?>
            <li class="nav__item">
                <a href="all-lots.html"><?=$catName?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
