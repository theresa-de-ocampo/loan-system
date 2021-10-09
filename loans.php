<?php
	require_once "config/config.php";
	require_once "lib/verification.php";
	require_once "lib/database-handler.php";
	require_once "models/Cycle.php";
	require_once "models/DataSubject.php";
	require_once "models/Guarantor.php";
	require_once "models/Transaction.php";
	require_once "models/Loan.php";

	$cycle = new Cycle();
	$data_subject = new DataSubject();
	$guarantor = new Guarantor();
	$guarantors = $guarantor->getCurrentGuarantors();
	$loan = new Loan();
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
	<link rel="stylesheet" type="text/css" href="css/loans.css" />
	<link rel="stylesheet" type="text/css" href="css/media-queries.css" />
	<link rel="shortcut icon" type="image/x-icon" href="img/others/favicon.png" />
	<title><?php echo COOPERATIVE; ?></title>
</head>
<body>
	<?php require_once "inc/vertical-nav-bar.html"; ?>

	<main id="loans">
		<header>
			<i class="fas fa-bars"></i>
			<h2 class="text">Loans</h2>
		</header>

		<!-- Hidden, used as header for printing. -->
		<div id="coop-info-holder"><?php require_once "inc/print-header.php"; ?></div>
		<div id="cycle-holder"><?php echo $cycle->getCycleId(); ?></div>
		
		<?php
		if (count($guarantors) > 0):
			foreach ($guarantors as $g): 
				$data = $loan->getLoanSummaryByGuarantor($g->guarantor_id); 
				$guarantor_name = $data_subject->getName($g->guarantor_id);
		?>
		<section>
			<h3><?php echo $guarantor_name; ?></h3>
			<table 
				id="<?php echo $g->guarantor_id; ?>-tbl" 
				data-guarantor-name="<?php echo $guarantor_name; ?>"
				class="display cell-border" 
				width="100%"
				>
				<thead>
					<tr>
						<th>Borrower</th>
						<th>Loan Status</th>
						<th>Paid <span>(&#8369;)</span></th>
						<th>Unpaid <span>(&#8369;)</span></th>
					</tr>
				</thead>
				<tbody>
					<?php if (!is_null($data)): ?>
					<tr>
						<td><?php echo $data["borrower"]; ?></td>
						<td><?php echo $data["status"]; ?></td>
						<td><?php echo number_format($data["paid"], 2, ".", ","); ?></td>
						<td><?php echo number_format($data["unpaid"], 2, ".", ","); ?></td>
					</tr>
					<?php endif; ?>
				</tbody>
			</table>
			<p class="pattern-bg"><span class="peso-sign">&#8369; </span><span class="amount"></span></p>
		</section>
		<?php 
			endforeach;
		else :
		?>
		<section class="empty-state">
			<div>
				<p>It looks like your cooperative has no guarantors yet.</p>
				<hr />
				<p>Recruit some investors, and build a higher net worth!</p>
			</div>
		</section><!-- .empty-state -->
		<?php endif; ?>
	</main>

	<script src="js/jquery-3.6.0.min.js"></script>
	<script src="js/vertical-nav-bar.js"></script>
	<script src="js/datatables.min.js"></script>
	<script src="js/tables.js"></script>
	<script src="js/loan.js"></script>
</body>
</html>