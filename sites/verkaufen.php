<?php
    // Zentrale Datenbank-Verbindung einbinden
    require_once '../database/db.php';

    $status_msg = "";
    $status_type = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_sell_request'])) {
        // $conn wird automatisch durch db.php bereitgestellt
        $device_name = $_POST['device_name'];
        $condition = $_POST['condition'];
        $damages = $_POST['damages'];
        $email = $_POST['email'];
        $description = $_POST['description'];

        // SQL-Statement mit Platzhaltern (Prepared Statement)
        $sql = "INSERT INTO sell_requests (device_name, device_condition, damages, email, description, status) 
                VALUES (:device_name, :condition, :damages, :email, :description, 'offen')";

        try {
            // Prepared Statement erstellen
            $stmt = $conn->prepare($sql);

            // Parameter binden
            $stmt->bindValue(':device_name', $device_name, SQLITE3_TEXT);
            $stmt->bindValue(':condition', $condition, SQLITE3_TEXT);
            $stmt->bindValue(':damages', $damages, SQLITE3_TEXT);
            $stmt->bindValue(':email', $email, SQLITE3_TEXT);
            $stmt->bindValue(':description', $description, SQLITE3_TEXT);

            // Statement ausführen
            $result = $stmt->execute();

            if ($result) { // SQLite3::execute() gibt ein SQLite3Result-Objekt bei Erfolg zurück
                $status_msg = "Deine Anfrage wurde erfolgreich gesendet! Du findest sie in deinem Nutzerkonto mit der Mail-Adresse: " . htmlspecialchars($email);
                $status_type = "success";
            } else {
                $status_msg = "Fehler beim Speichern der Anfrage."; // Sollte bei enableExceptions(true) nicht erreicht werden
                $status_type = "error";
            }
            $stmt->close(); // Statement schließen
        } catch (Exception $e) {
            $status_msg = "Fehler beim Speichern: " . $e->getMessage();
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
    <link rel="stylesheet" href="../assets/styles/verkaufen.css">
    <link rel="icon" href="../assets/images/logo.png">
    <title>Verkaufen &bull; Favour Technologies</title>
</head>
<body>
    <div class="navbar">
        <div class="navbar-links-left">
            <button id="theme-toggle" aria-label="Toggle Theme">
                <span class="icon-sun">☀️</span><span class="icon-moon">🌙</span>
            </button>
            <ul class="navbar-links-left-link"><a class="navbar-links-left-link-a" href="../">Start</a></ul>
            <ul class="navbar-links-left-link navbar-links-active"><a class="navbar-links-left-link-a" href="./verkaufen">Verkaufen</a></ul>
            <ul class="navbar-links-left-link"><a class="navbar-links-left-link-a" href="./kaufen">Kaufen</a></ul>
            <ul class="navbar-links-left-link"><a class="navbar-links-left-link-a" href="./reparieren">Reparatur</a></ul>
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
            <section class="sell-section">
                <h1 class="page-title">Geräte verkaufen</h1>
                <p class="page-subtitle">Hier kannst du deine alten Geräte zu fairen Preisen in Zahlung geben. Fülle dazu einfach das Formular aus.</p>
                
                <?php if ($status_msg): ?>
                    <div class="alert alert-<?php echo $status_type; ?>">
                        <?php echo $status_msg; ?>
                    </div>
                <?php endif; ?>

                <form action="verkaufen" method="POST" class="sell-form">
                    <div class="form-group">
                        <label for="device_name">Gerätename</label>
                        <input type="text" id="device_name" name="device_name" placeholder="z.B. iPhone 15 Pro" required>
                    </div>
                    <div class="form-group">
                        <label for="condition">Zustand</label>
                        <select id="condition" name="condition" required>
                            <option value="" disabled selected>Zustand wählen...</option>
                            <option value="Neu">Neu (Originalverpackt)</option>
                            <option value="Wie neu">Wie neu (Keine Gebrauchsspuren)</option>
                            <option value="Sehr gut">Sehr gut (Minimale Gebrauchsspuren)</option>
                            <option value="Gut">Gut (Sichtbare Gebrauchsspuren)</option>
                            <option value="Schlecht">Schlecht (Umfangreiche Gebrauchsspuren)</option>
                            <option value="Defekt">Defekt</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="email">E-Mail Adresse</label>
                        <input type="email" id="email" name="email" placeholder="beispiel@mail.de" required>
                    </div>
                    <div class="form-group">
                        <label for="damages">Mögliche Schäden / Mängel</label>
                        <textarea id="damages" name="damages" rows="2" placeholder="Beschreibe Kratzer, Defekte etc."></textarea>
                    </div>
                    <div class="form-group">
                        <label for="description">Zusätzliche Beschreibung</label>
                        <textarea id="description" name="description" rows="4" placeholder="Weitere Details zum Gerät..." required></textarea>
                    </div>
                    <button type="submit" name="submit_sell_request" class="cta-button">Verkaufsanfrage senden</button>
                </form>

                <div class="info-text">
                    <p><strong>Wichtiger Hinweis:</strong> Den aktuellen Stand deiner Anfrage kannst du jederzeit in deinem Nutzerkonto einsehen. Ein Nutzerkonto ist für die Nutzung dieses Services derzeit verpflichtend.</p>
                </div>
            </section>
        </div>
    </main>

    <footer class="footer">
        <div class="container footer-content">
            <p class="footer-text">&copy; 2026 Favour Technologies. Alle Rechte vorbehalten.</p>
            <p class="footer-disclaimer">Diese Website dient ausschließlich zu Demonstrationszwecken im Rahmen eines Schulprojekts. Alle Daten sind fiktiv.</p>
        </div>
    </footer>

    <script>
        const themeToggle = document.getElementById("theme-toggle"); // Desktop toggle
        const iconSun = themeToggle.querySelector('.icon-sun');
        const iconMoon = themeToggle.querySelector('.icon-moon');

        function updateIcon() {
            if (document.body.classList.contains("dark-mode")) {
                iconSun.style.opacity = '1';
                iconSun.style.transform = 'translate(-50%, -50%) rotate(0deg)';
                iconMoon.style.opacity = '0';
                iconMoon.style.transform = 'translate(-50%, -50%) rotate(90deg)';
            } else {
                iconSun.style.opacity = '0';
                iconSun.style.transform = 'translate(-50%, -50%) rotate(-90deg)';
                iconMoon.style.opacity = '1';
                iconMoon.style.transform = 'translate(-50%, -50%) rotate(0deg)';
            }
        }

        if (localStorage.getItem("theme") === "dark") {
            document.body.classList.add("dark-mode");
        }
        updateIcon();

        themeToggle.addEventListener("click", function() {
            document.body.classList.toggle("dark-mode");
            localStorage.setItem("theme", document.body.classList.contains("dark-mode") ? "dark" : "light");
            updateIcon();
        });
    </script>
</body>
</html>
