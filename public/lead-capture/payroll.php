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
	require_once "../../models/Salary.php";
	require_once "../../models/Administrator.php";
	require_once "../../models/Fund.php";

	$user_id = $_SESSION["generic-user-verified"];
	$converter = new Converter();
	$cycle = new Cycle();
	$payroll = new Payroll();
	$roi = new Roi();
	$guarantor = new Guarantor();
	$salary = new Salary();
	$administrator = new Administrator();
	$fund = new Fund();
	$shares = $roi->getSharesByGuarantor($user_id);
	$salaries = $salary->getSalariesByGuarantor($user_id);
	$funds = $fund->getFundsByGuarantor($user_id);

	function checkClaimStatus($object) {
		$converter = new Converter();
		if (is_null($object->date_time_claimed))
			$column = "<td><a>Not Yet Claimed</a>";
		else {
			$formatted_date_time = $converter->shortToLongDateTime($object->date_time_claimed);
			$column = "<td data-sort='$object->date_time_claimed'>$formatted_date_time</td>";
		}
		return $column;
	}
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
	<link rel="stylesheet" type="text/css" href="../../css/media-queries.css" />
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
					<tr>
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
					</tr>
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
						<?php echo checkClaimStatus($s); ?>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table><!-- #roi-history-tbl -->
		</section><!-- #roi-history -->

		<section id="salary-history">
			<h3>Salary History</h3>
			<hr />
			<table id="salary-history-tbl" class="display cell-border" width="100%">
				<thead>
					<tr>
						<th>Year</th>
						<th>Position</th>
						<th>Earnings</th>
						<th>Date & Time Claimed</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($salaries as $s): ?>
					<tr>
						<td><?php echo $s->closing_id; ?></td>
						<td><?php echo $administrator->getPosition($s->closing_id, $user_id); ?></td>
						<td><?php echo $s->amount; ?></td>
						<?php echo checkClaimStatus($s); ?>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table><!-- #salary-history-tbl -->
		</section><!-- #salary-history -->

		<section id="funds-history">
			<h3>History of Claimed Funds</h3>
			<hr />
			<table id="funds-history-tbl" class="display cell-border" width="100%">
				<thead>
					<tr>
						<th>Year</th>
						<th>Position</th>
						<th>Earnings</th>
						<th>Purpose</th>
						<th>Date & Time Claimed</th>
					</tr>
				</thead>
				<tbody>
					<?php
						foreach ($funds as $f):
							// If not an employee during that cycle, just skip.
							if (!$administrator->getPosition($f->closing_id, $user_id))
								continue;
					?>
					<tr>
						<td><?php echo $f->closing_id; ?></td>
						<td><?php echo $administrator->getPosition($f->closing_id, $user_id); ?></td>
						<td><?php echo number_format($f->amount, 2, ".", ","); ?></td>
						<?php if (is_null($f->purpose)): ?>
						<td>N/A</td>
						<?php else: ?>
						<td><?php echo $f->purpose; ?></td>
						<?php endif; ?>
						<?php echo checkClaimStatus($f); ?>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table><!-- #funds-history-tbl -->
		</section><!-- #funds-history -->
	</main>

	<script src="../../js/jquery-3.6.0.min.js"></script>
	<script src="../../js/vertical-nav-bar.js"></script>
	<script src="../../js/datatables.min.js"></script>
	<script src="../../js/tables.js"></script>
	<script src="js/payroll.js"></script>
</body>
</html>