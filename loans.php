<?php
	require_once "config/config.php";
	require_once "lib/verification.php";
	require_once "lib/database-handler.php";
	require_once "lib/conversion-util.php";
	require_once "models/Cycle.php";
	require_once "models/DataSubject.php";
	require_once "models/Guarantor.php";
	require_once "models/Transaction.php";
	require_once "models/Loan.php";

	$converter = new Converter();
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
	<?php require_once "inc/vertical-nav-bar.php"; ?>

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
				$loans = $loan->getLoanSummaryByGuarantor($g->guarantor_id);
				$gname_composed = $g->fname." ".$g->mname[0].". ".$g->lname;
		?>
		<!-- Use data-gurantor-lname in the future to sort sections by guarantor's last name -->
		<section data-gurantor-lname="<?php echo $g->lname; ?>">
			<h3><?php echo $gname_composed; ?></h3>
			<table 
				id="<?php echo $g->guarantor_id; ?>-tbl"
				data-guarantor-name="<?php echo $gname_composed; ?>"
				class="display cell-border" 
				width="100%"
				>
				<thead>
					<tr>
						<th>Borrower</th>
						<th>Loan Date & Time</th>
						<th>Status</th>
						<th>Paid <span>(&#8369;)</span></th>
						<th>Unpaid <span>(&#8369;)</span></th>
					</tr>
				</thead>
				<tbody>
					<?php 
						if (!is_null($loans)):
							foreach ($loans as $l):
					 ?>
					<tr>
						<td data-sort="<?php echo $l->lname; ?>"><?php echo $l->fname." ".$l->mname[0].". ".$l->lname; ?></td>
						<td data-sort="<?php echo $l->loan_date_time; ?>">
							<?php echo $converter->shortToLongDateTime($l->loan_date_time); ?>
						</td>
						<td><?php echo $l->status; ?></td>
						<td><?php echo number_format($l->paid, 2, ".", ","); ?></td>
						<td><?php echo number_format($l->unpaid, 2, ".", ","); ?></td>
					</tr>
					<?php
							endforeach;
						endif; 
					?>
				</tbody>
			</table>
			<p class="pattern-bg total"><span class="peso-sign">&#8369; </span><span class="amount"></span></p>
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
	<script src="js/loans.js"></script>
</body>
</html>