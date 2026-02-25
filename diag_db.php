<?php
require_once 'db.php';

try {
    $stmt = $pdo->query("DESCRIBE promotions");
    echo "Columns in 'promotions' table:<br>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")<br>";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>