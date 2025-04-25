<?php
$name = '';
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['name'])) {
    $name = htmlspecialchars($_GET['name']);
    $currentTime = date('Y-m-d H:i:s');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GET Request - Web Business Card</title>
    <link rel="stylesheet" href="styles/main.css">
</head>
<body>
    <header>
        <h1>Web Business Card</h1>
        <nav>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="get-page.php">GET Page</a></li>
                <li><a href="post-page.php">POST Page</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="page-content">
            <h2>Submit your name via GET request:</h2>
            <form id="get-form" method="GET">
                <label for="get-name">Name:</label>
                <input type="text" id="get-name" name="name" required><br>
                <button type="submit">Submit GET</button>
            </form>

            <?php
            if ($name) {
                echo "<div id='get-result'>
                        <h3>GET request received</h3>
                        <p>Hello, $name!</p>
                        <small>Executed at: $currentTime</small>
                      </div>";
            }
            ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Veskera R.S. KI-408</p>
    </footer>

</body>
</html>