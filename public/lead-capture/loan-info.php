<?php
	require_once "../../config/config.php";
	require_once "../../lib/generic-verification.php";
	require_once "../../lib/database-handler.php";
	require_once "../../lib/conversion-util.php";
	require_once "../../models/User.php";
	require_once "../../models/Cycle.php";
	require_once "../../models/DataSubject.php";
	require_once "../../models/Transaction.php";
	require_once "../../models/Loan.php";

	$user_id = $_SESSION["generic-user-verified"];
	$user = new User();
	$account = $user->getUser($user_id);
	$username = $account->username;

	$converter = new Converter();
	$cycle = new Cycle();
	$data_subject = new DataSubject();
	$loan = new Loan();
	$loans = $loan->getLoansByBorrower($user_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta name="author" content="Jesus Lopez" />
	<meta name="author" content="Theresa De Ocampo" />
	<meta name="description" content="" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="../../css/all.min.css" />
	<link rel="stylesheet" type="text/css" href="../../css/datatables.min.css" />
	<link rel="stylesheet" type="text/css" href="../../css/style.css" />
	<link rel="stylesheet" type="text/css" href="../../css/tables.css" />
	<link rel="stylesheet" type="text/css" href="../../css/forms.css" />
	<link rel="shortcut icon" type="image/x-icon" href="../../img/others/favicon.png" />
	<title><?php echo COOPERATIVE; ?></title>
</head>
<body>
	<section id="above-the-fold">
		<h1>Hi, <?php echo $username; ?>!</h1>
	</section><!--#above-the-fold -->

	<!-- Hidden, used as header for printing. -->
	<div id="coop-info-holder"><?php require_once "../../inc/print-header.php"; ?></div>
	<div id="cycle-holder"><?php echo $cycle->getCycleId(); ?></div>

	<section id="loan-history">
		<h2>Loan History</h2>
		<hr />
		<table id="loan-history-tbl" class="display cell-border" width="100%">
			<thead>
				<tr>
					<th>ID</th>
					<th>Guarantor</th>
					<th>Loan Date</th>
					<th>Principal <span>(&#8369;)</span></th>
					<th>Status</th>
					<th>Details</th>
				</tr>
			</thead>
			<tbody>
				<?php
					foreach ($loans as $l):
						$gname = $data_subject->getName($l->guarantor_id);
				?>
				<tr>
					<td><?php echo $l->loan_id; ?></td>
					<td data-sort="<?php echo $gname->lname; ?>">
						<?php echo $gname->fname." ".$gname->mname[0].". ".$gname->lname; ?>
					</td>
					<td data-sort="<?php echo $l->loan_date_time; ?>">
						<?php echo $converter->shortToLongDate($l->loan_date_time); ?>		
					</td>
					<td><?php echo number_format($l->principal, 2, ".", ","); ?></td>
					<td><?php echo $l->status; ?></td>
					<td><a class="fas fa-eye" href="loan-details.php?id=<?php echo $l->loan_id; ?>"></a></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table><!-- #loan-history-tbl -->
	</section><!-- #loan-history -->

	<section id="debt-collection-history">
		<h2>Debt Collection History</h2>
		<hr />
	</section><!-- #dect-collection-history -->

	<!-- 
		Later on lalagyan to ng:
			- ROI History
			- Salary History
			- Account Settings (Change password, username, profile picture)
	-->

	<script src="../../js/jquery-3.6.0.min.js"></script>
	<script src="../../js/datatables.min.js"></script>
	<script src="../../js/tables.js"></script>
	<script src="js/loan-info.js"></script>
</body>
</html>