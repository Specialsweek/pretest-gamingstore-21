<?php
require_once 'db.php';

echo "<h2>Mirai Gear: Robust Database Repair (v2)</h2>";
echo "<p>Inspecting database constraints on 'products' table...</p>";

try {
    // 1. Find all foreign keys pointing to the 'products' table
    $query = "
        SELECT 
            TABLE_NAME, 
            CONSTRAINT_NAME 
        FROM 
            INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
        WHERE 
            REFERENCED_TABLE_NAME = 'products' 
            AND TABLE_SCHEMA = :db_name
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute(['db_name' => $db]); // $db is defined in db.php
    $constraints = $stmt->fetchAll();

    if (empty($constraints)) {
        echo "<p style='color: blue;'>&#x2139; No foreign key constraints found pointing to 'products'.</p>";
    } else {
        echo "<p>Found " . count($constraints) . " constraint(s). Processing...</p>";

        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

        foreach ($constraints as $c) {
            $tableName = $c['TABLE_NAME'];
            $constraintName = $c['CONSTRAINT_NAME'];

            echo "<p>Processing <strong>$tableName</strong> (Constraint: $constraintName)...</p>";

            // 2. Drop the existing constraint
            try {
                $pdo->exec("ALTER TABLE `$tableName` DROP FOREIGN KEY `$constraintName`");
                echo "<p style='color: green;'>&nbsp;&nbsp;&nbsp;&nbsp;&#10004; Dropped old constraint.</p>";
            } catch (PDOException $e) {
                echo "<p style='color: orange;'>&nbsp;&nbsp;&nbsp;&nbsp;&#x2139; Could not drop constraint (might have been dropped already). Error: " . $e->getMessage() . "</p>";
            }

            // 3. Re-add with CASCADE
            // Note: We assume the column name is product_id based on standard project patterns
            try {
                $pdo->exec("ALTER TABLE `$tableName` ADD CONSTRAINT `$constraintName` 
                            FOREIGN KEY (product_id) REFERENCES products(id) 
                            ON DELETE CASCADE");
                echo "<p style='color: green;'>&nbsp;&nbsp;&nbsp;&nbsp;&#10004; Re-created with ON DELETE CASCADE.</p>";
            } catch (PDOException $e) {
                echo "<p style='color: red;'>&nbsp;&nbsp;&nbsp;&nbsp;&#10008; Failed to re-create constraint. Error: " . $e->getMessage() . "</p>";
            }
        }

        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    }

    echo "<h3>Robust Repair Complete!</h3>";
    echo "<p>You should now be able to delete products regardless of their order history.</p>";
    echo "<p><a href='index.php'>Go back to Store</a></p>";
    echo "<p style='color: #ff0055;'><strong>Security Note:</strong> Please delete this file (robust_fix_delete.php) after running it.</p>";

} catch (PDOException $e) {
    if (isset($pdo))
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "<p style='color: red;'>&#10008; System Error: " . $e->getMessage() . "</p>";
}
?>