<?php
namespace yeticave\user;

use yeticave\Database;

/**
 * Поиск пользователя по email
 * @param string $email
 * @return array
 */
function searchByEmail(string $email)
{
    $DB = Database::getInstance();
    $DB->prepareQuery(
        'SELECT * FROM `users` WHERE `email` = ?',
        [$email]
    );

    return $DB->getAssocResult() ?? [];
}

/**
 * Добавление пользователя
 * @param array $fields
 * @return bool|int
 */
function add(array $fields) {
    if(empty($fields)) {
        return false;
    }
    //Данные для поля `about` приходят из textarea `message`
    if(!empty($fields['message'])) {
        $fields['about'] = $fields['message'];
        unset($fields['message']);
    }
    $fields['password'] = hashPassword($fields['password']);


    $params = ['name', 'email', 'password', 'about', 'avatar_url'];
    $newFields = [];
    foreach ($params as $key) {
        $newFields[$key] = $fields[$key] ?? '';
    }

    $sql = 'INSERT INTO `users` (`name`, `email`, `password`, `created_at`, `about`, `avatar_url`) VALUES (?, ?, ?, NOW(), ?, ?)';

    return Database::getInstance()->prepareQuery($sql, $newFields, true);
}

/**
 * Получение данных пользователя по его ID
 * @param int $id
 * @return array
 */
function getInfo(int $id)
{
    $DB = Database::getInstance();
    $DB->prepareQuery(
        'SELECT * FROM `users` WHERE `id` = ?',
        [$id]
    );

    return $DB->getAssocResult() ?? [];
}

/**
 * Проверка пароля
 * @param string $password
 * @param string $hash
 * @return bool
 */
function checkPassword(string $password, string $hash) {
    return password_verify($password, $hash);
}

/**
 * Хэширование пароля
 * @param string $password
 * @return string
 */
function hashPassword(string $password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Авторизация пользователя с указанным ID
 * @param int   $id
 * @param array $user
 */
function auth(int $id, array $user) {
    $user = !empty($user) ? $user : getInfo($id);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_avatar'] = $user['avatar_url'] ? AVATARS_UPLOAD_DIR . $user['avatar_url'] : 'img/user.png';
}

/**
 * Проверка авторизации пользователя по данным сессии
 * @return bool
 */
function isAuth() {
    return !empty($_SESSION['user_id']);
}

/**
 * Разлогиниваение пользователя
 */
function logout() {
    unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_avatar']);
}

/**
 * Возвращает id пользователя из данных сессии
 * @return int|bool
 */
function getId() {
    return (int) $_SESSION['user_id'];
}
/**
 * Возвращает имя пользователя из данных сессии
 * @return mixed|string
 */
function getName() {
    return $_SESSION['user_name'] ?? '';
}

/**
 * Возвращает ссылку на аватар пользователя из данных сессии
 * @return mixed|string
 */
function getAvatar() {
    return $_SESSION['user_avatar'] ?? '';
}
