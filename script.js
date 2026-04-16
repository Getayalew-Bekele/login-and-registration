const loginForm = document.getElementById('loginForm');
if (loginForm) {
    loginForm.addEventListener('submit', function(e) {
        const username = document.getElementById('loginUsername');
        const password = document.getElementById('loginPassword');
        
        if (!username.value.trim()) {
            e.preventDefault();
            showMessage('Please enter username', 'error', 'login');
            username.focus();
            return false;
        }
        
        if (!password.value.trim()) {
            e.preventDefault();
            showMessage('Please enter password', 'error', 'login');
            password.focus();
            return false;
        }
    });
}

// Form validation for registration page
const registerForm = document.getElementById('registerForm');
if (registerForm) {
    registerForm.addEventListener('submit', function(e) {
        const firstName = document.getElementById('firstName');
        const lastName = document.getElementById('lastName');
        const department = document.getElementById('department');
        const gender = document.querySelector('input[name="gender"]:checked');
        const username = document.getElementById('regUsername');
        const password = document.getElementById('regPassword');
        
        let errors = [];
        
        if (!firstName.value.trim()) errors.push('First name is required');
        if (!lastName.value.trim()) errors.push('Last name is required');
        if (!department.value) errors.push('Please select a department');
        if (!gender) errors.push('Please select a gender');
        if (!username.value.trim()) errors.push('Username is required');
        if (!password.value) {
            errors.push('Password is required');
        } else if (password.value.length < 4) {
            errors.push('Password must be at least 4 characters');
        }
        
        if (errors.length > 0) {
            e.preventDefault();
            showMessage(errors.join('<br>'), 'error', 'register');
            return false;
        }
    });
    
    const passwordInput = document.getElementById('regPassword');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            if (this.value.length > 0 && this.value.length < 4) {
                this.style.borderColor = '#e74c3c';
            } else if (this.value.length >= 4) {
                this.style.borderColor = '#2ecc71';
            } else {
                this.style.borderColor = '#e0e0e0';
            }
        });
    }
}

function showMessage(message, type, formType) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `message-area ${type}`;
    messageDiv.innerHTML = message;
    
    const form = formType === 'login' ? loginForm : registerForm;
    const existingMessage = form.querySelector('.message-area');
    
    if (existingMessage) {
        existingMessage.remove();
    }
    
    form.insertBefore(messageDiv, form.firstChild);
    
    setTimeout(() => {
        if (messageDiv) messageDiv.remove();
    }, 5000);
}

const resetButtons = document.querySelectorAll('.btn-secondary');
resetButtons.forEach(btn => {
    btn.addEventListener('click', function(e) {
        if (!confirm('Are you sure you want to clear all form fields?')) {
            e.preventDefault();
        }
    });
});
