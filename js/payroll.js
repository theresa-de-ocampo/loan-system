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
});