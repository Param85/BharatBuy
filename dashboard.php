<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/session.php';
redirectIfLoggedOut();
require_once __DIR__ . '/includes/header.php';
?>

<link rel="stylesheet" href="css/dashboard.css?v=<?= time(); ?>">

<section class="dashboard-hero">
    <div class="header-container" style="flex-direction: column; align-items: flex-start; max-width: 1200px; margin: 0 auto; width: 100%;">
        <h1>Hello, <?php echo htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]); ?>!</h1>
        <p>Compare prices and get the best value recommendations instantly.</p>
        
        <div class="search-container" style="width: 100%;">
            <input type="text" id="productSearch" placeholder="Search for electronics, clothes, etc...">
            <button id="searchBtn" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i> Find Best Deal</button>
        </div>
    </div>
</section>

<div id="loader">
    <i class="fas fa-spinner fa-spin fa-2x"></i>
    <p>Analyzing prices and ratings...</p>
</div>

<div id="results" class="results-grid">
    <!-- Results will be injected here by search.js -->
</div>

<script src="js/search.js?v=<?= time(); ?>"></script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
