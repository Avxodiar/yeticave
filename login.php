<?php
require_once 'src/init.php';
require_once 'src/form.php';

use function yeticave\form\isValidMail;
use function yeticave\user\{auth, isAuth, getName, searchByEmail, checkPassword};

$email = '';
$errors = [];
$requiredFields = ['email', 'password'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    foreach ($requiredFields as $field) {
        $value = htmlspecialchars($_POST[$field]);
        $arRes[$field] = $value;
        if (empty($value)) {
            $errors[$field] = 'Поле не заполнено';
        }
    }

    if (!count($errors)) {

        if(!isValidMail($_POST['email'])) {
            $errors['email'] = 'Указан не корректный E-mail';
        } else {
            $arUser = searchByEmail($_POST['email']);
            if(!empty($arUser) && checkPassword($_POST['password'], $arUser['password'])) {
                auth($arUser['id'], $arUser);
            }
            $errors['password'] = 'Введите пароль';
            $errors['form'] = 'Указан неверный логин или пароль';
        }
    }
    $email = $_POST['email'];
    unset($arRes);
}

if(isAuth()) {
    $content = getTemplate(
        'welcome.php',
        [
            'message' => 'Добро пожаловать, ' . getName() . '!',
            'button' => 'Выйти',
            'buttonUrl' => '/logout.php'
        ]
    );
} else {
    // После регистрации требуется показать сообщение над формой «Теперь вы можете войти, используя свой email и пароль».
    $fromRegistration = (isset($_GET['registration']) && $_GET['registration'] === 'success');
    $content = getTemplate(
        'login.php',
        [
            'email' => $email,
            'errors' => $errors,
            'message' => ($fromRegistration) ? 'Теперь вы можете войти, используя свой email и пароль' : ''
        ]
    );
}

includeTemplate('Авторизация', $content);
