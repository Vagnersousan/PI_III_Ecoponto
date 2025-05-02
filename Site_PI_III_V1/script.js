$(document).ready(function() {
    // Login form submission logic
    // ... (rest of the login and password toggle code remains the same)
    $("#togglePassword").click(function () {
        var input = $("#inputSenha");
        var showIcon = $("#togglePassword");
        var hideIcon = $("#hidePassword");
        if (input.attr("type") === "password") {
            input.attr("type", "text");
            showIcon.hide();
            hideIcon.show();
        } else {
            input.attr("type", "password");
            hideIcon.hide();
            showIcon.show();
        }
    });

    // --- Chart.js Code --- 
    const ctxGeracaoColeta = document.getElementById('graficoGeracaoColeta');
    const ctxDestinoALC = document.getElementById('graficoDestinoALC');
    const ctxTaxaColeta = document.getElementById('graficoTaxaColeta');

    if (ctxGeracaoColeta && ctxDestinoALC && ctxTaxaColeta) {

        // Data (from residuos_data.csv)
        const data = {
            labels: ['Brasil (2022)', 'Brasil (2023)', 'América Latina & Caribe (2021)'],
            generated: [81.8, 80.96, 230.47],
            collected: [76.1, 75.6, 195.32],
            collectionRate: [93.0, 93.4, 84.7],
            alc2021_destination: {
                'Aterro Sanitário': 106.16,
                'Aproveitados (Recicl./Compost.)': 10.13,
                'Disposição Inadequada': 94.09,
                'Destino Desconhecido': 20.09
            }
        };

        // Calculate 'Not Collected' data
        const notCollected = data.generated.map((g, i) => parseFloat((g - data.collected[i]).toFixed(2)));

        // Chart 1: Generation vs Collection (Bar + Line Chart)
        new Chart(ctxGeracaoColeta, {
            type: 'bar', // Base type is bar
            data: {
                labels: data.labels,
                datasets: [
                    {
                        label: 'Gerado (Mton)',
                        data: data.generated,
                        backgroundColor: 'rgba(54, 162, 235, 0.7)', // Blue
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        order: 2 // Ensure bars are behind the line
                    },
                    {
                        label: 'Coletado (Mton)',
                        data: data.collected,
                        backgroundColor: 'rgba(75, 192, 192, 0.7)', // Green
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1,
                        order: 2 // Ensure bars are behind the line
                    },
                    {
                        label: 'Não Coletado (Mton)', // New dataset for the line
                        data: notCollected,
                        type: 'line', // Specify type as line
                        borderColor: 'rgba(255, 99, 132, 1)', // Red line
                        backgroundColor: 'rgba(255, 99, 132, 0.5)', // Point color
                        fill: false,
                        tension: 0.1,
                        order: 1 // Ensure line is drawn on top
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Milhões de Toneladas (Mton)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Região (Ano)'
                        }
                    }
                },
                responsive: true,
                maintainAspectRatio: false // Set to false for ALL charts
            }
        });

        // Chart 2: ALC Destination 2021 (Doughnut Chart)
        const alcLabels = Object.keys(data.alc2021_destination);
        const alcData = Object.values(data.alc2021_destination);
        const alcTotal = alcData.reduce((a, b) => a + b, 0);

        new Chart(ctxDestinoALC, {
            type: 'doughnut', 
            data: {
                labels: alcLabels,
                datasets: [{
                    label: 'Destinação RSU ALC 2021 (Mton)',
                    data: alcData,
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.7)', // Green
                        'rgba(255, 206, 86, 0.7)', // Yellow
                        'rgba(255, 99, 132, 0.7)', // Red
                        'rgba(153, 102, 255, 0.7)' // Purple
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // Set to false for ALL charts
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                const value = context.parsed;
                                const percentage = ((value / alcTotal) * 100).toFixed(1);
                                label += `${value.toFixed(2)} Mton (${percentage}%)`;
                                return label;
                            }
                        }
                    },
                    legend: {
                        position: 'top',
                    }
                }
            }
        });

        // Chart 3: Collection Rate (Bar Chart)
        new Chart(ctxTaxaColeta, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Taxa de Coleta (%)',
                    data: data.collectionRate,
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.7)', // Blue
                        'rgba(75, 192, 192, 0.7)', // Green
                        'rgba(255, 159, 64, 0.7)' // Orange
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }
                // No deviation line added here as per discussion
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        title: {
                            display: true,
                            text: 'Taxa de Coleta (%)'
                        }
                    },
                     x: {
                        title: {
                            display: true,
                            text: 'Região (Ano)'
                        }
                    }
                },
                responsive: true,
                maintainAspectRatio: false, // Set to false for ALL charts
                 plugins: {
                    legend: {
                        display: false // Hide legend as it's a single dataset
                    }
                }
            }
        });
    } // End if chart elements exist

}); // End $(document).ready()

// Replit login function (from user's script)
// ... (rest of the Replit login code remains the same)
function LoginWithReplit() {
  window.addEventListener("message", authComplete);
  var h = 500;
  var w = 350;
  var left = screen.width / 2 - w / 2;
  var top = screen.height / 2 - h / 2;
  var authWindow = window.open(
    "https://replit.com/auth_with_repl_site?domain=" + location.host,
    "_blank",
    "modal =yes, toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=" +
      w +
      ", height=" +
      h +
      ", top=" +
      top +
      ", left=" +
      left
  );
  function authComplete(e) {
    if (e.data !== "auth_complete") {
      return;
    }
    window.removeEventListener("message", authComplete);
    authWindow.close();
    location.reload();
  }
}