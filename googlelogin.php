<?php
require_once 'vendor/autoload.php'; // composer autoload (потрібно встановити бібліотеку)

session_start();

// Конфіг Google OAuth
$google_client = new Google_Client();
$google_client->setClientId('341535977204-6vsum5n0rtv1rcdq545da5og7cd5cu46.apps.googleusercontent.com');
$google_client->setClientSecret('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');
$google_client->setRedirectUri('http://localhost/web2125ki48veskerars03/googlelogin.php');
$google_client->addScope('email');
$google_client->addScope('profile');

if (isset($_GET['code'])) {
    try {
        // Обмінюємо код на токен доступу
        $token = $google_client->fetchAccessTokenWithAuthCode($_GET['code']);
        
        if (isset($token['error'])) {
            throw new Exception('Помилка автентифікації: ' . $token['error']);
        }

        $google_client->setAccessToken($token['access_token']);
        
        // Зберігаємо токен у сесії
        $_SESSION['access_token'] = $token['access_token'];

        // Отримуємо інформацію про користувача
        $google_oauth = new Google_Service_Oauth2($google_client);
        $userInfo = $google_oauth->userinfo->get();

        // Зберігаємо дані користувача в сесії або базі даних
        $_SESSION['user_email'] = $userInfo->email;
        $_SESSION['user_name'] = $userInfo->name;
        $_SESSION['user_id'] = $userInfo->id;

        // Перенаправляємо на головну сторінку
        header('Location: home.php');
        exit;
    } catch (Exception $e) {
        echo 'Помилка: ' . $e->getMessage();
    }
} else {
    echo 'Недійсний код авторизації';
}