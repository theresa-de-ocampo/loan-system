<?php 
	require_once "config/config.php";
	require_once "lib/verification.php";
	require_once "lib/database-handler.php";
	require_once "lib/conversion-util.php";
	require_once "models/Cycle.php";
	require_once "models/Guarantor.php";

	$converter = new Converter();
	$guarantor = new Guarantor();
	$not_current_guarantors = $guarantor->getNotCurrentGuarantors();
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
	<link rel="stylesheet" type="text/css" href="css/header-fields.css" />
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

		<section id="new-guarantor">
			<h3>Guarantor</h3>
			<form action="src/add-guarantor.php" method="post">
				<div class="flex-wrapper">
					<div class="flex-item short">
						<label for="data-subject-id">ID<i class="far fa-question-circle"></i></label>
						<input id="data-subject-id" type="number" name="data-subject-id" required readonly />
					</div><!-- .flex-item -->
					<div class="flex-item">
						<label for="name">New Guarantor<i class="far fa-question-circle"></i></label>
						<input id="name" type="text" required readonly />
					</div><!-- .flex-item -->
					<div class="flex-item short">
						<label for="">No. of Share</label>
						<input type="number" name="number-of-share" class="number-of-share" required min="1" max="5" />
					</div><!-- .flex-item -->
				</div><!-- .flex-wrapper -->
				<footer>
					<button type="reset" class="dt-button">Cancel</button>
					<button type="submit" name="submit" class="dt-button">Submit</button>
				</footer>
			</form>
			<hr class="divider" />
			<table id="new-guarantor-tbl" class="display cell-border" width="100%">
				<thead>
					<tr>
						<th>ID</th>
						<th>First Name</th>
						<th>Middle Name</th>
						<th>Last Name</th>
						<th>Age</th>
						<th>Address</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($not_current_guarantors as $ncg): ?>
					<tr>
						<td><?php echo $ncg->data_subject_id; ?></td>
						<td><?php echo $ncg->fname; ?></td>
						<td><?php echo $ncg->mname; ?></td>
						<td><?php echo $ncg->lname; ?></td>
						<td><?php echo $converter->bdayToAge($ncg->bday); ?></td>
						<td><?php echo $ncg->phase_block_lot; ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table><!-- #new-guarantor-tbl -->
		</section><!-- #new-guarantor -->
	</main>

	<script src="js/jquery-3.6.0.min.js"></script>
	<script src="js/vertical-nav-bar.js"></script>
	<script src="js/datatables.min.js"></script>
	<script src="js/tingle.min.js"></script>
	<script src="js/tables.js"></script>
	<script src="js/modal.js"></script>
	<script src="js/validation.js"></script>
	<script src="js/add-guarantor.js"></script>
</body>
</html>