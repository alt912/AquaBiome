function drawChart(selector, data, keys, colors) {
    const container = document.querySelector(selector);
    if (!container) return;

    d3.select(selector).selectAll("*").remove();

    // Récupère la largeur réelle — même si l'onglet est caché
    let width = container.offsetWidth;
    if (width <= 0) {
        // Si le container est masqué (display:none), on mesure le parent visible
        const parent = container.closest('.stat-card') || container.parentElement;
        width = parent ? parent.offsetWidth - 40 : 300;
    }

    const margin = { top: 20, right: 30, bottom: 40, left: 50 };
    const chartWidth = width - margin.left - margin.right;
    const chartHeight = 250 - margin.top - margin.bottom;

    if (chartWidth <= 0) return;

    const svg = d3.select(selector)
        .append("svg")
        .attr("width", chartWidth + margin.left + margin.right)
        .attr("height", chartHeight + margin.top + margin.bottom)
        .append("g")
        .attr("transform", `translate(${margin.left},${margin.top})`);

    const parseTime = d3.timeParse("%Y-%m-%d %H:%M");
    const formattedData = data.map(d => ({
        ...d,
        date: typeof d.date === 'string' ? parseTime(d.date) : d.date
    })).filter(d => d.date !== null);

    if (formattedData.length === 0) return;

    // --- BRIDAGE DE L'AXE Y À 40 ---
    const realMax = d3.max(formattedData, d => Math.max(...keys.map(k => d[k] || 0)));
    const displayMax = Math.min(realMax, 40);

    const x = d3.scaleTime()
        .domain(d3.extent(formattedData, d => d.date))
        .range([0, chartWidth]);

    const y = d3.scaleLinear()
        .domain([0, Math.max(displayMax * 1.1, 15)])
        .range([chartHeight, 0]);

    svg.append("g")
        .attr("transform", `translate(0,${chartHeight})`)
        .call(d3.axisBottom(x).ticks(5).tickFormat(d3.timeFormat("%d/%m")));

    svg.append("g").call(d3.axisLeft(y));

    keys.forEach((key, i) => {
        svg.append("path")
            .datum(formattedData)
            .attr("fill", "none")
            .attr("stroke", colors[i])
            .attr("stroke-width", 3)
            .attr("d", d3.line()
                .defined(d => d[key] !== null && d[key] <= 100)
                .x(d => x(d.date))
                .y(d => y(Math.min(d[key] || 0, 40)))
                .curve(d3.curveMonotoneX)
            );

        svg.selectAll(".dot-" + key)
            .data(formattedData.filter(d => d[key] !== null && d[key] <= 100))
            .enter().append("circle")
            .attr("cx", d => x(d.date))
            .attr("cy", d => y(Math.min(d[key] || 0, 40)))
            .attr("r", 4)
            .attr("fill", colors[i]);
    });
}

// ----------------------------------------------------------------
// initCharts : dessine tous les graphiques, même ceux dans un onglet
// masqué, en les rendant temporairement visibles le temps de mesurer.
// ----------------------------------------------------------------
function initCharts() {
    if (typeof rawData === 'undefined' || rawData.length === 0) return;

    const allTabContents = document.querySelectorAll('.tab-content');

    // Rend tous les onglets temporairement visibles (off-screen)
    allTabContents.forEach(el => {
        if (!el.classList.contains('active')) {
            el.style.visibility = 'hidden';
            el.style.position = 'absolute';
            el.style.display = 'block';
        }
    });

    // Dessine les 3 graphiques avec une vraie largeur
    drawChart("#chart_gh", rawData, ["gh"], ["#7d7bc9"]);
    drawChart("#chart_ph_kh", rawData, ["ph", "kh"], ["#ff6f9c", "#4e73df"]);
    drawChart("#chart_toxic", rawData, ["nitrites", "ammonium"], ["#e74a3b", "#f6c23e"]);

    // Remet les onglets inactifs dans leur état caché
    allTabContents.forEach(el => {
        if (!el.classList.contains('active')) {
            el.style.visibility = '';
            el.style.position = '';
            el.style.display = '';
        }
    });
}

// ----------------------------------------------------------------
// Initialisation au chargement de la page
// ----------------------------------------------------------------
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCharts);
} else {
    initCharts(); // DOM déjà prêt (scripts sans defer, placés en fin de body)
}

// Redessine lors du resize (throttle 250ms)
let resizeTimer;
window.addEventListener('resize', function () {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(initCharts, 250);
});

// ----------------------------------------------------------------
// Gestion des onglets
// ----------------------------------------------------------------
function openTab(evt, tabName) {
    // Cache tous les onglets
    document.querySelectorAll('.tab-content').forEach(el => {
        el.style.display = '';
        el.classList.remove('active');
    });

    // Désactive les boutons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });

    // Active l'onglet sélectionné
    const target = document.getElementById(tabName);
    if (target) {
        target.classList.add('active');
    }
    evt.currentTarget.classList.add('active');

    // Redessine pour prendre la bonne largeur
    initCharts();
}