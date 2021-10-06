// jshint esversion: 6
$(function() {
	let $loanDisbursementsTbl = $("#loan-disbursements-tbl").DataTable({
		dom: "Bfrtip", 
		responsive: true,
		buttons: [
			{
				text: "Add",
				action: function() {
					window.location.href = "add-loan.php";
				},
				attr: {
					id: "add-loan"
				}
			},
			{
				extend: "print",
				title: "Loan Disbursements",
				exportOptions: {
					columns: [0, 1, 2, 3, 4]
				},
				customize: addHeaderToPdf,
				messageTop: cycle
			},
			{
				extend: 'csv',
				title: "Loan Disbursements",
				exportOptions: {
					columns: [0, 1, 2, 3, 4]
				}
			}
		],
		columnDefs: [
			{
				targets: [3],
				width: 100
			}
		]
	});
	checkForRows($loanDisbursementsTbl, "#loan-disbursements-tbl");

	let $principalPaymentsTbl = $("#principal-payments-tbl").DataTable({
		dom: "Bfrtip", 
		responsive: true,
		buttons: [
			{
				extend: "print",
				title: "Principal Payments",
				customize: addHeaderToPdf,
				messageTop: cycle
			},
			{
				extend: 'csv',
				title: "Principal Payments"
			}
		]
	});
	checkForRows($principalPaymentsTbl, "#principal-payments-tbl");
});