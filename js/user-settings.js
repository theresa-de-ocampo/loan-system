// jshint esversion: 6
const profilePictureSrc = $("nav img").attr("src");
$("#profile-picture").attr("src", profilePictureSrc);
$("#show-passwords").on("click", toggleVisibility);

$("form").on("submit", function(e) {
	if (checkIfComplete(e))
		if (checkUsername(e, $("#username").val()))
			if (checkEmail(e, $("#email").val()))
				if (checkPassword(e, $("#password").val()))
					confirmPassword(e, $("#password").val(), $("#confirm-password").val());
});