<?php
namespace yeticave\form;

use function user\searchByEmail;

const ERROR_FIELDS = [
    'email' => 'Введите e-mail',
    'password' => 'Введите пароль',
    'name' => 'Введите имя',
    'address' => 'Напишите как с вами связаться',
    'notEqual' => 'Некорректное описание',
    'wrongEmail' => 'Указан не корректный E-mail',
    'existEmail' => 'Указаный E-mail уже зарегистрирован'
];

/**
 * Проверка е-mail на валидность
 * @param string $email
 * @return mixed
 */
function isValidMail(string $email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Экранирование значений полей из формы.
 * Важно! Поле с типом password не экранируется и возвращается как есть!
 * @param string $value     - значение поля
 * @param string $fieldType - тип поля
 * @return string
 */
function escapeField(string $value, string $fieldType = 'text') {
    return ($fieldType === 'password') ? $value : htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Проверка равенства значений и возврат текста ошибки при различии
 * @param string $value
 * @param string $escape
 * @return mixed|string
 */
function checkEqual(string $value, string $escape) {
    return ($value === $escape) ? '' : ERROR_FIELDS['notEqual'];
}

/**
 * Валидация полей формы на заполнение обязательных полей $fields и валидность данных
 * @param array $post   - данные запроса, например $_POST или $_GET
 * @param array $fields - перечисление обязательных полей
 * @return array        - список с 2мя подмассивами: валидированный массив поле-значение и список ошибок
 */
function validate( array $post, array $fields) {
    $arRes = [];
    $errors = [];

    foreach ($fields as $field) {
        $value = escapeField($post[$field], $field);
        $arRes[$field] = $value;

        $errorMessage = '';
        if (empty($value)) {
            $errorMessage = ERROR_FIELDS[$field];
        } else {
            switch ($field) {
                case 'name':
                case 'message':
                    $errorMessage = checkEqual($post[$field], $value);
                    break;
                case 'email':
                    if(!isValidMail($value)) {
                        $errorMessage = ERROR_FIELDS['wrongEmail'];
                    } elseif (!empty(searchByEmail($value))) {
                        $errorMessage = ERROR_FIELDS['existEmail'];
                    }
                    break;
            }
        }
        if($errorMessage) {
            $errors[$field] = $errorMessage;
        }
    }

    return [$arRes, $errors];
}
