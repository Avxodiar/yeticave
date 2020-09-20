<section class="lots">
  <h2 class="profile">
    <a href="/profile.php">Профиль</a>&nbsp;<span>&raquo;</span>&nbsp;История просмотров
  </h2>

    <?php
    if(count($lots)) { ?>
        <ul class="lots__list">
        <?php
        foreach ($lots as $lot)
        {
            ?>
                <li class="lots__item lot">
                    <div class="lot__image">
                        <a class="text-link" href="lot.php?id=<?=$lot['id']?>">
                            <img src="<?= $lot['pict']; ?>" width="350" height="260" alt="<?=$lot['alt']?>">
                        </a>
                    </div>
                    <div class="lot__info">
                        <span class="lot__category"><?= $lot['category']; ?></span>
                        <h3 class="lot__title">
                            <a class="text-link" href="lot.php?id=<?=$lot['id']?>">
                                <?=$lot['name']?>
                            </a>
                        </h3>
                        <div class="lot__state">
                            <div class="lot__rate">
                                <span class="lot__amount">Стартовая цена</span>
                                <span class="lot__cost"><?=$lot['priceFormat']?></span>
                            </div>
                            <div class="lot__timer timer">
                                <?=$lot['timer']; ?>
                            </div>
                        </div>
                    </div>
                </li>
        <?php
        }
        ?>
        </ul>

        <?=$pagination;?>

        <?php
    } else { ?>
        <p>Список просмотров пуст</p>
    <?php
    }
    ?>

</section>
