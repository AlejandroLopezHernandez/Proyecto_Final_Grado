document.addEventListener("DOMContentLoaded", function () {
  fetch("/manager/cervezasXestilo")
    .then((response) => response.json())
    .then((data) => {
      const labels = data.map((item) => item.estilo_cerveza);
      const values = data.map((item) => item.numero_cervezas);
      const backgroundColors = data.map((_, i) =>
        i % 2 === 0 ? "rgba(255, 233, 122, 0.7)" : "rgba(200, 255, 190, 0.7)"
      );

      const ctx = document.getElementById("cervezasXestilo").getContext("2d");
      new Chart(ctx, {
        type: "bar",
        data: {
          labels: labels,
          datasets: [
            {
              label: "Cervezas por estilo",
              data: values,
              backgroundColor: backgroundColors,
              borderColor: "rgb(235, 99, 54)",
              borderWidth: 1
            }
          ]
        },
        options: {
          responsive: true,
          scales: {
            x: {
              ticks: {
                maxRotation: 45,
                minRotation: 45,
                autoSkip: false
              }
            },
            y: {
              beginAtZero: true,
              ticks: {
                precision: 0
              },
              title: {
                display: true,
                text: "Número de cervezas"
              }
            }
          },
          plugins: {
            title: {
              display: true,
              text: "Distribución de cervezas por estilo",
              font: {
                size: 18
              }
            },
            legend: {
              display: false
            },
            tooltip: {
              callbacks: {
                label: function (context) {
                  return `Total: ${context.parsed.y}`;
                }
              }
            }
          }
        }
      });
    });
});

document.addEventListener("DOMContentLoaded", function () {
  fetch("/manager/nBebidasXtipo")
    .then((response) => response.json())
    .then((data) => {
      const labels = data.map((item) => item.tipo_bebida);
      const values = data.map((item) => item.numero_bebidas);

      const backgroundColors = data.map((_, i) =>
        i % 2 === 0 ? "rgba(144, 238, 144, 0.7)" : "rgba(221, 160, 221, 0.7)"
      );

      const ctx = document
        .getElementById("bebidasAlcoholicas")
        .getContext("2d");
      new Chart(ctx, {
        type: "bar",
        data: {
          labels: labels,
          datasets: [
            {
              label: "Clasificación de bebidas alcohólicas",
              data: values,
              backgroundColor: backgroundColors,
              borderColor: "rgba(60, 60, 60, 0.7)",
              borderWidth: 1
            }
          ]
        },
        options: {
          responsive: true,
          indexAxis: "y", // <- clave para hacerlo horizontal
          scales: {
            x: {
              beginAtZero: true,
              ticks: {
                precision: 0
              },
              title: {
                display: true,
                text: "Número de bebidas"
              }
            },
            y: {
              ticks: {
                autoSkip: false
              }
            }
          },
          plugins: {
            title: {
              display: true,
              text: "Distribución de bebidas alcohólicas por tipo",
              font: {
                size: 18
              }
            },
            legend: {
              display: false
            },
            tooltip: {
              callbacks: {
                label: function (context) {
                  return `Total: ${context.parsed.x}`;
                }
              }
            }
          }
        }
      });
    });
});
document.addEventListener("DOMContentLoaded", function () {
  fetch("/manager/NcomidasXtipo")
    .then((response) => response.json())
    .then((data) => {
      const labels = data.map((item) => item.categoria);
      const values = data.map((item) => item.numero_comidas);

      // Colores alternos suaves para no sobrecargar visualmente
      const backgroundColors = data.map((_, i) =>
        i % 2 === 0 ? "rgba(242, 30, 6, 0.7)" : "rgba(7, 120, 240, 0.7)"
      );

      const ctx = document
        .getElementById("cuadroTiposComidas")
        .getContext("2d");
      new Chart(ctx, {
        type: "bar",
        data: {
          labels: labels,
          datasets: [
            {
              label: "Tipos de comida",
              data: values,
              backgroundColor: backgroundColors,
              borderColor: "rgba(100, 100, 100, 0.5)",
              borderWidth: 1
            }
          ]
        },
        options: {
          responsive: true,
          indexAxis: "y", // horizontal
          scales: {
            x: {
              beginAtZero: true,
              ticks: {
                precision: 0
              },
              title: {
                display: true,
                text: "Número de comidas"
              }
            },
            y: {
              ticks: {
                autoSkip: false,
                font: {
                  size: 12
                }
              }
            }
          },
          plugins: {
            title: {
              display: true,
              text: "Distribución de comidas por tipo",
              font: {
                size: 18
              }
            },
            tooltip: {
              callbacks: {
                label: function (context) {
                  return `${context.label}: ${context.parsed.x}`;
                }
              }
            },
            legend: {
              display: false
            }
          }
        }
      });
    });
});
document.addEventListener("DOMContentLoaded", function () {
  fetch("/manager/NcomidasXprecio")
    .then((response) => response.json())
    .then((data) => {
      const labels = data.map((item) => item.rango_precio);
      const values = data.map((item) => item.cantidad_comidas);
      const total = values.reduce((a, b) => a + b, 0);

      const backgroundColors = [
        "rgba(255, 159, 64, 0.7)",
        "rgba(255, 205, 86, 0.7)",
        "rgba(75, 192, 192, 0.7)",
        "rgba(54, 162, 235, 0.7)",
        "rgba(153, 102, 255, 0.7)",
        "rgba(201, 203, 207, 0.7)",
        "rgba(255, 99, 132, 0.7)"
      ];

      const ctx = document
        .getElementById("cuadroComidasPrecio")
        .getContext("2d");

      const centerTextPlugin = {
        id: "centerText",
        beforeDraw: (chart) => {
          const { width, height, ctx } = chart;
          ctx.save();
          ctx.font = "bold 18px sans-serif";
          ctx.fillStyle = "#333";
          ctx.textAlign = "right";
          ctx.textBaseline = "right";
          ctx.fillText(`${total} comidas`, width / 2, height / 2);
          ctx.restore();
        }
      };

      new Chart(ctx, {
        type: "doughnut",
        data: {
          labels: labels,
          datasets: [
            {
              label: "Comidas por rango de precio",
              data: values,
              backgroundColor: backgroundColors,
              borderColor: "white",
              borderWidth: 2
            }
          ]
        },
        options: {
          responsive: true,
          cutout: "55%",
          plugins: {
            title: {
              display: true,
              text: "Distribución de comidas por rango de precio",
              font: {
                size: 18
              }
            },
            legend: {
              position: "right"
            },
            tooltip: {
              callbacks: {
                label: function (context) {
                  return `${context.label}: ${context.parsed} comidas`;
                }
              }
            }
          }
        },
        plugins: [centerTextPlugin]
      });
    });
});
document.addEventListener("DOMContentLoaded", function () {
  fetch("/manager/NbebidasXprecio")
    .then((response) => response.json())
    .then((data) => {
      const labels = data.map((item) => item.rango_precio);
      const values = data.map((item) => item.cantidad_bebidas);
      const total = values.reduce((a, b) => a + b, 0);

      const backgroundColors = [
        "rgba(54, 162, 235, 0.7)", // azul
        "rgba(75, 192, 192, 0.7)", // turquesa
        "rgba(255, 205, 86, 0.7)", // amarillo
        "rgba(153, 102, 255, 0.7)", // violeta
        "rgba(201, 203, 207, 0.7)", // gris
        "rgba(255, 159, 64, 0.7)" // naranja
      ];

      const ctx = document
        .getElementById("cuadroBebidasPrecio")
        .getContext("2d");

      const centerTextPlugin = {
        id: "centerText",
        beforeDraw: (chart) => {
          const { width, height, ctx } = chart;
          ctx.save();
          ctx.font = "bold 18px sans-serif";
          ctx.fillStyle = "#333";
          ctx.textAlign = "right";
          ctx.textBaseline = "right";
          ctx.fillText(`${total} bebidas`, width / 2, height / 2);
          ctx.restore();
        }
      };

      new Chart(ctx, {
        type: "doughnut",
        data: {
          labels: labels,
          datasets: [
            {
              label: "Bebidas por rango de precio",
              data: values,
              backgroundColor: backgroundColors,
              borderColor: "white",
              borderWidth: 2
            }
          ]
        },
        options: {
          responsive: true,
          cutout: "55%",
          plugins: {
            title: {
              display: true,
              text: "Distribución de bebidas por rango de precio",
              font: {
                size: 18
              }
            },
            legend: {
              position: "right"
            },
            tooltip: {
              callbacks: {
                label: function (context) {
                  return `${context.label}: ${context.parsed} bebidas`;
                }
              }
            }
          }
        },
        plugins: [centerTextPlugin]
      });
    });
});
document.addEventListener("DOMContentLoaded", function () {
  fetch("/manager/productosXproveedor")
    .then((response) => response.json())
    .then((data) => {
      const labels = data.map((item) => item.nombre_proveedor);
      const values = data.map((item) => item.numero_productos);

      // Colores alternos para mejorar visualmente
      const backgroundColors = data.map((_, i) =>
        i % 2 === 0 ? "rgba(255, 193, 102, 0.8)" : "rgba(255, 228, 181, 0.8)"
      );

      const ctx = document
        .getElementById("cuadroProveedoresProducto")
        .getContext("2d");
      new Chart(ctx, {
        type: "bar",
        data: {
          labels: labels,
          datasets: [
            {
              label: "Productos por proveedor",
              data: values,
              backgroundColor: backgroundColors,
              borderColor: "rgba(120, 72, 0, 0.6)",
              borderWidth: 1
            }
          ]
        },
        options: {
          responsive: true,
          scales: {
            x: {
              ticks: {
                maxRotation: 45,
                minRotation: 45,
                autoSkip: false
              }
            },
            y: {
              beginAtZero: true,
              title: {
                display: true,
                text: "Cantidad de productos"
              },
              ticks: {
                precision: 0
              }
            }
          },
          plugins: {
            title: {
              display: true,
              text: "Número de productos por proveedor",
              font: {
                size: 18
              }
            },
            legend: {
              display: false
            },
            tooltip: {
              callbacks: {
                label: function (context) {
                  return `${context.label}: ${context.parsed.y} productos`;
                }
              }
            }
          }
        }
      });
    });
});
