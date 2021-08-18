<?php
	require_once "config/config.php";
	require_once "lib/database-handler.php";
	require_once "lib/conversion-util.php";
	require_once "models/Guarantor.php";

	$converter = new Converter();
	$guarantor = new Guarantor();
	$guarantors = $guarantor->getGuarantors();
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
			<h2 class="text">Members</h2>
		</header>
		
		<section id="guarantors">
			<h3>Guarantors</h3>
			<hr />
			<table id="guarantors-tbl" class="display cell-border" width="100%">
				<thead>
					<tr>
						<th>ID</th>
						<th>First Name</th>
						<th>Middle Name</th>
						<th>Last Name</th>
						<th>Contact Number</th>
						<th>Birthday</th>
						<th>Age</th>
						<th>Address</th>
						<th>Edit</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($guarantors as $g): ?>
					<tr>
						<td><?php echo $g->guarantor_id ?></td>
						<td><?php echo $g->fname ?></td>
						<td><?php echo $g->mname ?></td>
						<td><?php echo $g->lname ?></td>
						<td><?php echo $g->contact_no ?></td>
						<td><?php echo $converter->shortToLongDate($g->bday, null) ?></td>
						<td><?php echo $converter->bdayToAge($g->bday); ?></td>
						<td><?php echo $g->phase_block_lot ?></td>
						<td><i class="fas fa-user-edit"></i></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</section><!-- #guarantors -->

		<section id="savings">
			<h3>Savings</h3>
			<p>
				Donec laoreet, magna vitae egestas suscipit, lectus dolor lobortis magna, nec gravida diam tellus aliquet sem. Quisque semper tristique risus in elementum. Praesent nec pellentesque quam, non semper dolor. Curabitur a efficitur orci, ac semper nisl. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Mauris sit amet finibus felis, nec consectetur urna. In ullamcorper imperdiet nibh. Suspendisse mattis, nibh a dictum iaculis, elit sem mattis eros, eget rutrum magna ante ut tellus. Nullam dapibus orci nunc, ut tempus nunc maximus sit amet. Proin mollis eget arcu commodo mattis. Nullam in ullamcorper leo, id porta diam.
			</p>
		</section><!-- .savings -->
	</main>

	<script src="js/jquery-3.6.0.min.js"></script>
	<script src="js/vertical-nav-bar.js"></script>
	<script src="js/datatables.min.js"></script>
	<script src="js/tingle.min.js"></script>
	<script src="js/modal.js"></script>
	<script src="js/members.js"></script>
</body>
</html>