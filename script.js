const totalImages = 5;
let currentIndex = 0;
let autoTimer;

const images = Array.from({ length: totalImages }, (_, i) =>
  document.getElementById(`carouselImage${i}`)
);
const dots = Array.from(document.querySelectorAll('.carousel_dot'));
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');

function goTo(index) {
  images[currentIndex].classList.remove('carousel_active');
  dots[currentIndex].classList.remove('active');
  currentIndex = (index + totalImages) % totalImages;
  images[currentIndex].classList.add('carousel_active');
  dots[currentIndex].classList.add('active');
}

function startAuto() {
  autoTimer = setInterval(() => goTo(currentIndex + 1), 3500);
}

function resetAuto() {
  clearInterval(autoTimer);
  startAuto();
}

prevBtn.addEventListener('click', () => { goTo(currentIndex - 1); resetAuto(); });
nextBtn.addEventListener('click', () => { goTo(currentIndex + 1); resetAuto(); });
dots.forEach(dot => {
  dot.addEventListener('click', () => { goTo(Number(dot.dataset.index)); resetAuto(); });
});

startAuto();