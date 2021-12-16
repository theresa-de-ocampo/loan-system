<?php
	require_once "config/config.php";
	require_once "lib/verification.php";
	require_once "lib/database-handler.php";
	require_once "models/User.php";

	$id = $_SESSION["admin-verified"];
	$user = new User();
	$account = $user->getUser($id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta name="author" content="Theresa De Ocampo" />
	<meta name="description" content="" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="css/all.min.css" />
	<link rel="stylesheet" type="text/css" href="css/datatables.min.css" />
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<link rel="stylesheet" type="text/css" href="css/vertical-nav-bar.css" />
	<link rel="stylesheet" type="text/css" href="css/forms.css" />
	<link rel="stylesheet" type="text/css" href="css/image-upload.css" />
	<link rel="stylesheet" type="text/css" href="css/user-settings.css" />
	<link rel="stylesheet" type="text/css" href="css/media-queries.css" />
	<link rel="shortcut icon" type="image/x-icon" href="img/others/favicon.png" />
	<title><?php echo COOPERATIVE; ?></title>
</head>
<body>
	<?php require_once "inc/vertical-nav-bar.php"; ?>
	<main>
		<header>
			<i class="fas fa-bars"></i>
			<h2 class="text">User Settings</h2>
		</header>

		<section id="user-settings">
			<h3>User Settings</h3>
			<hr />
			<form action="src/edit-user.php" method="post" enctype="multipart/form-data" class="grid-wrapper" novalidate>
				<div class="grid-item">
						<label for="profile-picture">Profile Picture</label>
						<div id="drop-area">
							<img id="profile-picture" />
						</div><!-- #drop-area -->
						<input id="profile-picture" type="file" name="profile-picture" />
				</div><!-- .grid-item -->
				<div class="grid-item flex-wrapper">
					<div class="flex-item">
						<label for="username">Username</label>
						<input id="username" type="text" name="username" class="small" 
							value="<?php echo $account->username; ?>" required />
					</div><!-- .flex-item -->
					<div class="flex-item">
						<label for="email">Email</label>
						<input id="email" type="text" name="email" value="<?php echo $account->email; ?>" required />
					</div><!-- .flex-item -->
					<div class="flex-item">
						<label for="password">Password</label>
						<input id="password" type="password" name="password" required />
					</div><!-- .flex-item -->
					<div class="flex-item">
						<label for="confirm-password">Confirm Password</label>
						<input id="confirm-password" type="password" name="confirm-password" required />
					</div><!-- .flex-item -->
					<div class="flex-item solo">
						<input id="show-passwords" type="checkbox" required  class="show-password" />
						<label for="show-passwords">Show Passwords</label>
					</div><!-- .flex-item -->
					<input id="id" type="hidden" name="id" required class="short" readonly value="<?php echo $id; ?>" />
					<hr />
					<div id="button-container" class="flex-item">
						<button type="submit" name="edit" class="dt-button">Save Changes</button>
					</div>
				</div><!-- .grid-item.flex-wrapper -->
			</form><!-- .grid-wrapper -->
		</section><!-- #user-settings -->
	</main>

	<script src="js/jquery-3.6.0.min.js"></script>
	<script src="js/vertical-nav-bar.js"></script>
	<script src="js/image-upload.js"></script>
	<script src="js/show-password.js"></script>
	<script src="js/validation.js"></script>
	<script src="js/user-settings.js"></script>
</body>
</html>