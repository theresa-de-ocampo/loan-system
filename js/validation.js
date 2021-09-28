// jshint esversion: 6
$("form").attr("novalidate", true); // Disable HTML5 validation

function completeInputs() {
	let isComplete = true;
	$(":required").each(function() {
		if ($(this).val() === "")
			isComplete = false;
	});
	return isComplete;
}