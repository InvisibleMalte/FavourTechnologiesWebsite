<?php
    require_once '../database/db.php';

    $status_msg = "";
    $status_type = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_repair_request'])) {
        $device_name = $_POST['device_name'];
        $repair_type = $_POST['repair_type'];
        $email = $_POST['email'];
        $description = $_POST['description'];

        $sql = "INSERT INTO repair_requests (device_name, repair_type, email, description, status) 
                VALUES (:device_name, :repair_type, :email, :description, 'offen')";

        try {
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':device_name', $device_name, SQLITE3_TEXT);
            $stmt->bindValue(':repair_type', $repair_type, SQLITE3_TEXT);
            $stmt->bindValue(':email', $email, SQLITE3_TEXT);
            $stmt->bindValue(':description', $description, SQLITE3_TEXT);

            if ($stmt->execute()) {
                $status_msg = "Deine Reparaturanfrage wurde erfolgreich gesendet! Du findest sie in deinem Nutzerkonto mit der Mail-Adresse: " . htmlspecialchars($email);
                $status_type = "success";
            } else {
                $status_type = "error";
                $status_msg = "Ein Fehler ist aufgetreten.";
            }
            $stmt->close();
        } catch (Exception $e) {
            $status_msg = "Fehler: " . $e->getMessage();
            $status_type = "error";
        }
    }
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/styles/basics.css">
    <link rel="stylesheet" href="../assets/styles/reparieren.css">
    <link rel="icon" href="../assets/images/logo.png">
    <title>Reparatur &bull; Favour Technologies</title>
</head>
<body>
    <div class="navbar">
        <div class="navbar-links-left">
            <button id="theme-toggle" aria-label="Toggle Theme">
                <span class="icon-sun">☀️</span><span class="icon-moon">🌙</span>
            </button>
            <ul class="navbar-links-left-link"><a class="navbar-links-left-link-a" href="../">Start</a></ul>
            <ul class="navbar-links-left-link"><a class="navbar-links-left-link-a" href="./verkaufen">Verkaufen</a></ul>
            <ul class="navbar-links-left-link"><a class="navbar-links-left-link-a" href="./kaufen">Kaufen</a></ul>
            <ul class="navbar-links-left-link navbar-links-active"><a class="navbar-links-left-link-a" href="./reparieren">Reparatur</a></ul>
        </div>
        <div class="navbar-logo">
            <a href="../"><img class="navbar-logo-image" src="../assets/images/logo.png" alt="Logo"></a>
        </div>
        <div class="navbar-links-right">
            <ul class="navbar-links-right-link"><a class="navbar-links-right-link-a" href="./abos">Abos</a></ul>
            <ul class="navbar-links-right-link"><a class="navbar-links-right-link-a" href="./vergleich">Vergleich</a></ul>
            <ul class="navbar-links-right-link"><a class="navbar-links-right-link-a" href="./ueber-uns">Über uns</a></ul>
            <ul class="navbar-links-right-link"><a class="navbar-links-right-link-a" href="./kontakt">Kontakt</a></ul>
            <ul class="navbar-links-right-link right"><a class="navbar-links-right-link-a right" href="./nutzerkonto">Nutzerkonto</a></ul>
        </div>
    </div>

    <main class="content-wrapper">
        <div class="container">
            <section class="repair-section">
                <h1 class="page-title">Gerät reparieren</h1>
                <p class="page-subtitle">Egal ob Displaybruch, Akkuprobleme oder Softwarefehler – wir machen dein Gerät wieder fit.</p>
                
                <?php if ($status_msg): ?>
                    <div class="alert alert-<?php echo $status_type; ?>">
                        <?php echo $status_msg; ?>
                    </div>
                <?php endif; ?>

                <form action="reparieren" method="POST" class="repair-form">
                    <div class="form-group">
                        <label for="device_name">Welches Gerät ist defekt?</label>
                        <input type="text" id="device_name" name="device_name" placeholder="z.B. iPhone 13, Galaxy S22" required>
                    </div>
                    <div class="form-group">
                        <label for="repair_type">Art des Defekts</label>
                        <select id="repair_type" name="repair_type" required>
                            <option value="" disabled selected>Problem wählen...</option>
                            <option value="Display">Display / Glasbruch</option>
                            <option value="Akku">Akku / Ladebuchse</option>
                            <option value="Kamera">Kamera</option>
                            <option value="Wasserschaden">Wasserschaden</option>
                            <option value="Software">Software / Bootloop</option>
                            <option value="Sonstiges">Sonstiges</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="email">Deine E-Mail Adresse</label>
                        <input type="email" id="email" name="email" placeholder="deine@mail.de" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Genaue Fehlerbeschreibung</label>
                        <textarea id="description" name="description" rows="5" placeholder="Bitte beschreibe das Problem so genau wie möglich..." required></textarea>
                    </div>
                    <button type="submit" name="submit_repair_request" class="cta-button">Reparaturanfrage senden</button>
                </form>

                <div class="info-text" style="margin-top: 30px;">
                    <p><strong>Unser Versprechen:</strong> Wir verwenden hochwertige Ersatzteile und führen die meisten Reparaturen innerhalb von 48 Stunden durch.</p>
                    <p><strong>Wichtiger Hinweis:</strong> Den aktuellen Stand deiner Anfrage kannst du jederzeit in deinem Nutzerkonto einsehen. Ein Nutzerkonto ist für die Nutzung dieses Services derzeit verpflichtend.</p>
                </div>
            </section>
        </div>
    </main>

    <footer class="footer">
        <div class="container footer-content">
            <p class="footer-text">&copy; 2026 Favour Technologies. Alle Rechte vorbehalten.</p>
        </div>
    </footer>

    <script>
        const themeToggle = document.getElementById("theme-toggle");
        if (localStorage.getItem("theme") === "dark") {
            document.body.classList.add("dark-mode");
        }
        
        themeToggle.addEventListener("click", function() {
            document.body.classList.toggle("dark-mode");
            localStorage.setItem("theme", document.body.classList.contains("dark-mode") ? "dark" : "light");
        });
    </script>
</body>
</html>