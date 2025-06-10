function validate() {
    let isValid = true;
    
    const username = document.getElementById('username');
    if (username.value.trim() === '') {
        showError(username, 'Username è obbligatorio');
        isValid = false;
    } else {
        removeError(username);
    }
    
    const password = document.getElementById('password');
    if (password.value === '') {
        showError(password, 'Password è obbligatoria');
        isValid = false;
    } else {
        removeError(password);
    }
    
    return isValid;
}

function onResponse(data) {
    console.log(data);
    if (data.success) {
        window.location.href = '/index.php';
    } else {
        onError(data.message);
    }
}

function onError(message) {
    console.error('Errore:', message);
    
    const errorMessage = document.querySelector(".error-message");
    if (errorMessage) {
        errorMessage.innerHTML = "";
        errorMessage.classList.remove("hidden");
        
        const messageText = document.createElement("span");
        messageText.textContent = message;
        
        errorMessage.appendChild(messageText);
    }
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function handleLogin(e) {
    e.preventDefault();
    
    if (!validate()) {
        return;
    }
    
    const formData = new FormData();
    formData.append('username', document.getElementById('username').value);
    formData.append('password', document.getElementById('password').value);
    
    fetch('/api/auth/login.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(onResponse)
        .catch(onError);
}

const loginForm = document.getElementById('loginForm');
loginForm.addEventListener('submit', handleLogin);