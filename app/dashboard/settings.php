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
$errors = [];
$success_message = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validate username
    if (empty($username)) {
        $errors[] = "Username is required";
    } elseif (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        $errors[] = "Username must be 3-20 characters (letters, numbers, underscores only)";
    }

    // Validate email
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    // Validate names
    if (!empty($firstname) && !preg_match('/^[a-zA-Z \'-]{2,50}$/', $firstname)) {
        $errors[] = "First name contains invalid characters";
    }

    if (!empty($lastname) && !preg_match('/^[a-zA-Z \'-]{2,50}$/', $lastname)) {
        $errors[] = "Last name contains invalid characters";
    }

    // Validate password if provided
    $update_password = !empty($password);
    if ($update_password) {
        if (strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters";
        } elseif (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            $errors[] = "Password must contain at least one uppercase letter, one lowercase letter, and one number";
        }
    }

    // If no errors, update database
    if (empty($errors)) {
        try {
            $conn = (new Connection())->getConnection();

            // Prepare the update query
            if ($update_password) {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $sql = "UPDATE user SET 
                        username = :username, 
                        email = :email, 
                        firstname = :firstname, 
                        lastname = :lastname, 
                        password = :password 
                        WHERE id = :user_id";
            } else {
                $sql = "UPDATE user SET 
                        username = :username, 
                        email = :email, 
                        firstname = :firstname, 
                        lastname = :lastname 
                        WHERE id = :user_id";
            }

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':firstname', $firstname);
            $stmt->bindParam(':lastname', $lastname);
            $stmt->bindParam(':user_id', $_SESSION['user']['id']);

            if ($update_password) {
                $stmt->bindParam(':password', $password_hash);
            }

            $stmt->execute();

            // Update session data
            $_SESSION['user']['username'] = $username;
            $_SESSION['user']['email'] = $email;
            $_SESSION['user']['firstname'] = $firstname;
            $_SESSION['user']['lastname'] = $lastname;

            // Set success message and redirect
            $_SESSION['success_message'] = "Profile updated successfully!";
            header("Location: settings.php");
            exit();
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}

// Check for success message from redirect
$success_message = $_SESSION['success_message'] ?? null;
unset($_SESSION['success_message']); // Clear after displaying
?>

<section class="py-3 py-md-5 mt-5 mb-10">
    <div class="container">
        <div class="row gy-3 gy-md-4 gy-lg-0 align-items-lg-center">
            <div class="col-12 col-lg-6 col-xl-5">
                <img class="img-fluid rounded" loading="lazy" src="assets/images/settings-img-1.jpg" alt="User Settings">
            </div>
            <div class="col-12 col-lg-6 col-xl-7">
                <div class="row justify-content-xl-center">
                    <div class="col-12 col-xl-11">
                        <h2 class="mb-3">Account Settings</h2>
                        <p class="lead fs-4 text-secondary mb-3">Manage your personal information and account preferences.</p>

                        <?php if (isset($_SESSION['user'])): ?>
                            <p class="mb-4">You're logged in as <strong><?= htmlspecialchars($_SESSION['user']['username']) ?></strong>. Update your details below.</p>
                        <?php endif; ?>

                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul>
                                    <?php foreach ($errors as $error): ?>
                                        <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($success_message)): ?>
                            <div class="alert alert-success">
                                <?= htmlspecialchars($success_message) ?>
                            </div>
                        <?php endif; ?>

                        <form action="settings.php" method="post">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" name="username" id="username" class="form-control" value="<?= htmlspecialchars($_SESSION['user']['username'] ?? '') ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($_SESSION['user']['email'] ?? '') ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="firstname" class="form-label">First Name</label>
                                        <input type="text" name="firstname" id="firstname" class="form-control" value="<?= htmlspecialchars($_SESSION['user']['firstname'] ?? '') ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="lastname" class="form-label">Last Name</label>
                                        <input type="text" name="lastname" id="lastname" class="form-control" value="<?= htmlspecialchars($_SESSION['user']['lastname'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-4">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" id="password" class="form-control" placeholder="Enter new password">
                                <small class="text-muted">Leave blank to keep current password</small>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg px-4">Save Changes</button>
                        </form>

                        <div class="row gy-4 gy-md-0 gx-xxl-5X mt-5">
                            <div class="col-12 col-md-6">
                                <div class="d-flex">
                                    <div class="me-4 text-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#515A47" class="bi bi-shield-lock" viewBox="0 0 16 16">
                                            <path d="M5.338 1.59a61.44 61.44 0 0 0-2.837.856.481.481 0 0 0-.328.39c-.554 4.157.726 7.19 2.253 9.188a10.725 10.725 0 0 0 2.287 2.233c.346.244.652.42.893.533.12.057.218.095.293.118a.55.55 0 0 0 .101.025.615.615 0 0 0 .1-.025c.076-.023.174-.061.294-.118.24-.113.547-.29.893-.533a10.726 10.726 0 0 0 2.287-2.233c1.527-1.997 2.807-5.031 2.253-9.188a.48.48 0 0 0-.328-.39c-.651-.213-1.75-.56-2.837-.855C9.552 1.29 8.531 1.067 8 1.067c-.53 0-1.552.223-2.662.524zM5.072.56C6.157.265 7.31 0 8 0s1.843.265 2.928.56c1.11.3 2.229.655 2.887.87a1.54 1.54 0 0 1 1.044 1.262c.596 4.477-.787 7.795-2.465 9.99a11.775 11.775 0 0 1-2.517 2.453 7.159 7.159 0 0 1-1.048.625c-.28.132-.581.24-.829.24s-.548-.108-.829-.24a7.158 7.158 0 0 1-1.048-.625 11.777 11.777 0 0 1-2.517-2.453C1.928 10.487.545 7.169 1.141 2.692A1.54 1.54 0 0 1 2.185 1.43 62.456 62.456 0 0 1 5.072.56z" />
                                            <path d="M9.5 6.5a1.5 1.5 0 0 1-1 1.415l.385 1.99a.5.5 0 0 1-.491.595h-.788a.5.5 0 0 1-.49-.595l.384-1.99a1.5 1.5 0 1 1 2-1.415z" />
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
                                            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                                            <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z" />
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