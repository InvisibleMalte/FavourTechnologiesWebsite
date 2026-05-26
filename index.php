<?php 
    session_start();
    // Dummy Accounts beim Login ausschildern damit man sich einloggen kann:
        // Mitarbeiter: dummy-mitarbeiter@favour-technologies.de / Dummy123!
        // Kunde: dummy@favour-technologies.de / Dummy123
    // Handyansichten aller Nutzerbezogenen Seiten (Nutzerkonto, Mitarbeiter, Verwaltung) sowie alle Unterseiten davon überprüfen und optimieren 
    // mal sehen, vielleicht ja noch mehr ._.
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/styles/basics.css">
    <link rel="stylesheet" href="./assets/styles/index.css">
    <link rel="icon" href="./assets/images/logo.png">
    
    <title>Favour Technologies &bull; Startseite</title>

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
            <ul class="navbar-links-left-link navbar-links-active"><a class="navbar-links-left-link-a" href="/">Start</a></ul>
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
            
            <?php if(isset($_SESSION['user_id'])): ?>
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
            <?php else: ?>
            <ul class="navbar-links-right-link right"><a class="navbar-links-right-link-a right" href="login">Nutzerkonto</a></ul>
            <?php endif; ?>
        </div>

        <div class="navbar-user-mobile">
            <?php if(isset($_SESSION['user_id'])): ?>
            <div class="dropdown">
                <div class="nav-name" style="padding: 0;">
                    <svg class="mobile-user-icon" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                </div>
                <div class="dropdown-content">
                    <a href="nutzerkonto">Übersicht</a>
                    <?php if($_SESSION['user_rank'] == 'Mitarbeiter' || $_SESSION['user_rank'] == 'Admin'): ?><a href="mitarbeiter">Mitarbeiter</a><?php endif; ?>
                    <?php if($_SESSION['user_rank'] == 'Admin'): ?><a href="verwaltung">Verwaltung</a><?php endif; ?>
                    <a href="nutzerkonto?tab=settings">Einstellungen</a>
                    <a href="logout" style="color: #dc3545;">Abmelden</a>
                </div>
            </div>
            <?php else: ?>
            <a href="login" aria-label="Nutzerkonto">
                <svg class="mobile-user-icon" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
            </a>
            <?php endif; ?>
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
            <?php if(isset($_SESSION['user_id'])): ?>
                <ul class="navbar-links-right-link"><a class="navbar-links-right-link-a" href="nutzerkonto">Mein Konto</a></ul>
            <?php else: ?>
                <ul class="navbar-links-right-link"><a class="navbar-links-right-link-a" href="login">Anmelden</a></ul>
            <?php endif; ?>
        </nav>
    </div>

    <header class="hero">
        <div class="container">
            <h1 class="hero-title">Favour Technologies</h1>
            <p class="hero-subtitle">Wir kümmern uns persönlich um deine Technik – egal ob alt oder neu. Nachhaltig, schnell und zuverlässig.</p>
            <a href="#services" class="cta-button">Unsere Services</a>
        </div>
    </header>

    <section id="services" class="services">
        <div class="container">
            <div class="services-header">
                <h2 class="services-header-title">Unsere Dienstleistungen</h2>
                <p>Alles aus einer Hand für deine digitalen Begleiter.</p>
            </div>
            <div class="services-grid">
                <div class="service-card">
                    <h3 class="service-card-title">Ankauf</h3>
                    <p class="service-card-text">Wir kaufen deine alte Technik an, bereiten sie professionell auf und geben ihr ein zweites Leben.</p>
                    <a href="./verkaufen" class="service-card-link">Mehr erfahren &rarr;</a>
                </div>
                <div class="service-card">
                    <h3 class="service-card-title">Verkauf</h3>
                    <p class="service-card-text">Entdecke die neueste Technik und geprüfte Refurbished-Geräte zu fairen Preisen.</p>
                    <a href="./kaufen" class="service-card-link">Zum Shop &rarr;</a>
                </div>
                <div class="service-card">
                    <h3 class="service-card-title">Reparatur</h3>
                    <p class="service-card-text">Defektes Display oder schwacher Akku? Wir reparieren dein Gerät innerhalb von 48 Stunden.</p>
                    <a href="./reparieren" class="service-card-link">Reparatur buchen &rarr;</a>
                </div>
                <div class="service-card">
                    <h3 class="service-card-title">Abos</h3>
                    <p class="service-card-text">Durch unser Abo profitierst du von regelmäßigen Rabatten und Zugang zu den Echtzeitpreisen unserer Produkte weltweit.</p>
                    <a href="./abos" class="service-card-link">Abos entdecken &rarr;</a>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container footer-content">
            <p class="footer-text">&copy; 2026 Favour Technologies. Alle Rechte vorbehalten.</p>
            <p class="footer-disclaimer">Diese Website dient ausschließlich zu Demonstrationszwecken im Rahmen eines Schulprojekts. Alle Daten sind fiktiv.</p>
        </div>
    </footer>

    <div id="disclaimer-banner" class="info-banner">
        <span><strong>Hinweis:</strong> Diese Website ist ein Schulprojekt. Alle Produkte und Daten sind fiktiv.</span>
        <span class="close-banner" onclick="document.getElementById('disclaimer-banner').style.display='none'">X</span>
    </div>

    <script>
        const themeToggle = document.getElementById("theme-toggle"); // Desktop toggle
        const hamburger = document.getElementById("hamburger");
        const mobileNav = document.getElementById("mobile-nav");
        const iconSun = themeToggle.querySelector('.icon-sun');
        const iconMoon = themeToggle.querySelector('.icon-moon');

        hamburger.addEventListener("click", () => {
            mobileNav.classList.toggle("active");
        });

        // Close mobile nav when clicking outside
        document.addEventListener("click", (event) => {
            const isClickInsideMobileNav = mobileNav.contains(event.target);
            const isClickOnHamburger = hamburger.contains(event.target);
            const isMobileNavActive = mobileNav.classList.contains("active");

            if (isMobileNavActive && !isClickInsideMobileNav && !isClickOnHamburger) {
                mobileNav.classList.remove("active");
            }
        });

        // Dropdown toggle for mobile
        document.querySelectorAll('.dropdown').forEach(dropdown => {
            dropdown.addEventListener('click', function(e) {
                if (window.innerWidth <= 1100) {
                    this.classList.toggle('active');
                    e.stopPropagation();
                }
            });
        });
        document.addEventListener('click', () => {
            document.querySelectorAll('.dropdown').forEach(d => d.classList.remove('active'));
        });

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
            let theme = "light";
            if (document.body.classList.contains("dark-mode")) {
                theme = "dark";
            }
            localStorage.setItem("theme", theme);
            updateIcon();
        });
    </script>
</body>
</html>