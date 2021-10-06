// jshint esversion: 6
$("form").attr("novalidate", true); // Disable HTML5 validation

function checkIfComplete(e, formQualifier = "") {
	let isComplete = true;
	$(formQualifier + " :required").each(function() {
		if ($(this).val() === "")
			isComplete = false;
	});

	if (!isComplete) {
		e.preventDefault();
		alert("Please fill out all required fields!");
	}
	return isComplete;
}

function checkNumberOfShares(e, formQualifier = "") {
	let numberOfShare = $(formQualifier + " .number-of-share").val();
	console.log(numberOfShare);
	if (numberOfShare < 1 || numberOfShare > 5) {
		e.preventDefault();
		alert("Number of shares must be between 1 and 5 (inclusive).");
	}
}