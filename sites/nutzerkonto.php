<?php
session_start();
require_once '../database/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login");
    exit();
}

if (!isset($_GET['tab'])) {
    header("Location: nutzerkonto?tab=overview");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['user_email'];
$status_msg = "";

// Passwort oder Name ändern
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $new_name = $_POST['name'];
    $new_password = $_POST['password'];
    
    if (!empty($new_password)) {
        $stmt = $conn->prepare("UPDATE users SET name = :name, password = :pw WHERE id = :id");
        $stmt->bindValue(':pw', $new_password, SQLITE3_TEXT);
    } else {
        $stmt = $conn->prepare("UPDATE users SET name = :name WHERE id = :id");
    }
    $stmt->bindValue(':name', $new_name, SQLITE3_TEXT);
    $stmt->bindValue(':id', $user_id, SQLITE3_INTEGER);
    if ($stmt->execute()) {
        $_SESSION['user_name'] = $new_name;
        $status_msg = "Profil aktualisiert!";
    }
}

// Antrag schließen
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['close_request'])) {
    $table = $_POST['request_type'] . "_requests";
    $stmt = $conn->prepare("UPDATE $table SET status = 'geschlossen' WHERE id = :id AND email = :email");
    $stmt->bindValue(':id', $_POST['request_id'], SQLITE3_INTEGER);
    $stmt->bindValue(':email', $user_email, SQLITE3_TEXT);
    $stmt->execute();
}

// Anträge abrufen
$sells = $conn->query("SELECT *, 'sell' as type FROM sell_requests WHERE email = '$user_email' ORDER BY created_at DESC");
$repairs = $conn->query("SELECT *, 'repair' as type FROM repair_requests WHERE email = '$user_email' ORDER BY created_at DESC");
$contacts = $conn->query("SELECT *, 'contact' as type FROM contact_requests WHERE email = '$user_email' ORDER BY created_at DESC");

$all_reqs = [];
while($r = $sells->fetchArray(SQLITE3_ASSOC)) $all_reqs[] = $r;
while($r = $repairs->fetchArray(SQLITE3_ASSOC)) $all_reqs[] = $r;
while($r = $contacts->fetchArray(SQLITE3_ASSOC)) $all_reqs[] = $r;
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/styles/basics.css">
    <link rel="stylesheet" href="../assets/styles/verkaufen.css">
    <title>Mein Konto &bull; Favour Technologies</title>
    <style>
        .dashboard-grid { display: grid; grid-template-columns: 250px 1fr; gap: 30px; margin-top: 40px; }
        .side-nav { background: var(--card-bg); border: 1px solid var(--navbar-border); border-radius: 20px; padding: 20px; height: fit-content; }
        .side-nav-link { display: block; padding: 12px; text-decoration: none; color: var(--text-color); border-radius: 10px; margin-bottom: 5px; transition: 0.2s; }
        .side-nav-link:hover, .side-nav-link.active { background: var(--primary-color); color: white; }
        
        .request-card { background: var(--card-bg); border: 1px solid var(--navbar-border); border-radius: 15px; padding: 20px; margin-bottom: 20px; }
        .request-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--navbar-border); padding-bottom: 10px; margin-bottom: 15px; }
        .tag { padding: 4px 8px; border-radius: 5px; font-size: 0.75rem; font-weight: bold; text-transform: uppercase; }
        .tag-sell { background: #e3f2fd; color: #1976d2; }
        .tag-repair { background: #f3e5f5; color: #7b1fa2; }
        .tag-contact { background: #e8f5e9; color: #2e7d32; }
        
        .chat-box { max-height: 300px; overflow-y: auto; background: var(--bg-color); padding: 15px; border-radius: 10px; margin-bottom: 15px; }
        .msg { margin-bottom: 10px; padding: 10px; border-radius: 10px; max-width: 80%; font-size: 0.9rem; }
        .msg.user { background: var(--primary-color); color: white; margin-left: auto; }
        .msg.staff { background: var(--navbar-border); color: var(--text-color); }
        
        @media (max-width: 900px) { .dashboard-grid { grid-template-columns: 1fr; } }
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
            <a href="/"><img class="navbar-logo-image" src="assets/images/logo.png" alt="Logo"></a>
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
        <div class="dashboard-grid">
            <aside class="side-nav">
                <a href="?tab=overview" class="side-nav-link <?php echo (!isset($_GET['tab']) || $_GET['tab'] == 'overview') ? 'active' : ''; ?>">Übersicht</a>
                <a href="?tab=requests" class="side-nav-link <?php echo ($_GET['tab'] == 'requests') ? 'active' : ''; ?>">Meine Anträge</a>
                <a href="?tab=settings" class="side-nav-link <?php echo ($_GET['tab'] == 'settings') ? 'active' : ''; ?>">Einstellungen</a>
                <hr style="border:0; border-top:1px solid var(--navbar-border); margin: 10px 0;">
                <a href="logout" class="side-nav-link" style="color:#dc3545;">Abmelden</a>
            </aside>

            <section class="main-content">
                <?php if($status_msg) echo "<div class='alert alert-success'>$status_msg</div>"; ?>

                <?php if(!isset($_GET['tab']) || $_GET['tab'] == 'overview'): ?>
                    <h1>Willkommen, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
                    <p style="color:var(--text-muted);">Hier findest du eine Zusammenfassung deiner Aktivitäten.</p>
                    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:20px; margin-top:30px;">
                        <div class="request-card">
                            <h3>Aktive Anträge</h3>
                            <p style="font-size:1.5rem;">Gesamt: <?php 
                                $count = 0;
                                foreach($all_reqs as $r) if($r['status'] == 'offen') $count++;
                                echo $count;
                            ?></p>
                        </div>
                    </div>

                <?php elseif($_GET['tab'] == 'requests'): ?>
                    <h1>Meine Anträge</h1>
                    <?php 
                    // Sortieren: Offen zuerst, dann nach Datum
                    usort($all_reqs, function($a, $b) {
                        if ($a['status'] === $b['status']) {
                            return strcmp($b['created_at'], $a['created_at']);
                        }
                        return ($a['status'] === 'offen') ? -1 : 1;
                    });

                    if(empty($all_reqs)): ?>
                        <p>Du hast aktuell keine Anträge.</p>
                    <?php else: 
                        foreach($all_reqs as $req): ?>
                        <div class="request-card">
                            <div class="request-header">
                                <div>
                                    <span class="tag tag-<?php echo $req['type']; ?>"><?php echo $req['type']; ?></span>
                                    <strong style="margin-left:10px;">
                                        <?php echo htmlspecialchars($req['device_name'] ?? $req['subject']); ?>
                                    </strong>
                                </div>
                                <small><?php echo date('d.m.Y', strtotime($req['created_at'])); ?></small>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <p style="font-size:0.9rem; color:var(--text-muted);">
                                    Status: <span style="color:<?php echo ($req['status'] == 'offen') ? 'var(--primary-color)' : '#dc3545'; ?>"><?php echo $req['status']; ?></span>
                                </p>
                                <div style="display: flex; gap: 10px;">
                                    <a href="antrag?id=<?php echo $req['id']; ?>&type=<?php echo $req['type']; ?>" class="cta-button" style="padding:8px 20px; font-size:0.85rem;">Öffnen</a>
                                    <?php if($req['status'] == 'offen'): ?>
                                        <form method="POST" style="margin:0;"><input type="hidden" name="request_id" value="<?php echo $req['id']; ?>"><input type="hidden" name="request_type" value="<?php echo $req['type']; ?>"><button type="submit" name="close_request" class="cta-button danger" style="padding:8px 20px; font-size:0.85rem;">Schließen</button></form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; endif; ?>

                <?php elseif($_GET['tab'] == 'settings'): ?>
                    <h1>Einstellungen</h1>
                    <div class="request-card" style="max-width:500px;">
                        <form method="POST" class="sell-form" style="padding:0; border:none; background:none;">
                            <div class="form-group">
                                <label>Anzeigename</label>
                                <input type="text" name="name" value="<?php echo htmlspecialchars($_SESSION['user_name']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>E-Mail (Änderung nur via Support)</label>
                                <input type="text" value="<?php echo htmlspecialchars($user_email); ?>" disabled>
                            </div>
                            <div class="form-group">
                                <label>Neues Passwort (leer lassen zum Beibehalten)</label>
                                <input type="password" name="password">
                            </div>
                            <button type="submit" name="update_profile" class="cta-button">Speichern</button>
                        </form>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </main>

    <footer class="footer" style="margin-top:100px;">
        <div class="container footer-content">
            <p class="footer-text">&copy; 2026 Favour Technologies.</p>
        </div>
    </footer>

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