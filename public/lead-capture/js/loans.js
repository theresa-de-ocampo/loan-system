// jshint esversion: 6
$(function() {
	let $loanHistoryTbl = $("#loan-history-tbl").DataTable({
		dom: "Bfrtip", 
		responsive: true,
		buttons: [
			{
				extend: "print",
				title: "Loan History",
				exportOptions: {
					columns: [0, 1, 2, 3, 4]
				},
				customize: addHeaderToPdf,
				messageTop: cycle
			}
		],
		columnDefs: [
			{
				targets: [2],
				width: 100
			}
		],
		order: [[4, "asc"], [2, "desc"]]
	});
	checkForRows($loanHistoryTbl, "#loan-history-tbl");
});