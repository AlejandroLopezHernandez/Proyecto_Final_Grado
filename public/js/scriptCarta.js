// script.js para IndexCarta.html

// Variables globales
let comanda = [];
const API_BASE_URL = "http://localhost:8000"; // Reemplaza con tu URL base

// Elementos del DOM
const comandaContainer = document.createElement("div");
comandaContainer.id = "comanda-container";
comandaContainer.style.display = "none";
document.body.appendChild(comandaContainer);

// Botón flotante para ver la comanda
const verComandaBtn = document.createElement("button");
verComandaBtn.id = "ver-comanda-btn";
verComandaBtn.textContent = "Ver Comanda";
verComandaBtn.className = "btn btn-primary";
verComandaBtn.style.position = "fixed";
verComandaBtn.style.top = "20px";
verComandaBtn.style.right = "20px";
verComandaBtn.style.zIndex = "1000";
document.body.appendChild(verComandaBtn);

// Botón burbuja para el contador
const contadorComanda = document.createElement("span");
contadorComanda.id = "contador-comanda";
contadorComanda.className = "contador-burbuja";
contadorComanda.textContent = "0";
verComandaBtn.appendChild(contadorComanda);

// Event listeners
verComandaBtn.addEventListener("click", toggleComandaContainer);

// Función para alternar la visibilidad del contenedor de la comanda
function toggleComandaContainer() {
  if (comandaContainer.style.display === "none") {
    actualizarComandaUI();
    comandaContainer.style.display = "block";
  } else {
    comandaContainer.style.display = "none";
  }
}

// Función para agregar un producto a la comanda
function agregarALaComanda(producto) {
  // Verificar si el producto ya está en la comanda
  const productoExistente = comanda.find((item) => item.id === producto.id);

  if (productoExistente) {
    productoExistente.cantidad += 1;
  } else {
    comanda.push({
      ...producto,
      cantidad: 1
    });
  }

  actualizarContadorComanda();
}

// Función para actualizar el contador de la comanda
function actualizarContadorComanda() {
  const totalItems = comanda.reduce((total, item) => total + item.cantidad, 0);
  contadorComanda.textContent = totalItems;
}

// Función para actualizar la interfaz de la comanda
function actualizarComandaUI() {
  let contenido = "<h3>Tu Comanda</h3>";

  if (comanda.length === 0) {
    contenido += "<p>No hay productos en la comanda</p>";
  } else {
    let total = 0;

    comanda.forEach((item) => {
      const subtotal = item.precio * item.cantidad;
      total += subtotal;

      contenido += `
        <div class="item-comanda">
          <div>
            <strong>${item.nombre}</strong> x${item.cantidad}
            <br>
            <small>${item.precio}€ c/u</small>
          </div>
          <div>
            ${subtotal.toFixed(2)}€
            <button class="boton-eliminar" data-id="${item.id}">X</button>
          </div>
        </div>
      `;
    });

    contenido += `
      <div style="margin-top: 15px; border-top: 1px solid #ddd; padding-top: 10px;">
        <strong>Total: ${total.toFixed(2)}€</strong>
      </div>
      <button id="finalizar-comanda" class="btn btn-primary" style="width: 100%; margin-top: 10px;">
        Finalizar Comanda
      </button>
    `;
  }

  comandaContainer.innerHTML = contenido;

  // Agregar event listeners a los botones de eliminar
  document.querySelectorAll(".boton-eliminar").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      const id = e.target.getAttribute("data-id");
      eliminarDeLaComanda(id);
    });
  });

  // Agregar event listener al botón de finalizar comanda
  if (document.getElementById("finalizar-comanda")) {
    document
      .getElementById("finalizar-comanda")
      .addEventListener("click", finalizarComanda);
  }
}

// Función para eliminar un producto de la comanda
function eliminarDeLaComanda(id) {
  comanda = comanda.filter((item) => item.id !== id);
  actualizarComandaUI();
  actualizarContadorComanda();
}

// Función para finalizar la comanda
function finalizarComanda() {
  alert("Comanda enviada con éxito. ¡Gracias por tu pedido!");
  comanda = [];
  actualizarComandaUI();
  actualizarContadorComanda();
  comandaContainer.style.display = "none";
}

// Función para cargar categorías de comida
async function cargarCategoriasComida() {
  try {
    const response = await fetch(`${API_BASE_URL}/comida/categoriasJson`);
    if (!response.ok) throw new Error("Error al cargar categorías");
    return await response.json();
  } catch (error) {
    console.error("Error:", error);
    return [];
  }
}

// Función para cargar comidas por categoría
async function cargarComidasPorCategoria(categoria) {
  try {
    const response = await fetch(
      `${API_BASE_URL}/comida/categoria/${categoria}`
    );
    if (!response.ok) throw new Error("Error al cargar comidas");
    return await response.json();
  } catch (error) {
    console.error("Error:", error);
    return [];
  }
}

// Función para carcar detalles de una comida
async function cargarDetalleComida(id) {
  try {
    const response = await fetch(`${API_BASE_URL}/comida/detalle/${id}`);
    if (!response.ok) throw new Error("Error al cargar detalles");
    return await response.json();
  } catch (error) {
    console.error("Error:", error);
    return null;
  }
}

// Función para carcar tipos de bebidas
async function cargarTiposBebidas() {
  try {
    const response = await fetch(`${API_BASE_URL}/bebida/tipos`);
    if (!response.ok) throw new Error("Error al cargar tipos de bebidas");
    return await response.json();
  } catch (error) {
    console.error("Error:", error);
    return [];
  }
}

// Función para carcar bebidas por tipo
async function cargarBebidasPorTipo(tipo) {
  try {
    const response = await fetch(`${API_BASE_URL}/bebida/${tipo}/registros`);
    if (!response.ok) throw new Error("Error al cargar bebidas");
    return await response.json();
  } catch (error) {
    console.error("Error:", error);
    return [];
  }
}

// Función para inicializar los botones de añadir a comanda
function inicializarBotonesComanda() {
  document.querySelectorAll(".boton-comanda").forEach((boton) => {
    boton.addEventListener("click", function () {
      // Obtener información del producto desde el DOM o atributos de datos
      const card = this.closest(".item-card");
      const nombre = card.querySelector(".item-name").textContent;
      const precioText = card.querySelector(".item-price").textContent;
      const precio = parseFloat(precioText.replace("€", "").trim());
      const id =
        card.getAttribute("data-id") || Math.random().toString(36).substr(2, 9);

      const producto = {
        id,
        nombre,
        precio,
        categoria: card.closest(".category-section").id
      };

      agregarALaComanda(producto);
    });
  });
}

// Función principal para cargar todos los datos
async function cargarDatos() {
  // Cargar categorías de comida
  const categoriasComida = await cargarCategoriasComida();

  // Cargar comidas por categoría
  for (const categoria of categoriasComida) {
    const comidas = await cargarComidasPorCategoria(categoria);
    // Aquí podrías actualizar el DOM con las comidas cargadas
  }

  // Cargar tipos de bebidas
  const tiposBebidas = await cargarTiposBebidas();

  // Cargar bebidas por tipo
  for (const tipo of tiposBebidas) {
    const bebidas = await cargarBebidasPorTipo(tipo);
    // Aquí podrías actualizar el DOM con las bebidas cargadas
  }

  // Inicializar botones de comanda
  inicializarBotonesComanda();
}

// Inicializar la aplicación cuando el DOM esté listo
document.addEventListener("DOMContentLoaded", cargarDatos);
