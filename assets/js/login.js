function onResponse(data) {
    console.log(data);
    if (data.success) {
        window.location.href = 'index.php';
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

    const formData = new FormData(this);

    fetch('api/login.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(onResponse)
        .catch(onError);
}

document.getElementById('loginForm').addEventListener('submit', handleLogin);