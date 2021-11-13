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

	let $debtCollectionHistoryTbl = $("#debt-collection-history-tbl").DataTable({
		dom: "Bfrtip", 
		responsive: true,
		buttons: [
			{
				extend: "print",
				title: "Debt Colelction History",
				exportOptions: {
					columns: [0, 1, 2, 3]
				},
				customize: addHeaderToPdf,
				messageTop: cycle
			}
		]
	});
	checkForRows($debtCollectionHistoryTbl, "#debt-collection-history-tbl");
	let grandTotal = $debtCollectionHistoryTbl.column(2).data().sum() + $debtCollectionHistoryTbl.column(3).data().sum();
	console.log(grandTotal);
	$("#debt-collection-history-tbl_wrapper + p span.amount").text(grandTotal.toLocaleString("en"));
});