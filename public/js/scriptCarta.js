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
// Añade esto al inicio del archivo, después de las variables globales
const mesasDisponibles = Array.from({ length: 14 }, (_, i) => i + 1); // Mesas del 1 al 14

// Modifica la función actualizarComandaUI para incluir el selector de mesa
function actualizarComandaUI() {
  let contenido = "<h3>Tu Comanda</h3>";

  // Selector de mesa mejorado
  contenido += `
    <div class="mesa-select-container">
      <label class="mesa-select-label">Selecciona tu mesa:</label>
      <select id="select-mesa" class="mesa-select">
        <option value="">-- Elige una mesa --</option>
        ${mesasDisponibles
          .map(
            (mesa) => `
          <option value="${mesa}" ${
              mesa === 1 ? "selected" : ""
            }>Mesa ${mesa} ${mesa === 1 ? " (Recomendada)" : ""}</option>
        `
          )
          .join("")}
      </select>
    </div>
  `;
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

// Reemplaza la función finalizarComanda con esta versión mejorada
async function finalizarComanda() {
  const mesaSeleccionada = document.getElementById("select-mesa").value;

  if (!mesaSeleccionada) {
    alert("Por favor, selecciona una mesa");
    return;
  }

  if (comanda.length === 0) {
    alert("La comanda está vacía");
    return;
  }

  await imprimirTicket(mesaSeleccionada);
}

// Implementación de la función imprimirTicket
async function imprimirTicket(idMesa) {
  const total = comanda.reduce(
    (sum, item) => sum + item.precio * item.cantidad,
    0
  );

  // 1) Crear canvas con el ticket
  const ancho = 400;
  const lineHeight = 20;
  const padding = 20;
  const headerLines = 3;
  const altura =
    padding * 2 +
    headerLines * lineHeight +
    comanda.length * 2 * lineHeight +
    lineHeight;
  const canvas = document.createElement("canvas");
  canvas.width = ancho;
  canvas.height = altura;
  const ctx = canvas.getContext("2d");

  // Estilo del ticket
  ctx.fillStyle = "#ffffff";
  ctx.fillRect(0, 0, ancho, altura);
  ctx.fillStyle = "#000000";
  ctx.font = "16px sans-serif";
  let y = padding;

  // Cabecera del ticket
  ctx.fillText("El Cañaveral", padding, y);
  y += lineHeight;
  ctx.fillText(`Mesa: ${idMesa}`, padding, y);
  y += lineHeight;
  const now = new Date();
  ctx.fillText(`Fecha: ${now.toLocaleString()}`, padding, y);
  y += lineHeight;
  ctx.fillText("-----------------------------", padding, y);
  y += lineHeight;

  // Productos
  comanda.forEach((item) => {
    const subtotal = (item.precio * item.cantidad).toFixed(2);
    ctx.fillText(item.nombre, padding, y);
    y += lineHeight;
    ctx.fillText(
      `${item.cantidad} x ${item.precio.toFixed(2)} = ${subtotal} €`,
      padding + 10,
      y
    );
    y += lineHeight;
  });

  // Total
  ctx.fillText("-----------------------------", padding, y);
  y += lineHeight;
  ctx.fillText(`TOTAL: ${total.toFixed(2)} €`, padding, y);

  // 2) Abrir ventana con la imagen
  const dataURL = canvas.toDataURL("image/png");
  const imgWindow = window.open("");
  if (imgWindow) {
    imgWindow.document.write(`
    <html><head><title>Ticket Mesa ${idMesa}</title></head>
    <body style="margin:0; padding:10px; text-align:center; background:#f0f0f0;">
      <img src="${dataURL}" style="max-width:100%;"/><br>
      <button onclick="window.print()">Imprimir</button>
    </body></html>`);
  } else {
    // fallback descarga
    const link = document.createElement("a");
    link.href = dataURL;
    link.download = `ticket_mesa_${idMesa}.png`;
    link.click();
  }

  // 3) Guardar en BD
  try {
    const response = await fetch(`${API_BASE_URL}/comanda/guardar`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        mesaId: idMesa,
        productos: comanda,
        total: total,
        fecha: now.toISOString()
      })
    });

    if (!response.ok) throw new Error("Error al guardar comanda en servidor");
    const respData = await response.json();
    console.log("Comanda guardada en BD con id:", respData.id);

    // 4) Limpiar comanda
    comanda = [];
    actualizarComandaUI();
    actualizarContadorComanda();
    comandaContainer.style.display = "none";

    // Mostrar mensaje de éxito
    alert("Comanda enviada con éxito. ¡Gracias por tu pedido!");
  } catch (err) {
    console.error("Error guardando comanda:", err);
    alert("Error guardando la comanda en servidor");
  }
}
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

// Función para eliminar un producto de la comanda
function eliminarDeLaComanda(id) {
  comanda = comanda.filter((item) => item.id !== id);
  actualizarComandaUI();
  actualizarContadorComanda();
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
