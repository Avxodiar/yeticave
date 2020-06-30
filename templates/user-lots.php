  <section class="rates container">
    <h2>
      <a href="/profile.php">Профиль</a>&nbsp;<span>&raquo;</span>&nbsp;Мои лоты
    </h2>
    <?php if(!empty($error)):?>
      <p class="lot__title">
          У вас нет ставок
      </p>
    <?php else:?>
    <table class="rates__list">
      <?php foreach ($lots as $lot)
      {
        $trClass = '';
        switch ($lot['status']) {
          case 'win':
            $trClass = 'rates__item--win';
            break;
          case 'end':
            $trClass = 'rates__item--end';
            break;
        }
        ?>
      <tr class="rates__item <?=$trClass?>" id='<?=$lot['id']?>'>
        <td class="rates__info">
          <div class="rates__img">
            <img src="<?=$lot['image_url']?>" width="54" height="40" alt="<?=$lot['name']?>">
          </div>
          <h3 class="rates__title"><a href="/lot.php?id=<?=$lot['id']?>"><?=$lot['name']?></a></h3>
        </td>
        <td class="rates__category">
            <?=$lot['cat_name']?>
        </td>
        <td class="rates__category" aria-label="Стартовая цена / Шаг ставки">
            <?=$lot['price_start']?> p / <?=$lot['price_step']?> p
        </td>
        <td class="rates__timer">
          <?php
          if ($lot['status']) {
            echo '<div class="timer timer--end">Торги окончены</div>';
          } else {
            echo '<div class="timer timer--finishing">' . $lot['tsFinish'] . '</div>';
          } ?>
        </td>
        <td class="rates__price <?=(!$lot['price_rate']) ? 'rates__category' : ''?>">
            <?=$lot['max_bet']?>
        </td>
      </tr>
      <?php
      }
      ?>
    </table>
    <?php endif;?>
  </section>
