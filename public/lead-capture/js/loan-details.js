// jshint esversion: 6
$(function() {
	$("#back").on("click", function() {
		window.location.href = "loan-info.php#loan-disbursements";
	});

	let summaryDetails = $("#loan-info-holder").html().replace(' class="pattern-bg"', "");

	function addHeaderToLoanDetailsPdf(win) {
		$(win.document.body).prepend(summaryDetails);
	}

	// PRINCIPAL
	let $principalPaymentsTbl = $("#principal-payments-tbl").DataTable({
		dom: "Bfrtip", 
		responsive: true,
		buttons: [
			{
				extend: "print",
				title: "Principal Payments",
				customize: addHeaderToLoanDetailsPdf
			}
		],
		order: [[2, "asc"]]
	});
	checkForRows($principalPaymentsTbl, "#principal-payments-tbl");

	// INTERESTS
	let $interestsTbl = $("#interests-tbl").DataTable({
		dom: "Bfrtip", 
		responsive: true,
		buttons: [
			{
				extend: "print",
				title: "Interests",
				customize: addHeaderToLoanDetailsPdf
			}
		],
		order: [[1, "asc"]]
	});
	checkForRows($interestsTbl, "#interests-tbl");

	let $interestPaymentsTbl = $("#interest-payments-tbl").DataTable({
		dom: "Bfrtip",
		responsive: true,
		buttons: [
			{
				extend: "print",
				title: "Interest Payments",
				customize: addHeaderToLoanDetailsPdf
			}
		]
	});
	checkForRows($interestPaymentsTbl, "#interest-payments-tbl");

	// PENALTIES
	let $penaltiesTbl = $("#penalties-tbl").DataTable({
		dom: "Bfrtip", 
		responsive: true,
		buttons: [
			{
				extend: "print",
				title: "Penalties",
				customize: addHeaderToLoanDetailsPdf
			}
		],
		order: [[1, "asc"]]
	});
	checkForRows($penaltiesTbl, "#penalties-tbl");

	let $penaltyPaymentsTbl = $("#penalty-payments-tbl").DataTable({
		dom: "Bfrtip", 
		responsive: true,
		buttons: [
			{
				extend: "print",
				title: "Penalty Payments",
				customize: addHeaderToLoanDetailsPdf
			}
		]
	});
	checkForRows($penaltyPaymentsTbl, "#penalty-payments-tbl");

	// PROCESSING FEES
	let $processingFeesTbl = $("#processing-fees-tbl").DataTable({
		dom: "Bfrtip", 
		responsive: true,
		buttons: [
			{
				extend: "print",
				title: "Processing Fees",
				customize: addHeaderToLoanDetailsPdf
			}
		],
		order: [[1, "asc"]]
	});
	checkForRows($processingFeesTbl, "#processing-fees-tbl");

	let $processingFeePaymentsTbl = $("#processing-fee-payments-tbl").DataTable({
		dom: "Bfrtip", 
		responsive: true,
		buttons: [
			{
				extend: "print",
				title: "Processing Fee Payments",
				customize: addHeaderToLoanDetailsPdf
			}
		]
	});
	checkForRows($processingFeePaymentsTbl, "#processing-fee-payments-tbl");
});