<?php
namespace user;

function searchByEmail(string $email) {
    global $users;

    $result = null;
    foreach ($users as $key => $user) {
        if($user['email'] === $email) {
            $user['id'] = $key;
            $result = $user;
            break;
        }
    }
    return $result;
}

function getUser(int $id) {
    global $users;
    return isset($users[$id]) ? $users[$id] : [];
}

function checkPassword(string $password, string $hash) {
    return password_verify($password, $hash);
}

function auth(int $id) {
    $user = getUser($id);
    $_SESSION['user'] = $user['name'];
    $_SESSION['avatar'] = $user['avatar'] ?? 'img/user.png';
}

function isAuth() {
    return !empty($_SESSION['user']);
}

function logout() {
    unset($_SESSION['user']);
    unset($_SESSION['avatar']);
}

function getName() {
    return $_SESSION['user'] ?? '';
}

function getAvatar() {
    return $_SESSION['avatar'] ?? '';
}
