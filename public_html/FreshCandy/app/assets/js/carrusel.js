// Script para el funcionamiento del carrusel
document.addEventListener('DOMContentLoaded', () => {
    const carruselSlider = document.querySelector('.carrusel-slider');
    const slides = document.querySelectorAll('.carrusel-slide');
    const prevBtn = document.querySelector('.carrusel-prev');
    const nextBtn = document.querySelector('.carrusel-next');
    const indicators = document.querySelectorAll('.indicator');
    
    let currentSlide = 0;
    const totalSlides = slides.length;
    
    // Función para activar un slide
    function goToSlide(slideIndex) {
      // Actualizar el slider
      carruselSlider.className = 'carrusel-slider';
      carruselSlider.classList.add('slide-' + slideIndex);
      
      // Actualizar los indicadores
      indicators.forEach(ind => ind.classList.remove('active'));
      indicators[slideIndex].classList.add('active');
      
      // Activar/desactivar slides para animaciones
      slides.forEach(slide => slide.classList.remove('active'));
      slides[slideIndex].classList.add('active');
      
      // Actualizar el índice actual
      currentSlide = slideIndex;
    }
    
    // Iniciar con el primer slide activo
    slides[0].classList.add('active');
    
    // Eventos para los botones de navegación
    prevBtn.addEventListener('click', () => {
      let newIndex = currentSlide - 1;
      if (newIndex < 0) newIndex = totalSlides - 1;
      goToSlide(newIndex);
    });
    
    nextBtn.addEventListener('click', () => {
      let newIndex = currentSlide + 1;
      if (newIndex >= totalSlides) newIndex = 0;
      goToSlide(newIndex);
    });
    
    // Eventos para los indicadores
    indicators.forEach((indicator, index) => {
      indicator.addEventListener('click', () => {
        goToSlide(index);
      });
    });
    
    // Cambio automático cada 5 segundos
    function autoSlide() {
      let newIndex = currentSlide + 1;
      if (newIndex >= totalSlides) newIndex = 0;
      goToSlide(newIndex);
    }
    
    // Iniciar el cambio automático
    let slideInterval = setInterval(autoSlide, 5000);
    
    // Pausar el carrusel al pasar el mouse
    carruselSlider.addEventListener('mouseenter', () => {
      clearInterval(slideInterval);
    });
    
    // Reanudar el carrusel al quitar el mouse
    carruselSlider.addEventListener('mouseleave', () => {
      slideInterval = setInterval(autoSlide, 5000);
    });
  });