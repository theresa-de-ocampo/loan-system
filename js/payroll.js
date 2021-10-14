// jshint esversion: 6
$(function() {
	const $principalSummationTbl = $("#principal-summation-tbl").DataTable({
		dom: "Bfrtip",
		responsive: true,
		buttons: [
			{
				extend: "print",
				title: "Principal Summation",
				customize: addHeaderToPdf,
				messageTop: cycle
			},
			{
				extend: "csv",
				title: "Principal Summation"
			}
		]
	});
	const principalGrandTotal = $principalSummationTbl.column(1).data().sum();
	$("#principal-summation-tbl_wrapper + p span.amount").text(principalGrandTotal.toLocaleString("en"));
	checkForRows($principalSummationTbl, "#guarantors-tbl");

	const $interestSummationTbl = $("#interest-summation-tbl").DataTable({
		dom: "Bfrtip",
		responsive: true,
		buttons: [
			{
				extend: "print",
				title: "Interest Summation",
				customize: addHeaderToPdf,
				messageTop: cycle
			},
			{
				extend: "csv",
				title: "Interest Summation"
			}
		]
	});
	const interestGrandTotal = $interestSummationTbl.column(1).data().sum();
	$("#interest-summation-tbl_wrapper + p span.amount").text(interestGrandTotal.toLocaleString("en"));
	checkForRows($interestSummationTbl, "#interest-summation-tbl");

	const $sharesTbl = $("#shares-tbl").DataTable({
		dom: "Bfrtip",
		responsive: true,
		buttons: [
			{
				extend: "print",
				title: "Shares",
				customize: addHeaderToPdf,
				messageTop: cycle
			},
			{
				extend: "csv",
				title: "Shares"
			}
		],
		columnDefs: [
			{
				targets: [1],
				width: 50
			}
		]
	});
	checkForRows($sharesTbl, "#shares-tbl");
});