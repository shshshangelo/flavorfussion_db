let profile = document.querySelector('.header .flex .profile');

document.querySelector('#user-btn').onclick = () =>{
   profile.classList.toggle('active')
   navbar.classList.remove('active');
}

let navbar = document.querySelector('.header .flex .navbar');

document.querySelector('#menu-btn').onclick = () =>{
   navbar.classList.toggle('active');
   profile.classList.remove('active');
}

window.onscroll = () =>{
   profile.classList.remove('active');
   navbar.classList.remove('active');
}


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

subImages = document.querySelectorAll('.update-product .image-container .sub-images img');
mainImage = document.querySelector('.update-product .image-container .main-image img');

subImages.forEach(images =>{
   images.onclick = () =>{
      let src = images.getAttribute('src');
      mainImage.src = src;
   }
});