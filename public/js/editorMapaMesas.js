document.addEventListener("DOMContentLoaded", () => {
  const mapa = document.getElementById("fondoMapa");
  const btnNuevaMesa = document.getElementById("btnNuevaMesa");
  const btnBorrarUltima = document.getElementById("btnBorrarUltimaMesa");
  const btnBorrarTodas = document.getElementById("btnBorrarTodasMesas");
  const btnGuardar = document.getElementById("btnGuardarMapa");

  // Carga inicial del mapa
  cargarMapa();

  // ----------------------------
  // Botón: Nueva Mesa
  // ----------------------------
  if (btnNuevaMesa) {
    btnNuevaMesa.addEventListener("click", () => {
      const nuevaMesa = document.createElement("div");
      nuevaMesa.classList.add("mesa");
      // Posición por defecto: ajusta según tu UI
      nuevaMesa.style.left = "1400px";
      nuevaMesa.style.top = "120px";
      // Nombre por defecto: “MesaN”
      const contador = document.querySelectorAll(".mesa").length + 1;
      nuevaMesa.innerHTML = `<input type="text" value="Mesa${contador}">`;
      mapa.appendChild(nuevaMesa);
      hacerDraggable(nuevaMesa);
    });
  }

  // ----------------------------
  // Botón: Eliminar Última Mesa
  // ----------------------------
  if (btnBorrarUltima) {
    btnBorrarUltima.addEventListener("click", () => {
      if (!confirm("¿Seguro que deseas eliminar la última mesa añadida?")) {
        return;
      }
      // Lógica: llamamos a endpoint en backend para eliminar la última en BD
      fetch("/mapa/borrar-ultima", {
        method: "POST",
        headers: { "Content-Type": "application/json" }
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.success) {
            alert(
              "Última mesa eliminada" +
                (data.deletedId ? " (ID: " + data.deletedId + ")" : "")
            );
            // Eliminar del DOM si existía
            if (data.deletedId) {
              const elem = document.querySelector(
                `.mesa[data-id="${data.deletedId}"]`
              );
              if (elem) elem.remove();
            }
            // Refrescar visualmente
            cargarMapa();
          } else {
            alert(
              "No se pudo eliminar la última mesa: " + (data.message || "")
            );
          }
        })
        .catch((err) => {
          console.error("Error al eliminar última mesa:", err);
          alert("Error al enviar petición de eliminar última mesa");
        });
    });
  }

  // ----------------------------
  // Botón: Borrar Todas las Mesas
  // ----------------------------
  if (btnBorrarTodas) {
    btnBorrarTodas.addEventListener("click", () => {
      if (
        !confirm(
          "¿Seguro que deseas borrar todas las mesas? Esta acción no se puede deshacer."
        )
      ) {
        return;
      }
      fetch("/mapa/borrar-todas", {
        method: "POST",
        headers: { "Content-Type": "application/json" }
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.success) {
            alert(
              "Se han borrado todas las mesas" +
                (data.deletedCount != null
                  ? " (" + data.deletedCount + ")"
                  : "")
            );
            // Limpiar DOM
            mapa.innerHTML = "";
          } else {
            alert("Error al borrar todas las mesas");
          }
        })
        .catch((err) => {
          console.error("Error al borrar todas las mesas:", err);
          alert("Error al enviar petición de borrar todas las mesas");
        });
    });
  }

  // ----------------------------
  // Botón: Guardar Mapa
  // ----------------------------
  if (btnGuardar) {
    btnGuardar.addEventListener("click", () => {
      const mesasData = [];
      mapa.querySelectorAll(".mesa").forEach((mesa) => {
        const id = mesa.dataset.id || null;
        const nombreInput = mesa.querySelector("input");
        const nombre = nombreInput
          ? nombreInput.value.trim()
          : mesa.textContent.trim();
        const posicionX = parseInt(mesa.style.left, 10) || 0;
        const posicionY = parseInt(mesa.style.top, 10) || 0;
        mesasData.push({ id, nombre, posicionX, posicionY });
      });

      fetch("/mapa/guardar", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ mesas: mesasData })
      })
        .then((resp) => resp.json())
        .then((data) => {
          if (data.success) {
            alert("Mapa guardado correctamente");
            window.location.href = "/panel"; // Ajusta la ruta a tu panel principal
          } else {
            alert("Error al guardar el mapa");
          }
        })
        .catch((err) => {
          console.error("Error al guardar mapa:", err);
          alert("Error en la petición de guardar mapa");
        });
    });
  }

  // ----------------------------
  // Función: cargarMapa
  // ----------------------------
  function cargarMapa() {
    fetch("/mapa/mostrar")
      .then((res) => res.json())
      .then((data) => {
        mapa.innerHTML = ""; // Limpiar mapa
        if (Array.isArray(data.mesas)) {
          data.mesas.forEach((mesa) => {
            const divMesa = document.createElement("div");
            divMesa.classList.add("mesa");
            divMesa.style.position = "absolute";
            divMesa.style.left = mesa.posicionX + "px";
            divMesa.style.top = mesa.posicionY + "px";
            if (mesa.id != null) {
              divMesa.dataset.id = mesa.id;
            }
            divMesa.innerHTML = `<input type="text" value="${
              mesa.nombre || ""
            }">`;
            hacerDraggable(divMesa);
            mapa.appendChild(divMesa);
          });
        } else {
          console.warn("Respuesta /mapa/mostrar sin array 'mesas'");
        }
      })
      .catch((err) => {
        console.error("Error al cargar mapa:", err);
      });
  }

  // ----------------------------
  // Función: hacerDraggable
  // ----------------------------
  function hacerDraggable(el) {
    let offsetX, offsetY;
    el.style.cursor = "move";
    el.addEventListener("mousedown", (e) => {
      // Si se clickea dentro del input, no arrastrar
      if (e.target.tagName === "INPUT" || e.target.closest("input")) {
        return;
      }
      offsetX = e.offsetX;
      offsetY = e.offsetY;

      function mover(ev) {
        el.style.left = ev.pageX - offsetX + "px";
        el.style.top = ev.pageY - offsetY + "px";
      }
      document.addEventListener("mousemove", mover);
      document.addEventListener(
        "mouseup",
        () => {
          document.removeEventListener("mousemove", mover);
        },
        { once: true }
      );
    });
  }
});
