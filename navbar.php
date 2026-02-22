<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header class="main-header">
    <div class="header-top">
        <div class="container header-container">
            <a href="index.php" class="navbar-brand">Mirai Gear</a>

            <div class="header-search">
                <form action="index.php" method="GET" class="search-form">
                    <span class="search-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </span>
                    <input type="text" name="search" placeholder="ค้นหาสินค้า..." class="search-input">
                </form>
            </div>

            <div class="header-actions">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="orders.php" class="action-btn" title="คำสั่งซื้อของฉัน">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </a>
                <?php endif; ?>

                <?php
                $cartCount = 0;
                if (isset($_SESSION['cart'])) {
                    $cartCount = array_sum($_SESSION['cart']);
                }
                ?>
                <a href="cart.php" class="action-btn cart-btn">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <path d="M16 10a4 4 0 0 1-8 0"></path>
                    </svg>
                    <?php if ($cartCount > 0): ?>
                        <span class="cart-badge"><?= $cartCount ?></span>
                    <?php endif; ?>
                </a>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="logout.php" class="auth-btn logout-btn">
                        ออกจากระบบ
                    </a>
                <?php else: ?>
                    <a href="login.php" class="auth-btn login-btn">
                        เข้าสู่ระบบ
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="header-bottom">
        <div class="container nav-container">
            <button class="category-btn">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
                หมวดหมู่
            </button>
            <nav class="main-nav">
                <a href="index.php" class="nav-item">หน้าแรก</a>
                <a href="#" class="nav-item">สินค้าทั้งหมด</a>
                <a href="#" class="nav-item">โปรโมชั่น</a>
                <a href="#" class="nav-item">บทความ</a>
                <a href="#" class="nav-item">ติดต่อเรา</a>
                <a href="#" class="nav-item">เกี่ยวกับเรา</a>
            </nav>
        </div>
    </div>
</header>