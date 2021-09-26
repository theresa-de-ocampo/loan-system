<?php
	require_once "config/config.php";
	require_once "lib/verification.php";
	require_once "lib/database-handler.php";
	require_once "lib/conversion-util.php";
	require_once "models/Guarantor.php";
	require_once "models/DataSubject.php";

	$converter = new Converter();
	$guarantor = new Guarantor();
	$data_subject = new DataSubject();
	$current_guarantors = $guarantor->getCurrentGuarantors();
	$data_subjects = $data_subject->getDataSubjects();
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
	<link rel="stylesheet" type="text/css" href="css/add-loan.css" />
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

		<section id="loan">
			<h3>Loan</h3>
			<hr />
			<form action="src/add-loan.php" method="post">
				<div id="tabbed-panel">

					<input id="guarantor-tab" name="tab" type="radio" checked />
					<label for="guarantor-tab">Guarantor</label>
					
					<input id="borrower-tab" name="tab" type="radio" />
					<label for="borrower-tab">Borrower</label>
					
					<input id="dealings-tab" name="tab" type="radio" />
					<label for="dealings-tab">Dealings</label>

					<input id="submit-tab" name="tab" type="radio" />
					<label for="submit-tab">Submit</label>
					
					<div id="content-wrapper">
						<div id="guarantor-content" class="tab-content">
							<header>
								<ul class="step-container">
									<li class="step-item number">Step &#9312;</li>
									<li class="step-item description">Select the Guarantor</li>
								</ul><!-- .step-container -->
							</header>
							<div class="flex-wrapper">
								<div class="flex-item short">
									<label for="guarantor-id">ID<i class="far fa-question-circle"></i></label>
									<input id="guarantor-id" type="number" name="data-subject-id" required readonly />
								</div><!-- .flex-item -->
								<div class="flex-item">
									<label for="guarantor-name">Guarantor<i class="far fa-question-circle"></i></label>
									<input id="guarantor-name" type="text" required readonly />
								</div><!-- .flex-item -->
							</div><!-- .flex-wrapper -->
							<hr class="divider" />
							<table id="guarantor-tbl" class="display cell-border" width="100%">
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
									<?php foreach ($current_guarantors as $cg): ?>
									<tr>
										<td><?php echo $cg->data_subject_id; ?></td>
										<td><?php echo $cg->fname; ?></td>
										<td><?php echo $cg->mname; ?></td>
										<td><?php echo $cg->lname; ?></td>
										<td><?php echo $converter->bdayToAge($cg->bday); ?></td>
										<td><?php echo $cg->phase_block_lot; ?></td>
									</tr>
									<?php endforeach; ?>
								</tbody>
							</table><!-- #guarantor-tbl -->
						</div><!-- #guarantor-content.tab-content -->

						<div id="borrower-content" class="tab-content">
							<header>
								<ul class="step-container">
									<li class="step-item number">Step &#9313;</li>
									<li class="step-item description">Provide Borrower Information</li>
								</ul><!-- .step-container -->
							</header>

							<div id="existing-data-subject">
								<div class="flex-wrapper">
									<div class="flex-item short">
										<label for="borrower-id">ID<i class="far fa-question-circle"></i></label>
										<input id="borrower-id" type="number" name="data-subject-id" required readonly />
									</div><!-- .flex-item -->
									<div class="flex-item">
										<label for="borrower-name">Borrower<i class="far fa-question-circle"></i></label>
										<input id="borrower-name" type="text" required readonly />
									</div><!-- .flex-item -->
								</div><!-- .flex-wrapper -->
								<hr class="divider" />
								<table id="data-subject-tbl" class="display cell-border" width="100%">
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
										<?php foreach ($data_subjects as $ds): ?>
										<tr>
											<td><?php echo $ds->data_subject_id; ?></td>
											<td><?php echo $ds->fname; ?></td>
											<td><?php echo $ds->mname; ?></td>
											<td><?php echo $ds->lname; ?></td>
											<td><?php echo $converter->bdayToAge($ds->bday); ?></td>
											<td><?php echo $ds->phase_block_lot; ?></td>
										</tr>
										<?php endforeach; ?>
									</tbody>
								</table><!-- #data-subject-tbl -->
							</div><!-- #existing-data-subject -->
							<div id="new-data-subject">
								<div class="grid-wrapper">
									<div class="grid-item solo">
										<button type="button" class="dt-button">&#8592; Choose Existing</button>
										<hr />
									</div><!-- .grid-item.solo-->
									<div class="grid-item">
										<label for="fname">First Name</label>
										<input id="fname" type="text" name="fname" required />
									</div><!-- .grid-item -->
									<div class="grid-item">
										<label for="mname">Middle Name</label>
										<input id="mname" type="text" name="mname" required />
									</div><!-- .grid-item -->
									<div class="grid-item">
										<label for="lname">Last Name</label>
										<input id="lname" type="text" name="lname" required />
									</div><!-- .grid-item -->
									<div class="grid-item">
										<label for="contact-no">Contact No. (09*********)</label>
										<input id="contact-no" type="text" name="contact-no" pattern="^09[0-9]{9}" required />
									</div><!-- .grid-item -->
									<div class="grid-item">
										<label for="bday">Birthday</label>
										<input id="bday" type="date" name="bday" required class="medium" />
									</div><!-- .grid-item -->
									<div class="grid-item">
										<label for="">Phase, Block, and Lot</label>
										<input id="address" type="text" name="address" required class="medium" />
									</div><!-- .grid-item -->
								</div><!-- .grid-wrapper -->
							</div><!-- #new-data-subject -->
						</div><!-- #borrower-content.tab-content -->

						<div id="dealings-content" class="tab-content">
							<header>
								<ul class="step-container">
									<li class="step-item number">Step &#9314;</li>
									<li class="step-item description">Finalize Transaction</li>
								</ul><!-- .step-container -->
							</header>
							<p>
								Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus vestibulum, nunc eu lobortis pharetra, metus purus tristique eros, sed efficitur augue sapien et lacus. Praesent sed ultricies felis. Duis ligula mauris, consequat et mattis vitae, congue ac tortor. Integer hendrerit arcu in risus pellentesque, ac fringilla odio euismod.
							</p>
						</div><!-- dealings-content.tab-content -->

						<div id="submit-content" class="tab-content">
							<header>
								<ul class="step-container">
									<li class="step-item number">Step &#9315;</li>
									<li class="step-item description">Agreement</li>
								</ul><!-- .step-container -->
							</header>
							<p>
								Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus vestibulum, nunc eu lobortis pharetra, metus purus tristique eros, sed efficitur augue sapien et lacus. Praesent sed ultricies felis. Duis ligula mauris, consequat et mattis vitae, congue ac tortor. Integer hendrerit arcu in risus pellentesque, ac fringilla odio euismod.
							</p>
						</div><!-- #submit-content.tab-content -->
					</div><!-- #content-wrapper -->
				</div><!-- #tabbed-panel -->
			</form>
		</section><!-- #loan -->
	</main>

	<script src="js/jquery-3.6.0.min.js"></script>
	<script src="js/vertical-nav-bar.js"></script>
	<script src="js/datatables.min.js"></script>
	<script src="js/tingle.min.js"></script>
	<script src="js/modal.js"></script>
	<script src="js/add-loan.js"></script>
</body>
</html>