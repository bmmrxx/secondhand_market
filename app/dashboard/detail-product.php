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

<style>
    html,
    body {
        margin: 0;
        padding: 0;
        overflow-x: hidden;
        font-family: Arial, sans-serif;
        background-color: #f8f9fa;
    }

    .product-detail-container {
        display: flex;
        justify-content: center;
        padding: 40px 20px;
        min-height: calc(100vh - 120px);
    }

    .product-detail-card {
        width: 90%;
        max-width: 1000px;
        display: flex;
        background: white;
        border-radius: 16px;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
        overflow: hidden;
    }

    .product-image-container {
        flex: 1;
        min-height: 500px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f1f1f1;
    }

    .product-image-container img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        max-height: 500px;
    }

    .product-detail-info {
        flex: 1;
        padding: 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .product-detail-info h2 {
        color: #402C1A;
        margin-top: 0;
        font-size: 2rem;
        margin-bottom: 20px;
    }

    .product-detail-info p {
        color: #555;
        line-height: 1.6;
        margin-bottom: 30px;
    }

    .price {
        font-weight: bold;
        color: #28a745;
        font-size: 1.8rem;
        margin: 20px 0;
    }

    .btn-buy {
        background-color: #402C1A;
        color: white;
        border: none;
        padding: 15px 30px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 1.1rem;
        transition: background-color 0.3s;
        width: 100%;
        max-width: 300px;
    }

    .btn-buy:hover {
        background-color: #2b1e12;
    }

    @media (max-width: 768px) {
        .product-detail-card {
            flex-direction: column;
        }

        .product-image-container {
            min-height: 300px;
        }
    }
</style>
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