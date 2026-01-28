// HAMBURGER MENU
const menuToggle = document.querySelector('.menu-toggle');
const nav = document.querySelector('header nav');

if (menuToggle && nav) {
    menuToggle.addEventListener('click', () => {
        nav.style.display = nav.style.display === 'flex' ? 'none' : 'flex';
    });
}


// COLOR THEME 
document.addEventListener("DOMContentLoaded", () => {
    // check localStorage on page load
    const savedTheme = localStorage.getItem("theme");
    if (savedTheme === "dark") {
        document.documentElement.classList.add("dark-mode");
    } else {
        document.documentElement.classList.remove("dark-mode");
    }
});


// fade-in on scroll (features and cards)
const cards = document.querySelectorAll('.feature-card');
if (cards) {
    const observer = new IntersectionObserver(entries=>{
        entries.forEach(entry=>{
            if(entry.isIntersecting){entry.target.classList.add('visible');}
        });
    },{threshold:0.2});
    cards.forEach(card=>observer.observe(card));
}
  

// ADMIN MODAL
const openAdminBtn = document.getElementById("openAdminModal");
const adminModal = document.getElementById("adminModal");
const closeAdminModal = document.getElementById("closeAdminModal");
const modalForm = document.getElementById("modalForm");

if ( openAdminBtn && closeAdminModal && adminModal){

openAdminBtn.addEventListener("click", (e) => {
    e.preventDefault();
    adminModal.classList.add("active");
    modalForm.classList.add('active-form');
});

closeAdminModal.addEventListener("click", () => {
    adminModal.classList.remove("active");
    modalForm.classList.remove('active-form');
});

// Close on background click
adminModal.addEventListener("click", (e) => {
    if (e.target === adminModal) {
        adminModal.classList.remove("active");
        modalForm.classList.remove('active-form');
    }
});
}
