<?php
	require_once "config/config.php";
	session_start();
	if (isset($_SESSION["admin-verified"]))
		header("Location: home.php");

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta name="author" content="Theresa De Ocampo" />
	<meta name="author" content="Jesus Lopez" />
	<meta name="description" content="" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<link rel="stylesheet" type="text/css" href="css/forms.css" />
	<link rel="stylesheet" type="text/css" href="css/login.css" />
	<link rel="stylesheet" type="text/css" href="css/media-queries.css" />
	<link rel="shortcut icon" type="image/x-icon" href="img/others/favicon.png" />
	<title><?php echo COOPERATIVE; ?></title>
</head>
<body> 
	<div class="login-container">
		<div class="headline"><?php echo COOPERATIVE; ?></div>
		<form action="src/process-login.php" method="post">
			<div class="form-group">
				<input type="email" name="email" placeholder="email" required />
			</div><!-- form-group -->
			<div class="form-group">
				<input type="password" name="password" placeholder="password" class="password" required />
			</div><!-- form-group -->
			<div class="show-password">
				<input id="show-password" type="checkbox" />
				<label for="show-password"> show password </label>
			</div><!-- show-password -->
			<button type="submit" name="submit" class="btn">login</button>
		</form>
	</div><!-- login-container -->

	<script src="js/jquery-3.6.0.min.js"></script>
	<script src="js/show-password.js"></script>
</body>
</html>