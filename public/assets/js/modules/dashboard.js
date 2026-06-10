export function initDashboard() {
  const canvas = document.getElementById("caChart");

  if (!canvas || typeof Chart === "undefined") return;

  const ctx = canvas.getContext("2d");
  const monthlyData = JSON.parse(canvas.dataset.monthly);

  // Gradient : du haut (violet opaque) vers le bas (violet transparent)
  const gradient = ctx.createLinearGradient(0, 0, 0, 320);
  gradient.addColorStop(0, "rgba(79, 70, 229, 0.85)");
  gradient.addColorStop(1, "rgba(129, 140, 248, 0.2)");

  new Chart(ctx, {
    type: "bar",
    data: {
      labels: [
        "Jan",
        "Fév",
        "Mar",
        "Avr",
        "Mai",
        "Juin",
        "Juil",
        "Août",
        "Sep",
        "Oct",
        "Nov",
        "Déc",
      ],
      datasets: [
        {
          label: "Chiffre d'Affaires HT",
          data: monthlyData,
          backgroundColor: gradient,
          hoverBackgroundColor: "rgba(79, 70, 229, 1)",
          borderRadius: 8,
          maxBarThickness: 40,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      layout: {
        padding: { top: 20, left: 8 },
      },
      scales: {
        x: {
          grid: { display: false },
          ticks: {
            color: "#9ca3af",
            font: { family: "'Inter', sans-serif", size: 12 },
          },
        },
        y: {
          beginAtZero: true,
          grid: { color: "#f3f4f6" },
          ticks: {
            color: "#9ca3af",
            font: { family: "'Inter', sans-serif", size: 12 },
            callback: (value) => value.toLocaleString("fr-FR") + " €",
          },
        },
      },
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: "#1f2937",
          padding: 12,
          displayColors: false,
          callbacks: {
            label: (ctx) =>
              " CA : " + ctx.parsed.y.toLocaleString("fr-FR") + " €",
          },
        },
      },
      animation: { duration: 1000 },
    },
  });
}
