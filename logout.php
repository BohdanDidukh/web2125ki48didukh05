<?php
session_start();

// Видаляємо всі дані сесії
session_unset();
session_destroy();

// Перенаправляємо на головну сторінку
header('Location: index.php');
exit;