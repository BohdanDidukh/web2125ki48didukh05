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

        $check_sql = "SELECT * FROM login_password WHERE login = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $login);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if ($result->num_rows > 0) {
            $message = "Цей логін вже зайнятий!";
        } else {

            $open_password = $password;

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $encryption_key = "your_encryption_key";
            $iv = substr(hash('sha256', "your_iv"), 0, 16);
            $encrypted_password = openssl_encrypt($password, "aes-256-cbc", $encryption_key, 0, $iv);

            $sql = "INSERT INTO login_password (login, password, openssl_encrypt, password_hash) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if (!$stmt) {
                die("SQL Error: " . $conn->error);
            }

            $stmt->bind_param("ssss", $login, $open_password, $encrypted_password, $hashed_password);

            if ($stmt->execute()) {

                session_start();
                $_SESSION['login'] = $login;

                header("Location: home.php");
                exit();
            } else {
                $message = "Помилка: " . $conn->error;
            }

            $stmt->close();
        }

        $check_stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Реєстрація</title>
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
    <h2>Реєстрація</h2>
    <form method="POST">
        <input type="text" name="login" placeholder="Введіть логін" required><br>
        <input type="password" name="password" placeholder="Введіть пароль" required><br>
        <button type="submit">Зареєструватися</button>
    </form>
    <div class="message"><?php echo $message; ?></div>

    <div>
        <a href="login.php" class="link">Увійти</a>
    </div>
</div>

</body>
</html>