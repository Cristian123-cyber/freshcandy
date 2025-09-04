'use strict';
  
    // Elementos del DOM
    let modal, modalTitle, modalMessage, modalIcon, confirmBtn, cancelBtn;
  
    // Configuración de iconos por tipo de acción
    const ACTION_ICONS = {
      default: 'fa-triangle-exclamation',
      delete: 'fa-trash-can',
      warning: 'fa-exclamation',
      success: 'fa-circle-check',
      info: 'fa-circle-info'
    };
  
    // Callback para la acción confirmada
    let currentCallback = null;
  
    // Inicializar la modal
    function initDangerActionModal() {
      modal = document.getElementById('dangerActionModal');
      modalTitle = document.getElementById('dam-modal-title');
      modalMessage = document.getElementById('dam-modal-message');
      modalIcon = document.getElementById('dam-modal-icon');
      confirmBtn = document.querySelector('.dam-btn-confirm');
      cancelBtn = document.querySelector('.dam-btn-cancel');
      
      setupEventListeners();
    }
  
    // Configurar event listeners
    function setupEventListeners() {
      // Cerrar modal al hacer clic en los botones de cerrar/cancelar
      document.querySelector('.dam-close-modal').addEventListener('click', closeModal);
      cancelBtn.addEventListener('click', closeModal);
      
      // Cerrar modal al hacer clic fuera del contenido
      modal.addEventListener('click', (e) => {
        if (e.target === modal) {
          closeModal();
        }
      });
      
      // Cerrar con tecla ESC
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.style.display === 'block') {
          closeModal();
        }
      });
      
      // Confirmar acción
      confirmBtn.addEventListener('click', executeAction);
    }
  
    // Abrir la modal de confirmación
    export function showConfirmation(options) {
      if (!modal) {
        console.error('Modal no inicializada. Llama a initDangerActionModal() primero.');
        return;
      }
      
      // Configurar el contenido basado en las opciones
      modalTitle.textContent = options.title || 'Confirmar acción';
      modalMessage.textContent = options.message || '¿Estás seguro que deseas realizar esta acción?';
      
      // Establecer el tipo y el icono
      const actionType = options.type || 'default';
      modal.className = `dam-modal dam-type-${actionType}`;
      modalIcon.className = `dam-icon fa-solid ${ACTION_ICONS[actionType] || ACTION_ICONS.default}`;
      
      // Configurar el texto del botón de confirmación
      confirmBtn.textContent = options.confirmText || 'Confirmar';
      
      // Guardar el callback
      currentCallback = options.callback || null;
      
      // Mostrar la modal
      modal.style.display = 'block';
      document.body.style.overflow = 'hidden';
    }
  
    // Ejecutar la acción confirmada
    function executeAction() {
        if (typeof currentCallback === 'function') {
        currentCallback();
      }
      closeModal();
    }
  
    // Cerrar la modal
    function closeModal() {
      modal.style.display = 'none';
      document.body.style.overflow = 'auto';
      currentCallback = null;
    }
  
    // Inicialización cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', initDangerActionModal);