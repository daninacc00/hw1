/**
 * Mostra un messaggio di errore per un campo
 */
function showError(input, message) {
    input.classList.add('error');

    // Rimuovi eventuali errori precedenti
    const existingError = input.parentNode.querySelector('.error-text');
    if (existingError) {
        existingError.remove();
    }

    // Aggiungi il nuovo messaggio di errore
    const errorSpan = document.createElement('span');
    errorSpan.className = 'error-text';
    errorSpan.textContent = message;
    input.parentNode.appendChild(errorSpan);
}

/**
 * Rimuove un messaggio di errore da un campo
 */
function removeError(input) {
    input.classList.remove('error');

    const existingError = input.parentNode.querySelector('.error-text');
    if (existingError) {
        existingError.remove();
    }
}

/**
 * Verifica se una email è valida
 */
function isValidEmail(email) {
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

/**
 * Verifica se una password è valida
 */
function isValidPassword(password) {
    const minLength = 8;
    const hasUpper = /[A-Z]/.test(password);
    const hasLower = /[a-z]/.test(password);
    const hasNumber = /[0-9]/.test(password);
    const hasSpecial = /[^a-zA-Z0-9]/.test(password);

    return password.length >= minLength && hasUpper && hasLower && hasNumber && hasSpecial;
}

/**
 * Valida la complessità della password in tempo reale
 */
function validatePasswordStrength(password) {
    const lengthCheck = document.getElementById('length');
    const upperCheck = document.getElementById('uppercase');
    const lowerCheck = document.getElementById('lowercase');
    const numberCheck = document.getElementById('number');
    const specialCheck = document.getElementById('special');

    // Lunghezza minima
    if (password.length >= 8) {
        lengthCheck.classList.add('valid');
    } else {
        lengthCheck.classList.remove('valid');
    }

    // Maiuscola
    if (/[A-Z]/.test(password)) {
        upperCheck.classList.add('valid');
    } else {
        upperCheck.classList.remove('valid');
    }

    // Minuscola
    if (/[a-z]/.test(password)) {
        lowerCheck.classList.add('valid');
    } else {
        lowerCheck.classList.remove('valid');
    }

    // Numero
    if (/[0-9]/.test(password)) {
        numberCheck.classList.add('valid');
    } else {
        numberCheck.classList.remove('valid');
    }

    // Carattere speciale
    if (/[^a-zA-Z0-9]/.test(password)) {
        specialCheck.classList.add('valid');
    } else {
        specialCheck.classList.remove('valid');
    }
}