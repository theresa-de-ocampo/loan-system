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
		],
		order: [[5, "asc"], [3, "desc"]]
	});
	checkForRows($loanDisbursementsTbl, "#loan-disbursements-tbl");

	let $appropriationsTbl = $("#appropriations-tbl").DataTable({
		dom: "Bfrtip", 
		responsive: true,
		buttons: [
			{
				extend: "print",
				title: "Appropriations",
				customize: addHeaderToPdf,
				messageTop: cycle
			},
			{
				extend: 'csv',
				title: "Appropriations"
			}
		],
		order: [[2, "desc"], [0, "asc"]]
	});
	checkForRows($appropriationsTbl, "#appropriations");
});