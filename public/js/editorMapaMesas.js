document.addEventListener("DOMContentLoaded", () => {
    const mapa = document.getElementById("fondoMapa");
    const btnNuevaMesa = document.getElementById("btnNuevaMesa");
    const btnGuardar = document.getElementById("btnGuardarMapa");

    cargarMapa();

    btnNuevaMesa.addEventListener("click", () => {
        const nuevaMesa = document.createElement("div");
        nuevaMesa.classList.add("mesa");
        nuevaMesa.style.left = "1600px";
        nuevaMesa.style.top = "120px";
        nuevaMesa.innerHTML = `<input type="text" value="mesa${document.querySelectorAll('.mesa').length + 1}">`;
        mapa.appendChild(nuevaMesa);
        hacerDraggable(nuevaMesa);
    });

    btnGuardar.addEventListener("click", () => {
        const mesasData = [];

        mapa.querySelectorAll(".mesa").forEach(mesa => {
            const id = mesa.dataset.id || null;
            const nombreInput = mesa.querySelector("input");
            const nombre = nombreInput ? nombreInput.value : mesa.textContent;
            const posicionX = parseInt(mesa.style.left, 10);
            const posicionY = parseInt(mesa.style.top, 10);
            mesasData.push({ id, nombre, posicionX, posicionY });
        });

        fetch("/mapa/guardar", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ mesas: mesasData })
        })
        .then(resp => resp.json())
        .then(data => {
            if (data.success) {
                alert("Mapa guardado correctamente");
                window.location.href = "/panel"; // Volver al panel principal
            } else {
                alert("Error al guardar el mapa");
            }
        });
    });

    function cargarMapa() {
        fetch("/mapa/mostrar")
            .then(res => res.json())
            .then(data => {
                mapa.innerHTML = ""; // Limpiar mapa antes de cargar

                data.mesas.forEach(mesa => {
                    const divMesa = document.createElement("div");
                    divMesa.classList.add("mesa");
                    divMesa.style.left = mesa.posicionX + "px";
                    divMesa.style.top = mesa.posicionY + "px";
                    divMesa.dataset.id = mesa.id;
                    divMesa.innerHTML = `<input type="text" value="${mesa.nombre}">`;
                    hacerDraggable(divMesa);
                    mapa.appendChild(divMesa);
                });
            });
    }

    function hacerDraggable(el) {
        let offsetX, offsetY;
        el.addEventListener("mousedown", e => {
            if (e.target.tagName === "INPUT") return; // No arrastrar al escribir nombre
            offsetX = e.offsetX;
            offsetY = e.offsetY;

            function mover(ev) {
                el.style.left = (ev.pageX - offsetX) + "px";
                el.style.top = (ev.pageY - offsetY) + "px";
            }

            document.addEventListener("mousemove", mover);
            document.addEventListener("mouseup", () => {
                document.removeEventListener("mousemove", mover);
            }, { once: true });
        });
    }
});
