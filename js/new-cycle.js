// jshint esversion: 6
$(function() {
	const positions = ["#auditor", "#treasurer", "#asst-treasurer"];
	let $existingDataSubjectPane;
	let $newDataSubjectPane;
	let $existingDataSubjectInputs;
	let $newDataSubjectInputs;

	function setVariables(qualifier) {
		$existingDataSubjectPane = $(qualifier + " .existing-data-subject");
		$newDataSubjectPane = $(qualifier + " .new-data-subject");
		$existingDataSubjectInputs = $(qualifier + " .existing-data-subject input");
		$newDataSubjectInputs = $(qualifier + " .new-data-subject input");
	}

	function displayExistingDataSubject(e) {
		const qualifier = e.data.qualifier;
		setVariables(qualifier);
		$newDataSubjectPane.css("display", "none");
		$existingDataSubjectPane.css("display", "block");
		$newDataSubjectInputs.each(function() {
			$(this).removeAttr("required");
		});
		$existingDataSubjectInputs.each(function() {
			$(this).attr("required", true);
		});
	}

	function displayNewDataSubject(qualifier) {
		setVariables(qualifier);
		$existingDataSubjectPane.css("display", "none");
		$newDataSubjectPane.css("display", "block");
		$existingDataSubjectInputs.each(function() {
			$(this).removeAttr("required");
		});
		$newDataSubjectInputs.each(function() {
			$(this).attr("required", true);
		});
	}

	function setExistingDataSubjectTbl(e) {
		let $tr = $(e.currentTarget);
		const $tbl = e.data.tbl;
		const qualifier = e.data.qualifier;

		let person = getPerson($tbl, $tr);
		$(qualifier + "-id").val(person[0]);
		$(qualifier + "-name").val(person[1]);

		const hasAccount = $tr.attr("data-with-account");
		const $withAccountPane = $(qualifier + " .account-details p");
		const $withoutAccountPane = $(qualifier + " .account-details .grid-wrapper");
		const $newAccountInputs = $(qualifier + " .account-details .grid-wrapper input");
		if (hasAccount === "0") {
			$withoutAccountPane.css("display", "grid");
			$withAccountPane.css("display", "none");
			$newAccountInputs.each(function() {
				$(this).attr("required", true);
			});
		}
		else {
			$withoutAccountPane.css("display", "none");
			$withAccountPane.css("display", "block");
			$(qualifier + " .account-details p b").text(hasAccount);
			$newAccountInputs.each(function() {
				$(this).removeAttr("required");
			});
		}
	}

	const $auditorTbl = $("#auditor-tbl").DataTable({
		dom: "Bfrtip",
		responsive: true,
		buttons: [
			{
				text: "New Data Subject",
				action: function() {
					displayNewDataSubject("#auditor");
				}
			}
		],
		order: [[3, "asc"]]
	});

	const $treasurerTbl = $("#treasurer-tbl").DataTable({
		dom: "Bfrtip",
		responsive: true,
		buttons: [
			{
				text: "New Data Subject",
				action: function() {
					displayNewDataSubject("#treasurer");
				}
			}
		],
		order: [[3, "asc"]]
	});

	const $asstTreasurerTbl = $("#asst-treasurer-tbl").DataTable({
		dom: "Bfrtip",
		responsive: true,
		buttons: [
			{
				text: "New Data Subject",
				action: function() {
					displayNewDataSubject("#asst-treasurer");
				}
			}
		],
		order: [[3, "asc"]]
	});

	const $tables = [$auditorTbl, $treasurerTbl, $asstTreasurerTbl];
	let i = 0;
	for (let qualifier of positions) {
		$tbl = $tables[i++];
		$(qualifier + " .new-data-subject button").on("click", {qualifier: qualifier}, displayExistingDataSubject);
		$tbl.on("click", "tbody tr", {qualifier: qualifier, tbl: $tbl}, setExistingDataSubjectTbl);
		$(qualifier + " input[id*='show-password']").on("click", {qualifier: qualifier}, toggleVisibility);
	}
});