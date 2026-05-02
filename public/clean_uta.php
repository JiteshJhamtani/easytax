<?php
// ==========================================
// EASYTAX LOCAL UAT CLEANUP SCRIPT (V2)
// Safely bypasses foreign keys to wipe test data
// ==========================================

$host = 'localhost';
$db   = 'easytax_bihar'; // Strictly targeting the local database!
$user = 'root';
$pass = 'Admin@123456';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Turn OFF the foreign key security guard
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");

    // 2. Wipe the child tables first (logs, documents, etc.)
    $pdo->exec("TRUNCATE TABLE application_logs;");
    
    // 3. Wipe the parent table
    $pdo->exec("TRUNCATE TABLE applications;");

    // 4. Turn the security guard back ON
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

    echo "✅ SUCCESS: All test applications and their history logs have been completely wiped from the local UAT server.<br><br>";
    echo "<h3>⚠️ SECURITY REMINDER:</h3>";
    echo "Please delete this <b>clean_uat.php</b> file from your UAT server immediately.";

} catch(PDOException $e) {
    echo "❌ ERROR: " . $e->getMessage();
}
?>