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
				targets: [0],
				width: 200
			},
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
		if (month == 11 && day == 30)
			$("form").submit();
	}

	$.get("inc/roi-form.html", function(data) {
		$sharesTbl.on("click", "td a.pending", function() {
			let $tr = $(this).closest("tr");
			if ($tr.hasClass("child"))
				$tr = $tr.prev();
			const id = $tr.attr("data-guarantor-id");
			const row = $sharesTbl.row($tr).data();

			createModal(data);
			$(".tingle-modal-box #id").val(id);
			$(".tingle-modal-box #name").val(row[0].display);
			$.getScript("js/image-upload.js");

			$("form").on("submit", function(e) {
				checkIfComplete(e);
			});
		});
	});

	$.get("inc/claimed-roi.html", function(data) {
		$sharesTbl.on("click", "td a.claimed", function() {
			let $this = $(this);
			let $tr = $this.closest("tr");
			if ($tr.hasClass("child"))
				$tr = $tr.prev();
			const row = $sharesTbl.row($tr).data();
			const dateTimeClaimed = $this.attr("data-date-time-claimed");
			const proofSrc = "img/payroll/2021/roi/" + $this.attr("data-proof");

			createModal(data);
			const $proof = $(".tingle-modal-box img");
			$proof.attr("src", proofSrc);
			$(".tingle-modal-box #name").text(row[0].display);
			$(".tingle-modal-box #date-time-claimed").text(dateTimeClaimed);

			$proof.on("load", function() {
				const $modal = $(".tingle-modal");
				if (window.innerHeight <= $modal.height())
					$modal.addClass("tingle-modal--overflow");
			});
		});
	});
});