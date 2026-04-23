document.addEventListener("DOMContentLoaded", function () {
    if (
        !window.dashboardData ||
        !window.dashboardData.months ||
        window.dashboardData.months.length === 0
    )
        return;

    const ctx = document.getElementById("applicationsChart");
    if (!ctx) return;

    // Create a gradient for the chart fill
    const chartContext = ctx.getContext("2d");
    const gradient = chartContext.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, "rgba(0, 68, 178, 0.2)"); // Trust Blue (top)
    gradient.addColorStop(1, "rgba(0, 68, 178, 0.0)"); // Transparent (bottom)

    new Chart(ctx, {
        type: "line",
        data: {
            labels: window.dashboardData.months,
            datasets: [
                {
                    label: "Applications",
                    data: window.dashboardData.totals,
                    borderColor: "#0044B2", // Trust Blue
                    backgroundColor: gradient,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4, // Smooth, curvy lines
                    pointBackgroundColor: "#FF6B00", // Action Orange points
                    pointBorderColor: "#fff",
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: "#1E293B",
                    padding: 12,
                    titleFont: { size: 13 },
                    bodyFont: { size: 14, weight: "bold" },
                    displayColors: false,
                    callbacks: {
                        label: function (context) {
                            return context.parsed.y + " Applications";
                        },
                    },
                },
            },
            scales: {
                x: {
                    grid: { display: false, drawBorder: false },
                    ticks: { color: "#64748B", font: { size: 12 } },
                },
                y: {
                    beginAtZero: true,
                    grid: { color: "#F1F5F9", drawBorder: false },
                    ticks: {
                        color: "#64748B",
                        font: { size: 12 },
                        padding: 10,
                    },
                },
            },
            interaction: {
                intersect: false,
                mode: "index",
            },
            animation: { duration: 1000, easing: "easeOutQuart" },
        },
    });
});
