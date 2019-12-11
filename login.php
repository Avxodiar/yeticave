<?php
require_once 'src/config.php';
require_once 'src/functions.php';

$email = '';
$errors = [];
$requiredFields = ['email', 'password'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    foreach ($requiredFields as $field) {
        if (empty($field)) {
            $errors[$field] = 'Поле не заполнено';
        }
    }

    if (!count($errors)) {

        if(!user\checkEmail($_POST['email'])) {
            $errors['email'] = 'Указан не корректный E-mail';
        } else {
            $arUser = user\searchUser($_POST['email']);
            if(!empty($arUser) && user\checkPassword($_POST['password'], $arUser['password'])) {
                user\auth($arUser['id']);
            }
            $errors['password'] = 'Введите пароль';
            $errors['form'] = 'Указан неверный логин или пароль';
        }
    }
    $email = $_POST['email'];
    unset($arRes);
}

if(user\isAuth()) {
    $content = getTemplate(
        'welcome.php',
        [
            'message' => 'Добро пожаловать, ' . user\getName() . '!',
            'button' => 'Выйти',
            'buttonUrl' => '/logout.php'
        ]
    );
} else {
    $content = getTemplate(
        'login.php',
        [
            'email' => $email,
            'errors' => $errors
        ]
    );
}

includeTemplate('Авторизация', $content);
