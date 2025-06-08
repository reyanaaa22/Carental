// Modal functions
function openModal() {
    document.getElementById('loginform').classList.add('show');
}

function closeModal() {
    document.getElementById('loginform').classList.remove('show');
}

// Password strength meter and validation
const password = document.getElementById('password');
const strengthBar = document.getElementById('password-strength-bar');
const requirements = document.querySelector('.password-requirements');
const confirmPass = document.getElementById('confirm_password');

// Password requirements
const requirementsList = [
    { test: (p) => p.length >= 8, text: 'At least 8 characters' },
    { test: (p) => p.length >= 12, text: 'At least 12 characters' },
    { test: (p) => /[A-Z]/.test(p), text: 'Contains uppercase letter' },
    { test: (p) => /[a-z]/.test(p), text: 'Contains lowercase letter' },
    { test: (p) => /[0-9]/.test(p), text: 'Contains number' },
    { test: (p) => /[^A-Za-z0-9]/.test(p), text: 'Contains symbol' }
];

// Update requirements display
function updateRequirementsDisplay() {
    const passVal = password.value;
    let isValid = true;

    const requirements = document.querySelectorAll('.requirements-list .requirement');
    
    requirements.forEach(req => {
        const text = req.textContent;
        
        if (text.includes('8 characters')) {
            const ok = passVal.length >= 8;
            req.style.color = ok ? '#33cc33' : '#f00';
            if (!ok) isValid = false;
        } else if (text.includes('uppercase')) {
            const ok = /[A-Z]/.test(passVal);
            req.style.color = ok ? '#33cc33' : '#f00';
            if (!ok) isValid = false;
        } else if (text.includes('lowercase')) {
            const ok = /[a-z]/.test(passVal);
            req.style.color = ok ? '#33cc33' : '#f00';
            if (!ok) isValid = false;
        } else if (text.includes('number')) {
            const ok = /[0-9]/.test(passVal);
            req.style.color = ok ? '#33cc33' : '#f00';
            if (!ok) isValid = false;
        } else if (text.includes('special character')) {
            const ok = /[^A-Za-z0-9]/.test(passVal);
            req.style.color = ok ? '#33cc33' : '#f00';
            if (!ok) isValid = false;
        }
    });

    return isValid;
}

// Update strength meter
function updateStrengthMeter() {
    const passVal = password.value;
    let strength = 0;
    
    // Calculate strength based on requirements
    requirementsList.forEach(req => {
        if (req.test(passVal)) strength += 25;
    });
    
    // Update strength bar
    strengthBar.style.width = `${strength}%`;
    
    // Update bar color based on strength
    if (strength === 0) {
        strengthBar.style.background = 'transparent';
    } else if (strength < 30) {
        strengthBar.style.background = '#f00';
    } else if (strength < 60) {
        strengthBar.style.background = '#ff9900';
    } else if (strength < 90) {
        strengthBar.style.background = '#33cc33';
    } else {
        strengthBar.style.background = '#00cc00';
    }
    
    // Add validation message
    if (strength === 100) {
        requirements.innerHTML += '<div style="color: #33cc33; margin-top: 8px;">Password is strong and meets all requirements!</div>';
    }
}

// Add event listeners for password fields
if (password) {
    password.addEventListener('input', function() {
        updateStrengthMeter();
        updateRequirementsDisplay();
    });
}

if (confirmPass) {
    confirmPass.addEventListener('input', function() {
        const passVal = password.value;
        const confirmVal = this.value;
        
        if (passVal !== confirmVal) {
            this.style.borderColor = '#f00';
            this.nextElementSibling?.remove();
            const error = document.createElement('span');
            error.textContent = 'Passwords do not match';
            error.style.color = '#f00';
            error.style.fontSize = '0.85rem';
            error.style.marginTop = '4px';
            this.parentNode.insertBefore(error, this.nextSibling);
        } else {
            this.style.borderColor = '';
            this.nextElementSibling?.remove();
        }
    });
}

// Form submission validation
const registerForm = document.querySelector('#registerform form');
if (registerForm) {
    registerForm.addEventListener('submit', function(e) {
        const password = document.getElementById('password');
        const confirmPass = document.getElementById('confirm_password');
        const passVal = password.value;
        const confirmVal = confirmPass.value;

        if (!updateRequirementsDisplay()) {
            e.preventDefault();
            alert('Password does not meet all requirements');
            return;
        }

        if (passVal !== confirmVal) {
            e.preventDefault();
            alert('Passwords do not match');
            return;
        }
    });
}

// Logout confirmation
function confirmLogout() {
    if (confirm('Are you sure you want to logout?')) {
        window.location.href = 'logout.php?logout=1';
    }
}

// Auto-initialize modal if not logged in
document.addEventListener('DOMContentLoaded', function() {
    if (!document.cookie.includes('login')) {
        openModal();
    }
});

// Handle alerts auto-hide
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.classList.add('fade-out');
            setTimeout(() => {
                alert.remove();
            }, 500);
        }, 3000);
    });
});

// Registration form validation
function validateRegistrationForm() {
    const form = document.getElementById('registrationForm');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        let isValid = true;
        const errors = [];

        // Get form fields
        const fullname = document.getElementById('fullname');
        const email = document.getElementById('email');
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        const contact = document.getElementById('contact');
        const dob = document.getElementById('dob');
        const address = document.getElementById('address');

        // Clear previous errors
        clearErrors();

        // Validate Full Name
        if (!fullname.value.trim()) {
            showError(fullname, 'Full name is required');
            isValid = false;
        }

        // Validate Email
        if (!email.value.trim()) {
            showError(email, 'Email is required');
            isValid = false;
        } else if (!isValidEmail(email.value)) {
            showError(email, 'Please enter a valid email address');
            isValid = false;
        }

        // Validate Password
        if (!password.value) {
            showError(password, 'Password is required');
            isValid = false;
        } else if (password.value.length < 8) {
            showError(password, 'Password must be at least 8 characters long');
            isValid = false;
        }

        // Validate Confirm Password
        if (password.value !== confirmPassword.value) {
            showError(confirmPassword, 'Passwords do not match');
            isValid = false;
        }

        // Validate Contact
        if (!contact.value.trim()) {
            showError(contact, 'Contact number is required');
            isValid = false;
        }

        // Validate Date of Birth
        if (!dob.value) {
            showError(dob, 'Date of birth is required');
            isValid = false;
        }

        // Validate Address
        if (!address.value.trim()) {
            showError(address, 'Address is required');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
        }
    });
}

// Helper functions for registration
function showError(input, message) {
    const formGroup = input.closest('.form-group');
    const error = document.createElement('div');
    error.className = 'error-message';
    error.textContent = message;
    formGroup.appendChild(error);
    input.classList.add('is-invalid');
}

function clearErrors() {
    document.querySelectorAll('.error-message').forEach(error => error.remove());
    document.querySelectorAll('.is-invalid').forEach(input => input.classList.remove('is-invalid'));
}

function isValidEmail(email) {
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}

// Initialize registration form validation
document.addEventListener('DOMContentLoaded', function() {
    validateRegistrationForm();
}); 