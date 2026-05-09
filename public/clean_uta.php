<?php
$host = 'localhost';
$db   = 'easytax_uat';
$user = 'root';
$pass = 'Admin@123456';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");

    // Wipe application child tables first
    $pdo->exec("TRUNCATE TABLE application_logs;");
    $pdo->exec("TRUNCATE TABLE media;"); // spatie media library docs

    // Wipe applications
    $pdo->exec("TRUNCATE TABLE applications;");

    // ✅ Delete ONLY agents/marketers — keeps your ADMIN account safe
    $pdo->exec("DELETE FROM users WHERE role IN ('AGENT', 'agent', 'MARKETER', 'marketer');");

    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

    // Verify what's left
    $admins = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $apps   = $pdo->query("SELECT COUNT(*) FROM applications")->fetchColumn();

    echo "✅ SUCCESS: Applications and agents wiped.<br>";
    echo "Users remaining (admins only): <b>{$admins}</b><br>";
    echo "Applications remaining: <b>{$apps}</b><br><br>";
    echo "<h3>⚠️ SECURITY REMINDER:</h3>";
    echo "Please delete this <b>clean_uat.php</b> file from your server immediately.";

} catch(PDOException $e) {
    echo "❌ ERROR: " . $e->getMessage();
}
?>