document.addEventListener("DOMContentLoaded", () => {
    const zonaCentral = document.getElementById("fondoMapa");
    const contenidoDinamico = document.getElementById("contenido-dinamico");
    let mesaActual = null;

    cargarMapa();

    function cargarMapa() {
        fetch("/mapa/mostrar")
            .then(res => res.json())
            .then(data => {
                zonaCentral.innerHTML = "";

                zonaCentral.style.backgroundImage = "url('/imagen/fondoplano.png')";
                zonaCentral.style.backgroundSize = "contain";
                zonaCentral.style.backgroundPosition = "center";
                zonaCentral.style.backgroundRepeat = "no-repeat";

                data.mesas.forEach(mesa => {
                    const divMesa = document.createElement("div");
                    divMesa.classList.add("mesa");
                    divMesa.style.left = mesa.posicionX + "px";
                    divMesa.style.top = mesa.posicionY + "px";
                    divMesa.textContent = mesa.nombre;
                    divMesa.dataset.id = mesa.id;

                    divMesa.addEventListener("click", () => {
                        abrirModuloProductos(mesa.id);
                    });

                    zonaCentral.appendChild(divMesa);
                });
            });
    }

    function cargarCSSModuloProducto() {
        const idCSS = 'css-modulo-producto';
        const existente = document.getElementById(idCSS);
        if (existente) {
            existente.remove(); // Elimina si ya está cargado (por si recargas módulo)
        }

        const link = document.createElement('link');
        link.id = idCSS;
        link.rel = 'stylesheet';
        link.href = '/css/moduloProducto.css?v=' + Date.now(); // Cache busting para desarrollo
        document.head.appendChild(link);
    }

    // Guardar la mesa actual (nombre + id) en localStorage
    function setMesaActual(mesa) {
        mesaActual = mesa;
        localStorage.setItem("mesaActual", JSON.stringify(mesa));
    }

    function getMesaActual() {
        return JSON.parse(localStorage.getItem("mesaActual"));
    }

    function abrirModuloProductos(mesaId) {
        const nombreMesa = document.querySelector(`.mesa[data-id="${mesaId}"]`).textContent;
        fetch('/cargar/modulo-productos')
  .then(res => res.text())
  .then(html => {
    document.querySelector('#fondoMapa').innerHTML = html;
    window.inicializarModuloProducto({ id: mesaId, nombre: nombreMesa });
  });

    }
    
    

    // Guardar comanda de la mesa (array de productos)
    function guardarComandaActual(mesaId, productos) {
        localStorage.setItem(`comanda_mesa_${mesaId}`, JSON.stringify(productos));
    }

    // Cargar comanda de la mesa (array de productos) o vacío si no hay
    function cargarComanda(mesaId) {
        const data = localStorage.getItem(`comanda_mesa_${mesaId}`);
        return data ? JSON.parse(data) : [];
    }

    const botonVerMapa = document.getElementById("btnVerMapaMesas");
    botonVerMapa.addEventListener("click", cargarMapa);
});
