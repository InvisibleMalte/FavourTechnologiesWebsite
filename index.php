<?php 
    // ABOS
    // Dummy Konto als Benutzerkonto
    // Vergleich noch im Dev  
    // Mitarbeiterlink im Nutzerkonto wenn Mitarbeiter true ist in der DB
    // Mobile Navbar bug fixxen
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/styles/basics.css">
    <link rel="stylesheet" href="./assets/styles/index.css">
    <link rel="icon" href="/assets/images/logo.png">
    
    <title>Favour Technologies &bull; Startseite</title>

</head>
<body>
    <div class="navbar">
        <div class="navbar-links-left">
            <button id="theme-toggle" aria-label="Toggle Theme"></button>
            <ul class="navbar-links-left-link navbar-links-active"><a class="navbar-links-left-link-a" href="./">Start</a></ul>
            <ul class="navbar-links-left-link"><a class="navbar-links-left-link-a" href="./verkaufen">Verkaufen</a></ul>
            <ul class="navbar-links-left-link"><a class="navbar-links-left-link-a" href="./kaufen">Kaufen</a></ul>
            <ul class="navbar-links-left-link"><a class="navbar-links-left-link-a" href="./reparieren">Reparatur</a></ul>
        </div>
        <div class="navbar-logo">
            <a href="./"><img class="navbar-logo-image" src="./assets/images/logo.png" alt="Logo"></a>
        </div>
        <div class="navbar-links-right">
            <ul class="navbar-links-right-link"><a class="navbar-links-right-link-a" href="./abos">Abos</a></ul>
            <ul class="navbar-links-right-link"><a class="navbar-links-right-link-a" href="./vergleich">Vergleich</a></ul>
            <ul class="navbar-links-right-link"><a class="navbar-links-right-link-a" href="./ueber-uns">Über uns</a></ul>
            <ul class="navbar-links-right-link"><a class="navbar-links-right-link-a" href="./kontakt">Kontakt</a></ul>
            <ul class="navbar-links-right-link right"><a class="navbar-links-right-link-a right" href="./nutzerkonto">Nutzerkonto</a></ul>
        </div>
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
        const themeToggle = document.getElementById("theme-toggle");
        const sunIcon = '☀️';
        const moonIcon = '🌙';

        function updateIcon() {
            if (document.body.classList.contains("dark-mode")) {
                themeToggle.innerHTML = sunIcon;
            } else {
                themeToggle.innerHTML = moonIcon;
            }
        }

        // Initial setup
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