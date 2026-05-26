<?php
session_start();
require_once '../database/db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['user_rank'] != 'Mitarbeiter' && $_SESSION['user_rank'] != 'Admin')) {
    header("Location: login");
    exit();
}

$user_email = $_SESSION['user_email'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['close_request'])) {
    $req_id = $_POST['request_id'];
    $type = $_POST['request_type'];
    $table = $type . "_requests";
    $conn->exec("UPDATE $table SET status = 'geschlossen' WHERE id = $req_id");
}

$sells = $conn->query("SELECT *, 'sell' as type FROM sell_requests WHERE status = 'offen'");
$repairs = $conn->query("SELECT *, 'repair' as type FROM repair_requests WHERE status = 'offen'");
$contacts = $conn->query("SELECT *, 'contact' as type FROM contact_requests WHERE status = 'offen'");
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../assets/styles/basics.css">
    <link rel="stylesheet" href="../assets/styles/verkaufen.css">
    <title>Mitarbeiter &bull; Favour Technologies</title>
    <style>
        .request-card { background: var(--card-bg); border: 1px solid var(--navbar-border); border-radius: 15px; padding: 20px; margin-bottom: 20px; }
        .tag { padding: 4px 8px; border-radius: 5px; font-size: 0.75rem; font-weight: bold; text-transform: uppercase; }
        .tag-sell { background: #e3f2fd; color: #1976d2; }
        .tag-repair { background: #f3e5f5; color: #7b1fa2; }
        .tag-contact { background: #e8f5e9; color: #2e7d32; }
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
        <h1 style="margin-top:40px;">Offene Anfragen</h1>
        <?php 
        $all = [];
        while($r = $sells->fetchArray(SQLITE3_ASSOC)) $all[] = $r;
        while($r = $repairs->fetchArray(SQLITE3_ASSOC)) $all[] = $r;
        while($r = $contacts->fetchArray(SQLITE3_ASSOC)) $all[] = $r;
        
        foreach($all as $req): ?>
            <div class="request-card">
                <div style="display:flex; justify-content:space-between; margin-bottom:15px;">
                    <div><span class="tag tag-<?php echo $req['type']; ?>"><?php echo $req['type']; ?></span> <strong style="margin-left:10px;"><?php echo htmlspecialchars($req['device_name'] ?? $req['subject']); ?></strong></div>
                    <div style="display:flex; gap:10px;">
                        <a href="antrag?id=<?php echo $req['id']; ?>&type=<?php echo $req['type']; ?>" class="cta-button" style="padding:8px 20px; font-size:0.85rem;">Öffnen</a>
                        <form method="POST" style="margin:0;"><input type="hidden" name="request_id" value="<?php echo $req['id']; ?>"><input type="hidden" name="request_type" value="<?php echo $req['type']; ?>"><button type="submit" name="close_request" class="cta-button danger" style="padding:8px 20px; font-size:0.85rem;">Schließen</button></form>
                    </div>
                </div>
                <p style="font-size:0.85rem; color:var(--text-muted);"><?php echo htmlspecialchars(substr($req['description'] ?? $req['message'], 0, 100)) . '...'; ?></p>
            </div>
        <?php endforeach; ?>
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