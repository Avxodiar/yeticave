<form class="form container <?=$hasValidError ? 'form--invalid' : ''?>" method="post" enctype="multipart/form-data">
    <h2>Регистрация нового аккаунта</h2>
    <div class="form__item <?=$hasFieldValidError['email'] ? 'form__item--invalid' : ''?>">
        <label for="email">E-mail*</label>
        <input id="email" type="text" name="email" placeholder="Введите e-mail" required value="<?=$arRes['email']?>">
        <span class="form__error"><?=$errors['email']?></span>
    </div>
    <div class="form__item <?=$hasFieldValidError['password'] ? 'form__item--invalid' : ''?>">
        <label for="password">Пароль*</label>
        <input id="password" type="text" name="password" placeholder="Введите пароль" required>
        <span class="form__error"><?=$errors['password']?></span>
    </div>
    <div class="form__item <?=$hasFieldValidError['name'] ? 'form__item--invalid' : ''?>">
        <label for="name">Имя*</label>
        <input id="name" type="text" name="name" placeholder="Введите имя" required value="<?=$arRes['name']?>">
        <span class="form__error"><?=$errors['name']?></span>
    </div>
    <div class="form__item <?=$hasFieldValidError['message'] ? 'form__item--invalid' : ''?>">
        <label for="message">Контактные данные*</label>
        <textarea id="message" name="message" placeholder="Напишите как с вами связаться" required><?=$arRes['message']?></textarea>
        <span class="form__error"><?=$errors['message']?></span>
    </div>
    <div class="form__item form__item--file form__item--last">
        <label>Аватар</label>
        <div class="preview">
            <button class="preview__remove" type="button">x</button>
            <div class="preview__img">
                <img src="../img/avatar.jpg" width="113" height="113" alt="Ваш аватар">
            </div>
        </div>
        <div class="form__input-file">
            <input class="visually-hidden" type="file" name="avatar" id="photo2" value="">
            <label for="photo2">
                <span>+ Добавить</span>
            </label>
        </div>
        <span class="form__error <?= !empty($errors['avatar']) ? 'form__error--bottom' : ''?>"><?=$errors['avatar']?></span>
    </div>
    <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
    <button type="submit" class="button">Зарегистрироваться</button>
    <a class="text-link" href="/login.php">Уже есть аккаунт</a>
</form>
