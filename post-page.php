<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    echo "<h2>POST request received</h2>";
    echo "<p>Information from POST request:</p>";
    echo "<ul>";
    foreach ($_POST as $key => $value) {
        echo "<li><strong>$key:</strong> " . htmlspecialchars($value) . "</li>";
    }
    echo "</ul>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POST Page - Web Business Card</title>
    <link rel="stylesheet" href="styles/main.css">
</head>
<body>
    <header>
        <h1>POST Page</h1>
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
            <h2>This page is loaded via POST request</h2>
        </section>

        <section class="post-data">
            <h3>Submit your information via POST:</h3>
            <form id="post-form" method="POST">
                <label for="post-name">Name:</label>
                <input type="text" id="post-name" name="name" required><br>

                <label for="post-email">Email:</label>
                <input type="email" id="post-email" name="email" required><br>

                <label for="post-message">Message:</label>
                <textarea id="post-message" name="message" required></textarea><br>

                <button type="submit">Submit POST</button>
            </form>
            <div id="post-result"></div>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Veskera R.S. KI-408</p>
    </footer>

    <script src="js/ajax.js"></script>
</body>
</html>