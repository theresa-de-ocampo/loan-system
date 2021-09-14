<?php
	require_once "config/config.php";
	require_once "lib/verification.php";
	require_once "lib/database-handler.php";
	require_once "models/Guarantor.php";

	$guarantor = new Guarantor();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta name="author" content="Theresa De Ocampo" />
	<meta name="description" content="" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="css/all.min.css" />
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<link rel="stylesheet" type="text/css" href="css/vertical-nav-bar.css" />
	<link rel="stylesheet" type="text/css" href="css/home.css" />
	<link rel="stylesheet" type="text/css" href="css/media-queries.css" />
	<link rel="shortcut icon" type="image/x-icon" href="img/favicon.png" />
	<title><?php echo COOPERATIVE; ?></title>
</head>
<body>
	<?php require_once "inc/vertical-nav-bar.html"; ?>

	<main>
		<header>
			<i class="fas fa-bars"></i>
			<h2 class="text">Home</h2>
		</header>

		<section id="dashboard">
			<h3>Dashboard</h3>
			<div class="grid-wrapper">
				<div class="grid-item">
					<div class="tally-label">
						<div class="fas fa-user-tie"></div>
						<h4>Guarantors</h4>
					</div>
					<p><?php echo $guarantor->getTotalCurrentGuarantors("", "home.php"); ?></p>
				</div><!-- .grid-item -->
				<div class="grid-item">
					<div class="tally-label">
						<div class="fas fa-user-tag"></div>
						<h4>Borrowers</h4>
					</div>
					<p>0</p>
				</div><!-- .grid-item -->
				<div class="grid-item">
					<div class="tally-label">
						<div class="fas fa-file-invoice"></div>
						<h4>Payments Today</h4>
					</div>
					<p><span>&#8369;</span> 0</p>
				</div><!-- .grid-item -->
				<div class="grid-item">
					<div class="tally-label">
						<div class="fas fa-sign-language"></div>
						<h4>Loans Today</h4>
					</div>
					<p>0</p>
				</div><!-- .grid-item -->
				<div class="grid-item">
					<div class="tally-label">
						<div class="fas fa-piggy-bank"></div>
						<h4>Savings</h4>
					</div>
					<p><span>&#8369;</span> <?php echo number_format($guarantor->getTotalSavings("", "home.php")); ?></p>
				</div><!-- .grid-item -->
				<div class="grid-item">
					<div class="tally-label">
						<div class="fas fa-crosshairs"></div>
						<h4>Uncollected Loans</h4>
					</div>
					<p><span>&#8369;</span> 0</p>
				</div><!-- .grid-item -->
				<div class="grid-item">
					<div class="tally-label">
						<div class="fas fa-vote-yea"></div>
						<h4>Collections</h4>
					</div>
					<p><span>&#8369;</span> 0</p>
				</div><!-- .grid-item -->
				<div class="grid-item">
					<div class="tally-label">
						<div class="fas fa-wallet"></div>
						<h4>Cash On Hand</h4>
					</div>
					<p><span>&#8369;</span> <?php echo number_format($guarantor->getTotalSavings("", "home.php")); ?></p>
				</div><!-- .grid-item -->
				<div class="grid-item">
					<div class="tally-label">
						<div class="fas fa-exclamation-triangle"></div>
						<h4>To Be Recovered</h4>
					</div>
					<p><span>&#8369;</span> 0</p>
				</div><!-- .grid-item -->
			</div><!-- .grid-wrapper -->
		</section><!-- #dashboard -->
	</main>

	<script src="js/vertical-nav-bar.js"></script>
</body>
</html>