<?php
require_once('connection.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class Product
{
    private $conn;

    public function __construct()
    {
        $this->conn = (new Connection())->getConnection();
    }

    public function getProductById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM product WHERE ID = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

// Get product ID from URL
$productId = isset($_GET['id']) ? $_GET['id'] : null;
$productObj = new Product();
$product = $productObj->getProductById($productId);

if (!$product) {
    echo "Product not found";
    exit;
}

require_once('navbar.php');
?>

</head>

<body>

    <div class="product-detail-container">
        <div class="product-detail-card">
            <div class="product-image-container">
                <img src="<?= htmlspecialchars($product['image_url'] ?? 'assets/images/placeholder.png') ?>"
                    alt="<?= htmlspecialchars($product['name']) ?>">
            </div>
            <div class="product-detail-info">
                <h2><?= htmlspecialchars($product['name']) ?></h2>
                <p><?= htmlspecialchars($product['description']) ?></p>
                <p class="price">$<?= number_format($product['price'], 2) ?></p>
                <button class="btn-buy">Add to Cart</button>
            </div>

            <?php if (isset($product['category'])): ?>
                <div class="product-category" style="margin-top: 20px;">
                    <span style="color: #666;">Category: </span>
                    <span style="font-weight: bold;"><?= ucwords(str_replace('-', ' ', htmlspecialchars($product['category']))) ?></span>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php require_once('footer.php'); ?>