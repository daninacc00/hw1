// JavaScript modificato per caricare dati tramite API
let sliderImages = [];
let currentIndex = 0;
const itemsPerPage = 3;
const step = 1;

const container = document.getElementById("slider-container");
const prevButton = document.querySelector(".slider-controls .slider-btn.prev");
const nextButton = document.querySelector(".slider-controls .slider-btn.next");

// Inizializzazione dei bottoni
prevButton.innerHTML = "&#10094;";
nextButton.innerHTML = "&#10095;";

// Funzione per caricare le immagini dall'API
async function loadSliderImages() {
    fetch('/api/slider-images.php')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = response.json();
            return result;
        })
        .then(result => {
            console.log(result)
            if (!result.success) {
                throw new Error(result.error || 'Errore nel caricamento dei dati');
            }

            sliderImages = result.data;

            if (sliderImages.length === 0) {
                container.innerHTML = '<div class="no-data">Nessuna immagine disponibile</div>';
                return;
            }

            // Crea lo slider con i dati caricati
            createSlider();

            // Inizializza lo slider
            updateSlider();
        })
        .catch(error => {
            console.error('Errore nel caricamento delle immagini:', error);
            container.innerHTML = `<div class="error">Errore nel caricamento: ${error.message}</div>`;
        });
}

// Funzione per creare lo slider
function createSlider() {
    const sliderWrapper = document.createElement("div");
    sliderWrapper.classList.add("slider-wrapper");

    const sliderTrack = document.createElement("div");
    sliderTrack.classList.add("slider-track");

    sliderImages.forEach((imgData) => {
        const imageContainer = document.createElement("div");
        imageContainer.classList.add("image-container");

        const img = document.createElement("img");
        img.src = imgData.src;
        img.alt = imgData.alt;
        img.classList.add("slider-image");

        const overlayContainer = document.createElement("div");
        overlayContainer.classList.add("slider-image-overlay");

        const link = document.createElement("a");
        link.textContent = imgData.name;
        link.classList.add("button");

        overlayContainer.appendChild(link);

        if (imgData.isFreeShipping) {
            const chip = document.createElement("div");
            chip.textContent = "Spedizione gratuita";
            chip.classList.add("chip");
            chip.setAttribute("data-tooltip", "Idoneo per spedizione gratuita oltre le 200€");

            overlayContainer.appendChild(chip);
        }

        imageContainer.appendChild(img);
        imageContainer.appendChild(overlayContainer);
        sliderTrack.appendChild(imageContainer);
    });

    sliderWrapper.appendChild(sliderTrack);
    container.appendChild(sliderWrapper);
}

// Funzione per aggiornare lo slider
function updateSlider() {
    const sliderTrack = document.querySelector(".slider-track");

    if (!sliderTrack || sliderImages.length === 0) {
        return;
    }

    const imageWidth = 100 / itemsPerPage;
    const translateX = -(currentIndex * imageWidth);
    sliderTrack.style.transform = `translateX(${translateX}%)`;

    // Gestione stato bottoni
    if (currentIndex === 0) {
        prevButton.classList.add("disabled");
    } else {
        prevButton.classList.remove("disabled");
    }

    if (currentIndex + step > sliderImages.length - itemsPerPage) {
        nextButton.classList.add("disabled");
    } else {
        nextButton.classList.remove("disabled");
    }
}

prevButton.addEventListener("click", () => {
    if (currentIndex > 0) {
        currentIndex -= step;
        updateSlider();
    }
});

nextButton.addEventListener("click", () => {
    if (currentIndex + step <= sliderImages.length - itemsPerPage) {
        currentIndex += step;
        updateSlider();
    }
});

loadSliderImages();