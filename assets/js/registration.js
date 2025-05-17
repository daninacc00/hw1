function onResponse(data) {
    console.log(data);
    if (data.success) {
        window.location.href = 'login.php';
    } else {
        onError(data.message);
    }
}

function onError(message) {
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


function handleRegister(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('api/registration.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(onResponse)
        .catch(onError);
}

document.getElementById('registerForm').addEventListener('submit', handleRegister);