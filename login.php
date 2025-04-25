<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "web2425";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Помилка підключення: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = trim($_POST['login']);
    $password = trim($_POST['password']);

    if (empty($login) || empty($password)) {
        $message = "Будь ласка, заповніть всі поля!";
    } else {
        $sql = "SELECT `password_hash` FROM `login_password` WHERE `Login` = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die('Помилка підготовки запиту: ' . $conn->error);
        }

        $stmt->bind_param("s", $login);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($hashed_password);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                session_start();
                $_SESSION['login'] = $login;
                header("Location: home.php");
                exit;
            } else {
                $message = "Невірний пароль!";
            }
        } else {
            $message = "Користувач не знайдений!";
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вхід</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: white;
            padding: 40px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
            width: 320px;
            text-align: center;
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 25px;
            font-size: 14px;
            box-sizing: border-box;
        }
        input:focus {
            outline: none;
            border-color: #d9534f;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #d9534f;
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #c9302c;
        }
        .message {
            margin-top: 15px;
            color: #f2a6a6;
        }
        .link {
            margin-top: 20px;
            display: block;
            text-decoration: none;
            color: #d9534f;
            font-size: 14px;
        }
        .link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Вхід</h2>
    <form method="POST">
        <input type="text" name="login" placeholder="Введіть логін" required><br>
        <input type="password" name="password" placeholder="Введіть пароль" required><br>
        <button type="submit">Увійти</button>
    </form>
    <div class="message"><?php echo $message; ?></div>

    <div>
        <a href="index.php" class="link">Зареєструватися</a>
    </div>
</div>

</body>
</html>