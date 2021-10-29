// jshint esversion: 6
function toggleVisibility(e) {
	let qualifier = "";
	if (typeof e.data !== "undefined")
		qualifier = e.data.qualifier;
	const $passwordFields = $(qualifier + " input[name*='password']");
	if ($passwordFields.first().attr("type") === "password")
		$passwordFields.each(function() {
			$(this).attr("type", "text");
		});
	else
		$passwordFields.each(function() {
			$(this).attr("type", "password");
		});
}