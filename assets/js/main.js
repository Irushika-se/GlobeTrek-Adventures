
 

function confirmDelete() {
    return confirm("Are you sure you want to delete this? This cannot be undone.");
}
 

document.addEventListener("DOMContentLoaded", function () {
    setTimeout(function () {
        let alerts = document.querySelectorAll(".alert-dismissible");
        alerts.forEach(function (alert) {
            // Use Bootstrap dismiss
            let bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 4000);
});
 
// Calculate total price on booking page
function calculateTotal() {
    let persons = parseInt(document.getElementById("num_persons").value) || 1;
    let pricePerPerson = parseFloat(document.getElementById("price_per_person").value) || 0;
    let total = persons * pricePerPerson;
    document.getElementById("total_price_display").innerText = "LKR " + total.toLocaleString();
    document.getElementById("total_price").value = total;
}
 
