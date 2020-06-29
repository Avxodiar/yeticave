<div class="content_main-col lot-item">
    <header class="content__header content__header--left-pad">
        <h2 class="header_content-text">Профиль пользователя</h2>
    </header>

    <section class="profile">
        <div class="avatar">
            <img src="<?=$avatar?>" width="200" height="200">
        </div>
        <div class="info">
            <h3><?=$name?></h3>
            <p><a href="/profile_lots.php">Мои лоты (<?=$lotCount?>)</a></p>
            <p><a href="/profile_bets.php">Мои ставки (<?=$betCount?>)</a></p>
            <p><a href="/history.php">История просмотров</a></p>
        </div>
    </section>

</div>
