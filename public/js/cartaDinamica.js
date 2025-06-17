document.addEventListener("DOMContentLoaded", () => {
  const btnComida = document.getElementById("btn-comida");
  const btnBebida = document.getElementById("btn-bebida");
  const comidaSection = document.getElementById("comida-section");
  const bebidaSection = document.getElementById("bebida-section");

  btnComida.addEventListener("click", () => {
    btnComida.classList.add("active");
    btnBebida.classList.remove("active");
    comidaSection.style.display = "block";
    bebidaSection.style.display = "none";
  });

  btnBebida.addEventListener("click", () => {
    btnBebida.classList.add("active");
    btnComida.classList.remove("active");
    bebidaSection.style.display = "block";
    comidaSection.style.display = "none";
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const hash = window.location.hash;
  if (hash.startsWith("#modal-bebida-")) {
    const bebidaBtn = document.querySelector(`[data-bs-target="${hash}"]`);
    if (bebidaBtn) {
      // Mostrar sección bebida
      document.getElementById("btn-bebida")?.click();

      // Retrasar un poco para que el DOM cambie de sección
      setTimeout(() => {
        const modal = new bootstrap.Modal(document.querySelector(hash));
        modal.show();
      }, 300);
    }
  }
});
