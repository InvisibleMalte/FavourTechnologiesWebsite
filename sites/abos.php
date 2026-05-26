<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/styles/basics.css">
    <link rel="stylesheet" href="../assets/styles/verkaufen.css">
    <link rel="icon" href="../assets/images/logo.png">
    <title>Abos &bull; Favour Technologies</title>
    <style>
        .construction-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 100px 20px;
            min-height: 50vh;
        }
        .construction-title {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }
        .construction-text {
            color: var(--text-muted);
            max-width: 600px;
            line-height: 1.6;
            font-size: 1.1rem;
            margin-bottom: 30px;
        }
        .donation-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 35px; /* Erhöhter Abstand (ca. 2 Zeilen) zwischen den Buttons */
            margin-top: 40px; /* Mehr Platz zum Text oben */
            flex-wrap: wrap;
        }
        /* Styles für den prominenten Spenden-Button */
        .cta-button.donate-button {
            background-color: var(--primary-color); /* Primärfarbe als Hintergrund */
            color: #fff; /* Weiße Schrift */
            border: 1px solid var(--primary-color); /* Rand in Primärfarbe */
            padding: 15px 25px; /* Konsistentes Padding */
            font-size: 1.1rem; /* Konsistente Schriftgröße */
            font-weight: 600; /* Konsistente Schriftstärke */
            border-radius: 10px; /* Konsistenter Border-Radius */
            cursor: pointer; /* Zeiger-Cursor */
            text-decoration: none; /* Entfernt die Standard-Unterstreichung */
            transition: var(--transition); /* Sanfte Übergänge */
        }
        .cta-button.donate-button:hover {
            transform: translateY(-10px); /* "Anheben"-Effekt */
            box-shadow: 0 20px 40px rgba(0,0,0,0.1); /* Schatten für den Lift */
        }
        body.dark-mode .cta-button.donate-button:hover {
            box-shadow: 0 0 35px rgba(0, 123, 255, 0.4); /* Stärkerer Glow im Dark Mode */
        }

        /* Styles für den sekundären "Zurück zur Startseite" Button */
        .cta-button.secondary-button {
            background: transparent;
            color: var(--text-color);
            border: 1px solid var(--navbar-border);
            padding: 15px 25px; /* Match donate button size */
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 10px; /* Match form buttons */
            cursor: pointer;
            text-decoration: none; /* Entfernt die Standard-Unterstreichung */
            transition: var(--transition);
        }
        .cta-button.secondary-button:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            border-color: var(--primary-color); /* Highlight border on hover */
        }
        body.dark-mode .cta-button.secondary-button:hover {
            box-shadow: 0 0 35px rgba(0, 123, 255, 0.4);
            border-color: var(--primary-color); /* Highlight border on hover */
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-links-left">
            <button id="theme-toggle" aria-label="Toggle Theme">
                <span class="icon-sun">☀️</span><span class="icon-moon">🌙</span>
            </button>
            <ul class="navbar-links-left-link"><a class="navbar-links-left-link-a" href="../">Start</a></ul>
            <ul class="navbar-links-left-link"><a class="navbar-links-left-link-a" href="./verkaufen">Verkaufen</a></ul>
            <ul class="navbar-links-left-link"><a class="navbar-links-left-link-a" href="./kaufen">Kaufen</a></ul>
            <ul class="navbar-links-left-link"><a class="navbar-links-left-link-a" href="./reparieren">Reparatur</a></ul>
        </div>
        <div class="navbar-logo">
            <a href="../"><img class="navbar-logo-image" src="../assets/images/logo.png" alt="Logo"></a>
        </div>
        <div class="navbar-links-right">
            <ul class="navbar-links-right-link navbar-links-active"><a class="navbar-links-right-link-a" href="./abos">Abos</a></ul>
            <ul class="navbar-links-right-link"><a class="navbar-links-right-link-a" href="./vergleich">Vergleich</a></ul>
            <ul class="navbar-links-right-link"><a class="navbar-links-right-link-a" href="./ueber-uns">Über uns</a></ul>
            <ul class="navbar-links-right-link"><a class="navbar-links-right-link-a" href="./kontakt">Kontakt</a></ul>
            <ul class="navbar-links-right-link right"><a class="navbar-links-right-link-a right" href="./nutzerkonto">Nutzerkonto</a></ul>
        </div>
    </div>

    <main class="content-wrapper">
        <div class="container">
            <section class="construction-wrapper">
                <h1 class="construction-title">Abos & Mitgliedschaften</h1>
                <p class="construction-text">
                    Unsere exklusiven Abo-Modelle befinden sich zurzeit noch in der Entwicklung. 
                    Bald profitierst du hier von Echtzeit-Preisvergleichen und besonderen Rabatten. Du kannst uns aber gerne mit einer Spende unterstützen.
                </p>
                <div class="donation-container">
                    <a href="https://paypal.me/maltebuecking" target="_blank" class="cta-button donate-button">Jetzt spenden</a>
                    <a href="../" class="cta-button secondary-button">Zurück zur Startseite</a>
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