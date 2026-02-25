<?php
require_once 'db.php';
require_once 'products.php';
require_once 'auth_check.php'; // Session started here

$productObj = new Product($pdo);
$products = $productObj->getAllProducts();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mirai Gear</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <?php require_once 'navbar.php'; ?>


        <!-- Content starts here -->
    </div>

    <?php if (isAdmin()): ?>
        <div style="text-align: center; margin-bottom: 2rem;">
            <a href="create.php" class="btn">Add New Product</a>
        </div>
    <?php endif; ?>

    <div class="product-grid">
        <?php foreach ($products as $product): ?>
            <div class="product-card">
                <a href="product_details.php?id=<?= $product['id'] ?>">
                    <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>"
                        class="product-image" onerror="this.src='https://placehold.co/400x300?text=No+Image'">
                </a>
                <div class="product-info">
                    <span class="product-platform">
                        <?= htmlspecialchars($product['category']) ?>
                    </span>
                    <?php if ($product['stock'] <= 0): ?>
                        <div class="stock-badge badge-out">Out of Stock</div>
                    <?php elseif ($product['stock'] <= $product['low_stock_threshold']): ?>
                        <div class="stock-badge badge-low">Low Stock (<?= $product['stock'] ?> left)</div>
                    <?php else: ?>
                        <div class="stock-badge badge-in">In Stock</div>
                    <?php endif; ?>
                    <h3 class="product-title">
                        <a href="product_details.php?id=<?= $product['id'] ?>"
                            style="color: inherit; text-decoration: none;">
                            <?= htmlspecialchars($product['name']) ?>
                        </a>
                    </h3>
                    <p class="product-price">$
                        <?= number_format($product['price'], 2) ?>
                    </p>

                    <form method="POST" action="cart_action.php?action=add" class="quick-add-form">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="quick-add-btn" <?= ($product['stock'] <= 0) ? 'disabled' : '' ?>>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                            </svg>
                            <?= ($product['stock'] <= 0) ? 'Out of Stock' : 'Add to Cart' ?>
                        </button>
                    </form>

                    <?php if (isAdmin()): ?>
                        <div class="actions">
                            <a href="edit.php?id=<?= $product['id'] ?>" class="btn">Edit</a>
                            <a href="delete.php?id=<?= $product['id'] ?>" class="btn btn-danger" onclick="return confirm('Are
                    you sure?')">Delete</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const forms = document.querySelectorAll('.quick-add-form');

            forms.forEach(form => {
                form.addEventListener('submit', async (e) => {
                    e.preventDefault();

                    const btn = form.querySelector('.quick-add-btn');
                    const originalContent = btn.innerHTML;

                    // Visual feedback: Loading state
                    btn.disabled = true;
                    btn.style.opacity = '0.7';

                    const formData = new FormData(form);

                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (response.ok) {
                            const data = await response.json();
                            if (data.status === 'success') {
                                // Update Navbar Badge
                                if (typeof updateCartBadge === 'function') {
                                    updateCartBadge(data.cartCount);
                                }

                                // Visual feedback: Success state
                                btn.innerHTML = 'Added!';
                                btn.style.borderColor = 'var(--neon-purple)';
                                btn.style.color = 'var(--neon-purple)';

                                setTimeout(() => {
                                    btn.innerHTML = originalContent;
                                    btn.style.borderColor = '';
                                    btn.style.color = '';
                                    btn.disabled = false;
                                    btn.style.opacity = '1';
                                }, 1500);
                            }
                        }
                    } catch (error) {
                        console.error('Error adding to cart:', error);
                        btn.disabled = false;
                        btn.style.opacity = '1';
                    }
                });
            });
        });
    </script>
</body>

</html>