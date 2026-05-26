<?php
session_start();
require_once '../database/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_rank'] != 'Admin') {
    header("Location: login");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_user'])) {
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, rank) VALUES (:name, :email, :password, :rank)");
    $stmt->bindValue(':name', $_POST['name'], SQLITE3_TEXT);
    $stmt->bindValue(':email', $_POST['email'], SQLITE3_TEXT);
    $stmt->bindValue(':password', $_POST['password'], SQLITE3_TEXT);
    $stmt->bindValue(':rank', $_POST['rank'], SQLITE3_TEXT);
    try {
        $stmt->execute();
    } catch (Exception $e) {
        // Email bereits vorhanden
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_user'])) {
    $user_id_to_delete = $_POST['user_id'];
    // Verhindere das Löschen des Admin-Users (ID 1)
    if ($user_id_to_delete != 1) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindValue(':id', $user_id_to_delete, SQLITE3_INTEGER);
        $stmt->execute();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_tickets'])) {
    $conn->exec("DELETE FROM sell_messages; DELETE FROM sell_requests; DELETE FROM repair_messages; DELETE FROM repair_requests; DELETE FROM contact_messages; DELETE FROM contact_requests;");
    header("Location: verwaltung");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_db'])) {
    $conn->exec("DELETE FROM sell_messages; DELETE FROM sell_requests; DELETE FROM repair_messages; DELETE FROM repair_requests; DELETE FROM contact_messages; DELETE FROM contact_requests; DELETE FROM users WHERE id != 1;");
    header("Location: logout");
    exit();
}

$users = $conn->query("SELECT * FROM users");
$closed_sells = $conn->query("SELECT *, 'sell' as type FROM sell_requests WHERE status = 'geschlossen'");
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../assets/styles/basics.css">
    <link rel="stylesheet" href="../assets/styles/verkaufen.css">
    <title>Verwaltung &bull; Favour Technologies</title>
    <style>
        .admin-card { background: var(--card-bg); border: 1px solid var(--navbar-border); border-radius: 20px; padding: 30px; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { text-align: left; padding: 15px 12px; border-bottom: 1px solid var(--navbar-border); vertical-align: middle; }
        th { color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; }
        
        .table-input {
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid var(--navbar-border);
            background: var(--bg-color);
            color: var(--text-color);
            font-family: inherit;
            font-size: 0.9rem;
            width: 100%;
            transition: var(--transition);
        }
        .table-input:focus { outline: none; border-color: var(--primary-color); }
    </style>
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

        <!-- Mobile Side Menu -->
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
        <h1 style="margin-top:40px;">Nutzer hinzufügen</h1>
        <div class="admin-card">
            <form method="POST" class="sell-form" style="padding:0; border:none; background:none; display: flex; flex-direction: row; flex-wrap: nowrap; gap: 15px; align-items: flex-end;">
                <div class="form-group" style="flex: 1; min-width: 120px; margin-bottom:0;">
                    <label>Name</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group" style="flex: 1; min-width: 150px; margin-bottom:0;">
                    <label>E-Mail</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group" style="flex: 1; min-width: 120px; margin-bottom:0;">
                    <label>Passwort</label>
                    <input type="text" name="password" required>
                </div>
                <div class="form-group" style="flex: 0 0 130px; margin-bottom:0;">
                    <label>Rang</label>
                    <select name="rank">
                        <option value="Kunde">Kunde</option>
                        <option value="Mitarbeiter">Mitarbeiter</option>
                        <option value="Admin">Admin</option>
                    </select>
                </div>
                <button type="submit" name="add_user" class="cta-button" style="margin-top: 0; padding: 10px 20px; width: auto; white-space: nowrap;">Hinzufügen</button>
            </form>
        </div>

        <h1 style="margin-top:40px;">Benutzerverwaltung</h1>
        <div class="admin-card">
            <table>
                <tr><th>Name</th><th>Email</th><th>Rang</th><th style="text-align:right;">Aktion</th></tr>
                <?php while($u = $users->fetchArray(SQLITE3_ASSOC)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($u['name']); ?></td>
                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                    <td><?php echo htmlspecialchars($u['rank']); ?></td>
                    <td style="text-align:right;">
                        <a href="edit_user?id=<?php echo $u['id']; ?>" class="cta-button" style="padding: 8px 12px; text-decoration: none; display: inline-block; margin-right: 10px;">✏️</a>
                        <?php if ($u['id'] != 1): // Admin-User nicht löschbar machen ?>
                            <form method="POST" style="display:inline-block;" onsubmit="return confirm('Soll dieser Benutzer wirklich gelöscht werden?');">
                                <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                <button type="submit" name="delete_user" class="cta-button danger" style="padding: 8px 12px; font-size:0.85rem;">🗑️</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
        <div class="admin-card" style="border-color:var(--primary-color);">
            <h2>Alle Tickets löschen</h2>
            <p>Löscht alle Verkaufs-, Reparatur- und Kontaktanfragen inklusive aller zugehörigen Nachrichten. Alle Nutzerkonten bleiben jedoch bestehen.</p>
            <form method="POST" onsubmit="return confirm('Möchtest du wirklich alle Tickets und Nachrichten unwiderruflich löschen?');">
                <button type="submit" name="reset_tickets" class="cta-button">Tickets zurücksetzen</button>
            </form>
        </div>
        <div class="admin-card" style="border-color:#dc3545;">
            <h2>Datenbank zurücksetzen</h2>
            <p>Löscht alle Anträge und alle Nutzer außer Admin (ID 1).</p>
            <form method="POST" onsubmit="return confirm('Wirklich alles löschen?');"><button type="submit" name="reset_db" class="cta-button danger">Jetzt zurücksetzen</button></form>
        </div>
    </main>
    <script>
        const themeToggle = document.getElementById("theme-toggle");
        if (localStorage.getItem("theme") === "dark") {
            document.body.classList.add("dark-mode");
        }
        
        themeToggle.addEventListener("click", function() {
            document.body.classList.toggle("dark-mode");
            localStorage.setItem("theme", document.body.classList.contains("dark-mode") ? "dark" : "light");
            if(typeof updateIcon === "function") updateIcon();
        });

        const hamburger = document.getElementById("hamburger");
        const mobileNav = document.getElementById("mobile-nav");

        if(hamburger && mobileNav) {
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
        }
    </script>
</body>
</html>