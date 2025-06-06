let currentTab = "profile";

function handleClickTab(e, link) {
    e.preventDefault();
    const tabName = link.getAttribute('data-tab');
    switchTab(tabName);
}

function switchTab(tabName) {
    if (tabName === currentTab) return;

    hideTab(currentTab);
    currentTab = tabName;
    showTab(currentTab);
    updateActiveLink(tabName);
}

function showTab(tabName) {
    const tabContent = document.getElementById(`tab-${tabName}`);
    console.log("showTab: ", tabContent)
    if (tabContent) {
        tabContent.classList.add('active');
        // Animazione fade-in opzionale
        tabContent.style.opacity = '0';
        setTimeout(() => {
            tabContent.style.opacity = '1';
        }, 50);
    }
}

function hideTab(tabName) {
    
    const tabContent = document.getElementById(`tab-${tabName}`);
    console.log("hideTab: ", tabContent)
    if (tabContent) {
        tabContent.classList.remove('active');
    }
}

function updateActiveLink(tabName) {
    // Rimuovi active da tutti i link
    tabLinks.forEach(link => {
        link.classList.remove('active');
    });

    // Aggiungi active al link corrente
    const activeLink = document.querySelector(`[data-tab="${tabName}"]`);
    if (activeLink) {
        activeLink.classList.add('active');
    }
}

const tabLinks = document.querySelectorAll('.tab-link');
const tabContents = document.querySelectorAll('.tab-content');

tabLinks.forEach(link => {
    link.addEventListener('click', (e) => handleClickTab(e, link));
});

showTab(currentTab);