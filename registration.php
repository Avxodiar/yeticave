<?php
require_once 'src/config.php';
require_once 'src/functions.php';

if(user\isAuth()) {
    header('Location: /profile.php');
    exit();
}

$requiredFields = ['email', 'password', 'name'];
$arRes = array_fill_keys($requiredFields, '');
$errors = [];

$content = getTemplate(
    'registration.php',
    [
        'arRes' => $arRes,
        'errors' => $errors
    ]
);

includeTemplate('Регистрация', $content);
