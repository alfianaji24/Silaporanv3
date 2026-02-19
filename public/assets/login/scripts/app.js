const inputs = document.querySelectorAll(".input-field");
const toggle_btn = document.querySelectorAll(".toggle");
const main = document.querySelector("main");
const bullets = document.querySelectorAll(".bullets span");
const images = document.querySelectorAll(".image");

inputs.forEach((inp) => {
    inp.addEventListener("focus", () => {
        inp.classList.add("active");
    });
    inp.addEventListener("blur", () => {
        if (inp.value != "") return;
        inp.classList.remove("active");
    });
});

toggle_btn.forEach((btn) => {
    btn.addEventListener("click", () => {
        main.classList.toggle("sign-up-mode");
    });
});

function moveSlider(indexOrEvent) {
    let index = (typeof indexOrEvent === 'object' && indexOrEvent.target)
        ? indexOrEvent.target.dataset.value : indexOrEvent;
    index = parseInt(index, 10) || 1;

    let currentImage = document.querySelector(`.img-${index}`);
    if (currentImage) {
        images.forEach((img) => img.classList.remove("show"));
        currentImage.classList.add("show");
    }

    const textSlider = document.querySelector(".text-group");
    const slideHeight = 4.4;
    if (textSlider) {
        textSlider.style.transform = `translateY(${-(index - 1) * slideHeight}rem)`;
    }

    bullets.forEach((bull) => bull.classList.remove("active"));
    const activeBullet = document.querySelector(`.bullets span[data-value="${index}"]`);
    if (activeBullet) activeBullet.classList.add("active");
}

bullets.forEach((bullet) => {
    bullet.addEventListener("click", moveSlider);
});

const totalSlides = bullets.length;

// Inisialisasi slide pertama saat halaman load
moveSlider(1);

// Auto-rotate teks dan gambar setiap 4 detik (otomatis mengikuti jumlah bullets)
let currentSlide = 1;
setInterval(() => {
    currentSlide = currentSlide >= totalSlides ? 1 : currentSlide + 1;
    moveSlider(currentSlide);
}, 4000);
