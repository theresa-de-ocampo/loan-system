<?php
	require_once "config/config.php";
	require_once "lib/verification.php";
	require_once "lib/database-handler.php";
	require_once "models/Cycle.php";
	require_once "models/Guarantor.php";
	require_once "models/Borrower.php";
	require_once "models/Transaction.php";
	require_once "models/Loan.php";
	require_once "models/Interest.php";
	require_once "models/ProcessingFee.php";
	require_once "models/Penalty.php";

	$guarantor = new Guarantor();
	$borrower = new Borrower();
	$transaction = new Transaction();
	$loan = new Loan();
	$interest = new Interest();
	$processing_fee = new ProcessingFee();
	$penalty = new Penalty();
	$savings = $guarantor->getTotalSavings();
	$total_uncollected_loans = $loan->getTotalUncollectedLoans();
	$collections = $interest->getTotalInterestCollected() + $processing_fee->getTotalProcessingFeeCollected() + $penalty->getTotalPenaltiesCollected();
	$cash_on_hand = ($savings + $collections) - $total_uncollected_loans;
	$to_be_recovered = $collections - $cash_on_hand;
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
	<link rel="stylesheet" type="text/css" href="css/tally.css" />
	<link rel="stylesheet" type="text/css" href="css/media-queries.css" />
	<link rel="shortcut icon" type="image/x-icon" href="img/others/favicon.png" />
	<title><?php echo COOPERATIVE; ?></title>
</head>
<body>
	<?php require_once "inc/vertical-nav-bar.php"; ?>

	<main>
		<header>
			<i class="fas fa-bars"></i>
			<h2 class="text">Home</h2>
		</header>

		<section id="dashboard">
			<h3>Dashboard</h3>
			<div class="tally grid-wrapper">
				<div class="grid-item">
					<div class="tally-label">
						<div class="fas fa-user-tie"></div>
						<h4>Guarantors</h4>
					</div>
					<p><?php echo $guarantor->getTotalCurrentGuarantors(); ?></p>
				</div><!-- .grid-item -->
				<div class="grid-item">
					<div class="tally-label">
						<div class="fas fa-user-tag"></div>
						<h4>Borrowers</h4>
					</div>
					<p><?php echo $borrower->getTotalCurrentBorrowers(); ?></p>
				</div><!-- .grid-item -->
				<div class="grid-item">
					<div class="tally-label">
						<div class="fas fa-file-invoice"></div>
						<h4>Payments Today</h4>
					</div>
					<p><span>&#8369;</span> <?php echo number_format($transaction->getTotalPaymentsToday(), 2, ".", ","); ?></p>
				</div><!-- .grid-item -->
				<div class="grid-item">
					<div class="tally-label">
						<div class="fas fa-sign-language"></div>
						<h4>Loans Today</h4>
					</div>
					<p><?php echo $loan->getTotalLoansToday(); ?></p>
				</div><!-- .grid-item -->
				<div class="grid-item">
					<div class="tally-label">
						<div class="fas fa-piggy-bank"></div>
						<h4>Savings</h4>
					</div>
					<p><span>&#8369;</span> <?php echo number_format($savings, 2, ".", ","); ?></p>
				</div><!-- .grid-item -->
				<div class="grid-item">
					<div class="tally-label">
						<div class="fas fa-crosshairs"></div>
						<h4>Uncollected Loans</h4>
					</div>
					<p><span>&#8369;</span> <?php echo number_format($total_uncollected_loans, 2, ".", ","); ?></p>
				</div><!-- .grid-item -->
				<div class="grid-item">
					<div class="tally-label">
						<div class="fas fa-vote-yea"></div>
						<h4>Collections</h4>
					</div>
					<p><span>&#8369;</span> <?php echo number_format($collections, 2, ".", ","); ?></p>
				</div><!-- .grid-item -->
				<div class="grid-item">
					<div class="tally-label">
						<div class="fas fa-wallet"></div>
						<h4>Cash On Hand</h4>
					</div>
					<p><span>&#8369;</span> <?php echo number_format($cash_on_hand, 2, ".", ","); ?></p>
				</div><!-- .grid-item -->
				<div class="grid-item">
					<div class="tally-label">
						<div class="fas fa-exclamation-triangle"></div>
						<h4>To Be Recovered</h4>
					</div>
					<p>
						<span>&#8369;</span>
						<?php echo number_format(($to_be_recovered < 0 ? 0 : $to_be_recovered), 2, ".", ","); ?>
					</p>
				</div><!-- .grid-item -->
			</div><!-- .grid-wrapper -->
		</section><!-- #dashboard -->
	</main>

	<script src="js/jquery-3.6.0.min.js"></script>
	<script src="js/vertical-nav-bar.js"></script>
</body>
</html>