function initHome() {


    

    //Obtener productos
    fetch('/../../controllers/obtenerProductos.php', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success){
            renderizarProductos(data.data);

            

        }else{
            showNotification(`Error al obtener productos: ${data.message}`, 'error');
        }
    })
    .catch(error => {
        showNotification('Error al obtener productos', 'error');
    });


}

//Obtener la clase de la etiqueta

function getClass(etiqueta){
    switch(etiqueta){
        case "Popular":
            return "popular";
            
        case "Nuevo":
            return "nuevo";
            
        case "Favorito":
            return "favorito";
            
        case "Edicion limitada":
            return "limitada";
            
        default:
            return "";
            

    }
}

//Animar productos
function animarProductos(){
    const productos = document.querySelectorAll(".producto");

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
          threshold: 0.2, //
        }
      );

      if(productos){
        productos.forEach(producto =>{
            observer.observe(producto);
        })
      }

}

function renderizarProductos(productos){
    const contenedorProductos = document.getElementById("productosContainer");
    contenedorProductos.innerHTML = "";

    //Objeto para formatear el precio a pesos colombianos

    const formatoCOP = new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        minimumFractionDigits: 0 // para que no muestre los ".00"
      });

    productos.forEach(producto => {

        const productoElement = document.createElement("div");
        productoElement.className = "producto";

        productoElement.innerHTML = `
                    ${producto.titulo_etiqueta !== "Sin etiqueta" ? `<div class="etiqueta ${getClass(producto.titulo_etiqueta)}">${producto.titulo_etiqueta}</div>` : ''}
                    <div class="producto-img-container">
                        <img src="${producto.image_url}" alt="${producto.nombre}" />
                    </div>
                    <div class="contenido">
                        <h3>${producto.nombre}</h3>
                        <div class="calificacion">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <span class="precio">${formatoCOP.format(producto.precio)}</span>
                        <p>
                            ${producto.descripcion}
                        </p>
                        <div class="producto-footer">
                            <a class="boton btnAddCarrito" data-id="${producto.id}">Agregar <i class="fas fa-cart-plus"></i></a>
                        </div>

                    </div>
            
        
        `;

        contenedorProductos.appendChild(productoElement);


        
    });

    animarProductos();

}


document.addEventListener("DOMContentLoaded", initHome);
