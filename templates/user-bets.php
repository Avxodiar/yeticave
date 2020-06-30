  <section class="rates container">
    <h2>
      <a href="/profile.php">Профиль</a>&nbsp;<span>&raquo;</span>&nbsp;Мои ставки
    </h2>
    <?php if(!empty($error)):?>
      <p class="lot__title">
          У вас нет ставок
      </p>
    <?php else:?>
    <table class="rates__list">
      <?php foreach ($bets as $lot)
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
      <tr class="rates__item <?=$trClass?>" id='<?=$lot['lot_id']?>'>
        <td class="rates__info">
          <div class="rates__img">
            <img src="<?=$lot['image_url']?>" width="54" height="40" alt="<?=$lot['name']?>">
          </div>
          <h3 class="rates__title"><a href="/lot.php?id=<?=$lot['lot_id']?>"><?=$lot['name']?></a></h3>
        </td>
        <td class="rates__category">
            <?=$lot['cat_name']?>
        </td>
        <td class="rates__timer">
          <?php
          $processClass = $lot['process'] ? 'timer--finishing' : '';
          switch ($lot['status']) {
              case 'win':
                  echo '<div class="timer timer--win">Ставка выиграла</div>';
                  break;
              case 'end':
                  echo '<div class="timer timer--end">Торги окончены</div>';
                  break;
              default:
                  echo '<div class="timer ' . $processClass .'">' . $lot['tsFinish'] . '</div>';
          } ?>
        </td>
        <td class="rates__price">
            <?=$lot['price']?> р
        </td>
        <td class="rates__time">
            <?=$lot['tsInsert']?>
        </td>
      </tr>
      <?php
      }
      ?>
    </table>
    <?php endif;?>
  </section>
