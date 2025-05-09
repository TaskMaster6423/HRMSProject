<?php
	session_start();
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	include_once("includes/config.php");
	$wrongusername = ''; // Initialize variable
	$wrongpassword = ''; // Initialize variable
	
	try {
		// Check if user is already logged in
		if(isset($_SESSION['userlogin']) && !empty($_SESSION['userlogin'])){
			header('location:index.php');
			exit();
		}
		
		// Process login form
		if(isset($_POST['login'])){
			$username = htmlspecialchars(trim($_POST['username']));
			$password = trim($_POST['password']);
			$remember = isset($_POST['remember']) ? true : false;
			
			if(empty($username) || empty($password)){
				$wrongusername = '
				<div class="alert alert-danger alert-dismissible fade show" role="alert">
					<strong>Error!</strong> Please enter both username and password.
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>';
			} else {
				$sql = "SELECT UserName, Password, FirstName, LastName from users where UserName=:username";
				$query = $dbh->prepare($sql);
				$query->bindParam(':username', $username, PDO::PARAM_STR);
				$query->execute();
				$results = $query->fetchAll(PDO::FETCH_OBJ);
				
				if($query->rowCount() > 0){
					foreach ($results as $row) {
						$hashpass = $row->Password;
						$firstName = $row->FirstName;
						$lastName = $row->LastName;
					}
					
					//verifying Password
					if (password_verify($password, $hashpass)) {
						$_SESSION['userlogin'] = $username;
						$_SESSION['userFullName'] = $firstName . ' ' . $lastName;
						
						// Set remember me cookie if checked
						if($remember) {
							$token = bin2hex(random_bytes(32));
							setcookie('remember_token', $token, time() + (86400 * 30), "/"); // 30 days
							
							// Store token in database
							$sql = "UPDATE users SET remember_token = :token WHERE UserName = :username";
							$query = $dbh->prepare($sql);
							$query->bindParam(':token', $token, PDO::PARAM_STR);
							$query->bindParam(':username', $username, PDO::PARAM_STR);
							$query->execute();
						}
						
						header('location:index.php');
						exit();
					} else {
						$wrongpassword = '
						<div class="alert alert-danger alert-dismissible fade show" role="alert">
							<strong>Error!</strong> Invalid password.
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>';
					}
				} else {
					$wrongusername = '
					<div class="alert alert-danger alert-dismissible fade show" role="alert">
						<strong>Error!</strong> Invalid username.
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>';
				}
			}
		}
	} catch (PDOException $e) {
		$error = '
		<div class="alert alert-danger alert-dismissible fade show" role="alert">
			<strong>Database Error!</strong> ' . $e->getMessage() . '
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>';
	} catch (Exception $e) {
		$error = '
		<div class="alert alert-danger alert-dismissible fade show" role="alert">
			<strong>Error!</strong> ' . $e->getMessage() . '
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>';
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
		<title>Login - HRMS admin</title>
		
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
			.remember-me {
				margin: 15px 0;
			}
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
							<h3 class="account-title">Admin Login</h3>
							<!-- Account Form -->
							<form method="POST" enctype="multipart/form-data">
								<div class="form-group">
									<label>Username</label>
									<input class="form-control" name="username" required type="text" autocomplete="username" placeholder="Enter your username">
								</div>
								<?php if($wrongusername){echo $wrongusername;}?>
								
								<div class="form-group">
									<label>Password</label>
									<div class="position-relative">
										<input class="form-control" name="password" required type="password" autocomplete="current-password" placeholder="Enter your password" id="password">
										<span class="password-toggle" onclick="togglePassword()">
											<i class="fa fa-eye"></i>
										</span>
									</div>
								</div>
								<?php if($wrongpassword){echo $wrongpassword;}?>
								
								<div class="form-group remember-me">
									<div class="custom-control custom-checkbox">
										<input type="checkbox" class="custom-control-input" id="remember" name="remember">
										<label class="custom-control-label" for="remember">Remember me</label>
									</div>
								</div>
								
								<div class="form-group text-center">
									<button class="btn btn-primary account-btn" name="login" type="submit">Login</button>
								</div>
								
								<div class="text-center">
									<a class="text-muted" href="forgot-password.php">
										Forgot password?
									</a>
								</div>
									
								<div class="account-footer">
									<p>Having Trouble? <a target="_blank" href="https://github.com/MusheAbdulHakim/Smarthr---hr-payroll-project-employee-management-System/issues">Report an issue</a></p>
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
		</script>
	</body>
</html>