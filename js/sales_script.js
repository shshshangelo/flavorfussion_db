navbar = document.querySelector('.header .flex .navbar');

document.querySelector('#menu-btn').onclick = () =>{
   navbar.classList.toggle('active');
   profile.classList.remove('active');
}

profile = document.querySelector('.header .flex .profile');

document.querySelector('#user-btn').onclick = () =>{
   profile.classList.toggle('active');
   navbar.classList.remove('active');
}

window.onscroll = () =>{
   navbar.classList.remove('active');
   profile.classList.remove('active');
}

// Swiper
document.addEventListener("DOMContentLoaded", function () {
   var swiper = new Swiper(".hero-slider", {
      loop: true,
      grabCursor: true,
      effect: "flip",
      pagination: {
         el: ".swiper-pagination",
         clickable: true,
      },
      autoplay: {
         delay: 2500, // Set the delay between slides in milliseconds (3 seconds in this example)
      },
   });
});


// Loader
function loader(){
   document.querySelector('.loader').style.display = 'none';
}

function fadeOut(){
   setInterval(loader, 1000);
}

window.onload = fadeOut;

// For top button scrollbar
document.addEventListener("DOMContentLoaded", function () {
   
   // Get the button
   var scrollToTopBtn = document.getElementById("scrollToTopBtn");

   // When the user scrolls down 20px from the top of the document, show the button
   window.onscroll = function () {
       if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
           scrollToTopBtn.style.display = "block";
       } else {
           scrollToTopBtn.style.display = "none";
       }
   };

   // When the user clicks on the button, scroll to the top of the document
   scrollToTopBtn.addEventListener("click", function () {
       document.body.scrollTop = 0; // For Safari
       document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE, and Opera
   });
});


