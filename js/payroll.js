// jshint esversion: 6
$(function() {
	const yearFolder = $("#cycle-holder").text();
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
			const proofSrc = "img/payroll/" + yearFolder + "/roi/" + $this.attr("data-proof");

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

	const $honorariumTbl = $("#honorarium-tbl").DataTable({
		dom: "Bfrtip",
		responsive: true,
		buttons: [
			{
				extend: "print",
				title: "Honorarium",
				customize: addHeaderToPdf,
				messageTop: cycle
			},
			{
				extend: "csv",
				title: "Honorarium"
			}
		]
	});
	checkForRows($honorariumTbl, "#honorarium-tbl");

	$.get("inc/salary-form.html", function(data) {
		$honorariumTbl.on("click", "td a.salary.pending", function() {
			let $tr = $(this).closest("tr");
			if ($tr.hasClass("child"))
				$tr = $tr.prev();
			const id = $tr.attr("data-guarantor-id");
			const row = $honorariumTbl.row($tr).data();

			createModal(data);
			$(".tingle-modal-box #id").val(id);
			$(".tingle-modal-box #name").val(row[0].display);
			$.getScript("js/image-upload.js");

			$("form").on("submit", function(e) {
				checkIfComplete(e);
			});
		});
	});

	$.get("inc/claimed-salary.html", function(data) {
		$honorariumTbl.on("click", "td a.salary.claimed", function() {
			let $this = $(this);
			let $tr = $this.closest("tr");
			if ($tr.hasClass("child"))
				$tr = $tr.prev();
			const row = $honorariumTbl.row($tr).data();
			const dateTimeClaimed = $this.attr("data-date-time-claimed");
			const proofSrc = "img/payroll/" + yearFolder + "/salary/" + $this.attr("data-proof");

			createModal(data);
			const $proof = $(".tingle-modal-box img");
			$proof.attr("src", proofSrc);
			$(".tingle-modal-box #position").text(row[1]);
			$(".tingle-modal-box #name").text(row[0].display);
			$(".tingle-modal-box #date-time-claimed").text(dateTimeClaimed);

			$proof.on("load", function() {
				const $modal = $(".tingle-modal");
				if (window.innerHeight <= $modal.height())
					$modal.addClass("tingle-modal--overflow");
			});
		});
	});

	$.get("inc/fund-form.html", function(data) {
		$honorariumTbl.on("click", "td a.fund.pending", function() {
			const treasurerName = $("#treasurer-name").text();
			const asstTreasurerName = $("#asst-treasurer-name").text();
			const treasurerId = $("#treasurer-name").closest("tr").attr("data-guarantor-id");
			const asstTreasurerId = $("#asst-treasurer-name").closest("tr").attr("data-guarantor-id");

			createModal(data);
			const claimerOptions = `
				<option value="${treasurerId}">${treasurerName}</option>
				<option value="${asstTreasurerId}">${asstTreasurerName}</option>
			`;
			$(".tingle-modal-box #claimer").html(claimerOptions);
			$.getScript("js/image-upload.js");

			$("form").on("submit", function(e) {
				checkIfComplete(e);
			});
		});
	});

	$.get("inc/claimed-fund.html", function(data) {
		$honorariumTbl.on("click", "td a.fund.claimed", function() {
			let $this = $(this);
			let $tr = $this.closest("tr");
			if ($tr.hasClass("child"))
				$tr = $tr.prev();
			const row = $honorariumTbl.row($tr).data();
			const dateTimeClaimed = $this.attr("data-date-time-claimed");
			const claimer = $this.attr("data-claimer");
			const purpose = $this.attr("data-purpose");
			const proofSrc = "img/payroll/" + yearFolder + "/" + $this.attr("data-proof");

			createModal(data);
			const $proof = $(".tingle-modal-box img");
			$proof.attr("src", proofSrc);
			$(".tingle-modal-box #name").text(claimer);
			$(".tingle-modal-box #purpose").text(purpose);
			$(".tingle-modal-box #date-time-claimed").text(dateTimeClaimed);

			$proof.on("load", function() {
				const $modal = $(".tingle-modal");
				if (window.innerHeight <= $modal.height())
					$modal.addClass("tingle-modal--overflow");
			});
		});
	});
});