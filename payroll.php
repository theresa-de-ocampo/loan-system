<?php
	require_once "config/config.php";
	require_once "lib/verification.php";
	require_once "lib/database-handler.php";
	require_once "models/Cycle.php";
	require_once "models/Guarantor.php";

	$cycle = new Cycle();
	$guarantor = new Guarantor();
	$guarantors = $guarantor->getCurrentGuarantors();
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
	<link rel="stylesheet" type="text/css" href="css/tables.css" />
	<link rel="stylesheet" type="text/css" href="css/forms.css" />
	<link rel="stylesheet" type="text/css" href="css/media-queries.css" />
	<link rel="shortcut icon" type="image/x-icon" href="img/others/favicon.png" />
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
			<hr />
			<table id="principal-summation-tbl" class="display cell-border" width="100%">
				<thead>
					<tr>
						<th>Guarantor</th>
						<th>Total Amount Lent <span>(&#8369;)</span></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($guarantors as $g): ?>
						<tr>
							<td data-sort="<?php echo $g->lname; ?>"><?php echo $g->fname." ".$g->mname[0].". ".$g->lname; ?></td>
							<td><?php echo number_format($guarantor->getTotalAmountLent($g->guarantor_id), 2, ".", ","); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table><!-- #principal-summation-tbl -->
			<p class="pattern-bg"><span class="peso-sign">&#8369; </span><span class="amount"></span></p>
		</section><!-- #principal-summation -->

		<section id="interest-summation">
			<h3>Interest Summation</h3>
			<hr />
			<table id="interest-summation-tbl" class="display cell-border" width="100%">
				<thead>
					<th>Guarantor</th>
					<th>Total Interest Collected <span>(&#8369;)</span></th>
				</thead>
				<tbody>
					<?php foreach ($guarantors as $g): ?>
						<tr>
							<td data-sort="<?php echo $g->lname; ?>"><?php echo $g->fname." ".$g->mname[0].". ".$g->lname; ?></td>
							<td>
								<?php echo number_format($guarantor->getTotalInterestCollected($g->guarantor_id), 2, ".", ","); ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table><!-- #interest-summation-tbl -->
			<p class="pattern-bg"><span class="peso-sign">&#8369; </span><span class="amount"></span></p>
		</section><!-- #interest-summation -->
	</main><!-- #payroll -->

	<script src="js/jquery-3.6.0.min.js"></script>
	<script src="js/vertical-nav-bar.js"></script>
	<script src="js/datatables.min.js"></script>
	<script src="js/tables.js"></script>
	<script src="js/payroll.js"></script>
</body>
</html>