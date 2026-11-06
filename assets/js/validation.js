
 
// Validate registration form
function validateRegister() {
    let name = document.getElementById("full_name").value.trim();
    let email = document.getElementById("email").value.trim();
    let password = document.getElementById("password").value;
    let confirm = document.getElementById("confirm_password").value;
    let phone = document.getElementById("phone").value.trim();
 
    if (name === "") {
        alert("Please enter your full name.");
        return false;
    }
 
    if (email === "" || !email.includes("@")) {
        alert("Please enter a valid email address.");
        return false;
    }
 
    if (phone.length < 10) {
        alert("Please enter a valid phone number.");
        return false;
    }
 
    if (password.length < 6) {
        alert("Password must be at least 6 characters.");
        return false;
    }
 
    if (password !== confirm) {
        alert("Passwords do not match!");
        return false;
    }
 
    return true;
}
 
// Validate booking form
function validateBooking() {
    let date = document.getElementById("travel_date").value;
    let persons = document.getElementById("num_persons").value;
 
    if (date === "") {
        alert("Please select a travel date.");
        return false;
    }
 
    
    let selected = new Date(date);
    let today = new Date();
    if (selected <= today) {
        alert("Travel date must be in the future.");
        return false;
    }
 
    if (persons < 1) {
        alert("Number of persons must be at least 1.");
        return false;
    }
 
    return true;
}
 
// Validate contact form
function validateContact() {
    let name = document.getElementById("name").value.trim();
    let email = document.getElementById("email").value.trim();
    let message = document.getElementById("message").value.trim();
 
    if (name === "") {
        alert("Please enter your name.");
        return false;
    }
    if (!email.includes("@")) {
        alert("Please enter a valid email.");
        return false;
    }
    if (message.length < 10) {
        alert("Message is too short. Please provide more details.");
        return false;
    }
 
    return true;
}

