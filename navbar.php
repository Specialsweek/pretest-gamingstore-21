<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$cartCount = 0;
if (isset($_SESSION['cart'])) {
    $cartCount = array_sum($_SESSION['cart']);
}
?>

<nav class="navbar" id="navbar">
    <div class="nav-container">
        <a href="index.php" class="nav-logo">
            <span class="logo-icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"></path>
                </svg>
            </span>
            MIRAI GEAR
        </a>

        <div class="nav-menu" id="nav-menu">
            <ul class="nav-list">
                <li class="nav-item">
                    <a href="index.php" class="nav-link">Home</a>
                </li>
                <li class="nav-item">
                    <a href="index.php" class="nav-link">Shop</a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">Promotion</a>
                </li>
                <li class="nav-item">
                    <a href="contact.php" class="nav-link">Contact</a>
                </li>
                <?php if (isAdmin()): ?>
                    <li class="nav-item">
                        <a href="admin_contact.php" class="nav-link" style="color: var(--neon-cyan);">Manage Contact</a>
                    </li>
                <?php endif; ?>
                <li class="nav-item mobile-only" style="display: none;">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="profile.php" class="nav-link">Profile</a>
                        <br><br>
                        <a href="logout.php" class="nav-link" style="color: var(--accent);">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="nav-link" style="color: var(--neon-cyan);">Login</a>
                    <?php endif; ?>
                </li>
            </ul>
        </div>

        <div class="nav-actions">
            <a href="cart.php" class="nav-action-btn cart-btn" title="Cart">
                <div style="position: relative; display: flex;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <path d="M16 10a4 4 0 0 1-8 0"></path>
                    </svg>
                    <?php if ($cartCount > 0): ?>
                        <span class="nav-badge"><?= $cartCount ?></span>
                    <?php endif; ?>
                </div>
            </a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="profile.php" class="nav-action-btn desktop-only" title="Profile">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </a>
                <a href="logout.php" class="nav-auth-btn logout desktop-only">Logout</a>
            <?php else: ?>
                <a href="login.php" class="nav-auth-btn login desktop-only">Login</a>
            <?php endif; ?>

            <button class="nav-toggle" id="nav-toggle" aria-label="Toggle Navigation">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div>
</nav>

<script>
    // Header Scroll Effect
    window.addEventListener('scroll', function () {
        const navbar = document.getElementById('navbar');
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    // Mobile Menu Toggle
    const navToggle = document.getElementById('nav-toggle');
    const navMenu = document.getElementById('nav-menu');

    if (navToggle && navMenu) {
        navToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            navMenu.classList.toggle('active');
            navToggle.classList.toggle('active');
            document.body.style.overflow = navMenu.classList.contains('active') ? 'hidden' : 'auto';
        });

        // Close menu when clicking outside
        document.addEventListener('click', (e) => {
            if (navMenu.classList.contains('active') && !navMenu.contains(e.target) && !navToggle.contains(e.target)) {
                navMenu.classList.remove('active');
                navToggle.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        });

        // Close menu when clicking links
        const navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                navMenu.classList.remove('active');
                navToggle.classList.remove('active');
                document.body.style.overflow = 'auto';
            });
        });
    }
</script>