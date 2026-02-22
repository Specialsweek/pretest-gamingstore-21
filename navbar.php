<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="navbar">
    <div class="container navbar-container">
        <a href="index.php" class="navbar-brand">Mirai Gear</a>
        <div class="navbar-menu">
            <?php
            $cartCount = 0;
            if (isset($_SESSION['cart'])) {
                $cartCount = array_sum($_SESSION['cart']);
            }
            ?>
            <a href="cart.php" class="nav-link">
                Cart (
                <?= $cartCount ?>)
            </a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="nav-text">Welcome, <strong>
                        <?= htmlspecialchars($_SESSION['username']) ?>
                    </strong></span>
                <a href="orders.php" class="nav-link">My Orders</a>
                <a href="logout.php" class="nav-link nav-btn-red">Logout</a>
            <?php else: ?>
                <a href="login.php" class="nav-link">Login</a>
                <a href="register.php" class="nav-link nav-btn-red">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>