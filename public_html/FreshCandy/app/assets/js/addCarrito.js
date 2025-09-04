//import { cargarProductos } from "./scriptCarrito.js";


  

  
function agregarProductoAlCarrito(producto){
  let carrito = JSON.parse(localStorage.getItem("carritoProductos")) || [];

  //console.log(producto);

  let itemProd = {
    id: parseInt(producto.id),
    titulo: producto.nombre,
    precio: parseFloat(producto.precio),
    imagen: producto.image_url,
    cantidad: 1
  };


   // Verificar que el producto existe y no esté repetido
   if (itemProd && !carrito.some((item) => item.id === itemProd.id)) {
    carrito.push(itemProd);
    localStorage.setItem("carritoProductos", JSON.stringify(carrito));

    actualizarContadorCarrito();

    showNotification(`${itemProd.titulo} Agregado al carrito!!!`, "success");
  }

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
document.querySelector("#productosContainer").addEventListener("click", (e) => {
    if (e.target.classList.contains("btnAddCarrito")) {
  
      let idProducto = e.target.getAttribute("data-id");
      idProducto = parseInt(idProducto);

      if (isNaN(idProducto) || idProducto <= 0) {
        console.error("ID inválido:", idProducto);
        // Mostrar alerta, evitar que siga la lógica, etc.
        showNotification("ID inválido", "error");
        return;
    }

      //aca se trae el producto desde la bd, por ahora lo sacamos del arreglo de prueba arriba
      //se debe hacer una solicitud AJAX al servidor para obtener el producto
      //hacer llamada ajax al controlador obtenerProductos.php para obtner producto por ID
      fetch(`/../../controllers/obtenerProductos.php?id=${idProducto}`)
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {

            //Llamar a la funcion para agregar el producto al carrito

            
            agregarProductoAlCarrito(data.data);

            
          } else {
            console.error("Error al obtener el producto", data.message);
          }
        })
        .catch((error) => {
          console.error("Error al obtener el producto", error);
        });


  
     
    }
  });