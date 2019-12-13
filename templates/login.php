<form class="form container <?=hasError() ? 'form--invalid' : ''?>" method="post">
    <h2>Вход</h2>
    <div class="form__item <?=checkError('email')?>">
      <label for="email">E-mail*</label>
      <input id="email" type="text" name="email" placeholder="Введите e-mail" required value="<?=$email?>">
      <span class="form__error"><?=$errors['email']?></span>
    </div>
    <div class="form__item form__item--last <?=checkError('password')?>">
      <label for="password">Пароль*</label>
      <input id="password" type="password" name="password" placeholder="Введите пароль" required>
      <span class="form__error"><?=$errors['password']?></span>
    </div>
    <span class="form__error <?=checkError('form') ? 'form__error--bottom' : ''?>"><?=$errors['form']?></span>
    <button type="submit" class="button">Войти</button>
  </form>
