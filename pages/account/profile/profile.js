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
    if (userData.first_name && userData.last_name) {
        avatarElement.textContent = userData.first_name[0].toUpperCase() + userData.last_name[0].toUpperCase();
    }

    const nameElement = document.getElementById('profile-name');
    nameElement.textContent = `${userData.first_name} ${userData.last_name}`;

    const memberSinceElement = document.getElementById('profile-member-since');
    if (userData.created_at) {
        const formattedDate = formatItalianDate(userData.created_at);
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
