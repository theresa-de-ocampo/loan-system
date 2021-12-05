// jshint esversion: 6
$(function() {
	const $roiHistoryTbl = $("#roi-history-tbl").DataTable({
		dom: "Bfrtip",
		responsive: true,
		buttons: [
			{
				extend: "print",
				title: "Acquired ROI History",
				customize: addHeaderToPdf,
				messageTop: cycle
			},
			{
				extend: "csv",
				title: "Acquired ROI History"
			}
		]
	});
	checkForRows($roiHistoryTbl, "#roi-history-tbl");

	const $salaryHistoryTbl = $("#salary-history-tbl").DataTable({
		dom: "Bfrtip",
		responsive: true,
		buttons: [
			{
				extend: "print",
				title: "Salary History",
				customize: addHeaderToPdf,
				messageTop: cycle
			},
			{
				extend: "csv",
				title: "Salary History"
			}
		]
	});
	checkForRows($salaryHistoryTbl, "#salary-history-tbl");

	const $fundsHistoryTbl = $("#funds-history-tbl").DataTable({
		dom: "Bfrtip",
		responsive: true,
		buttons: [
			{
				extend: "print",
				title: "History of Claimed Funds",
				customize: addHeaderToPdf,
				messageTop: cycle
			},
			{
				extend: "csv",
				title: "History of Claimed Funds"
			}
		]
	});
	checkForRows($fundsHistoryTbl, "#funds-history-tbl");
});