<?php
// Datenbank-Konfiguration für SQLite
$db_dir = __DIR__ . '/../database';
$db_file = $db_dir . '/favour_tech.sqlite'; // Pfad zur SQLite-Datei

// Sicherstellen, dass das Verzeichnis existiert
if (!is_dir($db_dir)) {
    if (!@mkdir($db_dir, 0777, true)) {
        die("Fehler: Der Ordner '$db_dir' konnte nicht erstellt werden. Bitte erstelle ihn manuell.");
    }
}

try {
    // Verbindung herstellen
    $conn = new SQLite3($db_file);
    $conn->enableExceptions(true);

    // Tabellen erstellen
    $query = "
        CREATE TABLE IF NOT EXISTS sell_requests (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            device_name TEXT NOT NULL,
            device_condition TEXT NOT NULL,
            damages TEXT,
            email TEXT NOT NULL,
            description TEXT NOT NULL,
            status TEXT DEFAULT 'offen',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        CREATE TABLE IF NOT EXISTS repair_requests (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            device_name TEXT NOT NULL,
            repair_type TEXT NOT NULL,
            email TEXT NOT NULL,
            description TEXT NOT NULL,
            status TEXT DEFAULT 'offen',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        CREATE TABLE IF NOT EXISTS sell_messages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            request_id INTEGER NOT NULL,
            sender_email TEXT NOT NULL,
            message TEXT NOT NULL,
            is_employee INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (request_id) REFERENCES sell_requests(id) ON DELETE CASCADE
        );
        CREATE TABLE IF NOT EXISTS repair_messages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            request_id INTEGER NOT NULL,
            sender_email TEXT NOT NULL,
            message TEXT NOT NULL,
            is_employee INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (request_id) REFERENCES repair_requests(id) ON DELETE CASCADE
        );
        CREATE TABLE IF NOT EXISTS contact_requests (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT,
            email TEXT NOT NULL,
            subject TEXT NOT NULL,
            message TEXT NOT NULL,
            status TEXT DEFAULT 'offen',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );";

    $conn->exec($query);

} catch (Exception $e) {
    die("Datenbankfehler: " . $e->getMessage());
}
?>