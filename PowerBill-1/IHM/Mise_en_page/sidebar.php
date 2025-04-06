<div class="sidebar">
    <div class="logo-container">
        <img src="assets/images/bolt-icon.png" alt="PowerBill" class="logo">
        <h2 class="brand-name">PowerBill</h2>
    </div>
    
    <div class="nav-menu">
        <ul class="nav-list">
            <?php foreach ($sidebar_data['nav_items'] as $item): ?>
                <li class="nav-item <?php echo $item['active'] ? 'active' : ''; ?>">
                    <a href="<?php echo htmlspecialchars($item['url']); ?>">
                        <i class="<?php echo htmlspecialchars($item['icon']); ?>"></i>
                        <span><?php echo htmlspecialchars($item['label']); ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    
    <div class="logout-container">
        <a href="<?php echo htmlspecialchars($sidebar_data['logout_url']); ?>" class="logout-btn">
            Logout
        </a>
    </div>
</div>

<link rel="stylesheet" href="../assets/css/sidebar.css">