<section class="lots">
  <h2>История просмотров</h2>

    <?php
    if(count($history)) {
        foreach ($history as $lotId)
        {
            $lot = $lots[$lotId];
            ?>
            <ul class="lots__list">
                <li class="lots__item lot">
                    <div class="lot__image">
                        <img src="<?= $lot['pict']; ?>" width="350" height="260" alt="<?= $lot['alt']; ?>">
                    </div>
                    <div class="lot__info">
                        <span class="lot__category"><?= $lot['category']; ?></span>
                        <h3 class="lot__title"><a class="text-link"
                                                  href="lot.php?id=<?= $lotId; ?>"><?= $lot['name']; ?></a></h3>
                        <div class="lot__state">
                            <div class="lot__rate">
                                <span class="lot__amount">Стартовая цена</span>
                                <span class="lot__cost"><?= $lot['price']; ?></span>
                            </div>
                            <div class="lot__timer timer">
                                <?= $lot['timer']; ?>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>

            <ul class="pagination-list">
                <li class="pagination-item pagination-item-prev"><a>Назад</a></li>
                <li class="pagination-item pagination-item-active"><a>1</a></li>
                <li class="pagination-item"><a href="#">2</a></li>
                <li class="pagination-item"><a href="#">3</a></li>
                <li class="pagination-item"><a href="#">4</a></li>
                <li class="pagination-item pagination-item-next"><a href="#">Вперед</a></li>
            </ul>
        <?php }
    } else { ?>
        <p>Список просмотров пуст</p>
    <?php
    }
    ?>

</section>
