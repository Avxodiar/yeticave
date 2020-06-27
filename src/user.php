<?php
namespace yeticave\user;

use function yeticave\db\prepareStmt;
use function yeticave\db\executeStmt;
use function yeticave\db\getAssocResult;

/**
 * Поиск пользователя по email
 * @param string $email
 * @return array
 */
function searchByEmail(string $email) {
    $sql = 'SELECT * FROM `users` WHERE `email` = ?';
    $stmt = prepareStmt($sql);
    executeStmt($stmt, [$email]);

    return getAssocResult($stmt) ?? [];
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
    $stmt = prepareStmt($sql);

    return executeStmt($stmt, $newFields, true);
}

/**
 * Получение данных пользователя по его ID
 * @param int $id
 * @return array
 */
function get(int $id) {
    $sql = 'SELECT * FROM `users` WHERE `id` = ?';
    $stmt = prepareStmt($sql);
    executeStmt($stmt, $id);

    return getAssocResult($stmt) ?? [];
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
    $user = !empty($user) ? $user : get($id);
    $_SESSION['id'] = $user['id'];
    $_SESSION['user'] = $user['name'];
    $_SESSION['avatar'] = $user['avatar_url'] ? AVATARS_UPLOAD_DIR . $user['avatar_url'] : 'img/user.png';
}

/**
 * Проверка авторизации пользователя по данным сессии
 * @return bool
 */
function isAuth() {
    return !empty($_SESSION['user']);
}

/**
 * Разлогиниваение пользователя
 */
function logout() {
    unset($_SESSION['user'], $_SESSION['avatar']);
}

/**
 * Возвращает имя пользователя из данных сессии
 * @return mixed|string
 */
function getName() {
    return $_SESSION['user'] ?? '';
}

/**
 * Возвращает ссылку на аватар пользователя из данных сессии
 * @return mixed|string
 */
function getAvatar() {
    return $_SESSION['avatar'] ?? '';
}
