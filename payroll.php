<?php
	require_once "config/config.php";
	require_once "lib/verification.php";
	require_once "lib/database-handler.php";
	require_once "models/Cycle.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta name="author" content="Theresa De Ocampo" />
	<meta name="description" content="" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo COOPERATIVE; ?></title>
</head>
<body>
	<?php require_once "inc/vertical-nav-bar.html"; ?>

	<main id="payroll">
		<header>
			<i class="fas fa-bars"></i>
			<h2 class="text">Loans</h2>
		</header>

		<!-- Hidden, used as header for printing. -->
		<div id="coop-info-holder"><?php require_once "inc/print-header.php"; ?></div>
		<div id="cycle-holder"><?php echo $cycle->getCycleId(); ?></div>

		<section id="profits">
			<h3>Profits</h3>
		</section><!-- #profits -->

		<section id="principal-summation">
			<h3>Principal Summation</h3>
		</section><!-- #principal-summation -->

		<section id="interest-summation">
			<h3>Interest Summation</h3>
		</section><!-- #interest-summation -->
	</main><!-- #payroll -->

</body>
</html>