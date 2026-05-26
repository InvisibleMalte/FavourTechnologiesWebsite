<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/styles/basics.css">
    <link rel="icon" href="../assets/images/logo.png">
    <title>Über uns &bull; Favour Technologies</title>
    <style>
        .about-section {
            padding: 80px 0;
        }
        .about-header {
            text-align: center;
            max-width: 800px;
            margin: 0 auto 60px;
        }
        .about-title {
            font-size: 3rem;
            margin-bottom: 20px;
        }
        .about-text {
            font-size: 1.2rem;
            line-height: 1.6;
            color: var(--text-muted);
        }
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 30px;
            margin-top: 50px;
        }
        .team-member {
            background: var(--card-bg);
            padding: 30px;
            border-radius: 20px;
            text-align: center;
            border: 1px solid var(--navbar-border);
            transition: var(--transition);
        }
        .team-member:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        body.dark-mode .team-member:hover {
            box-shadow: 0 0 35px rgba(0, 123, 255, 0.4);
            border-color: var(--primary-color);
        }
        .member-role {
            color: var(--primary-color);
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 10px;
            display: block;
        }
        .member-name {
            font-size: 1.4rem;
            margin-bottom: 10px;
        }
        .mission-box {
            margin-top: 80px;
            padding: 40px;
            background: rgba(0, 123, 255, 0.05);
            border-radius: 20px;
            border-left: 5px solid var(--primary-color);
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
            <ul class="navbar-links-right-link"><a class="navbar-links-right-link-a" href="./abos">Abos</a></ul>
            <ul class="navbar-links-right-link"><a class="navbar-links-right-link-a" href="./vergleich">Vergleich</a></ul>
            <ul class="navbar-links-right-link navbar-links-active"><a class="navbar-links-right-link-a" href="./ueber-uns">Über uns</a></ul>
            <ul class="navbar-links-right-link"><a class="navbar-links-right-link-a" href="./kontakt">Kontakt</a></ul>
            <ul class="navbar-links-right-link right"><a class="navbar-links-right-link-a right" href="./nutzerkonto">Nutzerkonto</a></ul>
        </div>
    </div>

    <main class="container about-section">
        <div class="about-header">
            <h1 class="about-title">Ein fiktives Startup</h1>
            <p class="about-text">
                Favour Technologies ist ein rein fiktives Startup, das im Rahmen eines Schulprojekts von vier Schülern ins Leben gerufen wurde. 
                Dieses Projekt dient dazu, die Planung, Entwicklung und das Design einer modernen Unternehmenspräsenz realitätsnah zu simulieren.
            </p>
        </div>

        <div class="team-grid">
            <div class="team-member">
                <span class="member-role">Managment,<br>CEO</span>
                <h3 class="member-name">Suhail Alraei</h3>
                <p class="footer-text">Verantwortlich für die Strategie und unsere Vision.</p>
            </div>
            <div class="team-member">
                <span class="member-role">Technik-Chef,<br>Entwicklung</span>
                <h3 class="member-name">Malte Bücking</h3>
                <p class="footer-text">Experte in der Reperatur und Entwicklung.</p>
            </div>
            <div class="team-member">
                <span class="member-role">Design,<br>Logistik</span>
                <h3 class="member-name">Nikita <br>Neb</h3>
                <p class="footer-text">Zuständig für unser Design und die Logistik</p>
            </div>
            <div class="team-member">
                <span class="member-role">Kundensupport,<br>Marketing</span>
                <h3 class="member-name">Favour John</h3>
                <p class="footer-text">Kümmert sich um euch und bringt uns zu euch.</p>
            </div>
        </div>

        <div class="mission-box">
            <h2>Unsere Mission</h2>
            <p class="about-text" style="font-size: 1.1rem; margin-top: 15px;">
                Die zentrale Aufgabe unseres Schulprojekts bestand darin, ein fiktives Startup von Grund auf zu konzipieren. Wir haben uns intensiv damit auseinandergesetzt, alle geschäftsrelevanten Faktoren – von der Marktanalyse und Logistik bis hin zur technischen Infrastruktur – eigenständig zu recherchieren und zu planen. Unser Ziel ist es, am Beispiel von Favour Technologies aufzuzeigen, wie ein moderner, nachhaltiger Kreislauf für Technik in der Theorie funktionieren kann.
            </p>
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
</body>
</html>