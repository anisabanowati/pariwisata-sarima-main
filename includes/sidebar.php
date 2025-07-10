<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item">
            <a class="nav-link" href="dashboard.php">
                <span class="menu-title">Dashboard</span>
                <i class="mdi mdi-home menu-icon"></i>
            </a>
        </li>

        <?php
        // Get user role from session (assuming it's stored during login)
        $userRole = $_SESSION['role'] ?? 'user'; // Default to 'user' if not set

        // Show "Input Data Wisata" only for admin
        if ($userRole !== 'Admin'): ?>
            <li class="nav-item">
                <a class="nav-link" href="new_visitor.php">
                    <span class="menu-title">Input Data Wisata</span>
                    <i class="mdi mdi-account menu-icon"></i>
                </a>
            </li>
        <?php endif; ?>

        <li class="nav-item">
            <a class="nav-link" href="hasil_predik.php">
                <span class="menu-title">Hasil Predik</span>
                <i class="mdi mdi-account menu-icon"></i>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="manage_visitor.php">
                <span class="menu-title">Data Tempat Wisata</span>
                <i class="mdi mdi-account-multiple menu-icon"></i>
            </a>
        </li>

        <?php
        // Show "Management User" only for admin
        if ($userRole !== 'User'): ?>
            <li class="nav-item">
                <a class="nav-link" href="userregister.php">
                    <span class="menu-title">Management User</span>
                    <i class="mdi mdi-account-multiple menu-icon"></i>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</nav>