document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('api/login.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Login effettuato con successo!');
            // Reindirizza alla dashboard
            window.location.href = 'dashboard.php';
        } else {
            alert('Errore: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Errore:', error);
        alert('Errore di connessione');
    });
});