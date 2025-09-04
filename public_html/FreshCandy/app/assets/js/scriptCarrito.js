// carritoEmergente.js - Funcionalidad para el carrito emergente



//importar authHelper
import { checkAuthStatus, redirectTo } from "./authHelper.js";

// Elementos DOM
const carritoBtn = document.getElementById("carrito-btn");
const carritoPanel = document.getElementById("carrito-panel");
const carritoOverlay = document.getElementById("carrito-overlay");
const cerrarCarritoBtn = document.getElementById("cerrar-carrito");
const seguirComprandoBtn = document.getElementById("seguir-comprando");


const formatoCOP = new Intl.NumberFormat("es-CO", {
  style: "currency",
  currency: "COP",
  minimumFractionDigits: 0,
});

document.addEventListener("DOMContentLoaded", function () {
  configurarEventos();
  cargarProductos();
});

async function finalizarPedido() {

  const finalizarPedidoBtn = document.getElementById("finalizar-pedido");
  finalizarPedidoBtn.disabled = true;
  finalizarPedidoBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
  

  const autenticado = await checkAuthStatus(2);

  if (!autenticado) {
    console.error("Error al autenticar");

    //redirigir a la página de login
    
    //notificacion

    showNotification("Debes registrarte o iniciar sesión para finalizar el pedido", "error");

    
    setTimeout(() => {
      finalizarPedidoBtn.disabled = false;
      finalizarPedidoBtn.innerHTML =  'Realizar pedido';
      redirectTo("views/users/authForm3.php");
    }, 2000);
    return;
  }

  const dataCarrito = obtenerDataCarrito();

  if (!dataCarrito) {
    console.error("Error al obtener los datos del carrito");
    return;
  }

  if (!validarDatosCarrito(dataCarrito)) {
    console.error("Datos del carrito inválidos");

    showNotification("Agrega productos al carrito para proceder", "error");
    finalizarPedidoBtn.disabled = false;
    finalizarPedidoBtn.innerHTML =  'Realizar pedido';
    return;
  }

  

  

  sendDataCarrito(dataCarrito, finalizarPedidoBtn);

  
}

function sendDataCarrito(dataCarrito, finalizarPedidoBtn) {



  const data = {
    productos: dataCarrito
  };

  fetch("/../controllers/CarritoController.php?action=saveCarrito", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  })
  .then((response) => {
    return response.json();
  })
  .then((data) => {

    if (!data.success) {
      console.error("Error al guardar el carrito: ", data.message);
      showNotification(data.message, "error");
      
      finalizarPedidoBtn.disabled = false;
      
      return;
    }

    console.log("Carrito guardado con éxito", data);

    setTimeout(() => {
      finalizarPedidoBtn.disabled = false;
      finalizarPedidoBtn.innerHTML =  'Realizar pedido';
      redirectTo("views/users/viewConfPedido.php");
    }, 600);
  })
  .catch((error) => { 
    showNotification("Error al enviar los datos del carrito: ", "error");
  });
  
}

function validarDatosCarrito(carrito) {
  // Verificar que el carrito exista y sea un array
  if (!Array.isArray(carrito)) {
    showNotification("El carrito no es válido", "error");
    return false;
  }

  // Verificar que el carrito no esté vacío
  if (carrito.length === 0) {
    showNotification("El carrito está vacío", "error");
    return false;
  }

  // Validar cada item del carrito
  for (const item of carrito) {
    // Verificar que tenga todas las propiedades requeridas
    if (
      !item.hasOwnProperty("id") ||
      !item.hasOwnProperty("cantidad") ||
      !item.hasOwnProperty("precio") ||
      !item.hasOwnProperty("titulo") ||
      !item.hasOwnProperty("imagen")
    ) {
      console.error("Item del carrito con estructura inválida:", item);
      return false;
    }

    // Validar ID
    if (!Number.isInteger(item.id) || item.id <= 0) {
      console.error("ID de producto inválido:", item.id);
      return false;
    }

    // Validar cantidad
    if (!Number.isInteger(item.cantidad) || item.cantidad <= 0) {
      console.error("Cantidad inválida para el producto:", item.titulo);
      return false;
    }

    // Validar precio
    if (typeof item.precio !== "number" || item.precio <= 0) {
      console.error("Precio inválido para el producto:", item.titulo);
      return false;
    }

    // Validar título
    if (typeof item.titulo !== "string" || item.titulo.trim() === "") {
      console.error("Título inválido para el producto ID:", item.id);
      return false;
    }

    // Validar URL de imagen
    if (typeof item.imagen !== "string" || item.imagen.trim() === "") {
      console.error("URL de imagen inválida para el producto:", item.titulo);
      return false;
    }
  }

  return true;
}

function obtenerDataCarrito() {
  try {
    const data = localStorage.getItem("carritoProductos");
    return data ? JSON.parse(data) : [];
  } catch (error) {
    return false;
  }
}



function limpiarCarrito() {
  localStorage.removeItem("carritoProductos");
  
}

function sumarCantidadProducto(id) {
  let carrito = JSON.parse(localStorage.getItem("carritoProductos")) || [];
  carrito = carrito.map((item) => {
    if (item.id === id) {
      item.cantidad++;
    }
    return item;
  });
  localStorage.setItem("carritoProductos", JSON.stringify(carrito));
}

function restarCantidadProducto(id) {
  let carrito = obtenerDataCarrito();

  if (!carrito || carrito.length === 0) {
    console.error("No hay carrito en el localStorage");
    return;
  }

  carrito = carrito.map((item) => {
    if (item.id === id) {
      item.cantidad--;
    }
    return item;
  });
  localStorage.setItem("carritoProductos", JSON.stringify(carrito));
}
export function cargarProductos() {
  //aqui traeriamos esto del localStorage
  let data = localStorage.getItem("carritoProductos");
  if (!data) {
    mostrarCarritoVacio();
    actualizarContadorCarrito();
    return;
  }

  let productos = JSON.parse(data);

  // Si no hay productos, mostrar carrito vacío
  if (!productos || productos.length === 0) {
    mostrarCarritoVacio();
    actualizarContadorCarrito();
    return;
  }

  const contenedorItems = document.querySelector("#carritoItems");
  contenedorItems.innerHTML = "";

  productos.forEach((producto) => {
    let item = document.createElement("div");
    item.className = "carrito-item";
    item.dataset.id = producto.id; // Agregar el ID como atributo de datos

    item.innerHTML = `
                <img src="${producto.imagen}" alt="${producto.titulo}" class="item-imagen" />
                <div class="item-info">
                <h4 class="item-nombre">${producto.titulo}</h4>
                <p class="item-precio">$${producto.precio}</p>
                
                <div class="item-acciones">
                    <div class="cantidad-control">
                    <button class="btn-cantidad restar" data-id="${producto.id}">
                        <i class="fas fa-minus"></i>
                    </button>
                    <span class="cantidad">${producto.cantidad}</span>
                    <button class="btn-cantidad sumar" data-id="${producto.id}">
                        <i class="fas fa-plus"></i>
                    </button>
                    </div>
                    <button class="btn-eliminar" data-id="${producto.id}" title="Eliminar del carrito">
                    <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
                </div>
            `;

    contenedorItems.appendChild(item);
  });

  const btnSumar = document.querySelectorAll(".sumar");
  const btnRestar = document.querySelectorAll(".restar");
  const btnEliminar = document.querySelectorAll(".btn-eliminar");

  // Funcionalidad para botones de cantidad
  btnSumar.forEach((btn) => {
    btn.addEventListener("click", function () {
      const cantidadElement = this.previousElementSibling;
      let cantidad = parseInt(cantidadElement.textContent);
      cantidadElement.textContent = cantidad + 1;
      const id = parseInt(this.getAttribute("data-id"));

      if (isNaN(id)) {
        console.error("ID de producto inválido");
        return;
      }
      sumarCantidadProducto(id);
      actualizarCarrito();
    });
  });

  btnRestar.forEach((btn) => {
    btn.addEventListener("click", function () {
      const cantidadElement = this.nextElementSibling;
      let cantidad = parseInt(cantidadElement.textContent);
      if (cantidad > 1) {
        cantidadElement.textContent = cantidad - 1;
        const id = parseInt(this.getAttribute("data-id"));

        if (isNaN(id)) {
          console.error("ID de producto inválido");
          return;
        }
        restarCantidadProducto(id);
        actualizarCarrito();
      }
    });
  });

  // Funcionalidad para eliminar productos
  btnEliminar.forEach((btn) => {
    btn.addEventListener("click", function () {
      const idARemover = parseInt(this.getAttribute("data-id"));
      if (!isNaN(idARemover)) {
        // Encontrar y eliminar el elemento del DOM
        const itemToRemove = document.querySelector(
          `.carrito-item[data-id="${idARemover}"]`
        );
        if (itemToRemove) {
          itemToRemove.remove();
        }

        // Actualizar el localStorage y la UI
        removerItem(idARemover);

        // Verificar si el carrito está vacío después de eliminar
        const itemsRestantes = document.querySelectorAll(".carrito-item");
        if (itemsRestantes.length === 0) {
          mostrarCarritoVacio();
        }
      }
    });
  });

  // Inicializar contador de carrito
  actualizarContadorCarrito();
  actualizarCarrito();
}

// Función para actualizar los cálculos del carrito
function actualizarCarrito() {
  let subtotal = 0;
  const items = obtenerDataCarrito();

  // Si no hay items, mostrar carrito vacío y salir
  if (!items || items.length === 0) {
    mostrarCarritoVacio();
    return;
  }

  items.forEach((item) => {
    const precio = parseFloat(item.precio);
    const cantidad = parseInt(item.cantidad);

    if (!isNaN(precio) && !isNaN(cantidad)) {
      subtotal += precio * cantidad;
    } else {
      console.error("Error al calcular el subtotal");
    }
  });

  // Actualizar subtotal y total
  document.getElementById("subtotal").textContent = `${formatoCOP.format(subtotal)}`;
  document.getElementById("total").textContent = `${formatoCOP.format(subtotal)}`;
}

function removerItem(id) {
  let carrito = obtenerDataCarrito();

  if (!carrito) {
    console.error("No hay carrito en el localStorage");
    return;
  }



  // Filtrar el item a eliminar
  carrito = carrito.filter((prod) => prod.id !== id);

  // Actualizar el localStorage
  localStorage.setItem("carritoProductos", JSON.stringify(carrito));

  // Actualizar el contador del carrito
  actualizarContadorCarrito();

  // Actualizar los totales
  actualizarCarrito();
}

// Función para actualizar contador de items en el icono
function actualizarContadorCarrito() {
  let items = JSON.parse(localStorage.getItem("carritoProductos")) || [];

  const contador = document.querySelector(".contador-carrito");

  if (items.length === 0) {
    contador.style.display = "none";
  } else {
    contador.style.display = "flex";
    contador.textContent = items.length;
  }
}

// Función para mostrar carrito vacío
function mostrarCarritoVacio() {
  const itemsContainer = carritoPanel.querySelector(".carrito-items");

  // Limpiar contenedor de items
  itemsContainer.innerHTML = "";

  // Crear mensaje de carrito vacío
  const carritoVacio = document.createElement("div");
  carritoVacio.className = "carrito-vacio";
  carritoVacio.innerHTML = `
        <i class="fas fa-face-sad-tear fa-4x" style="color: var(--secondary-color);"></i>
        <p>Tu carrito está vacío</p>
        `;

  // Agregar mensaje al contenedor
  itemsContainer.appendChild(carritoVacio);

  // Actualizar resumen
  document.getElementById("subtotal").textContent = "$0.00";
  document.getElementById("total").textContent = "$0.00";
}

function configurarEventos() {
  // Event listeners para abrir/cerrar carrito

  carritoBtn.addEventListener("click", abrirCarrito);
  cerrarCarritoBtn.addEventListener("click", cerrarCarrito);
  seguirComprandoBtn.addEventListener("click", cerrarCarrito);
  carritoOverlay.addEventListener("click", cerrarCarrito);

  const finalizarPedidoBtn = document.getElementById("finalizar-pedido");
  finalizarPedidoBtn.removeEventListener("click", finalizarPedido); // limpia duplicados
  finalizarPedidoBtn.addEventListener("click", finalizarPedido);
}

// Función para abrir el carrito
function abrirCarrito() {
  carritoPanel.classList.add("active");
  carritoOverlay.classList.add("active");
  document.body.style.overflow = "hidden"; // Prevenir scroll en el body

  cargarProductos();
}

// Función para cerrar el carrito
function cerrarCarrito() {
  carritoPanel.classList.remove("active");
  carritoOverlay.classList.remove("active");
  document.body.style.overflow = ""; // Restaurar scroll en el body
}
