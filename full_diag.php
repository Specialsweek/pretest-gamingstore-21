<?php
require_once 'db.php';

try {
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        $stmt = $pdo->query("DESCRIBE $table");
        echo "<h3>Table: $table</h3><ul>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $extra = ($row['Default'] === null ? "NO DEFAULT" : "Default: " . $row['Default']);
            if ($row['Null'] === 'NO')
                $extra .= ", NOT NULL";
            echo "<li>" . $row['Field'] . " (" . $row['Type'] . ") - $extra</li>";
        }
        echo "</ul>";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>