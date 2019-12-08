<section class="lot-item container">
    <h2><?=$lot['name'];?></h2>
    <div class="lot-item__content">
        <div class="lot-item__left">
            <div class="lot-item__image">
                <img src="<?=$lot['pict'];?>" width="730" height="540" alt="<?=$lot['alt'];?>">
            </div>
            <p class="lot-item__category">Категория: <span><?=$lot['category'];?></span></p>
            <p class="lot-item__description"><?=$lot['description'];?></p>
        </div>
        <div class="lot-item__right">
            <div class="lot-item__state">
                <div class="lot-item__timer timer">
                    <?=$lot['timer'];?>
                </div>
                <div class="lot-item__cost-state">
                    <div class="lot-item__rate">
                        <span class="lot-item__amount">Текущая цена</span>
                        <span class="lot-item__cost"><?=$lot['price'];?></span>
                    </div>
                    <div class="lot-item__min-cost">
                        Мин. ставка <span><?=$lot['minPrice'];?></span>
                    </div>
                </div>
                <?php if(user\isAuth()): ?>
                <form class="lot-item__form" action="https://echo.htmlacademy.ru" method="post">
                    <p class="lot-item__form-item">
                        <label for="cost">Ваша ставка</label>
                        <input id="cost" type="number" name="cost" placeholder="<?=$lot['minPrice'];?>">
                    </p>
                    <button type="submit" class="button">Сделать ставку</button>
                </form>
                <?php endif;?>
            </div>
            <?php if(count($bets)): ?>
            <div class="history">
                <h3>История ставок (<span><?=count($bets);?></span>)</h3>
                <table class="history__list">
                    <?php foreach ($bets as $item): ?>
                        <tr class="history__item">
                            <td class="history__name"><?=$item['name']?></td>
                            <td class="history__price"><?=$item['price']?></td>
                            <td class="history__time"><?=$item['ts']?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <?php endif;?>
        </div>
    </div>
</section>
