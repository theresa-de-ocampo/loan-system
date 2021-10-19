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

	if ($("#flag-holder").text() === "") {
		const today = new Date();
		const day = today.getDate();
		const month = today.getMonth() + 1;
		if (month == 10 && day == 19) {
			$("form").submit();
		}
	}

	$.get("inc/roi-form.html", function(data) {
		$sharesTbl.on("click", "td a", function() {
			let $tr = $(this).closest("tr");
			if ($tr.hasClass("child"))
				$tr = $tr.prev();
			const id = $tr.attr("data-guarantor-id");
			const row = $sharesTbl.row($tr).data();
			console.log(row);

			createModal(data);
			$(".tingle-modal-box #id").val(id);
			$(".tingle-modal-box #name").val(row[0].display);
			$.getScript("js/image-upload.js");

			$("form").on("submit", function(e) {
				checkIfComplete(e);
			});
		});
	});
});