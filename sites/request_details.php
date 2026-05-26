<?php
session_start();
require_once '../database/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login");
    exit();
}

if (!isset($_GET['id']) || !isset($_GET['type'])) {
    header("Location: nutzerkonto");
    exit();
}

$req_id = $_GET['id'];
$type = $_GET['type'];
$user_email = $_SESSION['user_email'];
$is_staff = ($_SESSION['user_rank'] == 'Mitarbeiter' || $_SESSION['user_rank'] == 'Admin');

// Tabellen-Mapping
$tables = [
    'sell' => ['req' => 'sell_requests', 'msg' => 'sell_messages', 'title' => 'device_name', 'desc' => 'description'],
    'repair' => ['req' => 'repair_requests', 'msg' => 'repair_messages', 'title' => 'device_name', 'desc' => 'description'],
    'contact' => ['req' => 'contact_requests', 'msg' => 'contact_messages', 'title' => 'subject', 'desc' => 'message']
];

if (!array_key_exists($type, $tables)) {
    header("Location: nutzerkonto");
    exit();
}

$t = $tables[$type];

// Antrag abrufen
$stmt = $conn->prepare("SELECT * FROM {$t['req']} WHERE id = :id");
$stmt->bindValue(':id', $req_id, SQLITE3_INTEGER);
$req = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

if (!$req || (!$is_staff && $req['email'] !== $user_email)) {
    header("Location: nutzerkonto");
    exit();
}

// Antrag schließen
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['close_request']) && $req['status'] == 'offen') {
    $stmt = $conn->prepare("UPDATE {$t['req']} SET status = 'geschlossen' WHERE id = :id");
    $stmt->bindValue(':id', $req_id, SQLITE3_INTEGER);
    $stmt->execute();
    $req['status'] = 'geschlossen';
}

// Fallback-Name aus der Antrags-DB (hauptsächlich für Kontaktanfragen wichtig)
$requester_name_fallback = ($type == 'contact') ? $req['name'] : 'Kunde';

// Nachricht senden
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_message']) && $req['status'] == 'offen') {
    $msg = $_POST['message'];
    $is_employee = $is_staff ? 1 : 0;
    
    $stmt = $conn->prepare("INSERT INTO {$t['msg']} (request_id, sender_email, message, is_employee) VALUES (:rid, :email, :msg, :emp)");
    $stmt->bindValue(':rid', $req_id, SQLITE3_INTEGER);
    $stmt->bindValue(':email', $user_email, SQLITE3_TEXT);
    $stmt->bindValue(':msg', $msg, SQLITE3_TEXT);
    $stmt->bindValue(':emp', $is_employee, SQLITE3_INTEGER);
    $stmt->execute();
    header("Location: antrag?id=$req_id&type=$type");
    exit();
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../assets/styles/basics.css">
    <link rel="stylesheet" href="../assets/styles/verkaufen.css">
    <title>Antrag Details &bull; Favour Technologies</title>
    <style>
        .detail-card { background: var(--card-bg); border: 1px solid var(--navbar-border); border-radius: 20px; padding: 30px; margin-top: 40px; }
        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0; padding: 20px; background: var(--bg-color); border-radius: 15px; }
        .chat-container { margin-top: 40px; }
        .chat-box { max-height: 500px; overflow-y: auto; background: var(--bg-color); padding: 20px; border-radius: 15px; border: 1px solid var(--navbar-border); margin-bottom: 20px; display: flex; flex-direction: column; gap: 15px; }
        .msg { padding: 12px 18px; border-radius: 15px; max-width: 80%; font-size: 0.95rem; line-height: 1.4; overflow-wrap: break-word; }
        .msg.user { background: var(--navbar-border); color: var(--text-color); align-self: flex-start; border-bottom-left-radius: 2px; }
        .msg.staff { background: var(--primary-color); color: white; align-self: flex-end; border-bottom-right-radius: 2px; }
        .msg-meta { font-size: 0.7rem; opacity: 0.7; margin-bottom: 5px; display: block; }

        @media (max-width: 768px) {
            .detail-card { padding: 20px; }
            .info-grid { padding: 15px; grid-template-columns: 1fr; gap: 10px; }
            .msg { max-width: 90%; }
        }
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
                    <?php echo htmlspecialchars($_SESSION['user_name']); ?>
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
        <div style="margin-top: 40px; display: flex; align-items: center; gap: 20px;">
            <a href="<?php echo $is_staff ? 'mitarbeiter' : 'nutzerkonto?tab=requests'; ?>" style="text-decoration: none; font-size: 1.5rem;">←</a>
            <h1>Antrag: <?php echo htmlspecialchars($req[$t['title']]); ?></h1>
        </div>

        <div class="detail-card">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span class="tag" style="background:var(--primary-color); color:white; padding: 5px 15px; border-radius: 20px;"><?php echo strtoupper($type); ?></span>
                <div style="display: flex; align-items: center; gap: 15px;">
                    <span style="color: <?php echo ($req['status'] == 'offen') ? 'var(--primary-color)' : '#dc3545'; ?>; font-weight: 600;">Status: <?php echo ucfirst($req['status']); ?></span>
                    <?php if($req['status'] == 'offen'): ?>
                        <form method="POST" style="margin:0;" onsubmit="return confirm('Möchten Sie diesen Antrag wirklich schließen?');"><button type="submit" name="close_request" class="cta-button danger" style="padding:8px 15px; font-size:0.85rem;">Schließen</button></form>
                    <?php endif; ?>
                </div>
            </div>

            <div class="info-grid">
                <div><small style="color:var(--text-muted)">Erstellt am</small><br><strong><?php echo date('d.m.Y H:i', strtotime($req['created_at'])); ?></strong></div>
                <div><small style="color:var(--text-muted)">Kontakt</small><br><strong><?php echo htmlspecialchars($req['email']); ?></strong></div>
                <?php if(isset($req['device_condition'])): ?>
                    <div><small style="color:var(--text-muted)">Zustand</small><br><strong><?php echo htmlspecialchars($req['device_condition']); ?></strong></div>
                <?php endif; ?>
                <?php if(isset($req['repair_type'])): ?>
                    <div><small style="color:var(--text-muted)">Defekt</small><br><strong><?php echo htmlspecialchars($req['repair_type']); ?></strong></div>
                <?php endif; ?>
            </div>

            <div style="padding: 20px; border-left: 4px solid var(--primary-color); background: rgba(0,123,255,0.05); border-radius: 5px;">
                <small style="color:var(--text-muted)">Ursprüngliche Nachricht / Beschreibung:</small>
                <p style="margin-top: 10px;"><?php echo nl2br(htmlspecialchars($req[$t['desc']])); ?></p>
            </div>

            <div class="chat-container">
                <h3>Nachrichtenverlauf</h3>
                <div class="chat-box">
                    <?php 
                    $msgs = $conn->query("SELECT m.*, u.name as sender_name FROM {$t['msg']} m LEFT JOIN users u ON m.sender_email = u.email WHERE m.request_id = $req_id ORDER BY m.created_at ASC");
                    $has_msgs = false;
                    while($m = $msgs->fetchArray(SQLITE3_ASSOC)): 
                        $has_msgs = true;
                        $displayName = $m['sender_name'] ?: ($m['is_employee'] ? 'Support' : $requester_name_fallback);
                    ?>
                        <div class="msg <?php echo $m['is_employee'] ? 'staff' : 'user'; ?>">
                            <span class="msg-meta"><?php echo htmlspecialchars($displayName); ?> • <?php echo date('H:i', strtotime($m['created_at'])); ?></span>
                            <?php echo nl2br(htmlspecialchars($m['message'])); ?>
                        </div>
                    <?php endwhile; 
                    if(!$has_msgs) echo "<p style='text-align:center; color:var(--text-muted)'>Noch keine Nachrichten vorhanden.</p>";
                    ?>
                </div>

                <?php if($req['status'] == 'offen'): ?>
                    <form method="POST" class="sell-form" style="padding:0; border:none; background:none;">
                        <div class="form-group" style="margin-bottom: 10px;">
                            <textarea name="message" rows="3" placeholder="Ihre Nachricht..." required style="width:100%; border-radius: 15px;"></textarea>
                        </div>
                        <div style="display: flex; justify-content: flex-end;"><button type="submit" name="send_message" class="cta-button" style="width:auto;">Nachricht senden</button></div>
                    </form>
                <?php else: ?>
                    <div style="text-align:center; padding: 20px; border: 2px dashed var(--navbar-border); border-radius: 15px; color: var(--text-muted);">
                        Dieser Antrag wurde geschlossen. Eine Antwort ist nicht mehr möglich.
                    </div>
                <?php endif; ?>
            </div>
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

        const chatBox = document.querySelector('.chat-box');
        chatBox.scrollTop = chatBox.scrollHeight;
    </script>
</body>
</html>