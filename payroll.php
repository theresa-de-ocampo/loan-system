<?php
	require_once "config/config.php";
	require_once "lib/verification.php";
	require_once "lib/database-handler.php";
	require_once "lib/conversion-util.php";
	require_once "models/Cycle.php";
	require_once "models/DataSubject.php";
	require_once "models/Guarantor.php";
	require_once "models/Transaction.php";
	require_once "models/ProcessingFee.php";
	require_once "models/Penalty.php";
	require_once "models/Payroll.php";
	require_once "models/Roi.php";
	require_once "models/Salary.php";
	require_once "models/Fund.php";

	$converter = new Converter();
	$cycle = new Cycle();
	$data_subject = new DataSubject();
	$guarantor = new Guarantor();
	$processing_fee = new ProcessingFee();
	$penalty = new Penalty();
	$payroll = new Payroll();
	$roi = new Roi();
	$salary = new Salary();
	$fund = new Fund();

	$guarantors = $guarantor->getCurrentGuarantors();
	$profits = $payroll->getProfits();
	$rate = $profits["rate"];
	$interest = $profits["interest"];
	$processed = $payroll->getProcessedFlag();
	$total_processing_fee_collected = $processing_fee->getTotalProcessingFeeCollected();
	$earnings = $converter->roundDown($total_processing_fee_collected / 2);
	$funds = $penalty->getTotalPenaltiesCollected(); /*Amount of funds for this cycle, not an array of `fund` records. */
	$employees = $salary->getEmployees();
	$closed = (int)$cycle->getCycleId() >= date("Y");
	if ((int)$cycle->getCycleId() < date("Y"))
		$on_going = false;
	else
		$on_going = date("m") < 11 || (date("m") <= 11) && date("d") < 30;
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
	<?php require_once "inc/vertical-nav-bar.php"; ?>

	<main id="payroll">
		<header>
			<i class="fas fa-bars"></i>
			<h2 class="text">Payroll</h2>
		</header>

		<!-- Hidden, used as header for printing. -->
		<div id="coop-info-holder"><?php require_once "inc/print-header.php"; ?></div>
		<div id="cycle-holder"><?php echo $cycle->getCycleId(); ?></div>

		<!-- Hidden. If today if November 30, this flag is used to check if the year-end records are already set. -->
		<div id="flag-holder"><?php echo $processed; ?></div>

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
					<th>Guarantor</th>
					<th>Total Amount Lent <span>(&#8369;)</span></th>
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
			<p class="pattern-bg total"><span class="peso-sign">&#8369; </span><span class="amount"></span></p>
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
			<p class="pattern-bg total"><span class="peso-sign">&#8369; </span><span class="amount"></span></p>
		</section><!-- #interest-summation -->

		<section id="shares">
			<h3>Shares</h3>
			<hr />
			<table id="shares-tbl" class="display cell-border" width="100%">
				<thead>
					<th>Guarantor</th>
					<th>No. of Share</th>
					<th>Interest <span>(&#8369;)</span></th>
					<th><?php echo $rate * 100; ?>% Return <span>(&#8369;)</span></th>
					<th>Cut <span>(&#8369;)</span></th>
					<th>Total <span>(&#8369;)</span></th>
					<th>Principal Returned <span>(&#8369;)</span></th>
					<th>Grand Total <span>(&#8369;)</span></th>
					<th>Status</th>
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
						<?php if ($on_going): ?>
						<td>On Going</td>
						<?php else: ?>
							<?php if ($processed): $r = $roi->getRoi($guarantor_id); $status = $r->status; ?>
								<?php if ($status == "Pending"): ?>
									<td><a href="#" class="<?php echo strtolower($status); ?>"><?php echo $status; ?></a></td>
								<?php else: ?>
									<td>
										<a 
											href="#" 
											class="<?php echo strtolower($status); ?>"
											data-date-time-claimed="<?php echo $converter->shortToLongDateTime($r->date_time_claimed); ?>"
											data-proof="<?php echo $r->proof; ?>"
											>
											<?php echo $status; ?>
										</a>
									</td>
								<?php endif; ?>
							<?php else: ?>
								<td>On Going</td>
							<?php endif; ?>
						<?php endif; ?>
						<input type="hidden" name="g-id[]" value="<?php echo $guarantor_id; ?>" />
						<input type="hidden" name="g-amount[]" value="<?php echo $grand_total; ?>" />
					</tr>
					<?php
						$guarantor_ids[] = $guarantor_id;
						$guarantor_totals[] = $grand_total;
						endforeach;
					?>
				</tbody>
			</table><!-- #shares-tbl -->
		</section><!-- #shares -->

		<section id="honorarium">
			<h3>Honorarium</h3>
			<hr />
			<table id="honorarium-tbl" class="display cell-border" width="100%">
				<thead>
					<th>Employee</th>
					<th>Position</th>
					<th>Earnings <span>(&#8369;)</span></th>
					<th>Status</th>
				</thead>
				<tbody>
					<?php foreach ($employees as $e): ?>
					<tr data-guarantor-id="<?php echo $e->user_id ?>">
						<?php
							if ($e->position == "Asst. Treasurer")
								$position_attr = "asst-treasurer";
							else
								$position_attr = "treasurer";
						?>
						<td id="<?php echo $position_attr; ?>-name" data-sort="<?php echo $e->fname; ?>">
							<?php echo $e->fname." ".$e->mname[0].". ".$e->lname; ?>
						</td>
						<td><?php echo $e->position; ?></td>
						<td><?php echo number_format($earnings, 2, ".", ","); ?></td>
						<?php if ($on_going): ?>
						<td>On Going</td>
						<?php else: ?>
							<?php if ($processed): $s = $salary->getSalary($e->user_id); $status = $s->status; ?>
								<?php if ($status == "Pending"): ?>
									<td>
										<a href="#" class="salary <?php echo strtolower($status); ?>"><?php echo $status; ?></a>
									</td>
								<?php else: ?>
									<td>
										<a 
											href="#" 
											class="salary <?php echo strtolower($status); ?>"
											data-date-time-claimed=
												"<?php echo $converter->shortToLongDateTime($s->date_time_claimed); ?>"
											data-proof="<?php echo $s->proof; ?>"
											>
											<?php echo $status; ?>
										</a>
									</td>
								<?php endif; ?>
							<?php else: ?>
								<td>On Going</td>
							<?php endif; ?>
						<?php endif; ?>
					</tr>
					<?php endforeach; ?>
					<tr>
						<td data-sort="1">Cooperative</td>
						<td>N/A</td>
						<td><?php echo number_format($funds, 2, ".", ","); ?></td>
						<?php if ($on_going): ?>
						<td>On Going</td>
						<?php else: ?>
							<?php 
								if ($processed):
									$f = $fund->getFund();

								if (is_null($f->claimed_by))
									$status = "Pending";
								else {
									$claimer = $data_subject->getName($f->claimed_by);
									$name = $claimer->fname." ".$claimer->mname[0].". ".$claimer->lname;
									$status = "Claimed";
								}
							?>
								<?php if ($status == "Pending"): ?>
									<td>
										<a href="#" class="fund <?php echo strtolower($status); ?>"><?php echo $status; ?></a>
									</td>
								<?php else: ?>
									<td>
										<a 
											href="#" 
											class="fund <?php echo strtolower($status); ?>"
											data-claimer="<?php echo $name; ?>"
											data-purpose="<?php echo $f->purpose; ?>"
											data-date-time-claimed=
												"<?php echo $converter->shortToLongDateTime($f->date_time_claimed); ?>"
											data-proof="<?php echo $f->proof; ?>"
											>
											<?php echo $status; ?>
										</a>
									</td>
								<?php endif; ?>
							<?php else: ?>
								<td>On Going</td>
							<?php endif; ?>
						<?php endif; ?>
					</tr>
				</tbody>
			</table><!-- #honorarium-tbl -->
		</section><!-- #honorarium -->

		<form action="src/process-year-end.php" method="post">
			<input type="hidden" name="interest" value="<?php echo $interest; ?>" />
			<input type="hidden" name="processing-fee" value="<?php echo $total_processing_fee_collected; ?>" />

			<?php
				if (empty($guarantor_ids)) // Cycle has just started, and there's no data yet.
					$serialized_guarantor_ids = $serialized_guarantor_totals = 0;
				else {
					$serialized_guarantor_ids = serialize($guarantor_ids);
					$serialized_guarantor_totals = serialize($guarantor_totals);
				}
			?>
			<input type="hidden" name="g-ids" value="<?php echo $serialized_guarantor_ids; ?>" />
			<input type="hidden" name="g-amount" value="<?php echo $serialized_guarantor_totals; ?>" />
			<input type="hidden" name="salary" value="<?php echo $earnings; ?>" />
			<input type="hidden" name="funds" value="<?php echo $funds; ?>" />
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