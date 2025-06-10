loadUserProfile();

function loadUserProfile() {
    const loadingElement = document.getElementById('loading');
    const errorElement = document.getElementById('error-message');
    const profileContent = document.getElementById('profile-content');

    loadingElement.style.display = 'block';
    errorElement.style.display = 'none';
    profileContent.style.display = 'none';

    fetch('/api/account/profile/getUserById.php')
        .then(result => result.json())
        .then(data => {
            if (!data.success) {
                console.error('Errore nel caricamento del profilo:', error);
            }

            populateProfile(data.data);
        }).catch(error => {
            console.error('Errore nel caricamento del profilo:', error);

            loadingElement.style.display = 'none';
            errorElement.style.display = 'block';

            if (error.message.includes('Accedi')) {
                setTimeout(function () {
                    window.location.href = '/pages/login/login.php';
                }, 2000);
            }
        });


}

function populateProfile(userData) {
    const loadingElement = document.getElementById('loading');
    const profileContent = document.getElementById('profile-content');

    loadingElement.style.display = 'none';

    const avatarElement = document.getElementById('profile-avatar');
    if (userData.nome && userData.cognome) {
        avatarElement.textContent = userData.nome[0].toUpperCase() + userData.cognome[0].toUpperCase();
    }

    const nameElement = document.getElementById('profile-name');
    nameElement.textContent = `${userData.nome} ${userData.cognome}`;

    const memberSinceElement = document.getElementById('profile-member-since');
    if (userData.data_registrazione) {
        const formattedDate = formatItalianDate(userData.data_registrazione);
        memberSinceElement.textContent = `Member Nike da ${formattedDate}`;
    }

    profileContent.style.display = 'flex';
}

function formatItalianDate(dateString) {
    const mesi = [
        'Gennaio', 'Febbraio', 'Marzo', 'Aprile', 'Maggio', 'Giugno',
        'Luglio', 'Agosto', 'Settembre', 'Ottobre', 'Novembre', 'Dicembre'
    ];

    const date = new Date(dateString);
    const mese = mesi[date.getMonth()]
    const anno = date.getFullYear();

    return `${mese} ${anno}`;
}
