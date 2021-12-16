// jshint esversion: 6
const profilePictureSrc = $("nav img").attr("src");
$("#profile-picture").attr("src", profilePictureSrc);
$("#show-passwords").on("click", toggleVisibility);