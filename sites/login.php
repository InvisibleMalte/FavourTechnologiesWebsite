<?php
session_start();
require_once '../database/db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: nutzerkonto");
    exit();
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['register'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
        $stmt->bindValue(':name', $name, SQLITE3_TEXT);
        $stmt->bindValue(':email', $email, SQLITE3_TEXT);
        $stmt->bindValue(':password', $password, SQLITE3_TEXT);

        try {
            $stmt->execute();
            $success = "Registrierung erfolgreich! Bitte melde dich an.";
        } catch (Exception $e) {
            $error = "E-Mail wird bereits verwendet.";
        }
    } elseif (isset($_POST['login'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindValue(':email', $email, SQLITE3_TEXT);
        $result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

        if ($result && $password === $result['password']) {
            $_SESSION['user_id'] = $result['id'];
            $_SESSION['user_name'] = $result['name'];
            $_SESSION['user_email'] = $result['email'];
            $_SESSION['user_rank'] = $result['rank'];
            header("Location: nutzerkonto");
            exit();
        } else {
            $error = "Ungültige E-Mail oder Passwort.";
        }
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
    <title>Login &bull; Favour Technologies</title>
    <style>
        .auth-container { max-width: 400px; margin: 60px auto; padding: 20px; }
        .auth-card { background: var(--card-bg); padding: 30px; border-radius: 20px; border: 1px solid var(--navbar-border); }
        .slider-nav { display: flex; margin-bottom: 20px; border-bottom: 1px solid var(--navbar-border); }
        .slider-btn { flex: 1; padding: 10px; border: none; background: none; color: var(--text-muted); cursor: pointer; font-family: inherit; font-weight: 600; }
        .slider-btn.active { color: var(--primary-color); border-bottom: 2px solid var(--primary-color); }
        .form-content { display: none; }
        .form-content.active { display: block; }
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
                <span class="nav-name">Nutzerkonto<span class="arrow-down"></span></span>
                <div class="dropdown-content">
                    <a href="login">Anmelden</a>
                </div>
            </li>
        </div>

        <div class="navbar-user-mobile">
            <a href="login" aria-label="Nutzerkonto">
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
            <ul class="navbar-links-right-link"><a class="navbar-links-right-link-a" href="login">Anmelden</a></ul>
        </nav>
    </div>

    <div class="auth-container">
        <?php if($error) echo "<div class='alert alert-error'>$error</div>"; ?>
        <?php if($success) echo "<div class='alert alert-success'>$success</div>"; ?>
        
        <div class="auth-card">
            <div class="slider-nav">
                <button class="slider-btn active" onclick="switchForm('login-form', this)">Login</button>
                <button class="slider-btn" onclick="switchForm('register-form', this)">Registrieren</button>
            </div>

            <div id="login-form" class="form-content active">
                <form method="POST" class="sell-form" style="padding:0; border:none; background:none;">
                    <div class="form-group">
                        <label>E-Mail</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Passwort</label>
                        <input type="password" name="password" required>
                    </div>
                    <button type="submit" name="login" class="cta-button">Anmelden</button>
                </form>
            </div>

            <div id="register-form" class="form-content">
                <form method="POST" class="sell-form" style="padding:0; border:none; background:none;">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>E-Mail</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Passwort</label>
                        <input type="password" name="password" required>
                    </div>
                    <button type="submit" name="register" class="cta-button">Konto erstellen</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function switchForm(formId, btn) {
            document.querySelectorAll('.form-content').forEach(f => f.classList.remove('active'));
            document.querySelectorAll('.slider-btn').forEach(b => b.classList.remove('active'));
            document.getElementById(formId).classList.add('active');
            btn.classList.add('active');
        }

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