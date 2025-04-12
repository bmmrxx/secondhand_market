<?php
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

require_once('navbar.php');
require_once('connection.php');

$error_message = '';
$success_message = '';

// Process signup form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$username = trim($_POST['name']);
	$email = trim($_POST['email']);
	$password = $_POST['password'];

	// Validate inputs
	if (empty($username) || empty($email) || empty($password)) {
		$error_message = "All fields are required";
	} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$error_message = "Invalid email format";
	} elseif (strlen($password) < 8) {
		$error_message = "Password must be at least 8 characters";
	} else {
		try {
			// Check if username or email already exists
			$stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
			$stmt->execute([$username, $email]);

			if ($stmt->rowCount() > 0) {
				$error_message = "Username or email already exists";
			} else {
				// Hash password
				$hashed_password = password_hash($password, PASSWORD_DEFAULT);

				// Insert new user
				$insert_stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
				$insert_stmt->execute([$username, $email, $hashed_password]);

				if ($insert_stmt->rowCount() > 0) {
					$success_message = "Account created successfully! Redirecting to login...";
					// Redirect after 2 seconds
					header("refresh:2; url=login.php");
				} else {
					$error_message = "Error creating account. Please try again.";
				}
			}
		} catch (PDOException $e) {
			$error_message = "Database error: " . $e->getMessage();
		}
	}
}
?>

<section class="py-3 py-md-5 mt-5 mb-10">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-12 col-md-8 col-lg-6">
				<div class="rounded p-4 p-md-5 shadow-sm" style="background-color: #f8f9fa; border: 1px solid #515A47;">
					<h2 class="mb-4 text-center" style="color: #515A47;">Create Your Account</h2>

					<?php if ($error_message): ?>
						<div class="alert alert-danger mb-4"><?= htmlspecialchars($error_message) ?></div>
					<?php endif; ?>

					<?php if ($success_message): ?>
						<div class="alert alert-success mb-4"><?= htmlspecialchars($success_message) ?></div>
					<?php endif; ?>

					<form method="post" class="formSignIn">
						<div class="mb-3">
							<label for="name" class="form-label" style="color: #515A47;">Create your username:</label>
							<input type="text" class="form-control" id="name" name="name"
								value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>"
								placeholder="Enter username" required style="border-color: #515A47;">
						</div>

						<div class="mb-3">
							<label for="email" class="form-label" style="color: #515A47;">Email:</label>
							<input type="email" class="form-control" id="email" name="email"
								value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"
								placeholder="example@gmail.com" required style="border-color: #515A47;">
						</div>

						<div class="mb-4">
							<label for="pasahitza" class="form-label" style="color: #515A47;">Enter Password:</label>
							<input type="password" class="form-control" id="pasahitza" name="password"
								placeholder="Enter password (min 8 characters)" required style="border-color: #515A47;">
						</div>

						<div class="d-grid mb-3">
							<button type="submit" class="btn py-2"
								style="background-color: #515A47; color: white;">
								Sign Up
							</button>
						</div>

						<div class="text-center mt-3">
							<p class="mb-0" style="color: #515A47;">Already have an account?
								<a href="login.php" class="text-decoration-none" style="color: #515A47; font-weight: 600;">
									Log in here
								</a>
							</p>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>

<?php

require_once('../includes/footer.php');
?>