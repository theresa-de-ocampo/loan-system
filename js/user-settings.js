// jshint esversion: 6
const profilePictureSrc = $("nav img").attr("src");
console.log(profilePictureSrc);
$("#profile-picture").attr("src", profilePictureSrc);