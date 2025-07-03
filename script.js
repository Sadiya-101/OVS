// JavaScript for form validation and dynamic updates

// Validate form data before submission (for add and update forms)
function validateForm() {
    const name = document.querySelector('input[name="name"]').value;
    const dob = document.querySelector('input[name="dob"]').value;
    const username = document.querySelector('input[name="username"]').value;
    const password = document.querySelector('input[name="password"]') ? document.querySelector('input[name="password"]').value : '';

    // Check if all fields are filled in
    if (!name || !dob || !username || (password && !password.trim())) {
        alert("All fields are required!");
        return false;
    }

    // Additional validation for password (if present)
    if (password && password.length < 6) {
        alert("Password should be at least 6 characters long.");
        return false;
    }
    
    return true;
}

// Function to reset the add form fields after successful submission
function resetAddForm() {
    document.querySelector('input[name="name"]').value = '';
    document.querySelector('input[name="dob"]').value = '';
    document.querySelector('input[name="username"]').value = '';
    document.querySelector('input[name="password"]').value = '';
}

// Function to handle edit (update) form
function handleEditForm(event) {
    const form = event.target.closest('form');
    const name = form.querySelector('input[name="name"]');
    const dob = form.querySelector('input[name="dob"]');
    const username = form.querySelector('input[name="username"]');

    // Perform basic validation for editing
    if (!name.value || !dob.value || !username.value) {
        alert("All fields are required!");
        return false;
    }

    // If all values are present, submit the form
    form.submit();
}

// Function to confirm deletion
