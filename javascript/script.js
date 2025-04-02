// JavaScript code for handling form submission and toggling between login and registration forms
function toggleForm(formType) {
    document.getElementById("login-form").style.display = (formType === "login") ? "block" : "none";
    document.getElementById("register-form").style.display = (formType === "register") ? "block" : "none";
}