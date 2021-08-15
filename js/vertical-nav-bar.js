// jshint esversion: 6
let arrow = document.querySelectorAll(".fa-chevron-down");
for (var i = 0; i < arrow.length; i++) {
	arrow[i].addEventListener("click", (e)=>{
		let arrowParent = e.target.parentElement.parentElement;//selecting main parent of arrow
		arrowParent.classList.toggle("showMenu");
	});
}

let sidebar = document.querySelector(".vertical-nav-bar");
let sidebarBtn = document.querySelector(".fa-bars");
sidebarBtn.addEventListener("click", ()=>{
	sidebar.classList.toggle("close");
});