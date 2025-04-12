<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once('connection.php');

?>

<section class="py-3 py-md-5 mt-5 mb-10">
    <div class="container">
        <div class="row gy-3 gy-md-4 gy-lg-0 align-items-lg-center mb-5">
            <div class="col-12 col-lg-6 col-xl-5">
                <img class="img-fluid rounded" loading="lazy" src="/assets/images/profile-img-1.jpg" alt="My Products">
            </div>
            <div class="col-12 col-lg-6 col-xl-7">
                <div class="row justify-content-xl-center">
                    <div class="col-12 col-xl-11">
                        <h2 class="mb-3">My Products</h2>
                        <p class="lead fs-4 text-secondary mb-3">Manage the items you're selling in the marketplace.</p>

                        <div class="row mt-4">
                            <?php if (!empty($products)): ?>
                                <?php foreach ($products as $product): ?>
                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100 border-0 shadow-sm">
                                            <?php
                                            // Handle product images (assuming pictures might be comma-separated)
                                            $first_image = explode(',', $product['pictures'])[0] ?? '/assets/images/profile-img-1.jpg';
                                            ?>
                                            <img src="<?= htmlspecialchars($first_image) ?>"
                                                class="card-img-top"
                                                alt="<?= htmlspecialchars($product['name']) ?>"
                                                style="height: 200px; object-fit: cover;">
                                            <div class="card-body">
                                                <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                                                <p class="card-text text-secondary">
                                                    <?= htmlspecialchars(substr($product['description'] ?? '', 0, 100)) ?>
                                                    <?= (strlen($product['description'] ?? '') > 100) ? '...' : '' ?>
                                                </p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <span class="badge rounded-pill 
															<?= $product['listing_status'] === 'active' ? 'bg-success' : ($product['listing_status'] === 'pending' ? 'bg-warning text-dark' : 'bg-secondary') ?>">
                                                            <?= ucfirst($product['listing_status']) ?>
                                                        </span>
                                                        <span class="badge bg-info ms-2">$<?= number_format($product['price'], 2) ?></span>
                                                    </div>
                                                    <div>
                                                        <a href="edit_product.php?id=<?= $product['id'] ?>"
                                                            class="btn btn-outline-custom btn-sm">Edit</a>
                                                        <a href="view_product.php?id=<?= $product['id'] ?>"
                                                            class="btn btn-primary-custom btn-sm">View</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="col-12">
                                    <div class="alert alert-primary-color border-primary-border bg-light">
                                        You haven't listed any products yet.
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex mt-4">
                            <a href="add_product.php" class="btn btn-primary-custom px-4 me-3">Add New Product</a>
                            <a href="my_products.php" class="btn btn-outline-custom px-4">View All</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
require_once('footer.php');
