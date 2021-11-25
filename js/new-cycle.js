// jshint esversion: 6
$(function() {
	const positions = ["#treasurer", "#asst-treasurer"];
	let $existingDataSubjectPane;
	let $newDataSubjectPane;
	let $existingDataSubjectInputs;
	let $newDataSubjectInputs;
	let $withAccountPane;
	let $withoutAccountPane;
	let $newAccountInputs;

	function setVariables(qualifier) {
		$existingDataSubjectPane = $(qualifier + " .existing-data-subject");
		$newDataSubjectPane = $(qualifier + " .new-data-subject");
		$existingDataSubjectInputs = $(qualifier + " .existing-data-subject input");
		$newDataSubjectInputs = $(qualifier + " .new-data-subject input");
		$withAccountPane = $(qualifier + " .account-details p");
		$withoutAccountPane = $(qualifier + " .account-details .grid-wrapper");
		$newAccountInputs = $(qualifier + " .account-details .grid-wrapper input:not(.show-password)");
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
		setExistingDataSubjectTbl(null, $(qualifier + "-tbl").find("tr.selected"));
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
		$withoutAccountPane.css("display", "grid");
		$withAccountPane.css("display", "none");
		$newAccountInputs.each(function() {
			$(this).attr("required", true);
		});
	}

	function setExistingDataSubjectTbl(e, $tr) {
		let qualifier;
		if (e != null) {
			$tr = $(e.currentTarget);
			const $tbl = e.data.tbl;
			qualifier = e.data.qualifier;
			setVariables(qualifier);

			let person = getPerson($tbl, $tr);
			$(qualifier + "-id").val(person[0]);
			$(qualifier + "-name").val(person[1]);
		}
		
		const hasAccount = $tr.attr("data-with-account");
		if (hasAccount === "0" || hasAccount === undefined) {
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

	const $tables = [$treasurerTbl, $asstTreasurerTbl];
	let i = 0;
	for (let qualifier of positions) {
		$tbl = $tables[i++];
		$(qualifier + " .new-data-subject button").on("click", {qualifier: qualifier}, displayExistingDataSubject);
		$tbl.on("click", "tbody tr", {qualifier: qualifier, tbl: $tbl}, setExistingDataSubjectTbl);
		$(qualifier + " input[id*='show-password']").on("click", {qualifier: qualifier}, toggleVisibility);
	}

	function finalizeRequirements(qualifier) {
		if ($existingDataSubjectPane.is(":visible")) {
			$newDataSubjectInputs.each(function() {
				$(this).val("");
			});

			if ($withAccountPane.is(":visible")) {
				$newAccountInputs.each(function() {
					$(this).val("");
				});
			}
		}
		else {
			$existingDataSubjectInputs.each(function() {
				$(this).val("");
			});
		}
	}

	$("form").on("submit", function(e) {
		for (let qualifier of positions) {
			setVariables(qualifier);
			finalizeRequirements(qualifier);
		}

		if (checkIfComplete(e))
			if (checkNewCycleEntities(e))
				if (checkUsernames(e))
					if (checkEmails(e))
						if (checkPasswordsNC(e))
							confirmPasswordsNC(e);
	});
});