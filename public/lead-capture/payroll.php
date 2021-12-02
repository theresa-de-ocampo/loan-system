<?php
	require_once "../../config/config.php";
	require_once "../../lib/generic-verification.php";
	require_once "../../lib/database-handler.php";
	require_once "../../lib/conversion-util.php";
	require_once "../../models/User.php";
	require_once "../../models/Cycle.php";
	require_once "../../models/Payroll.php";
	require_once "../../models/Roi.php";
	require_once "../../models/Guarantor.php";

	$user_id = $_SESSION["generic-user-verified"];
	$converter = new Converter();
	$cycle = new Cycle();
	$payroll = new Payroll();
	$roi = new Roi();
	$guarantor = new Guarantor();
	$shares = $roi->getSharesByGuarantor($user_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta name="author" content="Theresa De Ocampo" />
	<meta name="description" content="" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="../../css/all.min.css" />
	<link rel="stylesheet" type="text/css" href="../../css/datatables.min.css" />
	<link rel="stylesheet" type="text/css" href="../../css/style.css" />
	<link rel="stylesheet" type="text/css" href="../../css/vertical-nav-bar.css" />
	<link rel="stylesheet" type="text/css" href="../../css/tables.css" />
	<link rel="stylesheet" type="text/css" href="../../css/forms.css" />
	<link rel="shortcut icon" type="image/x-icon" href="../../img/others/favicon.png" />
	<title><?php echo COOPERATIVE; ?></title>
</head>
<body>
	<?php require_once "inc/vertical-nav-bar.php"; ?>

	<main>
		<header>
			<i class="fas fa-bars"></i>
			<h2 class="text">Payroll</h2>
		</header>

		<!-- Hidden, used as header for printing. -->
		<div id="coop-info-holder"><?php require_once "../../inc/print-header.php"; ?></div>
		<div id="cycle-holder"><?php echo $cycle->getCycleId(); ?></div>

		<section id="roi-history">
			<h3>Acquired ROI History</h3>
			<hr />
			<table id="roi-history-tbl" class="display cell-border" width="100%">
				<thead>
					<th>Year</th>
					<th>Per Share</th>
					<th>No. of Share</th>
					<th>Interest Collected</th>
					<th>10% Return</th>
					<th>Cut</th>
					<th>Total</th>
					<th>Principal Returned</th>
					<th>Grand Total</th>
					<th>Date & Time Claimed</th>
				</thead>
				<tbody>
					<?php foreach($shares as $s): $year = $s->closing_id; $profits = $payroll->getProfits($year); ?>
					<tr>
						<td><?php echo $year; ?></td>
						<td>
							<?php
								$per_share = $profits["per_share"];
								echo number_format($per_share, 2, ".", ",");
							?>
						</td>
						<td>
							<?php
								$number_of_share = $guarantor->getNumberOfShares($user_id, $year);
								echo $number_of_share;
							?>
						</td>
						<td>
							<?php
								$total_interest_collected = $guarantor->getTotalInterestCollected($user_id, $year);
								echo number_format($total_interest_collected, 2, ".", ",");
							?>
						</td>
						<td>
							<?php
								$ten_percent_return = $total_interest_collected * $profits["rate"];
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
								$principal_returned = $guarantor->getTotalPrincipalReturned($user_id, $year);
								echo number_format($principal_returned, 2, ".", ",");
							?>
						</td>
						<td>
							<?php
								$grand_total = $total + $principal_returned;
								echo number_format($grand_total, 2, ".", ",");
							?>
						</td>
						<?php if (is_null($s->date_time_claimed)): ?>
						<td><a>Not Yet Claimed</a>
						<?php else: ?>
						<td data-sort="<?php echo $s->date_time_claimed ?>">
							<?php echo $converter->shortToLongDateTime($s->date_time_claimed); ?>
						</td>
						<?php endif; ?>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table><!-- #roi-history-tbl -->
		</section><!-- #roi-history -->
	</main>

	<script src="../../js/jquery-3.6.0.min.js"></script>
	<script src="../../js/vertical-nav-bar.js"></script>
	<script src="../../js/datatables.min.js"></script>
	<script src="../../js/tables.js"></script>
	<script src="js/payroll.js"></script>
</body>
</html>