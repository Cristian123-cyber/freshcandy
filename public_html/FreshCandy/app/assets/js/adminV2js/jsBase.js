import { logout } from '../../authHelper.js';

document.addEventListener('DOMContentLoaded', init);


function init() {

  setearFechaActual();

    setupNotificationDropdown();

    const logoutBtnAdmin = document.getElementById('logout-btn-admin');
    const logoutBtnPerfil = document.getElementById('logout-btn-perfil');
    logoutBtnAdmin.addEventListener('click', cerrarSesion);
    logoutBtnPerfil.addEventListener('click', cerrarSesion);

}



function cerrarSesion() {

  logout();

}


// Notification dropdown
const setupNotificationDropdown = () => {
    const notificationBtn = document.querySelector('.notification-btn');
    
    if (notificationBtn) {
        notificationBtn.addEventListener('click', function() {
            // Here you would toggle a notification dropdown
            // For now we'll just add a little animation
            this.classList.add('shake');
            setTimeout(() => {
                this.classList.remove('shake');
            }, 500);
        });
    }
};

function setearFechaActual() {
    const currentDateElement = document.getElementById("current-date");
    if (currentDateElement) {
      const options = {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
      };
      const currentDate = new Date();
      currentDateElement.textContent = currentDate.toLocaleDateString(
        "es-ES",
        options
      );
    }
  }





 