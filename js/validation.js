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

function toggleVisibility() {
	let passwordField = document.querySelector("input[name='password']");
	let $passwordFields = $(".password");
	if ($passwordFields.attr("type") === "password") 
		$passwordFields.attr("type", "text");
	else
		$passwordFields.attr("type", "password");
}