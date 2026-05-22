export function initDashboard() {
  const chartCanvas = document.getElementById("caChart");

  if (chartCanvas && typeof Chart !== "undefined") {
    const monthlyData = JSON.parse(chartCanvas.dataset.monthly);

    const ctx = chartCanvas.getContext("2d");
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
            label: "CA HT Mensuel (€)",
            data: monthlyData,
            backgroundColor: "#3498db",
            borderRadius: 5,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function (value) {
                return value + " €";
              },
            },
          },
        },
        plugins: {
          tooltip: {
            callbacks: {
              label: function (context) {
                return " CA : " + context.parsed.y.toLocaleString() + " €";
              },
            },
          },
        },
      },
    });
  }
}
