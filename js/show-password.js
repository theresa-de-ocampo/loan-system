// jshint esversion: 6
function toggleVisibility() {
	let passwordField = document.querySelector("input[name='password']");
	let $passwordFields = $(".password");
	if ($passwordFields.attr("type") === "password") 
		$passwordFields.attr("type", "text");
	else
		$passwordFields.attr("type", "password");
}

$("#show-password").on("click", toggleVisibility);