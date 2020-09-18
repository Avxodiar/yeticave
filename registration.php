<?php
require_once 'src/init.php';
require_once 'src/form.php';
require_once 'src/file.php';

use function yeticave\form\validate as formValidate;
use function yeticave\file\upload as fileUpload;
use function yeticave\user\{isAuth as isUserAuth, add as addUser};

if(isUserAuth()) {
    header('Location: /logout.php');
    exit();
}

$arRes = [];
$errors = [];
$requiredFields = ['email', 'password', 'name', 'message'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'src/database.php';
    [$arRes, $errors] = formValidate($_POST, $requiredFields);

    if(empty($errors)) {
        // Загрузка аватарки если указана
        if (!empty($_FILES['avatar']['tmp_name'])) {
            [$avatar, $error] = fileUpload($_FILES['avatar'], AVATARS_UPLOAD_DIR);
            if(!empty($error)) {
                $errors['avatar'] = $error;
            } elseif (!empty($avatar)) {
                $arRes ['avatar_url'] = $avatar;
            }
        }

        $res = addUser($arRes);
        // Если данные были сохранены успешно, то требуется переадресовать пользователя на страницу входа,
        if($res) {
            header('Location: /login.php?registration=success');
        }
        //удаляем загруженный файл c аватаркой если регистрация не завершена
        elseif (!empty($arRes ['avatar_url'])) {
            unlink(AVATARS_UPLOAD_DIR . $arRes ['avatar_url']);
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
