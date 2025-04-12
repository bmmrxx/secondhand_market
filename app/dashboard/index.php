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

<style>
    .fullscreen-center {
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding-top: 20px;
        box-sizing: border-box;
    }

    .image-container {
        width: 350px;
        height: 400px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
        transition: transform 0.3s ease;
    }

    .image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .image-wrapper:hover .image-container {
        transform: scale(1.05);
    }

    /* Added for product cards */
    .product-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 30px;
        padding: 20px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .product-card {
        width: 350px;
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
        transition: transform 0.3s ease;
    }

    .product-card:hover {
        transform: scale(1.05);
    }

    .product-details {
        padding: 20px;
    }

    .product-details h3 {
        margin-top: 0;
        color: #402C1A;
    }

    .price {
        font-weight: bold;
        color: #28a745;
        font-size: 1.2rem;
        margin: 10px 0;
    }

    .btn-buy {
        background-color: #402C1A;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .btn-buy:hover {
        background-color: #2b1e12;
    }

    .btn-buy a {
        color: white;
        text-decoration: none;
    }

    /* Header styles */
    .category-header {
        text-align: center;
        margin: 40px 0;
    }

    .category-header h1 {
        color: #402C1A;
        font-size: 2.5rem;
        margin-bottom: 10px;
    }

    .category-header p {
        font-size: 1.2rem;
        color: #666;
    }
</style>

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