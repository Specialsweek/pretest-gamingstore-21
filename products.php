<?php
require_once 'db.php';

class Product
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllProducts()
    {
        $stmt = $this->pdo->query("SELECT * FROM products ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public function getProductById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function createProduct($name, $price, $image, $category, $description, $stock = 0, $low_stock_threshold = 5)
    {
        $sql = "INSERT INTO products (name, price, image, category, description, stock, low_stock_threshold) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$name, $price, $image, $category, $description, $stock, $low_stock_threshold]);
    }

    public function updateProduct($id, $name, $price, $image, $category, $description, $stock, $low_stock_threshold)
    {
        $sql = "UPDATE products SET name = ?, price = ?, image = ?, category = ?, description = ?, stock = ?, low_stock_threshold = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$name, $price, $image, $category, $description, $stock, $low_stock_threshold, $id]);
    }

    public function deleteProduct($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>