import Chart from "chart.js/auto";

document.addEventListener("DOMContentLoaded", function () {
    // Grafik Perkembangan: rata-rata nilai per-tipe subject (umum, khusus, ekstra) per semester
    const perkembanganLabels = window.perkembanganLabels || [];
    const perkembanganDatasets = window.perkembanganDatasets || [];
    const perkembangan = document.getElementById("chartPerkembangan");
    if (perkembangan) {
        new Chart(perkembangan, {
            type: "bar",
            data: {
                labels: perkembanganLabels,
                datasets: perkembanganDatasets.map((ds, idx) => ({
                    label: ds.label,
                    data: ds.data,
                    borderColor: [
                        '#0ea5e9', // biru bold
                        '#f59e42', // orange bold
                        '#6366f1', // ungu bold
                    ][idx % 3],
                    backgroundColor: [
                        '#0ea5e9', // biru bold
                        '#f59e42', // orange bold
                        '#6366f1', // ungu bold
                    ][idx % 3],
                    borderWidth: 2,
                })),
            },
            options: {
                responsive: true,
                plugins: { legend: { display: true } },
                scales: {
                    x: {},
                    y: { beginAtZero: true },
                },
            },
        });
    }

    // Grafik Perbandingan: radar chart, satu dataset per semester
    const perbandinganLabels = window.perbandinganLabels || [];
    const perbandinganDatasets = window.perbandinganDatasets || [];
    const perbandingan = document.getElementById("chartPerbandingan");
    if (perbandingan) {
        new Chart(perbandingan, {
            type: "radar",
            data: {
                labels: perbandinganLabels,
                datasets: perbandinganDatasets.map((ds, idx) => ({
                    label: ds.label,
                    data: ds.data,
                    borderColor: [
                        '#14b8a6', '#6366f1', '#f59e42', '#ef4444', '#0ea5e9', '#a3e635', '#eab308'
                    ][idx % 7],
                    backgroundColor: [
                        'rgba(20,184,166,0.1)', 'rgba(99,102,241,0.1)', 'rgba(245,158,66,0.1)', 'rgba(239,68,68,0.1)', 'rgba(14,165,233,0.1)', 'rgba(163,230,53,0.1)', 'rgba(234,179,8,0.1)'
                    ][idx % 7],
                    pointBackgroundColor: [
                        '#14b8a6', '#6366f1', '#f59e42', '#ef4444', '#0ea5e9', '#a3e635', '#eab308'
                    ][idx % 7],
                    fill: true,
                })),
            },
            options: {
                responsive: true,
                plugins: { legend: { display: true } },
            },
        });
    }
});
