<?php
    require_once '../database/db.php';

    $status_msg = "";
    $status_type = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_contact'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $subject = $_POST['subject'];
        $message = $_POST['message'];

        $sql = "INSERT INTO contact_requests (name, email, subject, message) 
                VALUES (:name, :email, :subject, :message)";

        try {
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':name', $name, SQLITE3_TEXT);
            $stmt->bindValue(':email', $email, SQLITE3_TEXT);
            $stmt->bindValue(':subject', $subject, SQLITE3_TEXT);
            $stmt->bindValue(':message', $message, SQLITE3_TEXT);

            if ($stmt->execute()) {
                $status_msg = "Vielen Dank für deine Nachricht! Wir haben sie erhalten und werden uns unter " . htmlspecialchars($email) . " melden.";
                $status_type = "success";
            } else {
                $status_type = "error";
                $status_msg = "Fehler beim Senden der Nachricht.";
            }
            $stmt->close();
        } catch (Exception $e) {
            $status_msg = "Datenbankfehler: " . $e->getMessage();
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
    <link rel="stylesheet" href="../assets/styles/kontakt.css">
    <link rel="icon" href="../assets/images/logo.png">
    <title>Kontakt &bull; Favour Technologies</title>
</head>
<body lang="de">
    <div class="navbar">
        <div class="navbar-controls-mobile">
            <button class="hamburger" id="hamburger" aria-label="Menü öffnen">
                <span></span><span></span><span></span>
            </button>
            <button id="theme-toggle" aria-label="Toggle Theme">
                <span class="icon-sun">☀️</span><span class="icon-moon">🌙</span>
            </button>
        </div>
        
        <div class="navbar-links-left desktop-only">
            <ul class="navbar-links-left-link"><a class="navbar-links-left-link-a" href="../">Start</a></ul>
            <ul class="navbar-links-left-link"><a class="navbar-links-left-link-a" href="./verkaufen">Verkaufen</a></ul>
            <ul class="navbar-links-left-link"><a class="navbar-links-left-link-a" href="./kaufen">Kaufen</a></ul>
            <ul class="navbar-links-left-link"><a class="navbar-links-left-link-a" href="./reparieren">Reparatur</a></ul>
        </div>
        <div class="navbar-logo">
            <a href="../"><img class="navbar-logo-image" src="../assets/images/logo.png" alt="Logo"></a>
        </div>
        <div class="navbar-links-right desktop-only">
            <ul class="navbar-links-right-link"><a class="navbar-links-right-link-a" href="./abos">Abos</a></ul>
            <ul class="navbar-links-right-link"><a class="navbar-links-right-link-a" href="./vergleich">Vergleich</a></ul>
            <ul class="navbar-links-right-link"><a class="navbar-links-right-link-a" href="./ueber-uns">Über uns</a></ul>
            <ul class="navbar-links-right-link navbar-links-active"><a class="navbar-links-right-link-a" href="./kontakt">Kontakt</a></ul>
            <ul class="navbar-links-right-link right"><a class="navbar-links-right-link-a right" href="./nutzerkonto">Nutzerkonto</a></ul>
        </div>

        <div class="navbar-user-mobile">
            <a href="./nutzerkonto" aria-label="Nutzerkonto">
                <svg class="mobile-user-icon" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
            </a>
        </div>

        <!-- Mobile Side Menu -->
        <nav class="mobile-nav" id="mobile-nav">
            <ul class="navbar-links-left-link"><a class="navbar-links-left-link-a" href="../">Start</a></ul>
            <ul class="navbar-links-left-link"><a class="navbar-links-left-link-a" href="./verkaufen">Verkaufen</a></ul>
            <ul class="navbar-links-left-link"><a class="navbar-links-left-link-a" href="./kaufen">Kaufen</a></ul>
            <ul class="navbar-links-left-link"><a class="navbar-links-left-link-a" href="./reparieren">Reparatur</a></ul>
            <ul class="navbar-links-right-link"><a class="navbar-links-right-link-a" href="./abos">Abos</a></ul>
            <ul class="navbar-links-right-link"><a class="navbar-links-right-link-a" href="./vergleich">Vergleich</a></ul>
            <ul class="navbar-links-right-link"><a class="navbar-links-right-link-a" href="./ueber-uns">Über uns</a></ul>
            <ul class="navbar-links-right-link"><a class="navbar-links-right-link-a" href="./kontakt">Kontakt</a></ul>
            <ul class="navbar-links-right-link"><a class="navbar-links-right-link-a" href="./nutzerkonto">Nutzerkonto</a></ul>
        </nav>
    </div>

    <main class="content-wrapper">
        <div class="container">
            <section class="contact-section">
            <h1 class="page-title">Kontaktiere uns</h1>
            <p class="page-subtitle">Hast du Fragen zu unserem fiktiven Startup oder unserem Service? Schreib uns einfach!</p>

            <?php if ($status_msg): ?>
                <div class="alert alert-<?php echo $status_type; ?>" style="<?php echo $status_type == 'success' ? 'color: #28a745; background: rgba(40,167,69,0.1); border-color: #28a745;' : 'color: #dc3545; background: rgba(220,53,69,0.1); border-color: #dc3545;'; ?>">
                    <?php echo $status_msg; ?>
                </div>
            <?php endif; ?>

            <form action="kontakt" method="POST" class="contact-form">
                <div class="form-group">
                    <label for="name">Dein Name</label>
                    <input type="text" id="name" name="name" placeholder="Elias Beispiel" required>
                </div>
                <div class="form-group">
                    <label for="email">Deine E-Mail Adresse</label>
                    <input type="email" id="email" name="email" placeholder="elias@mail.de" required>
                </div>
                <div class="form-group">
                    <label for="subject">Betreff</label>
                    <input type="text" id="subject" name="subject" placeholder="Frage zum Ankauf" required>
                </div>
                <div class="form-group">
                    <label for="message">Nachricht</label>
                    <textarea id="message" name="message" rows="5" placeholder="Deine Nachricht an uns..." required></textarea>
                </div>
                <button type="submit" name="submit_contact" class="cta-button">Nachricht senden</button>
            </form>

            <div class="info-text" style="margin-top: 30px;">
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
        const themeToggle = document.getElementById("theme-toggle");
        if (localStorage.getItem("theme") === "dark") {
            document.body.classList.add("dark-mode");
        }
        themeToggle.addEventListener("click", function() {
            document.body.classList.toggle("dark-mode");
            localStorage.setItem("theme", document.body.classList.contains("dark-mode") ? "dark" : "light");
        });
    </script>
    <script>
        const hamburger = document.getElementById("hamburger");
        const mobileNav = document.getElementById("mobile-nav");

        hamburger.addEventListener("click", () => {
            mobileNav.classList.toggle("active");
        });

        document.addEventListener("click", (event) => {
            const isClickInsideMobileNav = mobileNav.contains(event.target);
            const isClickOnHamburger = hamburger.contains(event.target);
            const isMobileNavActive = mobileNav.classList.contains("active");

            if (isMobileNavActive && !isClickInsideMobileNav && !isClickOnHamburger) {
                mobileNav.classList.remove("active");
            }
        });
    </script>
</body>
</html>