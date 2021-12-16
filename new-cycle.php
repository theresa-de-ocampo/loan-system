<?php
	require_once "config/config.php";
	require_once "lib/verification.php";
	require_once "lib/database-handler.php";
	require_once "lib/conversion-util.php";
	require_once "models/Cycle.php";
	require_once "models/DataSubject.php";
	require_once "models/User.php";

	$converter = new Converter();
	$data_subject = new DataSubject();
	$user = new User();
	$data_subjects = $data_subject->getDataSubjects();
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
	<link rel="stylesheet" type="text/css" href="css/tingle.min.css" />
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<link rel="stylesheet" type="text/css" href="css/vertical-nav-bar.css" />
	<link rel="stylesheet" type="text/css" href="css/tables.css" />
	<link rel="stylesheet" type="text/css" href="css/forms.css" />
	<link rel="stylesheet" type="text/css" href="css/header-fields.css" />
	<link rel="stylesheet" type="text/css" href="css/new-cycle.css" />
	<link rel="stylesheet" type="text/css" href="css/media-queries.css" />
	<link rel="shortcut icon" type="image/x-icon" href="img/others/favicon.png" />
	<title><?php echo COOPERATIVE; ?></title>
</head>
<body>
	<?php require_once "inc/vertical-nav-bar.php"; ?>

	<main>
		<header>
			<i class="fas fa-bars"></i>
			<h2 class="text">Cycle</h2>
		</header>

		<form action="src/new-cycle.php" method="post">
			<?php
				$positions = ["treasurer", "asst-treasurer"];
				foreach ($positions as $position)
					require "inc/new-admin.php";
			?>
			
			<section>
				<h3>Agreement</h3>
				<p class="agreement">
					By clicking the button below, you agree to our <a href="agreement.php" target="_blank"><b>Terms</b> and that you have read our <b>Data Use Policy</b>, including our <b>Cookie Use</b></a>.
				</p>
				<button type="submit" name="submit" class="dt-button">Submit</button>
			</section>
		</form>
	</main>

	<script src="js/jquery-3.6.0.min.js"></script>
	<script src="js/vertical-nav-bar.js"></script>
	<script src="js/datatables.min.js"></script>
	<script src="js/tingle.min.js"></script>
	<script src="js/tables.js"></script>
	<script src="js/modal.js"></script>
	<script src="js/validation.js"></script>
	<script src="js/show-password.js"></script>
	<script src="js/new-cycle.js"></script>
</body>
</html>