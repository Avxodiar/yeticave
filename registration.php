<?php
require_once 'src/config.php';
require_once 'src/functions.php';
require_once 'src/form.php';
require_once 'src/file.php';

use function yeticave\form\validate as formValidate;
use function yeticave\file\upload as fileUpload;

if(user\isAuth()) {
    header('Location: /logout.php');
    exit();
}

$arRes = [];
$errors = [];
$requiredFields = ['email', 'password', 'name', 'message'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$arRes, $errors] = formValidate($_POST, $requiredFields);

    // Загрузка аватарки если указана
    if (!empty($_FILES['avatar']['tmp_name'])) {
        [$avatar, $error] = fileUpload($_FILES['avatar'], AVATARS_UPLOAD_DIR);
        if(!empty($error)) {
            $errors['avatar'] = $error;
        } elseif (!empty($avatar)) {
            $arRes ['avatar'] = $avatar;
        }
    }
} else {
    $arRes = array_fill_keys($requiredFields, '');
    $arRes['avatar'] = '';
}

$content = getTemplate(
    'registration.php',
    [
        'arRes' => $arRes,
        'errors' => $errors
    ]
);

includeTemplate('Регистрация', $content);
