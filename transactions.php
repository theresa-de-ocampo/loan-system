<?php
	require_once "config/config.php";
	require_once "lib/database-handler.php";
	require_once "lib/conversion-util.php";
	require_once "models/Transaction.php";
	require_once "models/DataSubject.php";

	$converter = new Converter();
	$transaction = new Transaction();
	$data_subject = new DataSubject();
	$loans = $transaction->getLoans();
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
	<link rel="stylesheet" type="text/css" href="css/media-queries.css" />
	<link rel="shortcut icon" type="image/x-icon" href="img/favicon.png" />
	<title><?php echo COOPERATIVE; ?></title>
</head>
<body>
	<?php require_once "inc/vertical-nav-bar.html"; ?>

	<main>
		<header>
			<i class="fas fa-bars"></i>
			<h2 class="text">Transactions</h2>
		</header>
		
		<section id="loan-disbursements">
			<h3>Loan Disbursements</h3>
			<hr />
			<table id="loan-disbursements-tbl" class="display cell-border" width="100%">
				<thead>
					<tr>
						<th>ID</th>
						<th>Borrower</th>
						<th>Guarantor</th>
						<th>Loan Date</th>
						<th>Principal</th>
						<th>Status</th>
						<th>Details</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($loans as $l): ?>
					<tr>
						<td><?php echo $l->loan_id; ?></td>
						<td><?php echo $data_subject->getName($l->borrower_id); ?></td>
						<td><?php echo $data_subject->getName($l->guarantor_id); ?></td>
						<td><?php echo $converter->shortToLongDate($l->loan_date_time); ?></td>
						<td><?php echo $l->principal; ?></td>
						<td><?php echo $l->status; ?></td>
						<td><a class="fas fa-eye" href="loan-details.php?id=<?php echo $l->loan_id; ?>"></a></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table><!-- #loan-disbursements-tbl -->
		</section><!-- #loan-disbursements -->

		<section id="principal-payments">
			<h3>Principal Payments</h3>
		</section><!-- #principal-payments -->
	</main>

	<script src="js/jquery-3.6.0.min.js"></script>
	<script src="js/vertical-nav-bar.js"></script>
	<script src="js/datatables.min.js"></script>
	<script src="js/tingle.min.js"></script>
	<script src="js/modal.js"></script>
	<script src="js/transactions.js"></script>
</body>
</html>