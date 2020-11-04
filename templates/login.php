<?php
if(!empty($message)) {?>
    <h4 style="margin: 40px auto -10px;"><?=$message?></h4>
<?php } ?>

<form class="form container <?=$hasValidError ? 'form--invalid' : ''?>" method="post">
    <h2>Вход</h2>
    <div class="form__item <?=$hasFieldValidError['email'] ? 'form__item--invalid' : ''?>">
        <label for="email">E-mail*</label>
        <input id="email" type="text" name="email" placeholder="Введите e-mail" required value="<?=$email?>">
        <span class="form__error"><?=$errors['email']?></span>
    </div>
    <div class="form__item form__item--last <?=$hasFieldValidError['password'] ? 'form__item--invalid' : ''?>">
        <label for="password">Пароль*</label>
        <input id="password" type="password" name="password" placeholder="Введите пароль" required>
        <span class="form__error"><?=$errors['password']?></span>
    </div>
    <span class="form__error <?=$hasFieldValidError['form'] ? 'form__error--bottom' : ''?>"><?=$errors['form']?></span>
    <button type="submit" class="button">Войти</button>
</form>
