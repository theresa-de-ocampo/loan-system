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
});