/**
 * Validation script per i form di login e registrazione
 */
document.addEventListener('DOMContentLoaded', function() {
    // Validazione del form di login
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Validazione username
            const username = document.getElementById('username');
            if (username.value.trim() === '') {
                showError(username, 'Username è obbligatorio');
                isValid = false;
            } else {
                removeError(username);
            }
            
            // Validazione password
            const password = document.getElementById('password');
            if (password.value === '') {
                showError(password, 'Password è obbligatoria');
                isValid = false;
            } else {
                removeError(password);
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    }
    
    // Validazione del form di registrazione
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        const passwordField = document.getElementById('password');
        const confirmField = document.getElementById('password_confirm');
        
        // Validazione real-time della password
        if (passwordField) {
            passwordField.addEventListener('input', function() {
                validatePasswordStrength(this.value);
            });
        }
        
        // Validazione real-time della conferma password
        if (confirmField && passwordField) {
            confirmField.addEventListener('input', function() {
                if (this.value !== passwordField.value) {
                    showError(this, 'Le password non corrispondono');
                } else {
                    removeError(this);
                }
            });
        }
        
        // Validazione all'invio del form
        registerForm.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Validazione username
            const username = document.getElementById('username');
            if (username.value.trim() === '') {
                showError(username, 'Username è obbligatorio');
                isValid = false;
            } else if (username.value.trim().length < 3) {
                showError(username, 'Username deve contenere almeno 3 caratteri');
                isValid = false;
            } else {
                removeError(username);
                
                // Verificare se l'username è già in uso (simulazione)
                checkUsernameAvailability(username.value.trim())
                    .then(available => {
                        if (!available) {
                            showError(username, 'Username già in uso');
                            isValid = false;
                        }
                    });
            }
            
            // Validazione email
            const email = document.getElementById('email');
            if (email.value.trim() === '') {
                showError(email, 'Email è obbligatoria');
                isValid = false;
            } else if (!isValidEmail(email.value.trim())) {
                showError(email, 'Email non valida');
                isValid = false;
            } else {
                removeError(email);
            }
            
            // Validazione password
            if (passwordField.value === '') {
                showError(passwordField, 'Password è obbligatoria');
                isValid = false;
            } else if (!isValidPassword(passwordField.value)) {
                showError(passwordField, 'Password non valida. Verifica i requisiti.');
                isValid = false;
            } else {
                removeError(passwordField);
            }
            
            // Validazione conferma password
            if (confirmField.value === '') {
                showError(confirmField, 'Conferma password è obbligatoria');
                isValid = false;
            } else if (confirmField.value !== passwordField.value) {
                showError(confirmField, 'Le password non corrispondono');
                isValid = false;
            } else {
                removeError(confirmField);
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    }
    
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
    
    /**
     * Verifica se un username è disponibile
     * Nota: Questa è una simulazione lato client, in un'app reale
     * dovresti fare una richiesta AJAX al server
     */
    function checkUsernameAvailability(username) {
        // Simulazione di una richiesta AJAX
        return new Promise((resolve) => {
            // In un'app reale, qui faresti una richiesta AJAX
            // Per ora restituiamo true per simulare che l'username sia disponibile
            setTimeout(() => {
                resolve(true);
            }, 300);
        });
    }
});