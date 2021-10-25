<?php
	if (isset($_GET["id"]))
		$id = $_GET["id"];
	else {
		echo "<script>alert('Sorry, something went wrong!');</script>";
		echo "<script>window.location.replace('transactions.php');</script>";
	}

	require_once "config/config.php";
	require_once "lib/verification.php";
	require_once "lib/database-handler.php";
	require_once "lib/conversion-util.php";
	require_once "models/Cycle.php";
	require_once "models/DataSubject.php";
	require_once "models/Transaction.php";
	require_once "models/Loan.php";
	require_once "models/Interest.php";
	require_once "models/Penalty.php";
	require_once "models/ProcessingFee.php";

	$converter = new Converter();
	$data_subject = new DataSubject();
	$transaction = new Transaction();
	$loan = new Loan();
	$interest = new Interest();
	$penalty = new Penalty();
	$processing_fee = new ProcessingFee();
	$accrued_interest = $interest->getAccruedInterest($id);
	$total_receivables = $loan->getTotalReceivablesByLoan($id);
	$loan_record = $loan->getLoan($id);
	$borrower = $data_subject->getName($loan_record->borrower_id);
	$guarantor = $data_subject->getName($loan_record->guarantor_id);
	$principal_payments = $loan->getPrincipalPayments($id);
	$interests = $interest->getInterests($id);
	$interest_payments = $interest->getInterestPayments($id);
	$penalties = $penalty->getPenalties($id);
	$penalty_payments = $penalty->getPenaltyPayments($id);
	$processing_fees = $processing_fee->getProcessingFees($id);
	$processing_fee_payments = $processing_fee->getProcessingFeePayments($id);
	$collateral = $loan_record->collateral;
	$hasCollateral = $collateral != "";
	$collateralFileName = "img/transactions/collateral/".$collateral;
	$proofImgTag = "<img src='img/transactions/loan/".$loan_record->proof."' alt='Acceptance Photo' />";
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
	<link rel="stylesheet" type="text/css" href="css/loan-details.css" />
	<link rel="stylesheet" type="text/css" href="css/media-queries.css" />
	<link rel="shortcut icon" type="image/x-icon" href="img/others/favicon.png" />
	<title><?php echo COOPERATIVE; ?></title>
</head>
<body>
	<?php require_once "inc/vertical-nav-bar.php"; ?>
	
	<main>
		<header>
			<i class="fas fa-bars"></i>
			<h2 class="text">Transactions</h2>
		</header>

		<!-- Hidden, used as header for printing. -->
		<div id="loan-info-holder">
			<?php require_once "inc/print-header.php"; ?>
			<div id="summary-details"><?php require "inc/loan-info.php"; ?></div>
			<hr class="print-hr" />
		</div><!-- #loan-info-holder -->

		<section id="summary">
			<button id="back" type="button" class="dt-button">&#8592; Back to Loan Disbursements</button>
			<h3>Summary</h3>
			<div class="grid-wrapper">
				<?php require "inc/loan-info.php"; ?>
				<div id="accrued-interest" class="pattern-bg">
					<h4>Accrued Interest</h4>
					<p><span>&#8369;</span> <?php echo number_format($accrued_interest, 2, ".", ","); ?></p>
				</div><!-- #accrued-interest.pattern-bg -->
				<div id="total-receivables" class="pattern-bg">
					<h4>Total Receivables</h4>
					<p>
						<span>&#8369;</span>
						<span id="total-receivables-amount"><?php echo number_format($total_receivables, 2, ".", ","); ?></span>
					</p>
				</div><!-- #total-receivables.pattern-bg -->
			</div><!-- .grid-wrapper -->
		</section><!-- #summary -->

		<section id="principal-payments">
			<h3>Principal Payments</h3>
			<hr />
			<div id="loan-id-holder"><?php echo $id; ?></div>
			<div id="loan-balance-holder"><?php echo $loan->getPrincipalBalance($id); ?></div>
			<table id="principal-payments-tbl" class="display cell-border" width="100%">
				<thead>
					<tr>
						<th>Balance <span>(&#8369;)</span></th>
						<th>Payment <span>(&#8369;)</span></th>
						<th>Date Paid</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($principal_payments as $pp): ?>
					<tr>
						<td>
							<?php
								echo number_format(
									$loan->getPrincipalBalanceByDateTime($pp->loan_id, $pp->date_time_paid), 2, ".", ","
								); 
							?>
						</td>
						<td><?php echo number_format($pp->amount, 2, ".", ","); ?></td>
						<td data-sort="<?php echo $pp->date_time_paid; ?>">
							<?php echo $converter->shortToLongDate($pp->date_time_paid); ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table><!-- #principal-payments-tbl -->
		</section><!-- #principal-payments -->

		<section id="interests">
			<h3>Interests</h3>
			<hr />
			<table id="interests-tbl" class="display cell-border" width="100%">
				<thead>
					<tr>
						<th>ID</th>
						<th>Interest Date</th>
						<th>Amount <span>(&#8369;)</span></th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($interests as $i): ?>
					<tr>
						<td><?php echo $i->interest_id; ?></td>
						<td data-sort="<?php echo $i->interest_date; ?>">
							<?php echo $converter->shortToLongDate($i->interest_date); ?>
						</td>
						<td><?php echo number_format($i->amount, 2, ".", ","); ?></td>
						<?php if ($i->status == "Paid" || $i->status == "Late"): ?>
						<td><?php echo $i->status; ?></td>
						<?php else: ?>
						<td>
							<a 
								href="#" 
								data-loan-id="<?php echo $id; ?>" 
								data-interest-id="<?php echo $i->interest_id; ?>"
								data-interest-balance="<?php echo $interest->getInterestBalance($i->interest_id); ?>"
								>
								<?php echo $i->status; ?>
							</a>
						</td>
						<?php endif; ?>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table><!-- #interests-tbl -->
		</section><!-- #interests -->

		<section id="interest-payments">
			<h3>Interest Payments</h3>
			<hr />
			<table id="interest-payments-tbl" class="display cell-border" width="100%">
				<thead>
					<tr>
						<th>Interest Date</th>
						<th>Amount <span>(&#8369;)</span></th>
						<th>Date Paid</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($interest_payments as $ip): ?>
					<tr>
						<td data-sort="<?php echo $ip->interest_date; ?>">
							<?php echo $converter->shortToLongDate($ip->interest_date); ?>
						</td>
						<td><?php echo number_format($ip->amount, 2, ".", ","); ?></td>
						<td data-sort="<?php echo $ip->date_time_paid; ?>">
							<?php echo $converter->shortToLongDate($ip->date_time_paid); ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table><!-- #interest-payments-tbl -->
		</section><!-- #interest-payments -->

		<section id="penalties">
			<h3>Penalties</h3>
			<hr />
			<table id="penalties-tbl" class="display cell-border" width="100%">
				<thead>
					<tr>
						<th>ID</th>
						<th>Penalty Date</th>
						<th>From Interest Date</th>
						<th>Amount <span>(&#8369;)</span></th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($penalties as $p): ?>
					<tr>
						<td><?php echo $p->penalty_id; ?></td>
						<td data-sort="<?php echo $p->penalty_date; ?>">
							<?php echo $converter->shortToLongDate($p->penalty_date); ?>
						</td>
						<td data-sort="<?php echo $p->interest_date; ?>">
							<?php echo $converter->shortToLongDate($p->interest_date); ?>
						</td>
						<td><?php echo number_format($p->amount, 2, ".", ","); ?></td>
						<?php if ($p->status == "Paid"): ?>
						<td><?php echo $p->status; ?></td>
						<?php else: ?>
						<td>
							<a
								href="#"
								data-loan-id="<?php echo $id; ?>"
								data-penalty-id="<?php echo $p->penalty_id; ?>"
								data-penalty-balance="<?php echo $penalty->getPenaltyBalance($p->penalty_id); ?>"
								>
								<?php echo $p->status; ?>
							</a>
						</td>
						<?php endif; ?>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table><!-- #penalties-tbl -->
		</section><!-- #penalties -->

		<section id="penalty-payments">
			<h3>Penalty Payments</h3>
			<hr />
			<table id="penalty-payments-tbl" class="display cell-border" width="100%">
				<thead>
					<tr>
						<th>Penalty Date</th>
						<th>Amount <span>(&#8369;)</span></th>
						<th>Date Paid</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($penalty_payments as $pp): ?>
					<tr>
						<td data-sort="<?php echo $pp->penalty_date; ?>">
							<?php echo $converter->shortToLongDate($pp->penalty_date); ?>
						</td>
						<td><?php echo number_format($pp->amount, 2, ".", ","); ?></td>
						<td data-sort="<?php echo $pp->date_time_paid; ?>">
							<?php echo $converter->shortToLongDate($pp->date_time_paid); ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table><!-- #penalty-payments-tbl -->
		</section><!-- #penalty-payments -->

		<section id="processing-fees">
			<h3>Processing Fees</h3>
			<hr />
			<table id="processing-fees-tbl" class="display cell-border" width="100%">
				<thead>
					<tr>
						<th>ID</th>
						<th>Fee Date</th>
						<th>Amount <span>(&#8369;)</span></th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($processing_fees as $pf): ?>
					<tr>
						<td><?php echo $pf->processing_fee_id; ?></td>
						<td data-sort="<?php echo $pf->processing_fee_date; ?>">
							<?php echo $converter->shortToLongDate($pf->processing_fee_date); ?>
						</td>
						<td><?php echo number_format($pf->amount, 2, ".", ","); ?></td>
						<?php if ($pf->status == "Paid"): ?>
						<td><?php echo $pf->status; ?></td>
						<?php else: ?>
						<td>
							<a 
								href="#" 
								data-loan-id="<?php echo $id; ?>" 
								data-processing-fee-id="<?php echo $pf->processing_fee_id; ?>"
								data-processing-fee-balance=
									"<?php echo $processing_fee->getProcessingFeeBalance($pf->processing_fee_id); ?>"
								>
								<?php echo $pf->status; ?>
							</a>
						</td>
						<?php endif; ?>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table><!-- #processing-fees-tbl -->
		</section><!-- #processing-fees -->

		<section id="processing-fee-payments">
			<h3>Processing Fee Payments</h3>
			<hr />
			<table id="processing-fee-payments-tbl" class="display cell-border" width="100%">
				<thead>
					<tr>
						<th>Fee Date</th>
						<th>Amount <span>(&#8369;)</span></th>
						<th>Date Paid</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($processing_fee_payments as $pfp): ?>
					<tr>
						<td data-sort="<?php echo $pfp->processing_fee_date; ?>">
							<?php echo $converter->shortToLongDate($pfp->processing_fee_date); ?>
						</td>
						<td><?php echo number_format($pfp->amount, 2, ".", ","); ?></td>
						<td data-sort="<?php echo $pfp->date_time_paid; ?>">
							<?php echo $converter->shortToLongDate($pfp->date_time_paid); ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table><!-- #processing-fee-payments-tbl -->
		</section><!-- #processing-fee-payments -->

		<section id="documents">
			<?php if ($collateral != ""): ?>
			<h3>Documents</h3>
			<hr />
			<h4>Proof of Transaction</h4>
			<?php echo $proofImgTag; ?>
			<h4>Collateral</h4>
			<?php if (strpos($collateral, "pdf") !== false): ?><!-- Can be replaced with str_contains in PHP 8.0 -->
			<div id="pdf-button-wrapper">
				<a class="focal-button" href="<?php echo $collateralFileName; ?>" target="__blank">View File</a>
			</div>
			<?php else: ?>
			
			<img src="<?php echo $collateralFileName; ?>" alt="Collateral Document" />
			<?php endif; ?>

			<?php else: ?>
			<h3>Proof of Transaction</h3>
			<?php echo $proofImgTag; ?>
			<?php endif; ?>
		</section><!-- #documents -->
	</main>

	<script src="js/jquery-3.6.0.min.js"></script>
	<script src="js/vertical-nav-bar.js"></script>
	<script src="js/datatables.min.js"></script>
	<script src="js/tingle.min.js"></script>
	<script src="js/tables.js"></script>
	<script src="js/modal.js"></script>
	<script src="js/validation.js"></script>
	<script src="js/loan-details.js"></script>
</body>
</html>