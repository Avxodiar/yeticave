<?php
require_once 'src/config.php';
require_once 'src/functions.php';
require_once 'src/data.php';

/*
1 Проверить, что форма была отправлена
    если нет то показать пустую форму
2 Получить все данные из формы
3 Проверить их корректность – выполнить валидацию
4 Обработать данные: сохранить в БД, отправить, показать…
5 Показать пользователю результат или отправить на другую страницу
*/
function hasError() {
    global $errors;
    return (count($errors));
}

function checkError($field) {
    global $errors;
    return !empty($errors[$field]) ? 'form__item--invalid' : '';
}

$arRes = [];
$errors = [];

$requiredFields = ['lot-name', 'category', 'message', 'lot-rate', 'lot-step', 'lot-date'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    foreach ($requiredFields as $field) {
        $value = htmlspecialchars($_POST[$field]);
        $arRes[$field] = $value;
        if (empty($value)) {
            $errors[$field] = 'Поле не заполнено';
        } else {
            switch ($field) {
                case 'lot-name':
                    if ($value != $_POST[$field]) {
                        $errors[$field] = 'Некорректное наименование';
                    }
                    break;
                case 'message':
                    if ($value != $_POST[$field]) {
                        $errors[$field] = 'Некорректное описание';
                    }
                    break;
                case 'lot-rate':
                case 'lot-step':
                    if (!filter_var($value, FILTER_VALIDATE_INT)) {
                        $errors[$field] = 'Значение должно быть целым числом';
                    }
                    break;
                case 'lot-date':
                    if(!$lotDate = strtotime($value)){
                        $errors[$field] = 'Неверно указана дата';
                    }
                    break;
                case 'category':
                    if(!in_array($value, $categories)) {
                        $errors[$field] = 'Указан не верный раздел';
                    }
                    break;
            }
        }
    }
    if (isset($_FILES['lot-image'])) {
        $upload = $_FILES['lot-image'];
        $file_name = $upload['name'];
        if ($upload['size'] > 2097152) {
            $errors['lot-image'] = 'Максимальный размер файла: 2Мб';
        } else {
            $fInfo = finfo_open(FILEINFO_MIME_TYPE);
            $file_type = finfo_file($fInfo, $upload['tmp_name']);

            if (strpos($file_type, 'image') === false) {
                $errors['lot-image'] = 'Может быть выбран только изображение!';
            } else {
                $file_url = USER_UPLOAD_DIR . $file_name;
                move_uploaded_file(
                    $upload['tmp_name'],
                    ROOT. USER_UPLOAD_DIR . $file_name
                );
                $arRes['lot-image'] = $file_url;
            }
        }
    }
} else {
    $arRes = array_fill_keys($requiredFields, '');
    $arRes['lot-image'] = '';
}

$indexContent = getTemplate('add.php', ['categories' => $categories, 'arRes' => $arRes, 'errors' => $errors]);

includeTemplate('Добавление лота', $indexContent);
