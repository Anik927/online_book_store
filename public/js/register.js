// view/register_page/register.js
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("registerForm");

    form.addEventListener("submit", function (event) {
        // Collect field values
        const name = document.getElementById("name").value.trim();
        const email = document.getElementById("email").value.trim();
        const password = document.getElementById("password").value;
        const address = document.getElementById("address").value.trim();
        const phone = document.getElementById("phone").value.trim();

        let errors = [];

        // 1. Validate Name
        if (name === "") {
            errors.push("Full Name is required.");
        }

        // 2. Validate Email format
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            errors.push("Please enter a valid email address.");
        }

        // 3. Validate Password Length (Mandatory Requirement: >= 8 characters)
        if (password.length < 8) {
            errors.push("Password must be at least 8 characters long.");
        }

        // 4. Validate Address
        if (address === "") {
            errors.push("Mailing Address is required.");
        }

        // 5. Validate Phone Number (Basic numeric check)
        const phoneRegex = /^[0-9]{11,14}$/; // Adjust bounds based on local formats
        if (!phoneRegex.test(phone.replace(/[\s-+]/g, ""))) {
            errors.push("Please enter a valid phone number.");
        }

        // If there are validation failures, halt form processing
        if (errors.length > 0) {
            event.preventDefault(); // Stop form submission to controller
            
            // Remove any old JS error boxes if they exist
            const oldAlert = document.querySelector(".js-error-box");
            if (oldAlert) oldAlert.remove();

            // Build an inline error box container dynamically
            const errorBox = document.createElement("div");
            errorBox.className = "error-msg js-error-box";
            errorBox.innerHTML = errors.join("<br>");

            // Insert it at the top of the form container
            form.parentNode.insertBefore(errorBox, form);
            
            // Scroll smoothly up to the message box
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });
});