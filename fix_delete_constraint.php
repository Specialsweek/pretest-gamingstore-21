<?php
require_once 'db.php';

echo "<h2>Mirai Gear: Database constraint repair</h2>";
echo "<p>Updating order_items foreign key to include CASCADE delete...</p>";

try {
    // 1. Identify the foreign key name
    // Usually it's order_items_ibfk_2 as seen in the error message, but we'll try to drop it specifically

    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

    // Attempt to drop the specific constraint mentioned in the error
    try {
        $pdo->exec("ALTER TABLE order_items DROP FOREIGN KEY order_items_ibfk_2");
        echo "<p style='color: green;'>&#10004; Dropped old constraint 'order_items_ibfk_2'.</p>";
    } catch (PDOException $e) {
        echo "<p style='color: orange;'>&#x2139; Note: Could not drop 'order_items_ibfk_2', it might have a different name. Attempting generic fix...</p>";
    }

    // 2. Add the new constraint with ON DELETE CASCADE
    $pdo->exec("ALTER TABLE order_items ADD CONSTRAINT fk_product_cascade 
                FOREIGN KEY (product_id) REFERENCES products(id) 
                ON DELETE CASCADE");

    echo "<p style='color: green;'>&#10004; Added new constraint with ON DELETE CASCADE.</p>";

    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    echo "<h3>Migration Complete!</h3>";
    echo "<p>You should now be able to delete products even if they have order history.</p>";
    echo "<p><a href='index.php'>Go back to Store</a></p>";
    echo "<p style='color: #ff0055;'><strong>Security Note:</strong> Please delete this file (fix_delete_constraint.php) after running it.</p>";

} catch (PDOException $e) {
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "<p style='color: red;'>&#10008; Error: " . $e->getMessage() . "</p>";
}
?>