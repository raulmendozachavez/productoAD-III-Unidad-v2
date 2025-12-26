// Validación de formularios de autenticación
document.addEventListener('DOMContentLoaded', function() {
    // Función para validar email
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    // Función para validar contraseña
    function isValidPassword(password) {
        return password.length >= 5;
    }

    // Validación para el formulario de registro
    const registerForm = document.querySelector('form[action*="register"]');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            const email = registerForm.querySelector('input[type="email"]');
            const password = registerForm.querySelector('input[type="password"]');
            
            // Validar email
            if (!isValidEmail(email.value)) {
                e.preventDefault();
                showError('Por favor ingresa un correo electrónico válido (ejemplo@dominio.com)');
                email.focus();
                return false;
            }
            
            // Validar contraseña
            if (!isValidPassword(password.value)) {
                e.preventDefault();
                showError('La contraseña debe tener al menos 5 caracteres');
                password.focus();
                return false;
            }
            
            return true;
        });
    }

    // Validación para el formulario de inicio de sesión
    const loginForm = document.querySelector('form[action*="login"]');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const email = loginForm.querySelector('input[type="email"]');
            const password = loginForm.querySelector('input[type="password"]');
            
            // Validar email
            if (!isValidEmail(email.value)) {
                e.preventDefault();
                showError('Por favor ingresa un correo electrónico válido');
                email.focus();
                return false;
            }
            
            // Validar contraseña
            if (!isValidPassword(password.value)) {
                e.preventDefault();
                showError('La contraseña debe tener al menos 5 caracteres');
                password.focus();
                return false;
            }
            
            return true;
        });
    }

    // Función para mostrar mensajes de error
    function showError(message) {
        // Eliminar mensajes de error existentes
        const existingError = document.querySelector('.client-error-message');
        if (existingError) {
            existingError.remove();
        }

        // Crear y mostrar el nuevo mensaje de error
        const errorDiv = document.createElement('div');
        errorDiv.className = 'client-error-message';
        errorDiv.style.color = '#d9534f';
        errorDiv.style.marginBottom = '15px';
        errorDiv.style.padding = '10px';
        errorDiv.style.backgroundColor = '#f8d7da';
        errorDiv.style.border = '1px solid #f5c6cb';
        errorDiv.style.borderRadius = '4px';
        errorDiv.textContent = '❌ ' + message;

        // Insertar el mensaje de error después del título del formulario
        const formTitle = document.querySelector('.form-container h2');
        if (formTitle) {
            formTitle.parentNode.insertBefore(errorDiv, formTitle.nextSibling);
        } else {
            // Si no encuentra el título, lo inserta al principio del formulario
            const form = document.querySelector('form');
            if (form) form.prepend(errorDiv);
        }

        // Hacer scroll hasta el mensaje de error
        errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    // Validación en tiempo real para los campos de email
    const emailInputs = document.querySelectorAll('input[type="email"]');
    emailInputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value && !isValidEmail(this.value)) {
                this.style.borderColor = '#d9534f';
            } else {
                this.style.borderColor = '';
            }
        });
    });

    // Validación en tiempo real para los campos de contraseña
    const passwordInputs = document.querySelectorAll('input[type="password"]');
    passwordInputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value && !isValidPassword(this.value)) {
                this.style.borderColor = '#d9534f';
            } else {
                this.style.borderColor = '';
            }
        });
    });
});
