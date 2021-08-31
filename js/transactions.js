// jshint esversion: 6
$("#loan-disbursements-tbl").DataTable({
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
			}
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