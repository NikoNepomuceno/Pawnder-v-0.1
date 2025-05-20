// This JS file is used for both login and register pages
// It handles the button interactions that navigate between the pages

document.addEventListener("DOMContentLoaded", function () {
    // Check which page we're on
    const currentPage = window.location.pathname;

    // Add the appropriate class to the container based on the current page
    const container = document.querySelector(".container");

    if (currentPage.includes("register")) {
        container.classList.add("register");
    } else if (currentPage.includes("login")) {
        container.classList.add("login");
    }

    // Handle the sign-up button click
    const signUpBtn = document.getElementById("sign-up-btn");
    if (signUpBtn) {
        signUpBtn.addEventListener("click", function (e) {
            // If the button is wrapped in an <a> tag, don't add additional event handling
            if (e.target.parentNode.tagName.toLowerCase() !== 'a') {
                window.location.href = "/register";
            }
        });
    }

    // Handle the sign-in button click
    const signInBtn = document.getElementById("sign-in-btn");
    if (signInBtn) {
        signInBtn.addEventListener("click", function (e) {
            // If the button is wrapped in an <a> tag, don't add additional event handling
            if (e.target.parentNode.tagName.toLowerCase() !== 'a') {
                window.location.href = "/login";
            }
        });
    }
}); 