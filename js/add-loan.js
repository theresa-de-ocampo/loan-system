// jshint esversion: 6
$(function() {
	let $existingDataSubjectPane = $("#existing-data-subject");
	let $newDataSubjectPane = $("#new-data-subject");
	let $existingDataSubjectInputs = $("#existing-data-subject input");
	let $newDataSubjectInputs = $("#new-data-subject input");
	let $principal = $("#principal");
	let guarantorOutstanding;

	let $guarantorTbl = $("#guarantor-tbl").DataTable({
		dom: "frtip",
		responsive: true,
		order: [[3, "asc"]]
	});

	let $dataSubjectTbl = $("#data-subject-tbl").DataTable({
		dom: "Bfrtip", 
		responsive: true,
		buttons: [
			{
				text: "New Data Suject",
				action: function() {
					$existingDataSubjectPane.css("display", "none");
					$newDataSubjectPane.css("display", "block");
					if ($window.width() >= maxWidth)
						changeHeight("borrower-tab");
					$existingDataSubjectInputs.each(function() {
						$(this).removeAttr("required");
					});
					$newDataSubjectInputs.each(function() {
						$(this).attr("required", true);
					});
				}
			}
		],
		order: [[3, "asc"]]
	});

	$("#new-data-subject button").on("click", function() {
		$newDataSubjectPane.css("display", "none");
		$existingDataSubjectPane.css("display", "block");
		if ($window.width() >= maxWidth)
			changeHeight("borrower-tab");
		$newDataSubjectInputs.each(function() {
			$(this).removeAttr("required");
		});
		$existingDataSubjectInputs.each(function() {
			$(this).attr("required", true);
		});
	});

	$("#guarantor-content input[readonly]").on("click", function() {
		createModal("<div class='info'>Select the borrower's gurantor from the table.</div>");
	});

	$("#borrower-content input[readonly]").on("click", function() {
		createModal("<div class='info'>Select the borrower from the table, or click the <b>New Data Subject</b> button.</div>");
	});

	$("label[for='collateral']").on("click", function(e) {
		e.preventDefault();
		createModal("<div class='info'>Required if loan amount >= &#8369; 10,000</div>");
	});

	$("#guarantor-tbl").on("click", "tbody tr", function() {
		let $tr = $(this);
		let person = getPerson($guarantorTbl, $tr);
		$("#guarantor-id").val(person[0]);
		$("#guarantor-name").val(person[1]);

		guarantorOutstanding = parseFloat($tr.find("[data-outstanding]").attr("data-outstanding"));
	});

	$("#data-subject-tbl").on("click", "tbody tr", function() {
		let $tr = $(this);
		let person = getPerson($dataSubjectTbl, $tr);
		$("#borrower-id").val(person[0]);
		$("#borrower-name").val(person[1]);
	});

	$principal.on("change", function() {
		let principal = parseFloat($principal.val());
		let $collateral = $("#collateral");
		if (principal >= 10000)
			$collateral.attr("required", true);
		else
			$collateral.removeAttr("required");
	});

	$("form").on("submit", function(e) {
		if ($existingDataSubjectPane.is(":visible"))
			$("#fname, #mname, #lname, #contact-no, #bday, #address").val("");
		else
			$("#borrower-id").val("");

		const principal = $("#principal").val();
		if (checkIfComplete(e))
			if (checkValue(e, principal))
				checkGuarantorOutstanding(e, guarantorOutstanding, principal);
	});

	/*
		(1)
		The tabbed panel contains animations. Specifically, it flips from the top, towards the viewer.
		For each tab, it goes from not being seen to visible.
		This was implemented using the opacity property.
		Using display (none -> block) instead will not work because you can't use transition for this property.
		In other words, your animating effect will be lost.

		(2)
		The tabbed panel is contained in the loan section, the white container in the UI.
		Each tab is also absolutely positioned to the top of the header tab.
		If you pick a tab with short content, there's an extra whitespace below the loan section.
		This is due to the height of the tab with the longest content.
		This is especially undesirable because our overall background color is not white, but a very light blue color.
		In order to fix this, the ff. dynamically changes the height of the loan section, and the tabs' content.
	*/
	let $loanSection = $("#loan");
	let $guarantorContent = $("#guarantor-content");
	let $borrowerContent = $("#borrower-content");
	let $dealingsContent = $("#dealings-content");
	let $submitContent = $("#submit-content");
	let $contentTabs = $(".tab-content");
	let offset = 110 + $("h3").outerHeight(true) + $("hr").outerHeight(true);
	let $window = $(window);
	let maxWidth = 850;

	function changeHeight(id, extra = 0) {
		switch (id) {
			case "guarantor-tab":
				$guarantorContent.css({"height": "auto", "overflow": "visible"});
				$loanSection.css("height",  $guarantorContent.outerHeight(true) + offset + extra);
				$contentTabs.not($guarantorContent).css({"height": 0, "overflow": "hidden"});
				break;
			case "borrower-tab":
				$borrowerContent.css({"height": "auto", "overflow": "visible"});
				$loanSection.css("height", $borrowerContent.outerHeight(true) + offset + extra);
				$contentTabs.not($borrowerContent).css({"height": 0, "overflow": "hidden"});
				break;
			case "dealings-tab":
				$dealingsContent.css({"height": "auto", "overflow": "visible"});
				$loanSection.css("height", $dealingsContent.outerHeight(true) + offset + extra);
				$contentTabs.not($dealingsContent).css({"height": 0, "overflow": "hidden"});
				break;
			case "submit-tab":
				$submitContent.css({"height": "auto", "overflow": "visible"});
				$loanSection.css("height", $submitContent.outerHeight(true) + offset + extra);
				$contentTabs.not($submitContent).css({"height": 0, "overflow": "hidden"});
				break;
		}
	}

	$("#tabbed-panel > input").on("click", function() {
		let id = $(this).attr("id");
		changeHeight(id);
	});

	$window.on("resize", function() {
		if ($window.width() >= maxWidth)
			changeHeight($("#tabbed-panel > input:checked").attr("id"));
	});

	$("table td.dtr-control").on("click", function() {
		if ($window.width() >= maxWidth)
			if ($(this).parents(".parent").length <= 0)
				changeHeight($("#tabbed-panel > input:checked").attr("id"), 47);
			else
				changeHeight($("#tabbed-panel > input:checked").attr("id"), -47);
	});

	if ($window.width() >= maxWidth) {
		$loanSection.css("height", $guarantorContent.outerHeight(true) + offset);
		$borrowerContent.css("height", 0);
		$dealingsContent.css("height", 0);
		$submitContent.css("height", 0);
	}
});