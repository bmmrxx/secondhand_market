<?php
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

require_once('connection.php');

// Process login only if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$username = $_POST['username'];
	$password = $_POST['password'];

	// Direct database query without separate class
	$conn = (new Connection())->getConnection();
	$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
	$stmt->execute([$username]);
	$user = $stmt->fetch(PDO::FETCH_ASSOC);

	if ($user && password_verify($password, $user['password'])) {
		$_SESSION['user'] = $user;
		header('Location: index.php');
		exit();
	} else {
		$error_message = "Invalid username or password";
	}
}

require_once('navbar.php');
?>

<section class="py-3 py-md-5 mt-5 mb-10">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-12 col-md-8 col-lg-6">
				<div class="rounded p-4 p-md-5 shadow-sm" style="background-color: #f8f9fa; border: 1px solid #515A47;">
					<h2 class="mb-4 text-center" style="color: #515A47;">Login</h2>

					<?php if (isset($error_message)): ?>
						<div class="alert alert-danger mb-4"><?= htmlspecialchars($error_message) ?></div>
					<?php endif; ?>

					<form method="post">
						<div class="mb-3">
							<label for="username" class="form-label" style="color: #515A47;">Username</label>
							<input type="text" class="form-control" id="username" name="username" required
								style="border-color: #515A47;">
						</div>

						<div class="mb-4">
							<label for="password" class="form-label" style="color: #515A47;">Password</label>
							<input type="password" class="form-control" id="password" name="password" required
								style="border-color: #515A47;">
						</div>

						<div class="d-grid mb-3">
							<button type="submit" class="btn py-2"
								style="background-color: #515A47; color: white;">
								Log In
							</button>
						</div>

						<div class="text-center mt-3">
							<p class="mb-0" style="color: #515A47;">Don't have an account?
								<a href="signup.php" class="text-decoration-none" style="color: #515A47; font-weight: 600;">
									Sign up here
								</a>
							</p>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>

<?php require_once('footer.php'); ?>