let currentSlide = 0;

function changeSlide(direction) {
    const slides = document.querySelectorAll('.slider-image');
    slides[currentSlide].classList.remove('active');
    currentSlide = (currentSlide + direction + slides.length) % slides.length;
    slides[currentSlide].classList.add('active');
}

// Initialiser la première image comme active
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.slider-image')[0].classList.add('active');
});


function updateDimensions(dimensionType, value) {
    const dimensionDisplay = document.getElementById(dimensionType + '-value');
    dimensionDisplay.innerText = value + ' cm';
}


