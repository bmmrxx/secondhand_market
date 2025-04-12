<?php
// Start session at the VERY TOP (before any output)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debugging - add this temporarily
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('navbar.php');

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// Database connection
require_once('connection.php');

// Initialize variables
$error_message = '';
$success_message = '';
$products = [];

try {
    $conn = (new Connection())->getConnection();

    // Fetch products for the current user
    $stmt = $conn->prepare("
        SELECT * FROM product 
        WHERE user_id = :user_id
        ORDER BY created_at DESC
    ");
    $stmt->execute([':user_id' => $_SESSION['user']['id']]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Database error: " . $e->getMessage();
}

// Process form submission (for delete, etc.)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle any POST actions here if needed
}
?>

<section class="py-3 py-md-5 mt-5 mb-10">
    <div class="container">
        <!-- Error message display -->
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <!-- Success message display -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['success_message']) ?>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <!-- My Products Section -->
        <div class="row gy-3 gy-md-4 gy-lg-0 align-items-lg-center">
            <div class="row-12 row-lg-6 row-xl-7 mb-5">
                <div class="row justify-content-xl-center">
                    <div class="row-12 col-xl-11">
                        <h2 class="mb-3">My Products</h2>
                        <p class="lead fs-4 text-secondary mb-3">Manage the items you're selling in the marketplace.</p>

                        <div class="row mt-4">
                            <?php if (!empty($products)): ?>
                                <?php foreach ($products as $product): ?>
                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100 border-0 shadow-sm">
                                            <img src="<?= htmlspecialchars($product['pictures'] ?? 'assets/images/profile-img-2.jpg') ?>"
                                                class="card-img-top"
                                                alt="<?= htmlspecialchars($product['name']) ?>"
                                                style="height: 200px; object-fit: cover;">
                                            <div class="card-body">
                                                <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                                                <p class="card-text text-secondary">
                                                    <?= htmlspecialchars(substr($product['description'] ?? '', 0, 100)) ?>
                                                    <?= (strlen($product['description'] ?? '') > 100 ? '...' : '') ?>
                                                </p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <span class="badge rounded-pill 
                                                            <?= ($product['status'] ?? '') === 'active' ? 'bg-success' : (($product['status'] ?? '') === 'pending' ? 'bg-warning text-dark' : 'bg-secondary') ?>">
                                                            <?= ucfirst($product['status'] ?? 'unknown') ?>
                                                        </span>
                                                        <span class="badge bg-info ms-2">$<?= number_format($product['price'] ?? 0, 2) ?></span>
                                                    </div>
                                                    <div>
                                                        <a href="edit_product.php?id=<?= $product['id'] ?>"
                                                            class="btn btn-primary btn-sm">Edit</a>
                                                        <a href="detail-product.php?id=<?= $product['id'] ?>"
                                                            class="btn btn-primary btn-sm">View</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        You haven't listed any products yet. <a href="add_product.php" class="alert-link">Add your first product</a>.
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex mt-4 mb-3">
                            <a href="add_product.php" class="btn btn-primary px-4 me-3">Add New Product</a>
                            <a href="index.php" class="btn btn-primary px-4">View all</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
require_once('footer.php');
?>