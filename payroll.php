<?php
	require_once "config/config.php";
	require_once "lib/verification.php";
	require_once "lib/database-handler.php";
	require_once "lib/conversion-util.php";
	require_once "models/Cycle.php";
	require_once "models/Guarantor.php";
	require_once "models/Transaction.php";
	require_once "models/Payroll.php";

	$converter = new Converter();
	$cycle = new Cycle();
	$guarantor = new Guarantor();
	$payroll = new Payroll();
	$guarantors = $guarantor->getCurrentGuarantors();
	$profits = $payroll->getProfits();
	$rate = $profits["rate"];
	$interest = $profits["interest"];
	$flag = $payroll->getProcessedFlag();
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
	<link rel="stylesheet" type="text/css" href="css/image-upload.css" />
	<link rel="stylesheet" type="text/css" href="css/payroll.css" />
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

		<!-- Hidden. If today if November 30, this flag is used to check if the year-end records are already set. -->
		<div id="flag-holder"><?php echo $flag; ?></div>

		<section id="profits">
			<h3>Profits</h3>
				<table class="pattern-bg">
					<tr>
						<th>Interest</th>
						<td>&#8369; <?php echo number_format($interest, 2, ".", ","); ?></td>
					</tr>
					<tr>
						<th>10% Guarantor</th>
						<td>&#8369; <?php echo number_format($profits["ten_percent_return"], 2, ".", ","); ?></td>
					</tr>
					<tr>
						<th>Net Income</th>
						<td>&#8369; <?php echo number_format($profits["net_income"], 2, ".", ","); ?></td>
					</tr>
					<tr>
						<th>Per Share</th>
						<td>
							&#8369; 
							<?php
								$per_share = $converter->roundDown($profits["per_share"]);
								echo number_format($per_share, 2, ".", ","); 
							?>
						</td>
					</tr>
				</table><!-- .pattern-bg -->
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

		<section id="shares">
			<h3>Shares</h3>
			<hr />
			<table id="shares-tbl" class="display cell-border" width="100%">
				<thead>
					<tr>
						<th rowspan="2">Guarantor</th>
						<th rowspan="2">No. of Share</th>
						<th>Interest</th>
						<th><?php echo $rate * 100; ?>% Return</th>
						<th>Cut</th>
						<th>Total</th>
						<th>Principal Returned</th>
						<th>Grand Total</th>
						<th rowspan="2">Status</th>
					</tr>
					<tr>
						<th colspan="6"><span>(&#8369;)</span></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($guarantors as $g): $guarantor_id = (int)$g->guarantor_id; ?>
					<tr data-guarantor-id="<?php echo $guarantor_id; ?>">
						<td data-sort="<?php echo $g->lname; ?>">
							<?php echo $g->fname." ".$g->mname[0].". ".$g->lname; ?>
						</td>
						<td>
							<?php
								$number_of_share = $guarantor->getNumberOfShares($guarantor_id);
								echo $number_of_share;
							?>
						</td>
						<td>
							<?php
								$total_interest_collected = $guarantor->getTotalInterestCollected($guarantor_id);
								echo number_format($total_interest_collected, 2, ".", ",");
							?>
						</td>
						<td>
							<?php
								$ten_percent_return = $total_interest_collected * $rate;
								echo number_format($ten_percent_return, 2, ".", ",");
							?>
						</td>
						<td>
							<?php
								$cut = $number_of_share * $per_share;
								echo number_format($cut, 2, ".", ",");
							?>
						</td>
						<td>
							<?php
								$total = $ten_percent_return + $cut;
								echo number_format($total, 2, ".", ",");
							?>
						</td>
						<td>
							<?php
								$principal_returned = $guarantor->getTotalPrincipalReturned($guarantor_id);
								echo number_format($principal_returned, 2, ".", ",");
							?>
						</td>
						<td>
							<?php
								$grand_total = $total + $principal_returned;
								echo number_format($grand_total, 2, ".", ",");
							?>
						</td>
						<?php if (date("m") <= 11 && date("d") < 30): ?>
						<td>On Going</td>
						<?php else: ?>
							<?php if ($flag !== ""): ?>
								<td><a href="#"><?php echo $payroll->getShareStatus($guarantor_id); ?></a></td>
							<?php endif; ?>
						<?php endif; ?>
						<input type="hidden" name="g-id[]" value="<?php echo $guarantor_id; ?>" />
						<input type="hidden" name="g-amount[]" value="<?php echo $grand_total; ?>" />
					</tr>
					<?php
						$guarantorIds[] = $guarantor_id;
						$guarantorTotals[] = $grand_total;
						endforeach;
					?>
				</tbody>
			</table><!-- #shares-tbl -->
		</section><!-- #shares -->

		<form action="src/process-year-end.php" method="post">
			<input type="hidden" name="interest" value="<?php echo $interest; ?>" />
			<input type="hidden" name="processing-fee" value="20" />
			<input type="hidden" name="penalty" value="15" />

			<?php
				$serializedGuarantorIds = serialize($guarantorIds);
				$serializedGuarantorTotals = serialize($guarantorTotals);
			?>
			<input type="hidden" name="g-ids" value="<?php echo $serializedGuarantorIds; ?>" />
			<input type="hidden" name="g-amount" value="<?php echo $serializedGuarantorTotals; ?>" />
		</form>
	</main><!-- #payroll -->

	<script src="js/jquery-3.6.0.min.js"></script>
	<script src="js/vertical-nav-bar.js"></script>
	<script src="js/datatables.min.js"></script>
	<script src="js/tingle.min.js"></script>
	<script src="js/tables.js"></script>
	<script src="js/modal.js"></script>
	<script src="js/validation.js"></script>
	<script src="js/payroll.js"></script>
</body>
</html>