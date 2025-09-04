
  document.addEventListener("DOMContentLoaded", function () {
    const carrusel = document.querySelector(".carrusel-animado");
    

    const observer = new IntersectionObserver(
      function (entries, observer) {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add("visible");
            observer.unobserve(entry.target); // Detener la observación después de la primera vez
          }
        });
      },
      {
        threshold: 0.2, // cuando el 30% del carrusel esté visible
      }
    );

    if (carrusel) {
      observer.observe(carrusel);
    }
    



    const caracteristicas = document.querySelectorAll(".caracteristica.animada");

    // Observer para elementos que entran en viewport
    const observerCaracteristicas = new IntersectionObserver(
        function (entries, observer) {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const item = entry.target;
                    
                    // Añadir delay según el índice
                    setTimeout(() => {
                        item.classList.add("visible");
                    }, index * 200);
                    
                    observer.unobserve(item);
                }
            });
        },
        {
            threshold: 0.2,
            rootMargin: '0px 0px -100px 0px'
        }
    );

    // Aplicar el observer a cada característica
    if (caracteristicas.length) {
        caracteristicas.forEach(caracteristica => {
            observerCaracteristicas.observe(caracteristica);
        });
    }
  });

