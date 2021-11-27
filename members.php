<?php
	require_once "config/config.php";
	require_once "lib/verification.php";
	require_once "lib/database-handler.php";
	require_once "lib/conversion-util.php";
	require_once "models/Cycle.php";
	require_once "models/DataSubject.php";
	require_once "models/Guarantor.php";
	require_once "models/User.php";
	require_once "models/Administrator.php";

	$converter = new Converter();
	$cycle = new Cycle();
	$guarantor = new Guarantor();
	$guarantors = $guarantor->getCurrentGuarantors();
	$savings = $guarantor->getSavings();
	$data_subject = new DataSubject();
	$data_subjects = $data_subject->getDataSubjects();
	$user = new User();
	$admininistrator = new Administrator();
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
	<link rel="shortcut icon" type="image/x-icon" href="img/others/favicon.png" />
	<title><?php echo COOPERATIVE; ?></title>
</head>
<body>
	<?php require_once "inc/vertical-nav-bar.php"; ?>

	<main>
		<header>
			<i class="fas fa-bars"></i>
			<h2 class="text">Members</h2>
		</header>

		<!-- Hidden, used as header for printing. -->
		<div id="coop-info-holder"><?php require_once "inc/print-header.php"; ?></div>
		<div id="cycle-holder"><?php echo $cycle->getCycleId(); ?></div>
		
		<section id="guarantors">
			<h3>Guarantors</h3>
			<hr />
			<table id="guarantors-tbl" class="display cell-border" width="100%">
				<thead>
					<tr>
						<th>First Name</th>
						<th>Middle Name</th>
						<th>Last Name</th>
						<th>Contact Number</th>
						<th>Birthday</th>
						<th>Age</th>
						<th>Address</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($guarantors as $g): ?>
					<tr>
						<td><?php echo $g->fname; ?></td>
						<td><?php echo $g->mname; ?></td>
						<td><?php echo $g->lname; ?></td>
						<td><?php echo $g->contact_no; ?></td>
						<td data-sort="<?php echo $g->bday; ?>"><?php echo $converter->shortToLongDate($g->bday); ?></td>
						<td><?php echo $converter->bdayToAge($g->bday); ?></td>
						<td><?php echo $g->phase_block_lot; ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table><!-- #guarantors-tbl -->
		</section><!-- #guarantors -->

		<section id="savings">
			<h3>Savings</h3>
			<hr />
			<table id="savings-tbl" class="display cell-border" width="100%">
				<thead>
					<tr>
						<th>Member</th>
						<th>No. of Share</th>
						<th>Principal <span>(&#8369;)</span></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($savings as $s): ?>
					<tr>
						<td data-sort="<?php echo $s->lname; ?>"><?php echo $s->fname." ".$s->mname[0].". ".$s->lname; ?></td>
						<td><?php echo $s->number_of_share; ?></td>
						<td><?php echo number_format($s->principal, 2, ".", ","); ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table><!-- #savings-tbl -->
		</section><!-- .savings -->

		<section id="data-subjects">
			<h3>All Data Subjects</h3>
			<hr />
			<table id="data-subjects-tbl" class="display cell-border" width="100%">
				<thead>
					<tr>
						<th>First Name</th>
						<th>Middle Name</th>
						<th>Last Name</th>
						<th>Contact Number</th>
						<th>Birthday</th>
						<th>Age</th>
						<th>Address</th>
						<th>Edit</th>
						<th>Acct</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($data_subjects as $ds): ?>
					<tr data-data-subject-id="<?php echo $ds->data_subject_id; ?>">
						<td><?php echo $ds->fname; ?></td>
						<td><?php echo $ds->mname; ?></td>
						<td><?php echo $ds->lname; ?></td>
						<td><?php echo $ds->contact_no; ?></td>
						<td data-sort="<?php echo $ds->bday; ?>"><?php echo $converter->shortToLongDate($ds->bday); ?></td>
						<td><?php echo $converter->bdayToAge($ds->bday); ?></td>
						<td><?php echo $ds->phase_block_lot; ?></td>
						<td><i class="fas fa-user-edit"></i></td>
						<?php if (!$user->hasAccount($ds->data_subject_id)): ?>
						<td><i class="fas fa-plus-square"></i></td>
						<?php else: ?>
							<?php if ($administrator->currentAdmin($ds->data_subject_id, $cycle->getLatestPeriod())): ?>
						<td><i class="fas fa-times-circle"></i></td>
							<?php else: ?>
						<td><i class="fas fa-minus-square"></i></td>
							<?php endif; ?>
						<?php endif; ?>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table><!-- #data-subjects-tbl -->
		</section><!-- #data-subjects -->
	</main>

	<script src="js/jquery-3.6.0.min.js"></script>
	<script src="js/vertical-nav-bar.js"></script>
	<script src="js/datatables.min.js"></script>
	<script src="js/tingle.min.js"></script>
	<script src="js/tables.js"></script>
	<script src="js/modal.js"></script>
	<script src="js/validation.js"></script>
	<script src="js/show-password.js"></script>
	<script src="js/members.js"></script>
</body>
</html>