<?php
session_start();
require_once '../database/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_rank'] != 'Admin') {
    header("Location: login");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: verwaltung");
    exit();
}

$edit_id = $_GET['id'];
$status_msg = "";
$status_type = "";

// Benutzerdaten laden
$stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
$stmt->bindValue(':id', $edit_id, SQLITE3_INTEGER);
$user_to_edit = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

if (!$user_to_edit) {
    header("Location: verwaltung");
    exit();
}

// Update Logik
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_user'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $rank = $_POST['rank'];
    $password = $_POST['password'];

    if (!empty($password)) {
        $stmt = $conn->prepare("UPDATE users SET name = :name, email = :email, rank = :rank, password = :password WHERE id = :id");
        $stmt->bindValue(':password', $password, SQLITE3_TEXT);
    } else {
        $stmt = $conn->prepare("UPDATE users SET name = :name, email = :email, rank = :rank WHERE id = :id");
    }
    
    $stmt->bindValue(':name', $name, SQLITE3_TEXT);
    $stmt->bindValue(':email', $email, SQLITE3_TEXT);
    $stmt->bindValue(':rank', $rank, SQLITE3_TEXT);
    $stmt->bindValue(':id', $edit_id, SQLITE3_INTEGER);

    try {
        if ($stmt->execute()) {
            $status_msg = "Benutzer erfolgreich aktualisiert!";
            $status_type = "success";
            // Daten neu laden
            $user_to_edit['name'] = $name;
            $user_to_edit['email'] = $email;
            $user_to_edit['rank'] = $rank;
        }
    } catch (Exception $e) {
        $status_msg = "Fehler: E-Mail wird eventuell bereits verwendet.";
        $status_type = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../assets/styles/basics.css">
    <link rel="stylesheet" href="../assets/styles/verkaufen.css">
    <title>Nutzer bearbeiten &bull; Favour Technologies</title>
</head>
<body>
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
            <ul class="navbar-links-left-link"><a class="navbar-links-left-link-a" href="/">Start</a></ul>
            <ul class="navbar-links-left-link"><a class="navbar-links-left-link-a" href="verkaufen">Verkaufen</a></ul>
            <ul class="navbar-links-left-link"><a class="navbar-links-left-link-a" href="kaufen">Kaufen</a></ul>
            <ul class="navbar-links-left-link"><a class="navbar-links-left-link-a" href="reparieren">Reparatur</a></ul>
        </div>
        <div class="navbar-logo">
            <a href="/"><img class="navbar-logo-image" src="../assets/images/logo.png" alt="Logo"></a>
        </div>
        <div class="navbar-links-right desktop-only">
            <ul class="navbar-links-right-link"><a class="navbar-links-right-link-a" href="abos">Abos</a></ul>
            <ul class="navbar-links-right-link"><a class="navbar-links-right-link-a" href="vergleich">Vergleich</a></ul>
            <ul class="navbar-links-right-link"><a class="navbar-links-right-link-a" href="ueber-uns">Über uns</a></ul>
            <ul class="navbar-links-right-link"><a class="navbar-links-right-link-a" href="kontakt">Kontakt</a></ul>
            
            <li class="navbar-links-right-link dropdown right">
                <span class="nav-name">
                    <?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Nutzerkonto'; ?>
                    <span class="arrow-down"></span>
                </span>
                <div class="dropdown-content">
                    <a href="nutzerkonto">Übersicht</a>
                    <?php if($_SESSION['user_rank'] == 'Mitarbeiter' || $_SESSION['user_rank'] == 'Admin'): ?><a href="mitarbeiter">Mitarbeiter</a><?php endif; ?>
                    <?php if($_SESSION['user_rank'] == 'Admin'): ?><a href="verwaltung">Verwaltung</a><?php endif; ?>
                    <a href="nutzerkonto?tab=settings">Einstellungen</a>
                    <a href="logout" style="color: #dc3545;">Abmelden</a>
                </div>
            </li>
        </div>

        <div class="navbar-user-mobile">
            <a href="nutzerkonto" aria-label="Nutzerkonto">
                <svg class="mobile-user-icon" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
            </a>
        </div>

        <nav class="mobile-nav" id="mobile-nav">
            <ul class="navbar-links-left-link"><a class="navbar-links-left-link-a" href="/">Start</a></ul>
            <ul class="navbar-links-left-link"><a class="navbar-links-left-link-a" href="verkaufen">Verkaufen</a></ul>
            <ul class="navbar-links-left-link"><a class="navbar-links-left-link-a" href="kaufen">Kaufen</a></ul>
            <ul class="navbar-links-left-link"><a class="navbar-links-left-link-a" href="reparieren">Reparatur</a></ul>
            <ul class="navbar-links-right-link"><a class="navbar-links-right-link-a" href="abos">Abos</a></ul>
            <ul class="navbar-links-right-link"><a class="navbar-links-right-link-a" href="vergleich">Vergleich</a></ul>
            <ul class="navbar-links-right-link"><a class="navbar-links-right-link-a" href="ueber-uns">Über uns</a></ul>
            <ul class="navbar-links-right-link"><a class="navbar-links-right-link-a" href="kontakt">Kontakt</a></ul>
            <ul class="navbar-links-right-link"><a class="navbar-links-right-link-a" href="nutzerkonto">Nutzerkonto</a></ul>
        </nav>
    </div>

    <main class="container">
        <div style="margin-top: 40px; display: flex; align-items: center; gap: 20px;">
            <a href="verwaltung" style="text-decoration: none; font-size: 1.5rem;">←</a>
            <h1>Nutzer bearbeiten</h1>
        </div>

        <?php if($status_msg): ?>
            <div class="alert alert-<?php echo $status_type; ?>" style="margin-top: 20px;">
                <?php echo $status_msg; ?>
            </div>
        <?php endif; ?>

        <div class="admin-card" style="max-width: 600px; margin-top: 30px; background: var(--card-bg); border: 1px solid var(--navbar-border); border-radius: 20px; padding: 30px;">
            <form method="POST" class="sell-form" style="padding:0; border:none; background:none;">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($user_to_edit['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label>E-Mail</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user_to_edit['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Rang</label>
                    <select name="rank">
                        <option value="Kunde" <?php if($user_to_edit['rank'] == 'Kunde') echo 'selected'; ?>>Kunde</option>
                        <option value="Mitarbeiter" <?php if($user_to_edit['rank'] == 'Mitarbeiter') echo 'selected'; ?>>Mitarbeiter</option>
                        <option value="Admin" <?php if($user_to_edit['rank'] == 'Admin') echo 'selected'; ?>>Admin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Neues Passwort (leer lassen zum Beibehalten)</label>
                    <input type="text" name="password" placeholder="Passwort ändern...">
                </div>
                <div style="display: flex; gap: 15px; margin-top: 20px;">
                    <button type="submit" name="update_user" class="cta-button" style="flex: 1;">Speichern</button>
                    <a href="verwaltung" class="cta-button" style="flex: 1; text-align: center; text-decoration: none; background: transparent; border: 1px solid var(--navbar-border); color: var(--text-color);">Abbrechen</a>
                </div>
            </form>
        </div>
    </main>

    <script>
        const themeToggle = document.getElementById("theme-toggle");
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