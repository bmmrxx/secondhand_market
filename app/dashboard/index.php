<?php
session_start();
require_once('connection.php');
require_once('navbar.php');

// Define our category hierarchy (main categories => subcategories)
$categoryHierarchy = [
    'Clothes' => ['footwear', 'bottoms', 'tops'],
    'Objects' => ['home-decor', 'elektrical-devices', 'kitchen'],
    'Games' => ['card-games', 'video-games', 'board-games']
];


class Product
{
    private $conn;

    public function __construct()
    {
        $this->conn = (new Connection())->getConnection();
    }

    // Get products by category
    public function getProductsByCategory($category)
    {
        $stmt = $this->conn->prepare("
            SELECT * FROM product 
            WHERE category = :category
        ");
        $stmt->execute([':category' => $category]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

$productObj = new Product();

// Determine what we're viewing
$mainCategory = $_GET['main_category'] ?? null;
$subCategory = $_GET['sub_category'] ?? null;

// Validate the subcategory exists in our hierarchy
if ($subCategory && !in_array($subCategory, array_merge(...array_values($categoryHierarchy)))) {
    $subCategory = null;
}
?>

<div class="category-header">
    <h1 class="display-4 fw-bold">Discover Hidden Treasures!</h1>
    <p class="lead">Explore unique clothing, amazing objects, and fun games. Click a category to start your journey.</p>
</div>

<div class="container-fluid fullscreen-center">
    <?php if (!$mainCategory && !$subCategory): ?>
        <!-- Main Categories View -->
        <div class="row justify-content-center text-center g-5">
            <?php foreach (array_keys($categoryHierarchy) as $category): ?>
                <div class="col-12 col-md-4 d-flex justify-content-center">
                    <a href="?main_category=<?= urlencode($category) ?>" class="image-wrapper">
                        <div class="image-container">
                            <img src="assets/images/<?= strtolower($category) ?>.png" alt="<?= htmlspecialchars($category) ?>">
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

    <?php elseif ($mainCategory && isset($categoryHierarchy[$mainCategory]) && !$subCategory): ?>
        <!-- Subcategories View -->
        <div class="row justify-content-center text-center g-5">
            <?php foreach ($categoryHierarchy[$mainCategory] as $subCat): ?>
                <div class="col-12 col-md-4 d-flex justify-content-center">
                    <a href="?main_category=<?= urlencode($mainCategory) ?>&sub_category=<?= urlencode($subCat) ?>" class="image-wrapper">
                        <div class="image-container">
                            <img src="assets/images/<?= $subCat ?>.png" alt="<?= ucwords(str_replace('-', ' ', $subCat)) ?>">
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

    <?php elseif ($subCategory): ?>
        <!-- Products View -->
        <div class="product-container">
            <?php
            $products = $productObj->getProductsByCategory($subCategory);
            if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <div class="image-container">
                            <img src="<?= htmlspecialchars($product['image_url'] ?? 'assets/images/placeholder.png') ?>"
                                alt="<?= htmlspecialchars($product['name']) ?>">
                        </div>
                        <div class="product-details">
                            <h3><?= htmlspecialchars($product['name']) ?></h3>
                            <p><?= htmlspecialchars($product['description']) ?></p>
                            <p class="price">$<?= htmlspecialchars($product['price']) ?></p>
                            <button class="btn-buy">
                                <a href="detail-product.php?id=<?= htmlspecialchars($product['id']) ?>">Buy Now</a>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-info" style="width: 100%; text-align: center;">
                    No products found in this category.
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once('footer.php'); ?>