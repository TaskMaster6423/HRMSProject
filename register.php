<?php
session_start();
error_reporting(0);
include_once("includes/config.php");

// Check if user is already logged in
if(isset($_SESSION['userlogin']) && !empty($_SESSION['userlogin'])){
	header('location:index.php');
	exit();
}

$success = '';
$error = '';

// Process registration form
if(isset($_POST['register'])){
	$username = htmlspecialchars(trim($_POST['username']));
	$password = trim($_POST['password']);
	$confirmPassword = trim($_POST['confirm_password']);
	$firstName = htmlspecialchars(trim($_POST['first_name']));
	$lastName = htmlspecialchars(trim($_POST['last_name']));
	$email = htmlspecialchars(trim($_POST['email']));
	
	// Validate input
	if(empty($username) || empty($password) || empty($confirmPassword) || empty($firstName) || empty($lastName) || empty($email)){
		$error = '
		<div class="alert alert-danger alert-dismissible fade show" role="alert">
			<strong>Error!</strong> All fields are required.
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>';
	} elseif($password !== $confirmPassword){
		$error = '
		<div class="alert alert-danger alert-dismissible fade show" role="alert">
			<strong>Error!</strong> Passwords do not match.
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>';
	} elseif(strlen($password) < 8){
		$error = '
		<div class="alert alert-danger alert-dismissible fade show" role="alert">
			<strong>Error!</strong> Password must be at least 8 characters long.
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>';
	} else {
		// Check if username already exists
		$sql = "SELECT UserName FROM users WHERE UserName = :username";
		$query = $dbh->prepare($sql);
		$query->bindParam(':username', $username, PDO::PARAM_STR);
		$query->execute();
		
		if($query->rowCount() > 0){
			$error = '
			<div class="alert alert-danger alert-dismissible fade show" role="alert">
				<strong>Error!</strong> Username already exists.
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>';
		} else {
			// Hash password
			$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
			
			// Insert new user
			$sql = "INSERT INTO users (UserName, Password, FirstName, LastName, Email, Status, CreatedAt) VALUES (:username, :password, :firstname, :lastname, :email, 1, NOW())";
			$query = $dbh->prepare($sql);
			$query->bindParam(':username', $username, PDO::PARAM_STR);
			$query->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
			$query->bindParam(':firstname', $firstName, PDO::PARAM_STR);
			$query->bindParam(':lastname', $lastName, PDO::PARAM_STR);
			$query->bindParam(':email', $email, PDO::PARAM_STR);
			
			if($query->execute()){
				$success = '
				<div class="alert alert-success alert-dismissible fade show" role="alert">
					<strong>Success!</strong> Registration successful. You can now login.
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>';
			} else {
				$error = '
				<div class="alert alert-danger alert-dismissible fade show" role="alert">
					<strong>Error!</strong> Something went wrong. Please try again.
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>';
			}
		}
	}
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
		<meta name="description" content="Smarthr - Bootstrap Admin Template">
		<meta name="keywords" content="admin, estimates, bootstrap, business, corporate, creative, management, minimal, modern, accounts, invoice, html5, responsive, CRM, Projects">
		<meta name="author" content="Dreamguys - Bootstrap Admin Template">
		<meta name="robots" content="noindex, nofollow">
		<title>Register - HRMS admin</title>
		
		<!-- Favicon -->
		<link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
		
		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="assets/css/bootstrap.min.css">
		
		<!-- Fontawesome CSS -->
		<link rel="stylesheet" href="assets/css/font-awesome.min.css">
		
		<!-- Main CSS -->
		<link rel="stylesheet" href="assets/css/style.css">
		
		<style>
			.account-page {
				background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			}
			.account-box {
				background: rgba(255, 255, 255, 0.95);
				border-radius: 10px;
				box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
			}
			.account-title {
				color: #333;
				font-weight: 600;
			}
			.form-control {
				border-radius: 5px;
				padding: 12px;
			}
			.btn-primary {
				background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
				border: none;
				padding: 12px 30px;
				border-radius: 5px;
				transition: all 0.3s ease;
			}
			.btn-primary:hover {
				transform: translateY(-2px);
				box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
			}
			.password-toggle {
				position: absolute;
				right: 10px;
				top: 50%;
				transform: translateY(-50%);
				cursor: pointer;
			}
			.password-strength {
				margin-top: 5px;
				font-size: 12px;
			}
			.strength-weak { color: #dc3545; }
			.strength-medium { color: #ffc107; }
			.strength-strong { color: #28a745; }
		</style>
	</head>
	<body class="account-page">
		<!-- Main Wrapper -->
		<div class="main-wrapper">
			<div class="account-content">
				<div class="container">
					<!-- Account Logo -->
					<div class="account-logo">
						<a href="index.php"><img src="assets/img/logo2.png" alt="Company Logo"></a>
					</div>
					<!-- /Account Logo -->
					
					<div class="account-box">
						<div class="account-wrapper">
							<h3 class="account-title">Register</h3>
							<!-- Account Form -->
							<form method="POST" enctype="multipart/form-data">
								<div class="form-group">
									<label>Username</label>
									<input class="form-control" name="username" required type="text" autocomplete="username" placeholder="Choose a username">
								</div>
								
								<div class="form-group">
									<label>First Name</label>
									<input class="form-control" name="first_name" required type="text" placeholder="Enter your first name">
								</div>
								
								<div class="form-group">
									<label>Last Name</label>
									<input class="form-control" name="last_name" required type="text" placeholder="Enter your last name">
								</div>
								
								<div class="form-group">
									<label>Email</label>
									<input class="form-control" name="email" required type="email" placeholder="Enter your email">
								</div>
								
								<div class="form-group">
									<label>Password</label>
									<div class="position-relative">
										<input class="form-control" name="password" required type="password" autocomplete="new-password" placeholder="Choose a password" id="password" onkeyup="checkPasswordStrength()">
										<span class="password-toggle" onclick="togglePassword()">
											<i class="fa fa-eye"></i>
										</span>
									</div>
									<div class="password-strength" id="password-strength"></div>
								</div>
								
								<div class="form-group">
									<label>Confirm Password</label>
									<div class="position-relative">
										<input class="form-control" name="confirm_password" required type="password" autocomplete="new-password" placeholder="Confirm your password" id="confirm-password">
										<span class="password-toggle" onclick="toggleConfirmPassword()">
											<i class="fa fa-eye"></i>
										</span>
									</div>
								</div>
								
								<?php if($error){echo $error;}?>
								<?php if($success){echo $success;}?>
								
								<div class="form-group text-center">
									<button class="btn btn-primary account-btn" name="register" type="submit">Register</button>
								</div>
								
								<div class="text-center">
									<p>Already have an account? <a href="login.php">Login</a></p>
								</div>
							</form>
							<!-- /Account Form -->
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Main Wrapper -->
		
		<!-- jQuery -->
		<script src="assets/js/jquery-3.2.1.min.js"></script>
		
		<!-- Bootstrap Core JS -->
		<script src="assets/js/popper.min.js"></script>
		<script src="assets/js/bootstrap.min.js"></script>
		
		<!-- Custom JS -->
		<script src="assets/js/app.js"></script>
		
		<script>
			function togglePassword() {
				var x = document.getElementById("password");
				var icon = document.querySelector(".password-toggle i");
				if (x.type === "password") {
					x.type = "text";
					icon.classList.remove("fa-eye");
					icon.classList.add("fa-eye-slash");
				} else {
					x.type = "password";
					icon.classList.remove("fa-eye-slash");
					icon.classList.add("fa-eye");
				}
			}
			
			function toggleConfirmPassword() {
				var x = document.getElementById("confirm-password");
				var icon = document.querySelectorAll(".password-toggle i")[1];
				if (x.type === "password") {
					x.type = "text";
					icon.classList.remove("fa-eye");
					icon.classList.add("fa-eye-slash");
				} else {
					x.type = "password";
					icon.classList.remove("fa-eye-slash");
					icon.classList.add("fa-eye");
				}
			}
			
			function checkPasswordStrength() {
				var password = document.getElementById("password").value;
				var strengthDiv = document.getElementById("password-strength");
				
				// Reset strength indicator
				strengthDiv.innerHTML = "";
				strengthDiv.className = "password-strength";
				
				if(password.length === 0) {
					return;
				}
				
				// Calculate password strength
				var strength = 0;
				if(password.length >= 8) strength++;
				if(password.match(/[a-z]+/)) strength++;
				if(password.match(/[A-Z]+/)) strength++;
				if(password.match(/[0-9]+/)) strength++;
				if(password.match(/[^a-zA-Z0-9]+/)) strength++;
				
				// Display strength indicator
				if(strength <= 2) {
					strengthDiv.innerHTML = "Weak password";
					strengthDiv.classList.add("strength-weak");
				} else if(strength <= 4) {
					strengthDiv.innerHTML = "Medium password";
					strengthDiv.classList.add("strength-medium");
				} else {
					strengthDiv.innerHTML = "Strong password";
					strengthDiv.classList.add("strength-strong");
				}
			}
		</script>
	</body>
</html> 