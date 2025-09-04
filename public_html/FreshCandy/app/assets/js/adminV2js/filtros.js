// Inicialización del filtrado
(function() {
    'use strict';
    
    // Estado de los filtros
    let currentFilter = null;
    
    // Elementos del DOM
    const filterBtn = document.querySelector('.filter-btn');

    filterBtn.addEventListener('click', mostrarMenuOptions);
    // Cuando el mouse sale del elemento
    filterBtn.addEventListener('mouseenter', mostrarMenuOptions )
    filterBtn.addEventListener('mouseleave', cerrarMenuOptions )
      
    const clearFiltersBtn = document.querySelector('.clear-filters-btn');
    const filterOptions = document.querySelectorAll('.filter-option');
    const menuOption = document.getElementById('filterMenu');

    menuOption.addEventListener('mouseenter', mostrarMenuOptions);
    menuOption.addEventListener('mouseleave', cerrarMenuOptions);
    
    // Inicializar eventos
    function initFilterControls() {
      // Selección de filtros
      filterOptions.forEach(option => {
        option.addEventListener('click', () => {
          const filterValue = option.dataset.filter;
          applyFilter(filterValue);
          updateActiveFilter(option);

          cerrarMenuOptions();
        });
      });
      
      // Limpiar filtros
      clearFiltersBtn.addEventListener('click', clearFilters);
    }
    
    function cerrarMenuOptions(){


      menuOption.classList.add('hidden');
      
    }
    function mostrarMenuOptions(){


      menuOption.classList.remove('hidden');
      
    }
    // Aplicar filtro
    function applyFilter(filterValue) {
      currentFilter = filterValue;
      clearFiltersBtn.disabled = false;
      
      // Aquí implementarías la lógica real de filtrado
      console.log('Aplicando filtro:', filterValue);
      // Ejemplo: filterProducts(filterValue);
      
      // Actualizar UI
      updateFilterButton(filterValue);
    }
    
    // Actualizar botón de filtro activo
    function updateFilterButton(filterValue) {
      const activeOption = Array.from(filterOptions).find(
        opt => opt.dataset.filter === filterValue
      );
      
      if (activeOption) {
        const icon = activeOption.querySelector('i').cloneNode(true);
        const text = activeOption.textContent.trim();
        
        filterBtn.innerHTML = '';
        filterBtn.appendChild(icon);
        filterBtn.insertAdjacentHTML('beforeend', 
          `<span class="filter-text">${text}</span>`);
        filterBtn.appendChild(document.createElement('i'))
          .className = 'fa-solid fa-chevron-down';
      }
    }
    
    // Marcar opción activa
    function updateActiveFilter(activeOption) {
      filterOptions.forEach(opt => {
        opt.classList.toggle('active', opt === activeOption);
      });
    }
    
    // Limpiar filtros
    function clearFilters() {
      currentFilter = null;
      clearFiltersBtn.disabled = true;
      
      // Restablecer botón de filtro
      filterBtn.innerHTML = `
        <i class="fa-solid fa-filter"></i>
        <span class="filter-text">Filtrar</span>
        <i class="fa-solid fa-chevron-down"></i>
      `;
      
      // Limpiar selección
      filterOptions.forEach(opt => opt.classList.remove('active'));
      
      // Aquí implementarías la lógica para limpiar el filtrado
      console.log('Filtros limpiados');
      // Ejemplo: filterProducts(null);
    }
    
    // Inicializar cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', initFilterControls);

    document.addEventListener('click', (e) => {
      if (!e.target.classList.contains('filter-menu') && !e.target.classList.contains('filter-btn')){
        cerrarMenuOptions();
        
      }


    })
    
    // Exponer funciones si es necesario
    window.ProductFilters = {
      applyFilter,
      clearFilters
    };
  })();