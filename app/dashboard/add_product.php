<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once('connection.php');

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// Process form data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];


    try {
        // Check if form data is valid
        if (empty($name) || empty($description) || empty($price) || empty($pictures)) {
            $error_message = 'Please fill in all fields.';
        } else {
            // Insert product data into database
            $conn = (new Connection())->getConnection();
            $stmt = $conn->prepare("            
			INSERT INTO products (name, description, price, pictures, user_id, status) 
			VALUES (:name, :description, :price, :pictures, :user_id, 'active')
		");
            $stmt->execute([
                ':name' => $name,
                ':description' => $description,
                ':price' => $price,
                ':pictures' => $pictures,
                ':user_id' => $_SESSION['user']['id']
            ]);

            // Get the last product ID
            $lastInsertId = $conn->lastInsertId();

            // Make sure the file is set
            if (isset($_FILES['pictures']) && $__FILES['pictures']['error'] == UPLOAD_ERR_OK) {
                require_once('image-upload.php');
                $fileName = new FileUpload($conn, 'product-images', ['jpg', 'jpeg', 'png'], 'product', 'pictures', 'ID');
                $fileUpload->uploadPicture($_FILES['pictures'], $lastInsertId, 'pictures');
            }

            // // Redirect to product listing page
            // header('Location: my_products.php');
            // exit();
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        $error_message = "Error" . $e->getMessage();
    }
}

require_once('navbar.php');
?>

<section class="py-3 py-md-5 mt-5 mb-10">
    <div class="container">
        <div class="row gy-3 gy-md-4 gy-lg-0 align-items-lg-center">
            <div class="col-12 col-lg-6 col-xl-5">
                <img class="img-fluid rounded" loading="lazy" src="assets/images/profile-img-1.jpg" alt="My Products">
            </div>
            <div class="col-12 col-lg-6 col-xl-7">
                <div class="row justify-content-xl-center">
                    <div class="col-12 col-xl-11">
                        <h2 class="mb-3">Add New Product</h2>
                        <p class="lead fs-4 text-secondary mb-3">Add a new product to your marketplace.</p>

                        <form action="add_product.php" method="post">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name" class="form-label">Product Name</label>
                                        <input type="text" name="name" id="name" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="description" class="form-label">Product Description</label>
                                        <textarea name="description" id="description" class="form-control" rows="3" required></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="price" class="form-label">Product Price</label>
                                        <input type="number" name="price" id="price" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pictures" class="form-label">Product Images</label>
                                        <input type="file" name="pictures" id="pictures" class="form-control" required
                                            accept="image/*">
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg px-4">Add Product</button>
                        </form>

                        <div class="row gy-4 gy-md-0 gx-xxl-5X mt-5">
                            <div class="col-12 col-md-6">
                                <div class="d-flex">
                                    <div class="me-4 text-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#515A47" class="bi bi-box-seam" viewBox="0 0 16 16">
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="h4 mb-3">Account Security</h2>
                                        <p class="text-secondary mb-0">Your information is protected with industry-standard encryption.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="d-flex">
                                    <div class="me-4 text-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#515A47" class="bi bi-person-circle" viewBox="0 0 16 16">
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="h4 mb-3">Profile Management</h2>
                                        <p class="text-secondary mb-0">Keep your details up to date for a better marketplace experience.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once('footer.php'); ?>