// =============================================
// FUNCIÓN PRINCIPAL: inicializarModuloProducto
// =============================================
function inicializarModuloProducto(mesa) {
  // =============================================
  // MÓDULO 1: ELEMENTOS DEL DOM Y CONFIGURACIÓN INICIAL
  // =============================================
  const panelTipos = document.getElementById("panel-nivel-1");
  const panelCategorias = document.getElementById("panel-nivel-2");
  const panelProductos = document.getElementById("panel-nivel-3");
  const panelOpciones = document.getElementById("panel-nivel-4");
  const panelComanda = document.getElementById("lineas-comanda");
  const btnImprimir = document.getElementById("btn-imprimir-ticket");
  if (btnImprimir) {
    btnImprimir.addEventListener("click", imprimirTicket);
  }

  if (!panelComanda) {
    console.warn(
      "moduloProducto: panelComanda no encontrado, abortando render de comanda."
    );
  }
  // Si faltan otros paneles, permitimos que la parte de comanda funcione si existe:
  if (!panelTipos || !panelCategorias || !panelProductos || !panelOpciones) {
    console.warn(
      "moduloProducto: faltan elementos de navegación de productos, listeners de producto no se asignarán."
    );
  }

  // Estado global de la aplicación
  const estadoApp = {
    comandasPorMesa: {}, // se cargará desde localStorage
    mesaActiva: null,
    productoSeleccionado: null,
    estiloSeleccionado: null,
    categoriaActual: null
  };

  // ---------- Funciones de persistencia ----------
  function cargarTodasComandas() {
    const json = localStorage.getItem("comandasPorMesa");
    try {
      estadoApp.comandasPorMesa = json ? JSON.parse(json) : {};
    } catch (e) {
      console.error("Error parseando comandasPorMesa de localStorage:", e);
      estadoApp.comandasPorMesa = {};
    }
  }

  function guardarTodasComandas() {
    // Guardamos el estado completo en localStorage sin recargar antes
    try {
      localStorage.setItem(
        "comandasPorMesa",
        JSON.stringify(estadoApp.comandasPorMesa)
      );
    } catch (e) {
      console.error("Error guardando comandasPorMesa en localStorage:", e);
    }
  }

  function cargarComandaDeLocalStorage(mesaId) {
    cargarTodasComandas();
    const key = String(mesaId);
    if (!estadoApp.comandasPorMesa[key]) {
      estadoApp.comandasPorMesa[key] = [];
    }
    estadoApp.mesaActiva = key;
    // Renderizamos la comanda de esta mesa
    renderizarComanda();
  }

  // Inicializa comandasPorMesa en memoria al inicio
  cargarTodasComandas();

  // Si se pasa mesa al cargar dinámicamente:
  if (mesa && mesa.id != null) {
    estadoApp.mesaActiva = String(mesa.id);
    // Mostrar nombre de la mesa en el título, si existe el elemento
    const tituloMesaEl = document.getElementById("nombre-mesa-activa");
    if (tituloMesaEl) {
      tituloMesaEl.textContent = ` ${mesa.nombre || mesa.id}`;
    }
    // Cargar comanda previa
    cargarComandaDeLocalStorage(mesa.id);
  } else {
    // Sin mesa al inicio, se espera seleccionar luego
    console.log(
      "inicializarModuloProducto sin mesa; se espera seleccionar mesa luego."
    );
  }

  // =============================================
  // MÓDULO 2: Funciones Auxiliares (getImagePath, scrollToPanel, etc.)
  // =============================================
  function getImagePath(nombre, categoria, tipoEspecial = null) {
    const nombreArchivo = nombre.replace(/\s+/g, "") + ".png";
    if (tipoEspecial === "estilo") {
      return "/imagen/default.png";
    }
    if (
      [
        "arroces_pastas",
        "mar",
        "entre_panes",
        "vegetariano",
        "vegano",
        "carnes",
        "entrantes",
        "ensaladas",
        "postres"
      ].includes(categoria.toLowerCase())
    ) {
      return `/imagen/Comidas/${nombreArchivo}`;
    }
    return `/imagen/Bebidas/${categoria.toLowerCase()}/${nombreArchivo}`;
  }
  function scrollToPanel(panelId) {
    const panel = document.getElementById(panelId);
    if (panel) {
      panel.scrollIntoView({ behavior: "smooth", block: "nearest" });
    }
  }

  // =============================================
  // MÓDULO 3: GESTIÓN DE NAVEGACIÓN POR PRODUCTOS
  // =============================================
  if (panelTipos) {
    panelTipos.addEventListener("click", async (e) => {
      const btn = e.target.closest(".categoria-btn");
      if (!btn) return;

      document
        .querySelectorAll("#panel-nivel-1 .categoria-btn")
        .forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");
      if (panelCategorias) panelCategorias.innerHTML = "";
      if (panelProductos) panelProductos.innerHTML = "";
      if (panelOpciones) panelOpciones.innerHTML = "";

      estadoApp.productoSeleccionado = null;
      estadoApp.estiloSeleccionado = null;
      estadoApp.categoriaActual = null;

      const tipo = btn.dataset.value;
      try {
        const response = await fetch(
          `/${tipo === "bebida" ? "bebida/tipos" : tipo}`
        );
        if (tipo === "bebida") {
          const result = await response.json();
          if (panelCategorias) {
            panelCategorias.innerHTML = result
              .map(
                (cat) => `
                            <button class="categoria-btn" data-nombre="${cat}" data-tipo="${tipo}">
                                ${cat.toUpperCase()}
                            </button>`
              )
              .join("");
          }
        } else {
          const text = await response.text();
          if (panelCategorias) panelCategorias.innerHTML = text;
        }
        scrollToPanel("panel-nivel-2");
      } catch (error) {
        console.error("Error cargando tipos:", error);
        if (panelCategorias)
          panelCategorias.innerHTML =
            '<p class="error">Error al cargar categorías</p>';
      }
    });
  }

  if (panelCategorias) {
    panelCategorias.addEventListener("click", async (e) => {
      const btn = e.target.closest(".categoria-btn");
      if (!btn) return;
      document
        .querySelectorAll("#panel-nivel-2 .categoria-btn")
        .forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");
      if (panelProductos) panelProductos.innerHTML = "";
      if (panelOpciones) panelOpciones.innerHTML = "";

      estadoApp.productoSeleccionado = null;
      estadoApp.estiloSeleccionado = null;

      const categoria = btn.dataset.nombre;
      estadoApp.categoriaActual = categoria;
      const tipoSeleccionado =
        document.querySelector("#panel-nivel-1 .categoria-btn.active")?.dataset
          .value || "comida";

      try {
        if (
          tipoSeleccionado === "bebida" &&
          ["cerveza", "destilados"].includes(categoria.toLowerCase())
        ) {
          const response = await fetch(`/bebida/${categoria}/estilos`);
          const estilos = await response.json();
          if (panelProductos) {
            panelProductos.innerHTML = estilos
              .map(
                (estilo) => `
                            <button class="producto-btn estilo-btn with-image"
                                    style="background-image: url('${getImagePath(
                                      estilo,
                                      categoria,
                                      "estilo"
                                    )}')"
                                    data-tipo="bebida"
                                    data-categoria="${categoria}"
                                    data-estilo="${estilo}">
                                <span class="btn-text">${estilo
                                  .toUpperCase()
                                  .replace(/([a-z])\s/g, "$1<br>")}</span>
                            </button>`
              )
              .join("");
            panelProductos.querySelectorAll(".estilo-btn").forEach((el) => {
              el.addEventListener("click", function () {
                panelProductos
                  .querySelectorAll(".estilo-btn")
                  .forEach((b) => b.classList.remove("active"));
                this.classList.add("active");
                estadoApp.estiloSeleccionado = this.dataset.estilo;
              });
            });
          }
        } else if (tipoSeleccionado === "bebida") {
          const response = await fetch(`/bebida/${categoria}/registros`);
          const bebidas = await response.json();
          if (panelProductos) {
            panelProductos.innerHTML = bebidas
              .map(
                (bebida) => `
                            <button class="producto-btn with-image"
                                    style="background-image: url('${getImagePath(
                                      bebida.nombre,
                                      categoria
                                    )}')"
                                    data-tipo="bebida"
                                    data-id="${bebida.id}"
                                    data-nombre="${bebida.nombre}"
                                    data-precio="${bebida.pvp}"
                                    data-formato="${bebida.formato}">
                                <span class="btn-text">
                                    ${bebida.nombre
                                      .toUpperCase()
                                      .replace(/\s+/g, "<br>")}<br>
                                    ${
                                      bebida.formato
                                        ? bebida.formato.toUpperCase()
                                        : ""
                                    }
                                </span>
                            </button>`
              )
              .join("");
          }
        } else {
          const response = await fetch(
            `/${tipoSeleccionado}/categoria/${categoria}`
          );
          let data;
          try {
            data = await response.json();
          } catch {
            data = null;
          }
          if (Array.isArray(data)) {
            if (panelProductos) {
              panelProductos.innerHTML = data
                .map(
                  (producto) => `
                                <button class="producto-btn with-image"
                                        style="background-image: url('${getImagePath(
                                          producto.nombre,
                                          categoria
                                        )}')"
                                        data-tipo="${tipoSeleccionado}"
                                        data-id="${producto.id}"
                                        data-nombre="${producto.nombre}"
                                        data-precio="${producto.pvp}">
                                    <span class="btn-text">${producto.nombre
                                      .toUpperCase()
                                      .replace(/\s+/g, "<br>")}</span>
                                </button>`
                )
                .join("");
            }
          } else {
            const text = await response.text();
            if (panelProductos) panelProductos.innerHTML = text;
          }
        }
        scrollToPanel("panel-nivel-3");
      } catch (error) {
        console.error("Error cargando productos:", error);
        if (panelProductos)
          panelProductos.innerHTML =
            '<p class="error">Error al cargar productos</p>';
      }
    });
  }

  // =============================================
  // MÓDULO 4: GESTIÓN DE PRODUCTOS Y OPCIONES
  // =============================================
  if (panelProductos && panelOpciones) {
    panelProductos.addEventListener("click", async (e) => {
      const btn = e.target.closest(".producto-btn");
      if (!btn) return;

      const tipo = btn.dataset.tipo;
      const nombre = btn.dataset.nombre;
      const precio = parseFloat(btn.dataset.precio) || 0;
      const id = btn.dataset.id;
      const estilo = btn.dataset.estilo;
      const categoria = btn.dataset.categoria;
      const formato = btn.dataset.formato;

      // Nivel 4 para bebidas con estilo
      if (tipo === "bebida" && estilo) {
        try {
          const response = await fetch(
            `/bebida/${categoria}/${estilo}/registros`
          );
          const bebidas = await response.json();
          panelOpciones.innerHTML = `
                        <div class="cabecera-opciones">
                            <h5>${estilo.toUpperCase()}</h5>
                            <div class="input-group">
                                <input type="text" class="opcion-input" placeholder="Especificaciones">
                                <button class="confirmar-btn">Añadir</button>
                            </div>
                        </div>
                        <div class="contenedor-botones">
                            ${bebidas
                              .map(
                                (bebida) => `
                                <button class="producto-btn with-image"
                                        style="background-image: url('${getImagePath(
                                          bebida.nombre,
                                          categoria
                                        )}')"
                                        data-tipo="bebida"
                                        data-id="${bebida.id}"
                                        data-nombre="${bebida.nombre}"
                                        data-precio="${bebida.pvp}"
                                        data-formato="${bebida.formato}">
                                    <span class="btn-text">
                                        ${bebida.nombre
                                          .toUpperCase()
                                          .replace(/\s+/g, "<br>")}<br>
                                        ${
                                          bebida.formato
                                            ? bebida.formato.toUpperCase()
                                            : ""
                                        }
                                    </span>
                                </button>`
                              )
                              .join("")}
                        </div>`;
          // Eventos productos nivel 4
          panelOpciones.querySelectorAll(".producto-btn").forEach((el) => {
            el.addEventListener("click", function () {
              const nombreCompleto = `${this.dataset.nombre} ${
                this.dataset.formato || ""
              }`.trim();
              estadoApp.productoSeleccionado = {
                nombre: nombreCompleto,
                precio: parseFloat(this.dataset.precio) || 0,
                extras: []
              };
              agregarProducto(
                nombreCompleto,
                parseFloat(this.dataset.precio) || 0,
                []
              );
              this.classList.add("active");
            });
          });
          // Evento añadir comentario/especificación
          const btnConfirm = panelOpciones.querySelector(".confirmar-btn");
          if (btnConfirm) {
            btnConfirm.addEventListener("click", () => {
              const inputEl = panelOpciones.querySelector(".opcion-input");
              const espec = inputEl.value.trim();
              if (espec && estadoApp.productoSeleccionado) {
                agregarExtra(estadoApp.productoSeleccionado.nombre, espec);
                inputEl.value = "";
              }
            });
          }
          scrollToPanel("panel-nivel-4");
        } catch (error) {
          console.error("Error bebidas nivel 4:", error);
          panelOpciones.innerHTML =
            '<p class="error">Error al cargar opciones</p>';
        }
        return;
      }

      // Productos normales
      estadoApp.productoSeleccionado = {
        nombre:
          tipo === "bebida" ? `${nombre} ${formato || ""}`.trim() : nombre,
        precio: precio,
        id: id,
        tipo: tipo,
        extras: []
      };

      agregarProducto(estadoApp.productoSeleccionado.nombre, precio, []);

      // Cargar opciones si existen
      if (id) {
        try {
          const response = await fetch(`/producto/${id}/opciones`);
          const data = await response.json();
          const opciones = Array.isArray(data) ? data : [];
          if (opciones.length > 0 || tipo === "comida") {
            panelOpciones.innerHTML = "";
            // Cabecera comentario
            const cabecera = document.createElement("div");
            cabecera.className = "cabecera-opciones";
            cabecera.innerHTML = `
                            <h5>Opciones para ${estadoApp.productoSeleccionado.nombre}</h5>
                            <div class="input-group">
                                <input type="text" class="opcion-input" placeholder="Añadir comentario">
                                <button class="confirmar-btn">Añadir</button>
                            </div>`;
            const btnConf = cabecera.querySelector(".confirmar-btn");
            btnConf.addEventListener("click", () => {
              const inputEl = cabecera.querySelector(".opcion-input");
              const espec = inputEl.value.trim();
              if (espec && estadoApp.productoSeleccionado) {
                agregarExtra(estadoApp.productoSeleccionado.nombre, espec);
                inputEl.value = "";
              }
            });
            panelOpciones.appendChild(cabecera);
            if (opciones.length > 0) {
              const contBot = document.createElement("div");
              contBot.className = "contenedor-botones";
              opciones.forEach((op) => {
                const btnOp = document.createElement("button");
                btnOp.className = "opcion-btn";
                btnOp.innerHTML = op.replace(/\s+/g, "<br>");
                btnOp.title = op;
                btnOp.addEventListener("click", () => {
                  agregarExtra(estadoApp.productoSeleccionado.nombre, op);
                });
                contBot.appendChild(btnOp);
              });
              panelOpciones.appendChild(contBot);
            }
            scrollToPanel("panel-nivel-4");
          }
        } catch (error) {
          console.error("Error cargando opciones:", error);
        }
      }
    });
  }

  // =============================================
  // MÓDULO 5: GESTIÓN DE COMANDA Y LOCALSTORAGE
  // =============================================

  // Añade o incrementa producto en la comanda activa
  function agregarProducto(nombre, precio, extras = []) {
    if (!estadoApp.mesaActiva) {
      alert("Primero debes seleccionar una mesa");
      return;
    }
    const key = String(estadoApp.mesaActiva);
    if (!estadoApp.comandasPorMesa[key]) {
      estadoApp.comandasPorMesa[key] = [];
    }
    // Buscar existente por nombre+extras
    const existente = estadoApp.comandasPorMesa[key].find(
      (linea) =>
        linea.nombre === nombre &&
        JSON.stringify(linea.extras || []) === JSON.stringify(extras || [])
    );
    if (existente) {
      existente.cantidad = (existente.cantidad || 1) + 1;
    } else {
      estadoApp.comandasPorMesa[key].push({
        nombre,
        precio,
        extras: Array.isArray(extras) ? extras.slice() : [],
        cantidad: 1
      });
    }
    guardarTodasComandas();
    renderizarComanda();
    console.log(
      `agregarProducto llamado con mesaActiva= ${estadoApp.mesaActiva}  nombre= ${nombre}  extras=`,
      extras
    );
    console.log("Estado comandasPorMesa:", estadoApp.comandasPorMesa);
  }

  // Añade un extra a una línea existente
  function agregarExtra(nombreProducto, extra) {
    const key = String(estadoApp.mesaActiva);
    const comanda = estadoApp.comandasPorMesa[key] || [];
    const linea = comanda.find((l) => l.nombre === nombreProducto);
    if (linea) {
      if (!Array.isArray(linea.extras)) linea.extras = [];
      linea.extras.push(extra);
      guardarTodasComandas();
      renderizarComanda();
    }
  }

  // Disminuye cantidad o elimina línea
  function disminuirProductoAt(index) {
    const key = String(estadoApp.mesaActiva);
    const comanda = estadoApp.comandasPorMesa[key] || [];
    if (index < 0 || index >= comanda.length) return;
    const linea = comanda[index];
    if (!linea) return;
    if ((linea.cantidad || 1) > 1) {
      linea.cantidad = (linea.cantidad || 1) - 1;
    } else {
      comanda.splice(index, 1);
    }
    guardarTodasComandas();
    renderizarComanda();
  }

  // Renderiza el panel de comanda: líneas con nombre, “cantidad x precio = subtotal”, botones +/-, extras con “✕”, y total final
  function renderizarComanda() {
    const key = String(estadoApp.mesaActiva);
    const comanda = estadoApp.comandasPorMesa[key] || [];
    console.log(`renderizarComanda para mesa "${key}" con items:`, comanda);
    if (!panelComanda) return;
    panelComanda.innerHTML = "";

    comanda.forEach((linea, idx) => {
      const divLinea = document.createElement("div");
      divLinea.className = "linea-comanda";

      // Nombre del producto
      const nombreSpan = document.createElement("span");
      nombreSpan.textContent = linea.nombre;

      // Subtotal línea
      const cantidad = linea.cantidad || 1;
      const subtotal = linea.precio * cantidad;
      const detalleSpan = document.createElement("span");
      detalleSpan.textContent = `${cantidad} x ${linea.precio.toFixed(
        2
      )} € = ${subtotal.toFixed(2)} €`;

      // Controles +/-
      const controlesDiv = document.createElement("div");
      controlesDiv.className = "comanda-controles";
      const btnMenos = document.createElement("button");
      btnMenos.textContent = "-";
      btnMenos.addEventListener("click", () => disminuirProductoAt(idx));
      const btnMas = document.createElement("button");
      btnMas.textContent = "+";
      btnMas.addEventListener("click", () => {
        linea.cantidad = (linea.cantidad || 1) + 1;
        guardarTodasComandas();
        renderizarComanda();
      });
      controlesDiv.append(btnMenos, btnMas);

      divLinea.append(nombreSpan, detalleSpan, controlesDiv);

      if (Array.isArray(linea.extras) && linea.extras.length) {
        const ulExtras = document.createElement("ul");
        ulExtras.className = "lista-extras";
        linea.extras.forEach((extra, eIdx) => {
          const li = document.createElement("li");
          const texto = document.createElement("span");
          texto.textContent = extra;
          const btnDel = document.createElement("span");
          btnDel.textContent = "✕";
          btnDel.className = "eliminar-extra";
          btnDel.style.cursor = "pointer";
          btnDel.addEventListener("click", () => {
            linea.extras.splice(eIdx, 1);
            guardarTodasComandas();
            renderizarComanda();
          });
          li.append(texto, " ", btnDel);
          ulExtras.append(li);
        });
        divLinea.append(ulExtras);
      }

      panelComanda.append(divLinea);
    });

    // Total general
    const totalGeneral = comanda.reduce(
      (sum, linea) => sum + linea.precio * (linea.cantidad || 1),
      0
    );
    const divTotal = document.createElement("div");
    divTotal.className = "total-comanda";
    divTotal.textContent = `TOTAL: ${totalGeneral.toFixed(2)} €`;
    panelComanda.append(divTotal);
  }

  // =============================================
  // MÓDULO 6: INTEGRACIÓN CON MESAS, TICKETS Y GUARDADO EN BD
  // =============================================

  function cambiarMesa(mesaId, nombreMesa) {
    estadoApp.mesaActiva = String(mesaId);
    if (panelProductos) panelProductos.innerHTML = "";
    if (panelOpciones) panelOpciones.innerHTML = "";
    estadoApp.productoSeleccionado = null;
    estadoApp.estiloSeleccionado = null;
    const tituloMesaEl = document.getElementById("nombre-mesa-activa");
    if (tituloMesaEl) {
      tituloMesaEl.textContent = nombreMesa
        ? `Mesa: ${nombreMesa}`
        : `Mesa: ${mesaId}`;
    }
    cargarComandaDeLocalStorage(estadoApp.mesaActiva);
  }
  window.cambiarMesa = cambiarMesa;

  // Imprime ticket: genera canvas centrado → ventana nueva + print → guarda en BD → limpia localStorage y redirige
  async function imprimirTicket() {
    const idMesa = estadoApp.mesaActiva;
    if (!idMesa || !estadoApp.comandasPorMesa[idMesa]?.length) {
      alert("Selecciona una mesa y añade productos");
      return;
    }
    const comanda = estadoApp.comandasPorMesa[idMesa];
    // Calcular total:
    const total = comanda.reduce(
      (sum, item) => sum + item.precio * (item.cantidad || 1),
      0
    );

    // 1) Crear canvas con el ticket centrado
    const ancho = 400;
    const lineHeight = 22;
    const padding = 20;
    // Líneas de cabecera + separadores + producto + extras + total
    let extraLines = 0;
    comanda.forEach((item) => {
      if (Array.isArray(item.extras)) extraLines += item.extras.length;
    });

    const cabeceraLines = 4;
    const mesaLines = 3; // Mesa, Fecha, separación
    const productoLines = comanda.length * 2;
    const extrasLines = extraLines;
    const totalLines = 2; // separación + TOTAL
    const altura =
      padding * 2 +
      cabeceraLines * lineHeight +
      mesaLines * lineHeight +
      productoLines * lineHeight +
      extrasLines * lineHeight +
      totalLines * lineHeight +
      lineHeight; // margen extra
    const canvas = document.createElement("canvas");
    canvas.width = ancho;
    canvas.height = altura;
    const ctx = canvas.getContext("2d");
    // Fondo blanco
    ctx.fillStyle = "#ffffff";
    ctx.fillRect(0, 0, ancho, altura);

    // Configuración para centrar texto:
    ctx.fillStyle = "#000000";
    ctx.textAlign = "center";
    // Usaremos xMedio = ancho/2
    const xMedio = ancho / 2;
    let y = padding;

    // 1.a) Cabecera centrada

    ctx.font = "bold 20px serif";
    const nombreRest = "Restaurante El Cañaveral";
    ctx.fillText(nombreRest, xMedio, y);
    y += lineHeight;

    ctx.font = "14px sans-serif";
    const direccion = "Av. de la ONU, 81, 28936 Móstoles, Madrid";
    ctx.fillText(direccion, xMedio, y);
    y += lineHeight;

    const telefono = "Tel: 91 234 56 78";
    ctx.fillText(telefono, xMedio, y);
    y += lineHeight;

    ctx.fillText("--------------------------------", xMedio, y);
    y += lineHeight;

    // 1.b) Mesa y fecha centrados
    const now = new Date();
    ctx.font = "14px sans-serif";
    ctx.fillText(`Mesa: ${idMesa}`, xMedio, y);
    y += lineHeight;
    ctx.fillText(`Fecha: ${now.toLocaleString()}`, xMedio, y);
    y += lineHeight;

    ctx.fillText("--------------------------------", xMedio, y);
    y += lineHeight;

    // 1.c) Productos y detalles
    ctx.font = "14px sans-serif";
    comanda.forEach((item) => {
      const cantidad = item.cantidad || 1;
      const subtotal = (item.precio * cantidad).toFixed(2);

      ctx.fillText(item.nombre, xMedio, y);
      y += lineHeight;
      // Detalle cantidad x precio = subtotal
      const detalle = `${cantidad} x ${item.precio.toFixed(2)} = ${subtotal} €`;
      ctx.fillText(detalle, xMedio, y);
      y += lineHeight;

      if (Array.isArray(item.extras) && item.extras.length) {
        item.extras.forEach((extra) => {
          ctx.fillText(`+ ${extra}`, xMedio, y);
          y += lineHeight;
        });
      }
    });

    ctx.fillText("--------------------------------", xMedio, y);
    y += lineHeight;
    ctx.font = "bold 16px sans-serif";
    ctx.fillText(`TOTAL: ${total.toFixed(2)} €`, xMedio, y);
    y += lineHeight;

    // 2) Convertir a dataURL y abrir ventana nueva con estilo adecuado
    const dataURL = canvas.toDataURL("image/png");
    const imgWindow = window.open(
      "",
      "_blank",
      "toolbar=no,location=no,menubar=no"
    );
    if (imgWindow) {
      imgWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Ticket Mesa ${idMesa}</title>
                <style>
                    body {
                        margin: 0;
                        padding: 10px;
                        text-align: center;
                        background: #f0f0f0;
                    }
                    img {
                        max-width: 100%;
                        border: 1px solid #ccc;
                        background: white;
                        padding: 10px;
                    }
                    #btn-print {
                        margin-top: 10px;
                        padding: 8px 16px;
                        font-size: 14px;
                        cursor: pointer;
                    }
                    @media print {
                        #btn-print { display: none; }
                    }
                </style>
            </head>
            <body>
                <img src="${dataURL}" alt="Ticket Mesa ${idMesa}"/><br/>
                <button id="btn-print" onclick="window.print()">Imprimir</button>
            </body>
            </html>`);
      imgWindow.document.close();
    } else {
      // fallback: descarga directa
      const link = document.createElement("a");
      link.href = dataURL;
      link.download = `ticket_mesa_${idMesa}.png`;
      link.click();
    }

    // 3) Guardar en BD (Symfony) vía fetch
    try {
      const response = await fetch("/comanda/guardar", {
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
    } catch (err) {
      console.error("Error guardando comanda:", err);
      alert("Error guardando la comanda en servidor");
    }

    // 4) Limpiar localStorage y estadoApp
    delete estadoApp.comandasPorMesa[idMesa];
    guardarTodasComandas();
    renderizarComanda();

    // 5) Volver al mapa de mesas

    if (window.cargarMapa) {
      window.cargarMapa();
    } else {
      window.location.href = "/panel";
    }
  }
  window.imprimirTicket = imprimirTicket;

  // =============================================
  // INICIALIZACIÓN FINAL
  // =============================================
  document.querySelector(".modulo-producto")?.classList.add("panel-animado");

  // Exponer globalmente:
  window.cambiarMesa = cambiarMesa;
  window.imprimirTicket = imprimirTicket;
  // Nota: window.inicializarModuloProducto ya se expone al final del archivo
}

// Auto-inicialización si se accede directamente vía URL /panel/productos
document.addEventListener("DOMContentLoaded", () => {
  const panelTiposTest = document.getElementById("panel-nivel-1");
  if (panelTiposTest) {
    inicializarModuloProducto(null);
  }
});

// Exponer la función global para carga dinámica:
window.inicializarModuloProducto = inicializarModuloProducto;
